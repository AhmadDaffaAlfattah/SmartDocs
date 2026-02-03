@extends('layouts.master')

@section('title', 'SmartDocs - Tambah Dokumen Operasi')

@push('styles')
<style>
    .folder-tree-item { padding: 10px 12px; cursor: pointer; border-radius: 4px; transition: background-color 0.2s; display: flex; align-items: center; gap: 8px; user-select: none; }
    .folder-tree-item:hover { background-color: #f0f0f0; }
    .folder-tree-item.selected { background-color: #0066cc; color: white; }
</style>
@endpush

@section('content')
    <div class="engineering-page-header">
        <div class="engineering-page-title">
            <div style="font-size: 28px; font-weight: bold; color: #333;">
                Operasi <span style="font-weight: bold;">» Tambah Dokumen</span>
            </div>
        </div>
    </div>

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 800px; margin: 30px auto 0;">
        <form action="{{ route('operasi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Judul -->
            <div style="margin-bottom: 20px;">
                <label for="judul" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Judul <span style="color: red;">*</span></label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px;" 
                       placeholder="Masukkan judul dokumen">
                @error('judul') <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <!-- Folder Tree -->
            <div style="margin-bottom: 20px;">
                <label for="folder_id" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Kategori <span style="color: red;">*</span></label>
                <div id="folderTree" style="border: 1px solid #999; border-radius: 4px; max-height: 300px; overflow-y: auto; background: white;"></div>
                <input type="hidden" id="folder_id" name="folder_id" value="">
                @error('folder_id') <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <!-- File -->
            <div style="margin-bottom: 20px;">
                <label for="file" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Upload File</label>
                <input type="file" id="file" name="file" style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px;">
                <p style="color: #666; font-size: 12px; margin-top: 5px;">Format: Excel, PDF, Word, dll | Ukuran maksimal: 10MB</p>
                @error('file') <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 20px; text-align: center; color: #999; font-size: 12px; font-weight: 600;">ATAU</div>

            <!-- Link -->
            <div style="margin-bottom: 20px;">
                <label for="link" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Masukkan Link</label>
                <input type="url" id="link" name="link" value="{{ old('link') }}" 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px;" 
                       placeholder="https://...">
                @error('link') <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p> @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button type="submit" style="background-color: #333; color: white; border: none; padding: 10px 24px; border-radius: 4px; cursor: pointer; font-weight: 600;">Simpan</button>
                <a href="{{ route('operasi.index') }}" style="background-color: #999; color: white; border: none; padding: 10px 24px; border-radius: 4px; text-decoration: none; font-weight: 600;">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const folderTreeData = {!! json_encode($folderTree) !!};
    
    function renderFolderTree(folderTree, selectedFolderId = null) {
        const renderFolder = (folder, level = 0) => {
            const indent = (level * 16) + 'px';
            const isSelected = folder.id == selectedFolderId ? 'selected' : '';
            const hasChildren = folder.children && folder.children.length > 0;
            let html = `<div class="folder-tree-item ${isSelected}" data-folder-id="${folder.id}" style="padding-left: ${indent}">
                📁 ${folder.nama_folder}
            </div>`;
            
            if (hasChildren) {
                folder.children.forEach(child => {
                    html += renderFolder(child, level + 1);
                });
            }
            return html;
        };
        
        let html = '';
        folderTree.forEach(folder => {
            html += renderFolder(folder);
        });
        return html || '<p style="color: #999; padding: 10px;">Tidak ada folder</p>';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const folderTreeHtml = renderFolderTree(folderTreeData);
        document.getElementById('folderTree').innerHTML = folderTreeHtml;
        
        document.querySelectorAll('.folder-tree-item').forEach(item => {
            item.addEventListener('click', function() {
                const folderId = this.dataset.folderId;
                document.getElementById('folder_id').value = folderId;
                document.querySelectorAll('.folder-tree-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    });
</script>
@endpush
