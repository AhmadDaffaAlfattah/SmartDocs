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

class VisualisasiDataSheet implements FromCollection, WithTitle, WithStyles, WithEvents
{
    protected $tahun;
    protected $bulan;
    protected $sentral;

    protected $monthlyIssues;
    protected $pieChartData;
    protected $barChartData;

    public function __construct($tahun = null, $bulan = null, $sentral = null, $monthlyIssues = [], $pieChartData = null, $barChartData = null)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->sentral = $sentral;
        $this->monthlyIssues = $monthlyIssues;
        $this->pieChartData = $pieChartData;
        $this->barChartData = $barChartData;
    }

    public function collection()
    {
        return new Collection([]);
    }

    public function title(): string
    {
        return 'Visualisasi Data';
    }

    public function styles(Worksheet $sheet)
    {
        return [
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

                $totalSafe = $assets->sum('safe');
                $totalWarning = $assets->sum('warning');
                $totalFault = $assets->sum('fault');
                $grandTotal = $totalSafe + $totalWarning + $totalFault;

                $sheet->setCellValue('A1', 'VISUALISASI DATA ASSET WELLNESS');
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // --- SECTION 1: RINGKASAN STATISTIK ---
                $row = 3;
                $sheet->setCellValue('A'.$row, '1. RINGKASAN STATISTIK');
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                $row++;

                $headers = ['Kategori', 'Jumlah', 'Persentase'];
                $sheet->getDelegate()->fromArray($headers, null, 'A'.$row);
                $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
                $sheet->getStyle('A'.$row.':C'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('CCCCCC');
                $row++;

                // Safe
                $pct_safe = $grandTotal > 0 ? round(($totalSafe / $grandTotal) * 100, 2) : 0;
                $sheet->setCellValue('A'.$row, 'SAFE');
                $sheet->setCellValue('B'.$row, $totalSafe);
                $sheet->setCellValue('C'.$row, $pct_safe . '%');
                $sheet->getStyle('A'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('90EE90');
                $row++;

                // Warning
                $pct_warning = $grandTotal > 0 ? round(($totalWarning / $grandTotal) * 100, 2) : 0;
                $sheet->setCellValue('A'.$row, 'WARNING');
                $sheet->setCellValue('B'.$row, $totalWarning);
                $sheet->setCellValue('C'.$row, $pct_warning . '%');
                $sheet->getStyle('A'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD700');
                $row++;

                // Fault
                $pct_fault = $grandTotal > 0 ? round(($totalFault / $grandTotal) * 100, 2) : 0;
                $sheet->setCellValue('A'.$row, 'FAULT');
                $sheet->setCellValue('B'.$row, $totalFault);
                $sheet->setCellValue('C'.$row, $pct_fault . '%');
                $sheet->getStyle('A'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF6B6B');
                $sheet->getStyle('A'.$row)->getFont()->getColor()->setARGB('FFFFFF');
                $row++;

                // Total
                $sheet->setCellValue('A'.$row, 'TOTAL EQUIPMENT');
                $sheet->setCellValue('B'.$row, $grandTotal);
                $sheet->setCellValue('C'.$row, '100%');
                $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
                $sheet->getStyle('A'.$row.':C'.$row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                
                // INSERT PIE CHART IMAGE
                if ($this->pieChartData) {
                    $piePath = sys_get_temp_dir() . '/pie_' . uniqid() . '.png';
                    file_put_contents($piePath, $this->pieChartData);
                    
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Pie Chart');
                    $drawing->setDescription('Pie Chart');
                    $drawing->setPath($piePath);
                    $drawing->setHeight(250);
                    $drawing->setCoordinates('E3'); // Place next to table
                    $drawing->setWorksheet($sheet->getDelegate());
                }

                $row += 2;

                // --- SECTION 2: TREN BULANAN ---
                $sheet->setCellValue('A'.$row, '2. DATA TREN ISSUE BULANAN (WARNING + FAULT)');
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                $row++;

                $headers = ['Bulan', 'Total Issue (Warning + Fault)'];
                $sheet->getDelegate()->fromArray($headers, null, 'A'.$row);
                $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
                $sheet->getStyle('A'.$row.':B'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('CCCCCC');
                $row++;

                foreach($this->monthlyIssues as $mName => $val) {
                    $sheet->setCellValue('A'.$row, $mName);
                    $sheet->setCellValue('B'.$row, $val);
                    $row++;
                }

                // INSERT BAR CHART IMAGE
                if ($this->barChartData) {
                    $barPath = sys_get_temp_dir() . '/bar_' . uniqid() . '.png';
                    file_put_contents($barPath, $this->barChartData);
                    
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Bar Chart');
                    $drawing->setDescription('Bar Chart');
                    $drawing->setPath($barPath);
                    $drawing->setHeight(250);
                    $drawing->setCoordinates('D15'); // Place next to table
                    $drawing->setWorksheet($sheet->getDelegate());
                }

                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(15);
            }
        ];
    }
}
