<!-- Folder Tree Item Component (Recursive) -->
<div class="folder-tree-item" style="margin-left: {{ $level * 15 }}px;">
    @if($folder->children->count() > 0)
        <span class="folder-toggle" onclick="toggleFolder(this)">▶</span>
    @else
        <span class="folder-toggle empty">•</span>
    @endif
    
    <span class="folder-icon">📁</span>
    
    <span class="folder-name" onclick="window.location.href='{{ route('folder.show', $folder->id) }}'">
        {{ $folder->nama_folder }}
    </span>
    
    <div class="folder-actions">
        <button class="folder-btn btn-add" title="Tambah Subfolder" 
            onclick="window.location.href='{{ route('folder.create', ['parent_id' => $folder->id]) }}'">
            ➕
        </button>
        <button class="folder-btn btn-edit" title="Edit" 
            onclick="window.location.href='{{ route('folder.edit', $folder->id) }}'">
            ✏️
        </button>
        <button class="folder-btn btn-delete" title="Hapus" 
            onclick="deleteFolder({{ $folder->id }})">
            ✕
        </button>
    </div>
    
    @if($folder->children->count() > 0)
        <div class="folder-children" style="display: none;">
            @foreach($folder->children as $child)
                @include('components.folder-tree-item', ['folder' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>

<script>
function toggleFolder(element) {
    const children = element.closest('.folder-tree-item').querySelector('.folder-children');
    if (children) {
        children.style.display = children.style.display === 'none' ? 'block' : 'none';
        element.textContent = children.style.display === 'none' ? '▶' : '▼';
    }
}

function deleteFolder(id) {
    if (confirm('Yakin ingin menghapus folder ini?')) {
        fetch(`/folder/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        });
    }
}
</script>
