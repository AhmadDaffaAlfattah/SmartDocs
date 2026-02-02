@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">📊 Excel Viewer & Manager</h1>

            @if($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Upload Form -->
            <div class="card mb-5">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">⬆️ Upload Excel File</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('excel.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Select Excel File</label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
                </div>
            </div>

            <!-- Excel Files List -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">📁 Uploaded Excel Files</h5>
                </div>
                <div class="card-body">
                    @if($excelUploads->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>File Name</th>
                                        <th>Original Name</th>
                                        <th>Sheets</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excelUploads as $upload)
                                        <tr>
                                            <td>
                                                <strong>{{ basename($upload->file_name) }}</strong>
                                            </td>
                                            <td>{{ $upload->original_name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $upload->total_sheets }} sheets</span>
                                            </td>
                                            <td>{{ $upload->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('excel.view', $upload->id) }}" 
                                                   class="btn btn-sm btn-success" title="View">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('excel.download', $upload->id) }}" 
                                                   class="btn btn-sm btn-info" title="Download">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <form action="{{ route('excel.destroy', $upload->id) }}" method="POST" 
                                                      class="d-inline" onsubmit="return confirm('Delete this file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        {{ $excelUploads->links() }}
                    @else
                        <div class="alert alert-info" role="alert">
                            📝 No Excel files uploaded yet. Upload your first file to get started!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
        padding: 1.25rem;
    }

    .btn {
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .table {
        margin-bottom: 0;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
</style>
@endsection
