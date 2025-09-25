<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinalStatusUpdateSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Use the ProgramModel to update records
        $programModel = new \App\Models\ProgramModel();
        
        // Set specific status for each program based on their progress
        $updates = [
            1 => 'berjalan',    // Jalan Lingkar - 30% progress
            2 => 'berjalan',    // Irigasi - 70% progress  
            3 => 'selesai',     // PAUD - 100% progress (already correct)
            4 => 'perencanaan', // Pasar - 0% progress (already correct)
            5 => 'berjalan'     // Puskesmas - 50% progress
        ];
        
        echo "Updating program status values:\n";
        foreach ($updates as $id => $status) {
            try {
                $programModel->update($id, ['status' => $status]);
                echo "✓ Program ID {$id} updated to '{$status}'\n";
            } catch (\Exception $e) {
                echo "✗ Failed to update Program ID {$id}: " . $e->getMessage() . "\n";
            }
        }
        
        // Verify the final results
        echo "\nFinal status verification:\n";
        $programs = $programModel->select('id, nama_kegiatan, status, progress_fisik')->findAll();
        
        foreach ($programs as $program) {
            $status = $program['status'] ?? 'NULL';
            echo "ID {$program['id']}: {$status} ({$program['progress_fisik']}% progress)\n";
        }
        
        // Show status distribution
        echo "\nStatus distribution:\n";
        $counts = [];
        foreach ($programs as $program) {
            $status = $program['status'] ?? 'NULL';
            $counts[$status] = ($counts[$status] ?? 0) + 1;
        }
        
        foreach ($counts as $status => $count) {
            echo "- {$status}: {$count} program(s)\n";
        }
    }
}