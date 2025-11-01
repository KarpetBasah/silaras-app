<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramMonitoringModel extends Model
{
    protected $table            = 'program_monitoring';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_id', 'tanggal_monitoring', 'progress_fisik', 'progress_keuangan',
        'anggaran_realisasi', 'kendala', 'solusi', 'rekomendasi', 'status_lapangan',
        'cuaca', 'jumlah_pekerja', 'foto_progress', 'koordinat_lat', 'koordinat_lng',
        'validator_name', 'validator_jabatan', 'is_verified', 'verified_by', 
        'verified_at', 'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'progress_fisik' => 'float',
        'progress_keuangan' => 'float',
        'anggaran_realisasi' => 'int',
        'koordinat_lat' => 'float',
        'koordinat_lng' => 'float',
        'jumlah_pekerja' => 'int',
        'is_verified' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'program_id' => 'required|is_natural_no_zero',
        'tanggal_monitoring' => 'required|valid_date',
        'progress_fisik' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'progress_keuangan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'anggaran_realisasi' => 'required|numeric|greater_than_equal_to[0]',
        'validator_name' => 'required|max_length[255]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get latest monitoring data for each program
     */
    public function getLatestMonitoringByProgram($filters = [])
    {
        $builder = $this->select('program_monitoring.*, 
                                 program.nama_kegiatan, program.kode_program, program.anggaran_total,
                                 program.koordinat_lat as program_lat, program.koordinat_lng as program_lng,
                                 program.tahun_pelaksanaan,
                                 opd.nama_singkat as opd_nama, sektor.nama_sektor, sektor.color as sektor_color,
                                 sektor.icon as sektor_icon')
                       ->join('program', 'program.id = program_monitoring.program_id')
                       ->join('opd', 'opd.id = program.opd_id')
                       ->join('sektor', 'sektor.id = program.sektor_id')
                       ->join('(SELECT program_id, MAX(tanggal_monitoring) as latest_date 
                                FROM program_monitoring 
                                GROUP BY program_id) latest', 
                               'latest.program_id = program_monitoring.program_id AND 
                                latest.latest_date = program_monitoring.tanggal_monitoring', 'inner');

        // Apply filters
        if (!empty($filters['sektor_id'])) {
            $builder->where('program.sektor_id', $filters['sektor_id']);
        }
        if (!empty($filters['opd_id'])) {
            $builder->where('program.opd_id', $filters['opd_id']);
        }
        if (!empty($filters['tahun'])) {
            $builder->where('program.tahun_pelaksanaan', $filters['tahun']);
        }
        if (!empty($filters['status_lapangan'])) {
            $builder->where('program_monitoring.status_lapangan', $filters['status_lapangan']);
        }

        return $builder->orderBy('program_monitoring.tanggal_monitoring', 'DESC')
                       ->findAll();
    }

    /**
     * Get monitoring history for a specific program
     */
    public function getMonitoringHistory($programId)
    {
        return $this->where('program_id', $programId)
                   ->orderBy('tanggal_monitoring', 'DESC')
                   ->findAll();
    }

    /**
     * Get monitoring data with program details
     */
    public function getMonitoringWithProgram($filters = [])
    {
        $builder = $this->select('program_monitoring.*, 
                                 program.nama_kegiatan, program.kode_program, program.anggaran_total,
                                 program.koordinat_lat as program_lat, program.koordinat_lng as program_lng,
                                 program.status as program_status, program.tahun_pelaksanaan,
                                 opd.nama_opd, opd.nama_singkat as opd_nama, 
                                 sektor.nama_sektor, sektor.color as sektor_color, sektor.icon as sektor_icon')
                       ->join('program', 'program.id = program_monitoring.program_id')
                       ->join('opd', 'opd.id = program.opd_id')
                       ->join('sektor', 'sektor.id = program.sektor_id');

        // Apply filters
        if (!empty($filters['program_id'])) {
            $builder->where('program_monitoring.program_id', $filters['program_id']);
        }
        if (!empty($filters['sektor_id'])) {
            $builder->where('program.sektor_id', $filters['sektor_id']);
        }
        if (!empty($filters['opd_id'])) {
            $builder->where('program.opd_id', $filters['opd_id']);
        }
        if (!empty($filters['tahun'])) {
            $builder->where('program.tahun_pelaksanaan', $filters['tahun']);
        }
        if (!empty($filters['status_lapangan'])) {
            $builder->where('program_monitoring.status_lapangan', $filters['status_lapangan']);
        }

        return $builder->orderBy('program_monitoring.tanggal_monitoring', 'DESC')
                       ->findAll();
    }

    /**
     * Get statistics for dashboard
     */
    public function getMonitoringStatistics($filters = [])
    {
        // Get basic program statistics (all programs)
        $programModel = new \App\Models\ProgramModel();
        $programBuilder = $programModel->select('
                        COUNT(program.id) as total_program_aktif,
                        SUM(program.anggaran_total) as total_anggaran
                    ');

        // Apply filters to program query
        if (!empty($filters['tahun'])) {
            $programBuilder->where('program.tahun_pelaksanaan', $filters['tahun']);
        }

        $programStats = $programBuilder->get()->getRowArray();

        // Get monitoring statistics (only for programs with monitoring data)
        $monitoringBuilder = $this->select('
                        COUNT(DISTINCT program_monitoring.program_id) as total_program_dengan_monitoring,
                        AVG(program_monitoring.progress_fisik) as rata_progress_fisik,
                        AVG(program_monitoring.progress_keuangan) as rata_progress_keuangan,
                        SUM(program_monitoring.anggaran_realisasi) as total_realisasi
                    ')
                   ->join('program', 'program.id = program_monitoring.program_id')
                   ->join('(SELECT program_id, MAX(tanggal_monitoring) as latest_date 
                            FROM program_monitoring 
                            GROUP BY program_id) latest', 
                           'latest.program_id = program_monitoring.program_id AND 
                            latest.latest_date = program_monitoring.tanggal_monitoring', 'inner');

        // Apply filters to monitoring query
        if (!empty($filters['tahun'])) {
            $monitoringBuilder->where('program.tahun_pelaksanaan', $filters['tahun']);
        }

        $monitoringStats = $monitoringBuilder->get()->getRowArray();

        // Combine results
        return [
            'total_program_aktif' => $programStats['total_program_aktif'] ?? 0,
            'total_program_dengan_monitoring' => $monitoringStats['total_program_dengan_monitoring'] ?? 0,
            'rata_progress_fisik' => $monitoringStats['rata_progress_fisik'] ?? 0,
            'rata_progress_keuangan' => $monitoringStats['rata_progress_keuangan'] ?? 0,
            'total_realisasi' => $monitoringStats['total_realisasi'] ?? 0,
            'total_anggaran' => $programStats['total_anggaran'] ?? 0
        ];
    }

    /**
     * Get statistics by sector
     */
    public function getStatisticsBySektor($filters = [])
    {
        // Get all sectors with program counts (including those without monitoring)
        $programModel = new \App\Models\ProgramModel();
        
        $allSektorsBuilder = $programModel->select('
                        sektor.id as sektor_id, sektor.nama_sektor, sektor.color as sektor_color, sektor.icon as sektor_icon,
                        COUNT(program.id) as total_program,
                        SUM(program.anggaran_total) as total_anggaran
                    ')
                   ->join('sektor', 'sektor.id = program.sektor_id')
                   ->groupBy('sektor.id, sektor.nama_sektor, sektor.color, sektor.icon');

        // Apply filters to program query
        if (!empty($filters['tahun'])) {
            $allSektorsBuilder->where('program.tahun_pelaksanaan', $filters['tahun']);
        }

        $allSektors = $allSektorsBuilder->findAll();

        // Get monitoring statistics by sector
        $monitoringBuilder = $this->select('
                        sektor.id as sektor_id,
                        COUNT(DISTINCT program_monitoring.program_id) as total_program_dengan_monitoring,
                        AVG(program_monitoring.progress_fisik) as rata_progress_fisik,
                        AVG(program_monitoring.progress_keuangan) as rata_progress_keuangan,
                        SUM(program_monitoring.anggaran_realisasi) as total_realisasi
                    ')
                   ->join('program', 'program.id = program_monitoring.program_id')
                   ->join('sektor', 'sektor.id = program.sektor_id')
                   ->join('(SELECT program_id, MAX(tanggal_monitoring) as latest_date 
                            FROM program_monitoring 
                            GROUP BY program_id) latest', 
                           'latest.program_id = program_monitoring.program_id AND 
                            latest.latest_date = program_monitoring.tanggal_monitoring', 'inner')
                   ->groupBy('sektor.id');

        // Apply filters to monitoring query
        if (!empty($filters['tahun'])) {
            $monitoringBuilder->where('program.tahun_pelaksanaan', $filters['tahun']);
        }

        $monitoringStats = $monitoringBuilder->findAll();

        // Create a mapping of monitoring stats by sektor_id
        $monitoringMap = [];
        foreach ($monitoringStats as $stat) {
            $monitoringMap[$stat['sektor_id']] = $stat;
        }

        // Combine all sector data with monitoring stats
        $result = [];
        foreach ($allSektors as $sektor) {
            $monitoringData = $monitoringMap[$sektor['sektor_id']] ?? null;
            
            $result[] = [
                'nama_sektor' => $sektor['nama_sektor'],
                'sektor_color' => $sektor['sektor_color'],
                'sektor_icon' => $sektor['sektor_icon'],
                'total_program' => $sektor['total_program'],
                'total_program_dengan_monitoring' => $monitoringData['total_program_dengan_monitoring'] ?? 0,
                'rata_progress_fisik' => $monitoringData['rata_progress_fisik'] ?? 0,
                'rata_progress_keuangan' => $monitoringData['rata_progress_keuangan'] ?? 0,
                'total_realisasi' => $monitoringData['total_realisasi'] ?? 0,
                'total_anggaran' => $sektor['total_anggaran']
            ];
        }

        return $result;
    }

    /**
     * Get programs with low progress for alerts
     */
    public function getProgramsWithLowProgress($threshold = 50)
    {
        return $this->select('program_monitoring.*, 
                             program.nama_kegiatan, program.kode_program,
                             opd.nama_singkat as opd_nama, sektor.nama_sektor')
                   ->join('program', 'program.id = program_monitoring.program_id')
                   ->join('opd', 'opd.id = program.opd_id')
                   ->join('sektor', 'sektor.id = program.sektor_id')
                   ->join('(SELECT program_id, MAX(tanggal_monitoring) as latest_date 
                            FROM program_monitoring 
                            GROUP BY program_id) latest', 
                           'latest.program_id = program_monitoring.program_id AND 
                            latest.latest_date = program_monitoring.tanggal_monitoring', 'inner')
                   ->where('program_monitoring.progress_fisik <', $threshold)
                   ->orderBy('program_monitoring.progress_fisik', 'ASC')
                   ->findAll();
    }
}