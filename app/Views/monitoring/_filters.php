<div class="row mb-4">
    <div class="col-md-6">
        <div class="card filter-controls">
            <div class="card-body">
                <div class="form-group">
                    <label for="filter-tahun">Tahun:</label>
                    <select id="filter-tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($tahun_list as $tahun): ?>
                            <option value="<?= $tahun ?>"><?= $tahun ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter-sektor">Sektor:</label>
                    <select id="filter-sektor" class="form-control">
                        <option value="">Semua Sektor</option>
                        <?php foreach ($sektor_list as $sektor): ?>
                            <option value="<?= $sektor['id'] ?>"><?= $sektor['nama_sektor'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>