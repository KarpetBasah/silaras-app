<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProgramModel;
use App\Models\SektorModel;
use App\Models\ProgramDokumenModel;
use CodeIgniter\API\ResponseTrait;

class Monitoring extends BaseController
{
    use ResponseTrait;

    protected $programModel;
    protected $sektorModel;
    protected $dokumenModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
        $this->sektorModel = new SektorModel();
        $this->dokumenModel = new ProgramDokumenModel();
    }

    public function index()
    {
        try {
            // Get active sectors only
            $sektor_list = $this->sektorModel->where('is_active', true)->findAll();
            
            // Get unique years from programs
            $years = $this->programModel->select('DISTINCT tahun_pelaksanaan as year')
                                      ->orderBy('tahun_pelaksanaan', 'DESC')
                                      ->findAll();
            $tahun_list = array_column($years, 'year');
            
            if (empty($tahun_list)) {
                $tahun_list = range(date('Y'), date('Y') + 2);
            }

            $data = [
                'title' => 'Monitoring & Evaluasi',
                'sektor_list' => $sektor_list,
                'tahun_list' => $tahun_list
            ];
            
            return view('monitoring/index', $data);
        } catch (\Exception $e) {
            log_message('error', '[Monitoring::index] Error: ' . $e->getMessage());
            return view('errors/html/error_exception', ['message' => 'Terjadi kesalahan saat memuat halaman']);
        }
    }

    public function getStatistics()
    {
        try {
            $tahun = $this->request->getGet('tahun') ?? date('Y');
            $sektor_id = $this->request->getGet('sektor_id');

            $query = $this->programModel
                ->select('COUNT(*) as total_program')
                ->select('COALESCE(AVG(progress_fisik), 0) as rata_progress')
                ->select('COALESCE(SUM(anggaran_total), 0) as total_anggaran')
                ->select('COALESCE(SUM(anggaran_realisasi), 0) as total_realisasi');

            // Add year filter
            $query->where('tahun_pelaksanaan', $tahun);

            // Add sector filter if specified
            if ($sektor_id) {
                $query->where('sektor_id', $sektor_id);
            }

            $stats = $query->get()->getRowArray();
            
            return $this->respond([
                'success' => true,
                'data' => [
                    'total_program' => (int) $stats['total_program'],
                    'rata_progress' => round((float) $stats['rata_progress'], 2),
                    'total_anggaran' => (float) $stats['total_anggaran'],
                    'total_realisasi' => (float) $stats['total_realisasi']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Monitoring::getStatistics] Error: ' . $e->getMessage());
            return $this->fail('Gagal memuat data statistik', 500);
        }
    }

    public function getProgramData()
    {
        try {
            $tahun = $this->request->getGet('tahun') ?? date('Y');
            $sektor_id = $this->request->getGet('sektor_id');

            $query = $this->programModel
                ->select('program.*, sektor.nama_sektor')
                ->join('sektor', 'sektor.id = program.sektor_id');

            // Add year filter
            $query->where('tahun_pelaksanaan', $tahun);

            // Add sector filter if specified
            if ($sektor_id) {
                $query->where('program.sektor_id', $sektor_id);
            }

            $programs = $query->findAll();

            // Format data for GeoJSON
            $features = array_map(function($program) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $program['koordinat_lng'],
                            (float) $program['koordinat_lat']
                        ]
                    ],
                    'properties' => [
                        'id' => $program['id'],
                        'nama' => $program['nama_kegiatan'],
                        'sektor' => $program['nama_sektor'],
                        'progress' => (float) $program['progress_fisik'],
                        'anggaran_total' => (float) $program['anggaran_total'],
                        'anggaran_realisasi' => (float) $program['anggaran_realisasi'],
                        'lokasi' => $program['lokasi_nama'],
                        'alamat' => $program['lokasi_alamat'],
                        'status' => $program['status']
                    ]
                ];
            }, $programs);

            return $this->respond([
                'success' => true,
                'type' => 'FeatureCollection',
                'features' => $features
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Monitoring::getProgramData] Error: ' . $e->getMessage());
            return $this->fail('Gagal memuat data program', 500);
        }
    }
