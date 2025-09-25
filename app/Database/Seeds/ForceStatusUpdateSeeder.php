<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ForceStatusUpdateSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Try raw SQL approach
        try {
            // Show current table structure
            $fields = $db->getFieldData('program');
            echo "Program table fields:\n";
            foreach ($fields as $field) {
                echo "- {$field->name} ({$field->type})\n";
            }
            
            // Use raw SQL to force update
            echo "\nUsing raw SQL to update status values...\n";
            
            $sql1 = "UPDATE program SET status = 'berjalan' WHERE id IN (1, 2, 5)";
            $sql2 = "UPDATE program SET status = 'selesai' WHERE id = 3";
            $sql3 = "UPDATE program SET status = 'perencanaan' WHERE id = 4";
            
            $db->query($sql1);
            echo "Updated programs 1, 2, 5 to 'berjalan'\n";
            
            $db->query($sql2);
            echo "Updated program 3 to 'selesai'\n";
            
            $db->query($sql3);
            echo "Updated program 4 to 'perencanaan'\n";
            
            // Check results with raw SQL
            $result = $db->query("SELECT id, nama_kegiatan, status FROM program ORDER BY id");
            $programs = $result->getResultArray();
            
            echo "\nFinal verification:\n";
            foreach ($programs as $program) {
                echo "ID {$program['id']}: {$program['status']}\n";
            }
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}