<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateProgramStatusEnum extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // First, update all existing records to use the new status values
        $db->query("UPDATE program SET status = 'berjalan' WHERE status IN ('pelaksanaan', 'monitoring', 'persiapan', 'lelang')");
        
        // Then modify the ENUM to only include the three new values
        $db->query("ALTER TABLE program MODIFY COLUMN status ENUM('perencanaan', 'berjalan', 'selesai') DEFAULT 'perencanaan'");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Revert back to the original ENUM values
        $db->query("ALTER TABLE program MODIFY COLUMN status ENUM('perencanaan', 'persiapan', 'lelang', 'pelaksanaan', 'monitoring', 'selesai', 'batal') DEFAULT 'perencanaan'");
    }
}
