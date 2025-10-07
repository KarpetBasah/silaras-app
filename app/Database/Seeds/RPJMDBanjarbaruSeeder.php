<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RPJMDBanjarbaruSeeder extends Seeder
{
    public function run()
    {
        // Check if rpjmd_priority_zones table exists
        if (!$this->db->tableExists('rpjmd_priority_zones')) {
            echo "Table 'rpjmd_priority_zones' does not exist. Please run migrations first.\n";
            return;
        }
        
        // Clear existing data
        $this->db->table('rpjmd_priority_zones')->truncate();
        
        // Insert sample RPJMD priority zones data for Banjarbaru
        $priorityZones = [
            // Strategic Areas (Kawasan Strategis) - Banjarbaru
            [
                'type' => 'strategic',
                'name' => 'Kawasan Pusat Pemerintahan Banjarbaru',
                'description' => 'Kawasan pusat pemerintahan dan pelayanan publik Kota Banjarbaru',
                'priority' => 'Tinggi',
                'theme' => null,
                'color' => '#1e40af',
                'coordinates' => json_encode([
                    [[-3.4582, 114.8348], [-3.4582, 114.8448], [-3.4482, 114.8448], [-3.4482, 114.8348]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Pendidikan Tinggi Banjarbaru',
                'description' => 'Kawasan pengembangan pendidikan tinggi dan riset',
                'priority' => 'Tinggi',
                'theme' => null,
                'color' => '#7c3aed',
                'coordinates' => json_encode([
                    [[-3.4700, 114.8500], [-3.4700, 114.8600], [-3.4600, 114.8600], [-3.4600, 114.8500]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Industri Banjarbaru',
                'description' => 'Kawasan pengembangan industri dan manufaktur',
                'priority' => 'Sedang',
                'theme' => null,
                'color' => '#059669',
                'coordinates' => json_encode([
                    [[-3.4400, 114.8200], [-3.4400, 114.8300], [-3.4300, 114.8300], [-3.4300, 114.8200]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Bisnis Syamsudin Noor',
                'description' => 'Kawasan komersial dan bisnis di sekitar bandara',
                'priority' => 'Sedang',
                'theme' => null,
                'color' => '#dc2626',
                'coordinates' => json_encode([
                    [[-3.4800, 114.8650], [-3.4800, 114.8750], [-3.4700, 114.8750], [-3.4700, 114.8650]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Thematic Zones (Zona Tematik) - Banjarbaru
            [
                'type' => 'thematic',
                'name' => 'Zona Pengembangan Ekonomi Kreatif',
                'description' => 'Zona khusus untuk pengembangan industri kreatif dan startup',
                'priority' => 'Tinggi',
                'theme' => 'Ekonomi Kreatif',
                'color' => '#06b6d4',
                'coordinates' => json_encode([
                    [[-3.4550, 114.8380], [-3.4550, 114.8450], [-3.4480, 114.8450], [-3.4480, 114.8380]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Konservasi Lingkungan',
                'description' => 'Zona perlindungan dan konservasi lingkungan hidup',
                'priority' => 'Tinggi',
                'theme' => 'Lingkungan',
                'color' => '#10b981',
                'coordinates' => json_encode([
                    [[-3.4350, 114.8150], [-3.4350, 114.8250], [-3.4250, 114.8250], [-3.4250, 114.8150]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Pengembangan Perumahan',
                'description' => 'Zona prioritas pengembangan perumahan dan permukiman',
                'priority' => 'Sedang',
                'theme' => 'Perumahan',
                'color' => '#f59e0b',
                'coordinates' => json_encode([
                    [[-3.4650, 114.8400], [-3.4650, 114.8500], [-3.4550, 114.8500], [-3.4550, 114.8400]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Infrastruktur Transportasi',
                'description' => 'Zona pengembangan infrastruktur transportasi dan logistik',
                'priority' => 'Sedang',
                'theme' => 'Transportasi',
                'color' => '#8b5cf6',
                'coordinates' => json_encode([
                    [[-3.4750, 114.8550], [-3.4750, 114.8650], [-3.4650, 114.8650], [-3.4650, 114.8550]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        // Insert the data
        foreach ($priorityZones as $zone) {
            $this->db->table('rpjmd_priority_zones')->insert($zone);
        }
        
        echo "RPJMD Priority Zones sample data for Banjarbaru inserted successfully.\n";
        echo "Inserted " . count($priorityZones) . " priority zones.\n";
        
        // Update some existing programs with RPJMD sasaran assignments (if programs table exists)
        if ($this->db->tableExists('programs')) {
            $programUpdates = [
                ['id' => 1, 'rpjmd_sasaran' => 'Peningkatan kualitas pelayanan publik'],
                ['id' => 2, 'rpjmd_sasaran' => 'Pengembangan ekonomi kreatif dan startup'],
                ['id' => 3, 'rpjmd_sasaran' => 'Peningkatan kualitas pendidikan tinggi'],
                ['id' => 4, 'rpjmd_sasaran' => 'Pengembangan infrastruktur transportasi'],
                ['id' => 5, 'rpjmd_sasaran' => 'Konservasi lingkungan dan pembangunan berkelanjutan']
            ];
            
            $updated = 0;
            foreach ($programUpdates as $update) {
                $result = $this->db->table('programs')
                    ->where('id', $update['id'])
                    ->update(['rpjmd_sasaran' => $update['rpjmd_sasaran']]);
                if ($result) $updated++;
            }
            
            echo "Updated $updated programs with RPJMD sasaran assignments.\n";
        }
    }
}