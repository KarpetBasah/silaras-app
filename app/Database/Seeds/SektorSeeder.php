<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SektorSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode_sektor' => 'INFRASTRUKTUR',
                'nama_sektor' => 'Infrastruktur',
                'deskripsi' => 'Pembangunan dan pemeliharaan infrastruktur dasar seperti jalan, jembatan, drainase',
                'icon' => 'fas fa-road',
                'color' => '#3498db',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sektor' => 'KESEHATAN',
                'nama_sektor' => 'Kesehatan',
                'deskripsi' => 'Pelayanan kesehatan masyarakat dan pembangunan fasilitas kesehatan',
                'icon' => 'fas fa-hospital',
                'color' => '#e74c3c',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sektor' => 'PENDIDIKAN',
                'nama_sektor' => 'Pendidikan',
                'deskripsi' => 'Peningkatan kualitas pendidikan dan pembangunan fasilitas pendidikan',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#2ecc71',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sektor' => 'EKONOMI',
                'nama_sektor' => 'Ekonomi',
                'deskripsi' => 'Pengembangan ekonomi daerah dan pemberdayaan UMKM',
                'icon' => 'fas fa-chart-line',
                'color' => '#f39c12',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sektor' => 'LINGKUNGAN',
                'nama_sektor' => 'Lingkungan',
                'deskripsi' => 'Pelestarian lingkungan hidup dan pengelolaan sampah',
                'icon' => 'fas fa-leaf',
                'color' => '#27ae60',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_sektor' => 'SOSIAL',
                'nama_sektor' => 'Sosial Budaya',
                'deskripsi' => 'Pengembangan sosial budaya dan fasilitas olahraga',
                'icon' => 'fas fa-users',
                'color' => '#9b59b6',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data
        $this->db->table('sektor')->insertBatch($data);
    }
}
