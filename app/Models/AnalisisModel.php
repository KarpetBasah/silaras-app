<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalisisModel extends Model
{
    protected $table = 'program'; // We'll use existing program table
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $programModel;
    protected $rpjmdPriorityZoneModel;

    public function __construct()
    {
        parent::__construct();
        $this->programModel = new ProgramModel();
        $this->rpjmdPriorityZoneModel = new RpjmdPriorityZoneModel();
    }

    /**
     * Detect overlapping programs within specified radius
     */
    public function detectOverlaps($filters = [], $radius = 100)
    {
        $programs = $this->programModel->getProgramsWithRelations($filters);
        $overlaps = [];

        for ($i = 0; $i < count($programs); $i++) {
            for ($j = $i + 1; $j < count($programs); $j++) {
                $program1 = $programs[$i];
                $program2 = $programs[$j];

                $distance = $this->calculateHaversineDistance(
                    $program1['koordinat_lat'], 
                    $program1['koordinat_lng'],
                    $program2['koordinat_lat'], 
                    $program2['koordinat_lng']
                );

                if ($distance <= $radius) {
                    $overlap = [
                        'program1_id' => $program1['id'],
                        'program2_id' => $program2['id'],
                        'distance' => round($distance, 2),
                        'conflict_level' => $this->getConflictLevel($program1, $program2, $distance),
                        'conflict_type' => $this->getConflictType($program1, $program2),
                        'impact_score' => $this->calculateImpactScore($program1, $program2),
                        'recommendation' => $this->generateOverlapRecommendation($program1, $program2, $distance)
                    ];

                    $overlaps[] = array_merge($overlap, [
                        'program1' => $this->formatProgramData($program1),
                        'program2' => $this->formatProgramData($program2)
                    ]);
                }
            }
        }

        return $overlaps;
    }

    /**
     * Identify gaps in program coverage
     */
    public function identifyGaps($filters = [], $gridSize = 0.005)
    {
        $programs = $this->programModel->getProgramsWithRelations($filters);
        $boundary = $this->getBanjarbaru_Boundary();
        
        // Create analysis grid
        $grid = $this->generateAnalysisGrid($boundary, $gridSize);
        
        // Get priority zones
        $priorityZones = $this->rpjmdPriorityZoneModel->getForLeaflet();
        
        // Mark covered areas
        $coveredAreas = $this->markCoveredAreas($programs, $gridSize);
        
        $gaps = [];
        foreach ($grid as $cell) {
            $cellKey = $this->getCellKey($cell['lat'], $cell['lng'], $gridSize);
            
            if (!isset($coveredAreas[$cellKey])) {
                // Check if this area is in priority zone
                $priorityInfo = $this->getPriorityInfo($cell['lat'], $cell['lng'], $priorityZones);
                
                if ($priorityInfo['is_priority']) {
                    $gaps[] = [
                        'coordinates' => [
                            'lat' => $cell['lat'],
                            'lng' => $cell['lng']
                        ],
                        'priority_level' => $priorityInfo['priority_level'],
                        'priority_zone' => $priorityInfo['zone_name'],
                        'recommended_sectors' => $this->recommendSectors($cell['lat'], $cell['lng'], $priorityInfo),
                        'gap_type' => $priorityInfo['gap_type'],
                        'urgency_score' => $this->calculateUrgencyScore($priorityInfo)
                    ];
                }
            }
        }

        return [
            'gaps' => $gaps,
            'statistics' => $this->calculateGapStatistics($grid, $coveredAreas, $gaps),
            'recommendations' => $this->generateGapRecommendations($gaps)
        ];
    }

    /**
     * Analyze RPJMD alignment
     */
    public function analyzeRPJMDAlignment($filters = [])
    {
        $programs = $this->programModel->getProgramsWithRelations($filters);
        $priorityZones = $this->rpjmdPriorityZoneModel->getForLeaflet();
        
        $aligned = [];
        $misaligned = [];
        
        foreach ($programs as $program) {
            $alignmentInfo = $this->checkRPJMDAlignment(
                $program['koordinat_lat'],
                $program['koordinat_lng'],
                $program,
                $priorityZones
            );
            
            $programData = $this->formatProgramData($program);
            $programData['alignment_info'] = $alignmentInfo;
            
            if ($alignmentInfo['is_aligned']) {
                $aligned[] = $programData;
            } else {
                $misaligned[] = $programData;
            }
        }

        return [
            'aligned' => $aligned,
            'misaligned' => $misaligned,
            'statistics' => [
                'total_programs' => count($programs),
                'aligned_count' => count($aligned),
                'misaligned_count' => count($misaligned),
                'alignment_percentage' => count($programs) > 0 ? 
                    round((count($aligned) / count($programs)) * 100, 2) : 0
            ],
            'by_sector' => $this->analyzeAlignmentBySectorFixed($aligned, $misaligned),
            'by_opd' => $this->analyzeAlignmentByOPDFixed($aligned, $misaligned)
        ];
    }

    /**
     * Calculate comprehensive analysis statistics
     */
    public function calculateAnalysisStatistics($filters = [])
    {
        $programs = $this->programModel->getProgramsWithRelations($filters);
        $overlaps = $this->detectOverlaps($filters);
        $alignment = $this->analyzeRPJMDAlignment($filters);
        $gaps = $this->identifyGaps($filters);

        // Sector analysis
        $sectorStats = $this->calculateSectorStatistics($programs);
        
        // Budget analysis
        $budgetStats = $this->calculateBudgetAnalysis($programs, $overlaps);
        
        return [
            'overview' => [
                'total_programs' => count($programs),
                'overlapping_programs' => count($overlaps),
                'alignment_percentage' => $alignment['statistics']['alignment_percentage'] ?? 0,
                'coverage_gaps' => isset($gaps['gaps']) ? count($gaps['gaps']) : 0,
                'total_budget' => array_sum(array_column($programs, 'anggaran_total')),
                'potential_savings' => $this->calculatePotentialSavings($overlaps)
            ],
            'sectors' => $sectorStats,
            'budget' => $budgetStats,
            'quality_indicators' => $this->calculateQualityIndicators($programs, $overlaps, $alignment),
            'recommendations' => $this->generateComprehensiveRecommendations($programs, $overlaps, $alignment, $gaps)
        ];
    }

    /**
     * Calculate sector-wise statistics
     */
    private function calculateSectorStatistics($programs)
    {
        $sectorCounts = [];
        $totalBudget = array_sum(array_column($programs, 'anggaran_total'));
        
        foreach ($programs as $program) {
            $sektor = $program['nama_sektor'];
            if (!isset($sectorCounts[$sektor])) {
                $sectorCounts[$sektor] = [
                    'count' => 0,
                    'budget' => 0
                ];
            }
            $sectorCounts[$sektor]['count']++;
            $sectorCounts[$sektor]['budget'] += $program['anggaran_total'];
        }
        
        $sectorStats = [];
        foreach ($sectorCounts as $sektor => $data) {
            $sectorStats[] = [
                'sektor' => $sektor,
                'jumlah' => $data['count'],
                'persentase' => count($programs) > 0 ? 
                    round(($data['count'] / count($programs)) * 100, 2) : 0,
                'budget' => $data['budget'],
                'budget_percentage' => $totalBudget > 0 ? 
                    round(($data['budget'] / $totalBudget) * 100, 2) : 0
            ];
        }
        
        return $sectorStats;
    }

    /**
     * Calculate budget analysis
     */
    private function calculateBudgetAnalysis($programs, $overlaps)
    {
        $totalBudget = array_sum(array_column($programs, 'anggaran_total'));
        $totalRealization = array_sum(array_column($programs, 'anggaran_realisasi'));
        
        return [
            'total_budget' => $totalBudget,
            'total_realization' => $totalRealization,
            'realization_percentage' => $totalBudget > 0 ? 
                round(($totalRealization / $totalBudget) * 100, 2) : 0,
            'potential_savings' => $this->calculatePotentialSavings($overlaps)
        ];
    }

    /**
     * Calculate potential savings from overlap resolution
     */
    private function calculatePotentialSavings($overlaps)
    {
        $savings = 0;
        foreach ($overlaps as $overlap) {
            if ($overlap['conflict_level'] === 'Tinggi') {
                // Estimate 20% savings for high conflicts
                $minBudget = min($overlap['program1']['anggaran'], $overlap['program2']['anggaran']);
                $savings += $minBudget * 0.2;
            }
        }
        return $savings;
    }

    /**
     * Calculate quality indicators
     */
    private function calculateQualityIndicators($programs, $overlaps, $alignment)
    {
        $totalPrograms = count($programs);
        $conflictCount = count($overlaps);
        $alignmentPercentage = $alignment['statistics']['alignment_percentage'] ?? 0;
        
        // Quality score calculation (0-100)
        $qualityScore = 100;
        
        // Deduct points for conflicts
        if ($totalPrograms > 0) {
            $conflictRatio = $conflictCount / $totalPrograms;
            $qualityScore -= $conflictRatio * 30; // Max 30 points deduction
        }
        
        // Deduct points for poor alignment
        $qualityScore -= (100 - $alignmentPercentage) * 0.4; // Max 40 points deduction
        
        $qualityScore = max(0, round($qualityScore, 1));
        
        return [
            'overall_quality_score' => $qualityScore,
            'conflict_ratio' => $totalPrograms > 0 ? round($conflictCount / $totalPrograms, 3) : 0,
            'alignment_score' => $alignmentPercentage,
            'efficiency_index' => $this->calculateEfficiencyIndex($programs, $overlaps)
        ];
    }

    /**
     * Calculate efficiency index
     */
    private function calculateEfficiencyIndex($programs, $overlaps)
    {
        if (count($programs) == 0) return 0;
        
        $totalBudget = array_sum(array_column($programs, 'anggaran_total'));
        $wastedBudget = $this->calculatePotentialSavings($overlaps);
        
        return $totalBudget > 0 ? round((1 - $wastedBudget / $totalBudget) * 100, 2) : 100;
    }

    /**
     * Generate comprehensive recommendations
     */
    private function generateComprehensiveRecommendations($programs, $overlaps, $alignment, $gaps)
    {
        $recommendations = [];
        
        // High conflict recommendation
        $highConflicts = array_filter($overlaps, function($overlap) {
            return $overlap['conflict_level'] === 'Tinggi';
        });
        
        if (count($highConflicts) > 0) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'Resolusi Konflik Tinggi',
                'description' => 'Terdapat ' . count($highConflicts) . ' konflik tinggi yang memerlukan perhatian segera',
                'action' => 'Lakukan koordinasi antar OPD untuk menyelesaikan tumpang tindih program'
            ];
        }
        
        // Low alignment recommendation
        $alignmentPercentage = $alignment['statistics']['alignment_percentage'] ?? 0;
        if ($alignmentPercentage < 70) {
            $recommendations[] = [
                'type' => 'important',
                'title' => 'Peningkatan Keselarasan RPJMD',
                'description' => 'Keselarasan program dengan RPJMD masih rendah (' . $alignmentPercentage . '%)',
                'action' => 'Review lokasi program agar sesuai dengan zona prioritas RPJMD'
            ];
        }
        
        // Gap areas recommendation
        if (isset($gaps['gaps']) && count($gaps['gaps']) > 0) {
            $highPriorityGaps = array_filter($gaps['gaps'], function($gap) {
                return $gap['priority_level'] === 'Tinggi';
            });
            
            if (count($highPriorityGaps) > 0) {
                $recommendations[] = [
                    'type' => 'normal',
                    'title' => 'Area Prioritas Tanpa Program',
                    'description' => 'Terdapat ' . count($highPriorityGaps) . ' area prioritas tinggi tanpa program',
                    'action' => 'Pertimbangkan penambahan program di area prioritas yang kosong'
                ];
            }
        }
        
        return $recommendations;
    }

    /**
     * UTILITY METHODS
     */

    private function calculateHaversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    private function getConflictLevel($program1, $program2, $distance)
    {
        // High conflict: same sector + close distance
        if ($program1['sektor_id'] == $program2['sektor_id'] && $distance < 50) {
            return 'Tinggi';
        }
        
        // Medium conflict: different sector but very close OR same sector medium distance
        if (($program1['sektor_id'] != $program2['sektor_id'] && $distance < 25) ||
            ($program1['sektor_id'] == $program2['sektor_id'] && $distance < 100)) {
            return 'Sedang';
        }
        
        return 'Rendah';
    }

    private function getConflictType($program1, $program2)
    {
        if ($program1['sektor_id'] == $program2['sektor_id']) {
            if ($program1['opd_id'] == $program2['opd_id']) {
                return 'Internal OPD';
            } else {
                return 'Antar OPD - Sektor Sama';
            }
        } else {
            return 'Antar Sektor';
        }
    }

    private function calculateImpactScore($program1, $program2)
    {
        $budgetFactor = (($program1['anggaran_total'] + $program2['anggaran_total']) / 2) / 1000000; // in millions
        $sectorFactor = $program1['sektor_id'] == $program2['sektor_id'] ? 2 : 1;
        
        return min(10, sqrt($budgetFactor) * $sectorFactor);
    }

    private function generateOverlapRecommendation($program1, $program2, $distance)
    {
        if ($program1['sektor_id'] == $program2['sektor_id']) {
            if ($program1['opd_id'] == $program2['opd_id']) {
                return 'Pertimbangkan penggabungan atau relokasi program dalam OPD yang sama';
            } else {
                return 'Koordinasi antar OPD diperlukan untuk menghindari duplikasi sektor';
            }
        } else {
            if ($distance < 50) {
                return 'Sinergi pelaksanaan untuk memaksimalkan dampak pembangunan terpadu';
            } else {
                return 'Monitor potensi konflik dan cari peluang sinergi';
            }
        }
    }

    private function getBanjarbaru_Boundary()
    {
        // Banjarbaru city boundary (approximate)
        return [
            'min_lat' => -3.5200,
            'max_lat' => -3.3800,
            'min_lng' => 114.7500,
            'max_lng' => 114.8800
        ];
    }

    private function generateAnalysisGrid($boundary, $gridSize)
    {
        $grid = [];
        
        $lat = $boundary['min_lat'];
        while ($lat <= $boundary['max_lat']) {
            $lng = $boundary['min_lng'];
            while ($lng <= $boundary['max_lng']) {
                $grid[] = [
                    'lat' => round($lat, 6),
                    'lng' => round($lng, 6)
                ];
                $lng += $gridSize;
            }
            $lat += $gridSize;
        }
        
        return $grid;
    }

    private function markCoveredAreas($programs, $gridSize, $coverageRadius = 500)
    {
        $covered = [];
        
        foreach ($programs as $program) {
            $programLat = $program['koordinat_lat'];
            $programLng = $program['koordinat_lng'];
            
            // Mark all grid cells within coverage radius
            $latRange = $coverageRadius / 111320; // approximate degrees per meter
            $lngRange = $coverageRadius / (111320 * cos(deg2rad($programLat)));
            
            $minLat = $programLat - $latRange;
            $maxLat = $programLat + $latRange;
            $minLng = $programLng - $lngRange;
            $maxLng = $programLng + $lngRange;
            
            $lat = $minLat;
            while ($lat <= $maxLat) {
                $lng = $minLng;
                while ($lng <= $maxLng) {
                    $distance = $this->calculateHaversineDistance(
                        $programLat, $programLng, $lat, $lng
                    );
                    
                    if ($distance <= $coverageRadius) {
                        $cellKey = $this->getCellKey($lat, $lng, $gridSize);
                        $covered[$cellKey] = true;
                    }
                    
                    $lng += $gridSize;
                }
                $lat += $gridSize;
            }
        }
        
        return $covered;
    }

    private function getCellKey($lat, $lng, $gridSize)
    {
        $gridLat = floor($lat / $gridSize) * $gridSize;
        $gridLng = floor($lng / $gridSize) * $gridSize;
        return round($gridLat, 6) . ',' . round($gridLng, 6);
    }

    private function getPriorityInfo($lat, $lng, $priorityZones)
    {
        foreach ($priorityZones as $zone) {
            if ($this->isPointInZone($lat, $lng, $zone['coordinates'])) {
                return [
                    'is_priority' => true,
                    'priority_level' => $zone['priority'] ?? 'Sedang',
                    'zone_name' => $zone['name'],
                    'zone_type' => $zone['type'],
                    'theme' => $zone['theme'] ?? '',
                    'gap_type' => $this->determineGapType($zone['type'], $zone['theme'])
                ];
            }
        }
        
        return [
            'is_priority' => false,
            'priority_level' => 'Rendah',
            'zone_name' => 'Area Non-Prioritas',
            'zone_type' => 'general',
            'theme' => '',
            'gap_type' => 'General'
        ];
    }

    private function isPointInZone($lat, $lng, $coordinates)
    {
        if (empty($coordinates) || !is_array($coordinates)) {
            return false;
        }
        
        // Handle GeoJSON format
        $coords = $coordinates;
        if (isset($coordinates[0]) && is_array($coordinates[0])) {
            $coords = $coordinates[0];
        }
        
        return $this->pointInPolygon($lat, $lng, $coords);
    }

    private function pointInPolygon($lat, $lng, $polygon)
    {
        if (count($polygon) < 3) {
            return false;
        }
        
        $inside = false;
        $j = count($polygon) - 1;
        
        for ($i = 0; $i < count($polygon); $j = $i++) {
            $xi = $polygon[$i][1]; // longitude
            $yi = $polygon[$i][0]; // latitude
            $xj = $polygon[$j][1]; // longitude
            $yj = $polygon[$j][0]; // latitude
            
            if ((($yi > $lat) != ($yj > $lat)) &&
                ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }
        }
        
        return $inside;
    }

    private function determineGapType($zoneType, $theme)
    {
        if ($zoneType === 'strategic') {
            return 'Kawasan Strategis';
        } elseif ($zoneType === 'thematic') {
            return 'Area Tematik - ' . ucfirst($theme);
        }
        return 'Umum';
    }

    private function recommendSectors($lat, $lng, $priorityInfo)
    {
        // Basic logic - can be enhanced with more sophisticated rules
        $recommendations = [];
        
        $theme = strtolower($priorityInfo['theme']);
        
        if (strpos($theme, 'pendidikan') !== false) {
            $recommendations = ['Pendidikan', 'Infrastruktur Jalan'];
        } elseif (strpos($theme, 'kesehatan') !== false) {
            $recommendations = ['Kesehatan', 'Infrastruktur Jalan'];
        } elseif (strpos($theme, 'ekonomi') !== false) {
            $recommendations = ['Ekonomi', 'Infrastruktur Jalan', 'Perdagangan'];
        } else {
            $recommendations = ['Infrastruktur Jalan', 'Lingkungan Hidup'];
        }
        
        return $recommendations;
    }

    private function calculateUrgencyScore($priorityInfo)
    {
        $score = 0;
        
        // Base score by priority level
        switch ($priorityInfo['priority_level']) {
            case 'Tinggi':
                $score += 3;
                break;
            case 'Sedang':
                $score += 2;
                break;
            case 'Rendah':
                $score += 1;
                break;
        }
        
        // Additional score for strategic areas
        if ($priorityInfo['zone_type'] === 'strategic') {
            $score += 1;
        }
        
        return $score;
    }

    private function formatProgramData($program)
    {
        return [
            'id' => $program['id'],
            'nama_kegiatan' => $program['nama_kegiatan'],
            'sektor' => $program['nama_sektor'],
            'opd' => $program['nama_opd'],
            'koordinat' => [
                'lat' => (float)$program['koordinat_lat'],
                'lng' => (float)$program['koordinat_lng']
            ],
            'anggaran' => $program['anggaran_total'],
            'tahun' => $program['tahun_pelaksanaan'],
            'status' => $program['status']
        ];
    }

    private function checkRPJMDAlignment($lat, $lng, $program, $priorityZones)
    {
        $priorityInfo = $this->getPriorityInfo($lat, $lng, $priorityZones);
        
        $alignmentScore = 0;
        $reasons = [];
        
        // Check if in priority zone
        if ($priorityInfo['is_priority']) {
            $alignmentScore += 3;
            $reasons[] = 'Berada dalam zona prioritas RPJMD';
        } else {
            $reasons[] = 'Tidak berada dalam zona prioritas RPJMD';
        }
        
        // Check if program priority matches zone priority
        if ($program['is_prioritas'] && $priorityInfo['priority_level'] === 'Tinggi') {
            $alignmentScore += 2;
            $reasons[] = 'Program prioritas sesuai zona prioritas tinggi';
        }
        
        return [
            'is_aligned' => $alignmentScore >= 3,
            'alignment_score' => $alignmentScore,
            'priority_zone' => $priorityInfo['zone_name'],
            'zone_priority' => $priorityInfo['priority_level'],
            'reasons' => $reasons
        ];
    }

    private function calculateGapStatistics($grid, $coveredAreas, $gaps)
    {
        return [
            'total_grid_cells' => count($grid),
            'covered_cells' => count($coveredAreas),
            'gap_cells' => count($gaps),
            'coverage_percentage' => count($grid) > 0 ? 
                round((count($coveredAreas) / count($grid)) * 100, 2) : 0,
            'priority_gaps' => count($gaps),
            'high_priority_gaps' => count(array_filter($gaps, function($gap) {
                return $gap['priority_level'] === 'Tinggi';
            }))
        ];
    }

    private function generateGapRecommendations($gaps)
    {
        $recommendations = [];
        
        $highPriorityGaps = array_filter($gaps, function($gap) {
            return $gap['priority_level'] === 'Tinggi';
        });
        
        if (count($highPriorityGaps) > 0) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'Area Prioritas Tinggi Tanpa Program',
                'description' => 'Terdapat ' . count($highPriorityGaps) . ' area prioritas tinggi yang belum memiliki program pembangunan',
                'action' => 'Segera lakukan perencanaan program untuk area-area tersebut'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Analyze alignment by sector (fixed version)
     */
    private function analyzeAlignmentBySectorFixed($aligned, $misaligned)
    {
        $sectorData = [];
        
        // Process aligned programs
        foreach ($aligned as $program) {
            $sector = $program['sektor_nama'] ?? 'Tidak Diketahui';
            if (!isset($sectorData[$sector])) {
                $sectorData[$sector] = ['aligned' => 0, 'misaligned' => 0, 'total' => 0];
            }
            $sectorData[$sector]['aligned']++;
            $sectorData[$sector]['total']++;
        }
        
        // Process misaligned programs
        foreach ($misaligned as $program) {
            $sector = $program['sektor_nama'] ?? 'Tidak Diketahui';
            if (!isset($sectorData[$sector])) {
                $sectorData[$sector] = ['aligned' => 0, 'misaligned' => 0, 'total' => 0];
            }
            $sectorData[$sector]['misaligned']++;
            $sectorData[$sector]['total']++;
        }
        
        // Calculate percentages
        foreach ($sectorData as $sector => &$data) {
            $data['alignment_percentage'] = $data['total'] > 0 ? 
                round(($data['aligned'] / $data['total']) * 100, 2) : 0;
        }
        
        return $sectorData;
    }

    /**
     * Analyze alignment by OPD (fixed version)
     */
    private function analyzeAlignmentByOPDFixed($aligned, $misaligned)
    {
        $opdData = [];
        
        // Process aligned programs
        foreach ($aligned as $program) {
            $opd = $program['opd_nama'] ?? 'Tidak Diketahui';
            if (!isset($opdData[$opd])) {
                $opdData[$opd] = ['aligned' => 0, 'misaligned' => 0, 'total' => 0];
            }
            $opdData[$opd]['aligned']++;
            $opdData[$opd]['total']++;
        }
        
        // Process misaligned programs
        foreach ($misaligned as $program) {
            $opd = $program['opd_nama'] ?? 'Tidak Diketahui';
            if (!isset($opdData[$opd])) {
                $opdData[$opd] = ['aligned' => 0, 'misaligned' => 0, 'total' => 0];
            }
            $opdData[$opd]['misaligned']++;
            $opdData[$opd]['total']++;
        }
        
        // Calculate percentages
        foreach ($opdData as $opd => &$data) {
            $data['alignment_percentage'] = $data['total'] > 0 ? 
                round(($data['aligned'] / $data['total']) * 100, 2) : 0;
        }
        
        return $opdData;
    }
}