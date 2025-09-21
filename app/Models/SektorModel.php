<?php

namespace App\Models;

use CodeIgniter\Model;

class SektorModel extends Model
{
    protected $table            = 'sektor';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_sektor', 'nama_sektor', 'deskripsi', 'icon', 'color', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
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
        'kode_sektor' => 'required|is_unique[sektor.kode_sektor,id,{id}]|max_length[20]',
        'nama_sektor' => 'required|max_length[255]',
        'icon' => 'permit_empty|max_length[50]',
        'color' => 'permit_empty|max_length[7]'
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
     * Get active sectors only
     */
    public function getActive()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get sector by kode
     */
    public function getByKode($kode)
    {
        return $this->where('kode_sektor', $kode)->first();
    }

    /**
     * Get sectors with program count
     */
    public function getWithProgramCount()
    {
        return $this->select('sektor.*, COUNT(program.id) as program_count')
                    ->join('program', 'program.sektor_id = sektor.id', 'left')
                    ->where('sektor.is_active', 1)
                    ->groupBy('sektor.id')
                    ->findAll();
    }
}
