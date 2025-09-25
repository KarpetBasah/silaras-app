<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ProgramModel;
use App\Models\ProgramDokumenModel;
use App\Models\OPDModel;
use App\Models\SektorModel;
use App\Models\RpjmdSasaranModel;

class InputProgram extends BaseController
{
    protected $programModel;
    protected $programDokumenModel;
    protected $opdModel;
    protected $sektorModel;
    protected $rpjmdSasaranModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->programModel = new ProgramModel();
        $this->programDokumenModel = new ProgramDokumenModel();
        $this->opdModel = new OPDModel();
        $this->sektorModel = new SektorModel();
        $this->rpjmdSasaranModel = new RpjmdSasaranModel();
    }

    public function index()
    {
        // Get recent programs for display
        $recentPrograms = $this->programModel->getProgramsWithRelations();
        $recentPrograms = array_slice($recentPrograms, 0, 5); // Limit to 5 recent
        
        // Get stats
        $totalPrograms = $this->programModel->countAll();
        $activePrograms = $this->programModel->where('status !=', 'selesai')->countAllResults(false);
        $completedPrograms = $this->programModel->where('status', 'selesai')->countAllResults(false);
        $totalOPD = $this->opdModel->where('is_active', 1)->countAllResults();
        
        $data = [
            'title' => 'Input Program OPD - GeoSelaras',
            'page' => 'input-program',
            'recentPrograms' => $recentPrograms,
            'stats' => [
                'total_programs' => $totalPrograms,
                'active_programs' => $activePrograms,
                'completed_programs' => $completedPrograms,
                'total_opd' => $totalOPD
            ]
        ];
        
        return view('input_program/index', $data);
    }
    
    public function create()
    {
        // Get data for dropdowns
        $data = [
            'title' => 'Input Program Baru - GeoSelaras',
            'opd_list' => $this->opdModel->getActive(),
            'sektor_list' => $this->sektorModel->getActive(),
            'rpjmd_list' => $this->rpjmdSasaranModel->getActive()
        ];
        
        return view('input_program/create', $data);
    }
    
    public function store()
    {
        // Validation rules
        $validation = $this->validate([
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'koordinat_lat' => 'required|decimal',
            'koordinat_lng' => 'required|decimal',
            'tahun_pelaksanaan' => 'required|integer|greater_than[2020]',
            'anggaran_total' => 'required|integer|greater_than[0]',
            'sektor_id' => 'required|is_natural_no_zero',
            'rpjmd_sasaran_id' => 'required|is_natural_no_zero',
            'opd_id' => 'required|is_natural_no_zero',
            'rab_file' => 'permit_empty|uploaded[rab_file]|max_size[rab_file,5120]|ext_in[rab_file,pdf,doc,docx,xls,xlsx]',
            'ded_file' => 'permit_empty|uploaded[ded_file]|max_size[ded_file,10240]|ext_in[ded_file,pdf,dwg,dxf]',
            'foto_lokasi' => 'permit_empty|uploaded[foto_lokasi]|max_size[foto_lokasi,2048]|ext_in[foto_lokasi,jpg,jpeg,png,gif]'
        ]);
        
        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Generate kode program
            $kodeProgram = $this->generateKodeProgram();
            
            // Prepare program data
            $programData = [
                'kode_program' => $kodeProgram,
                'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'opd_id' => $this->request->getPost('opd_id'),
                'sektor_id' => $this->request->getPost('sektor_id'),
                'rpjmd_sasaran_id' => $this->request->getPost('rpjmd_sasaran_id'),
                'lokasi_alamat' => $this->request->getPost('lokasi_alamat'),
                'koordinat_lat' => $this->request->getPost('koordinat_lat'),
                'koordinat_lng' => $this->request->getPost('koordinat_lng'),
                'tahun_pelaksanaan' => $this->request->getPost('tahun_pelaksanaan'),
                'anggaran_total' => $this->request->getPost('anggaran_total'),
                'status' => 'perencanaan',
                'progress_fisik' => 0.00,
                'created_by' => 1 // TODO: Get from session when auth is implemented
            ];

            // Insert program
            $programId = $this->programModel->insert($programData);
            
            if (!$programId) {
                throw new \Exception('Gagal menyimpan data program');
            }

            // Handle file uploads
            $this->handleFileUploads($programId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('/input-program')->with('success', 'Program berhasil ditambahkan!');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error storing program: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        // TODO: Implement program editing
        $data = [
            'title' => 'Edit Program - GeoSelaras',
            'program_id' => $id
        ];
        
        return view('input_program/edit', $data);
    }
    
    public function update($id)
    {
        // TODO: Implement program update logic
        return redirect()->to('/input-program')->with('success', 'Program berhasil diperbarui!');
    }
    
    public function delete($id)
    {
        // TODO: Implement program deletion
        return redirect()->to('/input-program')->with('success', 'Program berhasil dihapus!');
    }
    
    public function validateLocation()
    {
        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');
        $programId = $this->request->getPost('program_id'); // For edit mode
        
        // Check for overlapping programs within 100 meters radius
        $query = $this->programModel->select('id, nama_kegiatan, koordinat_lat, koordinat_lng')
                                   ->where('(
                                       6371 * ACOS(
                                           COS(RADIANS(' . $lat . ')) * 
                                           COS(RADIANS(koordinat_lat)) * 
                                           COS(RADIANS(koordinat_lng) - RADIANS(' . $lng . ')) +
                                           SIN(RADIANS(' . $lat . ')) * 
                                           SIN(RADIANS(koordinat_lat))
                                       )
                                   ) <= 0.1'); // 100 meters in km
        
        if ($programId) {
            $query->where('id !=', $programId);
        }
        
        $nearbyPrograms = $query->findAll();
        $hasOverlap = count($nearbyPrograms) > 0;
        
        return $this->response->setJSON([
            'valid' => !$hasOverlap,
            'message' => $hasOverlap ? 'Lokasi tumpang tindih dengan program: ' . $nearbyPrograms[0]['nama_kegiatan'] : 'Lokasi valid',
            'nearby_programs' => $nearbyPrograms
        ]);
    }

    /**
     * Generate unique program code
     */
    private function generateKodeProgram()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get last program number for this month
        $lastProgram = $this->programModel->like('kode_program', "PRG-{$year}{$month}-", 'after')
                                         ->orderBy('kode_program', 'DESC')
                                         ->first();
        
        $lastNumber = 1;
        if ($lastProgram) {
            $lastCode = $lastProgram['kode_program'];
            $lastNumber = (int)substr($lastCode, -3) + 1;
        }
        
        return sprintf("PRG-%s%s-%03d", $year, $month, $lastNumber);
    }

    /**
     * Handle file uploads for program documents
     */
    private function handleFileUploads($programId)
    {
        $uploadedFiles = [];
        
        // Handle RAB file
        $rabFile = $this->request->getFile('rab_file');
        if ($rabFile && $rabFile->isValid() && !$rabFile->hasMoved()) {
            $uploadedFiles[] = $this->saveFile($programId, $rabFile, 'rab');
        }

        // Handle DED file
        $dedFile = $this->request->getFile('ded_file');
        if ($dedFile && $dedFile->isValid() && !$dedFile->hasMoved()) {
            $uploadedFiles[] = $this->saveFile($programId, $dedFile, 'ded');
        }

        // Handle foto lokasi (multiple files)
        $fotoFiles = $this->request->getFiles();
        if (isset($fotoFiles['foto_lokasi'])) {
            foreach ($fotoFiles['foto_lokasi'] as $foto) {
                if ($foto->isValid() && !$foto->hasMoved()) {
                    $uploadedFiles[] = $this->saveFile($programId, $foto, 'foto_lokasi');
                }
            }
        }

        return $uploadedFiles;
    }

    /**
     * Save individual file and create database record
     */
    private function saveFile($programId, $file, $jenis)
    {
        // Create year/month directory structure
        $year = date('Y');
        $month = date('m');
        $uploadPath = $jenis === 'foto_lokasi' ? 'uploads/photos' : 'uploads/documents';
        $fullPath = $uploadPath . '/' . $year . '/' . $month;
        
        // Create directory if not exists
        if (!is_dir(FCPATH . $fullPath)) {
            mkdir(FCPATH . $fullPath, 0755, true);
        }

        // Generate unique filename
        $extension = $file->getClientExtension();
        $filename = $programId . '_' . $jenis . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
        
        // Move file
        $file->move(FCPATH . $fullPath, $filename);
        
        // Save to database
        $dokumenData = [
            'program_id' => $programId,
            'jenis_dokumen' => $jenis,
            'nama_dokumen' => $file->getClientName(),
            'nama_file' => $filename,
            'path_file' => $fullPath . '/' . $filename,
            'ukuran_file' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'uploaded_by' => 1, // TODO: Get from session when auth is implemented
            'is_public' => $jenis === 'foto_lokasi' ? 1 : 0
        ];

        $this->programDokumenModel->insert($dokumenData);
        
        return $dokumenData;
    }
}