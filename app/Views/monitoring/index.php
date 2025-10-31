<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-chart-bar"></i> Monitoring & Evaluasi</h1>
        <p>Visualisasi progres fisik dan realisasi anggaran dalam peta dinamis</p>
    </div>
    
    <!-- Dashboard Controls -->
    <div class="dashboard-controls">
        <div class="filter-group">
            <label for="filter-tahun">Filter Tahun:</label>
            <select id="filter-tahun" class="form-control-sm" onchange="updateDashboard()">
                <option value="">Semua Tahun</option>
                <?php foreach ($tahun_list as $tahun): ?>
                    <option value="<?= $tahun ?>"><?= $tahun ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="action-buttons">
            <a href="/monitoring/map" class="btn btn-primary">
                <i class="fas fa-map"></i> Lihat Peta Monitoring
            </a>
            <button onclick="showInputProgressModal()" class="btn btn-success">
                <i class="fas fa-plus"></i> Input Progress
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3 id="total-program"><?= $statistics['total_program_aktif'] ?? 0 ?></h3>
                <p>Total Program Aktif</p>
            </div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3 id="rata-progress"><?= number_format($statistics['rata_progress_fisik'] ?? 0, 1) ?>%</h3>
                <p>Rata-rata Progress Fisik</p>
            </div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <h3 id="rata-keuangan"><?= number_format($statistics['rata_progress_keuangan'] ?? 0, 1) ?>%</h3>
                <p>Rata-rata Progress Keuangan</p>
            </div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-rupiah-sign"></i>
            </div>
            <div class="stat-content">
                <h3 id="realisasi-anggaran">
                    <?php 
                    $realisasi = $statistics['total_realisasi'] ?? 0;
                    $total = $statistics['total_anggaran'] ?? 1;
                    $persentase = $total > 0 ? ($realisasi / $total) * 100 : 0;
                    echo number_format($persentase, 1) . '%';
                    ?>
                </h3>
                <p>Realisasi Anggaran Total</p>
            </div>
        </div>
    </div>

    <!-- Progress by Sector -->
    <div class="section-card">
        <div class="section-header">
            <h3><i class="fas fa-chart-pie"></i> Progress per Sektor</h3>
        </div>
        <div class="sector-stats">
            <?php if (!empty($statistics_by_sektor)): ?>
                <?php foreach ($statistics_by_sektor as $sektor): ?>
                    <div class="sector-item">
                        <div class="sector-info">
                            <div class="sector-icon" style="background-color: <?= $sektor['sektor_color'] ?>">
                                <i class="<?= $sektor['sektor_icon'] ?>"></i>
                            </div>
                            <div class="sector-details">
                                <h4><?= $sektor['nama_sektor'] ?></h4>
                                <p><?= $sektor['total_program'] ?> Program</p>
                            </div>
                        </div>
                        <div class="sector-progress">
                            <div class="progress-info">
                                <span>Fisik: <?= number_format($sektor['rata_progress_fisik'], 1) ?>%</span>
                                <span>Keuangan: <?= number_format($sektor['rata_progress_keuangan'], 1) ?>%</span>
                            </div>
                            <div class="progress-bars">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $sektor['rata_progress_fisik'] ?>%; background-color: <?= $sektor['sektor_color'] ?>"></div>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $sektor['rata_progress_keuangan'] ?>%; background-color: <?= $sektor['sektor_color'] ?>; opacity: 0.7"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-chart-bar"></i>
                    <p>Belum ada data monitoring yang tersedia</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert: Low Progress Programs -->
    <?php if (!empty($low_progress_programs)): ?>
    <div class="section-card alert-section">
        <div class="section-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Program dengan Progress Rendah</h3>
            <p>Program dengan progress fisik di bawah 50%</p>
        </div>
        <div class="alert-list">
            <?php foreach ($low_progress_programs as $program): ?>
                <div class="alert-item">
                    <div class="alert-info">
                        <h4><?= $program['nama_kegiatan'] ?></h4>
                        <p>
                            <span class="program-code"><?= $program['kode_program'] ?></span>
                            <span class="program-opd"><?= $program['opd_nama'] ?></span>
                            <span class="program-sektor"><?= $program['nama_sektor'] ?></span>
                        </p>
                    </div>
                    <div class="alert-progress">
                        <div class="progress-indicator danger">
                            <?= number_format($program['progress_fisik'], 1) ?>%
                        </div>
                        <button onclick="inputProgress(<?= $program['program_id'] ?>)" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Update Progress
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Input Modal -->
    <div id="inputProgressModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Input Progress Program</h3>
                <button onclick="closeModal()" class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="program-selector">
                    <label for="select-program">Pilih Program:</label>
                    <select id="select-program" class="form-control">
                        <option value="">-- Pilih Program --</option>
                    </select>
                </div>
                <div id="program-info" class="program-info" style="display: none;">
                    <p>Program dipilih. <a href="#" id="go-to-detail">Lanjut ke halaman input detail</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: var(--text-dark);
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}

.stat-card.primary { border-left: 4px solid var(--primary-color); }
.stat-card.success { border-left: 4px solid var(--success-color); }
.stat-card.warning { border-left: 4px solid var(--warning-color); }
.stat-card.info { border-left: 4px solid var(--accent-color); }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
}

.stat-card.primary .stat-icon { background: var(--primary-color); }
.stat-card.success .stat-icon { background: var(--success-color); }
.stat-card.warning .stat-icon { background: var(--warning-color); }
.stat-card.info .stat-icon { background: var(--accent-color); }

.stat-content h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
    color: var(--text-dark);
}

.stat-content p {
    margin: 0;
    color: var(--text-light);
    font-weight: 500;
}

.section-card {
    background: var(--white);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
}

.section-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.section-header h3 {
    margin: 0;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-header p {
    margin: 0.5rem 0 0 0;
    color: var(--text-light);
}

.sector-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.sector-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sector-item:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.sector-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sector-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.2rem;
}

.sector-details h4 {
    margin: 0;
    color: var(--text-dark);
}

.sector-details p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.sector-progress {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 200px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: var(--text-dark);
}

.progress-bars {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: var(--border-color);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    transition: all 0.3s ease;
}

.alert-section {
    border-left: 4px solid var(--warning-color);
}

.alert-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.alert-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
}

.alert-info h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-dark);
}

.alert-info p {
    margin: 0;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.program-code, .program-opd, .program-sektor {
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    background: var(--white);
    color: var(--text-dark);
}

.alert-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-indicator {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: bold;
    color: var(--white);
}

.progress-indicator.danger {
    background: var(--error-color);
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--text-light);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background: var(--white);
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    box-shadow: var(--shadow-lg);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: var(--text-dark);
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-light);
}

.modal-body {
    padding: 1.5rem;
}

.program-selector {
    margin-bottom: 1rem;
}

.program-selector label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-dark);
}

.program-info {
    padding: 1rem;
    background: var(--light-bg);
    border-radius: 8px;
    text-align: center;
}

.program-info a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

@media (max-width: 768px) {
    .dashboard-controls {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .sector-item {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .sector-progress {
        min-width: auto;
    }
    
    .alert-item {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
}
</style>

<script>
// Load programs for dropdown
async function loadPrograms() {
    try {
        const response = await fetch('/monitoring/getPrograms', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        const result = await response.json();
        
        if (result.success) {
            const select = document.getElementById('select-program');
            select.innerHTML = '<option value="">-- Pilih Program --</option>';
            
            result.data.forEach(program => {
                const option = document.createElement('option');
                option.value = program.id;
                option.textContent = `${program.kode_program} - ${program.nama_kegiatan}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading programs:', error);
    }
}

// Show input progress modal
function showInputProgressModal() {
    document.getElementById('inputProgressModal').style.display = 'block';
    loadPrograms();
}

// Close modal
function closeModal() {
    document.getElementById('inputProgressModal').style.display = 'none';
    document.getElementById('program-info').style.display = 'none';
}

// Handle program selection
document.getElementById('select-program').addEventListener('change', function() {
    const programId = this.value;
    const programInfo = document.getElementById('program-info');
    const goToDetail = document.getElementById('go-to-detail');
    
    if (programId) {
        programInfo.style.display = 'block';
        goToDetail.href = `/monitoring/input-progress/${programId}`;
    } else {
        programInfo.style.display = 'none';
    }
});

// Direct input progress for specific program
function inputProgress(programId) {
    window.location.href = `/monitoring/input-progress/${programId}`;
}

// Update dashboard with filter
async function updateDashboard() {
    const tahun = document.getElementById('filter-tahun').value;
    
    try {
        const url = `/monitoring/getStatistics?tahun=${tahun}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update main statistics
            const stats = result.statistics;
            document.getElementById('total-program').textContent = stats.total_program_aktif || 0;
            document.getElementById('rata-progress').textContent = (stats.rata_progress_fisik || 0).toFixed(1) + '%';
            document.getElementById('rata-keuangan').textContent = (stats.rata_progress_keuangan || 0).toFixed(1) + '%';
            
            const realisasiPersen = stats.total_anggaran > 0 ? 
                ((stats.total_realisasi / stats.total_anggaran) * 100) : 0;
            document.getElementById('realisasi-anggaran').textContent = realisasiPersen.toFixed(1) + '%';
            
            // Update sector statistics
            updateSectorStatistics(result.statistics_by_sektor);
        }
    } catch (error) {
        console.error('Error updating dashboard:', error);
    }
}

// Update sector statistics dynamically
function updateSectorStatistics(sektorStats) {
    const sektorContainer = document.querySelector('.sector-stats');
    
    if (sektorStats && sektorStats.length > 0) {
        let html = '';
        sektorStats.forEach(sektor => {
            html += `
                <div class="sector-item">
                    <div class="sector-info">
                        <div class="sector-icon" style="background-color: ${sektor.sektor_color}">
                            <i class="${sektor.sektor_icon}"></i>
                        </div>
                        <div class="sector-details">
                            <h4>${sektor.nama_sektor}</h4>
                            <p>${sektor.total_program} Program</p>
                        </div>
                    </div>
                    <div class="sector-progress">
                        <div class="progress-info">
                            <span>Fisik: ${parseFloat(sektor.rata_progress_fisik || 0).toFixed(1)}%</span>
                            <span>Keuangan: ${parseFloat(sektor.rata_progress_keuangan || 0).toFixed(1)}%</span>
                        </div>
                        <div class="progress-bars">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${sektor.rata_progress_fisik || 0}%; background-color: ${sektor.sektor_color}"></div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${sektor.rata_progress_keuangan || 0}%; background-color: ${sektor.sektor_color}; opacity: 0.7"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        sektorContainer.innerHTML = html;
    } else {
        sektorContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-chart-bar"></i>
                <p>Belum ada data monitoring yang tersedia untuk tahun ini</p>
            </div>
        `;
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('inputProgressModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?= $this->endSection() ?>