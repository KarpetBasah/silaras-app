<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProgramSampleSeeder extends Seeder
{
    public function run()
    {
        // Sample program data
        $programData = [
            [
                'kode_program' => 'PRG-202409-001',
                'nama_kegiatan' => 'Pembangunan Jalan Lingkar Timur',
                'deskripsi' => 'Pembangunan jalan lingkar bagian timur untuk mengurangi kemacetan di pusat kota',
                'opd_id' => 1, // Dinas PUPR
                'sektor_id' => 1, // Jalan dan Transportasi
                'rpjmd_sasaran_id' => 1, // Pembangunan Infrastruktur Dasar
                'lokasi_nama' => 'Jalan Lingkar Timur',
                'lokasi_alamat' => 'Kelurahan Landasan Ulin Timur, Kecamatan Liang Anggang',
                'koordinat_lat' => -3.4582,
                'koordinat_lng' => 114.8348,
                'tahun_pelaksanaan' => 2024,
                'anggaran_total' => 2500000000,
                'anggaran_realisasi' => 750000000,
                'status' => 'berjalan',
                'progress_fisik' => 30.00,
                'tanggal_mulai' => '2024-03-01',
                'tanggal_selesai_rencana' => '2024-12-31',
                'kontraktor' => 'PT. Jaya Konstruksi',
                'sumber_dana' => 'APBD',
                'is_prioritas' => 1,
                'created_by' => 1
            ],
            [
                'kode_program' => 'PRG-202409-002',
                'nama_kegiatan' => 'Rehabilitasi Saluran Irigasi Martapura',
                'deskripsi' => 'Rehabilitasi dan normalisasi saluran irigasi primer Martapura',
                'opd_id' => 4, // Dinas Pertanian
                'sektor_id' => 2, // Irigasi dan Pengairan
                'rpjmd_sasaran_id' => 1, // Pembangunan Infrastruktur Dasar
                'lokasi_nama' => 'Saluran Irigasi Martapura',
                'lokasi_alamat' => 'Kelurahan Guntung Payung, Kecamatan Landasan Ulin',
                'koordinat_lat' => -3.4650,
                'koordinat_lng' => 114.8400,
                'tahun_pelaksanaan' => 2024,
                'anggaran_total' => 1800000000,
                'anggaran_realisasi' => 1260000000,
                'status' => 'berjalan',
                'progress_fisik' => 70.00,
                'tanggal_mulai' => '2024-01-15',
                'tanggal_selesai_rencana' => '2024-08-31',
                'kontraktor' => 'CV. Sumber Berkah',
                'sumber_dana' => 'APBD',
                'is_prioritas' => 0,
                'created_by' => 1
            ],
            [
                'kode_program' => 'PRG-202409-003',
                'nama_kegiatan' => 'Pembangunan PAUD Terpadu Melati',
                'deskripsi' => 'Pembangunan gedung PAUD dengan fasilitas bermain dan pembelajaran modern',
                'opd_id' => 2, // Dinas Pendidikan
                'sektor_id' => 3, // Pendidikan
                'rpjmd_sasaran_id' => 2, // Pendidikan Berkualitas dan Merata
                'lokasi_nama' => 'PAUD Terpadu Melati',
                'lokasi_alamat' => 'Kelurahan Cempaka, Kecamatan Cempaka',
                'koordinat_lat' => -3.4520,
                'koordinat_lng' => 114.8280,
                'tahun_pelaksanaan' => 2024,
                'anggaran_total' => 850000000,
                'anggaran_realisasi' => 850000000,
                'status' => 'selesai',
                'progress_fisik' => 100.00,
                'tanggal_mulai' => '2024-01-01',
                'tanggal_selesai_rencana' => '2024-06-30',
                'tanggal_selesai_aktual' => '2024-06-20',
                'kontraktor' => 'PT. Bangun Sejahtera',
                'sumber_dana' => 'APBD',
                'is_prioritas' => 1,
                'created_by' => 1
            ],
            [
                'kode_program' => 'PRG-202409-004',
                'nama_kegiatan' => 'Pengembangan Pasar Tradisional Banjarbaru',
                'deskripsi' => 'Revitalisasi dan modernisasi pasar tradisional dengan fasilitas yang memadai',
                'opd_id' => 5, // Dinas Perdagangan
                'sektor_id' => 5, // Ekonomi dan Perdagangan
                'rpjmd_sasaran_id' => 4, // Ekonomi Berkelanjutan dan Kompetitif
                'lokasi_nama' => 'Pasar Tradisional Banjarbaru',
                'lokasi_alamat' => 'Jl. A. Yani Km 8, Kelurahan Komet',
                'koordinat_lat' => -3.4600,
                'koordinat_lng' => 114.8320,
                'tahun_pelaksanaan' => 2025,
                'anggaran_total' => 3200000000,
                'anggaran_realisasi' => 0,
                'status' => 'perencanaan',
                'progress_fisik' => 0.00,
                'tanggal_selesai_rencana' => '2025-12-31',
                'sumber_dana' => 'APBD + APBN',
                'is_prioritas' => 1,
                'created_by' => 1
            ],
            [
                'kode_program' => 'PRG-202409-005',
                'nama_kegiatan' => 'Pembangunan Puskesmas Pembantu Sungai Besar',
                'deskripsi' => 'Pembangunan fasilitas kesehatan untuk melayani masyarakat di wilayah Sungai Besar',
                'opd_id' => 3, // Dinas Kesehatan
                'sektor_id' => 4, // Kesehatan
                'rpjmd_sasaran_id' => 3, // Kesehatan Masyarakat Prima
                'lokasi_nama' => 'Puskesmas Pembantu Sungai Besar',
                'lokasi_alamat' => 'Kelurahan Sungai Besar, Kecamatan Banjarbaru Utara',
                'koordinat_lat' => -3.4480,
                'koordinat_lng' => 114.8420,
                'tahun_pelaksanaan' => 2024,
                'anggaran_total' => 1950000000,
                'anggaran_realisasi' => 975000000,
                'status' => 'berjalan',
                'progress_fisik' => 50.00,
                'tanggal_mulai' => '2024-02-01',
                'tanggal_selesai_rencana' => '2024-10-31',
                'kontraktor' => 'PT. Medika Konstruksi',
                'sumber_dana' => 'APBD',
                'is_prioritas' => 0,
                'created_by' => 1
            ]
        ];

        // Insert programs using model
        $programModel = new \App\Models\ProgramModel();
        
        foreach ($programData as $program) {
            $programModel->insert($program);
        }

        echo "Sample program data berhasil di-seed!\n";
    }
}
