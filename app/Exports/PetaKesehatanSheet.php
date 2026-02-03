<?php

namespace App\Exports;

use App\Models\AssetWellness;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class PetaKesehatanSheet implements FromCollection, WithTitle, WithStyles, WithEvents
{
    protected $tahun;
    protected $bulan;
    protected $sentral;

    public function __construct($tahun = null, $bulan = null, $sentral = null)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->sentral = $sentral;
    }

    public function collection()
    {
        // We will build the collection manually based on the structure
        return new Collection([]);
    }

    public function title(): string
    {
        return 'Peta Kesehatan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // DATA PREPARATION
                $query = AssetWellness::query();
                if ($this->tahun) $query->where('tahun', $this->tahun);
                if ($this->bulan) $query->where('bulan', $this->bulan);
                if ($this->sentral) $query->where('sentral', $this->sentral);
                $assets = $query->get();

                // Build Asset Lookup
                $assetLookup = [];
                $assetLookupBySilm = [];
                foreach($assets as $asset) {
                    $kodemesinnorm = strtoupper(preg_replace('/\s+/', '', trim($asset->kode_mesin)));
                    $silmnorm = strtoupper(preg_replace('/\s+/', '', trim($asset->kode_mesin_silm ?? '')));
                    if (!empty($kodemesinnorm)) $assetLookup[$kodemesinnorm] = $asset;
                    if (!empty($silmnorm)) $assetLookupBySilm[$silmnorm] = $asset;
                }

                $ulStructure = [
                    'UL NUNUKAN' => [
                        'PLTD Kuala Lapang' => ['1001', '1002', '1004', '1005', '1007', '1015'],
                        'PLTD Sei Bilal' => ['2001', '2002', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014'],
                        'PLTD Sebatik' => ['3001', '3003', '3006'],
                        'PLTD Tulin Onsoi' => ['4001', '4002', '4003', '4004', '4005', '4006', '4007', '4021'],
                    ],
                    'UL TANJUNG SELOR' => [
                        'PLTD Sambaliung' => ['5001', '5005', '5006', '5007', '5010', '5011', '5012', '5015'],
                        'PLTD Sei Buaya' => ['6002', '6007', '6008', '6009'],
                        'PLTD Bunyu' => [],
                        'PLTD Talisayan' => ['7001', '7002', '7003', '7005'],
                    ],
                    'UL TARAKAN' => [
                        'PLTMG GN Belah' => [],
                    ],
                    'UL BALIKPAPAN' => [
                        'PLTD Batakan' => ['1001', '1002'],
                        'PLTD Gunung Malang' => ['5004', '5008', '5009'],
                        'PLTD Tj Aru' => ['3010', '3011', '8005', '8010', '8011'],
                    ],
                ];

                // DRAWING THE MAP
                $sheet->setCellValue('A1', 'PETA KESEHATAN MESIN (Asset Wellness)');
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $currentRow = 3;

                // Legend
                $sheet->setCellValue('A'.$currentRow, 'LEGENDA:');
                $sheet->setCellValue('B'.$currentRow, 'SAFE');
                $sheet->getStyle('B'.$currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('90EE90');
                $sheet->setCellValue('C'.$currentRow, 'WARNING');
                $sheet->getStyle('C'.$currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD700');
                $sheet->setCellValue('D'.$currentRow, 'FAULT');
                $sheet->getStyle('D'.$currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF6B6B');
                $sheet->getStyle('A'.$currentRow.':D'.$currentRow)->getFont()->setBold(true);
                $sheet->getStyle('B'.$currentRow.':D'.$currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                $currentRow += 2;

                // Map Iteration
                foreach ($ulStructure as $ulName => $pltdGroups) {
                    // UL Header
                    $sheet->setCellValue('A'.$currentRow, $ulName);
                    $sheet->mergeCells('A'.$currentRow.':F'.$currentRow);
                    $sheet->getStyle('A'.$currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('5dade2');
                    $sheet->getStyle('A'.$currentRow)->getFont()->getColor()->setARGB('FFFFFF');
                    $sheet->getStyle('A'.$currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    
                    $currentRow++;

                    foreach ($pltdGroups as $pltdName => $machineNames) {
                        // Set PLTD Name in Col A
                        $sheet->setCellValue('A'.$currentRow, $pltdName);
                        $sheet->getStyle('A'.$currentRow)->getFont()->setBold(true);
                        $sheet->getStyle('A'.$currentRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                        
                        $startRowForPLTD = $currentRow;
                        $machineCount = count($machineNames);

                        if ($machineCount > 0) {
                            foreach($machineNames as $index => $machineName) {
                                // Find Machine
                                $machine = null;
                                $searchNorm = strtoupper(preg_replace('/\s+/', '', trim($machineName)));
                                
                                // Search Logic
                                if (isset($assetLookup[$searchNorm])) $machine = $assetLookup[$searchNorm];
                                elseif (isset($assetLookupBySilm[$searchNorm])) $machine = $assetLookupBySilm[$searchNorm];
                                else {
                                    foreach ($assetLookup as $key => $asset) {
                                        if (strpos($key, $searchNorm) !== false || strpos($searchNorm, $key) !== false) {
                                            $machine = $asset; break;
                                        }
                                    }
                                    if (!$machine && preg_match('/\d+$/', $searchNorm, $matches)) {
                                        $trailingDigits = $matches[0];
                                        foreach ($assetLookup as $key => $asset) {
                                            if (preg_match('/' . preg_quote($trailingDigits) . '$/', $key)) {
                                                $machine = $asset; break;
                                            }
                                        }
                                    }
                                }

                                // Determine Color
                                $color = 'E8F5E9'; // Default Light Green (Safe-ish background)
                                if ($machine) {
                                    if ($machine->fault > 0) $color = 'FF6B6B'; // Red
                                    elseif ($machine->warning > 0) $color = 'FFD700'; // Yellow
                                    else $color = '90EE90'; // Green
                                } else {
                                    $color = 'EEEEEE'; // Grey if not found
                                }

                                // List vertically in Column B (Index 2)
                                $targetRow = $startRowForPLTD + $index;
                                
                                // Force String type to prevent Scientific Notation
                                $sheet->setCellValueExplicit(
                                    'B' . $targetRow,
                                    $machine ? ($machine->kode_mesin_silm ?: $machineName) : $machineName,
                                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                                );

                                $sheet->getStyle('B' . $targetRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                                $sheet->getStyle('B' . $targetRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                                $sheet->getStyle('B' . $targetRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                            }
                            // Advance current row by number of machines
                            $currentRow += $machineCount;
                        } else {
                            // No machines, just leave empty or dash
                            $sheet->setCellValue('B'.$currentRow, '-');
                            $sheet->getStyle('B'.$currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $currentRow++;
                        }

                        // Merge PLTD Name cell vertically if multiple machines
                        // if ($machineCount > 1) {
                        //    $sheet->mergeCells('A' . $startRowForPLTD . ':A' . ($currentRow - 1));
                        // }
                        
                        // Add separator or margin if needed, but contiguous is fine.
                    }
                    $currentRow++; // Space between ULs
                }

                // Auto size columns
                foreach(range('A','Z') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
