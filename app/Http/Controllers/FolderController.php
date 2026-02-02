<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    /**
     * Display folder management page
     */
    public function index()
    {
        $folders = Folder::whereNull('parent_id')
            ->orderBy('urutan')
            ->with('children.children.children.children.children')
            ->get();

        return view('folder.index', [
            'folders' => $folders,
        ]);
    }

    /**
     * Get folder tree for sidebar
     */
    public function getSidebarTree()
    {
        $folderTree = Folder::getRootFolders();

        return view('components.folder-sidebar', [
            'folderTree' => $folderTree,
        ]);
    }

    /**
     * Show form to create new folder
     */
    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        $parentFolder = null;

        if ($parentId) {
            $parentFolder = Folder::findOrFail($parentId);
        }

        return view('folder.create', [
            'parentFolder' => $parentFolder,
        ]);
    }

    /**
     * Store newly created folder
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_folder' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        Folder::create($validated);

        return redirect(route('folder.index'))
            ->with('success', 'Folder berhasil ditambahkan!');
    }

    /**
     * Show form to edit folder
     */
    public function edit($id)
    {
        $folder = Folder::findOrFail($id);

        return view('folder.edit', [
            'folder' => $folder,
        ]);
    }

    /**
     * Update folder
     */
    public function update(Request $request, $id)
    {
        $folder = Folder::findOrFail($id);

        $validated = $request->validate([
            'nama_folder' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $folder->update($validated);

        return redirect(route('folder.index'))
            ->with('success', 'Folder berhasil diperbarui!');
    }

    /**
     * Delete folder
     */
    public function destroy($id)
    {
        try {
            $folder = Folder::findOrFail($id);
            $childrenCount = $folder->children()->count();

            // Delete this folder - CASCADE will automatically delete children
            $folder->delete();

            // Return JSON response for AJAX
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $childrenCount > 0
                        ? "Folder dan $childrenCount subfolder berhasil dihapus!"
                        : 'Folder berhasil dihapus!',
                ]);
            }

            // Redirect to index for non-AJAX requests
            return redirect(route('folder.index'))
                ->with('success', 'Folder berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus folder: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal menghapus folder!');
        }
    }

    /**
     * Get folder tree for API/AJAX
     */
    public function tree()
    {
        $folders = Folder::getRootFolders();
        $tree = [];

        foreach ($folders as $folder) {
            $tree[] = $folder->getHierarchy();
        }

        return response()->json($tree);
    }
}
