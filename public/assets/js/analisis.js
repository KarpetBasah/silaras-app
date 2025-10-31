/**
 * Analysis Module JavaScript
 * Handles analysis functionalities for overlap detection, gap identification, and RPJMD alignment
 */

let analysisMap;
let programLayer;
let overlapLayer;
let gapLayer;
let rpjmdLayer;
let analysisData = {
    programs: [],
    overlaps: [],
    gaps: [],
    alignment: {},
    statistics: {}
};

/**
 * Initialize analysis page
 */
function initAnalysisPage() {
    initAnalysisMap();
    initAnalysisTabs();
    initAnalysisControls();
    
    // Show initial loading states
    showInitialLoadingStates();
    
    // Load RPJMD zones first (independent of analysis data)
    loadRpjmdZones();
    
    // Load initial data
    loadAnalysisData();
}

/**
 * Initialize the analysis map
 */
function initAnalysisMap() {
    // Center on Banjarbaru
    const centerLat = -3.4503;
    const centerLng = 114.8166;
    
    analysisMap = L.map('analysis-map').setView([centerLat, centerLng], 13);
    
    // Add base map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(analysisMap);
    
    // Initialize empty layers
    programLayer = L.layerGroup().addTo(analysisMap);
    overlapLayer = L.layerGroup();
    gapLayer = L.layerGroup();
    rpjmdLayer = L.layerGroup();
    
    // Add layer controls
    initMapLayerControls();
}

/**
 * Initialize map layer controls
 */
function initMapLayerControls() {
    const showPrograms = document.getElementById('show-programs');
    const showOverlaps = document.getElementById('show-overlaps');
    const showGaps = document.getElementById('show-gaps');
    const showRpjmd = document.getElementById('show-rpjmd');
    
    showPrograms.addEventListener('change', function() {
        if (this.checked) {
            analysisMap.addLayer(programLayer);
        } else {
            analysisMap.removeLayer(programLayer);
        }
    });
    
    showOverlaps.addEventListener('change', function() {
        if (this.checked) {
            analysisMap.addLayer(overlapLayer);
        } else {
            analysisMap.removeLayer(overlapLayer);
        }
    });
    
    showGaps.addEventListener('change', function() {
        if (this.checked) {
            analysisMap.addLayer(gapLayer);
        } else {
            analysisMap.removeLayer(gapLayer);
        }
    });
    
    showRpjmd.addEventListener('change', function() {
        console.log('RPJMD toggle clicked:', this.checked);
        if (this.checked) {
            console.log('Adding RPJMD layer to map');
            analysisMap.addLayer(rpjmdLayer);
        } else {
            console.log('Removing RPJMD layer from map');
            analysisMap.removeLayer(rpjmdLayer);
        }
    });
}

/**
 * Initialize analysis tabs
 */
function initAnalysisTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(`tab-${targetTab}`).classList.add('active');
            
            // Load breakdown data if alignment tab is opened and data is available
            if (targetTab === 'alignment' && analysisData.alignment && analysisData.alignment.by_sector) {
                // Load default breakdown (sector) when alignment tab is first opened
                setTimeout(() => {
                    const activeBreakdownTab = document.querySelector('.breakdown-tab.active');
                    if (activeBreakdownTab) {
                        const breakdownType = activeBreakdownTab.dataset.breakdown;
                        loadBreakdownData(breakdownType);
                    } else {
                        // Set sector as default and load its data
                        const sectorTab = document.querySelector('.breakdown-tab[data-breakdown="sector"]');
                        if (sectorTab) {
                            sectorTab.classList.add('active');
                            loadBreakdownData('sector');
                        }
                    }
                }, 100);
            }
            
            // Update map view based on selected tab
            updateMapForTab(targetTab);
        });
    });
    
    // Initialize alignment section tabs
    initAlignmentSectionTabs();
    
    // Initialize breakdown tabs
    initBreakdownTabs();
}

/**
 * Initialize alignment section tabs (breakdown, programs, recommendations)
 */
function initAlignmentSectionTabs() {
    const sectionTabs = document.querySelectorAll('.section-tab');
    const sectionContents = document.querySelectorAll('.section-content');
    
    sectionTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetSection = this.dataset.section;
            
            // Remove active class from all tabs and contents
            sectionTabs.forEach(t => t.classList.remove('active'));
            sectionContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(`section-${targetSection}`).classList.add('active');
            
            // If breakdown section is opened and we have alignment data, load breakdown
            if (targetSection === 'breakdown' && analysisData.alignment && analysisData.alignment.by_sector) {
                const activeBreakdownTab = document.querySelector('.breakdown-tab.active');
                if (activeBreakdownTab) {
                    const breakdownType = activeBreakdownTab.dataset.breakdown;
                    loadBreakdownData(breakdownType);
                } else {
                    // Load default (sector) breakdown
                    loadBreakdownData('sector');
                }
            }
        });
    });
}

/**
 * Initialize breakdown tabs (sector, OPD, priority)
 */
function initBreakdownTabs() {
    const breakdownTabs = document.querySelectorAll('.breakdown-tab');
    
    breakdownTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetBreakdown = this.dataset.breakdown;
            
            // Remove active class from all breakdown tabs
            breakdownTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Load breakdown data
            loadBreakdownData(targetBreakdown);
        });
    });
}

/**
 * Initialize analysis controls
 */
function initAnalysisControls() {
    const runAnalysisBtn = document.getElementById('run-analysis');
    const filterControls = ['filter-tahun', 'filter-sektor', 'filter-opd', 'overlap-radius', 'grid-size'];
    
    // Run analysis button
    runAnalysisBtn.addEventListener('click', function() {
        runAnalysis();
    });
    
    // Filter change handlers
    filterControls.forEach(controlId => {
        const control = document.getElementById(controlId);
        if (control) {
            control.addEventListener('change', function() {
                // Auto-run analysis on filter change
                debounce(runAnalysis, 1000)();
            });
        }
    });
}

/**
 * Load initial analysis data
 */
function loadAnalysisData() {
    showLoader(true);
    
    const filters = getAnalysisFilters();
    
    Promise.all([
        fetchAnalysisData('tumpang-tindih', filters).catch(error => {
            console.warn('Tumpang tindih data failed to load:', error);
            return { overlaps: [], statistics: { high: 0, medium: 0, low: 0 } };
        }),
        fetchAnalysisData('kesenjangan', filters).catch(error => {
            console.warn('Kesenjangan data failed to load:', error);
            return { gaps: [], statistics: { gap_cells: 0, priority_gaps: 0, coverage_percentage: 0 }, recommendations: [] };
        }),
        fetchAnalysisData('keselarasan-rpjmd', filters).catch(error => {
            console.warn('Keselarasan RPJMD data failed to load:', error);
            return { 
                aligned: [], 
                misaligned: [], 
                statistics: { aligned_count: 0, misaligned_count: 0, total_programs: 0, alignment_percentage: 0 },
                by_sector: {},
                by_opd: {}
            };
        }),
        fetchAnalysisData('statistik', filters).catch(error => {
            console.warn('Statistik data failed to load:', error);
            return { overview: { total_programs: 0, overlapping_programs: 0, coverage_gaps: 0, alignment_percentage: 0 } };
        }),
        fetchProgramData(filters).catch(error => {
            console.warn('Program data failed to load:', error);
            return [];
        })
    ])
    .then(([overlaps, gaps, alignment, statistics, programs]) => {
        console.log('Analysis data loaded:', { overlaps, gaps, alignment, statistics, programs });
        
        analysisData.overlaps = overlaps;
        analysisData.gaps = gaps;
        analysisData.alignment = alignment;
        analysisData.statistics = statistics;
        analysisData.programs = programs;
        
        // Update all displays
        updateOverviewStats(statistics);
        updateOverlapAnalysis(overlaps);
        updateGapAnalysis(gaps);
        updateAlignmentAnalysis(alignment);
        updateProgramMap(programs);
        
        // Update map layers
        updateOverlapMap(overlaps);
        updateGapMap(gaps);
        updateAlignmentMap(alignment);
        
        showNotification('Data analisis berhasil dimuat', 'success');
    })
    .catch(error => {
        console.error('Error loading analysis data:', error);
        showNotification('Gagal memuat data analisis', 'error');
    })
    .finally(() => {
        showLoader(false);
    });
}

/**
 * Run comprehensive analysis
 */
function runAnalysis() {
    showLoader(true);
    
    const filters = getAnalysisFilters();
    
    Promise.all([
        fetchAnalysisData('tumpang-tindih', filters),
        fetchAnalysisData('kesenjangan', filters),
        fetchAnalysisData('keselarasan-rpjmd', filters),
        fetchAnalysisData('statistik', filters),
        fetchProgramData(filters)
    ])
    .then(([overlaps, gaps, alignment, statistics, programs]) => {
        analysisData.overlaps = overlaps;
        analysisData.gaps = gaps;
        analysisData.alignment = alignment;
        analysisData.statistics = statistics;
        analysisData.programs = programs;
        
        // Update all displays
        updateOverviewStats(statistics);
        updateOverlapAnalysis(overlaps);
        updateGapAnalysis(gaps);
        updateAlignmentAnalysis(alignment);
        updateProgramMap(programs);
        
        // Update map layers
        updateOverlapMap(overlaps);
        updateGapMap(gaps);
        updateAlignmentMap(alignment);
        
        showNotification('Analisis berhasil diperbarui', 'success');
    })
    .catch(error => {
        console.error('Error running analysis:', error);
        showNotification('Gagal menjalankan analisis', 'error');
    })
    .finally(() => {
        showLoader(false);
    });
}

/**
 * Get current analysis filters
 */
function getAnalysisFilters() {
    return {
        tahun: document.getElementById('filter-tahun').value,
        sektor_id: document.getElementById('filter-sektor').value,
        opd_id: document.getElementById('filter-opd').value,
        radius: document.getElementById('overlap-radius').value,
        grid_size: document.getElementById('grid-size').value
    };
}

/**
 * Fetch analysis data from API
 */
function fetchAnalysisData(endpoint, filters = {}) {
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            params.append(key, filters[key]);
        }
    });
    
    return fetch(`/analisis/api/${endpoint}?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'API error');
            }
        });
}

/**
 * Fetch program data
 */
function fetchProgramData(filters = {}) {
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            params.append(key, filters[key]);
        }
    });
    
    return fetch(`/peta-program/api/programs?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'API error');
            }
        });
}

/**
 * Update overview statistics display
 */
function updateOverviewStats(stats) {
    // Handle case where stats might have different structure
    const overview = stats.overview || {};
    
    // Safely update overview statistics with null checking
    const totalProgramsStat = document.getElementById('total-programs-stat');
    if (totalProgramsStat) totalProgramsStat.textContent = overview.total_programs || 0;
    
    const overlapCountStat = document.getElementById('overlap-count-stat');
    if (overlapCountStat) overlapCountStat.textContent = overview.overlapping_programs || 0;
    
    const gapCountStat = document.getElementById('gap-count-stat');
    if (gapCountStat) gapCountStat.textContent = overview.coverage_gaps || 0;
    
    const alignmentStat = document.getElementById('alignment-stat');
    if (alignmentStat) alignmentStat.textContent = (overview.alignment_percentage || 0) + '%';
}

/**
 * Update program markers on map
 */
function updateProgramMap(programs) {
    programLayer.clearLayers();
    
    // Handle case where programs might be undefined or empty
    if (!programs || !Array.isArray(programs) || programs.length === 0) {
        console.log('No programs to display on map');
        return;
    }
    
    programs.forEach(program => {
        // Validate program data before creating marker
        if (!program.lat || !program.lng || !program.sektor) {
            console.warn('Invalid program data:', program);
            return;
        }
        
        const marker = L.circleMarker([program.lat, program.lng], {
            radius: 6,
            fillColor: getSectorColor(program.sektor.id),
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        });
        
        marker.bindPopup(createProgramPopup(program));
        programLayer.addLayer(marker);
    });
}

/**
 * Update overlap analysis display
 */
function updateOverlapAnalysis(overlaps) {
    // Handle case where overlaps is an object with overlaps array
    const overlapList = Array.isArray(overlaps) ? overlaps : (overlaps.overlaps || []);
    
    // Update overlap statistics
    const highConflicts = overlapList.filter(o => o.conflict_level === 'Tinggi').length;
    const mediumConflicts = overlapList.filter(o => o.conflict_level === 'Sedang').length;
    const lowConflicts = overlapList.filter(o => o.conflict_level === 'Rendah').length;
    
    // Safely update elements with null checking
    const highConflictElement = document.getElementById('high-conflict');
    if (highConflictElement) highConflictElement.textContent = highConflicts;
    
    const mediumConflictElement = document.getElementById('medium-conflict');
    if (mediumConflictElement) mediumConflictElement.textContent = mediumConflicts;
    
    const lowConflictElement = document.getElementById('low-conflict');
    if (lowConflictElement) lowConflictElement.textContent = lowConflicts;
    
    // Update overlap list
    updateOverlapList(overlapList);
}

/**
 * Update overlap list display
 */
function updateOverlapList(overlaps) {
    const container = document.getElementById('overlap-items');
    container.innerHTML = '';
    
    if (overlaps.length === 0) {
        container.innerHTML = '<div class="no-data">Tidak ada tumpang tindih ditemukan</div>';
        return;
    }
    
    overlaps.forEach(overlap => {
        const item = document.createElement('div');
        item.className = 'overlap-item';
        item.innerHTML = `
            <div class="overlap-header">
                <span class="conflict-level ${overlap.conflict_level.toLowerCase()}">${overlap.conflict_level}</span>
                <span class="conflict-distance">${overlap.distance}m</span>
            </div>
            <div class="overlap-programs">
                <div class="program-info">
                    <h5>${overlap.program1.nama_kegiatan}</h5>
                    <p><strong>Sektor:</strong> ${overlap.program1.sektor}</p>
                    <p><strong>OPD:</strong> ${overlap.program1.opd}</p>
                </div>
                <div class="overlap-vs">⚡</div>
                <div class="program-info">
                    <h5>${overlap.program2.nama_kegiatan}</h5>
                    <p><strong>Sektor:</strong> ${overlap.program2.sektor}</p>
                    <p><strong>OPD:</strong> ${overlap.program2.opd}</p>
                </div>
            </div>
            <div class="overlap-recommendation">
                <strong>Rekomendasi:</strong> ${overlap.recommendation}
            </div>
        `;
        
        item.addEventListener('click', () => {
            showOverlapDetail(overlap);
        });
        
        container.appendChild(item);
    });
}

/**
 * Update gap analysis display
 */
function updateGapAnalysis(gaps) {
    // Handle case where gaps might have different structure
    const gapStats = gaps.statistics || {};
    
    // Safely update elements with null checking
    const totalGaps = document.getElementById('total-gaps');
    if (totalGaps) totalGaps.textContent = gapStats.gap_cells || 0;
    
    const priorityGaps = document.getElementById('priority-gaps');
    if (priorityGaps) priorityGaps.textContent = gapStats.priority_gaps || 0;
    
    const coveragePercentage = document.getElementById('coverage-percentage');
    if (coveragePercentage) coveragePercentage.textContent = (gapStats.coverage_percentage || 0) + '%';
    
    // Update recommendations
    updateGapRecommendations(gaps.recommendations || []);
}

/**
 * Update gap recommendations display
 */
function updateGapRecommendations(recommendations) {
    const container = document.getElementById('gap-recommendations-list');
    container.innerHTML = '';
    
    if (recommendations.length === 0) {
        container.innerHTML = '<div class="no-data">Tidak ada rekomendasi khusus</div>';
        return;
    }
    
    recommendations.forEach(rec => {
        const item = document.createElement('div');
        item.className = `recommendation-item ${rec.type}`;
        item.innerHTML = `
            <div class="rec-header">
                <i class="fas fa-lightbulb"></i>
                <strong>${rec.title}</strong>
            </div>
            <p>${rec.description}</p>
            <div class="rec-action">${rec.action}</div>
        `;
        container.appendChild(item);
    });
}

/**
 * Update alignment analysis display
 */
function updateAlignmentAnalysis(alignment) {
    // Store alignment data for breakdown
    analysisData.alignment = alignment;
    
    // Handle case where alignment might have different structure
    const alignStats = alignment.statistics || {};
    const alignedList = alignment.aligned || [];
    const misalignedList = alignment.misaligned || [];
    
    // Safely update elements with null checking
    const alignedPrograms = document.getElementById('aligned-programs');
    if (alignedPrograms) alignedPrograms.textContent = alignStats.aligned_count || 0;
    
    const misalignedPrograms = document.getElementById('misaligned-programs');
    if (misalignedPrograms) misalignedPrograms.textContent = alignStats.misaligned_count || 0;
    
    const alignmentPercentage = document.getElementById('alignment-percentage');
    if (alignmentPercentage) alignmentPercentage.textContent = (alignStats.alignment_percentage || 0) + '%';
    
    const totalAnalyzedPrograms = document.getElementById('total-analyzed-programs');
    if (totalAnalyzedPrograms) totalAnalyzedPrograms.textContent = alignStats.total_programs || 0;
    
    const centerAlignmentPercentage = document.getElementById('center-alignment-percentage');
    if (centerAlignmentPercentage) centerAlignmentPercentage.textContent = (alignStats.alignment_percentage || 0) + '%';
    
    // Calculate alignment score (out of 100)
    const alignmentScore = Math.round(alignStats.alignment_percentage || 0);
    const alignmentScoreElement = document.getElementById('alignment-score');
    if (alignmentScoreElement) alignmentScoreElement.textContent = alignmentScore;
    
    // Update programs list
    updateAlignmentProgramsList(alignedList, misalignedList);
    
    // Update breakdown data immediately - load default sector breakdown
    loadBreakdownData('sector');
    
    // Update recommendations
    updateAlignmentRecommendations(alignment);
    
    // Update pie chart if Chart.js is available
    if (typeof Chart !== 'undefined') {
        updateAlignmentChart(alignStats);
    }
}

/**
 * Update alignment programs list
 */
function updateAlignmentProgramsList(aligned, misaligned) {
    const container = document.getElementById('programs-list');
    container.innerHTML = '';
    
    const allPrograms = [
        ...aligned.map(p => ({...p, alignment_status: 'aligned'})),
        ...misaligned.map(p => ({...p, alignment_status: 'misaligned'}))
    ];
    
    if (allPrograms.length === 0) {
        container.innerHTML = '<div class="no-data">Tidak ada program untuk ditampilkan</div>';
        return;
    }
    
    allPrograms.forEach(program => {
        const item = document.createElement('div');
        item.className = `program-item ${program.alignment_status}`;
        item.innerHTML = `
            <div class="program-header">
                <h5>${program.nama_kegiatan}</h5>
                <span class="alignment-badge ${program.alignment_status}" style="margin-left: 1rem;">
                    ${program.alignment_status === 'aligned' ? 'Selaras' : 'Tidak Selaras'}
                </span>
            </div>
            <div class="program-details">
                <p><strong>Sektor:</strong> ${program.sektor}</p>
                <p><strong>OPD:</strong> ${program.opd}</p>
                <p><strong>Anggaran:</strong> ${formatCurrency(program.anggaran)}</p>
                ${program.alignment_info ? `<p><strong>Zona RPJMD:</strong> ${program.alignment_info.priority_zone}</p>` : ''}
            </div>
        `;
        container.appendChild(item);
    });
    
    // Initialize filter functionality
    initProgramsFilter(allPrograms);
}

/**
 * Initialize programs filter functionality
 */
function initProgramsFilter(programs) {
    const alignmentFilter = document.getElementById('program-alignment-filter');
    const searchInput = document.getElementById('program-search');
    
    function filterPrograms() {
        const alignmentValue = alignmentFilter.value;
        const searchValue = searchInput.value.toLowerCase();
        
        const items = document.querySelectorAll('.program-item');
        
        items.forEach(item => {
            const isAligned = item.classList.contains('aligned');
            const programName = item.querySelector('h5').textContent.toLowerCase();
            
            let showItem = true;
            
            // Filter by alignment status
            if (alignmentValue) {
                if (alignmentValue === 'aligned' && !isAligned) showItem = false;
                if (alignmentValue === 'misaligned' && isAligned) showItem = false;
            }
            
            // Filter by search term
            if (searchValue && !programName.includes(searchValue)) {
                showItem = false;
            }
            
            item.style.display = showItem ? 'block' : 'none';
        });
    }
    
    alignmentFilter.addEventListener('change', filterPrograms);
    searchInput.addEventListener('input', debounce(filterPrograms, 300));
}

/**
 * Update alignment recommendations
 */
function updateAlignmentRecommendations(alignment) {
    const container = document.getElementById('alignment-recommendations');
    container.innerHTML = '';
    
    const recommendations = generateAlignmentRecommendations(alignment);
    
    if (recommendations.length === 0) {
        container.innerHTML = '<div class="no-data">Tidak ada rekomendasi khusus</div>';
        return;
    }
    
    recommendations.forEach(rec => {
        const card = document.createElement('div');
        card.className = `recommendation-card ${rec.priority}`;
        card.innerHTML = `
            <div class="rec-header">
                <div class="rec-icon">
                    <i class="${rec.icon}"></i>
                </div>
                <div class="rec-title">
                    <h4>${rec.title}</h4>
                    <span class="rec-priority">${rec.priority.toUpperCase()}</span>
                </div>
            </div>
            <div class="rec-description">
                <p>${rec.description}</p>
            </div>
            <div class="rec-actions">
                <strong>Tindakan yang disarankan:</strong>
                <ul>
                    ${rec.actions.map(action => `<li>${action}</li>`).join('')}
                </ul>
            </div>
        `;
        container.appendChild(card);
    });
}

/**
 * Generate alignment recommendations based on analysis
 */
function generateAlignmentRecommendations(alignment) {
    const recommendations = [];
    const alignmentPercentage = alignment.statistics.alignment_percentage || 0;
    
    if (alignmentPercentage < 50) {
        recommendations.push({
            priority: 'urgent',
            icon: 'fas fa-exclamation-triangle',
            title: 'Keselarasan Rendah Memerlukan Perhatian Segera',
            description: 'Kurang dari 50% program berada dalam zona prioritas RPJMD. Hal ini menunjukkan adanya ketidakselarasan yang signifikan dalam perencanaan.',
            actions: [
                'Review ulang lokasi program yang tidak selaras',
                'Pertimbangkan relokasi program ke zona prioritas',
                'Evaluasi kembali penetapan zona prioritas RPJMD',
                'Koordinasi dengan OPD terkait penyesuaian program'
            ]
        });
    }
    
    if (alignment.misaligned.length > 0) {
        const majorMisalignedSectors = getMajorMisalignedSectors(alignment.misaligned);
        if (majorMisalignedSectors.length > 0) {
            recommendations.push({
                priority: 'important',
                icon: 'fas fa-chart-line',
                title: 'Sektor dengan Keselarasan Rendah',
                description: `Sektor ${majorMisalignedSectors.join(', ')} memiliki banyak program di luar zona prioritas RPJMD.`,
                actions: [
                    'Fokus pada peningkatan keselarasan sektor prioritas',
                    'Lakukan assessment mendalam untuk sektor tersebut',
                    'Pertimbangkan penyesuaian strategi sektoral'
                ]
            });
        }
    }
    
    if (alignmentPercentage >= 80) {
        recommendations.push({
            priority: 'normal',
            icon: 'fas fa-thumbs-up',
            title: 'Keselarasan Baik - Pertahankan Kualitas',
            description: 'Tingkat keselarasan dengan RPJMD sudah sangat baik. Fokus pada optimalisasi dan monitoring.',
            actions: [
                'Monitoring berkala untuk mempertahankan keselarasan',
                'Dokumentasi best practices',
                'Share learning ke OPD lain'
            ]
        });
    }
    
    return recommendations;
}

/**
 * Get sectors with major misalignment issues
 */
function getMajorMisalignedSectors(misalignedPrograms) {
    const sectorCounts = {};
    
    misalignedPrograms.forEach(program => {
        const sector = program.sektor;
        sectorCounts[sector] = (sectorCounts[sector] || 0) + 1;
    });
    
    return Object.keys(sectorCounts)
        .filter(sector => sectorCounts[sector] >= 3)
        .sort((a, b) => sectorCounts[b] - sectorCounts[a]);
}

/**
 * Load breakdown data based on selected type
 */
function loadBreakdownData(type) {
    const container = document.getElementById('alignment-breakdown-content');
    
    // Check if we have alignment data
    if (!analysisData.alignment || !analysisData.alignment.by_sector) {
        container.innerHTML = '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data breakdown...</p></div>';
        return;
    }
    
    let content = '';
    
    switch(type) {
        case 'sector':
            content = generateSectorBreakdown(analysisData.alignment.by_sector);
            break;
        case 'opd':
            content = generateOPDBreakdown(analysisData.alignment.by_opd);
            break;
        case 'priority':
            content = generatePriorityBreakdown();
            break;
        default:
            content = generateSectorBreakdown(analysisData.alignment.by_sector);
    }
    
    container.innerHTML = content;
}

/**
 * Generate sector breakdown content
 */
function generateSectorBreakdown(sectorData = {}) {
    let tableRows = '';
    
    if (Object.keys(sectorData).length === 0) {
        tableRows = '<tr><td colspan="5" class="text-center">Tidak ada data sektor</td></tr>';
    } else {
        for (const [sectorName, data] of Object.entries(sectorData)) {
            tableRows += `
                <tr>
                    <td>${sectorName}</td>
                    <td>${data.total || 0}</td>
                    <td class="text-success">${data.aligned || 0}</td>
                    <td class="text-warning">${data.misaligned || 0}</td>
                    <td>
                        <div class="percentage-bar">
                            <div class="percentage-fill" style="width: ${data.alignment_percentage || 0}%"></div>
                            <span class="percentage-text">${data.alignment_percentage || 0}%</span>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    return `
        <div class="breakdown-table">
            <h4 style="margin-bottom: 1rem;">Keselarasan per Sektor</h4>
            <table class="breakdown-data-table">
                <thead>
                    <tr>
                        <th>Sektor</th>
                        <th>Total Program</th>
                        <th>Selaras</th>
                        <th>Tidak Selaras</th>
                        <th>% Keselarasan</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </div>
    `;
}

/**
 * Generate OPD breakdown content
 */
function generateOPDBreakdown(opdData = {}) {
    let tableRows = '';
    
    if (Object.keys(opdData).length === 0) {
        tableRows = '<tr><td colspan="5" class="text-center">Tidak ada data OPD</td></tr>';
    } else {
        for (const [opdName, data] of Object.entries(opdData)) {
            tableRows += `
                <tr>
                    <td>${opdName}</td>
                    <td>${data.total || 0}</td>
                    <td class="text-success">${data.aligned || 0}</td>
                    <td class="text-warning">${data.misaligned || 0}</td>
                    <td>
                        <div class="percentage-bar">
                            <div class="percentage-fill" style="width: ${data.alignment_percentage || 0}%"></div>
                            <span class="percentage-text">${data.alignment_percentage || 0}%</span>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    return `
        <div class="breakdown-table">
            <h4 style="margin-bottom: 1rem;">Keselarasan per OPD</h4>
            <table class="breakdown-data-table">
                <thead>
                    <tr>
                        <th>OPD</th>
                        <th>Total Program</th>
                        <th>Selaras</th>
                        <th>Tidak Selaras</th>
                        <th>% Keselarasan</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </div>
    `;
}

/**
 * Generate priority breakdown content
 */
function generatePriorityBreakdown() {
    return `
        <div class="breakdown-table">
            <h4>Program per Zona Prioritas</h4>
            <div class="priority-zones-summary">
                <div class="zone-card high-priority">
                    <div class="zone-header">
                        <h5>Zona Prioritas Tinggi</h5>
                        <span class="zone-count">18 Program</span>
                    </div>
                    <div class="zone-details">
                        <p>Kawasan strategis pengembangan ekonomi</p>
                        <div class="zone-programs">
                            <span class="program-count selaras">15 Selaras</span>
                            <span class="program-count tidak-selaras">3 Tidak Selaras</span>
                        </div>
                    </div>
                </div>
                
                <div class="zone-card medium-priority">
                    <div class="zone-header">
                        <h5>Zona Prioritas Sedang</h5>
                        <span class="zone-count">8 Program</span>
                    </div>
                    <div class="zone-details">
                        <p>Area pengembangan sosial dan pendidikan</p>
                        <div class="zone-programs">
                            <span class="program-count selaras">6 Selaras</span>
                            <span class="program-count tidak-selaras">2 Tidak Selaras</span>
                        </div>
                    </div>
                </div>
                
                <div class="zone-card low-priority">
                    <div class="zone-header">
                        <h5>Di Luar Zona Prioritas</h5>
                        <span class="zone-count">2 Program</span>
                    </div>
                    <div class="zone-details">
                        <p>Program di luar kawasan prioritas RPJMD</p>
                        <div class="zone-programs">
                            <span class="program-count tidak-selaras">2 Perlu Review</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Update overlap markers on map
 */
function updateOverlapMap(overlaps) {
    overlapLayer.clearLayers();
    
    overlaps.forEach(overlap => {
        // Draw line between overlapping programs
        const line = L.polyline([
            [overlap.program1.koordinat.lat, overlap.program1.koordinat.lng],
            [overlap.program2.koordinat.lat, overlap.program2.koordinat.lng]
        ], {
            color: getConflictColor(overlap.conflict_level),
            weight: 3,
            opacity: 0.8
        });
        
        line.bindPopup(`
            <div class="overlap-popup">
                <h4>Tumpang Tindih ${overlap.conflict_level}</h4>
                <p><strong>Jarak:</strong> ${overlap.distance}m</p>
                <p><strong>Program 1:</strong> ${overlap.program1.nama_kegiatan}</p>
                <p><strong>Program 2:</strong> ${overlap.program2.nama_kegiatan}</p>
            </div>
        `);
        
        overlapLayer.addLayer(line);
    });
}

/**
 * Update gap markers on map
 */
function updateGapMap(gaps) {
    gapLayer.clearLayers();
    
    if (!gaps.gaps) return;
    
    gaps.gaps.forEach(gap => {
        const marker = L.circleMarker([gap.coordinates.lat, gap.coordinates.lng], {
            radius: 4,
            fillColor: getPriorityColor(gap.priority_level),
            color: '#fff',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.6
        });
        
        marker.bindPopup(`
            <div class="gap-popup">
                <h4>Area Kesenjangan</h4>
                <p><strong>Prioritas:</strong> ${gap.priority_level}</p>
                <p><strong>Zona:</strong> ${gap.priority_zone}</p>
                <p><strong>Rekomendasi Sektor:</strong> ${gap.recommended_sectors.join(', ')}</p>
            </div>
        `);
        
        gapLayer.addLayer(marker);
    });
}

/**
 * Update alignment markers on map
 */
function updateAlignmentMap(alignment) {
    // Clear existing RPJMD layer
    rpjmdLayer.clearLayers();
    
    // Load RPJMD zones
    loadRpjmdZones(alignment);
}

/**
 * Load RPJMD zones and display on map
 */
function loadRpjmdZones(alignment = null) {
    fetch('/analisis/api/rpjmd-zones')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                displayRpjmdZones(data.data, alignment);
            } else {
                console.warn('No RPJMD zones data available');
            }
        })
        .catch(error => {
            console.warn('Failed to load RPJMD zones:', error);
        });
}

/**
 * Display RPJMD zones on map
 */
function displayRpjmdZones(zones, alignment = null) {
    rpjmdLayer.clearLayers();
    console.log('Displaying RPJMD zones:', zones.length);
    
    zones.forEach(zone => {
        if (!zone.coordinates) {
            console.warn('Zone without coordinates:', zone.name);
            return;
        }
        
        const color = zone.color || getPriorityZoneColor(zone.priority);
        const fillOpacity = zone.type === 'strategic' ? 0.3 : 0.2;
        
        // Create polygon for zone
        const polygon = L.polygon(zone.coordinates, {
            color: color,
            weight: 2,
            opacity: 0.8,
            fillColor: color,
            fillOpacity: fillOpacity,
            className: `rpjmd-zone ${zone.type} priority-${zone.priority?.toLowerCase() || 'sedang'}`
        });
        
        // Create popup content
        const popupContent = createRpjmdZonePopup(zone, alignment);
        polygon.bindPopup(popupContent);
        
        // Add tooltip
        polygon.bindTooltip(`${zone.name} (${zone.priority || 'Sedang'})`, {
            permanent: false,
            direction: 'center',
            className: 'rpjmd-tooltip'
        });
        
        rpjmdLayer.addLayer(polygon);
    });
    
    console.log(`Loaded ${zones.length} RPJMD zones on map, layer has ${rpjmdLayer.getLayers().length} features`);
}

/**
 * Create popup content for RPJMD zone
 */
function createRpjmdZonePopup(zone, alignment = null) {
    let alignmentInfo = '';
    
    if (alignment) {
        // Count programs in this zone
        const alignedInZone = alignment.aligned.filter(p => 
            p.alignment_info && p.alignment_info.priority_zone === zone.name
        ).length;
        
        const misalignedInZone = alignment.misaligned.filter(p => 
            p.alignment_info && p.alignment_info.priority_zone === zone.name
        ).length;
        
        const totalInZone = alignedInZone + misalignedInZone;
        
        if (totalInZone > 0) {
            alignmentInfo = `
                <div class="zone-alignment-info">
                    <h5>Program dalam Zona:</h5>
                    <div class="zone-program-stats">
                        <span class="aligned-count">${alignedInZone} Selaras</span>
                        <span class="misaligned-count">${misalignedInZone} Tidak Selaras</span>
                        <span class="total-count">Total: ${totalInZone}</span>
                    </div>
                </div>
            `;
        }
    }
    
    return `
        <div class="rpjmd-zone-popup">
            <h4>${zone.name}</h4>
            <div class="zone-details">
                <p><strong>Tipe:</strong> ${zone.type === 'strategic' ? 'Strategis' : 'Tematik'}</p>
                <p><strong>Prioritas:</strong> <span class="priority-badge priority-${zone.priority?.toLowerCase() || 'sedang'}">${zone.priority || 'Sedang'}</span></p>
                ${zone.theme ? `<p><strong>Tema:</strong> ${zone.theme}</p>` : ''}
                ${zone.description ? `<p><strong>Deskripsi:</strong> ${zone.description}</p>` : ''}
            </div>
            ${alignmentInfo}
        </div>
    `;
}

/**
 * Get color for priority zone
 */
function getPriorityZoneColor(priority) {
    const colors = {
        'Tinggi': '#dc2626',   // Red
        'Sedang': '#f59e0b',   // Orange  
        'Rendah': '#10b981'    // Green
    };
    return colors[priority] || '#6b7280'; // Default gray
}

/**
 * Utility functions
 */
function getSectorColor(sektorId) {
    const colors = {
        1: '#3b82f6', // Jalan - Blue
        2: '#06b6d4', // Irigasi - Cyan
        3: '#10b981', // Pendidikan - Green
        4: '#f59e0b', // Kesehatan - Yellow
        5: '#ef4444', // Ekonomi - Red
        6: '#8b5cf6'  // Sosial - Purple
    };
    return colors[sektorId] || '#6b7280';
}

function getConflictColor(level) {
    const colors = {
        'Tinggi': '#dc2626',
        'Sedang': '#f59e0b',
        'Rendah': '#10b981'
    };
    return colors[level] || '#6b7280';
}

function getPriorityColor(level) {
    const colors = {
        'Tinggi': '#dc2626',
        'Sedang': '#f59e0b',
        'Rendah': '#10b981'
    };
    return colors[level] || '#6b7280';
}

function updateMapForTab(tab) {
    // Remove all layers first
    analysisMap.removeLayer(overlapLayer);
    analysisMap.removeLayer(gapLayer);
    analysisMap.removeLayer(rpjmdLayer);
    
    // Show relevant layers based on tab
    switch(tab) {
        case 'overview':
            // Show all layers as per user selection
            break;
        case 'overlap':
            if (document.getElementById('show-overlaps').checked) {
                analysisMap.addLayer(overlapLayer);
            }
            break;
        case 'gaps':
            if (document.getElementById('show-gaps').checked) {
                analysisMap.addLayer(gapLayer);
            }
            break;
        case 'alignment':
            if (document.getElementById('show-rpjmd').checked) {
                analysisMap.addLayer(rpjmdLayer);
            }
            break;
    }
}

function showOverlapDetail(overlap) {
    const modal = document.getElementById('overlap-detail-modal');
    const content = document.getElementById('overlap-detail-content');
    
    content.innerHTML = `
        <div class="overlap-detail">
            <div class="conflict-info">
                <span class="conflict-badge ${overlap.conflict_level.toLowerCase()}">
                    ${overlap.conflict_level}
                </span>
                <span class="distance-info">${overlap.distance} meter</span>
            </div>
            
            <div class="programs-comparison">
                <div class="program-detail">
                    <h4>Program 1</h4>
                    <p><strong>Nama:</strong> ${overlap.program1.nama_kegiatan}</p>
                    <p><strong>Sektor:</strong> ${overlap.program1.sektor}</p>
                    <p><strong>OPD:</strong> ${overlap.program1.opd}</p>
                    <p><strong>Anggaran:</strong> ${formatCurrency(overlap.program1.anggaran)}</p>
                    <p><strong>Koordinat:</strong> ${overlap.program1.koordinat.lat}, ${overlap.program1.koordinat.lng}</p>
                </div>
                
                <div class="program-detail">
                    <h4>Program 2</h4>
                    <p><strong>Nama:</strong> ${overlap.program2.nama_kegiatan}</p>
                    <p><strong>Sektor:</strong> ${overlap.program2.sektor}</p>
                    <p><strong>OPD:</strong> ${overlap.program2.opd}</p>
                    <p><strong>Anggaran:</strong> ${formatCurrency(overlap.program2.anggaran)}</p>
                    <p><strong>Koordinat:</strong> ${overlap.program2.koordinat.lat}, ${overlap.program2.koordinat.lng}</p>
                </div>
            </div>
            
            <div class="recommendation-section">
                <h4>Rekomendasi</h4>
                <p>${overlap.recommendation}</p>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
}

function createProgramPopup(program) {
    return `
        <div class="program-popup">
            <h4>${program.nama_kegiatan}</h4>
            <p><strong>Sektor:</strong> ${program.sektor.nama}</p>
            <p><strong>OPD:</strong> ${program.opd.nama}</p>
            <p><strong>Status:</strong> ${program.status}</p>
            <p><strong>Anggaran:</strong> ${formatCurrency(program.anggaran_total)}</p>
        </div>
    `;
}

function showLoader(show) {
    const loader = document.getElementById('analysis-loader');
    loader.style.display = show ? 'flex' : 'none';
}

function showNotification(message, type) {
    // Simple notification - can be enhanced with a proper notification library
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#ffffff' : '#dc2626'};
        color: black;
        border-radius: 6px;
        z-index: 10000;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

/**
 * Show initial loading states for all tab content
 */
function showInitialLoadingStates() {
    const loadingHTML = '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data analisis...</p></div>';
    
    // Overlap tab loading state
    const overlapItems = document.getElementById('overlap-items');
    if (overlapItems) overlapItems.innerHTML = loadingHTML;
    
    // Gap recommendations loading state
    const gapRecommendations = document.getElementById('gap-recommendations-list');
    if (gapRecommendations) gapRecommendations.innerHTML = loadingHTML;
    
    // Alignment breakdown loading state
    const alignmentBreakdown = document.getElementById('alignment-breakdown-content');
    if (alignmentBreakdown) alignmentBreakdown.innerHTML = loadingHTML;
    
    // Programs list loading state
    const programsList = document.getElementById('programs-list');
    if (programsList) programsList.innerHTML = loadingHTML;
    
    // Alignment recommendations loading state
    const alignmentRecommendations = document.getElementById('alignment-recommendations');
    if (alignmentRecommendations) alignmentRecommendations.innerHTML = loadingHTML;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}