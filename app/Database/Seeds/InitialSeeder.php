<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // Sektor data
        $sektorData = [
            [
                'kode_sektor' => 'INFRA',
                'nama_sektor' => 'Infrastruktur',
                'deskripsi' => 'Sektor pembangunan infrastruktur kota',
                'icon' => 'bi bi-buildings',
                'color' => '#2563eb',
                'is_active' => true
            ],
            [
                'kode_sektor' => 'PENDIK',
                'nama_sektor' => 'Pendidikan',
                'deskripsi' => 'Sektor pengembangan pendidikan',
                'icon' => 'bi bi-book',
                'color' => '#16a34a',
                'is_active' => true
            ],
            [
                'kode_sektor' => 'KESEHAT',
                'nama_sektor' => 'Kesehatan',
                'deskripsi' => 'Sektor pembangunan kesehatan',
                'icon' => 'bi bi-heart-pulse',
                'color' => '#dc2626',
                'is_active' => true
            ]
        ];

        // Insert sektor data
        $this->db->table('sektor')->insertBatch($sektorData);

        // Sample program data
        $programData = [
            [
                'kode_program' => 'INFRA-2024-001',
                'nama_kegiatan' => 'Pembangunan Jalan Lingkar Utara',
                'deskripsi' => 'Pembangunan jalan lingkar untuk mengurangi kemacetan',
                'opd_id' => 1,
                'sektor_id' => 1,
                'rpjmd_sasaran_id' => 1,
                'lokasi_nama' => 'Jalan Lingkar Utara',
                'lokasi_alamat' => 'Kecamatan Landasan Ulin',
                'koordinat_lat' => -3.442701,
                'koordinat_lng' => 114.845207,
                'tahun_pelaksanaan' => 2024,
                'anggaran_total' => 15000000000,
                'status' => 'pelaksanaan',
                'progress_fisik' => 35.5,
                'is_prioritas' => true
            ],
            [
                'kode_program' => 'PENDIK-2024-001',
                'nama_kegiatan' => 'Renovasi SDN 1 Banjarbaru',
                'deskripsi' => 'Renovasi gedung sekolah dan fasilitas pendukung',
                'opd_id' => 2,
                'sektor_id' => 2,
                'rpjmd_sasaran_id' => 2,
                'lokasi_nama' => 'SDN 1 Banjarbaru',
                'lokasi_alamat' => 'Jl. Pendidikan No. 1',
                'koordinat_lat' => -3.457701,
                'koordinat_lng' => 114.835207,
                'tahun_pelaksanaan' => 2024,
                'anggaran_total' => 5000000000,
                'status' => 'pelaksanaan',
                'progress_fisik' => 60.0,
                'is_prioritas' => true
            ]
        ];

        // Insert program data
        $this->db->table('program')->insertBatch($programData);
    }
}