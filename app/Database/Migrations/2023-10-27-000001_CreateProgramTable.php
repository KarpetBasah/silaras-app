<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramTable extends Migration
{
    public function up()
    {
        // Tabel sektor
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_sektor' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_sektor' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_sektor');
        $this->forge->createTable('sektor', true);

        // Tabel program
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
                'null' => true,
            ],
            'koordinat_lng' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ],
            'tahun_pelaksanaan' => [
                'type' => 'YEAR',
                'constraint' => 4,
            ],
            'anggaran_total' => [
                'type' => 'BIGINT',
                'default' => 0,
            ],
            'anggaran_realisasi' => [
                'type' => 'BIGINT',
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['perencanaan', 'pelaksanaan', 'selesai', 'ditunda', 'dibatalkan'],
                'default' => 'perencanaan',
            ],
            'progress_fisik' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
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
                'type' => 'BOOLEAN',
                'default' => false,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_program');
        $this->forge->addForeignKey('sektor_id', 'sektor', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('program', true);
    }

    public function down()
    {
        $this->forge->dropTable('program', true);
        $this->forge->dropTable('sektor', true);
    }
}