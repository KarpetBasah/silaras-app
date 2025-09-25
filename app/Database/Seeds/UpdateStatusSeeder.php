<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateStatusSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Update old status values to new ones
        $updates = [
            'pelaksanaan' => 'berjalan',
            'monitoring' => 'berjalan',
            'persiapan' => 'berjalan',
            'lelang' => 'berjalan'
        ];
        
        foreach ($updates as $oldStatus => $newStatus) {
            $db->table('program')
               ->where('status', $oldStatus)
               ->update(['status' => $newStatus]);
        }
        
        // Show current status distribution
        $statusCounts = $db->table('program')
                          ->select('status, COUNT(*) as count')
                          ->groupBy('status')
                          ->get()
                          ->getResultArray();
        
        echo "Status program berhasil diupdate!\n";
        echo "Distribusi status saat ini:\n";
        foreach ($statusCounts as $status) {
            echo "- " . $status['status'] . ": " . $status['count'] . " program\n";
        }
    }
}