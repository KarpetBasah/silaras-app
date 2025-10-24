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
                                <span class="slider"></span>
                                <span class="switch-label">Program</span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" id="show-overlaps">
                                <span class="slider"></span>
                                <span class="switch-label">Tumpang Tindih</span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" id="show-gaps">
                                <span class="slider"></span>
                                <span class="switch-label">Kesenjangan</span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" id="show-rpjmd">
                                <span class="slider"></span>
                                <span class="switch-label">RPJMD</span>
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
                
                <!-- Alignment Overview -->
                <div class="alignment-overview">
                    <div class="alignment-chart-container">
                        <div class="chart-wrapper">
                            <canvas id="alignment-pie-chart" width="250" height="250"></canvas>
                            <div class="chart-center-text">
                                <span class="center-percentage" id="center-alignment-percentage">0%</span>
                                <span class="center-label">Keselarasan</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alignment-stats-grid">
                        <div class="alignment-stat aligned">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="alignment-number" id="aligned-programs">0</div>
                                <div class="alignment-label">Program Selaras</div>
                                <div class="stat-description">Berada dalam zona prioritas RPJMD</div>
                            </div>
                        </div>
                        
                        <div class="alignment-stat misaligned">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="alignment-number" id="misaligned-programs">0</div>
                                <div class="alignment-label">Program Tidak Selaras</div>
                                <div class="stat-description">Di luar zona prioritas RPJMD</div>
                            </div>
                        </div>
                        
                        <div class="alignment-stat total">
                            <div class="stat-icon">
                                <i class="fas fa-list-alt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="alignment-number" id="total-analyzed-programs">0</div>
                                <div class="alignment-label">Total Program</div>
                                <div class="stat-description">Yang dianalisis keselarasannya</div>
                            </div>
                        </div>
                        
                        <div class="alignment-stat score">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="alignment-number" id="alignment-score">0</div>
                                <div class="alignment-label">Skor Keselarasan</div>
                                <div class="stat-description">Dari skala 100</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Analysis -->
                <div class="alignment-details-section">
                    <div class="section-tabs">
                        <button class="section-tab active" data-section="breakdown">
                            <i class="fas fa-chart-bar"></i> Breakdown Analisis
                        </button>
                        <button class="section-tab" data-section="programs">
                            <i class="fas fa-list"></i> Daftar Program
                        </button>
                        <button class="section-tab" data-section="recommendations">
                            <i class="fas fa-lightbulb"></i> Rekomendasi
                        </button>
                    </div>
                    
                    <!-- Breakdown Content -->
                    <div class="section-content active" id="section-breakdown">
                        <div class="breakdown-controls">
                            <div class="breakdown-tabs">
                                <button class="breakdown-tab active" data-breakdown="sector">
                                    <i class="fas fa-industry"></i> Per Sektor
                                </button>
                                <button class="breakdown-tab" data-breakdown="opd">
                                    <i class="fas fa-building"></i> Per OPD
                                </button>
                                <button class="breakdown-tab" data-breakdown="priority">
                                    <i class="fas fa-star"></i> Per Prioritas
                                </button>
                            </div>
                        </div>
                        <div id="alignment-breakdown-content" class="breakdown-content">
                            <div class="loading-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Memuat data breakdown...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Programs List Content -->
                    <div class="section-content" id="section-programs">
                        <div class="programs-filter">
                            <div class="filter-controls">
                                <select id="program-alignment-filter" class="form-control-sm">
                                    <option value="">Semua Program</option>
                                    <option value="aligned">Program Selaras</option>
                                    <option value="misaligned">Program Tidak Selaras</option>
                                </select>
                                <input type="text" id="program-search" class="form-control-sm" placeholder="Cari program...">
                            </div>
                        </div>
                        <div id="programs-list" class="programs-list">
                            <div class="loading-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Memuat daftar program...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommendations Content -->
                    <div class="section-content" id="section-recommendations">
                        <div id="alignment-recommendations" class="recommendations-container">
                            <div class="loading-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Menganalisis rekomendasi...</p>
                            </div>
                        </div>
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
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.filter-section, .analysis-settings {
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--light-bg);
    height: fit-content;
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
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
    margin-left: auto;
    padding: 0.5rem 0;
}

.switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    white-space: nowrap;
    min-width: fit-content;
}

.switch-label {
    color: var(--text-dark);
    font-weight: 500;
    user-select: none;
    cursor: pointer;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: relative;
    width: 35px;
    height: 18px;
    background-color: #ccc;
    border-radius: 18px;
    transition: 0.3s;
    cursor: pointer;
    flex-shrink: 0;
}

.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
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
    transform: translateX(17px);
}

.conflict-stats, .gap-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 2rem;
    margin-top: 2rem;
}

/* RPJMD Alignment Specific Styles */
.alignment-overview {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    margin-bottom: 2rem;
    background: var(--white);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.alignment-chart-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.chart-wrapper {
    position: relative;
    display: inline-block;
}

.chart-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
}

.center-percentage {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.center-label {
    display: block;
    font-size: 0.9rem;
    color: var(--text-light);
    margin-top: 0.25rem;
}

.alignment-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
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

.alignment-stat {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--light-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.alignment-stat:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.alignment-stat.aligned {
    border-left: 4px solid var(--success-color);
}

.alignment-stat.misaligned {
    border-left: 4px solid var(--warning-color);
}

.alignment-stat.total {
    border-left: 4px solid var(--accent-color);
}

.alignment-stat.score {
    border-left: 4px solid var(--primary-color);
}

.alignment-stat .stat-icon {
    font-size: 2rem;
    color: var(--primary-color);
    min-width: 40px;
}

.alignment-stat.aligned .stat-icon {
    color: var(--success-color);
}

.alignment-stat.misaligned .stat-icon {
    color: var(--warning-color);
}

.alignment-stat.total .stat-icon {
    color: var(--accent-color);
}

.alignment-stat .stat-content {
    flex: 1;
}

.alignment-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--text-dark);
    line-height: 1;
}

.alignment-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-top: 0.25rem;
}

.stat-description {
    font-size: 0.85rem;
    color: var(--text-light);
    margin-top: 0.25rem;
}

.alignment-details-section {
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.section-tabs {
    display: flex;
    background: var(--secondary-color);
    border-bottom: 1px solid var(--border-color);
}

.section-tab {
    flex: 1;
    padding: 1rem 1.5rem;
    border: none;
    background: transparent;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-light);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.section-tab.active {
    background: var(--white);
    color: var(--primary-color);
    border-bottom: 3px solid var(--primary-color);
}

.section-tab:hover:not(.active) {
    color: var(--text-dark);
    background: rgba(255, 255, 255, 0.5);
}

.section-content {
    display: none;
    padding: 2rem;
}

.section-content.active {
    display: block;
}

.breakdown-controls {
    margin-bottom: 2rem;
}

.breakdown-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.breakdown-tab {
    padding: 0.75rem 1.5rem;
    border: 1px solid var(--border-color);
    background: var(--light-bg);
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-dark);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.breakdown-tab.active {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.breakdown-tab:hover:not(.active) {
    background: var(--white);
    box-shadow: var(--shadow);
}

.breakdown-content {
    min-height: 300px;
}

.programs-filter {
    margin-bottom: 2rem;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.programs-list {
    max-height: 500px;
    overflow-y: auto;
}

.program-item {
    padding: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 1rem;
    background: var(--light-bg);
    transition: all 0.3s ease;
}

.program-item:hover {
    box-shadow: var(--shadow);
    transform: translateY(-1px);
}

.program-item.aligned {
    border-left: 4px solid var(--success-color);
}

.program-item.misaligned {
    border-left: 4px solid var(--warning-color);
}

.recommendations-container {
    min-height: 300px;
}

.recommendation-card {
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 1rem;
    background: var(--light-bg);
}

.recommendation-card.urgent {
    border-left: 4px solid #dc2626;
}

.recommendation-card.important {
    border-left: 4px solid var(--warning-color);
}

.recommendation-card.normal {
    border-left: 4px solid var(--accent-color);
}

.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--text-light);
}

.loading-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.alignment-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.alignment-badge.aligned {
    background: var(--success-color);
    color: white;
}

.alignment-badge.misaligned {
    background: var(--warning-color);
    color: white;
}

.program-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.program-details p {
    margin: 0.25rem 0;
    font-size: 0.9rem;
}

.rec-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.rec-icon {
    font-size: 1.5rem;
    color: var(--primary-color);
    min-width: 30px;
}

.rec-title h4 {
    margin: 0;
    color: var(--text-dark);
}

.rec-priority {
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    background: var(--primary-color);
    color: white;
    margin-top: 0.25rem;
    display: inline-block;
}

.rec-description {
    margin-bottom: 1rem;
    color: var(--text-light);
}

.rec-actions ul {
    margin: 0.5rem 0 0 1rem;
    color: var(--text-dark);
}

.rec-actions li {
    margin-bottom: 0.25rem;
}

.breakdown-chart {
    margin-bottom: 2rem;
}

.breakdown-table {
    overflow-x: auto;
}

.breakdown-data-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.breakdown-data-table th,
.breakdown-data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.breakdown-data-table th {
    background: var(--primary-color);
    color: white;
    font-weight: 600;
}

.breakdown-data-table tr:hover {
    background: var(--light-bg);
}

.priority-zones-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.zone-card {
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--white);
    transition: all 0.3s ease;
}

.zone-card:hover {
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.zone-card.high-priority {
    border-left: 4px solid #dc2626;
}

.zone-card.medium-priority {
    border-left: 4px solid var(--warning-color);
}

.zone-card.low-priority {
    border-left: 4px solid var(--text-light);
}

.zone-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.zone-header h5 {
    margin: 0;
    color: var(--text-dark);
}

.zone-count {
    font-weight: bold;
    color: var(--primary-color);
    background: var(--light-bg);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
}

.zone-details p {
    color: var(--text-light);
    margin-bottom: 1rem;
}

.zone-programs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.program-count {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.program-count.selaras {
    background: var(--success-color);
    color: white;
}

.program-count.tidak-selaras {
    background: var(--warning-color);
    color: white;
}

.chart-placeholder {
    background: var(--light-bg);
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    color: var(--text-light);
    margin-top: 1rem;
}

.no-data {
    text-align: center;
    padding: 2rem;
    color: var(--text-light);
    font-style: italic;
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

@media (max-width: 1024px) and (min-width: 769px) {
    .control-group {
        gap: 1.5rem;
    }
    
    .filter-section, .analysis-settings {
        padding: 1.25rem;
    }
    
    .map-layer-controls {
        gap: 0.75rem;
    }
    
    .switch {
        font-size: 0.8rem;
    }
    
    .switch-label {
        font-size: 0.8rem;
    }
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
    
    .control-group {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .map-layer-controls {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
        margin-left: 0;
        margin-top: 0.5rem;
    }
    
    .switch {
        font-size: 0.8rem;
    }
    
    .switch-label {
        font-size: 0.8rem;
    }
    
    .alignment-overview {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        text-align: center;
    }
    
    .alignment-stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .section-tabs {
        flex-direction: column;
    }
    
    .breakdown-tabs {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-controls {
        flex-direction: column;
        gap: 0.5rem;
        align-items: stretch;
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