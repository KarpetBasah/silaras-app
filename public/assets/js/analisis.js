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
        if (this.checked) {
            analysisMap.addLayer(rpjmdLayer);
        } else {
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
            
            // Update map view based on selected tab
            updateMapForTab(targetTab);
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
        fetchAnalysisData('statistik', filters),
        fetchProgramData(filters)
    ])
    .then(([statistics, programs]) => {
        analysisData.statistics = statistics;
        analysisData.programs = programs;
        
        updateOverviewStats(statistics);
        updateProgramMap(programs);
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
        fetchAnalysisData('statistik', filters)
    ])
    .then(([overlaps, gaps, alignment, statistics]) => {
        analysisData.overlaps = overlaps;
        analysisData.gaps = gaps;
        analysisData.alignment = alignment;
        analysisData.statistics = statistics;
        
        // Update all displays
        updateOverviewStats(statistics);
        updateOverlapAnalysis(overlaps);
        updateGapAnalysis(gaps);
        updateAlignmentAnalysis(alignment);
        
        // Update map layers
        updateOverlapMap(overlaps);
        updateGapMap(gaps);
        updateAlignmentMap(alignment);
        
        showNotification('Analisis berhasil dijalankan', 'success');
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
    document.getElementById('total-programs-stat').textContent = stats.overview.total_programs || 0;
    document.getElementById('overlap-count-stat').textContent = stats.overview.overlapping_programs || 0;
    document.getElementById('gap-count-stat').textContent = stats.overview.coverage_gaps || 0;
    document.getElementById('alignment-stat').textContent = (stats.overview.alignment_percentage || 0) + '%';
}

/**
 * Update program markers on map
 */
function updateProgramMap(programs) {
    programLayer.clearLayers();
    
    programs.forEach(program => {
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
    // Update overlap statistics
    const highConflicts = overlaps.filter(o => o.conflict_level === 'Tinggi').length;
    const mediumConflicts = overlaps.filter(o => o.conflict_level === 'Sedang').length;
    const lowConflicts = overlaps.filter(o => o.conflict_level === 'Rendah').length;
    
    document.getElementById('high-conflict').textContent = highConflicts;
    document.getElementById('medium-conflict').textContent = mediumConflicts;
    document.getElementById('low-conflict').textContent = lowConflicts;
    
    // Update overlap list
    updateOverlapList(overlaps);
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
    document.getElementById('total-gaps').textContent = gaps.statistics.gap_cells || 0;
    document.getElementById('priority-gaps').textContent = gaps.statistics.priority_gaps || 0;
    document.getElementById('coverage-percentage').textContent = (gaps.statistics.coverage_percentage || 0) + '%';
    
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
    document.getElementById('aligned-programs').textContent = alignment.statistics.aligned_count || 0;
    document.getElementById('misaligned-programs').textContent = alignment.statistics.misaligned_count || 0;
    document.getElementById('alignment-percentage').textContent = (alignment.statistics.alignment_percentage || 0) + '%';
    
    // Update pie chart if Chart.js is available
    if (typeof Chart !== 'undefined') {
        updateAlignmentChart(alignment.statistics);
    }
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
    // This would show RPJMD zones and program alignment status
    // Implementation depends on RPJMD zone data structure
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
        background: ${type === 'success' ? '#10b981' : '#dc2626'};
        color: white;
        border-radius: 6px;
        z-index: 10000;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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