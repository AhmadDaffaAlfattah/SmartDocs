<div class="folder-tree-item" style="--level: {{ $level }};">
    <div class="folder-item-content">
        <!-- Toggle Button -->
        @if($folder->children->count() > 0)
            <button class="folder-toggle-btn" title="Expand/Collapse">^</button>
        @else
            <span class="folder-toggle-spacer"></span>
        @endif

        <!-- Folder Icon & Name -->
        <span class="folder-icon">📁</span>
        <span class="folder-name">{{ $folder->nama_folder }}</span>

        <!-- Action Buttons -->
        <div class="folder-actions">
            <a href="{{ route('folder.create', ['parent_id' => $folder->id]) }}" class="btn-action" title="Tambah Subfolder">
                <img src="https://cdn-icons-png.flaticon.com/128/11607/11607148.png" alt="Plus" width="18" height="18">
            </a>
            <a href="{{ route('folder.edit', $folder->id) }}" class="btn-action" title="Edit">
                <img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" width="18" height="18">
            </a>
            <button onclick="deleteFolder({{ $folder->id }})" class="btn-action btn-delete" title="Hapus">
                <img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" alt="Delete" width="18" height="18">
            </button>
        </div>
    </div>

    <!-- Children Folders -->
    @if($folder->children->count() > 0)
        <div class="folder-children">
            @foreach($folder->children as $child)
                @include('components.folder-tree-display', ['folder' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
