# RPJMD Module Documentation

## Overview
Modul RPJMD (Rencana Pembangunan Jangka Menengah Daerah) merupakan fitur analisis keselarasan program dengan prioritas zona pembangunan daerah. Modul ini memungkinkan visualisasi layer prioritas dan analisis tingkat keselarasan program pembangunan terhadap RPJMD.

## Features

### 1. Priority Layer Visualization
- **Strategic Areas (Kawasan Strategis)**: Kawasan-kawasan prioritas pembangunan dengan tingkat kepentingan tinggi
- **Thematic Zones (Zona Tematik)**: Zona-zona berdasarkan tema pengembangan spesifik (pendidikan, kesehatan, pariwisata, dll.)
- **Interactive Layer Controls**: Toggle untuk mengaktifkan/nonaktifkan tampilan layer
- **Dynamic Coloring**: Setiap zona memiliki warna yang dapat dikustomisasi

### 2. Program Alignment Analysis
- **Point-in-Polygon Analysis**: Analisis apakah lokasi program berada dalam zona prioritas
- **Alignment Statistics**: Statistik program yang selaras vs tidak selaras dengan RPJMD
- **Real-time Analysis**: Analisis keselarasan dilakukan secara real-time saat membuka popup program
- **Recommendation System**: Memberikan rekomendasi berdasarkan hasil analisis

### 3. Interactive Map Interface
- **Leaflet.js Integration**: Menggunakan library Leaflet untuk visualisasi peta interaktif
- **Multiple Base Layers**: OpenStreetMap dan Satellite view
- **Program Markers**: Marker program dengan indikator keselarasan (warna berubah berdasarkan alignment)
- **Rich Popups**: Popup informatif dengan detail program dan status keselarasan

### 4. Analysis Panel
- **Floating Panel**: Panel analisis yang dapat dibuka/tutup
- **Statistical Overview**: Ringkasan statistik keselarasan program
- **Non-aligned Program List**: Daftar program yang tidak selaras dengan zona prioritas
- **Quick Navigation**: Link langsung ke program di peta

### 5. Filtering System
- **Multi-criteria Filtering**: Filter berdasarkan sektor, OPD, dan status program
- **Dynamic Updates**: Peta dan analisis diupdate secara otomatis saat filter diubah

## Technical Implementation

### Database Schema
```sql
-- Table: rpjmd_priority_zones
CREATE TABLE rpjmd_priority_zones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('strategic', 'thematic'),
    name VARCHAR(255),
    description TEXT,
    priority ENUM('Tinggi', 'Sedang', 'Rendah'),
    theme VARCHAR(100),
    color VARCHAR(7),
    coordinates JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME,
    updated_at DATETIME
);

-- Added column to programs table
ALTER TABLE program ADD COLUMN rpjmd_sasaran_id INT;
```

### File Structure
```
app/
├── Controllers/RPJMD.php              # Main controller for RPJMD functionality
├── Models/
│   ├── RpjmdPriorityZoneModel.php     # Model for priority zones
│   └── RpjmdSasaranModel.php          # Model for RPJMD sasaran (already existed)
├── Views/rpjmd/
│   └── index.php                      # Main RPJMD interface view
└── Database/
    ├── Migrations/
    │   └── 2025-09-29-005731_CreateRpjmdPriorityZones.php
    └── Seeds/
        └── RPJMDSampleSeeder.php      # Sample data seeder

public/assets/
├── js/rpjmd.js                        # JavaScript functionality for RPJMD
└── css/style.css                      # Updated with RPJMD styles
```

### API Endpoints
- `GET /rpjmd` - Main RPJMD interface page
- `GET /rpjmd/api/priority-layers` - Get all priority layers data
- `GET /rpjmd/api/alignment-analysis` - Get comprehensive alignment analysis
- `GET /rpjmd/api/alignment-analysis?program_id={id}` - Get single program analysis

### JavaScript Functions
- `initRPJMDMap()` - Initialize the RPJMD map with all components
- `loadPriorityLayers()` - Load and display priority zones
- `performAlignment()` - Execute alignment analysis
- `showPriorityLayer(layerId)` / `hidePriorityLayer(layerId)` - Toggle layer visibility
- `applyFilters()` - Apply filtering to programs

## Sample Data
Modul ini dilengkapi dengan data sampel:
- 4 kawasan strategis (Pusat Bisnis, Heritage, Industri, Pelabuhan)
- 4 zona tematik (Pendidikan, Kesehatan, Pariwisata, Teknologi)
- Random assignment untuk program yang sudah ada

## Usage

### Administrator
1. Akses menu RPJMD dari navigasi utama
2. Gunakan layer toggles untuk mengaktifkan zona prioritas yang diinginkan
3. Set filter untuk menampilkan program berdasarkan kriteria tertentu
4. Klik "Apply Analysis" untuk melakukan analisis keselarasan
5. Review hasil analisis di panel samping
6. Klik program marker untuk melihat detail keselarasan individual

### Developer
1. Extend `RpjmdPriorityZoneModel` untuk menambah metode analisis baru
2. Customize colors dan styling di `public/assets/css/style.css`
3. Add new priority zones melalui admin interface (belum diimplementasi) atau seeder
4. Modify alignment algorithm di `RPJMD::analyzeProgram()` method

## Future Enhancements
- Admin interface untuk mengelola priority zones
- Export hasil analisis ke PDF/Excel
- Historical analysis dan trending
- Integration dengan sistem perencanaan daerah lainnya
- Advanced spatial analysis (buffer zones, proximity analysis)
- Multi-year RPJMD comparison

## Dependencies
- CodeIgniter 4.6+
- Leaflet.js 1.9.4
- Font Awesome 6.4.0
- MySQL 8.0+ (untuk JSON column support)
- PHP 8.1+ (untuk modern PHP features)