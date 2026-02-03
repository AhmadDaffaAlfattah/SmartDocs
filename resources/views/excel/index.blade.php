@extends('layouts.master')

@section('title', 'SmartDocs - Excel Manager')

@push('styles')
    <style>
        /* Styles copied from layouts.app/bootstrap-like for consistency in this view */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            border-radius: 8px 8px 0 0;
            background-color: #f8f9fa;
        }

        .card-header.bg-primary {
            background-color: #2a5298;
            color: white;
        }

        .card-header.bg-secondary {
            background-color: #6c757d;
            color: white;
        }

        .card-body {
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
            margin-right: 5px;
        }

        .btn-primary {
            background-color: #2a5298;
            color: white;
        }
        .btn-primary:hover { background-color: #1e3c72; }

        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover { background-color: #218838; }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }
        .btn-info:hover { background-color: #138496; }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover { background-color: #c82333; }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table th, .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .table th {
            background-color: #343a40;
            color: white;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .bg-info { background-color: #17a2b8; color: white; }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: 8px 12px;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-5 { margin-bottom: 3rem; }
        .mb-0 { margin-bottom: 0; }
        .d-inline { display: inline-block; }
    </style>
@endpush

@section('content')
    <div style="padding: 20px;">
        <h1 class="mb-4" style="font-size: 28px; color: #333;">📊 Excel Viewer & Manager</h1>

        @if($message = Session::get('success'))
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif

        @if($message = Session::get('error'))
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @endif

        <!-- Upload Form -->
        <div class="card mb-5">
            <div class="card-header bg-primary">
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
                            <div style="color: red; font-size: 0.875em; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Upload
                    </button>
                </form>
            </div>
        </div>

        <!-- Excel Files List -->
        <div class="card">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">📁 Uploaded Excel Files</h5>
            </div>
            <div class="card-body">
                @if($excelUploads->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
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
                                                View
                                            </a>
                                            <a href="{{ route('excel.download', $upload->id) }}" 
                                               class="btn btn-sm btn-info" title="Download">
                                                Download
                                            </a>
                                            <form action="{{ route('excel.destroy', $upload->id) }}" method="POST" 
                                                  class="d-inline" onsubmit="return confirm('Delete this file?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 20px;">
                        {{ $excelUploads->links() }}
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        📝 No Excel files uploaded yet. Upload your first file to get started!
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
