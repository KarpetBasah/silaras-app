<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-chart-bar"></i> Monitoring & Evaluasi</h1>
        <p>Visualisasi progres fisik dan realisasi anggaran dalam peta dinamis</p>
    </div>
    
    <div class="coming-soon">
        <div class="coming-soon-content">
            <i class="fas fa-tools"></i>
            <h2>Modul Sedang Dalam Pengembangan</h2>
            <p>Modul Monitoring & Evaluasi sedang dalam tahap pengembangan dan akan segera tersedia.</p>
            <p>Fitur yang akan tersedia:</p>
            <ul>
                <li>Form Input Progres Fisik dan Realisasi Anggaran</li>
                <li>Peta Dinamis dengan Indikator Progres</li>
                <li>Dashboard Statistik Monitoring</li>
                <li>Grafik Progres per Sektor dan Wilayah</li>
                <li>Laporan Evaluasi Berkala</li>
            </ul>
        </div>
    </div>
</div>

<style>
.coming-soon {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 500px;
    background: var(--white);
    border-radius: 12px;
    margin-top: 2rem;
    box-shadow: var(--shadow);
}

.coming-soon-content {
    text-align: center;
    max-width: 600px;
    padding: 2rem;
}

.coming-soon-content i {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.coming-soon-content h2 {
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.coming-soon-content p {
    color: var(--text-light);
    margin-bottom: 1rem;
}

.coming-soon-content ul {
    text-align: left;
    color: var(--text-light);
    max-width: 400px;
    margin: 0 auto;
}

.coming-soon-content li {
    margin-bottom: 0.5rem;
}
</style>

<?= $this->endSection() ?>