<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LingkunganDocument extends Model
{
    use HasFactory;

    protected $table = 'lingkungan_documents';

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

        return implode(' > ', $breadcrumb);
    }

    /**
     * Get list of folders
     */
    public static function getFolders()
    {
        $folder = Folder::where('nama_folder', 'Lingkungan')
            ->whereNull('parent_id')
            ->first();

        if (!$folder) {
            return [];
        }

        return $folder->children()
            ->orderBy('urutan', 'asc')
            ->pluck('nama_folder')
            ->toArray();
    }
}
