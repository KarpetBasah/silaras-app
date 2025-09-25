<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table            = 'program';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_program', 'nama_kegiatan', 'deskripsi', 'opd_id', 'sektor_id', 
        'rpjmd_sasaran_id', 'lokasi_nama', 'lokasi_alamat', 'koordinat_lat', 
        'koordinat_lng', 'tahun_pelaksanaan', 'anggaran_total', 'anggaran_realisasi',
        'status', 'progress_fisik', 'tanggal_mulai', 'tanggal_selesai_rencana',
        'tanggal_selesai_aktual', 'kontraktor', 'konsultan', 'sumber_dana',
        'catatan', 'is_prioritas', 'created_by', 'updated_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'koordinat_lat' => 'float',
        'koordinat_lng' => 'float',
        'anggaran_total' => 'int',
        'anggaran_realisasi' => 'int',
        'progress_fisik' => 'float',
        'is_prioritas' => 'boolean'
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
        'kode_program' => 'required|is_unique[program.kode_program,id,{id}]|max_length[50]',
        'nama_kegiatan' => 'required|max_length[255]',
        'opd_id' => 'required|is_natural_no_zero',
        'sektor_id' => 'required|is_natural_no_zero',
        'rpjmd_sasaran_id' => 'required|is_natural_no_zero',
        'koordinat_lat' => 'required|decimal',
        'koordinat_lng' => 'required|decimal',
        'tahun_pelaksanaan' => 'required|exact_length[4]|is_natural_no_zero',
        'anggaran_total' => 'required|is_natural_no_zero'
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
     * Get programs with related data
     */
    public function getProgramsWithRelations($filters = [])
    {
        $builder = $this->select('program.*, opd.nama_opd, opd.nama_singkat as opd_singkat, 
                                 sektor.nama_sektor, sektor.icon as sektor_icon, sektor.color as sektor_color,
                                 rpjmd_sasaran.nama_sasaran as rpjmd_nama')
                       ->join('opd', 'opd.id = program.opd_id')
                       ->join('sektor', 'sektor.id = program.sektor_id') 
                       ->join('rpjmd_sasaran', 'rpjmd_sasaran.id = program.rpjmd_sasaran_id');

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
        if (!empty($filters['status'])) {
            $builder->where('program.status', $filters['status']);
        }

        return $builder->findAll();
    }

    /**
     * Get program detail with relations
     */
    public function getProgramDetail($id)
    {
        return $this->select('program.*, opd.nama_opd, opd.nama_singkat as opd_singkat, opd.kepala_opd,
                             sektor.nama_sektor, sektor.icon as sektor_icon, sektor.color as sektor_color,
                             rpjmd_sasaran.nama_sasaran as rpjmd_nama, rpjmd_sasaran.deskripsi as rpjmd_deskripsi')
                   ->join('opd', 'opd.id = program.opd_id')
                   ->join('sektor', 'sektor.id = program.sektor_id')
                   ->join('rpjmd_sasaran', 'rpjmd_sasaran.id = program.rpjmd_sasaran_id')
                   ->find($id);
    }

    /**
     * Get programs by year
     */
    public function getProgramsByYear($year)
    {
        return $this->where('tahun_pelaksanaan', $year)->findAll();
    }

    /**
     * Get programs by status
     */
    public function getProgramsByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }
}
