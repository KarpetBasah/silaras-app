<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class PetaProgram extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Peta Program - GeoSelaras',
            'page' => 'peta-program'
        ];
        
        return view('peta_program/index', $data);
    }
    
    public function getProgramData()
    {
        // TODO: Get data from database
        // Sample data for now
        $programs = [
            [
                'id' => 1,
                'nama_kegiatan' => 'Pembangunan Jalan Lingkar Timur',
                'lat' => -3.4582,
                'lng' => 114.8348,
                'sektor' => 'jalan',
                'status' => 'perencanaan',
                'anggaran' => 2500000000,
                'tahun' => 2024,
                'opd' => 'Dinas PUPR'
            ],
            [
                'id' => 2,
                'nama_kegiatan' => 'Rehabilitasi Saluran Irigasi Martapura',
                'lat' => -3.4650,
                'lng' => 114.8400,
                'sektor' => 'irigasi',
                'status' => 'berjalan',
                'anggaran' => 1800000000,
                'tahun' => 2024,
                'opd' => 'Dinas Pertanian'
            ],
            [
                'id' => 3,
                'nama_kegiatan' => 'Pembangunan PAUD Terpadu',
                'lat' => -3.4520,
                'lng' => 114.8280,
                'sektor' => 'pendidikan',
                'status' => 'selesai',
                'anggaran' => 850000000,
                'tahun' => 2024,
                'opd' => 'Dinas Pendidikan'
            ],
            [
                'id' => 4,
                'nama_kegiatan' => 'Pengembangan Pasar Tradisional',
                'lat' => -3.4600,
                'lng' => 114.8320,
                'sektor' => 'ekonomi',
                'status' => 'perencanaan',
                'anggaran' => 3200000000,
                'tahun' => 2025,
                'opd' => 'Dinas Perdagangan'
            ],
            [
                'id' => 5,
                'nama_kegiatan' => 'Pembangunan Puskesmas Pembantu',
                'lat' => -3.4480,
                'lng' => 114.8420,
                'sektor' => 'kesehatan',
                'status' => 'berjalan',
                'anggaran' => 1950000000,
                'tahun' => 2024,
                'opd' => 'Dinas Kesehatan'
            ]
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $programs
        ]);
    }
    
    public function getProgramDetail($id)
    {
        // TODO: Get specific program data from database
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'id' => $id,
                'nama_kegiatan' => 'Sample Program',
                'deskripsi' => 'Deskripsi detail program',
                'lokasi' => 'Alamat lokasi program',
                'koordinat' => [-3.4582, 114.8348],
                'sektor' => 'infrastruktur',
                'status' => 'perencanaan',
                'anggaran' => 2500000000,
                'tahun_pelaksanaan' => 2024,
                'opd' => 'Sample OPD',
                'sasaran_rpjmd' => 'Sample sasaran RPJMD',
                'progress_fisik' => 45,
                'realisasi_anggaran' => 35
            ]
        ]);
    }
}