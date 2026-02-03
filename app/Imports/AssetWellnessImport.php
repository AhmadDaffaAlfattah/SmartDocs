<?php

namespace App\Imports;

use App\Models\AssetWellness;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AssetWellnessImport implements ToCollection
{
    private $tahun;
    private $bulan;
    private $sentralOverride;

    public function __construct($tahun = null, $bulan = null, $sentral = null)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->sentralOverride = $sentral;
    }

    public function collection(Collection $rows)
    {
        $headerRowIndex = null;
        $headerMap = []; // index => key name

        // 1. Detect Header Row
        foreach ($rows as $index => $row) {
            // Check first 10 rows for a header
            if ($index > 10) break;

            $rowArray = $row->toArray();
            // Convert to string to search easily
            $rowString =  implode(' ', array_map('strtolower', array_filter($rowArray, 'is_string')));

            if (str_contains($rowString, 'kode mesin') || str_contains($rowString, 'kode_mesin')) {
                $headerRowIndex = $index;
                
                // Map columns
                foreach ($rowArray as $colIndex => $cellValue) {
                    if ($cellValue) {
                        // Slugify: "KODE MESIN" -> "kode_mesin"
                        $slug = Str::slug($cellValue, '_');
                        $headerMap[$colIndex] = $slug;
                    }
                }
                break;
            }
        }

        // Fallback: If no header found, assume standard format (Row 1 is headers, if > 1 rows)
        if ($headerRowIndex === null && $rows->count() > 0) {
            // Try row 0
            $headerRowIndex = 0;
            foreach ($rows[0] as $colIndex => $cellValue) {
                if ($cellValue) {
                     $slug = Str::slug($cellValue, '_');
                     $headerMap[$colIndex] = $slug;
                }
            }
        }

        Log::info('AssetWellnessImport: Header detected at row ' . $headerRowIndex);
        Log::info('AssetWellnessImport: Header Map', $headerMap);

        // 2. Process Data Rows
        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) continue; // Skip headers and pre-header rows

            // Convert row to associative array based on map
            $rowData = [];
            foreach ($headerMap as $colIndex => $key) {
                $rowData[$key] = $row[$colIndex] ?? null;
            }

            // Skip empty rows
            if (empty(array_filter($rowData))) continue;

            $this->processRow($rowData);
        }
    }

    private function processRow($row)
    {
        // Helper to find key
        $findKey = function($keys, $array) {
            foreach ((array)$keys as $k) {
                if (isset($array[$k])) return $array[$k];
            }
            return null;
        };

        $kodeMesin = $findKey(['kode_mesin', 'kode_mesin_silm', 'code'], $row);

        if (!$kodeMesin) {
            return;
        }

        $tahun = $this->tahun ?: ($findKey('tahun', $row) ?? date('Y'));
        $bulanRaw = $this->bulan ?: ($findKey('bulan', $row) ?? date('m'));
        $bulan = str_pad($bulanRaw, 2, '0', STR_PAD_LEFT);
        $sentral = $this->sentralOverride ?: ($findKey('sentral', $row) ?? null);

        AssetWellness::updateOrCreate(
            [
                'kode_mesin' => $kodeMesin,
                'tahun' => $tahun,
                'bulan' => $bulan,
            ],
            [
                 'unit_pembangkit_common' => $findKey(['unit_pembangkit_common', 'unit_pembangkit', 'unit_pembangkitcommon'], $row) ?? '-',
                 'tipe_aset' => $findKey(['tipe_aset', 'tipe'], $row),
                 'kode_mesin_silm' => $findKey('kode_mesin_silm', $row) ?? $kodeMesin,
                 'inisial_mesin' => $findKey(['inisial_mesin', 'inisial'], $row),
                 'daya_terpasang' => $findKey(['daya_terpasang', 'daya_mw'], $row) ?? 0,
                 'daya_mampu_netto' => $findKey(['daya_mampu_netto', 'daya_netto'], $row) ?? 0,
                 'daya_mampu_pasok' => $findKey(['daya_mampu_pasok', 'daya_pasok'], $row) ?? 0,
                 'total_equipment' => $findKey(['total_equipment', 'total_eq'], $row) ?? 0,
                 'safe' => $findKey('safe', $row) ?? 0,
                 'warning' => $findKey('warning', $row) ?? 0,
                 'fault' => $findKey('fault', $row) ?? 0,
                 'status_operasi' => $findKey(['status_operasi', 'status'], $row),
                 'sentral' => $sentral,
                 'keterangan' => $findKey(['keterangan', 'ket'], $row),
            ]
        );
    }
}
