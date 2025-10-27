// Map handling functions
const MonitoringMap = {
    map: null,
    markers: L.layerGroup(),
    markerCluster: null,
    
    // Initialize map
    init() {
        try {
            console.log('Initializing map...');
            
            // Check if map container exists
            const mapContainer = document.getElementById('monitoring-map');
            if (!mapContainer) {
                throw new Error('Map container not found');
            }

            // Initialize map with Banjarbaru coordinates
            this.map = L.map('monitoring-map', {
                center: [-3.4574, 114.8225],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: true
            });

            // Add base layers
            const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            });

            const satellite = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                attribution: '© Google'
            });

            const baseLayers = {
                "OpenStreetMap": osm,
                "Satellite": satellite
            };

            // Add default layer
            osm.addTo(this.map);

            // Add layer control
            L.control.layers(baseLayers).addTo(this.map);

            // Initialize marker cluster
            this.markerCluster = L.markerClusterGroup({
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                maxClusterRadius: 30
            });
            
            this.map.addLayer(this.markerCluster);

            // Add legend
            this.addLegend();

            // Force map to refresh size
            setTimeout(() => {
                this.map.invalidateSize();
            }, 100);

            console.log('Map initialized successfully');
            return this;
        } catch (error) {
            console.error('Error initializing map:', error);
            throw error;
        }
    },
    
    // Add markers from GeoJSON
    addMarkers(geojson) {
        // Clear existing markers
        this.markerCluster.clearLayers();
        
        if (!geojson || !geojson.features) {
            console.log('No features to add to map');
            return;
        }

        const markers = geojson.features.map(feature => {
            const coords = feature.geometry.coordinates;
            const props = feature.properties;
            
            // Create marker with custom icon
            const marker = L.marker([coords[1], coords[0]], {
                icon: this.createIcon(props.progress, props.color)
            });
            
            // Add popup
            marker.bindPopup(this.createPopupContent(props));
            
            return marker;
        });

        // Add markers to cluster
        this.markerCluster.addLayers(markers);
        
        // Fit map to markers if any exist
        if (markers.length > 0) {
            this.map.fitBounds(this.markerCluster.getBounds(), {
                padding: [50, 50]
            });
        }
    },
    
    // Create custom icon based on progress
    createIcon(progress, color) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div class="marker-pin" style="background-color: ${color}">
                     <span>${progress}%</span>
                   </div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
    },
    
    // Create popup content
    createPopupContent(props) {
        return `
            <div class="program-popup">
                <h5>${props.nama}</h5>
                <p><strong>Sektor:</strong> ${props.sektor}</p>
                <p><strong>Progress:</strong> ${props.progress}%</p>
                <p><strong>Anggaran:</strong> ${this.formatCurrency(props.anggaran_total)}</p>
                <p><strong>Realisasi:</strong> ${this.formatCurrency(props.anggaran_realisasi)}</p>
                <button class="btn btn-sm btn-primary update-progress" 
                        onclick="MonitoringForms.showUpdateProgress(${props.id}, ${JSON.stringify(props)})">
                    Update Progress
                </button>
            </div>
        `;
    },
    
    // Format currency
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value || 0);
    },
    
    // Add map legend
    addLegend() {
        const legend = L.control({ position: 'bottomright' });
        
        legend.onAdd = function() {
            const div = L.DomUtil.create('div', 'map-legend');
            div.innerHTML = `
                <h6>Status Progress</h6>
                <div class="legend-item">
                    <span class="progress-indicator progress-high"></span> > 80%
                </div>
                <div class="legend-item">
                    <span class="progress-indicator progress-medium"></span> 50% - 80%
                </div>
                <div class="legend-item">
                    <span class="progress-indicator progress-low"></span> < 50%
                </div>
            `;
            return div;
        };
        
        legend.addTo(this.map);
    },
    
    // Clear existing markers
    clearMarkers() {
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
    },
    
    // Add program markers to map
    addMarkers(programs) {
        this.clearMarkers();
        
        programs.features.forEach(feature => {
            const prop = feature.properties;
            const marker = L.circleMarker(
                [feature.geometry.coordinates[1], feature.geometry.coordinates[0]], 
                {
                    radius: 8,
                    fillColor: this.getMarkerColor(prop.progress),
                    color: "#fff",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8
                }
            );
        
        programs.features.forEach(feature => {
            const marker = L.circleMarker(
                [feature.geometry.coordinates[1], feature.geometry.coordinates[0]],
                this.getMarkerStyle(feature.properties.progress)
            );

            const popupContent = `
                <div class="program-popup">
                    <h6 class="mb-2">${prop.nama}</h6>
                    <div class="info-item mb-2">
                        <strong>Sektor:</strong> ${prop.sektor}
                    </div>
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar bg-${this.getProgressClass(prop.progress)}" 
                             role="progressbar" 
                             style="width: ${prop.progress}%">
                        </div>
                    </div>
                    <div class="info-item mb-2">
                        <strong>Progress:</strong> ${prop.progress}%
                    </div>
                    <div class="info-item mb-2">
                        <strong>Anggaran:</strong> Rp ${parseInt(prop.anggaran_total).toLocaleString()}
                    </div>
                    <div class="info-item mb-3">
                        <strong>Realisasi:</strong> Rp ${parseInt(prop.anggaran_realisasi).toLocaleString()}
                    </div>
                    <button class="btn btn-primary btn-sm w-100 update-progress"
                            onclick="MonitoringForms.showUpdateProgress(${prop.id}, ${JSON.stringify(prop)})">
                        <i class="fas fa-edit"></i> Update Progress
                    </button>
                </div>
                            ${feature.properties.progress}%
                        </div>
                    </div>
                    <p>
                        <strong>Realisasi:</strong><br>
                        Rp ${feature.properties.anggaran_realisasi.toLocaleString()} 
                        dari Rp ${feature.properties.anggaran_total.toLocaleString()}
                    </p>
                    <button class="btn btn-sm btn-primary update-progress" 
                            data-id="${feature.properties.id}"
                            data-name="${feature.properties.nama}">
                        <i class="fas fa-tasks"></i> Update Progress
                    </button>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            marker.addTo(this.map);
            this.markers.push(marker);
        });
    },
    
    // Get marker style based on progress
    getMarkerStyle(progress) {
        let color, fillColor;
        if (progress >= 80) {
            color = '#28a745';  // Border hijau
            fillColor = '#28a745';  // Fill hijau
        } else if (progress >= 50) {
            color = '#ffc107';  // Border kuning
            fillColor = '#ffc107';  // Fill kuning
        } else {
            color = '#dc3545';  // Border merah
            fillColor = '#dc3545';  // Fill merah
        }
        
        return {
            radius: 8,
            color: color,
            fillColor: fillColor,
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        };
    },

    // Get progress class for bootstrap
    getProgressClass(progress) {
        if (progress >= 80) return 'success';
        if (progress >= 50) return 'warning';
        return 'danger';
    },

    // Add legend to map
    addLegend() {
        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = function() {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML = `
                <div class="card">
                    <div class="card-body p-2">
                        <h6 class="mb-2">Status Progress</h6>
                        <div class="d-flex align-items-center mb-1">
                            <span class="marker-legend" style="background: #28a745"></span>
                            <span>Progress > 80%</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="marker-legend" style="background: #ffc107"></span>
                            <span>Progress 50% - 80%</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="marker-legend" style="background: #dc3545"></span>
                            <span>Progress < 50%</span>
                        </div>
                    </div>
                </div>
            `;
            return div;
        };
        legend.addTo(this.map);
    }
};