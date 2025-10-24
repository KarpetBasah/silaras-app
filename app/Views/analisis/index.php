<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-chart-line"></i> Analisis Tumpang Tindih & Kesenjangan</h1>
        <p>Algoritma spasial untuk mendeteksi konflik lokasi dan wilayah kosong dalam perencanaan program</p>
    </div>
    
    <!-- Analysis Controls -->
    <div class="analysis-controls">
        <div class="control-panel">
            <div class="control-group">
                <div class="filter-section">
                    <h4><i class="fas fa-filter"></i> Filter Analisis</h4>
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="filter-tahun">Tahun:</label>
                            <select id="filter-tahun" class="form-control-sm">
                                <option value="">Semua Tahun</option>
                                <?php foreach ($tahun_list as $tahun): ?>
                                    <option value="<?= $tahun ?>" <?= date('Y') == $tahun ? 'selected' : '' ?>><?= $tahun ?></option>
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
                            <label for="filter-opd">OPD:</label>
                            <select id="filter-opd" class="form-control-sm">
                                <option value="">Semua OPD</option>
                                <?php foreach ($opd_list as $opd): ?>
                                    <option value="<?= $opd['id'] ?>"><?= $opd['nama_singkat'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="analysis-settings">
                    <h4><i class="fas fa-cogs"></i> Pengaturan Analisis</h4>
                    <div class="settings-row">
                        <div class="setting-group">
                            <label for="overlap-radius">Radius Tumpang Tindih (meter):</label>
                            <input type="number" id="overlap-radius" class="form-control-sm" value="100" min="10" max="1000" step="10">
                        </div>
                        
                        <div class="setting-group">
                            <label for="grid-size">Grid Analisis (derajat):</label>
                            <select id="grid-size" class="form-control-sm">
                                <option value="0.01">Kasar (1km)</option>
                                <option value="0.005" selected>Sedang (500m)</option>
                                <option value="0.002">Halus (200m)</option>
                            </select>
                        </div>
                        
                        <div class="setting-group">
                            <button id="run-analysis" class="btn btn-primary">
                                <i class="fas fa-play"></i> Jalankan Analisis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Analysis Tabs -->
    <div class="analysis-tabs">
        <div class="tab-header">
            <button class="tab-button active" data-tab="overview">
                <i class="fas fa-dashboard"></i> Overview
            </button>
            <button class="tab-button" data-tab="overlap">
                <i class="fas fa-exclamation-triangle"></i> Tumpang Tindih
            </button>
            <button class="tab-button" data-tab="gaps">
                <i class="fas fa-map-marked"></i> Kesenjangan
            </button>
            <button class="tab-button" data-tab="alignment">
                <i class="fas fa-bullseye"></i> Keselarasan RPJMD
            </button>
        </div>
        
        <!-- Overview Tab -->
        <div id="tab-overview" class="tab-content active">
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h4><i class="fas fa-chart-pie"></i> Statistik Keseluruhan</h4>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value" id="total-programs-stat">0</div>
                                <div class="stat-label">Total Program</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value warning" id="overlap-count-stat">0</div>
                                <div class="stat-label">Tumpang Tindih</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value info" id="gap-count-stat">0</div>
                                <div class="stat-label">Area Kosong</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value success" id="alignment-stat">0%</div>
                                <div class="stat-label">Keselarasan RPJMD</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h4><i class="fas fa-map"></i> Peta Analisis</h4>
                        <div class="map-layer-controls">
                            <label class="switch">
                                <input type="checkbox" id="show-programs" checked>
                                <span class="slider">Program</span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" id="show-overlaps">
                                <span class="slider">Tumpang Tindih</span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" id="show-gaps">
                                <span class="slider">Kesenjangan</span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" id="show-rpjmd">
                                <span class="slider">RPJMD</span>
                            </label>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="analysis-map" style="height: 500px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overlap Analysis Tab -->
        <div id="tab-overlap" class="tab-content">
            <div class="analysis-section">
                <div class="section-header">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Deteksi Tumpang Tindih</h3>
                    <p>Program-program yang memiliki lokasi berdekatan dan berpotensi konflik</p>
                </div>
                
                <div class="conflict-summary">
                    <div class="conflict-stats">
                        <div class="conflict-stat high">
                            <div class="conflict-number" id="high-conflict">0</div>
                            <div class="conflict-label">Konflik Tinggi</div>
                        </div>
                        <div class="conflict-stat medium">
                            <div class="conflict-number" id="medium-conflict">0</div>
                            <div class="conflict-label">Konflik Sedang</div>
                        </div>
                        <div class="conflict-stat low">
                            <div class="conflict-number" id="low-conflict">0</div>
                            <div class="conflict-label">Konflik Rendah</div>
                        </div>
                    </div>
                </div>
                
                <div class="overlap-list">
                    <div class="list-header">
                        <h4>Daftar Tumpang Tindih Program</h4>
                        <div class="list-controls">
                            <select id="conflict-filter" class="form-control-sm">
                                <option value="">Semua Level</option>
                                <option value="Tinggi">Konflik Tinggi</option>
                                <option value="Sedang">Konflik Sedang</option>
                                <option value="Rendah">Konflik Rendah</option>
                            </select>
                        </div>
                    </div>
                    <div id="overlap-items" class="overlap-items">
                        <!-- Overlap items will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gap Analysis Tab -->
        <div id="tab-gaps" class="tab-content">
            <div class="analysis-section">
                <div class="section-header">
                    <h3><i class="fas fa-map-marked text-info"></i> Identifikasi Kesenjangan</h3>
                    <p>Wilayah prioritas yang belum memiliki intervensi program pembangunan</p>
                </div>
                
                <div class="gap-summary">
                    <div class="gap-stats">
                        <div class="gap-stat">
                            <div class="gap-number" id="total-gaps">0</div>
                            <div class="gap-label">Total Area Kosong</div>
                        </div>
                        <div class="gap-stat">
                            <div class="gap-number" id="priority-gaps">0</div>
                            <div class="gap-label">Area Prioritas</div>
                        </div>
                        <div class="gap-stat">
                            <div class="gap-number" id="coverage-percentage">0%</div>
                            <div class="gap-label">Cakupan Program</div>
                        </div>
                    </div>
                </div>
                
                <div class="gap-recommendations">
                    <h4><i class="fas fa-lightbulb"></i> Rekomendasi</h4>
                    <div id="gap-recommendations-list">
                        <!-- Recommendations will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- RPJMD Alignment Tab -->
        <div id="tab-alignment" class="tab-content">
            <div class="analysis-section">
                <div class="section-header">
                    <h3><i class="fas fa-bullseye text-success"></i> Analisis Keselarasan RPJMD</h3>
                    <p>Evaluasi kesesuaian program dengan kawasan prioritas RPJMD</p>
                </div>
                
                <div class="alignment-summary">
                    <div class="alignment-chart">
                        <canvas id="alignment-pie-chart" width="200" height="200"></canvas>
                    </div>
                    <div class="alignment-details">
                        <div class="alignment-stat aligned">
                            <div class="alignment-number" id="aligned-programs">0</div>
                            <div class="alignment-label">Program Selaras</div>
                        </div>
                        <div class="alignment-stat misaligned">
                            <div class="alignment-number" id="misaligned-programs">0</div>
                            <div class="alignment-label">Program Tidak Selaras</div>
                        </div>
                        <div class="alignment-stat percentage">
                            <div class="alignment-number" id="alignment-percentage">0%</div>
                            <div class="alignment-label">Persentase Keselarasan</div>
                        </div>
                    </div>
                </div>
                
                <div class="alignment-breakdown">
                    <div class="breakdown-tabs">
                        <button class="breakdown-tab active" data-breakdown="sector">Per Sektor</button>
                        <button class="breakdown-tab" data-breakdown="opd">Per OPD</button>
                    </div>
                    <div id="alignment-breakdown-content">
                        <!-- Breakdown content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="analysis-loader" class="loader-overlay" style="display: none;">
    <div class="loader-content">
        <div class="spinner"></div>
        <p>Menjalankan analisis spasial...</p>
    </div>
</div>

<!-- Overlap Detail Modal -->
<div id="overlap-detail-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Detail Tumpang Tindih</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="overlap-detail-content">
                <!-- Overlap details will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">Tutup</button>
        </div>
    </div>
</div>

<style>
/* Analysis-specific styles */
.analysis-controls {
    background: var(--white);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
}

.control-group {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.filter-section, .analysis-settings {
    padding: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--light-bg);
}

.filter-row, .settings-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.analysis-tabs {
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.tab-header {
    display: flex;
    background: var(--secondary-color);
    border-bottom: 1px solid var(--border-color);
}

.tab-button {
    flex: 1;
    padding: 1rem;
    border: none;
    background: transparent;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-light);
    transition: all 0.3s ease;
}

.tab-button.active {
    background: var(--white);
    color: var(--primary-color);
    border-bottom: 3px solid var(--primary-color);
}

.tab-content {
    display: none;
    padding: 2rem;
}

.tab-content.active {
    display: block;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}

.dashboard-card {
    background: var(--light-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.card-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--white);
}

.card-content {
    padding: 1.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: var(--white);
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-value.warning {
    color: var(--warning-color);
}

.stat-value.info {
    color: var(--accent-color);
}

.stat-value.success {
    color: var(--success-color);
}

.map-layer-controls {
    display: flex;
    gap: 1rem;
}

.switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: relative;
    width: 40px;
    height: 20px;
    background-color: #ccc;
    border-radius: 20px;
    transition: 0.3s;
    cursor: pointer;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 2px;
    top: 2px;
    background-color: white;
    border-radius: 50%;
    transition: 0.3s;
}

input:checked + .slider {
    background-color: var(--primary-color);
}

input:checked + .slider:before {
    transform: translateX(20px);
}

.conflict-stats, .gap-stats, .alignment-summary {
    display: flex;
    justify-content: space-around;
    margin-bottom: 2rem;
}

.conflict-stat, .gap-stat {
    text-align: center;
    padding: 1.5rem;
    background: var(--white);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    min-width: 120px;
}

.conflict-stat.high {
    border-left: 4px solid #dc2626;
}

.conflict-stat.medium {
    border-left: 4px solid #f59e0b;
}

.conflict-stat.low {
    border-left: 4px solid #10b981;
}

.conflict-number, .gap-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.overlap-items {
    max-height: 500px;
    overflow-y: auto;
}

.overlap-item {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.overlap-item:hover {
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loader-content {
    background: var(--white);
    padding: 2rem;
    border-radius: 8px;
    text-align: center;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid var(--border-color);
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-row, .settings-row {
        grid-template-columns: 1fr;
    }
    
    .tab-header {
        flex-wrap: wrap;
    }
    
    .conflict-stats, .gap-stats {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<!-- Analysis JavaScript -->
<script src="<?= base_url('assets/js/analisis.js') ?>"></script>

<script>
// Initialize analysis page
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined') {
        initAnalysisPage();
    } else {
        console.error('Leaflet library not loaded');
    }
});
</script>

<?= $this->endSection() ?>