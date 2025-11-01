<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MonitoringDummySeeder extends Seeder
{
    public function run()
    {
        // Get some program IDs that have coordinates
        $db = $this->db;
        $programs = $db->query("SELECT id, koordinat_lat, koordinat_lng FROM program WHERE koordinat_lat IS NOT NULL AND koordinat_lng IS NOT NULL LIMIT 5")->getResult('array');
        
        if (empty($programs)) {
            echo "No programs with coordinates found. Please run ProgramSeeder first.\n";
            return;
        }
        
        $monitoringData = [];
        
        foreach ($programs as $i => $program) {
            // Create 1-3 monitoring records per program
            $numRecords = rand(1, 3);
            
            for ($j = 0; $j < $numRecords; $j++) {
                $tanggal = date('Y-m-d', strtotime('-' . rand(1, 90) . ' days'));
                $progressFisik = rand(0, 100);
                $progressKeuangan = rand(0, $progressFisik); // Keuangan usually <= fisik
                $anggaranRealisasi = rand(100000000, 1000000000); // 100M - 1B
                
                $statusOptions = ['normal', 'terlambat', 'terkendala', 'dihentikan'];
                $cuacaOptions = ['cerah', 'berawan', 'hujan', 'mendung'];
                
                $monitoringData[] = [
                    'program_id' => $program['id'],
                    'tanggal_monitoring' => $tanggal,
                    'progress_fisik' => $progressFisik,
                    'progress_keuangan' => $progressKeuangan,
                    'anggaran_realisasi' => $anggaranRealisasi,
                    'kendala' => $this->getRandomKendala(),
                    'solusi' => $this->getRandomSolusi(),
                    'rekomendasi' => $this->getRandomRekomendasi(),
                    'status_lapangan' => $statusOptions[array_rand($statusOptions)],
                    'cuaca' => $cuacaOptions[array_rand($cuacaOptions)],
                    'jumlah_pekerja' => rand(5, 50),
                    'koordinat_lat' => $program['koordinat_lat'] + (rand(-100, 100) / 10000), // Small variation
                    'koordinat_lng' => $program['koordinat_lng'] + (rand(-100, 100) / 10000),
                    'validator_name' => $this->getRandomValidator(),
                    'validator_jabatan' => $this->getRandomJabatan(),
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime($tanggal . ' +' . rand(8, 17) . ' hours')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime($tanggal . ' +' . rand(8, 17) . ' hours'))
                ];
            }
        }
        
        // Insert data
        $builder = $db->table('program_monitoring');
        $builder->insertBatch($monitoringData);
        
        echo "Inserted " . count($monitoringData) . " monitoring records.\n";
        
        // Update program table with latest progress
        foreach ($programs as $program) {
            $latest = $db->query("SELECT progress_fisik, anggaran_realisasi FROM program_monitoring WHERE program_id = ? ORDER BY tanggal_monitoring DESC LIMIT 1", [$program['id']])->getRow('array');
            
            if ($latest) {
                $db->query("UPDATE program SET progress_fisik = ?, anggaran_realisasi = ? WHERE id = ?", [
                    $latest['progress_fisik'],
                    $latest['anggaran_realisasi'],
                    $program['id']
                ]);
            }
        }
        
        echo "Updated program progress data.\n";
    }
    
    private function getRandomKendala()
    {
        $kendala = [
            'Cuaca tidak mendukung',
            'Keterlambatan material',
            'Kekurangan tenaga kerja',
            'Masalah perizinan',
            'Kondisi tanah sulit',
            'Gangguan akses jalan',
            'Keterbatasan alat berat',
            null
        ];
        return $kendala[array_rand($kendala)];
    }
    
    private function getRandomSolusi()
    {
        $solusi = [
            'Menunggu cuaca membaik',
            'Koordinasi dengan supplier',
            'Menambah tenaga kerja',
            'Proses perizinan dipercepat',
            'Menggunakan metode khusus',
            'Membuat akses alternatif',
            'Menyewa alat tambahan',
            null
        ];
        return $solusi[array_rand($solusi)];
    }
    
    private function getRandomRekomendasi()
    {
        $rekomendasi = [
            'Lanjutkan sesuai rencana',
            'Perlu percepatan pelaksanaan',
            'Evaluasi metode kerja',
            'Tingkatkan koordinasi tim',
            'Pantau cuaca secara berkala',
            'Siapkan rencana kontinjensi',
            null
        ];
        return $rekomendasi[array_rand($rekomendasi)];
    }
    
    private function getRandomValidator()
    {
        $validators = [
            'Ir. Ahmad Sutanto',
            'Dra. Siti Nurhaliza',
            'Ir. Budi Santoso, MT',
            'Drs. Eko Prasetyo',
            'Ir. Maya Sari, M.Eng'
        ];
        return $validators[array_rand($validators)];
    }
    
    private function getRandomJabatan()
    {
        $jabatan = [
            'Kepala Seksi Monitoring',
            'Supervisor Lapangan',
            'Insinyur Pengawas',
            'Koordinator Proyek',
            'Kepala Bidang Teknik'
        ];
        return $jabatan[array_rand($jabatan)];
    }
}