<?php

namespace App\Controllers;

class TestMonitoring extends BaseController
{
    public function testLatestData()
    {
        $db = \Config\Database::connect();
        
        echo "<h2>Testing Latest Monitoring Data Logic</h2>";
        
        // 1. Check total monitoring records
        $total = $db->query('SELECT COUNT(*) as total FROM program_monitoring')->getRow()->total;
        echo "<p><strong>1. Total monitoring records:</strong> $total</p>";
        
        // 2. Check programs with monitoring data
        $programsWithMonitoring = $db->query('SELECT COUNT(DISTINCT program_id) as total FROM program_monitoring')->getRow()->total;
        echo "<p><strong>2. Programs with monitoring data:</strong> $programsWithMonitoring</p>";
        
        // 3. Test the latest monitoring subquery
        echo "<h3>3. Latest monitoring dates per program:</h3>";
        $latestDates = $db->query('
            SELECT program_id, MAX(tanggal_monitoring) as latest_date, COUNT(*) as total_records
            FROM program_monitoring 
            GROUP BY program_id
            ORDER BY program_id
        ')->getResult();
        
        echo "<ul>";
        foreach ($latestDates as $row) {
            echo "<li>Program ID {$row->program_id}: Latest = {$row->latest_date}, Total records = {$row->total_records}</li>";
        }
        echo "</ul>";
        
        // 4. Test the actual model method
        echo "<h3>4. Test ProgramMonitoringModel::getLatestMonitoringByProgram():</h3>";
        
        try {
            $monitoringModel = new \App\Models\ProgramMonitoringModel();
            $results = $monitoringModel->getLatestMonitoringByProgram();
            echo "<p><strong>Query returned:</strong> " . count($results) . " records</p>";
            
            echo "<h4>Results:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Program ID</th><th>Program Name</th><th>Tanggal Monitoring</th><th>Progress Fisik</th><th>Status</th></tr>";
            
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>{$row['program_id']}</td>";
                echo "<td>{$row['nama_kegiatan']}</td>";
                echo "<td>{$row['tanggal_monitoring']}</td>";
                echo "<td>{$row['progress_fisik']}%</td>";
                echo "<td>{$row['status_lapangan']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // 5. Check for duplicates
            echo "<h3>5. Verification - Check for duplicate program_ids:</h3>";
            $programIds = array_column($results, 'program_id');
            $duplicates = array_diff_assoc($programIds, array_unique($programIds));
            if (empty($duplicates)) {
                echo "<p style='color: green;'>✅ No duplicates found - each program appears only once</p>";
            } else {
                echo "<p style='color: red;'>❌ Duplicates found: " . implode(', ', $duplicates) . "</p>";
            }
            
            // 6. Show sample data structure
            echo "<h3>6. Sample data structure:</h3>";
            if (!empty($results)) {
                $sample = $results[0];
                echo "<ul>";
                echo "<li><strong>Program:</strong> {$sample['nama_kegiatan']}</li>";
                echo "<li><strong>Date:</strong> {$sample['tanggal_monitoring']}</li>";
                echo "<li><strong>Progress Fisik:</strong> {$sample['progress_fisik']}%</li>";
                echo "<li><strong>Progress Keuangan:</strong> {$sample['progress_keuangan']}%</li>";
                echo "<li><strong>Status:</strong> {$sample['status_lapangan']}</li>";
                echo "<li><strong>Coordinates:</strong> {$sample['program_lat']}, {$sample['program_lng']}</li>";
                echo "</ul>";
            }
            
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
        
        echo "<h3>7. Raw SQL Query Being Used:</h3>";
        echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto;'>";
        echo htmlentities("
SELECT program_monitoring.*, 
       program.nama_kegiatan, program.kode_program, program.anggaran_total,
       program.koordinat_lat as program_lat, program.koordinat_lng as program_lng,
       opd.nama_singkat as opd_nama, sektor.nama_sektor, sektor.color as sektor_color,
       sektor.icon as sektor_icon
FROM program_monitoring
JOIN program ON program.id = program_monitoring.program_id
JOIN opd ON opd.id = program.opd_id
JOIN sektor ON sektor.id = program.sektor_id
JOIN (SELECT program_id, MAX(tanggal_monitoring) as latest_date 
      FROM program_monitoring 
      GROUP BY program_id) latest 
      ON latest.program_id = program_monitoring.program_id 
      AND latest.latest_date = program_monitoring.tanggal_monitoring
ORDER BY program_monitoring.tanggal_monitoring DESC
        ");
        echo "</pre>";
    }
}