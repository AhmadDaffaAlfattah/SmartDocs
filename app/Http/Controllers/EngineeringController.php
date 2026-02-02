<?php

namespace App\Http\Controllers;

use App\Models\EngineeringDocument;
use App\Models\ExcelUpload;
use App\Models\ExcelSheet;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EngineeringController extends Controller
{
    /**
     * Display a listing of engineering documents
     */
    public function index(Request $request)
    {
        $query = EngineeringDocument::query();

        // Filter by folder
        if ($request->has('folder') && $request->folder != '') {
            $query->where('folder', $request->folder);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $documents = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $folders = EngineeringDocument::getFolders();
        $folderTree = Folder::getRootFolders();

        return view('engineering.index', [
            'documents' => $documents,
            'folders' => $folders,
            'folderTree' => $folderTree,
            'selectedFolder' => $request->folder ?? '',
            'searchQuery' => $request->search ?? '',
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new document
     */
    public function create()
    {
        $folders = EngineeringDocument::getFolders();
        $folderTree = Folder::getRootFolders();
        return view('engineering.create', ['folders' => $folders, 'folderTree' => $folderTree]);
    }

    /**
     * Store a newly created document in storage
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'folder_id' => 'required|integer',
            'file' => 'nullable|file|max:10240', // Max 10MB
            'link' => 'nullable|url',
        ]);

        // Ensure either file or link is provided
        if (!$request->hasFile('file') && empty($validated['link'])) {
            return back()->withErrors(['file' => 'Silakan upload file atau masukkan link!']);
        }

        $document = new EngineeringDocument();
        $document->judul = $validated['judul'];
        $document->folder_id = $validated['folder_id'];
        $document->tanggal_upload = now();

        // Get folder name from folder_id
        $folderRecord = Folder::find($validated['folder_id']);
        if ($folderRecord) {
            $document->folder = $folderRecord->nama_folder;
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('engineering-documents', $filename, 'public');

            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientOriginalExtension();
            $document->file_size = $file->getSize();
        }

        // Handle link
        if (!empty($validated['link'])) {
            $document->link = $validated['link'];
        }

        $document->save();

        return redirect()->route('engineering.index')
            ->with('success', 'Dokumen berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified document
     */
    public function edit($id)
    {
        $document = EngineeringDocument::findOrFail($id);
        $folders = EngineeringDocument::getFolders();
        $folderTree = Folder::getRootFolders();

        return view('engineering.edit', [
            'document' => $document,
            'folders' => $folders,
            'folderTree' => $folderTree,
        ]);
    }

    /**
     * Get edit data for modal (JSON response)
     */
    public function getEditData($id)
    {
        $document = EngineeringDocument::findOrFail($id);
        $folderTree = Folder::getRootFolders();

        return response()->json([
            'document' => $document,
            'folders' => $folderTree,
        ]);
    }

    /**
     * Update the specified document in storage
     */
    public function update(Request $request, $id)
    {
        $document = EngineeringDocument::findOrFail($id);

        // Validation
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'folder_id' => 'required|integer',
            'file' => 'nullable|file|max:10240',
            'link' => 'nullable|url',
        ]);

        $document->judul = $validated['judul'];
        $document->folder_id = $validated['folder_id'];

        // Get folder name from folder_id
        $folderRecord = Folder::find($validated['folder_id']);
        if ($folderRecord) {
            $document->folder = $folderRecord->nama_folder;
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('engineering-documents', $filename, 'public');

            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientOriginalExtension();
            $document->file_size = $file->getSize();
            $document->tanggal_upload = now();
        }

        // Handle link
        if (!empty($validated['link'])) {
            $document->link = $validated['link'];
        } else {
            $document->link = null;
        }

        $document->save();

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil diperbarui!']);
        }

        return redirect()->route('engineering.index')
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Delete the specified document
     */
    public function destroy($id)
    {
        $document = EngineeringDocument::findOrFail($id);

        // Delete file if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('engineering.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }

    /**
     * Download the file
     */
    public function download($id)
    {
        $document = EngineeringDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * View the document
     */
    public function view($id)
    {
        $document = EngineeringDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return view('engineering.view', ['document' => $document]);
    }

    /**
     * Viewer page for PDF/Excel files
     */
    public function viewer($id)
    {
        $document = EngineeringDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->route('engineering.index')->with('error', 'File tidak ditemukan!');
        }

        $fileType = $document->file_type ? strtolower($document->file_type) : '';

        // Fallback: detect dari file extension di path jika file_type kosong
        if (empty($fileType)) {
            $pathInfo = pathinfo($document->file_path);
            $fileType = strtolower($pathInfo['extension'] ?? '');
        }

        // Check apakah file Excel - lebih flexible
        $excelExtensions = ['xlsx', 'xls', 'csv', 'ods'];
        $isExcel = in_array($fileType, $excelExtensions);

        $sheetsData = null;
        $sheetNames = null;

        // Jika Excel, parse sheets
        if ($isExcel) {
            try {
                $filePath = storage_path('app/public/' . $document->file_path);

                if (file_exists($filePath)) {
                    $spreadsheet = IOFactory::load($filePath);
                    $sheetsData = [];

                    foreach ($spreadsheet->getSheetNames() as $index => $sheetName) {
                        $worksheet = $spreadsheet->getSheetByName($sheetName);
                        $allData = $worksheet->toArray(null, true, true, false);

                        $cleanData = [];
                        foreach ($allData as $row) {
                            $hasContent = false;
                            foreach ($row as $cell) {
                                if (!empty($cell) && $cell !== null) {
                                    $hasContent = true;
                                    break;
                                }
                            }
                            if ($hasContent) {
                                $cleanRow = array_map(function ($cell) {
                                    return is_string($cell) ? trim($cell) : $cell;
                                }, $row);
                                $cleanData[] = $cleanRow;
                            }
                        }

                        $sheetsData[$sheetName] = $cleanData;
                    }

                    $sheetNames = array_keys($sheetsData);
                }
            } catch (\Exception $e) {
                // Jika parse gagal, tetap return viewer biasa
                $sheetsData = null;
            }
        }

        return view('engineering.viewer', [
            'document' => $document,
            'fileType' => $fileType,
            'sheetsData' => $sheetsData,
            'sheetNames' => $sheetNames,
            'isExcel' => $isExcel,
        ]);
    }

    /**
     * View Excel file dari engineering document
     */
    private function viewExcelFile(EngineeringDocument $document)
    {
        try {
            $filePath = storage_path('app/public/' . $document->file_path);

            if (!file_exists($filePath)) {
                return redirect()->route('engineering.index')
                    ->with('error', 'File tidak ditemukan di storage: ' . $filePath);
            }

            // Parse Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheetsData = [];

            // Parse setiap sheet
            foreach ($spreadsheet->getSheetNames() as $index => $sheetName) {
                $worksheet = $spreadsheet->getSheetByName($sheetName);

                // Get data simple
                $allData = $worksheet->toArray(null, true, true, false);

                // Filter: ambil hanya row yang punya konten
                $cleanData = [];
                foreach ($allData as $row) {
                    // Check apakah row ini punya nilai yang meaningful
                    $hasContent = false;
                    foreach ($row as $cell) {
                        if (!empty($cell) && $cell !== null) {
                            $hasContent = true;
                            break;
                        }
                    }
                    if ($hasContent) {
                        // Trim dan bersihkan setiap cell
                        $cleanRow = array_map(function ($cell) {
                            return is_string($cell) ? trim($cell) : $cell;
                        }, $row);
                        $cleanData[] = $cleanRow;
                    }
                }

                $sheetsData[$sheetName] = $cleanData;
            }

            // Ambil sheet pertama sebagai default
            $sheetNames = array_keys($sheetsData);
            $selectedSheetName = $sheetNames[0] ?? null;
            $sheetData = $sheetsData[$selectedSheetName] ?? [];

            return view('engineering.excel-viewer', [
                'document' => $document,
                'sheetsData' => $sheetsData,
                'sheetNames' => $sheetNames,
                'selectedSheetName' => $selectedSheetName,
                'sheetData' => $sheetData,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('engineering.index')
                ->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Get file content for viewer (serve file)
     */
    public function getFile($id)
    {
        $document = EngineeringDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return response()->file(Storage::disk('public')->path($document->file_path));
    }

    /**
     * Export edited Excel file
     */
    public function exportExcel(Request $request)
    {
        try {
            $sheetsData = $request->input('sheets', []);
            $filename = $request->input('filename', 'export.xlsx');

            // Create new spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->removeSheetByIndex(0); // Remove default sheet

            // Add sheets with proper data handling
            $sheetIndex = 0;
            foreach ($sheetsData as $sheetName => $data) {
                $worksheet = $spreadsheet->createSheet($sheetIndex);
                $worksheet->setTitle($sheetName);

                // Write data with proper type casting
                foreach ($data as $rowIndex => $rowData) {
                    if (is_array($rowData)) {
                        foreach ($rowData as $colIndex => $cellValue) {
                            // Column letter (A, B, C, etc)
                            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                            $cellAddress = $colLetter . ($rowIndex + 1);

                            // Set cell value
                            $worksheet->setCellValue($cellAddress, (string)$cellValue);

                            // Style header row
                            if ($rowIndex === 0) {
                                $worksheet->getStyle($cellAddress)->getFont()->setBold(true);
                                $worksheet->getStyle($cellAddress)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $worksheet->getStyle($cellAddress)->getFill()->getStartColor()->setARGB('FF999999');
                                $worksheet->getStyle($cellAddress)->getFont()->getColor()->setARGB('FFFFFFFF');
                            }
                        }
                    }
                }

                // Auto-size columns
                foreach ($worksheet->getColumnIterator() as $column) {
                    $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }

                $sheetIndex++;
            }

            // Create writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            ob_clean();

            // Output file with proper headers
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            header('Expires: 0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            \Log::error('Export Excel Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
