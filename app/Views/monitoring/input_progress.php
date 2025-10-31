<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <div class="header-left">
            <h1><i class="fas fa-edit"></i> Input Progress Monitoring</h1>
            <p>Update progress fisik dan realisasi anggaran program</p>
        </div>
        <div class="header-right">
            <a href="/monitoring" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Program Information -->
    <div class="program-info-card">
        <div class="program-header">
            <div class="program-icon" style="background-color: <?= $program['sektor_color'] ?>">
                <i class="<?= $program['sektor_icon'] ?>"></i>
            </div>
            <div class="program-details">
                <h2><?= $program['nama_kegiatan'] ?></h2>
                <div class="program-meta">
                    <span class="program-code"><?= $program['kode_program'] ?></span>
                    <span class="program-opd"><?= $program['opd_nama'] ?></span>
                    <span class="program-sektor"><?= $program['nama_sektor'] ?></span>
                    <span class="program-year"><?= $program['tahun_pelaksanaan'] ?></span>
                </div>
                <div class="program-budget">
                    <span>Anggaran Total: <strong>Rp <?= number_format($program['anggaran_total'], 0, ',', '.') ?></strong></span>
                    <?php if ($latest_monitoring): ?>
                        <span>Progress Terakhir: <strong><?= number_format($latest_monitoring['progress_fisik'], 1) ?>%</strong> (<?= date('d/m/Y', strtotime($latest_monitoring['tanggal_monitoring'])) ?>)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="monitoring-form-container">
        <!-- Form Input Progress -->
        <div class="form-section">
            <div class="section-header">
                <h3><i class="fas fa-chart-line"></i> Input Data Monitoring</h3>
            </div>
            
            <form id="monitoringForm" enctype="multipart/form-data">
                <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                
                <div class="form-grid">
                    <!-- Basic Info -->
                    <div class="form-group">
                        <label for="tanggal_monitoring">Tanggal Monitoring *</label>
                        <input type="date" id="tanggal_monitoring" name="tanggal_monitoring" class="form-control" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status_lapangan">Status Lapangan</label>
                        <select id="status_lapangan" name="status_lapangan" class="form-control">
                            <option value="normal">Normal</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="terkendala">Terkendala</option>
                            <option value="dihentikan">Dihentikan</option>
                        </select>
                    </div>

                    <!-- Progress Data -->
                    <div class="form-group">
                        <label for="progress_fisik">Progress Fisik (%) *</label>
                        <div class="input-with-slider">
                            <input type="number" id="progress_fisik" name="progress_fisik" class="form-control" 
                                   min="0" max="100" step="0.1" 
                                   value="<?= $latest_monitoring['progress_fisik'] ?? 0 ?>" required>
                            <input type="range" id="progress_fisik_slider" class="progress-slider" 
                                   min="0" max="100" step="0.1" 
                                   value="<?= $latest_monitoring['progress_fisik'] ?? 0 ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="progress_keuangan">Progress Keuangan (%) *</label>
                        <div class="input-with-slider">
                            <input type="number" id="progress_keuangan" name="progress_keuangan" class="form-control" 
                                   min="0" max="100" step="0.1" 
                                   value="<?= $latest_monitoring['progress_keuangan'] ?? 0 ?>" required>
                            <input type="range" id="progress_keuangan_slider" class="progress-slider" 
                                   min="0" max="100" step="0.1" 
                                   value="<?= $latest_monitoring['progress_keuangan'] ?? 0 ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="anggaran_realisasi">Realisasi Anggaran (Rp) *</label>
                        <input type="number" id="anggaran_realisasi" name="anggaran_realisasi" class="form-control" 
                               min="0" max="<?= $program['anggaran_total'] ?>" 
                               value="<?= $latest_monitoring['anggaran_realisasi'] ?? 0 ?>" required>
                        <small class="form-text">Maksimal: Rp <?= number_format($program['anggaran_total'], 0, ',', '.') ?></small>
                    </div>

                    <!-- Additional Info -->
                    <div class="form-group">
                        <label for="cuaca">Kondisi Cuaca</label>
                        <input type="text" id="cuaca" name="cuaca" class="form-control" 
                               placeholder="Contoh: Cerah, Hujan ringan, dll">
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah_pekerja">Jumlah Pekerja</label>
                        <input type="number" id="jumlah_pekerja" name="jumlah_pekerja" class="form-control" 
                               min="0" placeholder="Jumlah pekerja di lapangan">
                    </div>

                    <!-- Validator Info -->
                    <div class="form-group">
                        <label for="validator_name">Nama Petugas Monitoring *</label>
                        <input type="text" id="validator_name" name="validator_name" class="form-control" 
                               placeholder="Nama lengkap petugas monitoring" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="validator_jabatan">Jabatan Petugas *</label>
                        <input type="text" id="validator_jabatan" name="validator_jabatan" class="form-control" 
                               placeholder="Jabatan petugas monitoring" required>
                    </div>
                </div>

                <!-- Coordinates -->
                <div class="form-group">
                    <label>Koordinat Lokasi Monitoring</label>
                    <div class="coordinate-input">
                        <input type="number" id="koordinat_lat" name="koordinat_lat" class="form-control" 
                               step="0.000001" placeholder="Latitude">
                        <input type="number" id="koordinat_lng" name="koordinat_lng" class="form-control" 
                               step="0.000001" placeholder="Longitude">
                        <button type="button" onclick="getCurrentLocation()" class="btn btn-info">
                            <i class="fas fa-map-marker-alt"></i> Lokasi Saat Ini
                        </button>
                    </div>
                </div>

                <!-- Text Areas -->
                <div class="form-group">
                    <label for="kendala">Kendala yang Dihadapi</label>
                    <textarea id="kendala" name="kendala" class="form-control" rows="3" 
                              placeholder="Jelaskan kendala atau hambatan dalam pelaksanaan"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="solusi">Solusi yang Ditempuh</label>
                    <textarea id="solusi" name="solusi" class="form-control" rows="3" 
                              placeholder="Jelaskan solusi untuk mengatasi kendala"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="rekomendasi">Rekomendasi</label>
                    <textarea id="rekomendasi" name="rekomendasi" class="form-control" rows="3" 
                              placeholder="Rekomendasi untuk periode monitoring selanjutnya"></textarea>
                </div>

                <!-- Photo Upload -->
                <div class="form-group">
                    <label for="foto_progress">Foto Progress</label>
                    <div class="file-upload-area" onclick="document.getElementById('foto_progress').click()">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik untuk memilih foto atau drag & drop</p>
                            <small>Format: JPG, PNG, maksimal 5MB per file</small>
                        </div>
                        <input type="file" id="foto_progress" name="foto_progress[]" class="form-control" 
                               multiple accept="image/*" style="display: none;">
                    </div>
                    <div id="preview-container" class="preview-container"></div>
                </div>

                <!-- Submit Buttons -->
                <div class="form-actions">
                    <button type="button" onclick="submitForm(false)" class="btn btn-secondary">
                        <i class="fas fa-save"></i> Simpan Draft
                    </button>
                    <button type="button" onclick="submitForm(true)" class="btn btn-success">
                        <i class="fas fa-check"></i> Simpan & Verifikasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Monitoring History -->
        <?php if (!empty($monitoring_history)): ?>
        <div class="history-section">
            <div class="section-header">
                <h3><i class="fas fa-history"></i> Riwayat Monitoring</h3>
            </div>
            
            <div class="history-timeline">
                <?php foreach ($monitoring_history as $index => $history): ?>
                    <div class="timeline-item <?= $index === 0 ? 'latest' : '' ?>">
                        <div class="timeline-marker">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-date"><?= date('d/m/Y', strtotime($history['tanggal_monitoring'])) ?></span>
                                <span class="timeline-status status-<?= $history['status_lapangan'] ?>"><?= ucfirst($history['status_lapangan']) ?></span>
                            </div>
                            <div class="timeline-body">
                                <div class="progress-summary">
                                    <span>Fisik: <?= number_format($history['progress_fisik'], 1) ?>%</span>
                                    <span>Keuangan: <?= number_format($history['progress_keuangan'], 1) ?>%</span>
                                </div>
                                <p class="validator-info">Oleh: <?= $history['validator_name'] ?> (<?= $history['validator_jabatan'] ?>)</p>
                                <?php if ($history['kendala']): ?>
                                    <p class="kendala-info"><strong>Kendala:</strong> <?= $history['kendala'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-left h1 {
    margin: 0;
    color: var(--text-dark);
}

.header-left p {
    margin: 0.5rem 0 0 0;
    color: var(--text-light);
}

.program-info-card {
    background: var(--white);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary-color);
}

.program-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.program-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.5rem;
    flex-shrink: 0;
}

.program-details h2 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
}

.program-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.program-code, .program-opd, .program-sektor, .program-year {
    background: var(--light-bg);
    color: var(--text-dark);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.program-budget {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    color: var(--text-light);
}

.monitoring-form-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.form-section, .history-section {
    background: var(--white);
    border-radius: 12px;
    padding: 1.5rem;
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

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-dark);
}

.form-control {
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.input-with-slider {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.input-with-slider .form-control {
    width: 100px;
}

.progress-slider {
    flex: 1;
    height: 6px;
    background: var(--border-color);
    border-radius: 3px;
    outline: none;
    cursor: pointer;
}

.progress-slider::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    background: var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
}

.coordinate-input {
    display: flex;
    gap: 0.5rem;
}

.coordinate-input .form-control {
    flex: 1;
}

.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: var(--light-bg);
}

.upload-content i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.upload-content p {
    margin: 0.5rem 0;
    color: var(--text-dark);
    font-weight: 500;
}

.upload-content small {
    color: var(--text-light);
}

.preview-container {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.preview-item {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid var(--border-color);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-remove {
    position: absolute;
    top: 5px;
    right: 5px;
    background: var(--error-color);
    color: var(--white);
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    font-size: 0.8rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
}

.timeline-item {
    position: relative;
    padding-left: 2rem;
    margin-bottom: 1.5rem;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: 7px;
    top: 20px;
    bottom: -20px;
    width: 2px;
    background: var(--border-color);
}

.timeline-item.latest:before {
    background: var(--primary-color);
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-item.latest .timeline-marker {
    color: var(--primary-color);
}

.timeline-content {
    background: var(--light-bg);
    border-radius: 8px;
    padding: 1rem;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.timeline-date {
    font-weight: 500;
    color: var(--text-dark);
}

.timeline-status {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--white);
}

.status-normal { background: var(--success-color); }
.status-terlambat { background: var(--warning-color); }
.status-terkendala { background: var(--error-color); }
.status-dihentikan { background: var(--text-light); }

.progress-summary {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.progress-summary span {
    background: var(--white);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
}

.validator-info {
    margin: 0.5rem 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.kendala-info {
    margin: 0.5rem 0 0 0;
    color: var(--text-dark);
    font-size: 0.9rem;
}

@media (max-width: 1024px) {
    .monitoring-form-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .program-header {
        flex-direction: column;
        text-align: center;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .input-with-slider {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .input-with-slider .form-control {
        width: 100%;
    }
    
    .coordinate-input {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Sync sliders with number inputs
document.getElementById('progress_fisik').addEventListener('input', function() {
    document.getElementById('progress_fisik_slider').value = this.value;
});

document.getElementById('progress_fisik_slider').addEventListener('input', function() {
    document.getElementById('progress_fisik').value = this.value;
});

document.getElementById('progress_keuangan').addEventListener('input', function() {
    document.getElementById('progress_keuangan_slider').value = this.value;
});

document.getElementById('progress_keuangan_slider').addEventListener('input', function() {
    document.getElementById('progress_keuangan').value = this.value;
});

// File preview
document.getElementById('foto_progress').addEventListener('change', function() {
    const files = Array.from(this.files);
    const previewContainer = document.getElementById('preview-container');
    previewContainer.innerHTML = '';
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="preview-remove" onclick="removePreview(${index})">&times;</button>
                `;
                previewContainer.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Remove preview
function removePreview(index) {
    const input = document.getElementById('foto_progress');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('koordinat_lat').value = position.coords.latitude.toFixed(6);
            document.getElementById('koordinat_lng').value = position.coords.longitude.toFixed(6);
        }, function(error) {
            alert('Tidak dapat mengakses lokasi GPS: ' + error.message);
        });
    } else {
        alert('Browser tidak mendukung GPS');
    }
}

// Submit form
async function submitForm(verify = false) {
    const form = document.getElementById('monitoringForm');
    const formData = new FormData(form);
    
    if (verify) {
        formData.append('is_verified', '1');
    }
    
    try {
        const response = await fetch('/monitoring/saveProgress', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Data monitoring berhasil disimpan!');
            window.location.href = '/monitoring';
        } else {
            alert('Error: ' + result.message);
            if (result.errors) {
                console.error('Validation errors:', result.errors);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    }
}

// Format number inputs
document.getElementById('anggaran_realisasi').addEventListener('input', function() {
    let value = this.value.replace(/[^\d]/g, '');
    this.value = value;
});
</script>

<?= $this->endSection() ?>