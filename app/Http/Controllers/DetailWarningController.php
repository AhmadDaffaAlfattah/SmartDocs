<?php

namespace App\Http\Controllers;

use App\Models\AssetWellness;
use App\Models\DetailWarning;
use Illuminate\Http\Request;

class DetailWarningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $sentral = $request->input('sentral');

        $query = DetailWarning::with('assetWellness');

        // Filter by Year, Month, Sentral (via AssetWellness relation)
        if ($tahun) {
            $query->whereHas('assetWellness', function($q) use ($tahun) {
                $q->where('tahun', $tahun);
            });
        }
        if ($bulan) {
            $query->whereHas('assetWellness', function($q) use ($bulan) {
                $q->where('bulan', $bulan);
            });
        }
        if ($sentral) {
            $query->whereHas('assetWellness', function($q) use ($sentral) {
                $q->where('sentral', $sentral);
            });
        }

        if ($search) {
             $query->where(function($q) use ($search) {
                 // Search in related asset
                 $q->whereHas('assetWellness', function($subQ) use ($search) {
                     $subQ->where('kode_mesin', 'like', "%$search%")
                          ->orWhere('inisial_mesin', 'like', "%$search%");
                 })
                 // Or search in local string column
                 ->orWhere('unit_pembangkit', 'like', "%$search%")
                 ->orWhere('asset_description', 'like', "%$search%");
             });
        }

        $detailWarnings = $query->orderBy('created_at', 'desc')->get();
        
        // Pass filter data for dropdowns
        $years = range(2020, date('Y') + 5);
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $sentralList = AssetWellness::distinct()->pluck('sentral')->filter();

        return view('detail-warning.index', compact('detailWarnings', 'tahun', 'bulan', 'sentral', 'years', 'months', 'sentralList'));
    }

    public function create()
    {
        $assets = AssetWellness::orderBy('unit_pembangkit_common')->get();

        return view('detail-warning.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_wellness_id' => 'required|exists:asset_wellness,id',
            'unit_pembangkit' => 'required|string',
            'tanggal_identifikasi' => 'nullable|date',
            'status_saat_ini' => 'nullable|string',
            'asset_description' => 'nullable|string',
            'kondisi_aset' => 'nullable|string',
            'action_plan' => 'nullable|string',
            'target_selesai' => 'nullable|date',
            'progres_saat_ini' => 'nullable|string',
            'realisasi_selesai' => 'nullable|date',
            'main_issue_kendala' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        DetailWarning::create($validated);

        return redirect()->route('detail-warning.index')
            ->with('success', 'Detail Warning berhasil ditambahkan');
    }

    public function edit($id)
    {
        $detailWarning = DetailWarning::findOrFail($id);
        $assets = AssetWellness::orderBy('unit_pembangkit_common')->get();

        return view('detail-warning.edit', compact('detailWarning', 'assets'));
    }

    public function update(Request $request, $id)
    {
        $detailWarning = DetailWarning::findOrFail($id);

        $validated = $request->validate([
            'asset_wellness_id' => 'required|exists:asset_wellness,id',
            'unit_pembangkit' => 'required|string',
            'tanggal_identifikasi' => 'nullable|date',
            'status_saat_ini' => 'nullable|string',
            'asset_description' => 'nullable|string',
            'kondisi_aset' => 'nullable|string',
            'action_plan' => 'nullable|string',
            'target_selesai' => 'nullable|date',
            'progres_saat_ini' => 'nullable|string',
            'realisasi_selesai' => 'nullable|date',
            'main_issue_kendala' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        $detailWarning->update($validated);

        // Redirect back to Asset Wellness Dashboard with correct Month/Year context
        $asset = $detailWarning->assetWellness;
        return redirect()->route('asset-wellness.index', [
            'tahun' => $asset->tahun,
            'bulan' => $asset->bulan,
            'sentral' => $asset->sentral
        ])->with('success', 'Detail Warning berhasil diperbarui');
    }

    public function destroy($id)
    {
        $detailWarning = DetailWarning::findOrFail($id);
        $asset = $detailWarning->assetWellness;
        $detailWarning->delete();

        return redirect()->route('asset-wellness.index', [
            'tahun' => $asset->tahun,
            'bulan' => $asset->bulan,
            'sentral' => $asset->sentral
        ])->with('success', 'Detail Warning berhasil dihapus');
    }
}
