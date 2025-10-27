// Program list handling
const MonitoringProgram = {
    // Initialize program list
    init() {
        this.loadPrograms();
        this.initHandlers();
    },

    // Load programs data
    loadPrograms() {
        const filters = MonitoringData.getFilters();
        $.get(`${baseUrl}/monitoring/getProgramData`, filters)
            .done((response) => {
                if (response.features) {
                    this.renderProgramList(response.features);
                }
            })
            .fail(() => {
                toastr.error('Gagal memuat daftar program');
            });
    },

    // Render program list
    renderProgramList(features) {
        const tbody = $('#program-table tbody');
        tbody.empty();

        features.forEach(feature => {
            const prog = feature.properties;
            const row = `
                <tr>
                    <td>${prog.nama}</td>
                    <td>${prog.sektor}</td>
                    <td>${prog.lokasi || '-'}</td>
                    <td>Rp ${parseInt(prog.anggaran_total).toLocaleString()}</td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar ${this.getProgressColorClass(prog.progress)}" 
                                role="progressbar" style="width: ${prog.progress}%">
                            </div>
                        </div>
                        <small class="d-block mt-1">${prog.progress}%</small>
                    </td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: ${(prog.anggaran_realisasi / prog.anggaran_total * 100)}%">
                            </div>
                        </div>
                        <small class="d-block mt-1">
                            Rp ${parseInt(prog.anggaran_realisasi).toLocaleString()}
                        </small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary update-progress" 
                                data-program-id="${prog.id}" 
                                data-program-data='${JSON.stringify(prog)}'>
                            <i class="fas fa-edit"></i> Update Progress
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    },

    // Get progress color class
    getProgressColorClass(progress) {
        if (progress >= 80) return 'bg-success';
        if (progress >= 50) return 'bg-warning';
        return 'bg-danger';
    },

    // Initialize event handlers
    initHandlers() {
        // Handle update progress button click
        $('#program-table').on('click', '.update-progress', function() {
            const programId = $(this).data('program-id');
            const programData = $(this).data('program-data');
            MonitoringForms.showUpdateProgress(programId, programData);
        });
    }
};