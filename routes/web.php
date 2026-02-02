<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EngineeringController;
use App\Http\Controllers\OperasiController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\BusinessSupportController;
use App\Http\Controllers\KeamananController;
use App\Http\Controllers\LingkunganController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\AssetWellnessController;
use App\Http\Controllers\DetailWarningController;
use App\Http\Controllers\DetailFaultController;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dori', function () {
    return view('dori');
});

// Auth Routes
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'Logout berhasil');
})->name('logout');

Route::get('/change-password', function () {
    return view('auth.change-password');
})->name('change-password');

Route::post('/change-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = Auth::user();
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
    }

    $user->update(['password' => $request->new_password]);
    return redirect('/')->with('success', 'Password berhasil diubah');
})->middleware('auth');

// Account Routes
Route::prefix('account')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('account.index');
    Route::get('/create', [AccountController::class, 'create'])->name('account.create');
    Route::post('/', [AccountController::class, 'store'])->name('account.store');
    Route::get('/{account}/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/{account}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/{account}', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/{account}/get', [AccountController::class, 'getAccount'])->name('account.get');
    Route::post('/{account}/update-inline', [AccountController::class, 'updateInline'])->name('account.updateInline');
});

// Engineering Routes
Route::prefix('engineering')->group(function () {
    // Export route (harus di awal untuk avoid override)
    Route::post('/export-excel', [EngineeringController::class, 'exportExcel'])->name('engineering.exportExcel');

    Route::get('/', [EngineeringController::class, 'index'])->name('engineering.index');
    Route::get('/create', [EngineeringController::class, 'create'])->name('engineering.create');
    Route::post('/', [EngineeringController::class, 'store'])->name('engineering.store');
    Route::get('/{id}/edit', [EngineeringController::class, 'edit'])->name('engineering.edit');
    Route::get('/{id}/get-edit-data', [EngineeringController::class, 'getEditData'])->name('engineering.getEditData');
    Route::get('/{id}/get-file', [EngineeringController::class, 'getFile'])->name('engineering.getFile');
    Route::get('/{id}/viewer', [EngineeringController::class, 'viewer'])->name('engineering.viewer');
    Route::put('/{id}', [EngineeringController::class, 'update'])->name('engineering.update');
    Route::post('/{id}', [EngineeringController::class, 'update'])->name('engineering.update.post');
    Route::delete('/{id}', [EngineeringController::class, 'destroy'])->name('engineering.destroy');
    Route::get('/{id}/download', [EngineeringController::class, 'download'])->name('engineering.download');
    Route::get('/{id}/view', [EngineeringController::class, 'view'])->name('engineering.view');
});

// Operasi Routes
Route::prefix('operasi')->group(function () {
    Route::get('/', [OperasiController::class, 'index'])->name('operasi.index');
    Route::get('/create', [OperasiController::class, 'create'])->name('operasi.create');
    Route::post('/', [OperasiController::class, 'store'])->name('operasi.store');
    Route::get('/{id}/edit', [OperasiController::class, 'edit'])->name('operasi.edit');
    Route::get('/{id}/get-edit-data', [OperasiController::class, 'getEditData'])->name('operasi.getEditData');
    Route::get('/{id}/get-file', [OperasiController::class, 'getFile'])->name('operasi.getFile');
    Route::get('/{id}/viewer', [OperasiController::class, 'viewer'])->name('operasi.viewer');
    Route::put('/{id}', [OperasiController::class, 'update'])->name('operasi.update');
    Route::post('/{id}', [OperasiController::class, 'update'])->name('operasi.update.post');
    Route::delete('/{id}', [OperasiController::class, 'destroy'])->name('operasi.destroy');
    Route::get('/{id}/download', [OperasiController::class, 'download'])->name('operasi.download');
    Route::get('/{id}/view', [OperasiController::class, 'view'])->name('operasi.view');
});

// Pemeliharaan Routes
Route::prefix('pemeliharaan')->group(function () {
    Route::get('/', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
    Route::get('/create', [PemeliharaanController::class, 'create'])->name('pemeliharaan.create');
    Route::post('/', [PemeliharaanController::class, 'store'])->name('pemeliharaan.store');
    Route::get('/{id}/edit', [PemeliharaanController::class, 'edit'])->name('pemeliharaan.edit');
    Route::get('/{id}/get-edit-data', [PemeliharaanController::class, 'getEditData'])->name('pemeliharaan.getEditData');
    Route::get('/{id}/get-file', [PemeliharaanController::class, 'getFile'])->name('pemeliharaan.getFile');
    Route::get('/{id}/viewer', [PemeliharaanController::class, 'viewer'])->name('pemeliharaan.viewer');
    Route::put('/{id}', [PemeliharaanController::class, 'update'])->name('pemeliharaan.update');
    Route::post('/{id}', [PemeliharaanController::class, 'update'])->name('pemeliharaan.update.post');
    Route::delete('/{id}', [PemeliharaanController::class, 'destroy'])->name('pemeliharaan.destroy');
    Route::get('/{id}/download', [PemeliharaanController::class, 'download'])->name('pemeliharaan.download');
    Route::get('/{id}/view', [PemeliharaanController::class, 'view'])->name('pemeliharaan.view');
});

// Business Support Routes
Route::prefix('business-support')->group(function () {
    Route::get('/', [BusinessSupportController::class, 'index'])->name('business-support.index');
    Route::get('/create', [BusinessSupportController::class, 'create'])->name('business-support.create');
    Route::post('/', [BusinessSupportController::class, 'store'])->name('business-support.store');
    Route::get('/{id}/edit', [BusinessSupportController::class, 'edit'])->name('business-support.edit');
    Route::get('/{id}/get-edit-data', [BusinessSupportController::class, 'getEditData'])->name('business-support.getEditData');
    Route::get('/{id}/get-file', [BusinessSupportController::class, 'getFile'])->name('business-support.getFile');
    Route::get('/{id}/viewer', [BusinessSupportController::class, 'viewer'])->name('business-support.viewer');
    Route::put('/{id}', [BusinessSupportController::class, 'update'])->name('business-support.update');
    Route::post('/{id}', [BusinessSupportController::class, 'update'])->name('business-support.update.post');
    Route::delete('/{id}', [BusinessSupportController::class, 'destroy'])->name('business-support.destroy');
    Route::get('/{id}/download', [BusinessSupportController::class, 'download'])->name('business-support.download');
    Route::get('/{id}/view', [BusinessSupportController::class, 'view'])->name('business-support.view');
});

// Keamanan Routes
Route::prefix('keamanan')->group(function () {
    Route::get('/', [KeamananController::class, 'index'])->name('keamanan.index');
    Route::get('/create', [KeamananController::class, 'create'])->name('keamanan.create');
    Route::post('/', [KeamananController::class, 'store'])->name('keamanan.store');
    Route::get('/{id}/edit', [KeamananController::class, 'edit'])->name('keamanan.edit');
    Route::get('/{id}/get-edit-data', [KeamananController::class, 'getEditData'])->name('keamanan.getEditData');
    Route::get('/{id}/get-file', [KeamananController::class, 'getFile'])->name('keamanan.getFile');
    Route::get('/{id}/viewer', [KeamananController::class, 'viewer'])->name('keamanan.viewer');
    Route::put('/{id}', [KeamananController::class, 'update'])->name('keamanan.update');
    Route::post('/{id}', [KeamananController::class, 'update'])->name('keamanan.update.post');
    Route::delete('/{id}', [KeamananController::class, 'destroy'])->name('keamanan.destroy');
    Route::get('/{id}/download', [KeamananController::class, 'download'])->name('keamanan.download');
    Route::get('/{id}/view', [KeamananController::class, 'view'])->name('keamanan.view');
});

// Lingkungan Routes
Route::prefix('lingkungan')->group(function () {
    Route::get('/', [LingkunganController::class, 'index'])->name('lingkungan.index');
    Route::get('/create', [LingkunganController::class, 'create'])->name('lingkungan.create');
    Route::post('/', [LingkunganController::class, 'store'])->name('lingkungan.store');
    Route::get('/{id}/edit', [LingkunganController::class, 'edit'])->name('lingkungan.edit');
    Route::get('/{id}/get-edit-data', [LingkunganController::class, 'getEditData'])->name('lingkungan.getEditData');
    Route::get('/{id}/get-file', [LingkunganController::class, 'getFile'])->name('lingkungan.getFile');
    Route::get('/{id}/viewer', [LingkunganController::class, 'viewer'])->name('lingkungan.viewer');
    Route::put('/{id}', [LingkunganController::class, 'update'])->name('lingkungan.update');
    Route::post('/{id}', [LingkunganController::class, 'update'])->name('lingkungan.update.post');
    Route::delete('/{id}', [LingkunganController::class, 'destroy'])->name('lingkungan.destroy');
    Route::get('/{id}/download', [LingkunganController::class, 'download'])->name('lingkungan.download');
    Route::get('/{id}/view', [LingkunganController::class, 'view'])->name('lingkungan.view');
});

// Folder Routes
Route::prefix('folder')->group(function () {
    Route::get('/', [FolderController::class, 'index'])->name('folder.index');
    Route::get('/tree', [FolderController::class, 'tree'])->name('folder.tree');
    Route::get('/create', [FolderController::class, 'create'])->name('folder.create');
    Route::post('/', [FolderController::class, 'store'])->name('folder.store');
    Route::get('/{id}/edit', [FolderController::class, 'edit'])->name('folder.edit');
    Route::put('/{id}', [FolderController::class, 'update'])->name('folder.update');
    Route::delete('/{id}', [FolderController::class, 'destroy'])->name('folder.destroy');
});

// Excel Routes
Route::prefix('excel')->group(function () {
    Route::get('/', [ExcelController::class, 'index'])->name('excel.index');
    Route::post('/upload', [ExcelController::class, 'upload'])->name('excel.upload');
    Route::get('/{excelUpload}/view', [ExcelController::class, 'view'])->name('excel.view');
    Route::delete('/{excelUpload}', [ExcelController::class, 'destroy'])->name('excel.destroy');
    Route::get('/{excelUpload}/download', [ExcelController::class, 'download'])->name('excel.download');
});

// API Routes for Calendar (works on landing page)
Route::prefix('api')->group(function () {
    Route::post('/reminder', [CalendarController::class, 'storeReminder']);
    Route::delete('/reminder/{id}', [CalendarController::class, 'deleteReminder']);
    Route::get('/reminders', [CalendarController::class, 'getReminders']);
    Route::get('/calendar', [CalendarController::class, 'getCalendar']);
});

// Asset Wellness Routes
Route::resource('asset-wellness', AssetWellnessController::class);
Route::get('asset-wellness-download', [AssetWellnessController::class, 'download'])->name('asset-wellness.download');
Route::get('asset-wellness-export', [AssetWellnessController::class, 'exportExcel'])->name('asset-wellness.export');
Route::get('asset-wellness-pdf-report', [AssetWellnessController::class, 'exportPdfReport'])->name('asset-wellness.pdf-report');
Route::get('asset-wellness-pdf-screenshots', [AssetWellnessController::class, 'exportPdfScreenshots'])->name('asset-wellness.pdf-screenshots');

// Detail Warning Routes
Route::get('detail-warning', [DetailWarningController::class, 'index'])->name('detail-warning.index');
Route::get('detail-warning/create', [DetailWarningController::class, 'create'])->name('detail-warning.create');
Route::post('detail-warning', [DetailWarningController::class, 'store'])->name('detail-warning.store');
Route::get('detail-warning/{id}/edit', [DetailWarningController::class, 'edit'])->name('detail-warning.show');
Route::put('detail-warning/{id}', [DetailWarningController::class, 'update'])->name('detail-warning.update');
Route::delete('detail-warning/{id}', [DetailWarningController::class, 'destroy'])->name('detail-warning.destroy');

// Detail Fault Routes
Route::get('detail-fault', [DetailFaultController::class, 'index'])->name('detail-fault.index');
Route::get('detail-fault/create', [DetailFaultController::class, 'create'])->name('detail-fault.create');
Route::post('detail-fault', [DetailFaultController::class, 'store'])->name('detail-fault.store');
Route::get('detail-fault/{id}/edit', [DetailFaultController::class, 'edit'])->name('detail-fault.show');
Route::put('detail-fault/{id}', [DetailFaultController::class, 'update'])->name('detail-fault.update');
Route::delete('detail-fault/{id}', [DetailFaultController::class, 'destroy'])->name('detail-fault.destroy');
