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
        $user = auth()->user();
        
        // Task 6: Block 'user' role from accessing folder page
        if ($user->role === 'user') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $query = Folder::whereNull('parent_id')
            ->orderBy('urutan');

        // Filter based on role if not Super Admin
        // admin engineering = folder engineering, etc.
        // Role is usually 'admin' or 'user', Bidang defines the department (Engineering, Operasi, etc.)
        $role = strtolower($user->role ?? '');
        $bidang = $user->bidang ?? '';
        
        if ($role !== 'super_admin' && $role !== 'super admin') {
            $allowedFolder = null;
            
            // Map Bidang to Folder Name
            // Bidangs: 'Engineering', 'Business Support', 'Operasi', 'Pemeliharaan', 'Keamanan', 'Lingkungan'
            $bidangLower = strtolower($bidang);
            
            // Allow matching root folder or any folder that matches
            if (!empty($bidang)) {
                if (str_contains($bidangLower, 'engineering')) {
                    $allowedFolder = 'Engineering';
                } elseif (str_contains($bidangLower, 'operasi')) {
                    $allowedFolder = 'Operasi';
                } elseif (str_contains($bidangLower, 'business support') || str_contains($bidangLower, 'bussiness support')) {
                    $allowedFolder = 'Business Support';
                } elseif (str_contains($bidangLower, 'keamanan')) {
                    $allowedFolder = 'Keamanan';
                } elseif (str_contains($bidangLower, 'lingkungan')) {
                    $allowedFolder = 'Lingkungan';
                } elseif (str_contains($bidangLower, 'pemeliharaan')) {
                    $allowedFolder = 'Pemeliharaan';
                }
            }

            if ($allowedFolder) {
                // Use LIKE to match folder names loosely
                $query->where('nama_folder', 'like', "%$allowedFolder%");
            } else {
                // If user is Admin but has no valid matching folder, ideally show nothing or allow basic access?
                // The prompt says "prevent non-super-admin users from creating subfolders" (previous)
                // New prompt implies they want to CRUD.
                // We will assume if they are Admin but no match, they see nothing.
                $query->where('id', -1); 
            }
        }

        $folders = $query->with('children.children.children.children.children')
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
        $this->authorizeFolderManagement();

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
        $this->authorizeFolderManagement();

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
        $this->authorizeFolderManagement();

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
        $this->authorizeFolderManagement();

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
        $this->authorizeFolderManagement();

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
     * Check if user is allowed to manage folders
     */
    private function authorizeFolderManagement()
    {
        $user = auth()->user();
        
        // Allow Super Admin
        if ($user->role === 'super_admin' || $user->role === 'super admin') {
            return true;
        }

        // Allow any Admin to manage
        // Logic: Administrators can manage folders consistent with their 'bidang' (filtered in index).
        if ($user->role === 'admin') {
            return true;
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola folder.');
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
