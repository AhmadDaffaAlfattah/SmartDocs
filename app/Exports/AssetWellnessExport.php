<?php

namespace App\Exports;

use App\Models\AssetWellness;
use App\Models\DetailWarning;
use App\Models\DetailFault;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssetWellnessExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        return [
            'Form Penyampaian' => new AssetWellnessSheet($this->tahun, $this->bulan, $this->sentral),
            'Peta Kesehatan' => new PetaKesehatanSheet($this->tahun, $this->bulan, $this->sentral),
            'Detail Warning' => new DetailWarningSheet(),
            'Detail Fault' => new DetailFaultSheet(),
            'Visualisasi Data' => new VisualisasiDataSheet($this->tahun, $this->bulan, $this->sentral, $this->monthlyIssues, $this->pieChartData, $this->barChartData),
        ];
    }
}
