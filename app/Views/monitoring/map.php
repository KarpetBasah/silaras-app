<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <div class="header-left">
            <h1><i class="fas fa-map"></i> Peta Monitoring</h1>
            <p>Peta dinamis dengan indikator progress program berdasarkan warna ikon</p>
        </div>
        <div class="header-right">
            <a href="/monitoring" class="btn btn-secondary">
                <i class="fas fa-dashboard"></i> Dashboard
            </a>
        </div>
    </div>
    
    <!-- Map Controls -->
    <div class="map-controls">
        <div class="control-panel">
            <div class="filter-group">
                <label for="filter-tahun">Tahun:</label>
                <select id="filter-tahun" class="form-control-sm">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($tahun_list as $tahun): ?>
                        <option value="<?= $tahun ?>"><?= $tahun ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="filter-sektor">Sektor:</label>
                <select id="filter-sektor" class="form-control-sm">
                    <option value="">Semua Sektor</option>
                    <?php foreach ($sektor_list as $sektor): ?>
                        <option value="<?= $sektor['id'] ?>"><?= $sektor['nama_sektor'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="filter-status">Status Lapangan:</label>
                <select id="filter-status" class="form-control-sm">
                    <option value="">Semua Status</option>
                    <option value="normal">Normal</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="terkendala">Terkendala</option>
                    <option value="dihentikan">Dihentikan</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="filter-opd">OPD:</label>
                <select id="filter-opd" class="form-control-sm">
                    <option value="">Semua OPD</option>
                    <?php foreach ($opd_list as $opd): ?>
                        <option value="<?= $opd['id'] ?>"><?= $opd['nama_singkat'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-actions">
                <button onclick="filterMap()" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button onclick="resetFilter()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="map-container">
        <div id="monitoring-map" class="map"></div>
        
        <!-- Map Legend Overlay (Desktop) -->
        <div class="map-legend-overlay desktop-only">
            <div class="legend-header">
                <h4>Legenda</h4>
                <button onclick="toggleLegend()" class="legend-toggle">
                    <i class="fas fa-chevron-up" id="legend-icon"></i>
                </button>
            </div>
            <div class="legend-content" id="legend-content">
                <div class="legend-section">
                    <h5>Progress</h5>
                    <div class="legend-items">
                        <div class="legend-item">
                            <div class="legend-marker" style="background-color: #ef4444;">
                                <i class="fas fa-circle"></i>
                            </div>
                            <span>0-30% (Rendah)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker" style="background-color: #f59e0b;">
                                <i class="fas fa-circle"></i>
                            </div>
                            <span>31-60% (Sedang)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker" style="background-color: #10b981;">
                                <i class="fas fa-circle"></i>
                            </div>
                            <span>61-100% (Tinggi)</span>
                        </div>
                    </div>
                </div>
                
                <div class="legend-section">
                    <h5>Status Lapangan</h5>
                    <div class="legend-items">
                        <div class="legend-item">
                            <div class="legend-marker normal">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>Normal</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker terlambat">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span>Terlambat</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker terkendala">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <span>Terkendala</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker dihentikan">
                                <i class="fas fa-stop"></i>
                            </div>
                            <span>Dihentikan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map Loading -->
        <div id="map-loading" class="map-loading">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat data monitoring...</p>
            </div>
        </div>
    </div>

    <!-- Mobile Legend (Below Map) -->
    <div class="mobile-legend mobile-only">
        <div class="mobile-legend-content">
            <div class="legend-grid">
                <div class="legend-column">
                    <h4>Legenda Progress</h4>
                    <div class="legend-items-horizontal">
                        <div class="legend-item-mobile">
                            <div class="legend-marker" style="background-color: #ef4444;">
                                <i class="fas fa-circle"></i>
                            </div>
                            <span>0-30%</span>
                        </div>
                        <div class="legend-item-mobile">
                            <div class="legend-marker" style="background-color: #f59e0b;">
                                <i class="fas fa-circle"></i>
                            </div>
                            <span>31-60%</span>
                        </div>
                        <div class="legend-item-mobile">
                            <div class="legend-marker" style="background-color: #10b981;">
                                <i class="fas fa-circle"></i>
                            </div>
                            <span>61-100%</span>
                        </div>
                    </div>
                </div>
                
                <div class="legend-column">
                    <h4>Status Lapangan</h4>
                    <div class="legend-items-horizontal">
                        <div class="legend-item-mobile">
                            <div class="legend-marker normal">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>Normal</span>
                        </div>
                        <div class="legend-item-mobile">
                            <div class="legend-marker terlambat">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span>Terlambat</span>
                        </div>
                        <div class="legend-item-mobile">
                            <div class="legend-marker terkendala">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <span>Terkendala</span>
                        </div>
                        <div class="legend-item-mobile">
                            <div class="legend-marker dihentikan">
                                <i class="fas fa-stop"></i>
                            </div>
                            <span>Dihentikan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Panel -->
    <div id="info-panel" class="info-panel">
        <div class="info-header">
            <h3>Informasi Program</h3>
            <button onclick="closeInfoPanel()" class="close-btn">&times;</button>
        </div>
        <div id="info-content" class="info-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.header-left h1 {
    margin: 0;
    color: var(--text-dark);
}

.header-left p {
    margin: 0.5rem 0 0 0;
    color: var(--text-light);
}

.map-controls {
    background: var(--white);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
}

.control-panel {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: end;
    justify-content: center;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: var(--text-dark);
    font-size: 0.9rem;
}

.form-control-sm {
    padding: 0.5rem;
    border: 2px solid var(--border-color);
    border-radius: 6px;
    font-size: 0.9rem;
    min-width: 120px;
}

.form-control-sm:focus {
    outline: none;
    border-color: var(--primary-color);
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.map-legend-overlay {
    position: absolute;
    top: 20px;
    right: 20px;
    background: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    min-width: 200px;
    max-width: 250px;
}

.legend-header {
    padding: 0.75rem 1rem;
    background: var(--primary-color);
    color: var(--white);
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.legend-header h4 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
}

.legend-toggle {
    background: none;
    border: none;
    color: var(--white);
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 3px;
    transition: background 0.3s ease;
}

.legend-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
}

.legend-content {
    padding: 1rem;
    max-height: 300px;
    overflow-y: auto;
    transition: all 0.3s ease;
}

.legend-content.collapsed {
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
    overflow: hidden;
}

.legend-section {
    margin-bottom: 1rem;
}

.legend-section:last-child {
    margin-bottom: 0;
}

.legend-section h5 {
    margin: 0 0 0.5rem 0;
    color: var(--text-dark);
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.legend-items {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--text-dark);
}

.legend-marker {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 0.6rem;
    flex-shrink: 0;
}

.legend-marker.normal { background-color: var(--success-color); }
.legend-marker.terlambat { background-color: var(--warning-color); }
.legend-marker.terkendala { background-color: var(--error-color); }
.legend-marker.dihentikan { background-color: var(--text-light); }

/* Mobile/Desktop Visibility */
.desktop-only {
    display: block;
}

.mobile-only {
    display: none;
}

/* Mobile Legend (Static Below Map) */
.mobile-legend {
    background: var(--white);
    border-radius: 12px;
    margin-top: 1.5rem;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.mobile-legend-content {
    padding: 1.5rem;
}

.legend-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

.legend-column h4 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--border-color);
}

.legend-items-horizontal {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 1rem;
    justify-items: center;
}

.legend-item-mobile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
    padding: 0.75rem;
    border-radius: 8px;
    background: var(--light-bg);
    transition: all 0.3s ease;
}

.legend-item-mobile:hover {
    background: var(--border-color);
    transform: translateY(-2px);
}

.legend-item-mobile .legend-marker {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 0.7rem;
    flex-shrink: 0;
}

.legend-item-mobile span {
    font-size: 0.75rem;
    color: var(--text-dark);
    font-weight: 500;
    line-height: 1.2;
}

.map-container {
    position: relative;
    background: var(--white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
}

.map {
    width: 100%;
    height: 600px;
    position: relative;
    z-index: 1;
}

.map-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.loading-spinner {
    text-align: center;
    color: var(--primary-color);
}

.loading-spinner i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.info-panel {
    position: fixed;
    top: 50%;
    right: -400px;
    transform: translateY(-50%);
    width: 380px;
    max-height: 80vh;
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    z-index: 1001;
    transition: right 0.3s ease;
    overflow: hidden;
}

.info-panel.active {
    right: 20px;
}

.info-header {
    padding: 1rem 1.5rem;
    background: var(--primary-color);
    color: var(--white);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.close-btn {
    background: none;
    border: none;
    color: var(--white);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s ease;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.info-content {
    padding: 1.5rem;
    max-height: calc(80vh - 80px);
    overflow-y: auto;
}

.program-info {
    margin-bottom: 1.5rem;
}

.program-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.program-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-bottom: 1rem;
}

.program-meta span {
    font-size: 0.9rem;
    color: var(--text-light);
}

.progress-section {
    background: var(--light-bg);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.progress-section h4 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
    font-size: 1rem;
}

.progress-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.progress-item:last-child {
    margin-bottom: 0;
}

.progress-label {
    font-weight: 500;
    color: var(--text-dark);
}

.progress-value {
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    background: var(--white);
}

.monitoring-info {
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}

.monitoring-info h4 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
    font-size: 1rem;
}

.info-item {
    margin-bottom: 0.75rem;
}

.info-label {
    font-weight: 500;
    color: var(--text-dark);
    display: block;
    margin-bottom: 0.25rem;
}

.info-value {
    color: var(--text-light);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

@media (max-width: 1024px) {
    .control-panel {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .control-panel {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions {
        justify-content: center;
    }
    
    .map {
        height: 400px;
    }
    
    /* Hide desktop legend, show mobile legend */
    .desktop-only {
        display: none !important;
    }
    
    .mobile-only {
        display: block !important;
    }
    
    /* Mobile legend grid - single column on small screens */
    .legend-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .legend-items-horizontal {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .legend-item-mobile {
        padding: 0.5rem;
    }
    
    .legend-item-mobile span {
        font-size: 0.7rem;
    }
    
    .info-panel {
        position: fixed;
        top: auto;
        bottom: -100%;
        right: 0;
        left: 0;
        width: auto;
        max-height: 70vh;
        border-radius: 12px 12px 0 0;
        transform: none;
    }
    
    .info-panel.active {
        bottom: 0;
        right: 0;
    }
}

@media (max-width: 480px) {
    .legend-column h4 {
        font-size: 0.9rem;
    }
    
    .legend-items-horizontal {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    
    .legend-item-mobile {
        padding: 0.4rem;
    }
    
    .legend-item-mobile .legend-marker {
        width: 16px;
        height: 16px;
        font-size: 0.6rem;
    }
    
    .legend-item-mobile span {
        font-size: 0.65rem;
    }
}
</style>

<script>
let monitoringMap;
let markersLayer;
let currentMarkers = [];

// Initialize map
function initMap() {
    // Center on Banjarbaru (adjust coordinates as needed)
    monitoringMap = L.map('monitoring-map').setView([-3.4356, 114.8198], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(monitoringMap);
    
    markersLayer = L.layerGroup().addTo(monitoringMap);
    
    // Load initial data
    loadMapData();
}

// Get progress color based on percentage
function getProgressColor(progress) {
    if (progress >= 61) return '#10b981'; // Green
    if (progress >= 31) return '#f59e0b'; // Yellow
    return '#ef4444'; // Red
}

// Get status icon
function getStatusIcon(status) {
    const icons = {
        'normal': 'fa-check',
        'terlambat': 'fa-clock',
        'terkendala': 'fa-exclamation',
        'dihentikan': 'fa-stop'
    };
    return icons[status] || 'fa-question';
}

// Create custom marker
function createProgressMarker(data) {
    try {
        const progressColor = getProgressColor(data.progress_fisik || 0);
        const statusIcon = getStatusIcon(data.status_lapangan || 'normal');
        
        const markerHtml = `
            <div class="progress-marker" style="background-color: ${progressColor}">
                <div class="marker-icon">
                    <i class="${data.sektor_icon || 'fas fa-circle'}"></i>
                </div>
                <div class="marker-progress">${Math.round(data.progress_fisik || 0)}%</div>
                <div class="marker-status">
                    <i class="fas ${statusIcon}"></i>
                </div>
            </div>
        `;
        
        const icon = L.divIcon({
            html: markerHtml,
            className: 'custom-progress-marker',
            iconSize: [60, 60],
            iconAnchor: [30, 30]
        });
        
        const lat = parseFloat(data.program_lat);
        const lng = parseFloat(data.program_lng);
        
        if (isNaN(lat) || isNaN(lng)) {
            console.error('Invalid coordinates:', data.program_lat, data.program_lng);
            return null;
        }
        
        const marker = L.marker([lat, lng], { icon })
            .bindPopup(createPopupContent(data))
            .on('click', () => showProgramInfo(data));
        
        return marker;
    } catch (error) {
        console.error('Error creating marker:', error, data);
        return null;
    }
}

// Create popup content
function createPopupContent(data) {
    const formatDate = (dateStr) => {
        try {
            return new Date(dateStr).toLocaleDateString('id-ID');
        } catch {
            return 'N/A';
        }
    };
    
    const formatCurrency = (amount) => {
        try {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        } catch {
            return 'Rp 0';
        }
    };
    
    return `
        <div class="marker-popup">
            <h4>${data.nama_kegiatan || 'Program Tidak Dikenal'}</h4>
            <div class="popup-info">
                <p><strong>Kode:</strong> ${data.kode_program || 'N/A'}</p>
                <p><strong>OPD:</strong> ${data.opd_nama || 'N/A'}</p>
                <p><strong>Sektor:</strong> ${data.nama_sektor || 'N/A'}</p>
                <p><strong>Progress Fisik:</strong> ${Math.round(data.progress_fisik || 0)}%</p>
                <p><strong>Progress Keuangan:</strong> ${Math.round(data.progress_keuangan || 0)}%</p>
                <p><strong>Status:</strong> ${data.status_lapangan || 'Normal'}</p>
                <p><strong>Monitoring Terakhir:</strong> ${formatDate(data.tanggal_monitoring)}</p>
                <p><strong>Anggaran:</strong> ${formatCurrency(data.anggaran_total)}</p>
            </div>
            <div class="popup-actions">
                <button onclick="showProgramInfoFromData(this)" class="btn btn-sm btn-primary" 
                        data-program='${JSON.stringify(data)}'>
                    Detail
                </button>
                <button onclick="inputProgress(${data.program_id || 0})" class="btn btn-sm btn-success">
                    Update Progress
                </button>
            </div>
        </div>
    `;
}

// Load map data
async function loadMapData() {
    const loading = document.getElementById('map-loading');
    loading.style.display = 'flex';
    
    try {
        const params = new URLSearchParams({
            sektor: document.getElementById('filter-sektor')?.value || '',
            opd: document.getElementById('filter-opd')?.value || '',
            tahun: document.getElementById('filter-tahun')?.value || '',
            status_lapangan: document.getElementById('filter-status')?.value || ''
        });
        
        console.log('Loading map data with params:', params.toString());
        
        const response = await fetch(`<?= base_url('monitoring/getMapData') ?>?${params}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Response is not JSON');
        }
        
        const result = await response.json();
        console.log('Map data response:', result);
        console.log('Filters applied:', result.filters_applied);
        console.log('Data count received:', result.count);
        
        // Show filter notification
        showFilterNotification(result.filters_applied, result.count);
        
        if (result.success && result.data) {
            updateMapMarkers(result.data);
        } else {
            console.error('API returned error:', result.message || 'Unknown error');
            updateMapMarkers([]); // Show empty map
        }
    } catch (error) {
        console.error('Error loading map data:', error);
        updateMapMarkers([]); // Show empty map on error
    } finally {
        loading.style.display = 'none';
    }
}

// Update map markers
function updateMapMarkers(data) {
    // Clear existing markers
    if (markersLayer) {
        markersLayer.clearLayers();
    }
    currentMarkers = [];
    
    // Ensure data is array
    if (!Array.isArray(data)) {
        console.warn('Data is not an array:', data);
        return;
    }
    
    // Add new markers
    data.forEach(item => {
        try {
            if (item.program_lat && item.program_lng && 
                !isNaN(parseFloat(item.program_lat)) && 
                !isNaN(parseFloat(item.program_lng))) {
                const marker = createProgressMarker(item);
                if (markersLayer && marker) {
                    markersLayer.addLayer(marker);
                    currentMarkers.push({ marker, data: item });
                }
            } else {
                console.warn('Invalid coordinates for item:', item);
            }
        } catch (error) {
            console.error('Error creating marker for item:', item, error);
        }
    });
    
    // Fit map to markers if data exists
    if (currentMarkers.length > 0) {
        try {
            const group = new L.featureGroup(currentMarkers.map(m => m.marker));
            monitoringMap.fitBounds(group.getBounds().pad(0.1));
        } catch (error) {
            console.error('Error fitting bounds:', error);
        }
    } else {
        console.log('No valid markers to display');
    }
}

// Show program info from data attribute
function showProgramInfoFromData(button) {
    try {
        const data = JSON.parse(button.getAttribute('data-program'));
        showProgramInfo(data);
    } catch (error) {
        console.error('Error parsing program data:', error);
    }
}

// Show program info in panel
function showProgramInfo(data) {
    if (!data) {
        console.error('No data provided to showProgramInfo');
        return;
    }
    
    const panel = document.getElementById('info-panel');
    const content = document.getElementById('info-content');
    
    const formatDate = (dateStr) => {
        try {
            return new Date(dateStr).toLocaleDateString('id-ID');
        } catch {
            return 'N/A';
        }
    };
    
    const formatCurrency = (amount) => {
        try {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        } catch {
            return 'Rp 0';
        }
    };
    
    content.innerHTML = `
        <div class="program-info">
            <div class="program-title">${data.nama_kegiatan || 'Program Tidak Dikenal'}</div>
            <div class="program-meta">
                <span><strong>Kode Program:</strong> ${data.kode_program || 'N/A'}</span>
                <span><strong>OPD:</strong> ${data.opd_nama || 'N/A'}</span>
                <span><strong>Sektor:</strong> ${data.nama_sektor || 'N/A'}</span>
                <span><strong>Tahun:</strong> ${data.tahun_pelaksanaan || 'N/A'}</span>
            </div>
        </div>
        
        <div class="progress-section">
            <h4>Progress Terkini</h4>
            <div class="progress-item">
                <span class="progress-label">Progress Fisik</span>
                <span class="progress-value" style="color: ${getProgressColor(data.progress_fisik || 0)}">${Math.round(data.progress_fisik || 0)}%</span>
            </div>
            <div class="progress-item">
                <span class="progress-label">Progress Keuangan</span>
                <span class="progress-value" style="color: ${getProgressColor(data.progress_keuangan || 0)}">${Math.round(data.progress_keuangan || 0)}%</span>
            </div>
            <div class="progress-item">
                <span class="progress-label">Realisasi Anggaran</span>
                <span class="progress-value">${formatCurrency(data.anggaran_realisasi)}</span>
            </div>
        </div>
        
        <div class="monitoring-info">
            <h4>Info Monitoring Terakhir</h4>
            <div class="info-item">
                <span class="info-label">Tanggal Monitoring:</span>
                <span class="info-value">${formatDate(data.tanggal_monitoring)}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status Lapangan:</span>
                <span class="info-value">${data.status_lapangan || 'Normal'}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Petugas Monitoring:</span>
                <span class="info-value">${data.validator_name || 'N/A'}</span>
            </div>
            ${data.kendala ? `
                <div class="info-item">
                    <span class="info-label">Kendala:</span>
                    <span class="info-value">${data.kendala}</span>
                </div>
            ` : ''}
            
            <div class="action-buttons">
                <button onclick="inputProgress(${data.program_id || 0})" class="btn btn-success btn-sm">
                    <i class="fas fa-edit"></i> Update Progress
                </button>
                <button onclick="closeInfoPanel()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    `;
    
    panel.classList.add('active');
}

// Close info panel
function closeInfoPanel() {
    document.getElementById('info-panel').classList.remove('active');
}

// Toggle legend visibility
function toggleLegend() {
    const content = document.getElementById('legend-content');
    const icon = document.getElementById('legend-icon');
    
    if (content.classList.contains('collapsed')) {
        content.classList.remove('collapsed');
        icon.className = 'fas fa-chevron-up';
    } else {
        content.classList.add('collapsed');
        icon.className = 'fas fa-chevron-down';
    }
}

// Show filter notification
function showFilterNotification(filters, count) {
    const filtersCount = Object.keys(filters || {}).length;
    let message = `Menampilkan ${count} program`;
    
    if (filtersCount > 0) {
        const filterNames = [];
        if (filters.sektor_id) filterNames.push('Sektor');
        if (filters.opd_id) filterNames.push('OPD');
        if (filters.tahun) filterNames.push('Tahun');
        if (filters.status_lapangan) filterNames.push('Status');
        
        message += ` (difilter berdasarkan: ${filterNames.join(', ')})`;
    }
    
    // Create or update notification
    let notification = document.getElementById('filter-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'filter-notification';
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        `;
        document.body.appendChild(notification);
    }
    
    notification.textContent = message;
    notification.style.display = 'block';
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.style.display = 'none';
                notification.style.opacity = '1';
            }, 300);
        }
    }, 3000);
}

// Filter map
function filterMap() {
    loadMapData();
}

// Reset filter
function resetFilter() {
    document.getElementById('filter-tahun').value = '';
    document.getElementById('filter-sektor').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-opd').value = '';
    loadMapData();
}

// Input progress navigation
function inputProgress(programId) {
    window.location.href = `/monitoring/input-progress/${programId}`;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Add event listeners for automatic filtering
    const filterElements = ['filter-tahun', 'filter-sektor', 'filter-status', 'filter-opd'];
    filterElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', function() {
                console.log(`Filter ${id} changed to:`, this.value);
                loadMapData(); // Auto-reload when filter changes
            });
        }
    });
});

// Add custom CSS for markers
const style = document.createElement('style');
style.textContent = `
    .custom-progress-marker {
        background: none !important;
        border: none !important;
    }
    
    .progress-marker {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        position: relative;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .progress-marker:hover {
        transform: scale(1.1);
        z-index: 1000;
    }
    
    .marker-icon {
        color: white;
        font-size: 16px;
        margin-bottom: 2px;
    }
    
    .marker-progress {
        color: white;
        font-size: 10px;
        font-weight: bold;
        line-height: 1;
    }
    
    .marker-status {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #666;
        border: 2px solid #ddd;
    }
    
    .marker-popup {
        min-width: 250px;
    }
    
    .marker-popup h4 {
        margin: 0 0 10px 0;
        color: var(--text-dark);
        font-size: 14px;
    }
    
    .popup-info p {
        margin: 5px 0;
        font-size: 12px;
    }
    
    .popup-actions {
        margin-top: 10px;
        display: flex;
        gap: 5px;
    }
    
    .popup-actions .btn {
        padding: 4px 8px;
        font-size: 11px;
    }
`;
document.head.appendChild(style);
</script>

<?= $this->endSection() ?>