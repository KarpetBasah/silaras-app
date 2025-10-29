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
                            <label for="anggaran_total">Anggaran (Rupiah) <span class="required">*</span></label>
                            <input type="text" 
                                   id="anggaran_total" 
                                   name="anggaran_total" 
                                   class="form-control" 
                                   value="<?= old('anggaran_total') ?>"
                                   placeholder="Masukkan nominal anggaran" 
                                   pattern="[0-9,.]+"
                                   title="Masukkan angka saja, gunakan koma atau titik untuk pemisah ribuan"
                                   required>
                        </div>
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Informasi Lokasi</h3>
                    
                    <div class="form-group">
                        <label for="lokasi_alamat">Alamat Lokasi</label>
                        <textarea id="lokasi_alamat" 
                                  name="lokasi_alamat" 
                                  class="form-control" 
                                  rows="2" 
                                  placeholder="Alamat lengkap lokasi program"><?= old('lokasi_alamat') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="koordinat_lat">Latitude <span class="required">*</span></label>
                            <input type="text" 
                                   id="koordinat_lat" 
                                   name="koordinat_lat" 
                                   class="form-control" 
                                   value="<?= old('koordinat_lat') ?>"
                                   placeholder="-3.4582" 
                                   pattern="-?[0-9]+\.?[0-9]*"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="koordinat_lng">Longitude <span class="required">*</span></label>
                            <input type="text" 
                                   id="koordinat_lng" 
                                   name="koordinat_lng" 
                                   class="form-control" 
                                   value="<?= old('koordinat_lng') ?>"
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
                        <label for="sektor_id">Sektor <span class="required">*</span></label>
                        <select id="sektor_id" name="sektor_id" class="form-control" required>
                            <option value="">Pilih Sektor</option>
                            <?php foreach ($sektor_list as $sektor): ?>
                                <option value="<?= $sektor['id'] ?>" <?= old('sektor_id') == $sektor['id'] ? 'selected' : '' ?>>
                                    <?= $sektor['nama_sektor'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="rpjmd_sasaran_id">Sasaran RPJMD <span class="required">*</span></label>
                        <select id="rpjmd_sasaran_id" name="rpjmd_sasaran_id" class="form-control" required>
                            <option value="">Pilih Sasaran RPJMD</option>
                            <?php foreach ($rpjmd_list as $rpjmd): ?>
                                <option value="<?= $rpjmd['id'] ?>" <?= old('rpjmd_sasaran_id') == $rpjmd['id'] ? 'selected' : '' ?>>
                                    <?= $rpjmd['nama_sasaran'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="opd_id">OPD Pelaksana <span class="required">*</span></label>
                        <select id="opd_id" name="opd_id" class="form-control" required>
                            <option value="">Pilih OPD</option>
                            <?php foreach ($opd_list as $opd): ?>
                                <option value="<?= $opd['id'] ?>" <?= old('opd_id') == $opd['id'] ? 'selected' : '' ?>>
                                    <?= $opd['nama_singkat'] ?>
                                </option>
                            <?php endforeach; ?>
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

<!-- Interactive Map Modal -->
<div id="map-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Pilih Lokasi di Peta</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: 1rem;">
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Klik di peta untuk memilih lokasi atau seret marker untuk memindahkan posisi
                </p>
                <div id="temp-coordinates" style="margin-top: 0.5rem; padding: 0.5rem; background: #f8fafc; border-radius: 4px; font-family: monospace; font-size: 0.85rem; color: #374151;">
                    Koordinat akan ditampilkan di sini setelah memilih lokasi
                </div>
            </div>
            <div id="map-picker" style="height: 400px; background: #e2e8f0; border-radius: 8px; overflow: hidden;">
                <!-- Leaflet map will be initialized here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="confirmLocationSelection()">
                <i class="fas fa-map-pin"></i> Gunakan Lokasi Ini
            </button>
            <button type="button" class="btn btn-secondary modal-close">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>