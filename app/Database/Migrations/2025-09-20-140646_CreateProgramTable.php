<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramTable extends Migration
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
            'kode_program' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'opd_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'sektor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'rpjmd_sasaran_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'lokasi_nama' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'lokasi_alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'koordinat_lat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'koordinat_lng' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'tahun_pelaksanaan' => [
                'type' => 'YEAR',
                'constraint' => 4,
            ],
            'anggaran_total' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'anggaran_realisasi' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['perencanaan', 'persiapan', 'lelang', 'pelaksanaan', 'monitoring', 'selesai', 'batal'],
                'default' => 'perencanaan',
            ],
            'progress_fisik' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'comment' => 'Persentase progress fisik (0-100)',
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_selesai_rencana' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_selesai_aktual' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'kontraktor' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'konsultan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'sumber_dana' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_prioritas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_by' => [
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
        $this->forge->addUniqueKey('kode_program');
        $this->forge->addKey('opd_id');
        $this->forge->addKey('sektor_id');
        $this->forge->addKey('rpjmd_sasaran_id');
        $this->forge->addKey('tahun_pelaksanaan');
        $this->forge->addKey('status');
        $this->forge->addKey(['koordinat_lat', 'koordinat_lng']);
        
        // Foreign key constraints
        $this->forge->addForeignKey('opd_id', 'opd', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('sektor_id', 'sektor', 'id', 'CASCADE', 'RESTRICT'); 
        $this->forge->addForeignKey('rpjmd_sasaran_id', 'rpjmd_sasaran', 'id', 'CASCADE', 'RESTRICT');
        
        $this->forge->createTable('program');
    }

    public function down()
    {
        $this->forge->dropTable('program');
    }
}
