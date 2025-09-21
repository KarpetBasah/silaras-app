<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-plus-circle"></i> Input Program Baru</h1>
        <p>Form digital untuk memasukkan data program infrastruktur secara spasial dan tematik</p>
    </div>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Terdapat kesalahan pada form:</strong>
                <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="/input-program/store" class="program-form" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="form-grid">
                <!-- Program Information -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informasi Program</h3>
                    
                    <div class="form-group">
                        <label for="nama_kegiatan">Nama Kegiatan <span class="required">*</span></label>
                        <input type="text" 
                               id="nama_kegiatan" 
                               name="nama_kegiatan" 
                               class="form-control" 
                               value="<?= old('nama_kegiatan') ?>"
                               placeholder="Masukkan nama kegiatan/program" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Program</label>
                        <textarea id="deskripsi" 
                                  name="deskripsi" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Deskripsi detail program (opsional)"><?= old('deskripsi') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tahun_pelaksanaan">Tahun Pelaksanaan <span class="required">*</span></label>
                            <select id="tahun_pelaksanaan" name="tahun_pelaksanaan" class="form-control" required>
                                <option value="">Pilih Tahun</option>
                                <?php for ($year = 2024; $year <= 2030; $year++): ?>
                                    <option value="<?= $year ?>" <?= old('tahun_pelaksanaan') == $year ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endfor ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="anggaran">Anggaran (Rupiah) <span class="required">*</span></label>
                            <input type="number" 
                                   id="anggaran" 
                                   name="anggaran" 
                                   class="form-control" 
                                   value="<?= old('anggaran') ?>"
                                   placeholder="0" 
                                   min="1" 
                                   required>
                        </div>
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Informasi Lokasi</h3>
                    
                    <div class="form-group">
                        <label for="alamat">Alamat Lokasi</label>
                        <textarea id="alamat" 
                                  name="alamat" 
                                  class="form-control" 
                                  rows="2" 
                                  placeholder="Alamat lengkap lokasi program"><?= old('alamat') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="lokasi_lat">Latitude <span class="required">*</span></label>
                            <input type="text" 
                                   id="lokasi_lat" 
                                   name="lokasi_lat" 
                                   class="form-control" 
                                   value="<?= old('lokasi_lat') ?>"
                                   placeholder="-3.4582" 
                                   pattern="-?[0-9]+\.?[0-9]*"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lokasi_lng">Longitude <span class="required">*</span></label>
                            <input type="text" 
                                   id="lokasi_lng" 
                                   name="lokasi_lng" 
                                   class="form-control" 
                                   value="<?= old('lokasi_lng') ?>"
                                   placeholder="114.8348" 
                                   pattern="-?[0-9]+\.?[0-9]*"
                                   required>
                        </div>
                    </div>
                    
                    <div class="map-helper">
                        <button type="button" class="btn btn-outline" id="btn-map-picker">
                            <i class="fas fa-map"></i> Pilih dari Peta
                        </button>
                        <button type="button" class="btn btn-outline" id="btn-validate-location">
                            <i class="fas fa-shield-alt"></i> Validasi Lokasi
                        </button>
                    </div>
                    
                    <div id="location-validation" class="validation-result" style="display: none;"></div>
                </div>
                
                <!-- Sector and RPJMD -->
                <div class="form-section">
                    <h3><i class="fas fa-tags"></i> Kategorisasi</h3>
                    
                    <div class="form-group">
                        <label for="sektor">Sektor <span class="required">*</span></label>
                        <select id="sektor" name="sektor" class="form-control" required>
                            <option value="">Pilih Sektor</option>
                            <option value="jalan" <?= old('sektor') == 'jalan' ? 'selected' : '' ?>>Jalan dan Transportasi</option>
                            <option value="irigasi" <?= old('sektor') == 'irigasi' ? 'selected' : '' ?>>Irigasi dan Pengairan</option>
                            <option value="pendidikan" <?= old('sektor') == 'pendidikan' ? 'selected' : '' ?>>Pendidikan</option>
                            <option value="kesehatan" <?= old('sektor') == 'kesehatan' ? 'selected' : '' ?>>Kesehatan</option>
                            <option value="ekonomi" <?= old('sektor') == 'ekonomi' ? 'selected' : '' ?>>Ekonomi dan Perdagangan</option>
                            <option value="sosial" <?= old('sektor') == 'sosial' ? 'selected' : '' ?>>Sosial dan Budaya</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sasaran_rpjmd">Sasaran RPJMD <span class="required">*</span></label>
                        <select id="sasaran_rpjmd" name="sasaran_rpjmd" class="form-control" required>
                            <option value="">Pilih Sasaran RPJMD</option>
                            <option value="infrastruktur_dasar" <?= old('sasaran_rpjmd') == 'infrastruktur_dasar' ? 'selected' : '' ?>>Pembangunan Infrastruktur Dasar</option>
                            <option value="pendidikan_berkualitas" <?= old('sasaran_rpjmd') == 'pendidikan_berkualitas' ? 'selected' : '' ?>>Pendidikan Berkualitas</option>
                            <option value="kesehatan_masyarakat" <?= old('sasaran_rpjmd') == 'kesehatan_masyarakat' ? 'selected' : '' ?>>Kesehatan Masyarakat</option>
                            <option value="ekonomi_berkelanjutan" <?= old('sasaran_rpjmd') == 'ekonomi_berkelanjutan' ? 'selected' : '' ?>>Ekonomi Berkelanjutan</option>
                            <option value="lingkungan_lestari" <?= old('sasaran_rpjmd') == 'lingkungan_lestari' ? 'selected' : '' ?>>Lingkungan Lestari</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="opd">OPD Pelaksana <span class="required">*</span></label>
                        <select id="opd" name="opd" class="form-control" required>
                            <option value="">Pilih OPD</option>
                            <option value="dinas_pupr" <?= old('opd') == 'dinas_pupr' ? 'selected' : '' ?>>Dinas PUPR</option>
                            <option value="dinas_pendidikan" <?= old('opd') == 'dinas_pendidikan' ? 'selected' : '' ?>>Dinas Pendidikan</option>
                            <option value="dinas_kesehatan" <?= old('opd') == 'dinas_kesehatan' ? 'selected' : '' ?>>Dinas Kesehatan</option>
                            <option value="dinas_pertanian" <?= old('opd') == 'dinas_pertanian' ? 'selected' : '' ?>>Dinas Pertanian</option>
                            <option value="dinas_perdagangan" <?= old('opd') == 'dinas_perdagangan' ? 'selected' : '' ?>>Dinas Perdagangan</option>
                            <option value="bappeda" <?= old('opd') == 'bappeda' ? 'selected' : '' ?>>BAPPEDA</option>
                        </select>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="form-section">
                    <h3><i class="fas fa-paperclip"></i> Dokumen Pendukung</h3>
                    
                    <div class="form-group">
                        <label for="rab_file">RAB (Rencana Anggaran Biaya)</label>
                        <input type="file" 
                               id="rab_file" 
                               name="rab_file" 
                               class="form-control-file" 
                               accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <small class="form-text">Format: PDF, DOC, DOCX, XLS, XLSX (Max: 5MB)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="ded_file">DED (Detail Engineering Design)</label>
                        <input type="file" 
                               id="ded_file" 
                               name="ded_file" 
                               class="form-control-file" 
                               accept=".pdf,.dwg,.dxf">
                        <small class="form-text">Format: PDF, DWG, DXF (Max: 10MB)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="foto_lokasi">Foto Lokasi</label>
                        <input type="file" 
                               id="foto_lokasi" 
                               name="foto_lokasi[]" 
                               class="form-control-file" 
                               accept="image/*" 
                               multiple>
                        <small class="form-text">Format: JPG, PNG, GIF (Max: 2MB per file, maksimal 5 foto)</small>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Program
                </button>
                <a href="/input-program" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Simple Map Modal (placeholder) -->
<div id="map-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Pilih Lokasi di Peta</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="map-picker" style="height: 400px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b;">
                Peta akan dimuat di sini (integrasi dengan Leaflet.js)
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="confirm-location">Gunakan Lokasi Ini</button>
            <button type="button" class="btn btn-secondary modal-close">Batal</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>