@extends('layouts.master')

@section('title', 'SmartDocs - Viewer ' . $document->judul)

@push('styles')
    <!-- PDF.js untuk viewer PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
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
    </style>
@endpush

@section('content')
    <div class="viewer-container">
        <div class="viewer-header">
            <h1>{{ $document->judul }}</h1>
            <div class="viewer-controls">
                <a href="{{ route('business-support.index') }}" class="btn-back">← Kembali</a>
                <button type="button" onclick="downloadFile()" class="btn-download">📥 Download</button>
            </div>
        </div>

        @if($fileType === 'pdf')
            <div class="pdf-viewer">
                <canvas id="pdfCanvas"></canvas>
                <div style="display: flex; gap: 15px; align-items: center; margin-top: 15px;">
                    <button type="button" onclick="prevPdfPage()" style="background-color: #666; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">← Previous</button>
                    <p style="margin: 0; font-weight: 600;">Page <span id="pdfPageNum">1</span> of <span id="pdfPageCount">1</span></p>
                    <button type="button" onclick="nextPdfPage()" style="background-color: #666; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Next →</button>
                </div>
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: #999;">
                <p style="font-size: 16px;">File type .{{ $fileType }} tidak bisa ditampilkan di viewer</p>
                <a href="{{ route('business-support.download', $document->id) }}" class="btn-download" style="display: inline-block; margin-top: 20px;">📥 Download File</a>
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
    let currentPdfDoc = null;
    let currentPdfPage = 1;
    const fileType = '{{ $fileType }}';
    const documentId = '{{ $document->id }}';
    const getFileUrl = '/business-support/' + documentId + '/get-file';

    if (fileType === 'pdf') {
        loadPdf();
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
            document.getElementById('pdfCanvas').parentElement.innerHTML = '<p style="color: red;">❌ Error loading PDF file.</p>';
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
            page.render({ canvasContext: context, viewport: viewport });
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

    function downloadFile() {
        window.location.href = '{{ route('business-support.download', $document->id) }}';
    }
</script>
@endpush
