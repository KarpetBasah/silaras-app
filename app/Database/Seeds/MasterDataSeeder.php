<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // Seed OPD data
        $opdData = [
            [
                'kode_opd' => 'PUPR',
                'nama_opd' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'nama_singkat' => 'Dinas PUPR',
                'kepala_opd' => 'Ir. Bambang Sutrisno, M.T.',
                'alamat' => 'Jl. A. Yani Km 6 Banjarbaru',
                'telepon' => '0511-4772345',
                'email' => 'pupr@banjarbarukota.go.id',
                'is_active' => 1
            ],
            [
                'kode_opd' => 'DIKBUD',
                'nama_opd' => 'Dinas Pendidikan dan Kebudayaan',
                'nama_singkat' => 'Dinas Pendidikan',
                'kepala_opd' => 'Drs. Ahmad Soleh, M.Pd.',
                'alamat' => 'Jl. Panglima Batur No. 1 Banjarbaru',
                'telepon' => '0511-4772123',
                'email' => 'dikbud@banjarbarukota.go.id',
                'is_active' => 1
            ],
            [
                'kode_opd' => 'DINKES',
                'nama_opd' => 'Dinas Kesehatan',
                'nama_singkat' => 'Dinas Kesehatan',
                'kepala_opd' => 'dr. Siti Aminah, M.Kes.',
                'alamat' => 'Jl. Jenderal Sudirman No. 15 Banjarbaru',
                'telepon' => '0511-4772678',
                'email' => 'dinkes@banjarbarukota.go.id',
                'is_active' => 1
            ],
            [
                'kode_opd' => 'PERTANIAN',
                'nama_opd' => 'Dinas Pertanian dan Perikanan',
                'nama_singkat' => 'Dinas Pertanian',
                'kepala_opd' => 'Ir. Muhammad Yusuf, M.P.',
                'alamat' => 'Jl. Guntung Manggis Banjarbaru',
                'telepon' => '0511-4772890',
                'email' => 'pertanian@banjarbarukota.go.id',
                'is_active' => 1
            ],
            [
                'kode_opd' => 'PERDAGANGAN',
                'nama_opd' => 'Dinas Perdagangan dan Koperasi',
                'nama_singkat' => 'Dinas Perdagangan',
                'kepala_opd' => 'Drs. Hendra Gunawan, M.M.',
                'alamat' => 'Jl. Bina Marga Banjarbaru',
                'telepon' => '0511-4772456',
                'email' => 'perdagangan@banjarbarukota.go.id',
                'is_active' => 1
            ],
            [
                'kode_opd' => 'BAPPEDA',
                'nama_opd' => 'Badan Perencanaan Pembangunan Daerah',
                'nama_singkat' => 'BAPPEDA',
                'kepala_opd' => 'Ir. Raden Agus Pribadi, M.T.',
                'alamat' => 'Jl. Gatot Subroto Banjarbaru',
                'telepon' => '0511-4772234',
                'email' => 'bappeda@banjarbarukota.go.id',
                'is_active' => 1
            ]
        ];

        $this->db->table('opd')->insertBatch($opdData);

        // Seed Sektor data
        $sektorData = [
            [
                'kode_sektor' => 'JALAN',
                'nama_sektor' => 'Jalan dan Transportasi',
                'deskripsi' => 'Pembangunan dan pemeliharaan infrastruktur jalan, jembatan, dan transportasi',
                'icon' => 'fas fa-road',
                'color' => '#3B82F6',
                'is_active' => 1
            ],
            [
                'kode_sektor' => 'IRIGASI',
                'nama_sektor' => 'Irigasi dan Pengairan',
                'deskripsi' => 'Pembangunan dan pemeliharaan sistem irigasi dan pengairan',
                'icon' => 'fas fa-tint',
                'color' => '#06B6D4',
                'is_active' => 1
            ],
            [
                'kode_sektor' => 'PENDIDIKAN',
                'nama_sektor' => 'Pendidikan',
                'deskripsi' => 'Pembangunan dan pengembangan fasilitas pendidikan',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#10B981',
                'is_active' => 1
            ],
            [
                'kode_sektor' => 'KESEHATAN',
                'nama_sektor' => 'Kesehatan',
                'deskripsi' => 'Pembangunan dan pengembangan fasilitas kesehatan',
                'icon' => 'fas fa-heartbeat',
                'color' => '#EF4444',
                'is_active' => 1
            ],
            [
                'kode_sektor' => 'EKONOMI',
                'nama_sektor' => 'Ekonomi dan Perdagangan',
                'deskripsi' => 'Pembangunan fasilitas ekonomi dan perdagangan',
                'icon' => 'fas fa-store',
                'color' => '#F59E0B',
                'is_active' => 1
            ],
            [
                'kode_sektor' => 'SOSIAL',
                'nama_sektor' => 'Sosial dan Budaya',
                'deskripsi' => 'Pembangunan fasilitas sosial dan budaya',
                'icon' => 'fas fa-users',
                'color' => '#8B5CF6',
                'is_active' => 1
            ]
        ];

        $this->db->table('sektor')->insertBatch($sektorData);

        // Seed RPJMD Sasaran data
        $rpjmdData = [
            [
                'kode_sasaran' => 'INFRA-001',
                'nama_sasaran' => 'Pembangunan Infrastruktur Dasar',
                'deskripsi' => 'Meningkatkan kualitas dan kuantitas infrastruktur dasar untuk mendukung perekonomian daerah',
                'prioritas' => 'tinggi',
                'periode_awal' => 2024,
                'periode_akhir' => 2029,
                'anggaran_target' => 500000000000,
                'is_active' => 1
            ],
            [
                'kode_sasaran' => 'PENDIDIKAN-001',
                'nama_sasaran' => 'Pendidikan Berkualitas dan Merata',
                'deskripsi' => 'Meningkatkan akses dan kualitas pendidikan untuk semua lapisan masyarakat',
                'prioritas' => 'tinggi',
                'periode_awal' => 2024,
                'periode_akhir' => 2029,
                'anggaran_target' => 300000000000,
                'is_active' => 1
            ],
            [
                'kode_sasaran' => 'KESEHATAN-001',
                'nama_sasaran' => 'Kesehatan Masyarakat Prima',
                'deskripsi' => 'Meningkatkan derajat kesehatan masyarakat melalui pelayanan kesehatan yang berkualitas',
                'prioritas' => 'tinggi',
                'periode_awal' => 2024,
                'periode_akhir' => 2029,
                'anggaran_target' => 250000000000,
                'is_active' => 1
            ],
            [
                'kode_sasaran' => 'EKONOMI-001',
                'nama_sasaran' => 'Ekonomi Berkelanjutan dan Kompetitif',
                'deskripsi' => 'Mengembangkan perekonomian daerah yang berkelanjutan dan berdaya saing',
                'prioritas' => 'sedang',
                'periode_awal' => 2024,
                'periode_akhir' => 2029,
                'anggaran_target' => 200000000000,
                'is_active' => 1
            ],
            [
                'kode_sasaran' => 'LINGKUNGAN-001',
                'nama_sasaran' => 'Lingkungan Hidup Lestari',
                'deskripsi' => 'Menjaga kelestarian lingkungan hidup untuk pembangunan berkelanjutan',
                'prioritas' => 'sedang',
                'periode_awal' => 2024,
                'periode_akhir' => 2029,
                'anggaran_target' => 150000000000,
                'is_active' => 1
            ]
        ];

        $this->db->table('rpjmd_sasaran')->insertBatch($rpjmdData);

        echo "Master data berhasil di-seed!\n";
    }
}
