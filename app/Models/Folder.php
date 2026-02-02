<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';

    protected $fillable = [
        'nama_folder',
        'deskripsi',
        'parent_id',
        'urutan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Folder parent
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Relasi: Folder children
     */
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id')->orderBy('urutan', 'asc');
    }

    /**
     * Get all root folders (parent_id = null) with all nested children
     */
    public static function getRootFolders()
    {
        return self::whereNull('parent_id')
            ->with('children.children.children.children')  // Load up to 4 levels deep
            ->orderBy('urutan', 'asc')
            ->get();
    }

    /**
     * Get folder hierarchy recursively
     */
    public function getHierarchy()
    {
        $hierarchy = [
            'id' => $this->id,
            'nama_folder' => $this->nama_folder,
            'deskripsi' => $this->deskripsi,
            'parent_id' => $this->parent_id,
            'children' => [],
        ];

        foreach ($this->children as $child) {
            $hierarchy['children'][] = $child->getHierarchy();
        }

        return $hierarchy;
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $current = $this;

        while ($current) {
            array_unshift($breadcrumb, $current);
            $current = $current->parent;
        }

        return $breadcrumb;
    }
}
