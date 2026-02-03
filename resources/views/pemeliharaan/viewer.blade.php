@extends('layouts.master')

@section('title', 'SmartDocs - Viewer ' . $document->judul)

@push('styles')
    <!-- PDF.js untuk viewer PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- SheetJS untuk membaca Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- Handsontable untuk edit spreadsheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/12.4.0/handsontable.full.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/12.4.0/handsontable.full.min.js"></script>
    <style>
        .viewer-container {
            background: white;
            padding: 20px;
            /* margin: 20px; Removed for master layout */
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .viewer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .viewer-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .viewer-controls {
            display: flex;
            gap: 10px;
        }
        .viewer-controls button, .viewer-controls a {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 13px;
        }
        .btn-download {
            background-color: #333;
            color: white;
        }
        .btn-download:hover {
            background-color: #555;
        }
        .btn-back {
            background-color: #999;
            color: white;
        }
        .btn-back:hover {
            background-color: #777;
        }
        .btn-prev, .btn-next {
            background-color: #666;
            color: white;
            padding: 8px 15px;
        }
        .btn-prev:hover, .btn-next:hover {
            background-color: #444;
        }
        .pdf-viewer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        #pdfCanvas {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-nav {
            display: flex;
            gap: 15px;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .pdf-nav p {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        .excel-viewer {
            width: 100%;
            height: 700px;
            overflow: auto;
        }
        #spreadsheet {
            width: 100%;
            height: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="viewer-container">
        <div class="viewer-header">
            <h1>{{ $document->judul }}</h1>
            <div class="viewer-controls">
                <a href="{{ route('pemeliharaan.index') }}" class="btn-back">← Kembali</a>
                <button type="button" onclick="downloadFile()" class="btn-download">📥 Download</button>
            </div>
        </div>

        @if($fileType === 'pdf')
            <div class="pdf-viewer">
                <canvas id="pdfCanvas"></canvas>
                <div class="pdf-nav">
                    <button type="button" class="btn-prev" onclick="prevPdfPage()">← Previous</button>
                    <p>Page <span id="pdfPageNum">1</span> of <span id="pdfPageCount">1</span></p>
                    <button type="button" class="btn-next" onclick="nextPdfPage()">Next →</button>
                </div>
            </div>
        @elseif(in_array($fileType, ['xls', 'xlsx', 'csv']) && $isExcel && $sheetsData)
            <!-- Excel Viewer dengan Navbar Sheets -->
            <div style="display: flex; flex-direction: column; gap: 0; border: 1px solid #e0e0e0; border-radius: 4px; height: 600px; overflow: hidden;">
                <!-- Toolbar untuk Add Row -->
                <div style="background: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #e0e0e0; display: flex; gap: 10px; align-items: center;">
                    <button type="button" onclick="addNewRow()" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 12px;">
                        ➕ Add Row
                    </button>
                    <button type="button" onclick="addNewColumn(currentSheetName)" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 12px;">
                        ➕ Add Column
                    </button>
                    <span style="color: #999; font-size: 12px;">✏️ Klik cell untuk edit | Right-click nomor/kolom untuk hapus</span>
                </div>
                
                <!-- Main Content -->
                <div style="display: flex; gap: 0; flex: 1; overflow: hidden;">
                    <!-- Navbar Sheets -->
                    <div style="width: 250px; background: white; border-right: 1px solid #e0e0e0; padding: 15px; overflow-y: auto;">
                        <h5 style="margin: 0 0 15px 0; color: #333; font-weight: 600;">📄 Sheets</h5>
                        @foreach($sheetNames as $index => $sheetName)
                            <button type="button" 
                                    class="sheet-tab {{ $index === 0 ? 'active' : '' }}"
                                    onclick="changeExcelSheet(this, '{{ $sheetName }}')"
                                    data-sheet="{{ $sheetName }}"
                                    style="display: block; width: 100%; padding: 10px; margin-bottom: 8px; border: 1px solid #ddd; background: white; color: #515151; border-radius: 4px; cursor: pointer; text-align: left; transition: all 0.3s; font-weight: 500;">
                                📋 {{ $sheetName }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Tabel Excel -->
                    <div style="flex: 1; overflow: auto; background: white;">
                        <div id="excel-container" style="padding: 0;">
                            <table id="excel-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                <!-- Will be filled by JavaScript -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                const excelSheetsData = {!! json_encode($sheetsData) !!};
                let currentEditedData = {}; // Store edited data
                let newRowsData = {}; // Store new rows added by user
                let deletedRows = {}; // Track deleted rows
                let deletedColumns = {}; // Track deleted columns
                let newColumnsData = {}; // Store new columns
                let currentSheetName = null;
                let sheetColumnCount = {}; // Track column count per sheet
                
                // Load sheet pertama saat halaman load
                document.addEventListener('DOMContentLoaded', function() {
                    const firstSheet = document.querySelector('.sheet-tab');
                    if (firstSheet) {
                        currentSheetName = firstSheet.getAttribute('data-sheet');
                        loadExcelSheet(currentSheetName);
                    }
                });
                
                function changeExcelSheet(button, sheetName) {
                    currentSheetName = sheetName;
                    
                    // Remove active dari semua button
                    document.querySelectorAll('.sheet-tab').forEach(btn => {
                        btn.style.background = 'white';
                        btn.style.color = '#555';
                        btn.style.fontWeight = '500';
                        btn.style.borderColor = '#ddd';
                    });
                    
                    // Add active ke button yang diklik
                    button.style.background = '#667eea';
                    button.style.color = 'white';
                    button.style.fontWeight = '600';
                    button.style.borderColor = '#667eea';
                    
                    // Load data
                    loadExcelSheet(sheetName);
                }
                
                function loadExcelSheet(sheetName) {
                    const sheetData = excelSheetsData[sheetName] || [];
                    const table = document.getElementById('excel-table');
                    
                    if (sheetData.length === 0) {
                        table.innerHTML = '<tr><td style="padding: 8px; text-align: center; color: #999; font-size: 12px;">No data</td></tr>';
                        return;
                    }
                    
                    // Initialize tracking
                    if (!deletedRows[sheetName]) deletedRows[sheetName] = [];
                    if (!deletedColumns[sheetName]) deletedColumns[sheetName] = [];
                    if (!newColumnsData[sheetName]) newColumnsData[sheetName] = [];
                    if (!newRowsData[sheetName]) newRowsData[sheetName] = [];
                    
                    // Header row dengan warna dari dashboard
                    let html = '<thead><tr style="background: #FAFAFA; color: black; position: sticky; top: 0;">';
                    html += '<th style="padding: 8px 6px; text-align: center; font-weight: 600; border: 1px solid #666; width: 45px; min-width: 45px; background: #FAFAFA; font-size: 11px;" title="Right-click untuk delete row">🔧</th>';
                    
                    const headerRow = sheetData[0] || [];
                    headerRow.forEach((header, idx) => {
                        if (!deletedColumns[sheetName].includes(idx)) {
                            html += `<th data-col="${idx}" style="padding: 8px 6px; text-align: center; font-weight: 600; border: 1px solid #666; min-width: 80px; font-size: 11px; cursor: context-menu;" oncontextmenu="showColumnMenu(event, ${idx}, '${sheetName}')" title="Right-click untuk delete column">${header || '-'}</th>`;
                        }
                    });
                    
                    // Tombol tambah kolom
                    html += `<th style="padding: 8px; text-align: center; background: #FAFAFA; border: 1px solid #666; min-width: 30px; cursor: pointer;" onclick="addNewColumn('${sheetName}')" title="Klik untuk tambah kolom" style="font-size: 12px;">➕</th>`;
                    html += '</tr></thead><tbody>';
                    
                    const dataRows = sheetData.slice(1);
                    if (dataRows.length > 0) {
                        dataRows.forEach((row, rowIdx) => {
                            const actualRowNum = rowIdx + 2;
                            const isDeleted = deletedRows[sheetName].includes(rowIdx);
                            
                            if (!isDeleted) {
                                html += `<tr data-row="${rowIdx + 1}" style="background: #ffffff; color: #333; border-bottom: 1px solid #e0e0e0; font-size: 12px;">`;
                                
                                // Row number dengan context menu
                                html += `<td style="padding: 0; text-align: center; background: #f5f5f5; border: 1px solid #ddd; font-weight: 600; color: #666; width: 45px; min-width: 45px; user-select: none; font-size: 11px;" oncontextmenu="deleteRow(event, ${rowIdx}, '${sheetName}'); return false;" title="Right-click untuk delete row">${actualRowNum}</td>`;
                                
                                if (Array.isArray(row)) {
                                    row.forEach((cell, colIdx) => {
                                        if (!deletedColumns[sheetName].includes(colIdx)) {
                                            const cellKey = `${sheetName}_${rowIdx + 1}_${colIdx}`;
                                            const cellValue = currentEditedData[cellKey] !== undefined ? currentEditedData[cellKey] : (cell || '');
                                            html += `<td data-row="${rowIdx + 1}" data-col="${colIdx}" data-sheet="${sheetName}" style="padding: 0; border: 1px solid #ddd;">
                                                <input type="text" value="${cellValue}" onchange="saveCellData(this, '${sheetName}', ${rowIdx + 1}, ${colIdx})" style="width: 100%; padding: 6px 4px; border: none; background: white; color: #333; font-family: Arial, sans-serif; font-size: 12px; cursor: text; box-sizing: border-box;" />
                                            </td>`;
                                        }
                                    });
                                }
                                
                                // Kolom untuk delete/option
                                html += `<td style="padding: 0; text-align: center; background: #f5f5f5; border: 1px solid #ddd;"><button onclick="deleteRow(null, ${rowIdx}, '${sheetName}')" style="background: none; border: none; cursor: pointer; color: #e74c3c; font-weight: bold; padding: 4px 2px;">✕</button></td>`;
                                html += '</tr>';
                            }
                        });
                    } else {
                        html += `<tr><td colspan="100" style="padding: 12px; text-align: center; color: #999; font-size: 12px;">No data available</td></tr>`;
                    }
                    
                    // New rows added by user
                    const newRows = newRowsData[sheetName] || [];
                    newRows.forEach((row, newRowIdx) => {
                        const actualRowNum = dataRows.filter((_, i) => !deletedRows[sheetName].includes(i)).length + 2 + newRowIdx;
                        html += `<tr data-new-row="${newRowIdx}" style="background: #fffef0; color: #333; border-bottom: 1px solid #e0e0e0; font-size: 12px;">`;
                        
                        // Row number
                        html += `<td style="padding: 0; text-align: center; background: #fffacd; border: 1px solid #ddd; font-weight: 600; color: #666; width: 45px; min-width: 45px; user-select: none; font-size: 11px;" oncontextmenu="deleteNewRow(event, ${newRowIdx}, '${sheetName}'); return false;" title="Right-click untuk delete row">${actualRowNum}</td>`;
                        
                        const colCount = headerRow.length;
                        for (let colIdx = 0; colIdx < colCount + (newColumnsData[sheetName] ? newColumnsData[sheetName].length : 0); colIdx++) {
                            if (!deletedColumns[sheetName].includes(colIdx)) {
                                const cellValue = row[colIdx] || '';
                                html += `<td data-new-row="${newRowIdx}" data-col="${colIdx}" data-sheet="${sheetName}" style="padding: 0; border: 1px solid #ddd;">
                                    <input type="text" value="${cellValue}" onchange="saveNewRowData(this, '${sheetName}', ${newRowIdx}, ${colIdx})" style="width: 100%; padding: 6px 4px; border: none; background: #fffef0; color: #333; font-family: Arial, sans-serif; font-size: 12px; cursor: text; box-sizing: border-box;" />
                                </td>`;
                            }
                        }
                        
                        // Delete button
                        html += `<td style="padding: 0; text-align: center; background: #fffacd; border: 1px solid #ddd;"><button onclick="deleteNewRow(null, ${newRowIdx}, '${sheetName}')" style="background: none; border: none; cursor: pointer; color: #e74c3c; font-weight: bold; padding: 4px 2px;">✕</button></td>`;
                        html += '</tr>';
                    });
                    
                    html += '</tbody>';
                    table.innerHTML = html;
                }
                
                function saveCellData(input, sheetName, row, col) {
                    const cellKey = `${sheetName}_${row}_${col}`;
                    currentEditedData[cellKey] = input.value;
                    
                    // Visual feedback
                    input.style.background = '#fff8e7';
                    setTimeout(() => {
                        input.style.background = 'white';
                    }, 150);
                }
                
                function saveNewRowData(input, sheetName, newRowIdx, col) {
                    if (!newRowsData[sheetName]) {
                        newRowsData[sheetName] = [];
                    }
                    if (!newRowsData[sheetName][newRowIdx]) {
                        newRowsData[sheetName][newRowIdx] = [];
                    }
                    newRowsData[sheetName][newRowIdx][col] = input.value;
                    
                    // Visual feedback
                    input.style.background = '#fff4d6';
                    setTimeout(() => {
                        input.style.background = '#fffef0';
                    }, 150);
                }
                
                function addNewRow() {
                    if (!currentSheetName) return;
                    
                    if (!newRowsData[currentSheetName]) {
                        newRowsData[currentSheetName] = [];
                    }
                    
                    const headerRow = excelSheetsData[currentSheetName][0] || [];
                    const colCount = headerRow.length + (newColumnsData[currentSheetName] ? newColumnsData[currentSheetName].length : 0);
                    const newRow = new Array(colCount).fill('');
                    newRowsData[currentSheetName].push(newRow);
                    
                    // Reload sheet to show new row
                    loadExcelSheet(currentSheetName);
                    
                    // Scroll to bottom
                    const container = document.getElementById('excel-container');
                    setTimeout(() => {
                        container.scrollTop = container.scrollHeight;
                    }, 100);
                }
                
                function addNewColumn(sheetName) {
                    if (!newColumnsData[sheetName]) {
                        newColumnsData[sheetName] = [];
                    }
                    newColumnsData[sheetName].push(`Col${newColumnsData[sheetName].length + 1}`);
                    loadExcelSheet(sheetName);
                }
                
                function deleteRow(event, rowIdx, sheetName) {
                    if (event) event.preventDefault();
                    if (!deletedRows[sheetName]) deletedRows[sheetName] = [];
                    if (!deletedRows[sheetName].includes(rowIdx)) {
                        deletedRows[sheetName].push(rowIdx);
                        loadExcelSheet(sheetName);
                    }
                }
                
                function deleteNewRow(event, newRowIdx, sheetName) {
                    if (event) event.preventDefault();
                    if (newRowsData[sheetName]) {
                        newRowsData[sheetName].splice(newRowIdx, 1);
                        loadExcelSheet(sheetName);
                    }
                }
                
                function showColumnMenu(event, colIdx, sheetName) {
                    event.preventDefault();
                    if (confirm(`Hapus kolom ini?`)) {
                        if (!deletedColumns[sheetName]) deletedColumns[sheetName] = [];
                        if (!deletedColumns[sheetName].includes(colIdx)) {
                            deletedColumns[sheetName].push(colIdx);
                            loadExcelSheet(sheetName);
                        }
                    }
                }
                
                function downloadEditedExcel() {
                    // Build modified sheets data
                    const modifiedSheets = {};
                    
                    Object.keys(excelSheetsData).forEach(sheetName => {
                        const originalData = excelSheetsData[sheetName];
                        const modifiedData = [];
                        
                        // Add header row (skip baris pertama di file original, mulai dari row 1 di output)
                        const headerRow = originalData[0] || [];
                        const filteredHeader = headerRow.filter((_, idx) => !deletedColumns[sheetName] || !deletedColumns[sheetName].includes(idx));
                        
                        // Add new columns to header
                        if (newColumnsData[sheetName]) {
                            filteredHeader.push(...newColumnsData[sheetName]);
                        }
                        modifiedData.push(filteredHeader);
                        
                        // Add data rows with edited values
                        const dataRows = originalData.slice(1);
                        dataRows.forEach((row, rowIdx) => {
                            // Skip deleted rows
                            if (deletedRows[sheetName] && deletedRows[sheetName].includes(rowIdx)) {
                                return;
                            }
                            
                            const modifiedRow = [];
                            if (Array.isArray(row)) {
                                row.forEach((cell, colIdx) => {
                                    // Skip deleted columns
                                    if (deletedColumns[sheetName] && deletedColumns[sheetName].includes(colIdx)) {
                                        return;
                                    }
                                    const cellKey = `${sheetName}_${rowIdx + 1}_${colIdx}`;
                                    modifiedRow.push(currentEditedData[cellKey] !== undefined ? currentEditedData[cellKey] : (cell || ''));
                                });
                            }
                            // Add empty cells for new columns
                            if (newColumnsData[sheetName]) {
                                modifiedRow.push(...new Array(newColumnsData[sheetName].length).fill(''));
                            }
                            modifiedData.push(modifiedRow);
                        });
                        
                        // Add new rows if any
                        const newRows = newRowsData[sheetName] || [];
                        newRows.forEach(newRow => {
                            const filteredRow = newRow.filter((_, idx) => !deletedColumns[sheetName] || !deletedColumns[sheetName].includes(idx));
                            modifiedData.push(filteredRow);
                        });
                        
                        modifiedSheets[sheetName] = modifiedData;
                    });
                    
                    // Send to server for Excel generation
                    const filename = '{{ basename($document->file_name, '.xlsx') }}_edited.xlsx';
                    
                    fetch('/pemeliharaan/export-excel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            sheets: modifiedSheets,
                            filename: filename
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        
                        // Cleanup
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                        
                        alert('✅ File berhasil didownload!');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('❌ Gagal download file. Error: ' + error.message);
                    });
                }
                
    
            </script>
        @elseif(in_array($fileType, ['xls', 'xlsx', 'csv']))
            <div class="excel-viewer">
                <div id="spreadsheet"></div>
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: #999;">
                <p style="font-size: 16px;">File type .{{ $fileType }} tidak bisa ditampilkan di viewer</p>
                <a href="{{ route('pemeliharaan.download', $document->id) }}" class="btn-download" style="display: inline-block; margin-top: 20px;">📥 Download File</a>
            </div>
        @endif
    
        <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e0e0e0; color: #666; font-size: 12px;">
            <p><strong>Informasi File:</strong></p>
            <table style="width: 100%; text-align: left;">
                <tr>
                    <td style="padding: 5px 0;"><strong>Judul:</strong></td>
                    <td style="padding: 5px 0;">{{ $document->judul }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Folder:</strong></td>
                    <td style="padding: 5px 0;">{{ $document->folder }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Nama File:</strong></td>
                    <td style="padding: 5px 0;">{{ $document->file_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Ukuran:</strong></td>
                    <td style="padding: 5px 0;">{{ number_format($document->file_size / 1024, 2) }} KB</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Tanggal Upload:</strong></td>
                    <td style="padding: 5px 0;">{{ $document->tanggal_upload ? $document->tanggal_upload->format('d-m-Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // PDF Viewer
    let currentPdfDoc = null;
    let currentPdfPage = 1;
    let currentHotInstance = null;
    const fileType = '{{ $fileType }}';
    const documentId = '{{ $document->id }}';
    const getFileUrl = '/pemeliharaan/' + documentId + '/get-file';
    const isExcel = {{ $isExcel ? 'true' : 'false' }};
    // sheetsData constant is already defined in the Excel block logic if isExcel is true

    if (fileType === 'pdf') {
        loadPdf();
    } else if (['xls', 'xlsx', 'csv'].includes(fileType) && !isExcel) {
        // Hanya load dengan Handsontable jika bukan Excel yang sudah di-parse
        loadExcel();
    }

    function loadPdf() {
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        pdfjsLib.getDocument(getFileUrl).promise.then(pdf => {
            currentPdfDoc = pdf;
            currentPdfPage = 1;
            document.getElementById('pdfPageCount').textContent = pdf.numPages;
            renderPdfPage(1);
        }).catch(error => {
            console.error('Error loading PDF:', error);
            document.getElementById('pdfCanvas').parentElement.innerHTML = '<p style="color: red;">❌ Error loading PDF file. Please try again later.</p>';
        });
    }

    function renderPdfPage(pageNum) {
        if (!currentPdfDoc) return;
        
        currentPdfDoc.getPage(pageNum).then(page => {
            const canvas = document.getElementById('pdfCanvas');
            const context = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: 1.5 });
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            page.render({
                canvasContext: context,
                viewport: viewport
            });
            
            document.getElementById('pdfPageNum').textContent = pageNum;
        });
    }

    function nextPdfPage() {
        if (currentPdfDoc && currentPdfPage < currentPdfDoc.numPages) {
            currentPdfPage++;
            renderPdfPage(currentPdfPage);
        }
    }

    function prevPdfPage() {
        if (currentPdfPage > 1) {
            currentPdfPage--;
            renderPdfPage(currentPdfPage);
        }
    }

    function loadExcel() {
        fetch(getFileUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('File not found');
                }
                return response.arrayBuffer();
            })
            .then(data => {
                try {
                    const workbook = XLSX.read(new Uint8Array(data), { type: 'array' });
                    const sheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[sheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    
                    const container = document.getElementById('spreadsheet');
                    
                    if (currentHotInstance) {
                        currentHotInstance.destroy();
                    }
                    
                    currentHotInstance = new Handsontable(container, {
                        data: jsonData,
                        colHeaders: true,
                        rowHeaders: true,
                        height: 'auto',
                        licenseKey: 'non-commercial-and-evaluation',
                        contextMenu: true,
                        stretchH: 'all',
                        autoWrapRow: true,
                        autoWrapCol: true
                    });
                } catch (error) {
                    console.error('Error parsing Excel:', error);
                    document.getElementById('spreadsheet').innerHTML = '<p style="color: red; padding: 20px;">❌ Error loading Excel file</p>';
                }
            })
            .catch(error => {
                console.error('Error loading file:', error);
                document.getElementById('spreadsheet').innerHTML = '<p style="color: red; padding: 20px;">❌ Error loading file: ' + error.message + '</p>';
            });
    }

    function downloadFile() {
        if (typeof currentEditedData !== 'undefined') {
            const hasEditedCells = Object.keys(currentEditedData).length > 0;
            const hasNewRows = Object.keys(newRowsData).some(sheet => newRowsData[sheet] && newRowsData[sheet].length > 0);
            
            if (isExcel && (hasEditedCells || hasNewRows)) {
                // Download edited Excel
                downloadEditedExcel();
                return;
            }
        }
        window.location.href = '{{ route('pemeliharaan.download', $document->id) }}';
    }
</script>
@endpush
