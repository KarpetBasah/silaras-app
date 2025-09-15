<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<!-- Home -->
<main class="content">
<nav class="navbar">
    <div class="nav-container">
        <a href="/" class="nav-logo">
            <i class="fas fa-map-marked-alt"></i>
            SiLaras
        </a>
        
        <ul class="nav-menu" id="nav-menu">
            <li class="nav-item">
                <a href="/" class="nav-link">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#" class="nav-link">
                    <i class="fas fa-layer-group"></i>
                    Perencanaan
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-plus-circle"></i>
                        Input Program OPD
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-tasks"></i>
                        Rencana Strategis
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-calendar-alt"></i>
                        Rencana Kerja
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-chart-line"></i>
                        Analisis Program
                    </a>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    Monitoring
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-eye"></i>
                        Monitoring Program
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-chart-pie"></i>
                        Visualisasi Data
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file-alt"></i>
                        Laporan Progress
                    </a>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#" class="nav-link">
                    <i class="fas fa-clipboard-check"></i>
                    Evaluasi
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-star"></i>
                        Kinerja Program
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-bullseye"></i>
                        Capaian Target
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-award"></i>
                        Analisis Kualitas
                    </a>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#" class="nav-link">
                    <i class="fas fa-map"></i>
                    Geospasial
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-globe"></i>
                        Peta Interaktif
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-layer-group"></i>
                        Layer Management
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-search-location"></i>
                        Analisis Spasial
                    </a>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    Pengaturan
                    <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users"></i>
                        Manajemen User
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-building"></i>
                        Data OPD
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-database"></i>
                        Backup Data
                    </a>
                </div>
            </li>
            
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                Kota Banjarbaru
            </div>
        </ul>
        
        <button class="nav-toggle" id="nav-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-container">
        <h1>SiLaras</h1>
        <p>Sistem Perencanaan Berbasis Geospasial untuk Aksi Perubahan Peningkatan Kinerja Infrastruktur Pembangunan, Riset dan Inovasi Daerah Kota Banjarbaru</p>
    </div>
</section>

<!-- CONTENT -->
<main class="content">
    
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

</main>

<?= $this->endSection() ?>