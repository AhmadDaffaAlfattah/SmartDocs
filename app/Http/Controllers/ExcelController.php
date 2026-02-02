<?php

namespace App\Http\Controllers;

use App\Models\ExcelUpload;
use App\Models\ExcelSheet;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends Controller
{
    /**
     * Show Excel upload form
     */
    public function index()
    {
        $excelUploads = ExcelUpload::with('sheets')->latest()->paginate(10);
        return view('excel.index', compact('excelUploads'));
    }

    /**
     * Handle Excel file upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('excel_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('excel-uploads', $fileName, 'public');

            // Parse Excel file
            $spreadsheet = IOFactory::load(storage_path('app/public/' . $filePath));
            $sheetsData = [];
            $totalSheets = 0;

            // Buat ExcelUpload record
            $excelUpload = ExcelUpload::create([
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'sheets_data' => [],
                'total_sheets' => count($spreadsheet->getSheetNames()),
                'user_id' => auth()->id(),
            ]);

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

                // Simpan ke database
                ExcelSheet::create([
                    'excel_upload_id' => $excelUpload->id,
                    'sheet_name' => $sheetName,
                    'sheet_index' => $index,
                    'sheet_data' => $cleanData,
                ]);

                $sheetsData[$sheetName] = $data;
                $totalSheets++;
            }

            // Update sheets_data
            $excelUpload->update([
                'sheets_data' => $sheetsData,
                'total_sheets' => $totalSheets,
            ]);

            return redirect()->route('excel.view', $excelUpload->id)
                ->with('success', 'Excel file uploaded successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload Excel file: ' . $e->getMessage());
        }
    }

    /**
     * View Excel data dengan navbar dan tabel
     */
    public function view(ExcelUpload $excelUpload)
    {
        $sheets = $excelUpload->sheets()->orderBy('sheet_index')->get();
        $selectedSheet = request('sheet') ? $sheets->where('sheet_name', request('sheet'))->first() : $sheets->first();

        if (!$selectedSheet) {
            return redirect()->route('excel.index')->with('error', 'Sheet not found');
        }

        // sheet_data sudah array karena JSON casting di model
        $sheetData = is_array($selectedSheet->sheet_data) ? $selectedSheet->sheet_data : json_decode($selectedSheet->sheet_data, true);

        return view('excel.viewer', [
            'excelUpload' => $excelUpload,
            'sheets' => $sheets,
            'selectedSheet' => $selectedSheet,
            'sheetData' => $sheetData,
        ]);
    }

    /**
     * Delete Excel upload
     */
    public function destroy(ExcelUpload $excelUpload)
    {
        // Delete file from storage
        if ($excelUpload->file_path) {
            \Storage::disk('public')->delete($excelUpload->file_path);
        }

        // Delete from database
        $excelUpload->delete();

        return redirect()->route('excel.index')
            ->with('success', 'Excel file deleted successfully!');
    }

    /**
     * Download Excel file
     */
    public function download(ExcelUpload $excelUpload)
    {
        $filePath = storage_path('app/public/' . $excelUpload->file_path);

        if (!file_exists($filePath)) {
            return redirect()->route('excel.index')->with('error', 'File not found');
        }

        return response()->download($filePath, $excelUpload->original_name);
    }
}
