<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class RPJMD extends BaseController
{
    protected $programModel;
    protected $sektorModel;
    protected $opdModel;
    protected $rpjmdSasaranModel;
    protected $priorityZoneModel;
    
    public function __construct()
    {
        $this->programModel = new \App\Models\ProgramModel();
        $this->sektorModel = new \App\Models\SektorModel();
        $this->opdModel = new \App\Models\OPDModel();
        $this->rpjmdSasaranModel = new \App\Models\RpjmdSasaranModel();
        $this->priorityZoneModel = new \App\Models\RpjmdPriorityZoneModel();
    }

    public function index()
    {
        try {
            // Get unique years from programs
            $programs = $this->programModel->findAll();
            $tahun_list = [];
            foreach ($programs as $program) {
                if (!empty($program['tahun_pelaksanaan']) && !in_array($program['tahun_pelaksanaan'], $tahun_list)) {
                    $tahun_list[] = $program['tahun_pelaksanaan'];
                }
            }
            sort($tahun_list);
            
            // Get sectors and OPDs
            $sectors = $this->sektorModel->findAll();
            $opds = $this->opdModel->findAll();
            
            $data = [
                'title' => 'RPJMD - Analisis Keselarasan Program',
                'tahun_list' => $tahun_list ?: [], // Ensure it's always an array
                'sektor_list' => $sectors ?: [],
                'opd_list' => $opds ?: [],
                'sectors' => $sectors ?: [],
                'opds' => $opds ?: []
            ];

            return view('rpjmd/index', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in RPJMD index: ' . $e->getMessage());
            
            // Return with empty arrays if there's an error
            $data = [
                'title' => 'RPJMD - Analisis Keselarasan Program',
                'tahun_list' => [],
                'sektor_list' => [],
                'opd_list' => [],
                'sectors' => [],
                'opds' => []
            ];
            
            return view('rpjmd/index', $data);
        }
    }
    
    /**
     * Get RPJMD priority layers (strategic areas and thematic zones)
     */
    public function getPriorityLayers()
    {
        try {
            // Get strategic areas and thematic zones from database
            $strategicAreas = $this->priorityZoneModel->getStrategicAreas();
            $thematicZones = $this->priorityZoneModel->getThematicZones();
            
            // Format coordinates for each area/zone
            foreach ($strategicAreas as &$area) {
                $area['coordinates'] = json_decode($area['coordinates'], true);
            }
            
            foreach ($thematicZones as &$zone) {
                $zone['coordinates'] = json_decode($zone['coordinates'], true);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'strategicAreas' => $strategicAreas,
                    'thematicZones' => $thematicZones
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting priority layers: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data layer prioritas'
            ]);
        }
    }
    
    /**
     * Get alignment analysis for programs
     */
    public function getAlignmentAnalysis()
    {
        try {
            $programId = $this->request->getGet('program_id');
            
            if ($programId) {
                // Get single program analysis
                $program = $this->programModel->getProgramWithRelations($programId);
                if (!$program) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 'error',
                        'message' => 'Program tidak ditemukan'
                    ]);
                }
                
                $alignment = $this->analyzeProgram($program);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $alignment
                ]);
            }
            
            // Get comprehensive alignment analysis
            $filters = [
                'sektor_id' => $this->request->getGet('sektor_id'),
                'opd_id' => $this->request->getGet('opd_id'),
                'status' => $this->request->getGet('status')
            ];
            
            $programs = $this->programModel->getProgramsWithRelations($filters);
            
            $alignedPrograms = [];
            $nonAlignedPrograms = [];
            
            foreach ($programs as $program) {
                $alignment = $this->analyzeProgram($program);
                
                if ($alignment['aligned']) {
                    $alignedPrograms[] = $program;
                } else {
                    $nonAlignedPrograms[] = $program;
                }
            }
            
            $statistics = [
                'total' => count($programs),
                'aligned' => count($alignedPrograms),
                'non_aligned' => count($nonAlignedPrograms),
                'alignment_percentage' => count($programs) > 0 ? 
                    round((count($alignedPrograms) / count($programs)) * 100, 2) : 0
            ];
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'statistics' => $statistics,
                    'aligned_programs' => array_slice($alignedPrograms, 0, 10),
                    'non_aligned_programs' => array_slice($nonAlignedPrograms, 0, 10)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in alignment analysis: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menganalisis keselarasan'
            ]);
        }
    }
    
    /**
     * Analyze single program alignment with priority zones
     */
    private function analyzeProgram($program)
    {
        if (!isset($program['latitude']) || !isset($program['longitude'])) {
            return [
                'aligned' => false,
                'zones' => [],
                'recommendations' => 'Program belum memiliki koordinat lokasi'
            ];
        }
        
        $lat = (float)$program['latitude'];
        $lng = (float)$program['longitude'];
        
        // Get zones containing this point
        $containingZones = $this->priorityZoneModel->getZonesContainingPoint($lat, $lng);
        
        $aligned = !empty($containingZones);
        $zoneNames = array_column($containingZones, 'name');
        
        $recommendations = $this->getRecommendations($program, $containingZones);
        
        return [
            'aligned' => $aligned,
            'zones' => $zoneNames,
            'zone_details' => $containingZones,
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Get recommendations based on program and zone analysis
     */
    private function getRecommendations($program, $zones)
    {
        if (empty($zones)) {
            return 'Program berada di luar zona prioritas RPJMD. Pertimbangkan untuk memindahkan lokasi atau menyesuaikan target program.';
        }
        
        $highPriorityZones = array_filter($zones, function($zone) {
            return $zone['priority'] === 'Tinggi';
        });
        
        if (!empty($highPriorityZones)) {
            return 'Program sudah selaras dengan zona prioritas tinggi RPJMD. Pastikan implementasi sesuai dengan rencana strategis daerah.';
        }
        
        return 'Program berada dalam zona prioritas RPJMD. Pertimbangkan untuk mengoptimalkan dampak program sesuai dengan tema zona.';
    }
}