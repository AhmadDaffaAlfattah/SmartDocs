@extends('layouts.master')

@section('title', 'SmartDocs - Folder')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/folder.css') }}">
@endpush

@section('content')
@section('content')
    @php
        $user = Auth::user();
        $isSuperAdmin = $user->role === 'super_admin' || $user->role === 'super admin';
        $isAdmin = $user->role === 'admin';
        // Since the controller already filters the folders to only show what the user owns,
        // we can allow any 'admin' to see the management buttons for the folders they see.
        $canManageFolders = $isSuperAdmin || $isAdmin;
    @endphp

    <!-- Header -->
    <div class="engineering-page-header">
        <div class="engineering-page-title">
            <div style="font-size: 28px; font-weight: bold; color: #333333; margin-bottom: 20px;">
                Folder
            </div>
            @if($canManageFolders)
            <a href="{{ route('folder.create') }}" class="btn-tambah-data">
                ➕ Tambah Folder
            </a>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if ($message = Session::get('success'))
        <div style="margin: 20px 30px 0 30px;">
            <div class="alert alert-success">
                ✓ {{ $message }}
            </div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div style="margin: 20px 30px 0 30px;">
            <div class="alert alert-error">
                ✕ {{ $message }}
            </div>
        </div>
    @endif

    <!-- Folder Tree -->
    <div class="folder-tree-container">
        @if($folders->isEmpty())
            <div class="empty-state">
                <p>📁 Belum ada folder. 
                @if($canManageFolders)
                    <a href="{{ route('folder.create') }}">Buat folder pertama Anda</a>
                @endif
                </p>
            </div>
        @else
            <div class="folder-tree">
                @foreach($folders as $folder)
                    @include('components.folder-tree-display', ['folder' => $folder, 'level' => 0, 'canManageFolders' => $canManageFolders])
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle folder expand/collapse
        document.addEventListener('click', function(e) {
            if (e.target.closest('.folder-toggle-btn')) {
                const btn = e.target.closest('.folder-toggle-btn');
                const children = btn.closest('.folder-tree-item').querySelector('.folder-children');
                
                if (children) {
                    const isHidden = children.style.display === 'none';
                    children.style.display = isHidden ? 'block' : 'none';
                    btn.textContent = isHidden ? '▼' : '▶';
                }
                e.stopPropagation();
            }
        });

        // Delete folder
        function deleteFolder(id) {
            const folderItem = event.target.closest('.folder-tree-item');
            const childrenDiv = folderItem.querySelector('.folder-children');
            const hasChildren = childrenDiv && childrenDiv.querySelector('.folder-tree-item');
            
            let confirmMessage = 'Yakin ingin menghapus folder ini?';
            if (hasChildren) {
                confirmMessage = 'Folder ini memiliki subfolder. Yakin ingin menghapus folder dan semua subfolder?';
            }
            
            showConfirmModal({
                title: 'Hapus Folder',
                message: confirmMessage,
                type: 'danger',
                confirmText: 'Ya, Hapus',
                onConfirm: () => {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    
                    fetch(`/folder/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            throw new Error('Delete failed');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            // alert(data.message); // removed alert for smoother UX or use toast if available
                            // Fade out and reload
                            folderItem.style.opacity = '0';
                            folderItem.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => {
                                location.reload();
                            }, 300);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal menghapus folder! Silakan coba lagi.');
                    });
                }
            });
        }
    </script>
@endpush
