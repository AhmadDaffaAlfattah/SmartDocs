<?php
require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$config = require 'config/database.php';
$db = new DB;
$db->addConnection($config['connections']['mysql']);
$db->setAsGlobal();
$db->bootEloquent();

require 'app/Models/AssetWellness.php';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load('FORM ASSET WELLNESS DES 2025.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

// Data mulai dari row 14 (index 13)
$count = 0;
foreach (array_slice($rows, 13) as $row) {
    // Skip jika kolom kode mesin kosong
    if (empty($row[4])) {
        continue;
    }

    $bulan = $row[0] ?? null;
    $sentral = $row[2] ?? null;
    $tipe_aset = $row[3] ?? null;
    $kode_mesin_silm = $row[4] ?? null;
    $unit_pembangkit = $row[5] ?? null;
    $daya_terpasang = $row[6] ?? null;
    $daya_mampu_netto = $row[7] ?? null;
    $daya_mampu_pasok = $row[8] ?? null;
    $total_equipment = $row[9] ?? 0;
    $safe = $row[10] ?? 0;
    $warning = $row[11] ?? 0;
    $fault = $row[12] ?? 0;
    $percentage_safe = $row[13] ?? null;
    $percentage_warning = $row[14] ?? null;
    $percentage_fault = $row[15] ?? null;
    $warning_equipment = $row[16] ?? null;
    $fault_equipment = $row[17] ?? null;
    $status_operasi = $row[19] ?? null;
    $keterangan = $row[20] ?? '';

    // Convert bulan name to number
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

    $bulan_num = $bulan_map[$bulan] ?? '12';

    // Create kode mesin dari kode_mesin_silm (take last 4 digits)
    $kode_mesin = substr($kode_mesin_silm, -4) ?? 'UNKNOWN';

    $data = [
        'kode_mesin' => $kode_mesin,
        'unit_pembangkit_common' => $unit_pembangkit,
        'tipe_aset' => $tipe_aset,
        'kode_mesin_silm' => $kode_mesin_silm,
        'daya_terpasang' => (float)$daya_terpasang,
        'daya_mampu_netto' => (float)$daya_mampu_netto,
        'daya_mampu_pasok' => (float)$daya_mampu_pasok,
        'total_equipment' => (int)$total_equipment,
        'safe' => (int)$safe,
        'warning' => (int)$warning,
        'fault' => (int)$fault,
        'percentage_safe' => $percentage_safe,
        'percentage_warning' => $percentage_warning,
        'percentage_fault' => $percentage_fault,
        'warning_equipment' => $warning_equipment,
        'fault_equipment' => $fault_equipment,
        'status_operasi' => $status_operasi,
        'tahun' => '2025',
        'bulan' => $bulan_num,
        'sentral' => $sentral,
        'keterangan' => $keterangan ?: 'Kondisi normal'
    ];

    // Try to create record
    try {
        \App\Models\AssetWellness::create($data);
        $count++;
        echo "Created: $kode_mesin - $unit_pembangkit (Sentral: $sentral)" . PHP_EOL;
    } catch (\Exception $e) {
        echo "Error creating: $unit_pembangkit - " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Total records created: $count" . PHP_EOL;
