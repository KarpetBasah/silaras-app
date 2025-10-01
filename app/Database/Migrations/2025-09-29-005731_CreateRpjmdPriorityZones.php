<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRpjmdPriorityZones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['strategic', 'thematic'],
                'default' => 'strategic'
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['Tinggi', 'Sedang', 'Rendah'],
                'default' => 'Sedang',
                'null' => true,
            ],
            'theme' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#2563eb',
            ],
            'coordinates' => [
                'type' => 'JSON',
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
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['type', 'is_active']);
        $this->forge->createTable('rpjmd_priority_zones');
    }

    public function down()
    {
        $this->forge->dropTable('rpjmd_priority_zones');
    }
}
