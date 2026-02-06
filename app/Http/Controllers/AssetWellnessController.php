<?php

namespace App\Http\Controllers;

use App\Models\AssetWellness;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AssetWellnessController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', '2025');
        $bulan = $request->get('bulan', '12');
        $sentral = $request->get('sentral', '');

        $query = AssetWellness::query();

        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($sentral) {
            $query->where('sentral', $sentral);
        }

        $assets = $query->orderBy('kode_mesin')->get();
        $years = collect(range(1900, 2100));
        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        $sentrlList = AssetWellness::distinct()->pluck('sentral')->filter();

        // Calculate monthly warning + fault data for visualization
        $monthlyIssues = [];
        foreach ($bulanList as $monthKey => $monthName) {
            $monthlyQuery = AssetWellness::where('tahun', $tahun)->where('bulan', $monthKey);
            if ($sentral) {
                $monthlyQuery->where('sentral', $sentral);
            }
            $warningFault = $monthlyQuery->get()->sum(function ($item) {
                return $item->warning + $item->fault;
            });
            $monthlyIssues[$monthName] = $warningFault;
        }

        // Get all detail warning and fault
        $detailWarningsAll = \App\Models\DetailWarning::with('assetWellness')->orderBy('created_at', 'desc')->get();
        $detailFaultsAll = \App\Models\DetailFault::with('assetWellness')->orderBy('created_at', 'desc')->get();

        return view('asset-wellness.index_with_tabs', compact('assets', 'years', 'bulanList', 'sentrlList', 'tahun', 'bulan', 'sentral', 'detailWarningsAll', 'detailFaultsAll', 'monthlyIssues'));
    }

    public function create()
    {
        return view('asset-wellness.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mesin' => 'required',
            'unit_pembangkit_common' => 'required',
            'tipe_aset' => 'nullable',
            'kode_mesin_silm' => 'nullable',
            'daya_terpasang' => 'nullable|numeric',
            'daya_mampu_netto' => 'nullable|numeric',
            'daya_mampu_pasok' => 'nullable|numeric',
            'total_equipment' => 'required|integer',
            'safe' => 'required|integer',
            'warning' => 'required|integer',
            'fault' => 'required|integer',
            'status_operasi' => 'nullable',
            'tahun' => 'required',
            'bulan' => 'required',
            'sentral' => 'nullable',
            'keterangan' => 'nullable',
            'inisial_mesin' => 'nullable',
            'ul' => 'nullable'
        ]);

        AssetWellness::create($validated);

        return redirect()->route('asset-wellness.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(AssetWellness $assetWellness)
    {
        return view('asset-wellness.edit', compact('assetWellness'));
    }

    public function show(AssetWellness $assetWellness)
    {
        return redirect()->route('asset-wellness.index');
    }

    public function update(Request $request, AssetWellness $assetWellness)
    {
        $validated = $request->validate([
            'unit_pembangkit_common' => 'required',
            'total_equipment' => 'required|integer',
            'safe' => 'required|integer',
            'warning' => 'required|integer',
            'fault' => 'required|integer',
            'keterangan' => 'nullable',
            'inisial_mesin' => 'nullable',
            'tipe_aset' => 'nullable',
            'kode_mesin_silm' => 'nullable',
            'daya_terpasang' => 'nullable',
            'daya_mampu_netto' => 'nullable',
            'daya_mampu_pasok' => 'nullable',
            'status_operasi' => 'nullable',
            'ul' => 'nullable'
        ]);

        $assetWellness->update($validated);

        return redirect()->route('asset-wellness.index', [
            'tahun' => $assetWellness->tahun,
            'bulan' => $assetWellness->bulan,
            'sentral' => $assetWellness->sentral
        ])->with('success', 'Data berhasil diubah!');
    }

    public function destroy(AssetWellness $assetWellness)
    {
        $assetWellness->delete();

        return redirect()->route('asset-wellness.index')->with('success', 'Data berhasil dihapus!');
    }

    public function download(Request $request)
    {
        $tahun = $request->get('tahun', '2025');
        $bulan = $request->get('bulan', '12');
        $sentral = $request->get('sentral', '');
        $format = $request->get('format', 'excel'); // excel or pdf

        $query = AssetWellness::query();

        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($sentral) {
            $query->where('sentral', $sentral);
        }

        $assets = $query->orderBy('kode_mesin')->get();
        $filename = 'AssetWellness_' . $tahun . '_' . $bulan . '_' . date('YmdHis');

        if ($format === 'pdf') {
            return $this->downloadPDF($assets, $tahun, $bulan, $sentral, $filename);
        } else {
            return $this->downloadExcel($assets, $tahun, $bulan, $sentral, $filename);
        }
    }

    private function downloadExcel($assets, $tahun, $bulan, $sentral, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A1', 'DATA KESEHATAN MESIN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:M1');

        // Subtitle
        $sheet->setCellValue('A2', 'Tahun: ' . $tahun . ' | Bulan: ' . $bulan . ' | Sentral: ' . ($sentral ?: 'Semua'));
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->mergeCells('A2:M2');

        // Headers
        $headers = [
            'NO',
            'SENTRAL',
            'TIPE ASET',
            'KODE MESIN',
            'UNIT PEMBANGKIT/COMMON',
            'DAYA TERPASANG',
            'DAYA MAMPU NETTO',
            'DAYA MAMPU PASOK',
            'TOTAL EQUIPMENT',
            'SAFE',
            'WARNING',
            'FAULT',
            'KETERANGAN'
        ];

        foreach ($headers as $index => $header) {
            $cell = chr(65 + $index) . '4';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF333333');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Data
        $row = 5;
        foreach ($assets as $index => $asset) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $asset->sentral ?? '-');
            $sheet->setCellValue('C' . $row, $asset->tipe_aset ?? '-');
            $sheet->setCellValue('D' . $row, $asset->kode_mesin ?? '-');
            $sheet->setCellValue('E' . $row, $asset->unit_pembangkit_common ?? '-');
            $sheet->setCellValue('F' . $row, $asset->daya_terpasang ?? '-');
            $sheet->setCellValue('G' . $row, $asset->daya_mampu_netto ?? '-');
            $sheet->setCellValue('H' . $row, $asset->daya_mampu_pasok ?? '-');
            $sheet->setCellValue('I' . $row, $asset->total_equipment ?? '-');
            $sheet->setCellValue('J' . $row, $asset->safe ?? '0');
            $sheet->setCellValue('K' . $row, $asset->warning ?? '0');
            $sheet->setCellValue('L' . $row, $asset->fault ?? '0');
            $sheet->setCellValue('M' . $row, $asset->keterangan ?? '-');

            // Color code status
            if ($asset->fault > 0) {
                $sheet->getStyle('L' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF6B6B');
            } elseif ($asset->warning > 0) {
                $sheet->getStyle('K' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
            } else {
                $sheet->getStyle('J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF90EE90');
            }
            $row++;
        }

        // Auto width
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function downloadPDF($assets, $tahun, $bulan, $sentral, $filename)
    {
        // Create Excel dan convert to PDF
        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<h2 style="text-align: center;">DATA KESEHATAN MESIN</h2>';
        $html .= '<p style="text-align: center; font-size: 12px;">Tahun: ' . $tahun . ' | Bulan: ' . $bulan . ' | Sentral: ' . ($sentral ?: 'Semua') . '</p>';
        $html .= '<table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; font-size: 11px;">';
        $html .= '<tr style="background: #333; color: white;">';
        $html .= '<th>NO</th><th>SENTRAL</th><th>TIPE ASET</th><th>KODE MESIN</th><th>UNIT PEMBANGKIT</th>';
        $html .= '<th>DAYA TERPASANG</th><th>DAYA NETTO</th><th>DAYA PASOK</th><th>TOTAL EQ</th>';
        $html .= '<th>SAFE</th><th>WARNING</th><th>FAULT</th><th>KETERANGAN</th></tr>';

        foreach ($assets as $index => $asset) {
            $html .= '<tr>';
            $html .= '<td>' . ($index + 1) . '</td>';
            $html .= '<td>' . ($asset->sentral ?? '-') . '</td>';
            $html .= '<td>' . ($asset->tipe_aset ?? '-') . '</td>';
            $html .= '<td>' . ($asset->kode_mesin ?? '-') . '</td>';
            $html .= '<td>' . ($asset->unit_pembangkit_common ?? '-') . '</td>';
            $html .= '<td style="text-align: right;">' . ($asset->daya_terpasang ?? '-') . '</td>';
            $html .= '<td style="text-align: right;">' . ($asset->daya_mampu_netto ?? '-') . '</td>';
            $html .= '<td style="text-align: right;">' . ($asset->daya_mampu_pasok ?? '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($asset->total_equipment ?? '-') . '</td>';
            $html .= '<td style="text-align: center;">' . ($asset->safe ?? '0') . '</td>';
            $html .= '<td style="text-align: center;">' . ($asset->warning ?? '0') . '</td>';
            $html .= '<td style="text-align: center;">' . ($asset->fault ?? '0') . '</td>';
            $html .= '<td>' . ($asset->keterangan ?? '-') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        // Return as attachment - untuk sekarang return as HTML
        // User bisa print ke PDF dari browser
        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $filename . '.html', [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->get('tahun', '2025');
        $bulan = $request->get('bulan', '12');
        $sentral = $request->get('sentral');

        $filename = 'Laporan_Asset_Wellness_' . $tahun . '_' . $bulan . '.xlsx';

        // 1. Calculate stats for charts
        $query = AssetWellness::query();
        if ($tahun) $query->where('tahun', $tahun);
        if ($bulan) $query->where('bulan', $bulan);
        if ($sentral) $query->where('sentral', $sentral);
        $assets = $query->get();

        $totalSafe = $assets->sum('safe');
        $totalWarning = $assets->sum('warning');
        $totalFault = $assets->sum('fault');

        $monthlyIssues = [];
        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        foreach ($bulanList as $monthKey => $monthName) {
            $monthlyQuery = AssetWellness::where('tahun', $tahun)->where('bulan', $monthKey);
            if ($sentral) {
                $monthlyQuery->where('sentral', $sentral);
            }
            $warningFault = $monthlyQuery->get()->sum(function ($item) {
                return $item->warning + $item->fault;
            });
            $monthlyIssues[$monthName] = $warningFault;
        }

        // 2. Generate Chart Images
        $pieConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Safe', 'Warning', 'Fault'],
                'datasets' => [[
                    'data' => [$totalSafe, $totalWarning, $totalFault],
                    'backgroundColor' => ['#90EE90', '#FFD700', '#FF6B6B']
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                    'datalabels' => ['display' => true, 'color' => '#333', 'font' => ['size' => 14, 'weight' => 'bold']]
                ]
            ]
        ];

        $barConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($monthlyIssues),
                'datasets' => [[
                    'label' => 'Issues',
                    'data' => array_values($monthlyIssues),
                    'backgroundColor' => '#FF7B54'
                ]]
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
                'scales' => [
                    'y' => [
                        'ticks' => ['stepSize' => 1] 
                    ]
                ]
            ]
        ];

        $fetchChartImage = function($config) {
            try {
                $url = 'https://quickchart.io/chart?w=400&h=300&c=' . urlencode(json_encode($config));
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $data = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode == 200 && $data) {
                    return $data; // Return raw data for Excel (to save to temp file)
                }
                return null;
            } catch (\Exception $e) {
                return null;
            }
        };

        $pieChartData = $fetchChartImage($pieConfig);
        $barChartData = $fetchChartImage($barConfig);

        return Excel::download(
            new \App\Exports\AssetWellnessExport($tahun, $bulan, $sentral, $monthlyIssues, $pieChartData, $barChartData),
            $filename
        );
    }

    public function exportPdfReport(Request $request)
    {
        $tahun = $request->get('tahun', '2025');
        $bulan = $request->get('bulan', '12');
        $sentral = $request->get('sentral', '');

        $query = AssetWellness::query();

        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($sentral) {
            $query->where('sentral', $sentral);
        }

        $assets = $query->orderBy('kode_mesin')->get();

        // Get all detail warning and fault
        $detailWarnings = \App\Models\DetailWarning::with('assetWellness')
            ->orderBy('created_at', 'desc')
            ->get();

        $detailFaults = \App\Models\DetailFault::with('assetWellness')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate monthly warning + fault data for visualization
        $monthlyIssues = [];
        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        foreach ($bulanList as $monthKey => $monthName) {
            $monthlyQuery = AssetWellness::where('tahun', $tahun)->where('bulan', $monthKey);
            if ($sentral) {
                $monthlyQuery->where('sentral', $sentral);
            }
            $warningFault = $monthlyQuery->get()->sum(function ($item) {
                return $item->warning + $item->fault;
            });
            $monthlyIssues[$monthName] = $warningFault;
        }

        $totalSafe = $assets->sum('safe');
        $totalWarning = $assets->sum('warning');
        $totalFault = $assets->sum('fault');

        // Generate QuickChart URLs and fetch images as Base64
        // Use shorter configuration to avoid URL length issues
        $pieConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Safe', 'Warning', 'Fault'],
                'datasets' => [[
                    'data' => [$totalSafe, $totalWarning, $totalFault],
                    'backgroundColor' => ['#90EE90', '#FFD700', '#FF6B6B']
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                    'datalabels' => ['display' => true, 'color' => '#333', 'font' => ['size' => 14, 'weight' => 'bold']]
                ]
            ]
        ];

        $barConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($monthlyIssues),
                'datasets' => [[
                    'label' => 'Issues',
                    'data' => array_values($monthlyIssues),
                    'backgroundColor' => '#FF7B54'
                ]]
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
                'scales' => [
                    'y' => [
                        'ticks' => ['stepSize' => 1] 
                    ]
                ]
            ]
        ];

        // Helper to fetch and encode image
        $fetchChartImage = function($config) {
            try {
                $url = 'https://quickchart.io/chart?w=400&h=300&c=' . urlencode(json_encode($config));
                // Use curl for better reliability on Windows/Laragon
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL for local dev
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $data = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode == 200 && $data) {
                    return 'data:image/png;base64,' . base64_encode($data);
                }
                return null;
            } catch (\Exception $e) {
                return null;
            }
        };

        $pieChartBase64 = $fetchChartImage($pieConfig);
        $barChartBase64 = $fetchChartImage($barConfig);

        // Load the Blade view and convert to PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.asset_wellness_pdf_report', [
            'assets' => $assets,
            'detailWarnings' => $detailWarnings,
            'detailFaults' => $detailFaults,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'sentral' => $sentral,
            'pieChartBase64' => $pieChartBase64,
            'barChartBase64' => $barChartBase64,
            'monthlyIssues' => $monthlyIssues,
            'totalSafe' => $totalSafe,
            'totalWarning' => $totalWarning,
            'totalFault' => $totalFault
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);

        $filename = 'Laporan_Asset_Wellness_' . $tahun . '_' . $bulan . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');
        
        // 1. DATA CONTEXT (TAHUN & BULAN) LOGIC
        // Priority: 1. Filename Parsing, 2. Form Input, 3. Current Date
        
        $filename = $file->getClientOriginalName();
        $filename = str_replace(['.xlsx', '.xls', '.csv'], '', $filename);
        
        // Defaults
        $tahun = $request->get('tahun') ?: date('Y');
        $bulanNum = $request->get('bulan') ?: date('m');
        $sentral = $request->get('sentral');

        // Parse Filename (e.g., "Asset_November_2025" or "Data_11_2024")
        $foundMonth = false;
        $foundYear = false;

        // Extract Year (4 digits)
        if (preg_match('/20\d{2}/', $filename, $matches)) {
            $tahun = $matches[0];
            $foundYear = true;
        }

        // Extract Month (Name or Number)
        $indoMonths = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'may' => '05', 'jun' => '06',
            'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12'
        ];

        // Check for month names in filename
        $lowerFilename = strtolower($filename);
        foreach ($indoMonths as $name => $num) {
            if (str_contains($lowerFilename, $name)) {
                $bulanNum = $num;
                $foundMonth = true;
                break;
            }
        }

        // Final formatting
        $bulan = str_pad($bulanNum, 2, '0', STR_PAD_LEFT);

        try {
            $data = Excel::toArray(new \stdClass, $file);
            if (empty($data) || empty($data[0])) throw new \Exception("File kosong.");
            $rows = $data[0];

            $data = Excel::toArray(new \App\Imports\AssetWellnessImport, $file);
            if (empty($data) || empty($data[0])) throw new \Exception("File kosong.");
            $rows = $data[0];

            // CONFIGURATION: User specified Row 13 (Index 12) as Header, Row 14 (Index 13) as Data
            $targetHeaderIndex = 12; // Row 13
            $targetDataIndex = 13;   // Row 14

            $colMap = [];
            $useFixedMapping = false;

            // 2. FIXED MAPPING (Based on Debugging Shift)
            // Observed: Index 1 = Col A (No), Index 2 = Col B (Sentral), etc.
            // Mapping:
            // B(2)=Sentral, C(3)=Tipe, D(4)=KodeM, E(5)=Unit, F(6)=DT, G(7)=DMN, H(8)=DMP
            // I(9)=TotEq, J(10)=Safe, K(11)=Warn, L(12)=Fault
            // ... R(18)=Status, S(19)=Ket
            
            $count = 0;
            $cleanNum = function($val) {
                if (is_string($val)) {
                    $val = str_replace(',', '.', $val); 
                }
                return (float) preg_replace('/[^0-9.\-]/', '', (string)$val); 
            };

            // 3. DETECT OPTIONAL COLUMNS (e.g. Inisial Mesin)
            $headerRow = $rows[12] ?? [];
            $inisialColIndex = null;
            
            foreach ($headerRow as $idx => $val) {
                if (is_string($val) && (stripos($val, 'INISIAL') !== false)) {
                    $inisialColIndex = $idx;
                    break;
                }
            }

            for ($i = 13; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Mandatory KODE MESIN (Col D / Index 4)
                $kodeMesin = isset($row[4]) ? trim($row[4]) : null;
                if (empty($kodeMesin) || strlen($kodeMesin) < 2) continue;

                // Status & Notes (Shifted to 18/19)
                $rawStatus = $row[18] ?? null;
                $rawKet = $row[19] ?? null;

                // Synthetic Status Logic
                $status = 'Normal';
                $ket = '-';
                
                $safeVal = $cleanNum($row[10] ?? 0);
                $warnVal = $cleanNum($row[11] ?? 0);
                $faultVal = $cleanNum($row[12] ?? 0);
                $totalVal = $cleanNum($row[9] ?? 0);
                $tipeAset = $row[3] ?? '';

                if ($rawStatus && !str_starts_with($rawStatus, '=')) {
                    $status = $rawStatus;
                } else {
                    if (stripos($tipeAset, 'COMMON') !== false) {
                        $status = '-';
                    } elseif ($faultVal > 0) {
                        $status = 'Shutdown';
                    } elseif ($warnVal > 0 || ($totalVal > $safeVal)) {
                        $status = 'Derating';
                    } else {
                        $status = 'Normal';
                    }
                }

                if ($rawKet && !str_starts_with($rawKet, '=')) {
                    $ket = $rawKet;
                } else {
                    if ($status == 'Shutdown') $ket = 'Mesin Gangguan/Shutdown';
                    elseif ($status == 'Derating') $ket = 'Mesin Derating/Warning';
                    elseif ($status == 'Normal') $ket = 'Operasi Normal';
                }

                // Mapping UL
                $unitPembangkit = strtoupper($row[5] ?? '');
                $sentralRaw = strtoupper($row[2] ?? '');
                $ul = null;

                $ulMapping = [
                    'UL NUNUKAN' => ['SEI BILAL', 'SEBATIK', 'MALINAU', 'TIDUNG PALE', 'TULIN ONSOI', 'KUALA LAPANG'],
                    'UL TARAKAN' => ['GUNUNG BELAH', 'SEI BUAYA'],
                    'UL TANJUNG SELOR' => ['BUNYU', 'SAMBALIUNG', 'TALISAYAN'],
                    'UL BALIKPAPAN' => ['BATAKAN', 'GUNUNG MALANG', 'TANJUNG ARU', 'TJ ARU'],
                ];

                foreach ($ulMapping as $ulName => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (str_contains($unitPembangkit, $keyword) || str_contains($sentralRaw, $keyword)) {
                            $ul = $ulName;
                            break 2;
                        }
                    }
                }

                // Data to update
                $updateData = [
                    'sentral' => $sentral ?: ($row[2] ?? null),
                    'tipe_aset' => $row[3] ?? '-',
                    'unit_pembangkit_common' => $row[5] ?? '-',
                    'ul' => $ul,
                    'daya_terpasang' => $cleanNum($row[6] ?? 0),
                    'daya_mampu_netto' => $cleanNum($row[7] ?? 0),
                    'daya_mampu_pasok' => $cleanNum($row[8] ?? 0),
                    'total_equipment' => $cleanNum($row[9] ?? 0),
                    'safe' => $cleanNum($row[10] ?? 0),
                    'warning' => $cleanNum($row[11] ?? 0),
                    'fault' => $cleanNum($row[12] ?? 0),
                    'status_operasi' => $status,
                    'keterangan' => $ket,
                    'kode_mesin_silm' => $kodeMesin, 
                ];

                // Optional: Inisial Mesin if column detected
                if ($inisialColIndex !== null && !empty($row[$inisialColIndex])) {
                    $updateData['inisial_mesin'] = trim($row[$inisialColIndex]);
                }

                AssetWellness::updateOrCreate(
                    [
                        'kode_mesin' => $kodeMesin,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                    ],
                    $updateData
                );
                $count++;
            }

            if ($count == 0) {
                 // Debug: Show first row of data specifically
                 $firstRowDump = json_encode($rows[13] ?? 'Empty');
                 throw new \Exception("Nol data diproses. Cek format file. Data Baris 14: $firstRowDump");
            }

            $msg = "Import Sukses! $count data diproses untuk periode " . date("F", mktime(0, 0, 0, $bulan, 10)) . " $tahun.";
            if ($foundMonth || $foundYear) {
                $msg .= " (Periode dideteksi dari nama file)";
            }

            return redirect()->route('asset-wellness.index', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral])
                             ->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->route('asset-wellness.index', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral])
                             ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function destroyPeriod(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'bulan' => 'required', // Should be string 01-12
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $sentral = $request->sentral;

        $query = AssetWellness::where('tahun', $tahun)->where('bulan', $bulan);
        
        // If sentral is specific (not empty), filter by it. If "All", delete all for that month?
        // Usually filtering UI sends empty string or specific value.
        if (!empty($sentral) && $sentral !== 'All') {
            $query->where('sentral', $sentral);
            $msg = "Data Sentral $sentral untuk periode Bulan $bulan Tahun $tahun berhasil dihapus.";
        } else {
            $msg = "Seluruh Data untuk periode Bulan $bulan Tahun $tahun berhasil dihapus.";
        }

        $count = $query->delete();

        if ($count == 0) {
             return redirect()->back()->with('error', "Tidak ada data yang dihapus untuk periode tersebut.");
        }

        return redirect()->back()->with('success', "$msg Total: $count row.");
    }
}
