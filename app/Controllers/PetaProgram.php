<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ProgramModel;
use App\Models\OPDModel;
use App\Models\SektorModel;
use App\Models\RpjmdSasaranModel;

class PetaProgram extends BaseController
{
    protected $programModel;
    protected $opdModel;
    protected $sektorModel;
    protected $rpjmdSasaranModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->programModel = new ProgramModel();
        $this->opdModel = new OPDModel();
        $this->sektorModel = new SektorModel();
        $this->rpjmdSasaranModel = new RpjmdSasaranModel();
    }

    public function index()
    {
        // Get filter options
        $data = [
            'title' => 'Peta Program - GeoSelaras',
            'page' => 'peta-program',
            'opd_list' => $this->opdModel->getActive(),
            'sektor_list' => $this->sektorModel->getActive(),
            'tahun_list' => $this->getTahunList()
        ];
        
        return view('peta_program/index', $data);
    }

    /**
     * Get available years from programs
     */
    private function getTahunList()
    {
        $tahunData = $this->programModel->select('tahun_pelaksanaan')
                                        ->distinct()
                                        ->orderBy('tahun_pelaksanaan', 'DESC')
                                        ->findAll();
        
        $tahunList = [];
        foreach ($tahunData as $row) {
            $tahunList[] = $row['tahun_pelaksanaan'];
        }
        
        return $tahunList;
    }
    
    public function getProgramData()
    {
        // Get filter parameters
        $filters = [
            'sektor_id' => $this->request->getGet('sektor_id'),
            'opd_id' => $this->request->getGet('opd_id'),
            'tahun' => $this->request->getGet('tahun'),
            'status' => $this->request->getGet('status')
        ];

        // Get programs with relations
        $programs = $this->programModel->getProgramsWithRelations($filters);
        
        // Format data for map display
        $mapData = [];
        foreach ($programs as $program) {
            $mapData[] = [
                'id' => $program['id'],
                'nama_kegiatan' => $program['nama_kegiatan'],
                'deskripsi' => $program['deskripsi'],
                'lat' => (float)$program['koordinat_lat'],
                'lng' => (float)$program['koordinat_lng'],
                'lokasi_alamat' => $program['lokasi_alamat'],
                'sektor' => [
                    'id' => $program['sektor_id'],
                    'nama' => $program['nama_sektor'],
                    'icon' => $program['sektor_icon'],
                    'color' => $program['sektor_color']
                ],
                'opd' => [
                    'id' => $program['opd_id'],
                    'nama' => $program['nama_opd'],
                    'singkat' => $program['opd_singkat']
                ],
                'rpjmd' => [
                    'id' => $program['rpjmd_sasaran_id'],
                    'nama' => $program['rpjmd_nama']
                ],
                'status' => $program['status'],
                'progress_fisik' => (float)$program['progress_fisik'],
                'anggaran_total' => (int)$program['anggaran_total'],
                'anggaran_realisasi' => (int)$program['anggaran_realisasi'],
                'tahun_pelaksanaan' => $program['tahun_pelaksanaan'],
                'tanggal_mulai' => $program['tanggal_mulai'],
                'tanggal_selesai_rencana' => $program['tanggal_selesai_rencana'],
                'is_prioritas' => (bool)$program['is_prioritas']
            ];
        }

        // Calculate statistics
        $stats = $this->calculateStats($programs);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $mapData,
            'stats' => $stats,
            'total' => count($mapData)
        ]);
    }
    
    public function getProgramDetail($id)
    {
        try {
            $program = $this->programModel->getProgramDetail($id);
            
            if (!$program) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Program tidak ditemukan'
                ]);
            }

            // Get program documents
            $programDokumenModel = new \App\Models\ProgramDokumenModel();
            $documents = $programDokumenModel->getByProgramId($id);

            $data = [
                'id' => $program['id'],
                'kode_program' => $program['kode_program'],
                'nama_kegiatan' => $program['nama_kegiatan'],
                'deskripsi' => $program['deskripsi'],
                'lokasi_alamat' => $program['lokasi_alamat'],
                'koordinat' => [
                    'lat' => (float)$program['koordinat_lat'],
                    'lng' => (float)$program['koordinat_lng']
                ],
                'sektor' => [
                    'nama' => $program['nama_sektor'],
                    'icon' => $program['sektor_icon'],
                    'color' => $program['sektor_color']
                ],
                'opd' => [
                    'nama' => $program['nama_opd'],
                    'kepala' => $program['kepala_opd']
                ],
                'rpjmd' => [
                    'nama' => $program['rpjmd_nama'],
                    'deskripsi' => $program['rpjmd_deskripsi']
                ],
                'status' => $program['status'],
                'progress_fisik' => (float)$program['progress_fisik'],
                'anggaran_total' => (int)$program['anggaran_total'],
                'anggaran_realisasi' => (int)$program['anggaran_realisasi'],
                'tahun_pelaksanaan' => $program['tahun_pelaksanaan'],
                'tanggal_mulai' => $program['tanggal_mulai'],
                'tanggal_selesai_rencana' => $program['tanggal_selesai_rencana'],
                'tanggal_selesai_aktual' => $program['tanggal_selesai_aktual'],
                'kontraktor' => $program['kontraktor'],
                'konsultan' => $program['konsultan'],
                'sumber_dana' => $program['sumber_dana'],
                'catatan' => $program['catatan'],
                'is_prioritas' => (bool)$program['is_prioritas'],
                'documents' => $documents
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting program detail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail program'
            ]);
        }
    }

    /**
     * Calculate statistics from program data
     */
    private function calculateStats($programs)
    {
        $stats = [
            'total' => count($programs),
            'perencanaan' => 0,
            'persiapan' => 0,
            'lelang' => 0,
            'pelaksanaan' => 0,
            'monitoring' => 0,
            'selesai' => 0,
            'batal' => 0,
            'total_anggaran' => 0,
            'realisasi_anggaran' => 0,
            'progress_rata' => 0
        ];

        $totalProgress = 0;
        foreach ($programs as $program) {
            // Count by status
            if (isset($stats[$program['status']])) {
                $stats[$program['status']]++;
            }
            
            // Sum budget
            $stats['total_anggaran'] += (int)$program['anggaran_total'];
            $stats['realisasi_anggaran'] += (int)$program['anggaran_realisasi'];
            
            // Sum progress
            $totalProgress += (float)$program['progress_fisik'];
        }

        // Calculate average progress
        if ($stats['total'] > 0) {
            $stats['progress_rata'] = round($totalProgress / $stats['total'], 2);
        }

        return $stats;
    }
}