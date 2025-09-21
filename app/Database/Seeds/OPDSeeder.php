<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OPDSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode_opd' => 'BAPPEDA',
                'nama_opd' => 'Badan Perencanaan Pembangunan Daerah',
                'nama_singkat' => 'BAPPEDA',
                'kepala_opd' => 'Dr. Ahmad Sudarsono, M.Si',
                'alamat' => 'Jl. Gubernur Suryo No. 15',
                'telepon' => '(0341) 555-1001',
                'email' => 'bappeda@pemkot.go.id',
                'website' => 'https://bappeda.pemkot.go.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_opd' => 'DPUTR',
                'nama_opd' => 'Dinas Pekerjaan Umum dan Tata Ruang',
                'nama_singkat' => 'DPUTR',
                'kepala_opd' => 'Ir. Budi Santoso, M.T',
                'alamat' => 'Jl. Veteran No. 45',
                'telepon' => '(0341) 555-2001',
                'email' => 'dputr@pemkot.go.id',
                'website' => 'https://dputr.pemkot.go.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_opd' => 'DISHUB',
                'nama_opd' => 'Dinas Perhubungan',
                'nama_singkat' => 'DISHUB',
                'kepala_opd' => 'Drs. Slamet Riyadi, M.M',
                'alamat' => 'Jl. Tugu No. 20',
                'telepon' => '(0341) 555-3001',
                'email' => 'dishub@pemkot.go.id',
                'website' => 'https://dishub.pemkot.go.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_opd' => 'DISKES',
                'nama_opd' => 'Dinas Kesehatan',
                'nama_singkat' => 'DISKES',
                'kepala_opd' => 'dr. Siti Aminah, M.Kes',
                'alamat' => 'Jl. Dr. Saharjo No. 30',
                'telepon' => '(0341) 555-4001',
                'email' => 'diskes@pemkot.go.id',
                'website' => 'https://diskes.pemkot.go.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode_opd' => 'DISPENDIK',
                'nama_opd' => 'Dinas Pendidikan',
                'nama_singkat' => 'DISPENDIK',
                'kepala_opd' => 'Prof. Dr. Andi Susanto, M.Pd',
                'alamat' => 'Jl. Pahlawan No. 25',
                'telepon' => '(0341) 555-5001',
                'email' => 'dispendik@pemkot.go.id',
                'website' => 'https://dispendik.pemkot.go.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data
        $this->db->table('opd')->insertBatch($data);
    }
}
