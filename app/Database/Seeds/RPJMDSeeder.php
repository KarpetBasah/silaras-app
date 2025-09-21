<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RPJMDSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode_sasaran' => 'RPJMD-01',
                'nama_sasaran' => 'Meningkatkan Kualitas Infrastruktur Wilayah',
                'deskripsi' => 'Pembangunan dan peningkatan infrastruktur dasar untuk mendukung aktivitas masyarakat dan perekonomian daerah',
                'prioritas' => 'tinggi',
                'target_indikator' => 'Persentase jalan dalam kondisi baik mencapai 90%, 20 jembatan diperbaiki',
                'periode_awal' => 2022,
                'periode_akhir' => 2026,
                'kawasan_strategis' => 'Kawasan Pusat Kota, Kawasan Industri, Kawasan Wisata',
                'anggaran_target' => 50000000000,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sasaran' => 'RPJMD-02',
                'nama_sasaran' => 'Meningkatkan Kualitas Pelayanan Kesehatan',
                'deskripsi' => 'Peningkatan akses dan kualitas layanan kesehatan masyarakat melalui pembangunan fasilitas dan peningkatan SDM',
                'prioritas' => 'tinggi',
                'target_indikator' => 'Angka Harapan Hidup mencapai 73 tahun, 1 Puskesmas per 25.000 penduduk',
                'periode_awal' => 2022,
                'periode_akhir' => 2026,
                'kawasan_strategis' => 'Kawasan Padat Penduduk, Kawasan Terpencil',
                'anggaran_target' => 25000000000,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sasaran' => 'RPJMD-03', 
                'nama_sasaran' => 'Meningkatkan Kualitas Pendidikan',
                'deskripsi' => 'Peningkatan akses dan mutu pendidikan untuk semua lapisan masyarakat',
                'prioritas' => 'sedang',
                'target_indikator' => 'APS SD 99%, APS SMP 97%, Rasio siswa-guru 1:18',
                'periode_awal' => 2022,
                'periode_akhir' => 2026,
                'kawasan_strategis' => 'Seluruh wilayah kota dengan prioritas daerah tertinggal',
                'anggaran_target' => 30000000000,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data
        $this->db->table('rpjmd_sasaran')->insertBatch($data);
    }
}
