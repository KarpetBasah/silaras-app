// Form handling and data updates
const MonitoringForms = {
    modal: null,

    // Initialize modal
    init() {
        this.modal = new bootstrap.Modal(document.getElementById('updateProgressModal'), {
            keyboard: true,
            backdrop: true
        });
        this.initFormHandlers();
    },

    // Show progress update modal
    showUpdateProgress(programId, programData) {
        // Reset form dan hapus data lama
        this.resetForm();
        
        // Set program data
        $('#progressForm')
            .data('programId', programId)
            .data('anggaran_total', programData.anggaran_total);
            
        // Update program info
        $('.program-name').text(programData.nama);
        $('.program-sektor').text(programData.sektor);
        $('.program-lokasi').text(programData.lokasi || '-');
        $('.program-anggaran').text(`Rp ${parseInt(programData.anggaran_total).toLocaleString()}`);

        // Set current values if any
        if (programData.progress_fisik) {
            $('input[name="progress_fisik"]').val(programData.progress_fisik).trigger('input');
        }
        if (programData.anggaran_realisasi) {
            $('input[name="anggaran_realisasi"]').val(programData.anggaran_realisasi).trigger('input');
        }
        
        // Tampilkan modal
        this.modal.show();
    },

    // Initialize form handlers
    initFormHandlers() {
        // Handle form submission
        $('#progressForm').on('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        // Handle modal close
        $('#updateProgressModal').on('hidden.bs.modal', () => {
            this.resetForm();
        });

        // Handle progress input change
        $('input[name="progress_fisik"]').on('input', (e) => {
            const value = e.target.value;
            $('.progress-bar').css('width', `${value}%`);
            
            // Update progress bar color
            if (value >= 80) {
                $('.progress-bar').removeClass('bg-warning bg-danger').addClass('bg-success');
            } else if (value >= 50) {
                $('.progress-bar').removeClass('bg-success bg-danger').addClass('bg-warning');
            } else {
                $('.progress-bar').removeClass('bg-success bg-warning').addClass('bg-danger');
            }
        });

        // Handle realisasi anggaran input
        $('input[name="anggaran_realisasi"]').on('input', (e) => {
            const realisasi = parseFloat(e.target.value) || 0;
            const total = parseFloat($('#progressForm').data('anggaran_total')) || 0;
            
            if (total > 0) {
                const percentage = (realisasi / total) * 100;
                $('.realisasi-progress').css('width', `${percentage}%`);
                $('.realisasi-info').text(`${percentage.toFixed(2)}% dari total anggaran`);
            }
        });

        // Initialize file preview
        this.initFilePreview();
        
        // Initialize filters
        this.initFilters();
    },

    // Submit form
    submitForm() {
        const programId = $('#progressForm').data('programId');
        const formData = new FormData($('#progressForm')[0]);

        $.ajax({
            url: `${baseUrl}/monitoring/updateProgress/${programId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    this.modal.hide();
                    this.resetForm();
                    MonitoringData.refreshData();
                    toastr.success('Progress berhasil diperbarui');
                } else {
                    toastr.error('Error: ' + response.message);
                }
            },
            error: () => {
                toastr.error('Terjadi kesalahan saat memperbarui progress');
            }
        });
    },
    
    // Initialize progress update form
    initProgressForm() {
        $('#progressForm').on('submit', function(e) {
            e.preventDefault();
            const programId = $(this).data('programId');
            const formData = new FormData(this);

            $.ajax({
                url: `${baseUrl}/monitoring/updateProgress/${programId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#updateProgressModal').modal('hide');
                        MonitoringData.refreshData();
                        toastr.success('Progress berhasil diperbarui');
                    } else {
                        toastr.error('Error: ' + response.message);
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat memperbarui progress');
                }
            });
        });
    },
    
    // Initialize file preview
    initFilePreview() {
        $('input[name="foto[]"]').on('change', function(e) {
            const files = e.target.files;
            const container = $('.photo-preview');
            container.empty();

            // Check file count
            if (files.length > 5) {
                toastr.warning('Maksimal 5 foto yang dapat diunggah');
                $(this).val('');
                return;
            }

            // Check file sizes
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > 2 * 1024 * 1024) {
                    toastr.warning(`File ${files[i].name} melebihi 2MB`);
                    $(this).val('');
                    container.empty();
                    return;
                }
            }

            // Preview images
            Array.from(files).forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    container.append(`
                        <div class="col-4">
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-thumbnail w-100" 
                                     style="height: 120px; object-fit: cover;">
                                <div class="photo-info small text-muted mt-1">
                                    ${file.name.substring(0, 15)}...
                                    <br>${(file.size / 1024 / 1024).toFixed(1)} MB
                                </div>
                            </div>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            });
        });
    },

    // Reset form
    resetForm() {
        $('#progressForm')[0].reset();
        $('.photo-preview').empty();
        $('.progress-bar').css('width', '0%').removeClass('bg-success bg-warning bg-danger');
        $('.realisasi-progress').css('width', '0%');
        $('.realisasi-info').text('');
    }
};