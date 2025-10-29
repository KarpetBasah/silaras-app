<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ProgramModel;
use App\Models\OPDModel;
use App\Models\SektorModel;
use App\Models\RpjmdSasaranModel;
use App\Models\RpjmdPriorityZoneModel;
use App\Models\AnalisisModel;

class Analisis extends BaseController
{
    protected $programModel;
    protected $opdModel;
    protected $sektorModel;
    protected $rpjmdSasaranModel;
    protected $rpjmdPriorityZoneModel;
    protected $analisisModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->programModel = new ProgramModel();
        $this->opdModel = new OPDModel();
        $this->sektorModel = new SektorModel();
        $this->rpjmdSasaranModel = new RpjmdSasaranModel();
        $this->rpjmdPriorityZoneModel = new RpjmdPriorityZoneModel();
        $this->analisisModel = new AnalisisModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Analisis Tumpang Tindih & Kesenjangan - GeoSelaras',
            'page' => 'analisis',
            'opd_list' => $this->opdModel->getActive(),
            'sektor_list' => $this->sektorModel->getActive(),
            'tahun_list' => $this->getTahunList()
        ];
        
        return view('analisis/index', $data);
    }

    /**
     * API endpoint untuk analisis tumpang tindih
     */
    public function getTumpangTindih()
    {
        try {
            $filters = [
                'sektor_id' => $this->request->getGet('sektor_id'),
                'opd_id' => $this->request->getGet('opd_id'),
                'tahun' => $this->request->getGet('tahun'),
                'radius' => $this->request->getGet('radius') ?? 100 // default 100 meter
            ];

            $tumpangTindih = $this->analisisModel->detectOverlaps($filters, $filters['radius']);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $tumpangTindih,
                'total' => count($tumpangTindih)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getTumpangTindih: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menganalisis tumpang tindih'
            ]);
        }
    }

    /**
     * API endpoint untuk analisis kesenjangan
     */
    public function getKesenjangan()
    {
        try {
            $filters = [
                'sektor_id' => $this->request->getGet('sektor_id'),
                'tahun' => $this->request->getGet('tahun'),
                'grid_size' => $this->request->getGet('grid_size') ?? 0.005 // default ~500m grid
            ];

            $kesenjangan = $this->analisisModel->identifyGaps($filters, $filters['grid_size']);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $kesenjangan
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getKesenjangan: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menganalisis kesenjangan'
            ]);
        }
    }

    /**
     * API endpoint untuk analisis keselarasan RPJMD
     */
    public function getKeselarasanRPJMD()
    {
        try {
            $filters = [
                'sektor_id' => $this->request->getGet('sektor_id'),
                'tahun' => $this->request->getGet('tahun')
            ];

            $keselarasan = $this->analisisModel->analyzeRPJMDAlignment($filters);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $keselarasan
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getKeselarasanRPJMD: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menganalisis keselarasan RPJMD'
            ]);
        }
    }

    /**
     * API endpoint untuk dashboard statistik analisis
     */
    public function getStatistikAnalisis()
    {
        try {
            $filters = [
                'tahun' => $this->request->getGet('tahun')
            ];

            $statistik = $this->analisisModel->calculateAnalysisStatistics($filters);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $statistik
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getStatistikAnalisis: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung statistik'
            ]);
        }
    }



    /**
     * Get available years from programs
     */
    private function getTahunList()
    {
        try {
            $tahunData = $this->programModel->select('tahun_pelaksanaan')
                                            ->distinct()
                                            ->orderBy('tahun_pelaksanaan', 'DESC')
                                            ->findAll();
            
            $tahunList = [];
            foreach ($tahunData as $row) {
                if (!empty($row['tahun_pelaksanaan'])) {
                    $tahunList[] = $row['tahun_pelaksanaan'];
                }
            }
            
            // If no years found, provide some default years
            if (empty($tahunList)) {
                $tahunList = [2024, 2023, 2022, 2021, 2020];
            }
            
            return $tahunList;
        } catch (\Exception $e) {
            log_message('error', 'Error getting tahun list: ' . $e->getMessage());
            // Return default years if query fails
            return [2024, 2023, 2022, 2021, 2020];
        }
    }

    /**
     * API endpoint untuk mendapatkan zona prioritas RPJMD
     */
    public function getRpjmdZones()
    {
        try {
            $zones = $this->rpjmdPriorityZoneModel->getForLeaflet(true);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $zones,
                'count' => count($zones),
                'message' => 'Zona RPJMD berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting RPJMD zones: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memuat zona RPJMD: ' . $e->getMessage()
            ]);
        }
    }
}