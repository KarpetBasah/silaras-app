<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ProgramModel;
use App\Models\OPDModel;
use App\Models\SektorModel;

class Monitoring extends BaseController
{
    protected $programModel;
    protected $opdModel;
    protected $sektorModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->programModel = new ProgramModel();
        $this->opdModel = new OPDModel();
        $this->sektorModel = new SektorModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Monitoring & Evaluasi - GeoSelaras',
            'page' => 'monitoring',
            'opd_list' => $this->opdModel->getActive(),
            'sektor_list' => $this->sektorModel->getActive(),
            'tahun_list' => $this->getTahunList()
        ];
        
        return view('monitoring/index', $data);
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