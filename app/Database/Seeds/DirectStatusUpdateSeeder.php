<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DirectStatusUpdateSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // First, let's see what status values exist
        $results = $db->table('program')
                     ->select('id, nama_kegiatan, status')
                     ->get()
                     ->getResultArray();
        
        echo "Current program statuses:\n";
        foreach ($results as $program) {
            $status = $program['status'] ?: 'NULL/EMPTY';
            echo "ID {$program['id']}: {$status}\n";
        }
        
        // Update each record individually
        $updates = [
            1 => 'berjalan', // Pembangunan Jalan Lingkar Timur (was pelaksanaan)
            2 => 'berjalan', // Rehabilitasi Saluran Irigasi (was monitoring) 
            3 => 'selesai',  // Pembangunan PAUD (already selesai)
            4 => 'perencanaan', // Pengembangan Pasar (already perencanaan)
            5 => 'berjalan'  // Pembangunan Puskesmas (was pelaksanaan)
        ];
        
        echo "\nUpdating statuses:\n";
        foreach ($updates as $id => $status) {
            $db->table('program')
               ->where('id', $id)
               ->update(['status' => $status]);
            echo "Updated program ID {$id} to status: {$status}\n";
        }
        
        // Final check
        echo "\nFinal status distribution:\n";
        $final = $db->table('program')
                   ->select('status, COUNT(*) as count')
                   ->groupBy('status')
                   ->get()
                   ->getResultArray();
        
        foreach ($final as $status) {
            echo "- " . ($status['status'] ?: 'EMPTY') . ": " . $status['count'] . " program\n";
        }
    }
}