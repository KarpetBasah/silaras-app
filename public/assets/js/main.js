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
    
    // Add loading state to buttons
    document.querySelectorAll('button[type="submit"], .btn-submit').forEach(button => {
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
    // Format currency input
    const anggaranInput = document.getElementById('anggaran');
    if (anggaranInput) {
        anggaranInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 0) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });
    }
    
    // Location validation
    const btnValidateLocation = document.getElementById('btn-validate-location');
    if (btnValidateLocation) {
        btnValidateLocation.addEventListener('click', validateLocation);
    }
    
    // Map picker
    const btnMapPicker = document.getElementById('btn-map-picker');
    if (btnMapPicker) {
        btnMapPicker.addEventListener('click', openMapPicker);
    }
    
    // File upload validation
    initFileUploadValidation();
    
    // Form submission enhancement
    const programForm = document.querySelector('.program-form');
    if (programForm) {
        programForm.addEventListener('submit', function(e) {
            const anggaranInput = document.getElementById('anggaran');
            if (anggaranInput) {
                anggaranInput.value = anggaranInput.value.replace(/[^\d]/g, '');
            }
            
            const submitBtn = programForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            }
        });
    }
}

function validateLocation() {
    const lat = document.getElementById('lokasi_lat').value;
    const lng = document.getElementById('lokasi_lng').value;
    
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

function openMapPicker() {
    const modal = document.getElementById('map-modal');
    if (modal) {
        modal.style.display = 'flex';
        
        const mapDiv = document.getElementById('map-picker');
        mapDiv.innerHTML = `
            <div style="text-align: center;">
                <i class="fas fa-map" style="font-size: 3rem; color: #64748b; margin-bottom: 1rem;"></i>
                <p>Peta interaktif akan dimuat di sini</p>
                <p style="font-size: 0.9rem; color: #64748b;">
                    Integrasi dengan Leaflet.js untuk memilih lokasi secara visual
                </p>
                <button type="button" class="btn btn-primary" onclick="simulateLocationPick()">
                    <i class="fas fa-crosshairs"></i> Simulasi Pilih Lokasi
                </button>
            </div>
        `;
    }
}

function simulateLocationPick() {
    const lat = -3.4582 + (Math.random() - 0.5) * 0.1;
    const lng = 114.8348 + (Math.random() - 0.5) * 0.1;
    
    document.getElementById('lokasi_lat').value = lat.toFixed(6);
    document.getElementById('lokasi_lng').value = lng.toFixed(6);
    
    closeModal('map-modal');
    
    setTimeout(() => {
        validateLocation();
    }, 500);
}

function initModals() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const visibleModal = document.querySelector('.modal[style*="flex"]');
            if (visibleModal) {
                visibleModal.style.display = 'none';
            }
        }
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
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
        attribution: '© OpenStreetMap contributors'
    }).addTo(programMap);
    
    // Initialize markers layer
    markersLayer = L.layerGroup().addTo(programMap);
    
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
    // Determine marker color based on status
    const statusColors = {
        'perencanaan': '#f59e0b',
        'berjalan': '#3b82f6',
        'selesai': '#10b981'
    };
    
    // Determine icon based on sector
    const sectorIcons = {
        'jalan': 'road',
        'irigasi': 'tint',
        'pendidikan': 'graduation-cap',
        'kesehatan': 'heartbeat',
        'ekonomi': 'store',
        'sosial': 'users'
    };
    
    const color = statusColors[program.status] || '#64748b';
    const iconName = sectorIcons[program.sektor] || 'map-pin';
    
    // Create custom icon
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3);"><i class="fas fa-${iconName}" style="color: white; font-size: 12px;"></i></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });
    
    // Create marker
    const marker = L.marker([program.lat, program.lng], { icon: customIcon });
    
    // Create popup content
    const popupContent = `
        <div class="program-popup">
            <h4>${program.nama_kegiatan}</h4>
            <div class="popup-details">
                <p><strong>Sektor:</strong> ${getSektorName(program.sektor)}</p>
                <p><strong>Status:</strong> <span class="status-badge status-${program.status}">${getStatusName(program.status)}</span></p>
                <p><strong>Anggaran:</strong> ${formatCurrency(program.anggaran)}</p>
                <p><strong>Tahun:</strong> ${program.tahun}</p>
                <p><strong>OPD:</strong> ${program.opd}</p>
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
    
    filteredPrograms = allPrograms.filter(program => {
        return (!tahun || program.tahun.toString() === tahun) &&
               (!sektor || program.sektor === sektor) &&
               (!status || program.status === status) &&
               (!opd || program.opd.toLowerCase().includes(opd.replace('_', ' ')));
    });
    
    displayProgramMarkers(filteredPrograms);
    updateStatistics(filteredPrograms);
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
    
    content.innerHTML = `
        <div class="program-detail-grid">
            <div class="detail-section">
                <h5><i class="fas fa-info-circle"></i> Informasi Umum</h5>
                <table class="detail-table">
                    <tr><td>Nama Kegiatan</td><td>${program.nama_kegiatan}</td></tr>
                    <tr><td>Deskripsi</td><td>${program.deskripsi}</td></tr>
                    <tr><td>Lokasi</td><td>${program.lokasi}</td></tr>
                    <tr><td>Koordinat</td><td>${program.koordinat[0]}, ${program.koordinat[1]}</td></tr>
                </table>
            </div>
            
            <div class="detail-section">
                <h5><i class="fas fa-tags"></i> Kategorisasi</h5>
                <table class="detail-table">
                    <tr><td>Sektor</td><td>${getSektorName(program.sektor)}</td></tr>
                    <tr><td>Status</td><td><span class="status-badge status-${program.status}">${getStatusName(program.status)}</span></td></tr>
                    <tr><td>OPD Pelaksana</td><td>${program.opd}</td></tr>
                    <tr><td>Sasaran RPJMD</td><td>${program.sasaran_rpjmd}</td></tr>
                </table>
            </div>
            
            <div class="detail-section">
                <h5><i class="fas fa-money-bill-wave"></i> Anggaran & Progress</h5>
                <table class="detail-table">
                    <tr><td>Anggaran</td><td>${formatCurrency(program.anggaran)}</td></tr>
                    <tr><td>Tahun Pelaksanaan</td><td>${program.tahun_pelaksanaan}</td></tr>
                    <tr><td>Progress Fisik</td><td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${program.progress_fisik}%"></div>
                            <span class="progress-text">${program.progress_fisik}%</span>
                        </div>
                    </td></tr>
                    <tr><td>Realisasi Anggaran</td><td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${program.realisasi_anggaran}%"></div>
                            <span class="progress-text">${program.realisasi_anggaran}%</span>
                        </div>
                    </td></tr>
                </table>
            </div>
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