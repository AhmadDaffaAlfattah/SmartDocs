<!-- Folder Tree Dropdown with Searchable -->
<div class="folder-dropdown-wrapper">
    <label for="folder_id" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">
        Kategori <span style="color: red;">*</span>
    </label>
    
    <div class="folder-dropdown-container">
        <!-- Search Input -->
        <div class="folder-search-box">
            <input 
                type="text" 
                id="folderSearch" 
                class="folder-search-input" 
                placeholder="Cari atau ketik kategori..."
                autocomplete="off"
            >
            <span class="folder-search-icon">🔍</span>
        </div>

        <!-- Hidden Input to store selected value -->
        <input type="hidden" id="folder_id" name="folder_id" value="{{ old('folder_id', isset($document) ? $document->folder_id : '') }}">

        <!-- Folder Tree Display -->
        <div class="folder-tree-dropdown" id="folderTreeDropdown">
            @if(isset($folderTree) && count($folderTree) > 0)
                @include('components.folder-tree-dropdown-items', ['folders' => $folderTree, 'level' => 0])
            @elseif(isset($folders) && count($folders) > 0)
                @include('components.folder-tree-dropdown-items', ['folders' => $folders, 'level' => 0])
            @else
                <p style="padding: 12px; color: #999; font-size: 12px;">Tidak ada kategori tersedia</p>
            @endif
        </div>
    </div>

    <!-- Display Selected Folder -->
    <div class="folder-selected-display" id="folderSelectedDisplay">
        @if(old('folder_id', isset($document) ? $document->folder_id : ''))
            <strong>Dipilih:</strong> <span id="selectedFolderName"></span>
        @endif
    </div>
</div>

<style>
.folder-dropdown-wrapper {
    margin-bottom: 20px;
}

.folder-dropdown-container {
    border: 1px solid #999;
    border-radius: 4px;
    background: white;
    overflow: hidden;
}

.folder-search-box {
    padding: 8px 12px;
    border-bottom: 1px solid #ddd;
    position: relative;
}

.folder-search-input {
    width: 100%;
    padding: 8px 8px 8px 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 13px;
    box-sizing: border-box;
}

.folder-search-input:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 4px rgba(0, 102, 204, 0.2);
}

.folder-search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
}

.folder-tree-dropdown {
    max-height: 300px;
    overflow-y: auto;
    padding: 8px;
}

.folder-tree-item-dropdown {
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.folder-tree-item-dropdown:hover {
    background-color: #f0f0f0;
}

.folder-tree-item-dropdown.selected {
    background-color: #0066cc;
    color: white;
}

.folder-tree-item-dropdown.level-0 {
    padding-left: 12px;
    font-weight: 600;
}

.folder-tree-item-dropdown.level-1 {
    padding-left: 32px;
    font-size: 13px;
}

.folder-tree-item-dropdown.level-2 {
    padding-left: 52px;
    font-size: 13px;
}

.folder-tree-item-dropdown.hidden {
    display: none;
}

.folder-selected-display {
    padding: 10px 12px;
    background-color: #f9f9f9;
    border-top: 1px solid #ddd;
    font-size: 12px;
    color: #666;
    min-height: 20px;
}

.folder-selected-display:empty {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('folderSearch');
    const folderInput = document.getElementById('folder_id');
    const folderItems = document.querySelectorAll('.folder-tree-item-dropdown');
    const selectedDisplay = document.getElementById('folderSelectedDisplay');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        folderItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            const isMatch = text.includes(searchTerm);
            
            if (searchTerm === '') {
                item.classList.remove('hidden');
            } else if (isMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });

    // Click handler for folder items
    folderItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const folderId = this.getAttribute('data-folder-id');
            const folderName = this.getAttribute('data-folder-name');
            
            // Update hidden input
            folderInput.value = folderId;
            
            // Update visual selection
            folderItems.forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
            
            // Update display text
            selectedDisplay.innerHTML = `<strong>Dipilih:</strong> ${folderName}`;
            
            // Clear search
            searchInput.value = '';
            folderItems.forEach(item => item.classList.remove('hidden'));
        });
    });

    // Set initial selected state
    const selectedValue = folderInput.value;
    if (selectedValue) {
        folderItems.forEach(item => {
            if (item.getAttribute('data-folder-id') === selectedValue) {
                item.classList.add('selected');
                selectedDisplay.innerHTML = `<strong>Dipilih:</strong> ${item.getAttribute('data-folder-name')}`;
