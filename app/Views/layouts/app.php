<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SiLaras - Sistem Perencanaan Berbasis Geospasial</title>
    <meta name="description" content="Sistem Perencanaan Berbasis Geospasial untuk Infrastruktur Pembangunan">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- NAVIGATION -->
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
          
<div class="app">
    <?= $this->renderSection('content') ?>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Pemerintah Kota Banjarbaru. Sistem SiLaras - Perencanaan Berbasis Geospasial.</p>
    </div>
</footer>

<!-- SCRIPTS -->

<script src="<?= base_url('assets/js/script.js') ?>"></script>

<!-- -->

</body>
</html>