<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-container">
        <h1>GeoSelaras</h1>
        <p>Satu Peta, Satu Arah Pembangunan - Platform Perencanaan Berbasis Geospasial untuk Keselarasan Program Pembangunan Daerah Kota Banjarbaru</p>
    </div>
</section>

<!-- HOME CONTENT -->
<div class="content">
    <!-- Statistics Section -->
    <section class="stats">
        <div class="stat-card">
            <div class="number">156</div>
            <div class="label">Program Terintegrasi</div>
        </div>
        <div class="stat-card">
            <div class="number">85%</div>
            <div class="label">Keselarasan RPJMD</div>
        </div>
        <div class="stat-card">
            <div class="number">92%</div>
            <div class="label">Validasi Spasial</div>
        </div>
        <div class="stat-card">
            <div class="number">24</div>
            <div class="label">OPD Berpartisipasi</div>
        </div>
    </section>

    <h2 class="section-title">Modul Utama GeoSelaras</h2>
    
    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <h3>
                <i class="fas fa-plus-circle"></i>
                Input Program OPD
            </h3>
            <p>Form digital untuk memasukkan data program infrastruktur secara spasial dan tematik dengan validasi otomatis lokasi dan referensi RPJMD.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-map-marked-alt"></i>
                Visualisasi Peta Program
            </h3>
            <p>Peta interaktif yang menampilkan titik lokasi program berdasarkan sektor dan status dengan layer yang dapat dikustomisasi.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-file-alt"></i>
                Layer RPJMD
            </h3>
            <p>Overlay peta prioritas RPJMD untuk analisis keselarasan program dengan kawasan strategis dan sasaran pembangunan tematik.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-chart-line"></i>
                Analisis Tumpang Tindih
            </h3>
            <p>Algoritma spasial untuk mendeteksi konflik lokasi dan wilayah kosong, memberikan rekomendasi wilayah prioritas berdasarkan data spasial.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-chart-bar"></i>
                Monitoring & Evaluasi
            </h3>
            <p>Visualisasi progres fisik dan realisasi anggaran dalam peta dinamis dengan form input progres dan foto lapangan.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-comments"></i>
                Komunikasi & Dokumentasi
            </h3>
            <p>Forum OPD untuk diskusi teknis, form masukan publik, dan tautan ke regulasi, RPJMD, serta SOP sistem.</p>
        </div>
    </section>
</div>

<?= $this->endSection() ?>