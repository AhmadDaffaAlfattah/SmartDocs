<!-- Folder Tree Sidebar Component -->
<div class="sidebar-item collapsible" id="folder-menu">
    <span class="toggle-icon">▼</span>
    <span class="menu-text">📁 Folder</span>
</div>

<div class="submenu folder-tree" id="submenu-folder">
    <div class="folder-tree-header">
        <button class="btn-add-root-folder" onclick="window.location.href='{{ route('folder.create') }}'">
            ➕ Tambah Folder
        </button>
    </div>
    
    <div class="folder-list">
        @forelse($folderTree as $folder)
            @include('components.folder-tree-item', ['folder' => $folder, 'level' => 0])
        @empty
            <div style="padding: 10px; color: #999; font-size: 12px;">
                Belum ada folder. <a href="{{ route('folder.create') }}">Buat sekarang</a>
            </div>
        @endforelse
    </div>
</div>
