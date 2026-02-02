@extends('layouts.app')

@section('content')
<div class="excel-viewer-container">
    <!-- Header -->
    <div class="excel-header">
        <div class="header-content">
            <a href="{{ route('engineering.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Engineering
            </a>
            <div class="header-info">
                <h2>📊 {{ $document->judul }}</h2>
                <p class="text-muted">{{ count($sheetNames) }} sheet{{ count($sheetNames) > 1 ? 's' : '' }} • Uploaded {{ $document->created_at->diffForHumans() }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('engineering.download', $document->id) }}" class="btn btn-info">
                    <i class="fas fa-download"></i> Download
                </a>
                <a href="{{ route('engineering.edit', $document->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <div class="excel-wrapper">
        <!-- Navbar Sheet -->
        <nav class="excel-navbar">
            <h5 class="navbar-title">📄 Sheets</h5>
            <div class="navbar-items">
                @foreach($sheetNames as $sheetName)
                    <a href="javascript:void(0)" 
                       onclick="changeSheet('{{ $sheetName }}')"
                       class="navbar-item {{ $selectedSheetName === $sheetName ? 'active' : '' }}"
                       data-sheet="{{ $sheetName }}"
                       title="{{ $sheetName }}">
                        <span class="sheet-icon">📋</span>
                        <span class="sheet-name">{{ $sheetName }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        <!-- Main Content -->
        <div class="excel-content">
            <div class="sheet-header">
                <h3>{{ $selectedSheetName }}</h3>
                <p class="sheet-info">Sheet 1 of {{ count($sheetNames) }}</p>
            </div>

            <!-- Tabel Data -->
            <div class="table-wrapper">
                @php
                    $isValidData = is_array($sheetData) && count($sheetData) > 0;
                    
                    if ($isValidData) {
                        $headerRow = reset($sheetData);
                        $headerRow = is_array($headerRow) ? $headerRow : [];
                        $dataRows = count($sheetData) > 1 ? array_slice($sheetData, 1) : [];
                        $maxCols = max(array_map(function($row) {
                            return is_array($row) ? count($row) : 0;
                        }, array_merge([$headerRow], $dataRows)));
                    }
                @endphp

                @if($isValidData && count($headerRow) > 0)
                    <table class="excel-table">
                        <thead>
                            <tr>
                                @foreach($headerRow as $header)
                                    @if(!empty($header))
                                        <th>{{ $header }}</th>
                                    @else
                                        <th style="opacity: 0.5;">-</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($dataRows) > 0)
                                @foreach($dataRows as $row)
                                    <tr>
                                        @if(is_array($row))
                                            @foreach($row as $cell)
                                                <td>{{ !empty($cell) ? $cell : '-' }}</td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="{{ count($headerRow) }}" class="text-center text-muted py-4">
                                        📊 No data available in this sheet
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning" role="alert">
                        ⚠️ This sheet appears to be empty or invalid
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .excel-viewer-container {
        width: 100%;
        height: 100vh;
        display: flex;
        flex-direction: column;
        background: #f5f5f5;
    }

    /* Header */
    .excel-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .header-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        width: 100%;
    }

    .back-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
        min-width: fit-content;
    }

    .back-btn:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }

    .header-info {
        flex: 1;
    }

    .header-info h2 {
        margin-bottom: 0.25rem;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .header-info p {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        white-space: nowrap;
    }

    .header-actions .btn {
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
    }

    .btn-info {
        background: rgba(255,255,255,0.2);
    }

    .btn-info:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }

    .btn-warning {
        background: rgba(255,193,7,0.8);
    }

    .btn-warning:hover {
        background: rgba(255,193,7,1);
    }

    /* Main Wrapper */
    .excel-wrapper {
        display: flex;
        flex: 1;
        overflow: hidden;
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
    }

    /* Navbar */
    .excel-navbar {
        width: 250px;
        background: white;
        border-right: 1px solid #e0e0e0;
        padding: 1.5rem 0;
        overflow-y: auto;
        box-shadow: 1px 0 4px rgba(0,0,0,0.05);
    }

    .navbar-title {
        padding: 0 1.5rem;
        margin-bottom: 1rem;
        color: #333;
        font-weight: 600;
        font-size: 1rem;
    }

    .navbar-items {
        display: flex;
        flex-direction: column;
    }

    .navbar-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        color: #555;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .navbar-item:hover {
        background: #f5f5f5;
        color: #667eea;
        padding-left: 1.75rem;
    }

    .navbar-item.active {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border-left-color: #667eea;
        font-weight: 600;
        padding-left: 1.75rem;
    }

    .sheet-icon {
        font-size: 1.2rem;
    }

    .sheet-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.95rem;
    }

    /* Content Area */
    .excel-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 2rem;
        background: white;
    }

    .sheet-header {
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 1rem;
    }

    .sheet-header h3 {
        color: #333;
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .sheet-info {
        color: #999;
        font-size: 0.9rem;
    }

    /* Tabel */
    .table-wrapper {
        flex: 1;
        overflow: auto;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: white;
    }

    .excel-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
        background: white;
    }

    .excel-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .excel-table thead th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid rgba(255,255,255,0.2);
        word-break: break-word;
        white-space: normal;
        min-width: 100px;
    }

    .excel-table tbody tr {
        border-bottom: 1px solid #e8e8e8;
        transition: background 0.2s ease;
    }

    .excel-table tbody tr:hover {
        background: #f8f9ff;
    }

    .excel-table tbody tr:nth-child(even) {
        background: #fafbff;
    }

    .excel-table tbody td {
        padding: 12px 16px;
        word-break: break-word;
        color: #333;
        max-width: 300px;
        white-space: pre-wrap;
    }

    .excel-table tbody td:empty::before {
        content: '-';
        color: #ccc;
    }

    /* Scrollbar Styling */
    .table-wrapper::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .excel-wrapper {
            flex-direction: column;
        }

        .excel-navbar {
            width: 100%;
            max-height: 200px;
            border-right: none;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 0;
        }

        .navbar-items {
            flex-direction: row;
            overflow-x: auto;
        }

        .navbar-item {
            flex-shrink: 0;
            border-bottom: 3px solid transparent;
            border-left: none;
            padding: 0.75rem 1rem;
        }

        .navbar-item.active {
            border-bottom-color: #667eea;
            border-left: none;
        }

        .header-content {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .excel-content {
            padding: 1rem;
        }

        .excel-table thead th {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .excel-table tbody td {
            padding: 0.5rem;
        }
    }
</style>

<script>
    function changeSheet(sheetName) {
        // Update navbar active
        document.querySelectorAll('.navbar-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-sheet') === sheetName) {
                item.classList.add('active');
            }
        });
        
        // Update header
        document.querySelector('.sheet-header h3').textContent = sheetName;
        
        // Update table data
        const sheetsData = {!! json_encode($sheetsData) !!};
        const sheetData = sheetsData[sheetName] || [];
        
        updateTable(sheetData);
    }
    
    function updateTable(sheetData) {
        if (!sheetData || sheetData.length === 0) {
            document.querySelector('.table-wrapper').innerHTML = 
                '<div class="alert alert-warning" role="alert">⚠️ This sheet appears to be empty</div>';
            return;
        }
        
        const headerRow = sheetData[0] || [];
        const dataRows = sheetData.slice(1);
        
        let html = '<table class="excel-table"><thead><tr>';
        
        // Header
        headerRow.forEach(header => {
            html += `<th>${header || '-'}</th>`;
        });
        html += '</tr></thead><tbody>';
        
        // Data rows
        if (dataRows.length > 0) {
            dataRows.forEach(row => {
                html += '<tr>';
                if (Array.isArray(row)) {
                    row.forEach(cell => {
                        html += `<td>${cell || '-'}</td>`;
                    });
                }
                html += '</tr>';
            });
        } else {
            html += `<tr><td colspan="${headerRow.length}" class="text-center text-muted py-4">📊 No data available</td></tr>`;
        }
        
        html += '</tbody></table>';
        document.querySelector('.table-wrapper').innerHTML = html;
    }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
