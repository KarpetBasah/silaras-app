<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRpjmdTable extends Migration
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
            'kode_sasaran' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_sasaran' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'prioritas' => [
                'type' => 'ENUM',
                'constraint' => ['tinggi', 'sedang', 'rendah'],
                'default' => 'sedang',
            ],
            'target_indikator' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON format untuk multiple indicators',
            ],
            'periode_awal' => [
                'type' => 'YEAR',
                'constraint' => 4,
            ],
            'periode_akhir' => [
                'type' => 'YEAR',
                'constraint' => 4,
            ],
            'kawasan_strategis' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'GeoJSON format untuk polygon kawasan',
            ],
            'anggaran_target' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addUniqueKey('kode_sasaran');
        $this->forge->createTable('rpjmd_sasaran');
    }

    public function down()
    {
        $this->forge->dropTable('rpjmd_sasaran');
    }
}
