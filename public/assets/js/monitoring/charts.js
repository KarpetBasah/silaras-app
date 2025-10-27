// Chart handling functions
const MonitoringCharts = {
    progressChart: null,
    realisasiChart: null,
    wilayahChart: null,
    
    // Initialize progress chart
    initProgressChart(data) {
        const ctx = document.getElementById('progress-chart').getContext('2d');
        
        if (this.progressChart) {
            this.progressChart.destroy();
        }
        
        this.progressChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.nama_sektor),
                datasets: [{
                    label: 'Progress Fisik (%)',
                    data: data.map(item => item.rata_progress),
                    backgroundColor: this.getProgressColors(data.map(item => item.rata_progress))
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Progress (%)'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Progress Fisik per Sektor'
                    }
                }
            }
        });
    },
    
    // Initialize realisasi chart
    initRealisasiChart(data) {
        const ctx = document.getElementById('realisasi-chart').getContext('2d');
        
        if (this.realisasiChart) {
            this.realisasiChart.destroy();
        }
        
        this.realisasiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.nama_sektor),
                datasets: [{
                    label: 'Total Anggaran',
                    data: data.map(item => item.total_anggaran),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Realisasi',
                    data: data.map(item => item.total_realisasi),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Anggaran (Rp)'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Realisasi Anggaran per Sektor'
                    }
                }
            }
        });
    },
    
    // Get colors based on progress values
    getProgressColors(progressValues) {
        return progressValues.map(value => {
            if (value >= 80) return 'rgba(40, 167, 69, 0.5)';  // green
            if (value >= 50) return 'rgba(255, 193, 7, 0.5)';  // yellow
            return 'rgba(220, 53, 69, 0.5)';  // red
        });
    },
    
    // Initialize wilayah chart
    initWilayahChart(data) {
        const ctx = document.getElementById('wilayah-chart').getContext('2d');
        
        if (this.wilayahChart) {
            this.wilayahChart.destroy();
        }
        
        this.wilayahChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.wilayah),
                datasets: [{
                    label: 'Progress Fisik (%)',
                    data: data.map(item => item.rata_progress),
                    backgroundColor: data.map(item => {
                        if (item.rata_progress >= 80) return 'rgba(40, 167, 69, 0.7)';  // hijau
                        if (item.rata_progress >= 50) return 'rgba(255, 193, 7, 0.7)';  // kuning
                        return 'rgba(220, 53, 69, 0.7)';  // merah
                    }),
                    borderColor: data.map(item => {
                        if (item.rata_progress >= 80) return 'rgb(40, 167, 69)';
                        if (item.rata_progress >= 50) return 'rgb(255, 193, 7)';
                        return 'rgb(220, 53, 69)';
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Progress (%)'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Progress per Wilayah'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Progress: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });
    },

    // Update all charts with new data
    updateCharts(progressData, realisasiData, wilayahData) {
        this.initProgressChart(progressData);
        this.initRealisasiChart(realisasiData);
        this.initWilayahChart(wilayahData);
    }
};