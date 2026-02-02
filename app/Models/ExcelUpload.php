<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'original_name',
        'file_path',
        'sheets_data',
        'total_sheets',
        'user_id',
    ];

    protected $casts = [
        'sheets_data' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sheets()
    {
        return $this->hasMany(ExcelSheet::class, 'excel_upload_id');
    }
}
