<?php

namespace App\Console\Commands;

use App\Models\AssetWellness;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ImportAssetWellnessData extends Command
{
    protected $signature = 'import:asset-wellness';
    protected $description = 'Import Asset Wellness data from Excel file';

    public function handle()
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load('FORM ASSET WELLNESS DES 2025.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $bulan_map = [
            'Januari' => '01',
            'Maret' => '03',
            'April' => '04',
            'Mei' => '05',
            'Juni' => '06',
            'Juli' => '07',
            'Agustus' => '08',
            'September' => '09',
            'Oktober' => '10',
            'November' => '11',
            'Desember' => '12'
        ];

        $count = 0;
        foreach (array_slice($rows, 13) as $row) {
            if (empty($row[4])) continue;

            $bulan = $row[0] ?? null;
            $sentral = trim($row[2] ?? '');
            $tipe_aset = $row[3] ?? null;
            $kode_mesin_silm = $row[4] ?? null;
            $unit_pembangkit = trim($row[5] ?? '');
            $daya_terpasang = $row[6] ?? null;
            $daya_mampu_netto = $row[7] ?? null;
            $daya_mampu_pasok = $row[8] ?? null;
            $total_equipment = (int)($row[9] ?? 0);
            $safe = (int)($row[10] ?? 0);
            $warning = (int)($row[11] ?? 0);
            $fault = (int)($row[12] ?? 0);
            $percentage_safe = $row[13] ?? null;
            $percentage_warning = $row[14] ?? null;
            $percentage_fault = $row[15] ?? null;
            $warning_equipment = $row[16] ?? null;
            $fault_equipment = $row[17] ?? null;
            $status_operasi = $row[19] ?? null;
            $keterangan = trim($row[20] ?? '') ?: 'Kondisi normal';

            $bulan_num = $bulan_map[$bulan] ?? '12';
            $kode_mesin = substr($kode_mesin_silm, -4) ?? 'UNKNOWN';

            try {
                AssetWellness::create([
                    'kode_mesin' => $kode_mesin,
                    'unit_pembangkit_common' => $unit_pembangkit,
                    'tipe_aset' => $tipe_aset,
                    'kode_mesin_silm' => $kode_mesin_silm,
                    'daya_terpasang' => (float)$daya_terpasang,
                    'daya_mampu_netto' => (float)$daya_mampu_netto,
                    'daya_mampu_pasok' => (float)$daya_mampu_pasok,
                    'total_equipment' => $total_equipment,
                    'safe' => $safe,
                    'warning' => $warning,
                    'fault' => $fault,
                    'percentage_safe' => $percentage_safe,
                    'percentage_warning' => $percentage_warning,
                    'percentage_fault' => $percentage_fault,
                    'warning_equipment' => $warning_equipment,
                    'fault_equipment' => $fault_equipment,
                    'status_operasi' => $status_operasi,
                    'tahun' => '2025',
                    'bulan' => $bulan_num,
                    'sentral' => $sentral,
                    'keterangan' => $keterangan
                ]);
                $count++;
                $this->info("✓ $kode_mesin - $unit_pembangkit ($sentral)");
            } catch (\Exception $e) {
                $this->error("✗ Error: " . $e->getMessage());
            }
        }

        $this->info("\n\nTotal records created: $count");
    }
}
