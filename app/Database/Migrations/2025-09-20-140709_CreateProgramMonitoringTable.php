<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramMonitoringTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tanggal_monitoring' => [
                'type' => 'DATE',
            ],
            'progress_fisik' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'comment' => 'Persentase progress fisik (0-100)',
            ],
            'progress_keuangan' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'comment' => 'Persentase realisasi keuangan (0-100)',
            ],
            'anggaran_realisasi' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'kendala' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'solusi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rekomendasi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_lapangan' => [
                'type' => 'ENUM',
                'constraint' => ['normal', 'terlambat', 'terkendala', 'dihentikan'],
                'default' => 'normal',
            ],
            'cuaca' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'jumlah_pekerja' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'foto_progress' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of photo paths',
            ],
            'koordinat_lat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
                'comment' => 'Koordinat saat monitoring (GPS)'
            ],
            'koordinat_lng' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
                'comment' => 'Koordinat saat monitoring (GPS)'
            ],
            'validator_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Nama petugas monitoring',
            ],
            'validator_jabatan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'verified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('program_id');
        $this->forge->addKey('tanggal_monitoring');
        $this->forge->addKey('status_lapangan');
        $this->forge->addKey('is_verified');
        
        // Foreign key constraint
        $this->forge->addForeignKey('program_id', 'program', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('program_monitoring');
    }

    public function down()
    {
        $this->forge->dropTable('program_monitoring');
    }
}
