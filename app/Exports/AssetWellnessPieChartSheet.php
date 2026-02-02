<?php

namespace App\Exports;

use App\Models\AssetWellness;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class AssetWellnessPieChartSheet implements FromView
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

    public function view(): View
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

        // Calculate totals
        $totalSafe = $assets->sum('safe');
        $totalWarning = $assets->sum('warning');
        $totalFault = $assets->sum('fault');
        $totalEquipment = $assets->sum('total_equipment');

        // Calculate percentages
        $total = $totalSafe + $totalWarning + $totalFault;
        $persen_safe = $total > 0 ? round(($totalSafe / $total) * 100, 2) : 0;
        $persen_warning = $total > 0 ? round(($totalWarning / $total) * 100, 2) : 0;
        $persen_fault = $total > 0 ? round(($totalFault / $total) * 100, 2) : 0;

        return view('exports.asset_wellness_chart', [
            'totalSafe' => $totalSafe,
            'totalWarning' => $totalWarning,
            'totalFault' => $totalFault,
            'totalEquipment' => $totalEquipment,
            'persen_safe' => $persen_safe,
            'persen_warning' => $persen_warning,
            'persen_fault' => $persen_fault,
        ]);
    }
}
