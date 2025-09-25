<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixEmptyStatusSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Update empty status values to 'berjalan' for active programs
        $db->table('program')
           ->where('status', '')
           ->orWhere('status IS NULL')
           ->update(['status' => 'berjalan']);
        
        // Show current status distribution
        $statusCounts = $db->table('program')
                          ->select('status, COUNT(*) as count')
                          ->groupBy('status')
                          ->get()
                          ->getResultArray();
        
        echo "Status kosong berhasil diperbaiki!\n";
        echo "Distribusi status final:\n";
        foreach ($statusCounts as $status) {
            $statusName = $status['status'] ?: '(kosong)';
            echo "- " . $statusName . ": " . $status['count'] . " program\n";
        }
    }
}