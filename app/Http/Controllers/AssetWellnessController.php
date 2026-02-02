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
            'keterangan' => 'nullable'
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
            'keterangan' => 'nullable'
        ]);

        $assetWellness->update($validated);

        return redirect()->route('asset-wellness.index')->with('success', 'Data berhasil diubah!');
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
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $sentral = $request->get('sentral');

        $filename = 'Laporan_Asset_Wellness_' . $tahun . '_' . $bulan . '.xlsx';

        return Excel::download(
            new \App\Exports\AssetWellnessExport($tahun, $bulan, $sentral),
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

        $monthlyIssues = [];
        foreach ($bulanList as $monthKey => $monthName) {
            $monthlyQuery = AssetWellness::where('tahun', $tahun)->where('bulan', $monthKey);
            if ($sentral) {
                $monthlyQuery->where('sentral', $sentral);
            }
            $warningFault = $monthlyQuery->get()->sum(function($item) {
                return $item->warning + $item->fault;
            });
            $monthlyIssues[$monthName] = $warningFault;
        }

        // Load the Blade view and convert to PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.asset_wellness_pdf_report', [
            'assets' => $assets,
            'detailWarnings' => $detailWarnings,
            'detailFaults' => $detailFaults,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'sentral' => $sentral,
            'monthlyIssues' => $monthlyIssues,
            'bulanList' => $bulanList
        ]);


        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);

        $filename = 'Laporan_Asset_Wellness_' . $tahun . '_' . $bulan . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportPdfScreenshots(Request $request)
    {
        $tahun = $request->input('tahun', '2025');
        $bulan = $request->input('bulan', '12');
        $sentral = $request->input('sentral', '');

        // Get data for PDF
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
        $detailWarnings = \App\Models\DetailWarning::with('assetWellness')->orderBy('created_at', 'desc')->get();
        $detailFaults = \App\Models\DetailFault::with('assetWellness')->orderBy('created_at', 'desc')->get();

        // Calculate monthly warning + fault data for visualization
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

        $monthlyIssues = [];
        foreach ($bulanList as $monthKey => $monthName) {
            $monthlyQuery = AssetWellness::where('tahun', $tahun)->where('bulan', $monthKey);
            if ($sentral) {
                $monthlyQuery->where('sentral', $sentral);
            }
            $warningFault = $monthlyQuery->get()->sum(function($item) {
                return $item->warning + $item->fault;
            });
            $monthlyIssues[$monthName] = $warningFault;
        }

        // Render as HTML PDF - using existing proven template
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.asset_wellness_pdf_report', [
            'assets' => $assets,
            'detailWarnings' => $detailWarnings,
            'detailFaults' => $detailFaults,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'sentral' => $sentral,
            'monthlyIssues' => $monthlyIssues,
            'bulanList' => $bulanList
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);

        $filename = 'Laporan_Asset_Wellness_' . $tahun . '_' . $bulan . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }
}
