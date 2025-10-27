// Data handling and API calls
const MonitoringData = {
    // Initialize data
    init() {
        this.loadInitialData();
        this.initErrorHandling();
    },

    // Initialize error handling
    initErrorHandling() {
        $(document).ajaxError((event, jqXHR, settings, error) => {
            console.error('Ajax error:', error, 'Status:', jqXHR.status);
            let message = 'Terjadi kesalahan saat memuat data';
            
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                message = jqXHR.responseJSON.message;
            }
            
            toastr.error(message);
        });
    },

    // Load initial data
    async loadInitialData() {
        try {
            console.log('Loading initial data...');
            
            // Load data sequentially to avoid race conditions
            await this.loadStatistics();
            console.log('Statistics loaded');
            
            await this.loadProgramData();
            console.log('Program data loaded');
            
            if (typeof MonitoringCharts !== 'undefined') {
                await this.loadCharts();
                console.log('Charts loaded');
            }
            
            console.log('All initial data loaded successfully');
        } catch (error) {
            console.error('Error loading initial data:', error);
            toastr.error('Gagal memuat data awal');
        }
    },
    
    // Get current filter values
    getFilters() {
        return {
            tahun: $('#filter-tahun').val() || new Date().getFullYear(),
            sektor_id: $('#filter-sektor').val()
        };
    },
    
    // Load program data and update map
    async loadProgramData() {
        const filters = this.getFilters();
        try {
            const response = await $.get(`${baseUrl}/monitoring/getProgramData`, filters);
            MonitoringMap.addMarkers(response);
            this.updateProgramList(response);
            console.log('Program data loaded successfully');
        } catch (error) {
            console.error('Error loading program data:', error);
            toastr.error('Gagal memuat data program');
        }
    },
    
    // Load statistics and update dashboard
    async loadStatistics() {
        const filters = this.getFilters();
        try {
            const response = await $.get(`${baseUrl}/monitoring/getStatistics`, filters);
            if (response.success) {
                this.updateStatistics(response.data);
                console.log('Statistics loaded successfully');
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
            toastr.error('Gagal memuat statistik');
        }
    },
    
    // Update statistics in dashboard
    updateStatistics(data) {
        // Format numbers
        const numberFormatter = new Intl.NumberFormat('id-ID');
        const currencyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        // Update values
        $('#total-program').text(numberFormatter.format(data.total_program || 0));
        $('#rata-progress').text(parseFloat(data.rata_progress || 0).toFixed(1));
        $('#total-anggaran').text(currencyFormatter.format(data.total_anggaran || 0));
        $('#total-realisasi').text(currencyFormatter.format(data.total_realisasi || 0));
        
        // Update progress bars
        $('#progress-bar').css('width', `${data.rata_progress || 0}%`);
        
        if (data.total_anggaran > 0) {
            const realisasiPercent = (data.total_realisasi / data.total_anggaran) * 100;
            $('#realisasi-bar').css('width', `${realisasiPercent}%`);
        }
    },
    
    // Update program list table
    updateProgramList(programs) {
        const tbody = $('#program-list');
        tbody.empty();
        
        if (!programs || !programs.length) {
            tbody.append('<tr><td colspan="7" class="text-center">Tidak ada data program</td></tr>');
            return;
        }
        
        programs.forEach(program => {
            const tr = $('<tr></tr>');
            tr.append(`<td>${program.nama}</td>`);
            tr.append(`<td>${program.sektor}</td>`);
            tr.append(`<td>${program.lokasi || '-'}</td>`);
            tr.append(`<td class="text-right">${this.formatCurrency(program.anggaran_total)}</td>`);
            tr.append(`<td class="text-center">
                <div class="progress">
                    <div class="progress-bar ${this.getProgressClass(program.progress)}" 
                         style="width: ${program.progress}%">
                        ${program.progress}%
                    </div>
                </div>
            </td>`);
            tr.append(`<td class="text-right">${this.formatCurrency(program.realisasi)}</td>`);
            tr.append(`<td class="text-center">
                <button class="btn btn-sm btn-primary update-progress" 
                        data-id="${program.id}"
                        title="Update Progress">
                    <i class="fas fa-edit"></i>
                </button>
            </td>`);
            tbody.append(tr);
        });

        // Initialize update buttons
        $('.update-progress').on('click', function() {
            const programId = $(this).data('id');
            const program = programs.find(p => p.id === programId);
            if (program) {
                MonitoringForms.showUpdateProgress(programId, program);
            }
        });
    },
    
    // Format currency
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value || 0);
    },
    
    // Get progress bar class based on value
    getProgressClass(progress) {
        if (progress >= 80) return 'bg-success';
        if (progress >= 50) return 'bg-warning';
        return 'bg-danger';
    }
};
        $('#rata-progress').text(`${Math.round(data.rata_progress)}%`);
        $('#total-anggaran').text(`Rp ${parseInt(data.total_anggaran).toLocaleString()}`);
        $('#total-realisasi').text(`Rp ${parseInt(data.total_realisasi).toLocaleString()}`);
    },
    
    // Refresh all data
    refreshData() {
        this.loadProgramData();
        this.loadStatistics();
        this.loadCharts();
    },
    
    // Initialize monitoring dashboard
    init() {
        MonitoringMap.init();
        MonitoringForms.init();
        this.refreshData();
        
        // Set up auto refresh every 5 minutes
        setInterval(() => this.refreshData(), 5 * 60 * 1000);
    }
};