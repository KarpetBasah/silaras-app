<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-layer-group"></i> Layer RPJMD</h1>
        <p>Overlay peta prioritas RPJMD untuk analisis keselarasan program dengan rencana pembangunan daerah</p>
    </div>
    
    <!-- Control Panel -->
    <div class="rpjmd-controls">
        <div class="control-panel">
            <!-- Filter Controls -->
            <div class="filter-section">
                <h4><i class="fas fa-filter"></i> Filter Program</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="filter-tahun-rpjmd">Tahun:</label>
                        <select id="filter-tahun-rpjmd" class="form-control-sm">
                            <option value="">Semua Tahun</option>
                            <?php foreach ($tahun_list as $tahun): ?>
                                <option value="<?= $tahun ?>"><?= $tahun ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-sektor-rpjmd">Sektor:</label>
                        <select id="filter-sektor-rpjmd" class="form-control-sm">
                            <option value="">Semua Sektor</option>
                            <?php foreach ($sektor_list as $sektor): ?>
                                <option value="<?= $sektor['id'] ?>"><?= $sektor['nama_sektor'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-status-rpjmd">Status:</label>
                        <select id="filter-status-rpjmd" class="form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="perencanaan">Perencanaan</option>
                            <option value="berjalan">Berjalan</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-opd-rpjmd">OPD:</label>
                        <select id="filter-opd-rpjmd" class="form-control-sm">
                            <option value="">Semua OPD</option>
                            <?php foreach ($opd_list as $opd): ?>
                                <option value="<?= $opd['id'] ?>"><?= $opd['nama_singkat'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button id="analyze-alignment" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-line"></i> Analisis Keselarasan
                    </button>
                    
                    <button id="reset-filters-rpjmd" class="btn btn-secondary btn-sm">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
            
            <!-- Layer Controls -->
            <div class="layer-section">
                <h4><i class="fas fa-layers"></i> Layer RPJMD</h4>
                <div class="layer-toggles">
                    <div class="layer-group">
                        <h5>Kawasan Strategis</h5>
                        <div class="layer-items">
                            <label class="layer-toggle">
                                <input type="checkbox" id="layer-strategic-all" checked>
                                <span class="toggle-text">Tampilkan Semua</span>
                            </label>
                            <div id="strategic-layers-list">
                                <!-- Strategic area layers will be populated dynamically -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="layer-group">
                        <h5>Zona Tematik</h5>
                        <div class="layer-items">
                            <label class="layer-toggle">
                                <input type="checkbox" id="layer-thematic-all" checked>
                                <span class="toggle-text">Tampilkan Semua</span>
                            </label>
                            <div id="thematic-layers-list">
                                <!-- Thematic zone layers will be populated dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Map Container -->
    <div class="rpjmd-map-container">
        <div id="rpjmd-map" style="height: 650px; width: 100%; border-radius: 12px; overflow: hidden;"></div>
        
        <!-- Analysis Panel -->
        <div class="analysis-panel" id="analysis-panel" style="display: none;">
            <div class="panel-header">
                <h4><i class="fas fa-chart-pie"></i> Analisis Keselarasan Program</h4>
                <button type="button" class="panel-close" id="close-analysis">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="panel-content">
                <div class="alignment-stats">
                    <div class="stat-row">
                        <div class="stat-item aligned">
                            <div class="stat-number" id="aligned-count">0</div>
                            <div class="stat-label">Program Selaras</div>
                            <div class="stat-percentage" id="aligned-percentage">0%</div>
                        </div>
                        <div class="stat-item non-aligned">
                            <div class="stat-number" id="non-aligned-count">0</div>
                            <div class="stat-label">Tidak Selaras</div>
                            <div class="stat-percentage" id="non-aligned-percentage">0%</div>
                        </div>
                    </div>
                    
                    <div class="alignment-chart">
                        <canvas id="alignment-chart" width="300" height="150"></canvas>
                    </div>
                </div>
                
                <div class="non-aligned-programs" id="non-aligned-list">
                    <!-- Non-aligned programs will be listed here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Program Detail Modal (reuse from peta program) -->
<div id="program-detail-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-program-title">Detail Program & Analisis RPJMD</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="program-detail-content">
                <!-- Program details and alignment analysis will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">Tutup</button>
        </div>
    </div>
</div>

<script>
// Initialize RPJMD map when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined') {
        initRPJMDMap();
    } else {
        console.error('Leaflet library not loaded');
        document.getElementById('rpjmd-map').innerHTML = 
            '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ef4444;">' +
            '<p><i class="fas fa-exclamation-triangle"></i> Leaflet library tidak tersedia</p></div>';
    }
});
</script>

<!-- Include Leaflet.js -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Include RPJMD JavaScript -->
<script src="<?= base_url('assets/js/rpjmd.js') ?>"></script>

<?= $this->endSection() ?>