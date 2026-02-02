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

    public function __construct($tahun = null, $bulan = null, $sentral = null)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->sentral = $sentral;
    }

    public function sheets(): array
    {
        return [
            'Form Penyimpanan' => new AssetWellnessSheet($this->tahun, $this->bulan, $this->sentral),
            'Detail Warning' => new DetailWarningSheet(),
            'Detail Fault' => new DetailFaultSheet(),
        ];
    }
}
