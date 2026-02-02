<?php

namespace App\Http\Controllers;

use App\Models\AssetWellness;
use App\Models\DetailFault;
use Illuminate\Http\Request;

class DetailFaultController extends Controller
{
    public function index()
    {
        $detailFaults = DetailFault::with('assetWellness')->orderBy('created_at', 'desc')->get();

        return view('detail-fault.index', compact('detailFaults'));
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

        return redirect()->route('detail-fault.index')
            ->with('success', 'Detail Fault berhasil diperbarui');
    }

    public function destroy($id)
    {
        $detailFault = DetailFault::findOrFail($id);
        $detailFault->delete();

        return redirect()->route('detail-fault.index')
            ->with('success', 'Detail Fault berhasil dihapus');
    }
}
