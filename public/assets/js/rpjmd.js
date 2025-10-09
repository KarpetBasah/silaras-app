/**
 * RPJMD Module - Priority Zone Analysis and Program Alignment
 * Manages RPJMD priority layers, program analysis, and alignment statistics
 */

// Global variables
let rpjmdMap = null;
let priorityLayers = {};
let analysisPanel = null;
let currentPrograms = [];
let currentAnalysis = null;

// Initialize RPJMD module
function initRPJMDMap() {
    const mapContainer = document.getElementById('rpjmd-map');
    if (!mapContainer) return;
    
    // Check if map is already initialized
    if (rpjmdMap !== null) {
        console.log('RPJMD Map is already initialized');
        return;
    }
    
    // Clear any existing map instance
    mapContainer.innerHTML = '';
    
    // Create map
    rpjmdMap = L.map('rpjmd-map', {
        center: [-3.4582, 114.8348], // Banjarbaru coordinates (Kalimantan Selatan)
        zoom: 12,
        zoomControl: true
    });
    
    // Add base layers
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });
    
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });
    
    // Default layer
    osmLayer.addTo(rpjmdMap);
    
    // Layer control
    const baseMaps = {
        "OpenStreetMap": osmLayer,
        "Satellite": satelliteLayer
    };
    
    L.control.layers(baseMaps).addTo(rpjmdMap);
    
    // Initialize components
    initLayerControls();
    initAnalysisPanel();
    loadPriorityLayers();
    loadPrograms();
    
    console.log('RPJMD Map initialized successfully');
}

// Initialize layer toggle controls
function initLayerControls() {
    // Filter controls
    const filterInputs = document.querySelectorAll('.filter-group select, .filter-group input');
    filterInputs.forEach(input => {
        input.addEventListener('change', applyFilters);
    });
    
    // Apply Analysis button
    const analyzeBtn = document.getElementById('analyze-alignment');
    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', performAlignment);
    }
    
    // Reset filters button
    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }
    
    // We'll set up layer toggles after loading priority layers
}

// Setup layer toggles dynamically based on loaded data
function setupLayerToggles() {
    // Strategic areas toggles
    Object.keys(priorityLayers).forEach(layerId => {
        const layer = priorityLayers[layerId];
        if (layer.type === 'strategic') {
            const checkbox = document.getElementById(`layer-strategic-${layer.data.id}`);
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        showPriorityLayer(layerId);
                    } else {
                        hidePriorityLayer(layerId);
                    }
                });
                
                // Show layer by default
                showPriorityLayer(layerId);
                checkbox.checked = true;
            }
        } else if (layer.type === 'thematic') {
            const checkbox = document.getElementById(`layer-thematic-${layer.data.id}`);
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        showPriorityLayer(layerId);
                    } else {
                        hidePriorityLayer(layerId);
                    }
                });
                
                // Show layer by default
                showPriorityLayer(layerId);
                checkbox.checked = true;
            }
        }
    });
    
    // Setup "Show All" toggles
    setupShowAllToggles();
}

// Setup show all toggles
function setupShowAllToggles() {
    const strategicAllToggle = document.getElementById('layer-strategic-all');
    const thematicAllToggle = document.getElementById('layer-thematic-all');
    
    if (strategicAllToggle) {
        strategicAllToggle.addEventListener('change', function() {
            Object.keys(priorityLayers).forEach(layerId => {
                const layer = priorityLayers[layerId];
                if (layer.type === 'strategic') {
                    const checkbox = document.getElementById(`layer-strategic-${layer.data.id}`);
                    if (checkbox) {
                        checkbox.checked = this.checked;
                        if (this.checked) {
                            showPriorityLayer(layerId);
                        } else {
                            hidePriorityLayer(layerId);
                        }
                    }
                }
            });
        });
    }
    
    if (thematicAllToggle) {
        thematicAllToggle.addEventListener('change', function() {
            Object.keys(priorityLayers).forEach(layerId => {
                const layer = priorityLayers[layerId];
                if (layer.type === 'thematic') {
                    const checkbox = document.getElementById(`layer-thematic-${layer.data.id}`);
                    if (checkbox) {
                        checkbox.checked = this.checked;
                        if (this.checked) {
                            showPriorityLayer(layerId);
                        } else {
                            hidePriorityLayer(layerId);
                        }
                    }
                }
            });
        });
    }
}

// Initialize analysis panel
function initAnalysisPanel() {
    analysisPanel = document.querySelector('.analysis-panel');
    
    if (analysisPanel) {
        // Close button
        const closeBtn = analysisPanel.querySelector('.panel-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                analysisPanel.style.display = 'none';
            });
        }
        
        // Initially hide panel
        analysisPanel.style.display = 'none';
    }
}

// Load priority layers from API
async function loadPriorityLayers() {
    try {
        console.log('Loading priority layers...');
        const response = await fetch('/rpjmd/api/priority-layers');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Priority layers response:', data);
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            // Clear existing layers
            priorityLayers = {};
            
            // Process the data directly since API returns flat array
            const zones = data.data;
            console.log('Processing zones:', zones.length);
            
            // Group zones by type
            const strategicAreas = zones.filter(zone => zone.type === 'strategic');
            const thematicZones = zones.filter(zone => zone.type === 'thematic');
            
            console.log('Strategic areas:', strategicAreas.length);
            console.log('Thematic zones:', thematicZones.length);
            
            // Create layer toggles in the UI
            if (strategicAreas.length > 0 || thematicZones.length > 0) {
                createLayerToggles(strategicAreas, thematicZones);
                
                // Add strategic areas
                strategicAreas.forEach((area, index) => {
                    addStrategicAreaLayerFromData(area, index);
                });
                
                // Add thematic zones
                thematicZones.forEach((zone, index) => {
                    addThematicZoneLayerFromData(zone, index);
                });
                
                // Setup layer toggle controls after layers are created
                setTimeout(() => {
                    setupLayerToggles();
                }, 100);
                
                console.log('Priority layers loaded successfully:', Object.keys(priorityLayers).length);
                showMessage('Layer prioritas berhasil dimuat (' + Object.keys(priorityLayers).length + ' layer)', 'success');
            } else {
                console.warn('No priority zones found in data');
                showMessage('Tidak ada data zona prioritas yang tersedia', 'warning');
            }
        } else {
            console.error('Failed to load priority layers:', data.message || 'No data returned');
            showError('Gagal memuat data layer prioritas: ' + (data.message || 'Data tidak tersedia'));
        }
    } catch (error) {
        console.error('Error loading priority layers:', error);
        showError('Error saat memuat data layer prioritas: ' + error.message);
    }
}

// Create layer toggles in UI
function createLayerToggles(strategicAreas, thematicZones) {
    // Create strategic area toggles
    const strategicContainer = document.getElementById('strategic-layers-list');
    if (strategicContainer) {
        let strategicHtml = '';
        strategicAreas.forEach((area, index) => {
            const color = getStrategicColor(index);
            strategicHtml += `
                <label class="layer-toggle">
                    <input class="form-check-input" type="checkbox" 
                           id="layer-strategic-${area.id}" checked>
                    <span class="toggle-indicator" style="background-color: ${color};"></span>
                    <span class="toggle-text">${area.name}</span>
                </label>
            `;
        });
        strategicContainer.innerHTML = strategicHtml;
    }
    
    // Create thematic zone toggles
    const thematicContainer = document.getElementById('thematic-layers-list');
    if (thematicContainer) {
        let thematicHtml = '';
        thematicZones.forEach((zone, index) => {
            const color = getThematicColor(index);
            thematicHtml += `
                <label class="layer-toggle">
                    <input class="form-check-input" type="checkbox" 
                           id="layer-thematic-${zone.id}" checked>
                    <span class="toggle-indicator" style="background-color: ${color};"></span>
                    <span class="toggle-text">${zone.name}</span>
                </label>
            `;
        });
        thematicContainer.innerHTML = thematicHtml;
    }
}

// Add strategic area layer from API data
function addStrategicAreaLayerFromData(area, index) {
    const layerId = `strategic-${area.id}`;
    
    try {
        // Parse coordinates
        let coordinates;
        if (typeof area.coordinates === 'string') {
            coordinates = JSON.parse(area.coordinates);
        } else {
            coordinates = area.coordinates;
        }
        
        // Get color for strategic area
        const color = getStrategicColor(index);
        
        // Create polygon from coordinates
        const polygon = L.polygon(coordinates, {
            color: color,
            fillColor: color,
            fillOpacity: 0.3,
            weight: 2,
            opacity: 0.8
        });
        
        // Add popup
        polygon.bindPopup(`
            <div class="rpjmd-popup">
                <h4>${area.name}</h4>
                <p><strong>Tipe:</strong> Kawasan Strategis</p>
                <p><strong>Prioritas:</strong> ${area.priority || 'Sedang'}</p>
                <p><strong>Deskripsi:</strong> ${area.description || 'Tidak ada deskripsi'}</p>
            </div>
        `);
        
        // Store layer
        priorityLayers[layerId] = {
            layer: polygon,
            type: 'strategic',
            data: area,
            visible: true
        };
        
        // Add to map by default
        polygon.addTo(rpjmdMap);
        
        console.log(`Strategic area layer added: ${layerId}`);
        
    } catch (error) {
        console.error('Error creating strategic area layer:', area, error);
    }
}

// Add thematic zone layer from API data
function addThematicZoneLayerFromData(zone, index) {
    const layerId = `thematic-${zone.id}`;
    
    try {
        // Parse coordinates
        let coordinates;
        if (typeof zone.coordinates === 'string') {
            coordinates = JSON.parse(zone.coordinates);
        } else {
            coordinates = zone.coordinates;
        }
        
        // Get color for thematic zone
        const color = getThematicColor(index);
        
        // Create polygon from coordinates
        const polygon = L.polygon(coordinates, {
            color: color,
            fillColor: color,
            fillOpacity: 0.3,
            weight: 2,
            opacity: 0.8
        });
        
        // Add popup
        polygon.bindPopup(`
            <div class="rpjmd-popup">
                <h4>${zone.name}</h4>
                <p><strong>Tipe:</strong> Zona Tematik</p>
                <p><strong>Prioritas:</strong> ${zone.priority || 'Sedang'}</p>
                <p><strong>Deskripsi:</strong> ${zone.description || 'Tidak ada deskripsi'}</p>
            </div>
        `);
        
        // Store layer
        priorityLayers[layerId] = {
            layer: polygon,
            type: 'thematic',
            data: zone,
            visible: true
        };
        
        // Add to map by default
        polygon.addTo(rpjmdMap);
        
        console.log(`Thematic zone layer added: ${layerId}`);
        
    } catch (error) {
        console.error('Error creating thematic zone layer:', zone, error);
    }
}

// Get color for strategic areas
function getStrategicColor(index) {
    const colors = ['#ff6b35', '#e74c3c', '#c0392b', '#a93226', '#922b21'];
    return colors[index % colors.length];
}

// Get color for thematic zones
function getThematicColor(index) {
    const colors = ['#4ecdc4', '#16a085', '#1abc9c', '#17a2b8', '#138496'];
    return colors[index % colors.length];
}

// Show priority layer
function showPriorityLayer(layerId) {
    if (priorityLayers[layerId] && !priorityLayers[layerId].visible) {
        const layer = priorityLayers[layerId].layer;
        layer.addTo(rpjmdMap);
        priorityLayers[layerId].visible = true;
        console.log(`Layer shown: ${layerId}`);
    }
}

// Hide priority layer
function hidePriorityLayer(layerId) {
    if (priorityLayers[layerId] && priorityLayers[layerId].visible) {
        const layer = priorityLayers[layerId].layer;
        rpjmdMap.removeLayer(layer);
        priorityLayers[layerId].visible = false;
        console.log(`Layer hidden: ${layerId}`);
    }
}

// Clear all priority layers
function clearPriorityLayers() {
    Object.keys(priorityLayers).forEach(layerId => {
        if (priorityLayers[layerId].visible) {
            rpjmdMap.removeLayer(priorityLayers[layerId].layer);
        }
    });
    priorityLayers = {};
    console.log('All priority layers cleared');
}

// Load programs from API
// Load programs from RPJMD API
async function loadPrograms() {
    try {
        console.log('Loading programs...');
        const response = await fetch('/rpjmd/api/programs');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Programs response:', data);
        
        if (data.success === true) {
            currentPrograms = data.data || [];
            displayPrograms();
            console.log('Programs loaded:', currentPrograms.length);
        } else {
            console.error('Failed to load programs:', data.message);
            showError('Gagal memuat data program: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading programs:', error);
        showError('Error saat memuat data program: ' + error.message);
    }
}

// Display programs on map
function displayPrograms() {
    console.log('Displaying programs on map...', currentPrograms.length);
    
    // Remove existing program markers
    rpjmdMap.eachLayer(layer => {
        if (layer.options && layer.options.isProgram) {
            rpjmdMap.removeLayer(layer);
        }
    });
    
    // Clear programMarkers array
    programMarkers = [];
    
    let markerCount = 0;
    currentPrograms.forEach((program, index) => {
        console.log(`Processing program ${index + 1}:`, program);
        
        if (program.koordinat_lat && program.koordinat_lng) {
            const lat = parseFloat(program.koordinat_lat);
            const lng = parseFloat(program.koordinat_lng);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                console.log(`Adding marker for program ${program.id} at ${lat}, ${lng}`);
                addProgramMarker(program);
                markerCount++;
            } else {
                console.warn(`Invalid coordinates for program ${program.id}:`, program.koordinat_lat, program.koordinat_lng);
            }
        } else {
            console.warn(`Missing coordinates for program ${program.id}:`, program);
        }
    });
    
    console.log(`Added ${markerCount} program markers to map`);
}

// Add program marker to map
function addProgramMarker(program) {
    console.log(`Adding marker for program:`, program.id, program.nama_kegiatan);
    
    const lat = parseFloat(program.koordinat_lat);
    const lng = parseFloat(program.koordinat_lng);
    
    if (isNaN(lat) || isNaN(lng)) {
        console.error(`Invalid coordinates for program ${program.id}:`, lat, lng);
        return;
    }
    
    const markerColor = getStatusColor(program.status);
    console.log(`Marker color for status '${program.status}':`, markerColor);
    
    const marker = L.circleMarker([lat, lng], {
        radius: 8,
        fillColor: markerColor,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.8,
        isProgram: true
    });
    
    // Popup content with program info
    const popupContent = `
        <div class="rpjmd-popup">
            <h4>${program.nama_kegiatan || program.nama_program || 'Program'}</h4>
            <p><strong>OPD:</strong> ${program.nama_opd || 'N/A'}</p>
            <p><strong>Sektor:</strong> ${program.nama_sektor || 'N/A'}</p>
            <p><strong>Status:</strong> <span class="status-${(program.status || '').toLowerCase()}">${program.status || 'N/A'}</span></p>
            <p><strong>Anggaran:</strong> Rp ${formatRupiah(program.anggaran_total || 0)}</p>
            <p><strong>Koordinat:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
            <div class="alignment-status" id="alignment-${program.id}">
                <em>Klik "Analisis Keselarasan" untuk melihat status keselarasan</em>
            </div>
        </div>
    `;
    
    marker.bindPopup(popupContent);
    
    try {
        marker.addTo(rpjmdMap);
        programMarkers.push(marker);
        console.log(`Marker successfully added for program ${program.id} at [${lat}, ${lng}]`);
    } catch (error) {
        console.error(`Error adding marker for program ${program.id}:`, error);
    }
}

// Check program alignment with priority zones
async function checkProgramAlignment(program) {
    try {
        const response = await fetch(`/rpjmd/api/alignment-analysis?program_id=${program.id}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const alignmentElement = document.getElementById(`alignment-${program.id}`);
            if (alignmentElement) {
                const alignment = data.data;
                
                let statusClass = alignment.aligned ? 'aligned' : 'non-aligned';
                let statusText = alignment.aligned ? 'Selaras dengan RPJMD' : 'Tidak selaras dengan RPJMD';
                
                let zonesHtml = '';
                if (alignment.zones && alignment.zones.length > 0) {
                    zonesHtml = `
                        <div class="alignment-zones">
                            <strong>Zona yang terdampak:</strong><br>
                            ${alignment.zones.map(zone => `<span class="zone-tag">${zone}</span>`).join('')}
                        </div>
                    `;
                }
                
                alignmentElement.innerHTML = `
                    <div class="${statusClass}">
                        <strong>${statusText}</strong>
                        ${alignment.recommendations ? `<p><small>${alignment.recommendations}</small></p>` : ''}
                        ${zonesHtml}
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Error checking alignment:', error);
        const alignmentElement = document.getElementById(`alignment-${program.id}`);
        if (alignmentElement) {
            alignmentElement.innerHTML = '<em>Error menganalisis keselarasan</em>';
        }
    }
}

// Apply filters
function applyFilters() {
    const sektorId = document.getElementById('filter-sektor-rpjmd')?.value;
    const opdId = document.getElementById('filter-opd-rpjmd')?.value;
    const status = document.getElementById('filter-status-rpjmd')?.value;
    
    // Reload programs with filters
    loadProgramsWithFilters(sektorId, opdId, status);
}

// Reset all filters
function resetFilters() {
    // Reset all filter dropdowns to default values
    document.getElementById('filter-tahun-rpjmd').value = '';
    document.getElementById('filter-sektor-rpjmd').value = '';
    document.getElementById('filter-status-rpjmd').value = '';
    document.getElementById('filter-opd-rpjmd').value = '';
    
    // Reload all programs without filters
    loadPrograms();
    
    // Hide analysis panel
    hideAnalysisPanel();
    
    showMessage('Filter berhasil direset', 'success');
}

// Load programs with filters
// Load programs with filters from RPJMD API
async function loadProgramsWithFilters(sektorId = '', opdId = '', status = '') {
    try {
        let url = '/rpjmd/api/programs?';
        const params = [];
        
        if (sektorId && sektorId !== '') params.push(`sektor_id=${sektorId}`);
        if (opdId && opdId !== '') params.push(`opd_id=${opdId}`);
        if (status && status !== '') params.push(`status=${status}`);
        
        if (params.length > 0) {
            url += params.join('&');
        }
        
        console.log('Loading filtered programs:', url);
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Filtered programs response:', data);
        
        if (data.success === true) {
            currentPrograms = data.data || [];
            displayPrograms();
            console.log('Filtered programs loaded:', currentPrograms.length);
        } else {
            console.error('Failed to load filtered programs:', data.message);
            showError('Gagal memuat data program dengan filter');
        }
    } catch (error) {
        console.error('Error loading filtered programs:', error);
        showError('Error saat memuat data program dengan filter');
    }
}

// Perform alignment analysis
async function performAlignment() {
    showAnalysisPanel();
    updateAnalysisPanel('Menganalisis keselarasan program...', null);
    
    try {
        console.log('Starting alignment analysis...');
        
        // Get current filter values
        const sektorId = document.getElementById('filter-sektor-rpjmd')?.value || '';
        const opdId = document.getElementById('filter-opd-rpjmd')?.value || '';
        const status = document.getElementById('filter-status-rpjmd')?.value || '';
        
        // Build URL with filters
        let url = '/rpjmd/api/alignment-analysis';
        const params = [];
        if (sektorId) params.push(`sektor_id=${sektorId}`);
        if (opdId) params.push(`opd_id=${opdId}`);
        if (status) params.push(`status=${status}`);
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        console.log('Alignment analysis URL:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Alignment analysis response:', data);
        
        if (data.status === 'success') {
            currentAnalysis = data.data;
            console.log('Analysis data received:', currentAnalysis);
            
            updateAnalysisPanel('Analisis Selesai', currentAnalysis);
            
            // Update program markers with alignment info
            updateProgramMarkers(currentAnalysis);
            
            showMessage('Analisis keselarasan berhasil dilakukan', 'success');
            
        } else {
            console.error('Alignment analysis failed:', data.message);
            updateAnalysisPanel('Error: ' + data.message, null);
            showError('Gagal melakukan analisis: ' + data.message);
        }
    } catch (error) {
        console.error('Error performing alignment analysis:', error);
        updateAnalysisPanel('Error melakukan analisis keselarasan: ' + error.message, null);
        showError('Error melakukan analisis keselarasan: ' + error.message);
    }
}

// Show analysis panel
function showAnalysisPanel() {
    if (analysisPanel) {
        analysisPanel.style.display = 'flex';
    }
}

// Hide analysis panel
function hideAnalysisPanel() {
    if (analysisPanel) {
        analysisPanel.style.display = 'none';
    }
}

// Update analysis panel content
function updateAnalysisPanel(message, analysis) {
    const content = analysisPanel.querySelector('.panel-content');
    if (!content) return;
    
    if (!analysis) {
        content.innerHTML = `<p>${message}</p>`;
        return;
    }
    
    const aligned = analysis.statistics.aligned || 0;
    const nonAligned = analysis.statistics.non_aligned || 0;
    const total = aligned + nonAligned;
    const alignedPercentage = total > 0 ? Math.round((aligned / total) * 100) : 0;
    
    content.innerHTML = `
        <div class="alignment-stats">
            <div class="stat-row">
                <div class="stat-item aligned">
                    <div class="stat-number">${aligned}</div>
                    <div class="stat-label">Program Selaras</div>
                    <div class="stat-percentage">${alignedPercentage}%</div>
                </div>
                <div class="stat-item non-aligned">
                    <div class="stat-number">${nonAligned}</div>
                    <div class="stat-label">Tidak Selaras</div>
                    <div class="stat-percentage">${100 - alignedPercentage}%</div>
                </div>
            </div>
        </div>
        
        ${nonAligned > 0 ? `
        <div class="non-aligned-programs">
            <h5><i class="fas fa-exclamation-triangle"></i> Program Tidak Selaras</h5>
            ${analysis.non_aligned_programs.slice(0, 5).map(program => {
                const hasCoordinates = program.koordinat_lat && program.koordinat_lng && 
                                     !isNaN(parseFloat(program.koordinat_lat)) && 
                                     !isNaN(parseFloat(program.koordinat_lng));
                
                return `
                    <div class="program-item-small">
                        <div class="program-name">${program.nama_kegiatan || program.nama_program || 'Program'}</div>
                        <div class="program-details">${program.nama_opd || 'N/A'} - ${program.nama_sektor || 'N/A'}</div>
                        ${hasCoordinates ? 
                            `<button type="button" class="view-detail btn-link" onclick="centerMapOnProgram(${program.id}, ${program.koordinat_lat}, ${program.koordinat_lng}); return false;">Lihat di Peta</button>` :
                            `<span class="view-detail" style="color: #666; font-style: italic;">Koordinat tidak tersedia</span>`
                        }
                    </div>
                `;
            }).join('')}
            ${analysis.non_aligned_programs.length > 5 ? `
                <p><small>... dan ${analysis.non_aligned_programs.length - 5} program lainnya</small></p>
            ` : ''}
        </div>
        ` : ''}
    `;
}

// Update program markers with alignment results
function updateProgramMarkers(analysis) {
    console.log('Updating program markers with analysis:', analysis);
    
    if (!analysis || !analysis.non_aligned_programs) {
        console.warn('No analysis data for program markers update');
        return;
    }
    
    const nonAlignedIds = analysis.non_aligned_programs.map(p => p.id);
    console.log('Non-aligned program IDs:', nonAlignedIds);
    
    rpjmdMap.eachLayer(layer => {
        if (layer.options && layer.options.isProgram) {
            // Find corresponding program
            const program = currentPrograms.find(p => 
                Math.abs(p.koordinat_lat - layer.getLatLng().lat) < 0.0001 && 
                Math.abs(p.koordinat_lng - layer.getLatLng().lng) < 0.0001
            );
            
            if (program) {
                const isAligned = !nonAlignedIds.includes(program.id);
                console.log(`Program ${program.id} (${program.nama_program}): ${isAligned ? 'aligned' : 'not aligned'}`);
                
                // Update marker style based on alignment
                layer.setStyle({
                    color: isAligned ? '#22c55e' : '#ef4444', // Green for aligned, red for non-aligned
                    weight: isAligned ? 2 : 3
                });
            }
        }
    });
}

// Center map on specific program
function centerMapOnProgram(programId, lat = null, lng = null) {
    console.log(`Centering map on program ${programId}`, lat, lng);
    
    // If coordinates provided directly, use them
    if (lat !== null && lng !== null && !isNaN(lat) && !isNaN(lng)) {
        const latitude = parseFloat(lat);
        const longitude = parseFloat(lng);
        
        console.log(`Using direct coordinates: ${latitude}, ${longitude}`);
        rpjmdMap.setView([latitude, longitude], 16);
        
        // Find and open marker popup - search in all layers including programMarkers
        let markerFound = false;
        
        // First check in programMarkers array
        if (programMarkers && programMarkers.length > 0) {
            programMarkers.forEach(marker => {
                if (marker && marker.getLatLng) {
                    const layerLatLng = marker.getLatLng();
                    if (Math.abs(layerLatLng.lat - latitude) < 0.0001 && 
                        Math.abs(layerLatLng.lng - longitude) < 0.0001) {
                        marker.openPopup();
                        markerFound = true;
                    }
                }
            });
        }
        
        // If not found in programMarkers, check all map layers
        if (!markerFound) {
            rpjmdMap.eachLayer(layer => {
                if (layer.getLatLng && (layer.options.isProgram || layer._programId)) {
                    const layerLatLng = layer.getLatLng();
                    if (Math.abs(layerLatLng.lat - latitude) < 0.0001 && 
                        Math.abs(layerLatLng.lng - longitude) < 0.0001) {
                        layer.openPopup();
                        markerFound = true;
                    }
                }
            });
        }
        
        if (!markerFound) {
            console.log('Marker not found on map, but centered on coordinates');
            showMessage('Peta telah dipusatkan pada lokasi program', 'info');
        }
        return;
    }
    
    // Fallback: search in currentPrograms
    const program = currentPrograms.find(p => p.id === programId);
    if (program && program.koordinat_lat && program.koordinat_lng) {
        console.log(`Using program from currentPrograms:`, program);
        centerMapOnProgram(programId, program.koordinat_lat, program.koordinat_lng);
    } else {
        console.warn(`Program ${programId} not found or missing coordinates`);
        showMessage('Program tidak ditemukan atau koordinat tidak tersedia', 'warning');
    }
}

// Get status color for markers
function getStatusColor(status) {
    switch (status.toLowerCase()) {
        case 'perencanaan':
            return '#3b82f6'; // Blue
        case 'berjalan':
            return '#eab308'; // Yellow
        case 'selesai':
            return '#22c55e'; // Green
        default:
            return '#6b7280'; // Gray
    }
}

// Format currency
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}

// Show error message
function showError(message) {
    console.error('RPJMD Error:', message);
    // You can customize this to use your preferred notification system
    alert('Error: ' + message);
}

// Show success/info message
function showMessage(message, type = 'info') {
    console.log('RPJMD Message (' + type + '):', message);
    // You can customize this to use your preferred notification system
    if (type === 'success') {
        console.log('✅ ' + message);
    } else if (type === 'warning') {
        console.warn('⚠️ ' + message);
    } else {
        console.info('ℹ️ ' + message);
    }
}

// Show/hide loading indicator
function showLoading() {
    console.log('Loading RPJMD data...');
}

function hideLoading() {
    console.log('RPJMD data loading complete');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add a small delay to ensure Leaflet is fully loaded
    setTimeout(function() {
        if (document.getElementById('rpjmd-map') && typeof L !== 'undefined') {
            initRPJMDMap();
        } else if (typeof L === 'undefined') {
            console.error('Leaflet library not available on DOMContentLoaded');
        }
    }, 100);
});

// Fallback: Initialize when window is fully loaded
window.addEventListener('load', function() {
    // Check if map hasn't been initialized yet
    if (rpjmdMap === null && document.getElementById('rpjmd-map')) {
        if (typeof L !== 'undefined') {
            console.log('Initializing RPJMD Map on window load');
            initRPJMDMap();
        } else {
            console.error('Leaflet library still not available after window load');
            const mapEl = document.getElementById('rpjmd-map');
            if (mapEl) {
                mapEl.innerHTML = 
                    '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; margin: 1rem;">' +
                    '<p><i class="fas fa-exclamation-triangle"></i> Leaflet library tidak tersedia. Silakan refresh halaman.</p></div>';
            }
        }
    }
});

// Export functions for global access
window.initRPJMDMap = initRPJMDMap;
window.centerMapOnProgram = centerMapOnProgram;