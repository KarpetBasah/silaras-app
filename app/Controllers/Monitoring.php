<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ProgramModel;
use App\Models\ProgramMonitoringModel;
use App\Models\OPDModel;
use App\Models\SektorModel;

class Monitoring extends BaseController
{
    protected $programModel;
    protected $monitoringModel;
    protected $opdModel;
    protected $sektorModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->programModel = new ProgramModel();
        $this->monitoringModel = new ProgramMonitoringModel();
        $this->opdModel = new OPDModel();
        $this->sektorModel = new SektorModel();
    }

    public function index()
    {
        // Get statistics
        $statistics = $this->monitoringModel->getMonitoringStatistics();
        $statisticsBySektor = $this->monitoringModel->getStatisticsBySektor();
        $lowProgressPrograms = $this->monitoringModel->getProgramsWithLowProgress(50);
        
        $data = [
            'title' => 'Monitoring & Evaluasi - GeoSelaras',
            'page' => 'monitoring',
            'opd_list' => $this->opdModel->getActive(),
            'sektor_list' => $this->sektorModel->getActive(),
            'tahun_list' => $this->getTahunList(),
            'statistics' => $statistics,
            'statistics_by_sektor' => $statisticsBySektor,
            'low_progress_programs' => $lowProgressPrograms
        ];
        
        return view('monitoring/index', $data);
    }

    /**
     * Display monitoring map with programs and their progress
     */
    public function map()
    {
        $data = [
            'title' => 'Peta Monitoring - GeoSelaras',
            'page' => 'monitoring',
            'opd_list' => $this->opdModel->getActive(),
            'sektor_list' => $this->sektorModel->getActive(),
            'tahun_list' => $this->getTahunList()
        ];
        
        return view('monitoring/map', $data);
    }

    /**
     * Show form to input monitoring data for a program
     */
    public function inputProgress($programId = null)
    {
        if (!$programId) {
            return redirect()->to('/monitoring')->with('error', 'Program tidak ditemukan');
        }

        $program = $this->programModel->getProgramWithRelations($programId);
        if (!$program) {
            return redirect()->to('/monitoring')->with('error', 'Program tidak ditemukan');
        }

        // Get latest monitoring data for this program
        $latestMonitoring = $this->monitoringModel->getMonitoringHistory($programId);

        $data = [
            'title' => 'Input Progress Monitoring - GeoSelaras',
            'page' => 'monitoring',
            'program' => $program,
            'latest_monitoring' => $latestMonitoring ? $latestMonitoring[0] : null,
            'monitoring_history' => $latestMonitoring
        ];
        
        return view('monitoring/input_progress', $data);
    }

    /**
     * Save monitoring progress data
     */
    public function saveProgress()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $rules = [
            'program_id' => 'required|is_natural_no_zero',
            'tanggal_monitoring' => 'required|valid_date',
            'progress_fisik' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'progress_keuangan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'anggaran_realisasi' => 'required|numeric|greater_than_equal_to[0]',
            'validator_name' => 'required|max_length[255]',
            'validator_jabatan' => 'required|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'program_id' => $this->request->getPost('program_id'),
            'tanggal_monitoring' => $this->request->getPost('tanggal_monitoring'),
            'progress_fisik' => $this->request->getPost('progress_fisik'),
            'progress_keuangan' => $this->request->getPost('progress_keuangan'),
            'anggaran_realisasi' => $this->request->getPost('anggaran_realisasi'),
            'kendala' => $this->request->getPost('kendala'),
            'solusi' => $this->request->getPost('solusi'),
            'rekomendasi' => $this->request->getPost('rekomendasi'),
            'status_lapangan' => $this->request->getPost('status_lapangan') ?: 'normal',
            'cuaca' => $this->request->getPost('cuaca'),
            'jumlah_pekerja' => $this->request->getPost('jumlah_pekerja'),
            'koordinat_lat' => $this->request->getPost('koordinat_lat'),
            'koordinat_lng' => $this->request->getPost('koordinat_lng'),
            'validator_name' => $this->request->getPost('validator_name'),
            'validator_jabatan' => $this->request->getPost('validator_jabatan'),
            'created_by' => 1 // TODO: get from session when auth is implemented
        ];

        // Handle photo uploads
        $uploadedFiles = $this->request->getFiles();
        $photoArray = [];
        
        if (!empty($uploadedFiles['foto_progress'])) {
            foreach ($uploadedFiles['foto_progress'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/monitoring/', $newName);
                    $photoArray[] = 'uploads/monitoring/' . $newName;
                }
            }
        }
        
        if (!empty($photoArray)) {
            $data['foto_progress'] = json_encode($photoArray);
        }

        try {
            $this->monitoringModel->insert($data);
            
            // Update program's progress_fisik and anggaran_realisasi
            $this->programModel->update($data['program_id'], [
                'progress_fisik' => $data['progress_fisik'],
                'anggaran_realisasi' => $data['anggaran_realisasi']
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data monitoring berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get monitoring data for map (AJAX)
     */
    public function getMapData()
    {
        // Allow both AJAX and direct requests for debugging
        $this->response->setContentType('application/json');

        try {
            $filters = [
                'sektor_id' => $this->request->getGet('sektor'),
                'opd_id' => $this->request->getGet('opd'),
                'tahun' => $this->request->getGet('tahun'),
                'status_lapangan' => $this->request->getGet('status_lapangan')
            ];

            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });

            // Debug: log filters
            log_message('info', 'Monitoring filters applied: ' . json_encode($filters));

            $monitoringData = $this->monitoringModel->getLatestMonitoringByProgram($filters);
            
            // Ensure data is properly formatted
            $formattedData = [];
            foreach ($monitoringData as $item) {
                $formattedData[] = [
                    'id' => $item['id'] ?? null,
                    'program_id' => $item['program_id'] ?? null,
                    'nama_kegiatan' => $item['nama_kegiatan'] ?? 'N/A',
                    'kode_program' => $item['kode_program'] ?? 'N/A',
                    'opd_nama' => $item['opd_nama'] ?? 'N/A',
                    'nama_sektor' => $item['nama_sektor'] ?? 'N/A',
                    'sektor_color' => $item['sektor_color'] ?? '#666666',
                    'sektor_icon' => $item['sektor_icon'] ?? 'fas fa-circle',
                    'program_lat' => floatval($item['program_lat'] ?? 0),
                    'program_lng' => floatval($item['program_lng'] ?? 0),
                    'progress_fisik' => floatval($item['progress_fisik'] ?? 0),
                    'progress_keuangan' => floatval($item['progress_keuangan'] ?? 0),
                    'anggaran_realisasi' => floatval($item['anggaran_realisasi'] ?? 0),
                    'anggaran_total' => floatval($item['anggaran_total'] ?? 0),
                    'status_lapangan' => $item['status_lapangan'] ?? 'normal',
                    'tanggal_monitoring' => $item['tanggal_monitoring'] ?? date('Y-m-d'),
                    'kendala' => $item['kendala'] ?? '',
                    'solusi' => $item['solusi'] ?? '',
                    'rekomendasi' => $item['rekomendasi'] ?? '',
                    'tahun_pelaksanaan' => $item['tahun_pelaksanaan'] ?? null
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedData,
                'count' => count($formattedData),
                'filters_applied' => $filters // Debug info
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getMapData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading map data: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Get monitoring statistics (AJAX)
     */
    public function getStatistics()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $filters = [
            'tahun' => $this->request->getGet('tahun')
        ];

        $statistics = $this->monitoringModel->getMonitoringStatistics($filters);
        $statisticsBySektor = $this->monitoringModel->getStatisticsBySektor($filters);
        
        return $this->response->setJSON([
            'success' => true,
            'statistics' => $statistics,
            'statistics_by_sektor' => $statisticsBySektor
        ]);
    }

    /**
     * Get list of programs for dropdown selection
     */
    public function getPrograms()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $filters = [
            'sektor_id' => $this->request->getGet('sektor'),
            'opd_id' => $this->request->getGet('opd'),
            'tahun' => $this->request->getGet('tahun')
        ];

        $programs = $this->programModel->getProgramsWithRelations($filters);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $programs
        ]);
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
}