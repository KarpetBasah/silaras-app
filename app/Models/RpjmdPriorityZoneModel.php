<?php

namespace App\Models;

use CodeIgniter\Model;

class RpjmdPriorityZoneModel extends Model
{
    protected $table = 'rpjmd_priority_zones';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'type',
        'name',
        'description',
        'priority',
        'theme',
        'color',
        'coordinates',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'type' => 'required|in_list[strategic,thematic]',
        'name' => 'required|string|max_length[255]',
        'description' => 'permit_empty|string',
        'priority' => 'permit_empty|in_list[Tinggi,Sedang,Rendah]',
        'theme' => 'permit_empty|string|max_length[100]',
        'color' => 'required|string|max_length[7]',
        'coordinates' => 'required',
        'is_active' => 'permit_empty|boolean'
    ];
    
    protected $validationMessages = [
        'type' => [
            'required' => 'Tipe zona harus dipilih',
            'in_list' => 'Tipe zona harus strategic atau thematic'
        ],
        'name' => [
            'required' => 'Nama zona harus diisi',
            'max_length' => 'Nama zona maksimal 255 karakter'
        ],
        'priority' => [
            'in_list' => 'Prioritas harus Tinggi, Sedang, atau Rendah'
        ],
        'theme' => [
            'max_length' => 'Tema maksimal 100 karakter'
        ],
        'color' => [
            'required' => 'Warna zona harus diisi',
            'max_length' => 'Format warna tidak valid'
        ],
        'coordinates' => [
            'required' => 'Koordinat zona harus diisi'
        ]
    ];
    
    /**
     * Get all strategic areas
     */
    public function getStrategicAreas($activeOnly = true)
    {
        $builder = $this->where('type', 'strategic');
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        return $builder->orderBy('priority', 'DESC')
                      ->orderBy('name', 'ASC')
                      ->findAll();
    }
    
    /**
     * Get all thematic zones
     */
    public function getThematicZones($activeOnly = true)
    {
        $builder = $this->where('type', 'thematic');
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        return $builder->orderBy('theme', 'ASC')
                      ->orderBy('name', 'ASC')
                      ->findAll();
    }
    
    /**
     * Get zones by type
     */
    public function getByType($type, $activeOnly = true)
    {
        $builder = $this->where('type', $type);
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get zones by priority
     */
    public function getByPriority($priority, $activeOnly = true)
    {
        $builder = $this->where('priority', $priority);
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        return $builder->orderBy('name', 'ASC')->findAll();
    }
    
    /**
     * Get zones by theme
     */
    public function getByTheme($theme, $activeOnly = true)
    {
        $builder = $this->where('theme', $theme);
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        return $builder->orderBy('name', 'ASC')->findAll();
    }
    
    /**
     * Search zones by keyword
     */
    public function search($keyword, $activeOnly = true)
    {
        $builder = $this->groupStart()
                       ->like('name', $keyword)
                       ->orLike('description', $keyword)
                       ->orLike('theme', $keyword)
                       ->groupEnd();
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        return $builder->orderBy('type', 'ASC')
                      ->orderBy('name', 'ASC')
                      ->findAll();
    }
    
    /**
     * Get zone statistics
     */
    public function getStatistics()
    {
        $total = $this->where('is_active', true)->countAllResults();
        
        $strategic = $this->where('type', 'strategic')
                          ->where('is_active', true)
                          ->countAllResults();
        
        $thematic = $this->where('type', 'thematic')
                         ->where('is_active', true)
                         ->countAllResults();
        
        $highPriority = $this->where('priority', 'Tinggi')
                             ->where('is_active', true)
                             ->countAllResults();
        
        return [
            'total' => $total,
            'strategic' => $strategic,
            'thematic' => $thematic,
            'high_priority' => $highPriority,
            'strategic_percentage' => $total > 0 ? round(($strategic / $total) * 100, 2) : 0,
            'high_priority_percentage' => $total > 0 ? round(($highPriority / $total) * 100, 2) : 0
        ];
    }
    
    /**
     * Get unique themes
     */
    public function getUniqueThemes()
    {
        return $this->select('theme')
                    ->where('theme IS NOT NULL')
                    ->where('theme !=', '')
                    ->where('is_active', true)
                    ->groupBy('theme')
                    ->orderBy('theme', 'ASC')
                    ->findColumn('theme');
    }
    
    /**
     * Get zones within bounding box
     */
    public function getZonesWithinBounds($north, $south, $east, $west, $activeOnly = true)
    {
        // This is a simplified version - for proper implementation,
        // you would need to use PostGIS or similar spatial database functions
        $builder = $this;
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }
        
        // For now, return all zones - in production, implement proper spatial queries
        return $builder->findAll();
    }
    
    /**
     * Check if point is within any zone
     */
    public function getZonesContainingPoint($latitude, $longitude, $activeOnly = true)
    {
        $zones = $activeOnly ? $this->where('is_active', true)->findAll() : $this->findAll();
        $containingZones = [];
        
        foreach ($zones as $zone) {
            $coordinates = json_decode($zone['coordinates'], true);
            
            if ($this->isPointInPolygon($latitude, $longitude, $coordinates)) {
                $containingZones[] = $zone;
            }
        }
        
        return $containingZones;
    }
    
    /**
     * Point-in-polygon algorithm (Ray casting)
     */
    private function isPointInPolygon($lat, $lng, $polygon)
    {
        if (empty($polygon) || !is_array($polygon)) {
            return false;
        }
        
        // Handle multiple polygons (first one is the main polygon)
        $coords = $polygon[0] ?? $polygon;
        
        if (count($coords) < 3) {
            return false;
        }
        
        $inside = false;
        $j = count($coords) - 1;
        
        for ($i = 0; $i < count($coords); $j = $i++) {
            if ((($coords[$i][0] > $lat) != ($coords[$j][0] > $lat)) &&
                ($lng < ($coords[$j][1] - $coords[$i][1]) * ($lat - $coords[$i][0]) / ($coords[$j][0] - $coords[$i][0]) + $coords[$i][1])) {
                $inside = !$inside;
            }
        }
        
        return $inside;
    }
    
    /**
     * Get active zones (alias for backward compatibility)
     */
    public function getActiveZones()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Get zones formatted for Leaflet
     */
    public function getForLeaflet($activeOnly = true)
    {
        $zones = $activeOnly ? $this->where('is_active', true)->findAll() : $this->findAll();
        
        $formatted = [];
        foreach ($zones as $zone) {
            $zone['coordinates'] = json_decode($zone['coordinates'], true);
            $formatted[] = $zone;
        }
        
        return $formatted;
    }
    
    /**
     * Activate/deactivate zone
     */
    public function toggleActive($id)
    {
        $zone = $this->find($id);
        if ($zone) {
            return $this->update($id, ['is_active' => !$zone['is_active']]);
        }
        return false;
    }
}