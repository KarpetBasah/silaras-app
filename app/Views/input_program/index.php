<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-plus-circle"></i> Input Program OPD</h1>
        <p>Form digital untuk memasukkan data program infrastruktur secara spasial dan tematik</p>
    </div>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <div class="module-grid">
        <!-- Quick Stats -->
        <div class="module-card stats-overview">
            <h3><i class="fas fa-chart-bar"></i> Statistik Program</h3>
            <div class="stats-mini">
                <div class="stat-item">
                    <span class="number">42</span>
                    <span class="label">Program Aktif</span>
                </div>
                <div class="stat-item">
                    <span class="number">156</span>
                    <span class="label">Total Program</span>
                </div>
                <div class="stat-item">
                    <span class="number">24</span>
                    <span class="label">OPD Terdaftar</span>
                </div>
            </div>
        </div>
        
        <!-- Input Program Form -->
        <div class="module-card form-card">
            <h3><i class="fas fa-plus-circle"></i> Tambah Program Baru</h3>
            <a href="/input-program/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Input Program Baru
            </a>
            <p class="form-description">
                Masukkan data program infrastruktur dengan lokasi geografis dan referensi RPJMD
            </p>
        </div>
        
        <!-- Validation Tools -->
        <div class="module-card">
            <h3><i class="fas fa-shield-alt"></i> Validasi Otomatis</h3>
            <ul class="feature-list">
                <li><i class="fas fa-check"></i> Deteksi tumpang tindih lokasi</li>
                <li><i class="fas fa-check"></i> Validasi koordinat geografis</li>
                <li><i class="fas fa-check"></i> Keselarasan dengan RPJMD</li>
                <li><i class="fas fa-check"></i> Kelengkapan dokumen pendukung</li>
            </ul>
        </div>
        
        <!-- Recent Programs -->
        <div class="module-card recent-programs">
            <h3><i class="fas fa-history"></i> Program Terbaru</h3>
            <div class="program-list">
                <!-- TODO: Dynamic program list from database -->
                <div class="program-item">
                    <div class="program-info">
                        <strong>Pembangunan Jalan Lingkar Timur</strong>
                        <span class="program-meta">Dinas PUPR • 2024 • Rp 2.5M</span>
                    </div>
                    <span class="status-badge status-planning">Perencanaan</span>
                </div>
                
                <div class="program-item">
                    <div class="program-info">
                        <strong>Rehabilitasi Saluran Irigasi Martapura</strong>
                        <span class="program-meta">Dinas Pertanian • 2024 • Rp 1.8M</span>
                    </div>
                    <span class="status-badge status-progress">Berjalan</span>
                </div>
                
                <div class="program-item">
                    <div class="program-info">
                        <strong>Pembangunan PAUD Terpadu</strong>
                        <span class="program-meta">Dinas Pendidikan • 2024 • Rp 850K</span>
                    </div>
                    <span class="status-badge status-completed">Selesai</span>
                </div>
            </div>
        </div>
        
        <!-- Document Management -->
        <div class="module-card">
            <h3><i class="fas fa-folder-open"></i> Manajemen Dokumen</h3>
            <div class="doc-types">
                <div class="doc-type">
                    <i class="fas fa-file-pdf text-danger"></i>
                    <span>RAB (Rencana Anggaran Biaya)</span>
                </div>
                <div class="doc-type">
                    <i class="fas fa-file-alt text-primary"></i>
                    <span>DED (Detail Engineering Design)</span>
                </div>
                <div class="doc-type">
                    <i class="fas fa-camera text-success"></i>
                    <span>Foto Lokasi</span>
                </div>
                <div class="doc-type">
                    <i class="fas fa-map text-warning"></i>
                    <span>Peta Situasi</span>
                </div>
            </div>
        </div>
        
        <!-- RPJMD Reference -->
        <div class="module-card">
            <h3><i class="fas fa-book"></i> Referensi RPJMD</h3>
            <p>Pastikan program selaras dengan sasaran RPJMD Kota Banjarbaru:</p>
            <div class="rpjmd-sectors">
                <span class="sector-tag">Infrastruktur</span>
                <span class="sector-tag">Pendidikan</span>
                <span class="sector-tag">Kesehatan</span>
                <span class="sector-tag">Ekonomi</span>
                <span class="sector-tag">Lingkungan</span>
                <span class="sector-tag">Sosial</span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>