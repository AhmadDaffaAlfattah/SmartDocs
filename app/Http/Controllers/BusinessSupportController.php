<?php

namespace App\Http\Controllers;

use App\Models\BusinessSupportDocument;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BusinessSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessSupportDocument::query();

        if ($request->has('folder') && $request->folder != '') {
            $query->where('folder', $request->folder);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $documents = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $folders = BusinessSupportDocument::getFolders();
        $folderTree = Folder::getRootFolders();

        return view('business-support.index', [
            'documents' => $documents,
            'folders' => $folders,
            'folderTree' => $folderTree,
            'selectedFolder' => $request->folder ?? '',
            'searchQuery' => $request->search ?? '',
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $folders = BusinessSupportDocument::getFolders();
        $folderTree = Folder::getRootFolders();
        return view('business-support.create', ['folders' => $folders, 'folderTree' => $folderTree]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'folder_id' => 'required|integer',
            'file' => 'nullable|file|max:10240',
            'link' => 'nullable|url',
        ]);

        if (!$request->hasFile('file') && empty($validated['link'])) {
            return back()->withErrors(['file' => 'Silakan upload file atau masukkan link!']);
        }

        $document = new BusinessSupportDocument();
        $document->judul = $validated['judul'];
        $document->folder_id = $validated['folder_id'];
        $document->tanggal_upload = now();

        $folderRecord = Folder::find($validated['folder_id']);
        if ($folderRecord) {
            $document->folder = $folderRecord->nama_folder;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('business-support-documents', $filename, 'public');

            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientOriginalExtension();
            $document->file_size = $file->getSize();
        }

        if (!empty($validated['link'])) {
            $document->link = $validated['link'];
        }

        $document->save();

        return redirect()->route('business-support.index')
            ->with('success', 'Dokumen berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);
        $folders = BusinessSupportDocument::getFolders();
        $folderTree = Folder::getRootFolders();

        return view('business-support.edit', [
            'document' => $document,
            'folders' => $folders,
            'folderTree' => $folderTree,
        ]);
    }

    public function getEditData($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);
        $folderTree = Folder::getRootFolders();

        return response()->json([
            'document' => $document,
            'folders' => $folderTree,
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = BusinessSupportDocument::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'folder_id' => 'required|integer',
            'file' => 'nullable|file|max:10240',
            'link' => 'nullable|url',
        ]);

        $document->judul = $validated['judul'];
        $document->folder_id = $validated['folder_id'];

        $folderRecord = Folder::find($validated['folder_id']);
        if ($folderRecord) {
            $document->folder = $folderRecord->nama_folder;
        }

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('business-support-documents', $filename, 'public');

            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientOriginalExtension();
            $document->file_size = $file->getSize();
            $document->tanggal_upload = now();
        }

        if (!empty($validated['link'])) {
            $document->link = $validated['link'];
        } else {
            $document->link = null;
        }

        $document->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil diperbarui!']);
        }

        return redirect()->route('business-support.index')
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('business-support.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }

    public function download($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function view($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return view('business-support.view', ['document' => $document]);
    }

    public function viewer($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->route('business-support.index')->with('error', 'File tidak ditemukan!');
        }

        $fileType = $document->file_type ? strtolower($document->file_type) : '';

        if (empty($fileType)) {
            $pathInfo = pathinfo($document->file_path);
            $fileType = strtolower($pathInfo['extension'] ?? '');
        }

        $excelExtensions = ['xlsx', 'xls', 'csv', 'ods'];
        $isExcel = in_array($fileType, $excelExtensions);

        $sheetsData = null;
        $sheetNames = null;

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
                $sheetsData = null;
            }
        }

        return view('business-support.viewer', [
            'document' => $document,
            'fileType' => $fileType,
            'sheetsData' => $sheetsData,
            'sheetNames' => $sheetNames,
            'isExcel' => $isExcel,
        ]);
    }

    public function getFile($id)
    {
        $document = BusinessSupportDocument::findOrFail($id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return response()->file(Storage::disk('public')->path($document->file_path));
    }
}
