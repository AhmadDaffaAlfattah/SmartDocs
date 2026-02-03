@extends('layouts.master')

@section('title', 'SmartDocs - Keamanan')

@push('styles')
<style>
    /* Modal Overlay */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #e0e0e0; }
    .modal-header h2 { margin: 0; font-size: 18px; color: #333; }
    .modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; }
    .modal-body { padding: 20px; }
    .modal-footer { display: flex; gap: 10px; padding: 20px; border-top: 1px solid #e0e0e0; justify-content: flex-end; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px; }
    .form-group input[type="text"], .form-group input[type="url"], .form-group input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
    #editFolderTree { border: 1px solid #ddd; border-radius: 4px; max-height: 200px; overflow-y: auto; background: white; }
    .form-group input:focus { outline: none; border-color: #0066cc; box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1); }
    .required { color: #dc3545; }
    .help-text { font-size: 12px; color: #666; margin-top: 5px; margin-bottom: 0; }
    .error-text { font-size: 12px; color: #dc3545; margin-top: 5px; display: block; }
    .error-text.hidden { display: none; }
    .file-info { background: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 10px; font-size: 13px; color: #666; min-height: 20px; }
    .file-info a { color: #0066cc; text-decoration: none; }
    .file-info a:hover { text-decoration: underline; }
    .btn-cancel { padding: 10px 20px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 14px; color: #333; transition: background 0.2s; }
    .btn-cancel:hover { background: #e0e0e0; }
    .btn-submit { padding: 10px 20px; background: #0066cc; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; color: white; transition: background 0.2s; }
    .btn-submit:hover { background: #0052a3; }
    .folder-tree-item { padding: 10px 12px; cursor: pointer; border-radius: 4px; transition: background-color 0.2s; display: flex; align-items: center; gap: 8px; user-select: none; }
    .folder-tree-item:hover { background-color: #f0f0f0; }
    .folder-tree-item.selected { background-color: #0066cc; color: white; }
</style>
@endpush

@section('content')
    <!-- Keamanan Header -->
    <div class="engineering-page-header">
        <div class="engineering-page-title">
            <div style="font-size: 28px; font-weight: bold; color: #333333; margin-bottom: 20px;">
                Document <span style="font-weight: bold;">» Keamanan</span>
            </div>
            <a href="{{ route('keamanan.create') }}" class="btn-tambah-data">
                ➕ Tambah Data
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div style="margin: 20px 30px 0 30px;">
            <div class="alert alert-success">{{ $message }}</div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div style="margin: 20px 30px 0 30px;">
            <div class="alert alert-error">{{ $message }}</div>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-group">
            <label>Folder</label>
            <select name="folder" class="filter-select" onchange="filterByFolder(this.value)">
                <option value="">Semua</option>
                @foreach ($folders as $folder)
                    <option value="{{ $folder }}" {{ $selectedFolder == $folder ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($folder, 60) }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="margin-left: auto;">
            <label>Show</label>
            <select name="per_page" class="entries-select" onchange="changePerPage(this.value)">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
            </select>
            <span style="margin-left: 8px;">Entries</span>
        </div>
        <div class="search-box">
            <form method="GET" action="{{ route('keamanan.index') }}" style="display: flex; width: 100%;">
                <input type="text" name="search" placeholder="Search" value="{{ $searchQuery }}" style="flex: 1; margin-right: 10px;">
                <button type="submit" style="background: none; border: none; cursor: pointer; color: #666;">
                    <img src="https://cdn-icons-png.flaticon.com/128/151/151773.png" loading="lazy" alt="Magnifying glass" width="16" height="16">
                </button>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-section">
        <div class="table-header">
            <span>Show {{ $perPage }} Entries</span>
        </div>
        <div class="table-content">
            @if ($documents->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 30%;">Judul</th>
                            <th style="width: 15%;">Folder</th>
                            <th style="width: 15%;">Tanggal Upload</th>
                            <th style="width: 20%;">File / Link</th>
                            <th style="width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $key => $document)
                            <tr>
                                <td class="no">{{ ($documents->currentPage() - 1) * $perPage + $key + 1 }}</td>
                                <td class="judul">{{ $document->judul }}</td>
                                <td><span title="{{ $document->getFolderBreadcrumb() }}">{{ $document->getFolderBreadcrumb() }}</span></td>
                                <td>{{ $document->tanggal_upload ? $document->tanggal_upload->format('d/m/Y') : '-' }}</td>
                                <td class="file-status">
                                    @if ($document->file_name)
                                        <span style="color: #333; font-weight: 500;">{{ $document->file_name }}</span>
                                    @elseif ($document->link)
                                        <a href="{{ $document->link }}" target="_blank" style="color: #0066cc; text-decoration: underline; word-break: break-all;">{{ $document->link }}</a>
                                    @else
                                        <span style="color: #ccc;">-</span>
                                    @endif
                                </td>
                                <td class="action">
                                    @if ($document->file_path)
                                        <a href="{{ route('keamanan.viewer', $document->id) }}" class="action-btn view" title="View" target="_blank">
                                            <img src="{{ asset('images/view.png') }}" alt="View" width="32" height="32">
                                        </a>
                                    @elseif ($document->link)
                                        <a href="{{ $document->link }}" class="action-btn view" title="Open Link" target="_blank">
                                            <img src="{{ asset('images/view.png') }}" alt="View" width="32" height="32">
                                        </a>
                                    @endif
                                    <button class="action-btn edit" title="Edit" onclick="openEditModal({{ $document->id }})">
                                        <img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" width="32" height="32">
                                    </button>
                                    <form action="{{ route('keamanan.destroy', $document->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete" style="border: none; background: none; cursor: pointer;">
                                            <img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" loading="lazy" alt="Delete" width="32" height="32">
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>📄 Tidak ada dokumen ditemukan</p>
                </div>
            @endif
        </div>
        @if ($documents->count() > 0)
            <div class="pagination-section">
                {{ $documents->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Modal -->
    <div id="editModalOverlay" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Dokumen</h2>
                <button type="button" class="modal-close" onclick="closeEditModal()">✕</button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="editDocumentId" name="id">
                    <div class="form-group">
                        <label for="editJudul">Judul <span class="required">*</span></label>
                        <input type="text" id="editJudul" name="judul" placeholder="Masukkan judul dokumen">
                        <div class="error-text hidden" id="juldulError"></div>
                    </div>
                    <div class="form-group">
                        <label for="editFolderId">Folder <span class="required">*</span></label>
                        <div id="editFolderTree"></div>
                        <input type="hidden" id="editFolderId" name="folder_id">
                        <div class="error-text hidden" id="folderError"></div>
                    </div>
                    <div class="form-group">
                        <label for="editFile">File</label>
                        <div class="file-info" id="editFileInfo"></div>
                        <input type="file" id="editFile" name="file">
                        <p class="help-text">Biarkan kosong jika tidak ingin mengubah file. Ukuran maksimal: 10MB</p>
                        <div class="error-text hidden" id="fileError"></div>
                    </div>
                    <div class="form-group">
                        <label for="editLink">Link</label>
                        <input type="url" id="editLink" name="link" placeholder="https://example.com">
                        <p class="help-text">Masukkan URL lengkap (contoh: https://docs.google.com/...)</p>
                        <div class="error-text hidden" id="linkError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="button" class="btn-submit" onclick="submitEditForm()">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    function filterByFolder(value) {
        const url = new URL(window.location);
        if (value) { url.searchParams.set('folder', value); } else { url.searchParams.delete('folder'); }
        window.location = url.toString();
    }
    function changePerPage(value) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        window.location = url.toString();
    }
    function openEditModal(documentId) {
        const modalOverlay = document.getElementById('editModalOverlay');
        fetch(`/keamanan/${documentId}/get-edit-data`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editDocumentId').value = data.document.id;
                document.getElementById('editJudul').value = data.document.judul;
                document.getElementById('editFolderId').value = data.document.folder_id || '';
                document.getElementById('editLink').value = data.document.link || '';
                let fileInfo = '';
                if (data.document.file_path) {
                    fileInfo = `<strong>File saat ini:</strong> <a href="/keamanan/${data.document.id}/download">${data.document.file_name}</a>`;
                }
                if (data.document.link) {
                    fileInfo += (fileInfo ? '<br>' : '') + `<strong>Link saat ini:</strong> <a href="${data.document.link}" target="_blank">${data.document.link}</a>`;
                }
                document.getElementById('editFileInfo').innerHTML = fileInfo || 'Tidak ada file/link';
                const folderTreeHtml = renderFolderTree(data.folders, data.document.folder_id);
                document.getElementById('editFolderTree').innerHTML = folderTreeHtml;
                setTimeout(() => {
                    document.querySelectorAll('.folder-tree-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const folderId = this.dataset.folderId;
                            document.getElementById('editFolderId').value = folderId;
                            document.querySelectorAll('.folder-tree-item').forEach(i => i.classList.remove('selected'));
                            this.classList.add('selected');
                        });
                    });
                }, 100);
                modalOverlay.classList.add('active');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading document data');
            });
    }
    function renderFolderTree(folderTree, selectedFolderId) {
        const renderFolder = (folder, level = 0) => {
            const indent = (level * 16) + 'px';
            const isSelected = folder.id == selectedFolderId ? 'selected' : '';
            const hasChildren = folder.children && folder.children.length > 0;
            let html = `<div class="folder-tree-item ${isSelected}" data-folder-id="${folder.id}" style="padding-left: ${indent}">📁 ${folder.nama_folder}</div>`;
            if (hasChildren) { folder.children.forEach(child => { html += renderFolder(child, level + 1); }); }
            return html;
        };
        let html = '';
        folderTree.forEach(folder => { html += renderFolder(folder); });
        return html || '<p style="color: #999; padding: 10px;">Tidak ada folder</p>';
    }
    function closeEditModal() {
        document.getElementById('editModalOverlay').classList.remove('active');
    }
    function submitEditForm() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        const documentId = document.getElementById('editDocumentId').value;
        document.querySelectorAll('.error-text').forEach(el => el.classList.add('hidden'));
        fetch(`/keamanan/${documentId}`, {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        })
        .then(response => {
            if (response.ok) { window.location.reload(); } else if (response.status === 422) {
                return response.json().then(data => {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorEl = document.getElementById(`${key}Error`);
                            if (errorEl) { errorEl.textContent = data.errors[key][0]; errorEl.classList.remove('hidden'); }
                        });
                    }
                });
            } else { alert('Error submitting form'); }
        })
        .catch(error => { console.error('Error:', error); alert('Error submitting form'); });
    }
    document.getElementById('editModalOverlay')?.addEventListener('click', function(e) { if (e.target === this) { closeEditModal(); } });
    
    // NOTE: Sidebar and Profile logic is now handled in layouts.master
    </script>
@endpush
