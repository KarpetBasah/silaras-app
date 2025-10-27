<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<!-- Custom CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/monitoring.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/css/monitoring-map.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <!-- Header Section -->
        <?= $this->include('monitoring/_header') ?>
        
        <!-- Filters Section -->
        <?= $this->include('monitoring/_filters') ?>
        
        <!-- Statistics Dashboard -->
        <?= $this->include('monitoring/_statistics') ?>
        
        <!-- Map Section -->
        <?= $this->include('monitoring/_map') ?>
        
        <!-- Program List Section -->
        <?= $this->include('monitoring/_program_list') ?>
        
        <!-- Charts Section -->
        <?= $this->include('monitoring/_charts') ?>
        
        <!-- Progress Update Modal -->
        <?= $this->include('monitoring/_progress_modal') ?>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // Define base URL for API calls
    const baseUrl = '<?= base_url() ?>';
    
    // Initialize toastr options
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    };
</script>

<!-- Custom JS -->
<script src="<?= base_url('assets/js/monitoring/map.js') ?>"></script>
<script src="<?= base_url('assets/js/monitoring/charts.js') ?>"></script>
<script src="<?= base_url('assets/js/monitoring/forms.js') ?>"></script>
<script src="<?= base_url('assets/js/monitoring/data.js') ?>"></script>
<script src="<?= base_url('assets/js/monitoring/program.js') ?>"></script>

<script>
    $(document).ready(function() {
        // Set default error handler
        window.onerror = function(msg, url, line) {
            console.error('JavaScript error:', msg, 'at', url, ':', line);
            toastr.error('Terjadi kesalahan pada aplikasi');
            return false;
        };

        // Initialize components sequentially
        const init = async () => {
            try {
                console.log('Initializing monitoring dashboard...');
                
                // Wait for map initialization
                await new Promise((resolve) => {
                    setTimeout(async () => {
                        await MonitoringMap.init();
                        resolve();
                    }, 500);
                });
                console.log('Map initialized');

                // Initialize other components
                MonitoringForms.init();
                MonitoringData.init();
                
                console.log('All components initialized');
                
                // Load initial data
                await MonitoringData.loadInitialData();
                console.log('Initial data loaded');
                
            } catch (error) {
                console.error('Error during initialization:', error);
                toastr.error('Terjadi kesalahan saat memuat dashboard');
            }
        };

        // Start initialization
        init();
    });
</script>
<?= $this->endSection() ?>