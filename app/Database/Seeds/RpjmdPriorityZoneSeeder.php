<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RpjmdPriorityZoneSeeder extends Seeder
{
    public function run()
    {
        // Sample RPJMD Priority Zones for Banjarbaru area
        $data = [
            [
                'type' => 'strategic',
                'name' => 'Kawasan Ekonomi Strategis Landasan Ulin',
                'description' => 'Kawasan strategis untuk pengembangan ekonomi dan industri di sekitar Bandara Syamsudin Noor',
                'priority' => 'Tinggi',
                'theme' => 'Ekonomi',
                'color' => '#dc2626',
                'coordinates' => json_encode([
                    [
                        [-3.4350, 114.8050],
                        [-3.4350, 114.8200],
                        [-3.4450, 114.8200],
                        [-3.4450, 114.8050],
                        [-3.4350, 114.8050]
                    ]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Pusat Kota Banjarbaru',
                'description' => 'Kawasan pusat pemerintahan dan pelayanan publik',
                'priority' => 'Tinggi',
                'theme' => 'Pemerintahan',
                'color' => '#dc2626',
                'coordinates' => json_encode([
                    [
                        [-3.4480, 114.8120],
                        [-3.4480, 114.8250],
                        [-3.4580, 114.8250],
                        [-3.4580, 114.8120],
                        [-3.4480, 114.8120]
                    ]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Zona Pendidikan Lambung Mangkurat',
                'description' => 'Kawasan pengembangan pendidikan tinggi dan penelitian',
                'priority' => 'Sedang',
                'theme' => 'Pendidikan',
                'color' => '#f59e0b',
                'coordinates' => json_encode([
                    [
                        [-3.4620, 114.8080],
                        [-3.4620, 114.8180],
                        [-3.4720, 114.8180],
                        [-3.4720, 114.8080],
                        [-3.4620, 114.8080]
                    ]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'thematic',
                'name' => 'Kawasan Wisata Danau Seran',
                'description' => 'Kawasan pengembangan pariwisata alam',
                'priority' => 'Sedang',
                'theme' => 'Pariwisata',
                'color' => '#f59e0b',
                'coordinates' => json_encode([
                    [
                        [-3.4300, 114.8300],
                        [-3.4300, 114.8400],
                        [-3.4400, 114.8400],
                        [-3.4400, 114.8300],
                        [-3.4300, 114.8300]
                    ]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'strategic',
                'name' => 'Kawasan Agrobisnis Cempaka',
                'description' => 'Kawasan pengembangan agrobisnis dan pertanian modern',
                'priority' => 'Sedang',
                'theme' => 'Pertanian',
                'color' => '#10b981',
                'coordinates' => json_encode([
                    [
                        [-3.4750, 114.8150],
                        [-3.4750, 114.8280],
                        [-3.4850, 114.8280],
                        [-3.4850, 114.8150],
                        [-3.4750, 114.8150]
                    ]
                ]),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data
        $this->db->table('rpjmd_priority_zones')->insertBatch($data);
    }
}
