<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramDokumenModel extends Model
{
    protected $table            = 'program_dokumen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_id', 'jenis_dokumen', 'nama_dokumen', 'nama_file', 
        'path_file', 'ukuran_file', 'mime_type', 'deskripsi', 
        'tanggal_upload', 'uploaded_by', 'is_public'
    ];

    protected bool $allowEmptyInserts = false;   
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'program_id' => 'int',
        'ukuran_file' => 'int',
        'uploaded_by' => 'int',
        'is_public' => 'boolean',
        'tanggal_upload' => 'datetime'
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
        'jenis_dokumen' => 'required|in_list[rab,ded,foto_lokasi,foto_progress,kontrak,addendum,ba_serah_terima,lainnya]',
        'nama_dokumen' => 'required|max_length[255]',
        'nama_file' => 'required|max_length[255]',
        'path_file' => 'required|max_length[500]',
        'ukuran_file' => 'required|is_natural_no_zero',
        'mime_type' => 'required|max_length[100]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setUploadDate'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set upload date before insert
     */
    protected function setUploadDate(array $data)
    {
        if (!isset($data['data']['tanggal_upload'])) {
            $data['data']['tanggal_upload'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get documents by program ID
     */
    public function getByProgramId($programId)
    {
        return $this->where('program_id', $programId)->findAll();
    }

    /**
     * Get documents by jenis dokumen
     */
    public function getByJenis($programId, $jenis)
    {
        return $this->where('program_id', $programId)
                    ->where('jenis_dokumen', $jenis)
                    ->findAll();
    }

    /**
     * Get public documents
     */
    public function getPublicDocuments($programId)
    {
        return $this->where('program_id', $programId)
                    ->where('is_public', 1)
                    ->findAll();
    }

    /**
     * Delete file and record
     */
    public function deleteWithFile($id)
    {
        $dokumen = $this->find($id);
        if ($dokumen) {
            // Delete physical file
            $fullPath = FCPATH . $dokumen['path_file'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            // Delete database record
            return $this->delete($id);
        }
        return false;
    }
}