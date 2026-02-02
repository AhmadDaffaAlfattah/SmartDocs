<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'excel_upload_id',
        'sheet_name',
        'sheet_index',
        'sheet_data',
    ];

    protected $casts = [
        'sheet_data' => 'json',
    ];

    public function excelUpload()
    {
        return $this->belongsTo(ExcelUpload::class, 'excel_upload_id');
    }
}
