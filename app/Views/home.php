<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-container">
        <h1>SiLaras</h1>
        <p>Sistem Perencanaan Berbasis Geospasial untuk Aksi Perubahan Peningkatan Kinerja Infrastruktur Pembangunan, Riset dan Inovasi Daerah Kota Banjarbaru</p>
    </div>
</section>

<!-- HOME CONTENT -->
<div class="content">
    <!-- Statistics Section -->
    <section class="stats">
        <div class="stat-card">
            <div class="number">42</div>
            <div class="label">Total Program</div>
        </div>
        <div class="stat-card">
            <div class="number">58%</div>
            <div class="label">Progress Realisasi</div>
        </div>
        <div class="stat-card">
            <div class="number">75%</div>
            <div class="label">Capaian Target</div>
        </div>
        <div class="stat-card">
            <div class="number">12</div>
            <div class="label">OPD Terdaftar</div>
        </div>
    </section>

    <h2 class="section-title">Fitur Utama SiLaras</h2>
    
    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <h3>
                <i class="fas fa-layer-group"></i>
                Perencanaan Terintegrasi
            </h3>
            <p>Sistem perencanaan pembangunan yang terintegrasi dengan data geospasial untuk mengoptimalkan alokasi sumber daya dan koordinasi antar OPD dalam pelaksanaan program pembangunan daerah.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-chart-line"></i>
                Monitoring Real-time
            </h3>
            <p>Pantau progress pelaksanaan program pembangunan secara real-time dengan visualisasi data yang interaktif dan komprehensif untuk memudahkan pengambilan keputusan strategis.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-map-marked-alt"></i>
                Analisis Geospasial
            </h3>
            <p>Memanfaatkan teknologi GIS untuk analisis spasial yang mendalam, membantu identifikasi lokasi strategis dan optimalisasi sebaran program pembangunan infrastruktur.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-clipboard-check"></i>
                Evaluasi Kinerja
            </h3>
            <p>Sistem evaluasi komprehensif untuk mengukur kinerja program dengan indikator yang terukur, mendukung peningkatan kualitas perencanaan di masa mendatang.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-users"></i>
                Kolaborasi Multi-OPD
            </h3>
            <p>Platform kolaborasi yang memungkinkan koordinasi efektif antar OPD dalam perencanaan, pelaksanaan, dan evaluasi program pembangunan daerah.</p>
        </div>
        
        <div class="feature-card">
            <h3>
                <i class="fas fa-chart-pie"></i>
                Dashboard Analitik
            </h3>
            <p>Dashboard interaktif dengan visualisasi data yang komprehensif untuk memberikan insight mendalam tentang progress dan pencapaian program pembangunan.</p>
        </div>
    </section>
</div>

<?= $this->endSection() ?>