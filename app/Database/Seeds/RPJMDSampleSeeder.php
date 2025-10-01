<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RPJMDSampleSeeder extends Seeder
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
        
        // Insert sample RPJMD priority zones data
        $priorityZones = [
            // Strategic Areas (Kawasan Strategis)
            [
                'type' => 'strategic',
                'name' => 'Kawasan Pusat Bisnis Palembang',
                'description' => 'Kawasan pengembangan pusat ekonomi dan bisnis utama kota',
                'priority' => 'Tinggi',
                'theme' => null,
                'color' => '#2563eb',
                'coordinates' => json_encode([
                    [[-2.9441, 104.7389], [-2.9441, 104.7500], [-2.9300, 104.7500], [-2.9300, 104.7389]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Heritage Palembang',
                'description' => 'Kawasan pelestarian warisan budaya dan sejarah',
                'priority' => 'Tinggi',
                'theme' => null,
                'color' => '#7c3aed',
                'coordinates' => json_encode([
                    [[-2.9900, 104.7500], [-2.9900, 104.7600], [-2.9800, 104.7600], [-2.9800, 104.7500]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Industri Terpadu',
                'description' => 'Kawasan pengembangan industri dan manufaktur',
                'priority' => 'Sedang',
                'theme' => null,
                'color' => '#059669',
                'coordinates' => json_encode([
                    [[-2.8500, 104.8000], [-2.8500, 104.8200], [-2.8300, 104.8200], [-2.8300, 104.8000]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Pelabuhan dan Logistik',
                'description' => 'Kawasan pengembangan pelabuhan dan distribusi logistik',
                'priority' => 'Tinggi',
                'theme' => null,
                'color' => '#dc2626',
                'coordinates' => json_encode([
                    [[-3.0000, 104.7200], [-3.0000, 104.7400], [-2.9800, 104.7400], [-2.9800, 104.7200]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Thematic Zones (Zona Tematik)
            [
                'type' => 'thematic',
                'name' => 'Zona Pendidikan dan Riset',
                'description' => 'Zona pengembangan institusi pendidikan dan pusat penelitian',
                'priority' => 'Sedang',
                'theme' => 'Pendidikan',
                'color' => '#f59e0b',
                'coordinates' => json_encode([
                    [[-2.9200, 104.7000], [-2.9200, 104.7300], [-2.9000, 104.7300], [-2.9000, 104.7000]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Kesehatan dan Medis',
                'description' => 'Zona konsentrasi fasilitas kesehatan dan layanan medis',
                'priority' => 'Sedang',
                'theme' => 'Kesehatan',
                'color' => '#10b981',
                'coordinates' => json_encode([
                    [[-2.9600, 104.7300], [-2.9600, 104.7500], [-2.9400, 104.7500], [-2.9400, 104.7300]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Pariwisata dan Rekreasi',
                'description' => 'Zona pengembangan destinasi wisata dan fasilitas rekreasi',
                'priority' => 'Sedang',
                'theme' => 'Pariwisata',
                'color' => '#8b5cf6',
                'coordinates' => json_encode([
                    [[-2.9700, 104.7600], [-2.9700, 104.7800], [-2.9500, 104.7800], [-2.9500, 104.7600]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Teknologi dan Inovasi',
                'description' => 'Zona pengembangan teknologi informasi dan startup',
                'priority' => 'Sedang',
                'theme' => 'Teknologi',
                'color' => '#06b6d4',
                'coordinates' => json_encode([
                    [[-2.8800, 104.7400], [-2.8800, 104.7600], [-2.8600, 104.7600], [-2.8600, 104.7400]]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        // Insert sample data
        foreach ($priorityZones as $zone) {
            $this->db->table('rpjmd_priority_zones')->insert($zone);
        }
        
        echo "RPJMD Priority Zones sample data inserted successfully.\n";
        echo "Inserted " . count($priorityZones) . " priority zones.\n";
        
        // Also update program table to add rpjmd_sasaran_id if not exists
        if (!$this->db->fieldExists('rpjmd_sasaran_id', 'program')) {
            $forge = \Config\Database::forge();
            
            $fields = [
                'rpjmd_sasaran_id' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'sektor_id'
                ]
            ];
            
            $forge->addColumn('program', $fields);
            echo "Added rpjmd_sasaran_id column to program table.\n";
        }
        
        // Sample updates to existing programs with random RPJMD sasaran assignments
        $programModel = new \App\Models\ProgramModel();
        $programs = $programModel->findAll();
        
        if (!empty($programs)) {
            // Get available sasaran IDs (assuming some exist)
            $sasaranIds = range(1, 5); // Assuming sasaran IDs 1-5 exist
            
            foreach ($programs as $program) {
                // Randomly assign sasaran (30% chance of no assignment)
                $randomSasaran = rand(1, 10) <= 7 ? $sasaranIds[array_rand($sasaranIds)] : null;
                
                $programModel->update($program['id'], [
                    'rpjmd_sasaran_id' => $randomSasaran
                ]);
            }
            
            echo "Updated " . count($programs) . " programs with RPJMD sasaran assignments.\n";
        }
    }
}