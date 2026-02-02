@foreach($folders as $folder)
    <div class="folder-tree-item-dropdown" data-folder-id="{{ $folder->id }}" data-folder-name="{{ $folder->nama_folder }}" style="padding-left: {{ (($level + 1) * 16) }}px;">
        <span class="folder-icon">📁</span> {{ $folder->nama_folder }}
    </div>
    @if($folder->children && $folder->children->count() > 0)
        @include('components.folder-tree-dropdown-items', ['folders' => $folder->children, 'level' => $level + 1])
    @endif
@endforeach
