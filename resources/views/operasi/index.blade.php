@extends('layouts.master')

@section('title', 'SmartDocs - Operasi')

@push('styles')
<style>
    /* Add any specific styles if needed, master covers most */
    .filter-section { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .filter-group { display: flex; align-items: center; gap: 10px; }
    .filter-select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-width: 150px; }
    .entries-select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; }
    .search-box input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 250px; }
    .table-section { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
    .table-header { padding: 15px 20px; border-bottom: 1px solid #eee; color: #666; font-size: 13px; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f8f9fa; padding: 12px 20px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #eee; font-size: 13px; }
    .table td { padding: 12px 20px; border-bottom: 1px solid #eee; font-size: 13px; color: #333; vertical-align: middle; }
    .action-btn { background: none; border: none; cursor: pointer; padding: 4px; transition: transform 0.2s; display: inline-block; }
    .action-btn:hover { transform: scale(1.1); }
    .pagination-section { padding: 20px; display: flex; justify-content: flex-end; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="engineering-page-header">
        <div class="engineering-page-title">
            <div style="font-size: 28px; font-weight: bold; color: #333333;">
                Document <span style="font-weight: bold;">» Operasi</span>
            </div>
            @if(Auth::user()->role === 'super_admin' || Auth::user()->role === 'admin')
            <a href="{{ route('operasi.create') }}" class="btn-tambah-data">
                ➕ Tambah Data
            </a>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-group">
            <label>Folder</label>
            <select name="folder" class="filter-select" onchange="filterByFolder(this.value)">
                <option value="">Semua</option>
                @foreach ($folders as $folder)
                    <option value="{{ $folder }}" {{ $selectedFolder == $folder ? 'selected' : '' }}>
                        {{ \Illuminate\Support\Str::limit($folder, 60) }}
                    </option>
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

        <div class="search-box" style="margin-left: 15px;">
            <form method="GET" action="{{ route('operasi.index') }}" style="display: flex; align-items: center;">
                <input type="text" name="search" placeholder="Search" value="{{ $searchQuery }}" style="margin-right: 5px;">
                <button type="submit" style="background: none; border: none; cursor: pointer;">
                    <img src="https://cdn-icons-png.flaticon.com/128/151/151773.png" alt="Search" width="16" height="16">
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
                                <td class="no" style="text-align: center;">{{ ($documents->currentPage() - 1) * $perPage + $key + 1 }}</td>
                                <td class="judul font-medium">{{ $document->judul }}</td>
                                <td>{{ $document->folder }}</td>
                                <td>{{ $document->tanggal_upload ? $document->tanggal_upload->format('d/m/Y') : '-' }}</td>
                                <td class="file-status">
                                    @if ($document->file_name)
                                        <span style="color: #333; font-weight: 500;">{{ $document->file_name }}</span>
                                    @elseif ($document->link)
                                        <a href="{{ $document->link }}" target="_blank" style="color: #0066cc; text-decoration: underline;">Link</a>
                                    @else
                                        <span style="color: #ccc;">-</span>
                                    @endif
                                </td>
                                <td class="action">
                                    @if ($document->file_path)
                                        <a href="{{ route('operasi.viewer', $document->id) }}" class="action-btn" title="View" target="_blank">
                                            <img src="{{ asset('images/view.png') }}" alt="View" width="24" height="24">
                                        </a>
                                    @elseif ($document->link)
                                        <a href="{{ $document->link }}" class="action-btn" title="Open Link" target="_blank">
                                            <img src="{{ asset('images/view.png') }}" alt="View" width="24" height="24">
                                        </a>
                                    @endif

                                    @if(Auth::user()->role === 'super_admin' || Auth::user()->role === 'admin')
                                        <a href="{{ route('operasi.edit', $document->id) }}" class="action-btn" title="Edit">
                                            <img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" width="24" height="24">
                                        </a>
                                        <form id="delete-form-{{ $document->id }}" action="{{ route('operasi.destroy', $document->id) }}" method="POST" style="display: inline;" onsubmit="event.preventDefault(); confirmDelete('delete-form-{{ $document->id }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn" title="Delete">
                                                <img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" alt="Delete" width="24" height="24">
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 40px; text-align: center; color: #999;">
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
@endsection

@push('scripts')
<script>
    function filterByFolder(value) {
        const url = new URL(window.location);
        if (value) {
            url.searchParams.set('folder', value);
        } else {
            url.searchParams.delete('folder');
        }
        window.location = url.toString();
    }

    function changePerPage(value) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        window.location = url.toString();
    }
</script>
@endpush
