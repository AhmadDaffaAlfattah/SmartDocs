<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetWellness extends Model
{
    protected $table = 'asset_wellness';

    protected $fillable = [
        'kode_mesin',
        'unit_pembangkit_common',
        'tipe_aset',
        'kode_mesin_silm',
        'daya_terpasang',
        'daya_mampu_netto',
        'daya_mampu_pasok',
        'total_equipment',
        'safe',
        'warning',
        'fault',
        'percentage_safe',
        'percentage_warning',
        'percentage_fault',
        'warning_equipment',
        'fault_equipment',
        'status_operasi',
        'tahun',
        'bulan',
        'sentral',
        'keterangan',
        'inisial_mesin',
        'ul'
    ];
}
