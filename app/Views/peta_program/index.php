<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-map-marked-alt"></i> Peta Program</h1>
        <p>Visualisasi peta interaktif yang menampilkan titik lokasi program berdasarkan sektor dan status</p>
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
                <label for="filter-status">Status:</label>
                <select id="filter-status" class="form-control-sm">
                    <option value="">Semua Status</option>
                    <option value="perencanaan">Perencanaan</option>
                    <option value="berjalan">Berjalan</option>
                    <option value="selesai">Selesai</option>
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
            
            <button id="reset-filters" class="btn btn-secondary btn-sm">
                <i class="fas fa-undo"></i> Reset Filter
            </button>
        </div>
    </div>
    
    <!-- Map Container -->
    <div class="map-container">
        <div id="program-map" style="height: 600px; width: 100%; border-radius: 12px; overflow: hidden;"></div>
        
        <!-- Map Legend -->
        <div class="map-legend">
            <h4><i class="fas fa-info-circle"></i> Legenda</h4>
            
            <div class="legend-section">
                <h5>Status Program:</h5>
                <div class="legend-items">
                    <div class="legend-item">
                        <div class="legend-marker status-perencanaan"></div>
                        <span>Perencanaan</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker status-berjalan"></div>
                        <span>Berjalan</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker status-selesai"></div>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>
            
            <div class="legend-section">
                <h5>Sektor:</h5>
                <div class="legend-items">
                    <div class="legend-item">
                        <i class="fas fa-road sector-jalan"></i>
                        <span>Jalan & Transportasi</span>
                    </div>
                    <div class="legend-item">
                        <i class="fas fa-tint sector-irigasi"></i>
                        <span>Irigasi & Pengairan</span>
                    </div>
                    <div class="legend-item">
                        <i class="fas fa-graduation-cap sector-pendidikan"></i>
                        <span>Pendidikan</span>
                    </div>
                    <div class="legend-item">
                        <i class="fas fa-heartbeat sector-kesehatan"></i>
                        <span>Kesehatan</span>
                    </div>
                    <div class="legend-item">
                        <i class="fas fa-store sector-ekonomi"></i>
                        <span>Ekonomi</span>
                    </div>
                    <div class="legend-item">
                        <i class="fas fa-users sector-sosial"></i>
                        <span>Sosial</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Program Statistics -->
    <div class="map-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-map-pin text-primary"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="total-programs">0</div>
                    <div class="stat-label">Total Program</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="programs-planning">0</div>
                    <div class="stat-label">Perencanaan</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-spinner text-info"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="programs-running">0</div>
                    <div class="stat-label">Berjalan</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="programs-completed">0</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Program Detail Modal -->
<div id="program-detail-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-program-title">Detail Program</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="program-detail-content">
                <!-- Program details will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">Tutup</button>
        </div>
    </div>
</div>

<script>
// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined') {
        initProgramMap();
    } else {
        console.error('Leaflet library not loaded');
    }
});
</script>

<?= $this->endSection() ?>