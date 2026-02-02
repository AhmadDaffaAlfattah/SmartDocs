<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineeringDocument extends Model
{
    use HasFactory;

    protected $table = 'engineering_documents';

    protected $fillable = [
        'judul',
        'folder',
        'folder_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'link',
        'tanggal_upload',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Folder
     */
    public function folderRelation()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    /**
     * Get breadcrumb path untuk folder
     */
    public function getFolderBreadcrumb()
    {
        if (!$this->folderRelation) {
            return $this->folder ?? '-';
        }

        $breadcrumb = [];
        $current = $this->folderRelation;

        while ($current) {
            array_unshift($breadcrumb, $current->nama_folder);
            $current = $current->parent;
        }

        return implode(' >> ', $breadcrumb);
    }

    /**
     * Folder yang tersedia dari database (child of Engineering folder)
     */
    public static function getFolders()
    {
        $engineeringFolder = Folder::where('nama_folder', 'Engineering')
            ->whereNull('parent_id')
            ->first();

        if (!$engineeringFolder) {
            return [];
        }

        return $engineeringFolder->children()
            ->orderBy('urutan', 'asc')
            ->pluck('nama_folder')
            ->toArray();
    }

    /**
     * Format file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Format tanggal upload
     */
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal_upload->format('d-m-Y H:i');
    }
}
