<div class="modal fade" id="updateProgressModal" tabindex="-1" aria-labelledby="updateProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProgressModalLabel">Update Progress Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="progressForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Informasi Program -->
                    <div class="program-info mb-3 p-3 bg-light rounded">
                        <h6 class="program-name mb-2"></h6>
                        <div class="program-details small">
                            <div><strong>Sektor:</strong> <span class="program-sektor"></span></div>
                            <div><strong>Lokasi:</strong> <span class="program-lokasi"></span></div>
                            <div><strong>Anggaran Total:</strong> <span class="program-anggaran"></span></div>
                        </div>
                    </div>

                    <!-- Progress Fisik -->
                    <div class="form-group mb-3">
                        <label class="form-label">Progress Fisik (%)</label>
                        <div class="input-group">
                            <input type="number" name="progress_fisik" class="form-control" 
                                   required min="0" max="100" step="0.01">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Realisasi Anggaran -->
                    <div class="form-group mb-3">
                        <label class="form-label">Realisasi Anggaran</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="anggaran_realisasi" class="form-control" 
                                   required min="0">
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-success realisasi-progress" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="form-text text-muted realisasi-info"></small>
                    </div>

                    <!-- Foto Dokumentasi -->
                    <div class="form-group mb-3">
                        <label class="form-label">Foto Dokumentasi (Opsional)</label>
                        <input type="file" class="form-control" name="foto[]" 
                               accept="image/*" multiple>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> 
                            Upload maksimal 5 foto (max 2MB per file)
                        </div>
                        <div class="photo-preview mt-2 row g-2"></div>
                    </div>

                    <!-- Keterangan -->
                    <div class="form-group">
                        <label class="form-label">Keterangan Progress</label>
                        <textarea name="keterangan" class="form-control" rows="3" 
                                  placeholder="Tambahkan keterangan tentang progress yang dicapai..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveProgress">
                        <i class="fas fa-save"></i> Simpan Progress
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>