<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SiLaras - Satu Peta, Satu Arah Pembangunan</title>
    <meta name="description" content="GeoSelaras - Platform Perencanaan Berbasis Geospasial untuk Keselarasan Program Pembangunan Daerah">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
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
                <a href="/" class="nav-link <?= (uri_string() == '' || uri_string() == '/') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    Beranda
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/input-program" class="nav-link <?= (strpos(uri_string(), 'input-program') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i>
                    Input Program
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/peta-program" class="nav-link <?= (strpos(uri_string(), 'peta-program') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-map-marked-alt"></i>
                    Peta Program
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/rpjmd" class="nav-link <?= (strpos(uri_string(), 'rpjmd') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    RPJMD
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/analisis" class="nav-link <?= (strpos(uri_string(), 'analisis') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    Analisis
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/monitoring" class="nav-link <?= (strpos(uri_string(), 'monitoring') !== false) ? 'active' : '' ?>">
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
        <p>&copy; <?= date('Y') ?> BAPPERIDA Kota Banjarbaru. SiLaras - Satu Peta, Satu Arah Pembangunan.</p>
    </div>
</footer>

<!-- SCRIPTS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Main JS -->
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<?= $this->renderSection('scripts') ?>

<!-- -->

</body>
</html>