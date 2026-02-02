<?php
require 'vendor/autoload.php';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load('FORM ASSET WELLNESS DES 2025.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

// Show first 30 rows
foreach (array_slice($rows, 0, 30) as $idx => $row) {
    echo 'Row ' . ($idx + 1) . ': ' . json_encode($row) . PHP_EOL;
}
