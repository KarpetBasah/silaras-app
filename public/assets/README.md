# SiLaras Assets Documentation

Dokumentasi untuk penggunaan assets dalam aplikasi SiLaras.

## Struktur Assets

```
public/assets/
├── css/
│   └── style.css       # Main stylesheet
├── js/
│   └── main.js         # Main JavaScript file
└── images/             # (untuk file gambar di masa depan)
```

## CSS (style.css)

File CSS utama yang berisi semua styling untuk aplikasi SiLaras:

### Fitur CSS:
- **CSS Variables** untuk tema warna yang konsisten
- **Responsive Design** dengan mobile-first approach
- **Flexbox & Grid Layout** untuk tata letak modern
- **Smooth transitions** dan animasi
- **Notification system** styling
- **Loading states** untuk button

### CSS Variables:
```css
--primary-color: #2563eb     /* Biru utama */
--primary-dark: #1d4ed8      /* Biru gelap */
--secondary-color: #64748b    /* Abu-abu */
--accent-color: #10b981      /* Hijau aksen */
--danger-color: #ef4444      /* Merah */
--warning-color: #f59e0b     /* Kuning */
```

### Breakpoints:
- Desktop: > 768px
- Mobile: ≤ 768px
- Small Mobile: ≤ 480px

## JavaScript (main.js)

File JavaScript utama yang menangani interaksi UI:

### Fitur JavaScript:
- **Mobile Navigation** toggle dan responsive behavior
- **Dropdown Menu** untuk mobile devices
- **Smooth Scrolling** untuk internal links
- **Notification System** untuk feedback user
- **Utility Functions** untuk formatting dan animasi

### Fungsi Utama:

#### SiLaras.showNotification(message, type)
Menampilkan notifikasi kepada user.
```javascript
// Contoh penggunaan:
SiLaras.showNotification('Data berhasil disimpan!', 'success');
SiLaras.showNotification('Terjadi kesalahan!', 'error');
SiLaras.showNotification('Peringatan penting!', 'warning');
SiLaras.showNotification('Informasi umum', 'info');
```

#### SiLaras.formatNumber(num)
Format angka dengan pemisah ribuan.
```javascript
SiLaras.formatNumber(1234567); // "1.234.567"
```

#### SiLaras.formatCurrency(amount)
Format mata uang Rupiah.
```javascript
SiLaras.formatCurrency(1500000); // "Rp1.500.000"
```

#### SiLaras.animateCounter(element, start, end, duration)
Animasi counter untuk statistik.
```javascript
const element = document.querySelector('.counter');
SiLaras.animateCounter(element, 0, 100, 2000); // Count from 0 to 100 in 2 seconds
```

## Penggunaan dalam View

### Memasukkan CSS:
```php
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
```

### Memasukkan JavaScript:
```php
<script src="<?= base_url('assets/js/main.js') ?>"></script>
```

## Best Practices

### CSS:
1. Gunakan CSS variables untuk konsistensi warna
2. Ikuti mobile-first approach
3. Gunakan semantic class names
4. Avoid !important kecuali benar-benar diperlukan

### JavaScript:
1. Gunakan event delegation untuk element dinamis
2. Check null/undefined sebelum manipulasi DOM
3. Gunakan modern JavaScript (ES6+)
4. Tambahkan error handling

## Menambah CSS/JS Baru

### Untuk CSS tambahan:
1. Buat file baru di `public/assets/css/`
2. Import di view dengan `base_url()`
3. Atau tambahkan ke style.css jika terkait

### Untuk JavaScript tambahan:
1. Buat file baru di `public/assets/js/`
2. Import setelah main.js
3. Gunakan namespace untuk avoid conflict

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Optimisasi

### CSS:
- Gunakan minified version untuk production
- Combine multiple CSS files jika perlu
- Use critical CSS untuk above-the-fold content

### JavaScript:
- Minify untuk production
- Use async/defer attributes
- Consider lazy loading untuk non-critical scripts

## Troubleshooting

### CSS tidak muncul:
1. Check path dengan `base_url()`
2. Pastikan file exists dan readable
3. Check browser cache
4. Validate CSS syntax

### JavaScript error:
1. Check browser console
2. Pastikan DOM elements ada sebelum manipulasi
3. Check JavaScript syntax
4. Ensure proper event listeners

---

*Dokumentasi ini akan diupdate seiring pengembangan aplikasi SiLaras.*