<?php

namespace App\Models;

use CodeIgniter\Model;

class RpjmdSasaranModel extends Model
{
    protected $table            = 'rpjmd_sasaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_sasaran', 'nama_sasaran', 'deskripsi', 'prioritas', 
        'target_indikator', 'periode_awal', 'periode_akhir', 
        'kawasan_strategis', 'anggaran_target', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'periode_awal' => 'int',
        'periode_akhir' => 'int',
        'anggaran_target' => 'int',
        'is_active' => 'boolean'
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
        'kode_sasaran' => 'required|is_unique[rpjmd_sasaran.kode_sasaran,id,{id}]|max_length[20]',
        'nama_sasaran' => 'required|max_length[255]',
        'prioritas' => 'required|in_list[tinggi,sedang,rendah]',
        'periode_awal' => 'required|integer|greater_than[2020]',
        'periode_akhir' => 'required|integer|greater_than_equal_to[{periode_awal}]'
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
     * Get active RPJMD sasaran only
     */
    public function getActive()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get RPJMD sasaran by kode
     */
    public function getByKode($kode)
    {
        return $this->where('kode_sasaran', $kode)->first();
    }

    /**
     * Get RPJMD sasaran by periode
     */
    public function getByPeriode($tahun)
    {
        return $this->where('periode_awal <=', $tahun)
                    ->where('periode_akhir >=', $tahun)
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Get RPJMD sasaran with program count
     */
    public function getWithProgramCount()
    {
        return $this->select('rpjmd_sasaran.*, COUNT(program.id) as program_count')
                    ->join('program', 'program.rpjmd_sasaran_id = rpjmd_sasaran.id', 'left')
                    ->where('rpjmd_sasaran.is_active', 1)
                    ->groupBy('rpjmd_sasaran.id')
                    ->findAll();
    }
}