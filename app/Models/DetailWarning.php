<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailWarning extends Model
{
    protected $table = 'detail_warning';

    protected $fillable = [
        'asset_wellness_id',
        'unit_pembangkit',
        'tanggal_identifikasi',
        'status_saat_ini',
        'asset_description',
        'kondisi_aset',
        'action_plan',
        'target_selesai',
        'progres_saat_ini',
        'realisasi_selesai',
        'main_issue_kendala',
        'keterangan'
    ];

    public function assetWellness()
    {
        return $this->belongsTo(AssetWellness::class, 'asset_wellness_id');
    }
}
