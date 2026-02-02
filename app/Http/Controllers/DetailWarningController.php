<?php

namespace App\Http\Controllers;

use App\Models\AssetWellness;
use App\Models\DetailWarning;
use Illuminate\Http\Request;

class DetailWarningController extends Controller
{
    public function index()
    {
        $detailWarnings = DetailWarning::with('assetWellness')->orderBy('created_at', 'desc')->get();

        return view('detail-warning.index', compact('detailWarnings'));
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

        return redirect()->route('detail-warning.index')
            ->with('success', 'Detail Warning berhasil diperbarui');
    }

    public function destroy($id)
    {
        $detailWarning = DetailWarning::findOrFail($id);
        $detailWarning->delete();

        return redirect()->route('detail-warning.index')
            ->with('success', 'Detail Warning berhasil dihapus');
    }
}
