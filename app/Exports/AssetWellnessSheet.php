<?php

namespace App\Exports;

use App\Models\AssetWellness;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Maatwebsite\Excel\Concerns\WithTitle;

class AssetWellnessSheet implements FromCollection, WithHeadings, WithStyles, WithEvents, WithTitle
{
    protected $tahun;
    protected $bulan;
    protected $sentral;
    protected $assetData = [];
    protected $totalSafe = 0;
    protected $totalWarning = 0;
    protected $totalFault = 0;

    public function __construct($tahun = null, $bulan = null, $sentral = null)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->sentral = $sentral;
    }

    public function collection()
    {
        $query = AssetWellness::query();

        if ($this->tahun) {
            $query->where('tahun', $this->tahun);
        }
        if ($this->bulan) {
            $query->where('bulan', $this->bulan);
        }
        if ($this->sentral) {
            $query->where('sentral', $this->sentral);
        }

        $assets = $query->get();

        return $assets->map(function ($asset) {
            $total = $asset->safe + $asset->warning + $asset->fault;
            $persen_safe = $total > 0 ? round(($asset->safe / $total) * 100, 2) : 0;
            $persen_warning = $total > 0 ? round(($asset->warning / $total) * 100, 2) : 0;
            $persen_fault = $total > 0 ? round(($asset->fault / $total) * 100, 2) : 0;

            // Determine status based on safe/warning/fault values
            // LOGIKA UTAMA
            if ($asset->fault > 0) {
                $is_fault = 1;
                $is_warning = 0;
            } elseif ($asset->warning > 0) {
                $is_fault = 0;
                $is_warning = 1;
            } else {
                $is_fault = 0;
                $is_warning = 0;
            }

            // Store asset data for styling
            $this->assetData[] = [
                'is_fault' => $is_fault,
                'is_warning' => $is_warning,
            ];

            // Calculate totals for statistics
            $this->totalSafe += $asset->safe;
            $this->totalWarning += $asset->warning;
            $this->totalFault += $asset->fault;

            return [
                $asset->sentral ?? '-',
                $asset->tipe_aset ?? '-',
                sprintf('%s', $asset->kode_mesin_silm) ?? '-',  // Format as text to prevent scientific notation
                $asset->unit_pembangkit_common,
                $asset->daya_terpasang ?? '-',
                $asset->daya_mampu_netto ?? '-',
                $asset->daya_mampu_pasok ?? '-',
                $asset->total_equipment,
                $asset->safe,
                $asset->warning,
                $asset->fault,
                $persen_safe . '%',
                $persen_warning . '%',
                $persen_fault . '%',
                $asset->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sentral',
            'Tipe Aset',
            'Kode Mesin SILM',
            'Unit Pembangkit/Common',
            'Daya Terpasang (MW)',
            'Daya Mampu Netto (MW)',
            'Daya Mampu Pasok (MW)',
            'Total Equipment',
            'Equipment Safe',
            'Equipment Warning',
            'Equipment Fault',
            '% Safe',
            '% Warning',
            '% Fault',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->setAutoFilter('A1:O' . ($sheet->getHighestRow()));
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Format column C (Kode Mesin) as text
        $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode('@');

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Apply coloring based on safe/warning/fault values
                $dataStartRow = 2;
                foreach ($this->assetData as $index => $asset) {
                    $row = $dataStartRow + $index;

                    // Set white background for all columns first
                    $sheet->getStyle('A' . $row . ':O' . $row)->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);

                    // LOGIKA UTAMA - Color only the relevant column based on priority
                    if ($asset['is_fault'] == 1) {
                        // Prioritas 1: Jika FAULT ada, warna merah (#FF6B6B) hanya kolom K
                        $sheet->getStyle('K' . $row)->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6B6B']],
                            'font' => ['color' => ['rgb' => 'FFFFFF']],
                            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                        ]);
                    } elseif ($asset['is_warning'] == 1) {
                        // Prioritas 2: Jika WARNING ada (dan bukan FAULT), warna kuning (#FFD700) hanya kolom J
                        $sheet->getStyle('J' . $row)->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFD700']],
                            'font' => ['color' => ['rgb' => '000000']],
                            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                        ]);
                    } else {
                        // Prioritas 3: Aman, warna hijau (#90EE90) hanya kolom I
                        $sheet->getStyle('I' . $row)->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '90EE90']],
                            'font' => ['color' => ['rgb' => '000000']],
                            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                        ]);
                    }
                }

                // Add statistics at the bottom
                $lastDataRow = $dataStartRow + count($this->assetData);
                $statsStartRow = $lastDataRow + 2;

                // Statistics header
                $sheet->setCellValue('H' . $statsStartRow, 'STATISTIK');
                $sheet->getStyle('H' . $statsStartRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'left'],
                ]);

                // Safe statistics
                $safeRow = $statsStartRow + 2;
                $sheet->setCellValue('H' . $safeRow, 'Equipment Safe');
                $sheet->setCellValue('I' . $safeRow, $this->totalSafe);
                $total = $this->totalSafe + $this->totalWarning + $this->totalFault;
                $persen_safe = $total > 0 ? round(($this->totalSafe / $total) * 100, 2) : 0;
                $sheet->setCellValue('M' . $safeRow, $persen_safe . '%');
                $sheet->getStyle('H' . $safeRow . ':M' . $safeRow)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '90EE90']],
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Warning statistics
                $warningRow = $safeRow + 1;
                $sheet->setCellValue('H' . $warningRow, 'Equipment Warning');
                $sheet->setCellValue('I' . $warningRow, $this->totalWarning);
                $persen_warning = $total > 0 ? round(($this->totalWarning / $total) * 100, 2) : 0;
                $sheet->setCellValue('M' . $warningRow, $persen_warning . '%');
                $sheet->getStyle('H' . $warningRow . ':M' . $warningRow)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFD700']],
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Fault statistics
                $faultRow = $warningRow + 1;
                $sheet->setCellValue('H' . $faultRow, 'Equipment Fault');
                $sheet->setCellValue('I' . $faultRow, $this->totalFault);
                $persen_fault = $total > 0 ? round(($this->totalFault / $total) * 100, 2) : 0;
                $sheet->setCellValue('M' . $faultRow, $persen_fault . '%');
                $sheet->getStyle('H' . $faultRow . ':M' . $faultRow)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6B6B']],
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Total statistics
                $totalRow = $faultRow + 1;
                $sheet->setCellValue('H' . $totalRow, 'TOTAL');
                $sheet->setCellValue('I' . $totalRow, $total);
                $sheet->setCellValue('M' . $totalRow, '100%');
                $sheet->getStyle('H' . $totalRow . ':M' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Add Pie Chart data below statistics
                $chartStartRow = $totalRow + 3;

                // Add chart title
                $sheet->setCellValue('H' . $chartStartRow, 'PIE CHART ASSET WELLNESS');
                $sheet->getStyle('H' . $chartStartRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => 'left'],
                ]);

                // Create pie chart data table
                $chartDataStartRow = $chartStartRow + 2;
                $sheet->setCellValue('H' . $chartDataStartRow, 'Status');
                $sheet->setCellValue('I' . $chartDataStartRow, 'Jumlah');
                $sheet->setCellValue('J' . $chartDataStartRow, 'Persentase');
                $sheet->getStyle('H' . $chartDataStartRow . ':J' . $chartDataStartRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Calculate total for percentages
                $total = $this->totalSafe + $this->totalWarning + $this->totalFault;
                $persen_safe_chart = $total > 0 ? round(($this->totalSafe / $total) * 100, 2) : 0;
                $persen_warning_chart = $total > 0 ? round(($this->totalWarning / $total) * 100, 2) : 0;
                $persen_fault_chart = $total > 0 ? round(($this->totalFault / $total) * 100, 2) : 0;

                // Safe data
                $chartSafeRow = $chartDataStartRow + 1;
                $sheet->setCellValue('H' . $chartSafeRow, 'Safe');
                $sheet->setCellValue('I' . $chartSafeRow, $this->totalSafe);
                $sheet->setCellValue('J' . $chartSafeRow, $persen_safe_chart . '%');
                $sheet->getStyle('H' . $chartSafeRow . ':J' . $chartSafeRow)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '90EE90']],
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Warning data
                $chartWarningRow = $chartSafeRow + 1;
                $sheet->setCellValue('H' . $chartWarningRow, 'Warning');
                $sheet->setCellValue('I' . $chartWarningRow, $this->totalWarning);
                $sheet->setCellValue('J' . $chartWarningRow, $persen_warning_chart . '%');
                $sheet->getStyle('H' . $chartWarningRow . ':J' . $chartWarningRow)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFD700']],
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Fault data
                $chartFaultRow = $chartWarningRow + 1;
                $sheet->setCellValue('H' . $chartFaultRow, 'Fault');
                $sheet->setCellValue('I' . $chartFaultRow, $this->totalFault);
                $sheet->setCellValue('J' . $chartFaultRow, $persen_fault_chart . '%');
                $sheet->getStyle('H' . $chartFaultRow . ':J' . $chartFaultRow)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6B6B']],
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }
        ];
    }

    public function title(): string
    {
        return 'Form Penyimpanan';
    }
}
