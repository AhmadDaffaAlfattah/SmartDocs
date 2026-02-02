<?php

namespace App\Exports;

use App\Models\DetailFault;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailFaultSheet implements FromCollection, WithHeadings, WithStyles, \Maatwebsite\Excel\Concerns\WithTitle
{
    public function collection()
    {
        return DetailFault::with('assetWellness')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($detail) {
                return [
                    $detail->assetWellness->unit_pembangkit_common ?? '-',
                    $detail->unit_pembangkit,
                    $detail->tanggal_identifikasi ? \Carbon\Carbon::parse($detail->tanggal_identifikasi)->format('d-m-Y') : '-',
                    $detail->status_saat_ini ?? '-',
                    $detail->asset_description ?? '-',
                    $detail->kondisi_aset ?? '-',
                    $detail->action_plan ?? '-',
                    $detail->target_selesai ? \Carbon\Carbon::parse($detail->target_selesai)->format('d-m-Y') : '-',
                    $detail->progres_saat_ini ?? '-',
                    $detail->realisasi_selesai ? \Carbon\Carbon::parse($detail->realisasi_selesai)->format('d-m-Y') : '-',
                    $detail->main_issue_kendala ?? '-',
                    $detail->keterangan ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Asset / Mesin',
            'Unit Pembangkit',
            'Tanggal Identifikasi',
            'Status Saat Ini',
            'Asset Description',
            'Kondisi Aset',
            'Action Plan',
            'Target Selesai',
            'Progres Saat Ini',
            'Realisasi Selesai',
            'Main Issue / Kendala',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6B6B']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->setAutoFilter('A1:L' . ($sheet->getHighestRow()));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(18);

        return [];
    }

    public function title(): string
    {
        return 'Detail Fault';
    }
}
