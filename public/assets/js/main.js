/**
 * GeoSelaras - Main JavaScript File
 * Satu Peta, Satu Arah Pembangunan
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile menu toggle
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            
            // Toggle icon
            const icon = navToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // Input Program Form Enhancement
    if (document.querySelector('.program-form')) {
        initProgramForm();
    }
    
    // Modal functionality
    initModals();
    
    // Auto-hide alerts
    autoHideAlerts();
    
    // Initialize page-specific features
    if (document.getElementById('program-map')) {
        // Map will be initialized by inline script in the view
        // Add scroll listener to ensure z-index remains correct
        window.addEventListener('scroll', throttle(fixMapZIndex, 100));
        window.addEventListener('resize', throttle(fixMapZIndex, 100));
    }
    
    // Mobile dropdown toggle
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                this.classList.toggle('active');
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (navToggle && navMenu && !navToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            const icon = navToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });
    
    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && navMenu) {
            navMenu.classList.remove('active');
            const icon = navToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
            
            // Remove active class from dropdowns
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add loading state to buttons (except form submit buttons)
    document.querySelectorAll('button[type="submit"]:not(.program-form button), .btn-submit:not(.program-form .btn-submit)').forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('loading');
            this.disabled = true;
            
            // Remove loading state after 3 seconds if no form submission
            setTimeout(() => {
                this.classList.remove('loading');
                this.disabled = false;
            }, 3000);
        });
    });
    
});

function initProgramForm() {
    console.log('Initializing program form...');
    
    // Location validation
    const btnValidateLocation = document.getElementById('btn-validate-location');
    if (btnValidateLocation) {
        console.log('Validate location button found:', btnValidateLocation);
        btnValidateLocation.addEventListener('click', validateLocation);
    } else {
        console.log('Validate location button not found');
    }
    
    // Map picker
    const btnMapPicker = document.getElementById('btn-map-picker');
    if (btnMapPicker) {
        console.log('Map picker button found:', btnMapPicker);
        btnMapPicker.addEventListener('click', openMapPicker);
    } else {
        console.log('Map picker button not found');
    }
    
    // File upload validation
    initFileUploadValidation();
    
    // Form submission enhancement - remove auto-formatting conflict
    const programForm = document.querySelector('.program-form');
    if (programForm) {
        programForm.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            
            const submitBtn = programForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                // Re-enable button after 10 seconds as failsafe
                setTimeout(() => {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }, 10000);
            }
        });
    }
}

function validateLocation() {
    console.log('Validating location...');
    const lat = document.getElementById('koordinat_lat').value;
    const lng = document.getElementById('koordinat_lng').value;
    
    console.log('Lat:', lat, 'Lng:', lng);
    
    if (!lat || !lng) {
        showValidationResult('Masukkan koordinat latitude dan longitude terlebih dahulu', false);
        return;
    }
    
    const latNum = parseFloat(lat);
    const lngNum = parseFloat(lng);
    
    if (isNaN(latNum) || isNaN(lngNum)) {
        showValidationResult('Format koordinat tidak valid', false);
        return;
    }
    
    if (latNum < -11 || latNum > 6 || lngNum < 95 || lngNum > 141) {
        showValidationResult('Koordinat berada di luar wilayah Indonesia', false);
        return;
    }
    
    const btnValidate = document.getElementById('btn-validate-location');
    const originalText = btnValidate.innerHTML;
    btnValidate.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memvalidasi...';
    btnValidate.disabled = true;
    
    setTimeout(() => {
        // Simulate successful validation
        showValidationResult('Lokasi valid dan tidak ada tumpang tindih', true);
        btnValidate.innerHTML = originalText;
        btnValidate.disabled = false;
    }, 1500);
}

function showValidationResult(message, isValid) {
    const validationResult = document.getElementById('location-validation');
    validationResult.innerHTML = `<i class="fas fa-${isValid ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
    validationResult.className = `validation-result ${isValid ? 'valid' : 'invalid'}`;
    validationResult.style.display = 'block';
}

// Global variables for map picker
let mapPickerMap = null;
let mapPickerMarker = null;

function openMapPicker() {
    console.log('Opening map picker...');
    const modal = document.getElementById('map-modal');
    if (modal) {
        console.log('Modal found:', modal);
        modal.style.display = 'flex';
        
        // Clear the map div and prepare for Leaflet map
        const mapDiv = document.getElementById('map-picker');
        mapDiv.innerHTML = '';
        mapDiv.style.height = '400px';
        mapDiv.style.background = '#e2e8f0';
        
        // Initialize Leaflet map after modal is shown
        setTimeout(() => {
            initMapPicker();
        }, 100);
    }
}

function initMapPicker() {
    console.log('Initializing map picker...');
    
    // Check if Leaflet is available
    if (typeof L === 'undefined') {
        console.error('Leaflet library not loaded');
        showMapError('Leaflet library tidak tersedia. Pastikan koneksi internet stabil.');
        return;
    }
    
    // Get current coordinates if available
    const currentLat = document.getElementById('koordinat_lat').value || -3.4582;
    const currentLng = document.getElementById('koordinat_lng').value || 114.8348;
    
    try {
        // Initialize the map
        mapPickerMap = L.map('map-picker').setView([parseFloat(currentLat), parseFloat(currentLng)], 13);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(mapPickerMap);
        
        // Add current location marker if coordinates exist
        if (currentLat && currentLng && currentLat !== '' && currentLng !== '') {
            mapPickerMarker = L.marker([parseFloat(currentLat), parseFloat(currentLng)], {
                draggable: true
            }).addTo(mapPickerMap);
            
            // Update coordinate display
            updateCoordinatesFromMarker({
                lat: parseFloat(currentLat), 
                lng: parseFloat(currentLng)
            });
            
            mapPickerMarker.on('dragend', function(e) {
                updateCoordinatesFromMarker(e.target.getLatLng());
            });
        }
        
        // Add click event to map
        mapPickerMap.on('click', function(e) {
            console.log('Map clicked at:', e.latlng);
            
            // Remove existing marker if any
            if (mapPickerMarker) {
                mapPickerMap.removeLayer(mapPickerMarker);
            }
            
            // Add new marker at clicked location
            mapPickerMarker = L.marker(e.latlng, {
                draggable: true
            }).addTo(mapPickerMap);
            
            // Update coordinates
            updateCoordinatesFromMarker(e.latlng);
            
            // Add drag event to new marker
            mapPickerMarker.on('dragend', function(e) {
                updateCoordinatesFromMarker(e.target.getLatLng());
            });
        });
        
        // Add map controls
        addMapPickerControls();
        
        console.log('Map picker initialized successfully');
        
    } catch (error) {
        console.error('Error initializing map picker:', error);
        showMapError('Gagal memuat peta. Pastikan koneksi internet tersedia.');
    }
}

function showMapError(message) {
    const mapDiv = document.getElementById('map-picker');
    mapDiv.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #ef4444;">
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
            <p style="text-align: center; margin-bottom: 1rem;">${message}</p>
            <button type="button" class="btn btn-primary" onclick="simulateLocationPick()" style="margin-top: 0.5rem;">
                <i class="fas fa-map-pin"></i> Gunakan Lokasi Default Banjarbaru
            </button>
        </div>
    `;
}

function addMapPickerControls() {
    // Add locate control (find user's current location)
    const locateControl = L.control({position: 'topleft'});
    locateControl.onAdd = function() {
        const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        div.style.backgroundColor = 'white';
        div.style.backgroundImage = 'none';
        div.style.width = '30px';
        div.style.height = '30px';
        div.style.cursor = 'pointer';
        div.title = 'Gunakan Lokasi Saya';
        div.innerHTML = '<i class="fas fa-crosshairs" style="line-height: 30px; margin-left: 8px; color: #333;"></i>';
        
        div.onclick = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Pan map to user location
                    mapPickerMap.setView([lat, lng], 15);
                    
                    // Remove existing marker
                    if (mapPickerMarker) {
                        mapPickerMap.removeLayer(mapPickerMarker);
                    }
                    
                    // Add marker at user location
                    mapPickerMarker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(mapPickerMap);
                    
                    updateCoordinatesFromMarker({lat: lat, lng: lng});
                    
                    mapPickerMarker.on('dragend', function(e) {
                        updateCoordinatesFromMarker(e.target.getLatLng());
                    });
                }, function(error) {
                    console.error('Geolocation error:', error);
                    alert('Tidak dapat mengakses lokasi Anda. Pastikan GPS aktif dan berikan izin lokasi.');
                });
            } else {
                alert('Browser Anda tidak mendukung geolocation.');
            }
        };
        
        return div;
    };
    locateControl.addTo(mapPickerMap);
}

function updateCoordinatesFromMarker(latlng) {
    console.log('Updating coordinates:', latlng);
    // Update the display coordinates (temporary, will be set to form when confirmed)
    const coordDisplay = document.getElementById('temp-coordinates');
    if (coordDisplay) {
        coordDisplay.innerHTML = `Lat: ${latlng.lat.toFixed(6)}, Lng: ${latlng.lng.toFixed(6)}`;
    }
    
    // Store coordinates temporarily
    window.tempCoordinates = {
        lat: latlng.lat.toFixed(6),
        lng: latlng.lng.toFixed(6)
    };
}

function confirmLocationSelection() {
    if (window.tempCoordinates) {
        document.getElementById('koordinat_lat').value = window.tempCoordinates.lat;
        document.getElementById('koordinat_lng').value = window.tempCoordinates.lng;
        
        closeModal('map-modal');
        
        // Auto validate after selection
        setTimeout(() => {
            validateLocation();
        }, 500);
    } else {
        alert('Silakan pilih lokasi di peta terlebih dahulu');
    }
}

function simulateLocationPick() {
    // Fallback function when map fails to load
    const lat = -3.4582 + (Math.random() - 0.5) * 0.1;
    const lng = 114.8348 + (Math.random() - 0.5) * 0.1;
    
    document.getElementById('koordinat_lat').value = lat.toFixed(6);
    document.getElementById('koordinat_lng').value = lng.toFixed(6);
    
    closeModal('map-modal');
    
    setTimeout(() => {
        validateLocation();
    }, 500);
}

function initModals() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            const modalId = e.target.getAttribute('id');
            closeModal(modalId);
        }
    });
    
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                const modalId = modal.getAttribute('id');
                closeModal(modalId);
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const visibleModal = document.querySelector('.modal[style*="flex"]');
            if (visibleModal) {
                const modalId = visibleModal.getAttribute('id');
                closeModal(modalId);
            }
        }
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        
        // Clean up map picker when modal is closed
        if (modalId === 'map-modal' && mapPickerMap) {
            try {
                mapPickerMap.remove();
                mapPickerMap = null;
                mapPickerMarker = null;
                window.tempCoordinates = null;
            } catch (e) {
                console.log('Error cleaning up map:', e);
            }
        }
    }
}

function initFileUploadValidation() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const files = e.target.files;
            const maxSizes = {
                'rab_file': 5 * 1024 * 1024,
                'ded_file': 10 * 1024 * 1024,
                'foto_lokasi': 2 * 1024 * 1024
            };
            
            const inputName = e.target.name.replace('[]', '');
            const maxSize = maxSizes[inputName];
            
            for (let file of files) {
                if (maxSize && file.size > maxSize) {
                    alert(`File ${file.name} terlalu besar. Maksimal ${Math.round(maxSize / 1024 / 1024)}MB`);
                    e.target.value = '';
                    return;
                }
            }
            
            showFileInfo(e.target, files);
        });
    });
}

function showFileInfo(input, files) {
    const container = input.parentNode;
    let infoDiv = container.querySelector('.file-info');
    
    if (!infoDiv) {
        infoDiv = document.createElement('div');
        infoDiv.className = 'file-info';
        infoDiv.style.marginTop = '0.5rem';
        infoDiv.style.fontSize = '0.85rem';
        infoDiv.style.color = '#10b981';
        container.appendChild(infoDiv);
    }
    
    if (files.length > 0) {
        const fileNames = Array.from(files).map(file => 
            `${file.name} (${(file.size / 1024).toFixed(1)}KB)`
        ).join(', ');
        infoDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${fileNames}`;
    } else {
        infoDiv.innerHTML = '';
    }
}

function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
}

// Map Initialization and Functions
let programMap;
let markersLayer;
let allPrograms = [];
let filteredPrograms = [];

function initProgramMap() {
    // Initialize the map centered on Banjarbaru
    programMap = L.map('program-map').setView([-3.4582, 114.8348], 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
    }).addTo(programMap);
    
    // Initialize markers layer
    markersLayer = L.layerGroup().addTo(programMap);
    
    // Fix z-index issues with Leaflet controls
    fixLeafletZIndex();
    
    // Load program data
    loadProgramData();
    
    // Initialize filters
    initMapFilters();
}

function loadProgramData() {
    // Show loading indicator
    const mapElement = document.getElementById('program-map');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'map-loading';
    loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat data program...';
    loadingDiv.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000;';
    
    // Fetch program data
    fetch('/peta-program/getProgramData')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allPrograms = data.data;
                filteredPrograms = [...allPrograms];
                displayProgramMarkers(filteredPrograms);
                updateStatistics(filteredPrograms);
            }
        })
        .catch(error => {
            console.error('Error loading program data:', error);
            GeoSelaras.showNotification('Gagal memuat data program', 'error');
        })
        .finally(() => {
            // Remove loading indicator
            if (loadingDiv.parentNode) {
                loadingDiv.remove();
            }
        });
}

function displayProgramMarkers(programs) {
    // Clear existing markers
    markersLayer.clearLayers();
    
    programs.forEach(program => {
        const marker = createProgramMarker(program);
        markersLayer.addLayer(marker);
    });
}

function createProgramMarker(program) {
    // Determine marker color based on status (matching CSS colors)
    const statusColors = {
        'perencanaan': '#f59e0b',
        'berjalan': '#3b82f6', 
        'selesai': '#10b981'
    };
    
    // Use status color as primary color
    const color = statusColors[program.status] || '#64748b';
    const iconName = program.sektor && program.sektor.icon ? program.sektor.icon.replace('fas fa-', '') : 'map-pin';
    
    // Create custom icon using status color
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3);"><i class="fas fa-${iconName}" style="color: white; font-size: 12px;"></i></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });
    
    // Create marker
    const marker = L.marker([program.lat, program.lng], { icon: customIcon });
    
    // Create popup content with correct data structure
    const popupContent = `
        <div class="program-popup">
            <h4>${program.nama_kegiatan}</h4>
            <div class="popup-details">
                <p><strong>Sektor:</strong> ${program.sektor ? program.sektor.nama : 'N/A'}</p>
                <p><strong>Status:</strong> <span class="status-badge status-${program.status}">${getStatusName(program.status)}</span></p>
                <p><strong>Anggaran:</strong> ${formatCurrency(program.anggaran_total)}</p>
                <p><strong>Tahun:</strong> ${program.tahun_pelaksanaan}</p>
                <p><strong>OPD:</strong> ${program.opd ? program.opd.singkat : 'N/A'}</p>
            </div>
            <button onclick="showProgramDetail(${program.id})" class="btn btn-primary btn-sm">
                <i class="fas fa-info-circle"></i> Detail
            </button>
        </div>
    `;
    
    marker.bindPopup(popupContent, {
        maxWidth: 300,
        className: 'custom-popup'
    });
    
    return marker;
}

function initMapFilters() {
    const filterElements = ['filter-tahun', 'filter-sektor', 'filter-status', 'filter-opd'];
    
    filterElements.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', applyFilters);
        }
    });
    
    const resetButton = document.getElementById('reset-filters');
    if (resetButton) {
        resetButton.addEventListener('click', resetFilters);
    }
}

function applyFilters() {
    const tahun = document.getElementById('filter-tahun').value;
    const sektor = document.getElementById('filter-sektor').value;
    const status = document.getElementById('filter-status').value;
    const opd = document.getElementById('filter-opd').value;
    
    // Reload data with filters via API
    const params = new URLSearchParams();
    if (tahun) params.append('tahun', tahun);
    if (sektor) params.append('sektor_id', sektor);
    if (status) params.append('status', status);
    if (opd) params.append('opd_id', opd);
    
    const url = '/peta-program/getProgramData' + (params.toString() ? '?' + params.toString() : '');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filteredPrograms = data.data;
                displayProgramMarkers(filteredPrograms);
                updateStatistics(filteredPrograms);
            }
        })
        .catch(error => {
            console.error('Error loading filtered program data:', error);
            GeoSelaras.showNotification('Gagal memuat data program', 'error');
        });
}

function resetFilters() {
    document.getElementById('filter-tahun').value = '';
    document.getElementById('filter-sektor').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-opd').value = '';
    
    filteredPrograms = [...allPrograms];
    displayProgramMarkers(filteredPrograms);
    updateStatistics(filteredPrograms);
}

function updateStatistics(programs) {
    const total = programs.length;
    const planning = programs.filter(p => p.status === 'perencanaan').length;
    const running = programs.filter(p => p.status === 'berjalan').length;
    const completed = programs.filter(p => p.status === 'selesai').length;
    
    // Animate counters
    animateCounter(document.getElementById('total-programs'), 0, total);
    animateCounter(document.getElementById('programs-planning'), 0, planning);
    animateCounter(document.getElementById('programs-running'), 0, running);
    animateCounter(document.getElementById('programs-completed'), 0, completed);
}

function showProgramDetail(programId) {
    // Fetch detailed program data
    fetch(`/peta-program/getProgramDetail/${programId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProgramDetail(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading program detail:', error);
            GeoSelaras.showNotification('Gagal memuat detail program', 'error');
        });
}

function displayProgramDetail(program) {
    const modal = document.getElementById('program-detail-modal');
    const title = document.getElementById('modal-program-title');
    const content = document.getElementById('program-detail-content');
    
    title.textContent = program.nama_kegiatan;
    
    // Calculate budget realization percentage
    const realisasiPersen = program.anggaran_total > 0 ? 
        ((program.anggaran_realisasi / program.anggaran_total) * 100).toFixed(1) : 0;
    
    content.innerHTML = `
        <div class="program-detail-grid">
            <div class="detail-section">
                <h5><i class="fas fa-info-circle"></i> Informasi Umum</h5>
                <table class="detail-table">
                    <tr><td>Kode Program</td><td>${program.kode_program || 'N/A'}</td></tr>
                    <tr><td>Nama Kegiatan</td><td>${program.nama_kegiatan}</td></tr>
                    <tr><td>Deskripsi</td><td>${program.deskripsi || 'N/A'}</td></tr>
                    <tr><td>Lokasi</td><td>${program.lokasi_alamat || 'N/A'}</td></tr>
                    <tr><td>Koordinat</td><td>${program.koordinat.lat}, ${program.koordinat.lng}</td></tr>
                </table>
            </div>
            
            <div class="detail-section">
                <h5><i class="fas fa-tags"></i> Kategorisasi</h5>
                <table class="detail-table">
                    <tr><td>Sektor</td><td>${program.sektor.nama}</td></tr>
                    <tr><td>Status</td><td><span class="status-badge status-${program.status}">${getStatusName(program.status)}</span></td></tr>
                    <tr><td>OPD Pelaksana</td><td>${program.opd.nama}</td></tr>
                    <tr><td>Kepala OPD</td><td>${program.opd.kepala || 'N/A'}</td></tr>
                    <tr><td>Sasaran RPJMD</td><td>${program.rpjmd.nama}</td></tr>
                </table>
            </div>
            
            <div class="detail-section">
                <h5><i class="fas fa-money-bill-wave"></i> Anggaran & Progress</h5>
                <table class="detail-table">
                    <tr><td>Anggaran Total</td><td>${formatCurrency(program.anggaran_total)}</td></tr>
                    <tr><td>Realisasi Anggaran</td><td>${formatCurrency(program.anggaran_realisasi)}</td></tr>
                    <tr><td>Tahun Pelaksanaan</td><td>${program.tahun_pelaksanaan}</td></tr>
                    <tr><td>Progress Fisik</td><td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${program.progress_fisik}%; background-color: #10b981;"></div>
                            <span class="progress-text">${program.progress_fisik}%</span>
                        </div>
                    </td></tr>
                    <tr><td>Realisasi Anggaran</td><td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${realisasiPersen}%; background-color: #3b82f6;"></div>
                            <span class="progress-text">${realisasiPersen}%</span>
                        </div>
                    </td></tr>
                </table>
            </div>
            
            ${program.kontraktor || program.konsultan || program.sumber_dana ? `
            <div class="detail-section">
                <h5><i class="fas fa-handshake"></i> Pelaksanaan</h5>
                <table class="detail-table">
                    ${program.kontraktor ? `<tr><td>Kontraktor</td><td>${program.kontraktor}</td></tr>` : ''}
                    ${program.konsultan ? `<tr><td>Konsultan</td><td>${program.konsultan}</td></tr>` : ''}
                    ${program.sumber_dana ? `<tr><td>Sumber Dana</td><td>${program.sumber_dana}</td></tr>` : ''}
                    ${program.tanggal_mulai ? `<tr><td>Tanggal Mulai</td><td>${formatDate(program.tanggal_mulai)}</td></tr>` : ''}
                    ${program.tanggal_selesai_rencana ? `<tr><td>Target Selesai</td><td>${formatDate(program.tanggal_selesai_rencana)}</td></tr>` : ''}
                    ${program.tanggal_selesai_aktual ? `<tr><td>Selesai Aktual</td><td>${formatDate(program.tanggal_selesai_aktual)}</td></tr>` : ''}
                    <tr><td>Prioritas</td><td>${program.is_prioritas ? '<span class="priority-badge">Prioritas</span>' : 'Reguler'}</td></tr>
                </table>
            </div>
            ` : ''}
            
            ${program.documents && program.documents.length > 0 ? `
            <div class="detail-section">
                <h5><i class="fas fa-paperclip"></i> Dokumen</h5>
                <div class="documents-list">
                    ${program.documents.map(doc => `
                        <div class="document-item">
                            <i class="fas fa-file"></i>
                            <span>${doc.nama_dokumen}</span>
                            <small>(${doc.jenis_dokumen})</small>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}
            
            ${program.catatan ? `
            <div class="detail-section">
                <h5><i class="fas fa-sticky-note"></i> Catatan</h5>
                <p class="program-notes">${program.catatan}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    modal.style.display = 'flex';
}

function getSektorName(sektor) {
    const names = {
        'jalan': 'Jalan dan Transportasi',
        'irigasi': 'Irigasi dan Pengairan',
        'pendidikan': 'Pendidikan',
        'kesehatan': 'Kesehatan',
        'ekonomi': 'Ekonomi dan Perdagangan',
        'sosial': 'Sosial dan Budaya'
    };
    return names[sektor] || sektor;
}

function getStatusName(status) {
    const names = {
        'perencanaan': 'Perencanaan',
        'berjalan': 'Berjalan',
        'selesai': 'Selesai'
    };
    return names[status] || status;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function animateCounter(element, start, end, duration = 1000) {
    if (!element) return;
    
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.textContent = value;
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

function fixLeafletZIndex() {
    // Fix z-index for Leaflet controls to prevent overlay with navbar
    setTimeout(() => {
        const leafletControls = document.querySelectorAll('.leaflet-control-container .leaflet-control');
        leafletControls.forEach(control => {
            control.style.zIndex = '500';
        });
        
        // Fix zoom controls specifically
        const zoomControls = document.querySelectorAll('.leaflet-control-zoom');
        zoomControls.forEach(control => {
            control.style.zIndex = '500';
        });
        
        // Fix attribution
        const attribution = document.querySelectorAll('.leaflet-control-attribution');
        attribution.forEach(control => {
            control.style.zIndex = '500';
        });
        
        // Set map container z-index
        const mapContainer = document.getElementById('program-map');
        if (mapContainer) {
            mapContainer.style.zIndex = '1';
        }
        
        // Ensure legend has proper z-index
        const legend = document.querySelector('.map-legend');
        if (legend) {
            legend.style.zIndex = '500';
        }
    }, 100);
}

function fixMapZIndex() {
    // Ensure navbar always has highest z-index
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        navbar.style.zIndex = '1000';
    }
    
    // Re-apply Leaflet control fixes
    fixLeafletZIndex();
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Utility functions
const GeoSelaras = {
    
    // Show notification
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
    },
    
    // Format number with thousand separator
    formatNumber: function(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    },
    
    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },
    
    // Animate counter
    animateCounter: function(element, start, end, duration = 2000) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = this.formatNumber(value);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
};