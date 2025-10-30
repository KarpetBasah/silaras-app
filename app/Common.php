<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('format_anggaran')) {
    /**
     * Format anggaran dengan deteksi nilai otomatis
     * 
     * @param int|string $anggaran Nilai anggaran
     * @return string Formatted anggaran string
     */
    function format_anggaran($anggaran): string
    {
        // Convert to numeric if string
        $nilai = is_string($anggaran) ? (float) str_replace(['.', ','], ['', '.'], $anggaran) : (float) $anggaran;
        
        if ($nilai == 0) {
            return 'Rp 0';
        }
        
        // Triliun (12 zeros)
        if ($nilai >= 1000000000000) {
            $formatted = $nilai / 1000000000000;
            $unit = 'T';
        }
        // Miliar (9 zeros) 
        elseif ($nilai >= 1000000000) {
            $formatted = $nilai / 1000000000;
            $unit = 'M';
        }
        // Juta (6 zeros)
        elseif ($nilai >= 1000000) {
            $formatted = $nilai / 1000000;
            $unit = 'Juta';
        }
        // Ribu (3 zeros)
        elseif ($nilai >= 1000) {
            $formatted = $nilai / 1000;
            $unit = 'Ribu';
        }
        // Kurang dari ribu
        else {
            return 'Rp ' . number_format($nilai, 0, ',', '.');
        }
        
        // Remove trailing zeros and decimal point if whole number
        if ($formatted == floor($formatted)) {
            return 'Rp ' . number_format($formatted, 0) . ' ' . $unit;
        } else {
            return 'Rp ' . number_format($formatted, 1) . ' ' . $unit;
        }
    }
}
