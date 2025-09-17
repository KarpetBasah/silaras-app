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
            <img src="<?= base_url('assets/images/SiLaras Logo.png') ?>" alt="SiLaras Logo" style="height: 50px;" srcset="">
        </a>
        
        <ul class="nav-menu" id="nav-menu">
            <li class="nav-item">
                <a href="/" class="nav-link">
                    <i class="fas fa-home"></i>
                    Beranda
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/peta-program" class="nav-link">
                    <i class="fas fa-map-marked-alt"></i>
                    Peta Program
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/rpjmd" class="nav-link">
                    <i class="fas fa-file-alt"></i>
                    RPJMD
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/analisis" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    Analisis
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/monitoring" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    Monitoring
                </a>
            </li>
            
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                User
            </div>
        </ul>
        
        <button class="nav-toggle" id="nav-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>
          
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

<script src="<?= base_url('assets/js/main.js') ?>"></script>

<!-- -->

</body>
</html>