<?php

namespace App\Http\Controllers;

use App\Models\AssetWellness;
use App\Models\DetailFault;
use Illuminate\Http\Request;

class DetailFaultController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $sentral = $request->input('sentral');

        $query = DetailFault::with('assetWellness');

        // Filter by Year, Month, Sentral
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
                 $q->whereHas('assetWellness', function($subQ) use ($search) {
                     $subQ->where('kode_mesin', 'like', "%$search%")
                          ->orWhere('inisial_mesin', 'like', "%$search%");
                 })
                 ->orWhere('unit_pembangkit', 'like', "%$search%")
                 ->orWhere('asset_description', 'like', "%$search%");
             });
        }

        $detailFaults = $query->orderBy('created_at', 'desc')->get();
        
        // Pass filter data
        $years = range(2020, date('Y') + 5);
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $sentralList = AssetWellness::distinct()->pluck('sentral')->filter();

        return view('detail-fault.index', compact('detailFaults', 'tahun', 'bulan', 'sentral', 'years', 'months', 'sentralList'));
    }

    public function create()
    {
        $assets = AssetWellness::orderBy('unit_pembangkit_common')->get();

        return view('detail-fault.create', compact('assets'));
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

        DetailFault::create($validated);

        return redirect()->route('detail-fault.index')
            ->with('success', 'Detail Fault berhasil ditambahkan');
    }

    public function edit($id)
    {
        $detailFault = DetailFault::findOrFail($id);
        $assets = AssetWellness::orderBy('unit_pembangkit_common')->get();

        return view('detail-fault.edit', compact('detailFault', 'assets'));
    }

    public function update(Request $request, $id)
    {
        $detailFault = DetailFault::findOrFail($id);

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

        $detailFault->update($validated);

        $asset = $detailFault->assetWellness;
        return redirect()->route('asset-wellness.index', [
            'tahun' => $asset->tahun,
            'bulan' => $asset->bulan,
            'sentral' => $asset->sentral
        ])->with('success', 'Detail Fault berhasil diperbarui');
    }

    public function destroy($id)
    {
        $detailFault = DetailFault::findOrFail($id);
        $asset = $detailFault->assetWellness;
        $detailFault->delete();

        return redirect()->route('asset-wellness.index', [
            'tahun' => $asset->tahun,
            'bulan' => $asset->bulan,
            'sentral' => $asset->sentral
        ])->with('success', 'Detail Fault berhasil dihapus');
    }
}
