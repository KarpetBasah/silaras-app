<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramDokumenTable extends Migration
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
            'jenis_dokumen' => [
                'type' => 'ENUM',
                'constraint' => ['rab', 'ded', 'foto_lokasi', 'foto_progress', 'kontrak', 'addendum', 'ba_serah_terima', 'lainnya'],
            ],
            'nama_dokumen' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'path_file' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'ukuran_file' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'comment' => 'File size in bytes',
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_upload' => [
                'type' => 'DATETIME',
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Apakah dokumen dapat diakses publik',
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
        $this->forge->addKey('jenis_dokumen');
        $this->forge->addKey('tanggal_upload');
        
        // Foreign key constraint
        $this->forge->addForeignKey('program_id', 'program', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('program_dokumen');
    }

    public function down()
    {
        $this->forge->dropTable('program_dokumen');
    }
}
