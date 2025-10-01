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
    
    // Create map
    rpjmdMap = L.map('rpjmd-map', {
        center: [-3.4582, 114.8348], 
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
    const layerToggles = document.querySelectorAll('.layer-toggle input[type="checkbox"]');
    
    layerToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const layerId = this.getAttribute('data-layer');
            
            if (this.checked) {
                showPriorityLayer(layerId);
            } else {
                hidePriorityLayer(layerId);
            }
        });
    });
    
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
        
        if (data.status === 'success') {
            // Clear existing layers
            priorityLayers = {};
            
            // Add strategic areas
            if (data.data.strategicAreas) {
                data.data.strategicAreas.forEach(area => {
                    addStrategicAreaLayer(area);
                });
            }
            
            // Add thematic zones
            if (data.data.thematicZones) {
                data.data.thematicZones.forEach(zone => {
                    addThematicZoneLayer(zone);
                });
            }
            
            console.log('Priority layers loaded:', Object.keys(priorityLayers).length);
        } else {
            console.error('Failed to load priority layers:', data.message);
            showError('Gagal memuat data layer prioritas: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading priority layers:', error);
        showError('Error saat memuat data layer prioritas: ' + error.message);
    }
}

// Add strategic area layer
function addStrategicAreaLayer(area) {
    const layerId = `strategic-${area.id}`;
    
    // Create polygon from coordinates
    const polygon = L.polygon(area.coordinates, {
        color: area.color || '#2563eb',
        fillColor: area.color || '#2563eb',
        fillOpacity: 0.2,
        weight: 2
    });
    
    // Add popup
    polygon.bindPopup(`
        <div class="rpjmd-popup">
            <h4>${area.name}</h4>
            <p><strong>Tipe:</strong> Kawasan Strategis</p>
            <p><strong>Deskripsi:</strong> ${area.description || 'Tidak ada deskripsi'}</p>
            <p><strong>Prioritas:</strong> ${area.priority || 'Sedang'}</p>
        </div>
    `);
    
    // Store layer
    priorityLayers[layerId] = {
        layer: polygon,
        type: 'strategic',
        data: area,
        visible: false
    };
}

// Add thematic zone layer
function addThematicZoneLayer(zone) {
    const layerId = `thematic-${zone.id}`;
    
    // Create polygon from coordinates
    const polygon = L.polygon(zone.coordinates, {
        color: zone.color || '#dc2626',
        fillColor: zone.color || '#dc2626',
        fillOpacity: 0.15,
        weight: 2,
        dashArray: '5, 5'
    });
    
    // Add popup
    polygon.bindPopup(`
        <div class="rpjmd-popup">
            <h4>${zone.name}</h4>
            <p><strong>Tipe:</strong> Zona Tematik</p>
            <p><strong>Tema:</strong> ${zone.theme || 'Tidak ada tema'}</p>
            <p><strong>Deskripsi:</strong> ${zone.description || 'Tidak ada deskripsi'}</p>
        </div>
    `);
    
    // Store layer
    priorityLayers[layerId] = {
        layer: polygon,
        type: 'thematic',
        data: zone,
        visible: false
    };
}

// Show priority layer
function showPriorityLayer(layerId) {
    if (priorityLayers[layerId] && !priorityLayers[layerId].visible) {
        priorityLayers[layerId].layer.addTo(rpjmdMap);
        priorityLayers[layerId].visible = true;
    }
}

// Hide priority layer
function hidePriorityLayer(layerId) {
    if (priorityLayers[layerId] && priorityLayers[layerId].visible) {
        rpjmdMap.removeLayer(priorityLayers[layerId].layer);
        priorityLayers[layerId].visible = false;
    }
}

// Load programs from API
async function loadPrograms() {
    try {
        console.log('Loading programs...');
        const response = await fetch('/peta-program/api/programs');
        
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
    // Remove existing program markers
    rpjmdMap.eachLayer(layer => {
        if (layer.options && layer.options.isProgram) {
            rpjmdMap.removeLayer(layer);
        }
    });
    
    currentPrograms.forEach(program => {
        if (program.lat && program.lng) {
            addProgramMarker(program);
        }
    });
}

// Add program marker to map
function addProgramMarker(program) {
    const markerColor = getStatusColor(program.status);
    
    const marker = L.circleMarker([program.lat, program.lng], {
        radius: 8,
        fillColor: markerColor,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.8,
        isProgram: true
    });
    
    // Popup content with alignment info
    const popupContent = `
        <div class="rpjmd-popup">
            <h4>${program.nama_kegiatan}</h4>
            <p><strong>OPD:</strong> ${program.opd?.nama || 'N/A'}</p>
            <p><strong>Sektor:</strong> ${program.sektor?.nama || 'N/A'}</p>
            <p><strong>Status:</strong> <span class="status-${program.status.toLowerCase()}">${program.status}</span></p>
            <p><strong>Anggaran:</strong> Rp ${formatRupiah(program.anggaran_total || 0)}</p>
            <div class="alignment-status" id="alignment-${program.id}">
                <em>Menganalisis keselarasan...</em>
            </div>
        </div>
    `;
    
    marker.bindPopup(popupContent);
    marker.addTo(rpjmdMap);
    
    // Check alignment when popup opens
    marker.on('popupopen', () => {
        checkProgramAlignment(program);
    });
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
    
    // Filter programs
    const filteredPrograms = currentPrograms.filter(program => {
        let match = true;
        
        if (sektorId && sektorId !== 'all') {
            match = match && program.sektor_id == sektorId;
        }
        
        if (opdId && opdId !== 'all') {
            match = match && program.opd_id == opdId;
        }
        
        if (status && status !== 'all') {
            match = match && program.status.toLowerCase() === status.toLowerCase();
        }
        
        return match;
    });
    
    // Update display
    currentPrograms = filteredPrograms;
    displayPrograms();
}

// Perform alignment analysis
async function performAlignment() {
    showAnalysisPanel();
    updateAnalysisPanel('Menganalisis keselarasan program...', null);
    
    try {
        const response = await fetch('/rpjmd/api/alignment-analysis');
        const data = await response.json();
        
        if (data.status === 'success') {
            currentAnalysis = data.data;
            updateAnalysisPanel('Analisis Selesai', currentAnalysis);
            
            // Update program markers with alignment info
            updateProgramMarkers(currentAnalysis);
            
        } else {
            updateAnalysisPanel('Error: ' + data.message, null);
        }
    } catch (error) {
        console.error('Error performing alignment analysis:', error);
        updateAnalysisPanel('Error melakukan analisis keselarasan', null);
    }
}

// Show analysis panel
function showAnalysisPanel() {
    if (analysisPanel) {
        analysisPanel.style.display = 'flex';
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
            ${analysis.non_aligned_programs.slice(0, 5).map(program => `
                <div class="program-item-small">
                    <div class="program-name">${program.nama_program}</div>
                    <div class="program-details">${program.opd_nama} - ${program.sektor_nama}</div>
                    <a href="#" class="view-detail" onclick="centerMapOnProgram(${program.id})">Lihat di Peta</a>
                </div>
            `).join('')}
            ${analysis.non_aligned_programs.length > 5 ? `
                <p><small>... dan ${analysis.non_aligned_programs.length - 5} program lainnya</small></p>
            ` : ''}
        </div>
        ` : ''}
    `;
}

// Update program markers with alignment results
function updateProgramMarkers(analysis) {
    const nonAlignedIds = analysis.non_aligned_programs.map(p => p.id);
    
    rpjmdMap.eachLayer(layer => {
        if (layer.options && layer.options.isProgram) {
            // Find corresponding program
            const program = currentPrograms.find(p => 
                Math.abs(p.lat - layer.getLatLng().lat) < 0.0001 && 
                Math.abs(p.lng - layer.getLatLng().lng) < 0.0001
            );
            
            if (program) {
                const isAligned = !nonAlignedIds.includes(program.id);
                
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
function centerMapOnProgram(programId) {
    const program = currentPrograms.find(p => p.id === programId);
    if (program && program.lat && program.lng) {
        rpjmdMap.setView([program.lat, program.lng], 15);
        
        // Find and open marker popup
        rpjmdMap.eachLayer(layer => {
            if (layer.options && layer.options.isProgram) {
                if (Math.abs(layer.getLatLng().lat - program.lat) < 0.0001 && 
                    Math.abs(layer.getLatLng().lng - program.lng) < 0.0001) {
                    layer.openPopup();
                }
            }
        });
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
    // You can customize this to use your preferred notification system
    alert(message);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('rpjmd-map')) {
        initRPJMDMap();
    }
});

// Export functions for global access
window.initRPJMDMap = initRPJMDMap;
window.centerMapOnProgram = centerMapOnProgram;