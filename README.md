# SiLaras - Sistem Perencanaan Berbasis Geospasial

![SiLaras Logo](https://img.shields.io/badge/SiLaras-Geospatial%20Planning%20System-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple)
![License](https://img.shields.io/badge/License-MIT-green)

**SiLaras** adalah Sistem Perencanaan Berbasis Geospasial untuk Aksi Perubahan Peningkatan Kinerja Infrastruktur Pembangunan, Riset dan Inovasi Daerah Kota Banjarbaru.

## 🚀 Fitur Utama

- **📊 Dashboard Analitik** - Visualisasi data real-time dengan grafik interaktif
- **🗺️ Peta Geospasial** - Integrasi dengan teknologi GIS untuk analisis spasial
- **📋 Perencanaan Terintegrasi** - Manajemen program OPD dengan koordinasi antar instansi
- **📈 Monitoring Real-time** - Pantau progress pelaksanaan program secara langsung
- **📝 Evaluasi Kinerja** - Sistem evaluasi komprehensif dengan indikator terukur
- **👥 Kolaborasi Multi-OPD** - Platform koordinasi efektif antar Organisasi Perangkat Daerah

## 🛠️ Teknologi

- **Backend**: CodeIgniter 4 (PHP 8.1+)
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Database**: MySQL/MariaDB
- **Maps**: Integration ready untuk GIS
- **Icons**: Font Awesome 6
- **Responsive**: Mobile-first design

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/your-username/silaras-app.git
cd silaras-app
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:
```env
database.default.hostname = localhost
database.default.database = silaras_db
database.default.username = your_username
database.default.password = your_password
database.default.DBDriver = MySQLi
```

### 4. Database Setup
```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

### 5. Set Permissions
```bash
chmod -R 755 writable/
```

### 6. Run Development Server
```bash
php spark serve
```

Aplikasi akan berjalan di `http://localhost:8080`

## 📁 Struktur Project

```
silaras-app/
├── app/
│   ├── Config/          # Konfigurasi aplikasi
│   ├── Controllers/     # HTTP request handlers
│   ├── Models/          # Data models
│   ├── Views/           # Template files
│   └── Database/        # Migrations & Seeds
├── public/
│   ├── assets/
│   │   ├── css/         # Stylesheet files
│   │   ├── js/          # JavaScript files
│   │   └── images/      # Image assets
│   └── index.php        # Entry point
├── writable/            # Cache, logs, uploads
├── tests/               # Unit tests
└── vendor/              # Composer dependencies
```

## 🤝 Contributing

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📋 Requirements

- PHP 8.1 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.3+
- Apache/Nginx Web Server
- Composer untuk dependency management
- Git untuk version control

### PHP Extensions Required:
- `intl`
- `mbstring` 
- `json` (enabled by default)
- `mysqlnd` (untuk MySQL)
- `curl` (untuk HTTP requests)

## 📄 License

Project ini menggunakan [MIT License](LICENSE).

## 👥 Tim Pengembang

- **Developer**: [Nama Anda]
- **Organization**: Pemerintah Kota Banjarbaru
- **Department**: Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah

## 📞 Kontak

- **Email**: [email@banjarbaru.go.id]
- **Website**: [https://banjarbaru.go.id]
- **Issues**: [GitHub Issues](https://github.com/your-username/silaras-app/issues)

---

**SiLaras** - Membangun masa depan Kota Banjarbaru melalui perencanaan berbasis data geospasial yang inovatif dan terintegrasi.

*Dibuat dengan ❤️ untuk Kota Banjarbaru*
