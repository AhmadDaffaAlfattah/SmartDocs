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

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('landing');
});

// Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {
        $totalUsers = \App\Models\User::count();
        $totalBidang = 6;
        $totalDokumen = 
            \App\Models\EngineeringDocument::count() +
            \App\Models\OperasiDocument::count() +
            \App\Models\PemeliharaanDocument::count() +
            \App\Models\BusinessSupportDocument::count() +
            \App\Models\KeamananDocument::count() +
            \App\Models\LingkunganDocument::count();

        return view('landing', compact('totalUsers', 'totalDokumen', 'totalBidang'));
    })->name('landing');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/change-password', function () {
        return view('auth.change-password');
    })->name('change-password');

    Route::post('/change-password', function (Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        
        // Fix: Application uses plain-text passwords (based on AuthController logic)
        // So we compare directly instead of using Hash::check
        if ($request->current_password !== $user->password) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        $user->update(['password' => $request->new_password]);
        return redirect()->route('landing')->with('success', 'Password berhasil diubah');
    });

    // Account Routes (Super Admin Only)
    Route::prefix('account')->middleware(\App\Http\Middleware\CheckRole::class . ':super_admin')->group(function () {
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
    Route::prefix('engineering')->middleware(\App\Http\Middleware\CheckBidang::class . ':Engineering')->group(function () {
        Route::get('/', [EngineeringController::class, 'index'])->name('engineering.index'); 
        Route::get('/{id}/get-file', [EngineeringController::class, 'getFile'])->name('engineering.getFile');
        Route::get('/{id}/viewer', [EngineeringController::class, 'viewer'])->name('engineering.viewer');
        Route::get('/{id}/download', [EngineeringController::class, 'download'])->name('engineering.download');
        Route::get('/{id}/view', [EngineeringController::class, 'view'])->name('engineering.view');
        Route::get('/{id}/get-edit-data', [EngineeringController::class, 'getEditData'])->name('engineering.getEditData');

        // CRUD (Admin & Super Admin only)
        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
             Route::post('/export-excel', [EngineeringController::class, 'exportExcel'])->name('engineering.exportExcel');
             Route::get('/create', [EngineeringController::class, 'create'])->name('engineering.create');
             Route::post('/', [EngineeringController::class, 'store'])->name('engineering.store');
             Route::get('/{id}/edit', [EngineeringController::class, 'edit'])->name('engineering.edit');
             Route::put('/{id}', [EngineeringController::class, 'update'])->name('engineering.update');
             Route::post('/{id}', [EngineeringController::class, 'update'])->name('engineering.update.post');
             Route::delete('/{id}', [EngineeringController::class, 'destroy'])->name('engineering.destroy');
        });
    });

    // Operasi Routes
    Route::prefix('operasi')->middleware(\App\Http\Middleware\CheckBidang::class . ':Operasi')->group(function () {
        Route::get('/', [OperasiController::class, 'index'])->name('operasi.index');
        Route::get('/{id}/get-file', [OperasiController::class, 'getFile'])->name('operasi.getFile');
        Route::get('/{id}/viewer', [OperasiController::class, 'viewer'])->name('operasi.viewer');
        Route::get('/{id}/download', [OperasiController::class, 'download'])->name('operasi.download');
        Route::get('/{id}/view', [OperasiController::class, 'view'])->name('operasi.view');
        Route::get('/{id}/get-edit-data', [OperasiController::class, 'getEditData'])->name('operasi.getEditData');

        // CRUD
         Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
            Route::get('/create', [OperasiController::class, 'create'])->name('operasi.create');
            Route::post('/', [OperasiController::class, 'store'])->name('operasi.store');
            Route::get('/{id}/edit', [OperasiController::class, 'edit'])->name('operasi.edit');
            Route::put('/{id}', [OperasiController::class, 'update'])->name('operasi.update');
            Route::post('/{id}', [OperasiController::class, 'update'])->name('operasi.update.post');
            Route::delete('/{id}', [OperasiController::class, 'destroy'])->name('operasi.destroy');
         });
    });

    // Pemeliharaan Routes
    Route::prefix('pemeliharaan')->middleware(\App\Http\Middleware\CheckBidang::class . ':Pemeliharaan')->group(function () {
        Route::get('/', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
        Route::get('/{id}/get-file', [PemeliharaanController::class, 'getFile'])->name('pemeliharaan.getFile');
        Route::get('/{id}/viewer', [PemeliharaanController::class, 'viewer'])->name('pemeliharaan.viewer');
        Route::get('/{id}/download', [PemeliharaanController::class, 'download'])->name('pemeliharaan.download');
        Route::get('/{id}/view', [PemeliharaanController::class, 'view'])->name('pemeliharaan.view');
        Route::get('/{id}/get-edit-data', [PemeliharaanController::class, 'getEditData'])->name('pemeliharaan.getEditData');

        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
            Route::get('/create', [PemeliharaanController::class, 'create'])->name('pemeliharaan.create');
            Route::post('/', [PemeliharaanController::class, 'store'])->name('pemeliharaan.store');
            Route::get('/{id}/edit', [PemeliharaanController::class, 'edit'])->name('pemeliharaan.edit');
            Route::put('/{id}', [PemeliharaanController::class, 'update'])->name('pemeliharaan.update');
            Route::post('/{id}', [PemeliharaanController::class, 'update'])->name('pemeliharaan.update.post');
            Route::delete('/{id}', [PemeliharaanController::class, 'destroy'])->name('pemeliharaan.destroy');
        });
    });

    // Business Support Routes
    Route::prefix('business-support')->middleware(\App\Http\Middleware\CheckBidang::class . ':Business Support')->group(function () {
        Route::get('/', [BusinessSupportController::class, 'index'])->name('business-support.index');
        Route::get('/{id}/get-file', [BusinessSupportController::class, 'getFile'])->name('business-support.getFile');
        Route::get('/{id}/viewer', [BusinessSupportController::class, 'viewer'])->name('business-support.viewer');
        Route::get('/{id}/download', [BusinessSupportController::class, 'download'])->name('business-support.download');
        Route::get('/{id}/view', [BusinessSupportController::class, 'view'])->name('business-support.view');
        Route::get('/{id}/get-edit-data', [BusinessSupportController::class, 'getEditData'])->name('business-support.getEditData');

        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
            Route::get('/create', [BusinessSupportController::class, 'create'])->name('business-support.create');
            Route::post('/', [BusinessSupportController::class, 'store'])->name('business-support.store');
            Route::get('/{id}/edit', [BusinessSupportController::class, 'edit'])->name('business-support.edit');
            Route::put('/{id}', [BusinessSupportController::class, 'update'])->name('business-support.update');
            Route::post('/{id}', [BusinessSupportController::class, 'update'])->name('business-support.update.post');
            Route::delete('/{id}', [BusinessSupportController::class, 'destroy'])->name('business-support.destroy');
        });
    });

    // Keamanan Routes
    Route::prefix('keamanan')->middleware(\App\Http\Middleware\CheckBidang::class . ':Keamanan')->group(function () {
        Route::get('/', [KeamananController::class, 'index'])->name('keamanan.index');
        Route::get('/{id}/get-file', [KeamananController::class, 'getFile'])->name('keamanan.getFile');
        Route::get('/{id}/viewer', [KeamananController::class, 'viewer'])->name('keamanan.viewer');
        Route::get('/{id}/download', [KeamananController::class, 'download'])->name('keamanan.download');
        Route::get('/{id}/view', [KeamananController::class, 'view'])->name('keamanan.view');
        Route::get('/{id}/get-edit-data', [KeamananController::class, 'getEditData'])->name('keamanan.getEditData');

        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
            Route::get('/create', [KeamananController::class, 'create'])->name('keamanan.create');
            Route::post('/', [KeamananController::class, 'store'])->name('keamanan.store');
            Route::get('/{id}/edit', [KeamananController::class, 'edit'])->name('keamanan.edit');
            Route::put('/{id}', [KeamananController::class, 'update'])->name('keamanan.update');
            Route::post('/{id}', [KeamananController::class, 'update'])->name('keamanan.update.post');
            Route::delete('/{id}', [KeamananController::class, 'destroy'])->name('keamanan.destroy');
        });
    });

    // Lingkungan Routes
    Route::prefix('lingkungan')->middleware(\App\Http\Middleware\CheckBidang::class . ':Lingkungan')->group(function () {
        Route::get('/', [LingkunganController::class, 'index'])->name('lingkungan.index');
        Route::get('/{id}/get-file', [LingkunganController::class, 'getFile'])->name('lingkungan.getFile');
        Route::get('/{id}/viewer', [LingkunganController::class, 'viewer'])->name('lingkungan.viewer');
        Route::get('/{id}/download', [LingkunganController::class, 'download'])->name('lingkungan.download');
        Route::get('/{id}/view', [LingkunganController::class, 'view'])->name('lingkungan.view');
        Route::get('/{id}/get-edit-data', [LingkunganController::class, 'getEditData'])->name('lingkungan.getEditData');

        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
            Route::get('/create', [LingkunganController::class, 'create'])->name('lingkungan.create');
            Route::post('/', [LingkunganController::class, 'store'])->name('lingkungan.store');
            Route::get('/{id}/edit', [LingkunganController::class, 'edit'])->name('lingkungan.edit');
            Route::put('/{id}', [LingkunganController::class, 'update'])->name('lingkungan.update');
            Route::post('/{id}', [LingkunganController::class, 'update'])->name('lingkungan.update.post');
            Route::delete('/{id}', [LingkunganController::class, 'destroy'])->name('lingkungan.destroy');
        });
    });

    // Folder Routes (General)
    Route::prefix('folder')->group(function () {
        Route::get('/', [FolderController::class, 'index'])->name('folder.index');
        Route::get('/tree', [FolderController::class, 'tree'])->name('folder.tree');
        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
            Route::get('/create', [FolderController::class, 'create'])->name('folder.create');
            Route::post('/', [FolderController::class, 'store'])->name('folder.store');
            Route::get('/{id}/edit', [FolderController::class, 'edit'])->name('folder.edit');
            Route::put('/{id}', [FolderController::class, 'update'])->name('folder.update');
            Route::delete('/{id}', [FolderController::class, 'destroy'])->name('folder.destroy');
        });
    });

    // Excel Routes
    Route::prefix('excel')->group(function () {
        Route::get('/', [ExcelController::class, 'index'])->name('excel.index');
        Route::post('/upload', [ExcelController::class, 'upload'])->name('excel.upload');
        Route::get('/{excelUpload}/view', [ExcelController::class, 'view'])->name('excel.view');
        Route::delete('/{excelUpload}', [ExcelController::class, 'destroy'])->name('excel.destroy');
        Route::get('/{excelUpload}/download', [ExcelController::class, 'download'])->name('excel.download');
    });

    // API Routes for Calendar
    Route::prefix('api')->group(function () {
        Route::post('/reminder', [CalendarController::class, 'storeReminder']);
        Route::delete('/reminder/{id}', [CalendarController::class, 'deleteReminder']);
        Route::get('/reminders', [CalendarController::class, 'getReminders']);
        Route::get('/calendar', [CalendarController::class, 'getCalendar']);
        Route::get('/events', [CalendarController::class, 'getEvents']);
    });

    // Asset Wellness Routes (Mesin)
    Route::middleware(\App\Http\Middleware\CheckBidang::class . ':Mesin')->group(function () {
        Route::get('asset-wellness', [AssetWellnessController::class, 'index'])->name('asset-wellness.index');
        Route::get('asset-wellness-download', [AssetWellnessController::class, 'download'])->name('asset-wellness.download');
        Route::get('asset-wellness-export', [AssetWellnessController::class, 'exportExcel'])->name('asset-wellness.export');
        Route::get('asset-wellness-pdf-report', [AssetWellnessController::class, 'exportPdfReport'])->name('asset-wellness.pdf-report');
        Route::get('asset-wellness-pdf-screenshots', [AssetWellnessController::class, 'exportPdfScreenshots'])->name('asset-wellness.pdf-screenshots');
        Route::post('asset-wellness/import', [AssetWellnessController::class, 'importExcel'])->name('asset-wellness.import');
        
        Route::get('detail-warning', [DetailWarningController::class, 'index'])->name('detail-warning.index');
        Route::get('detail-fault', [DetailFaultController::class, 'index'])->name('detail-fault.index');

        // CRUD
        Route::middleware(\App\Http\Middleware\CheckRole::class . ':super_admin,admin')->group(function () {
             Route::delete('asset-wellness/delete-period', [AssetWellnessController::class, 'destroyPeriod'])->name('asset-wellness.destroyPeriod');
             Route::get('asset-wellness/create', [AssetWellnessController::class, 'create'])->name('asset-wellness.create');
             Route::post('asset-wellness', [AssetWellnessController::class, 'store'])->name('asset-wellness.store');
             Route::get('asset-wellness/{asset_wellness}/edit', [AssetWellnessController::class, 'edit'])->name('asset-wellness.edit');
             // Resource routes usually bind to model, explicit route is easier to debug here for now or use resource except index
             Route::get('asset-wellness/{asset_wellness}', [AssetWellnessController::class, 'show'])->name('asset-wellness.show');
             Route::put('asset-wellness/{asset_wellness}', [AssetWellnessController::class, 'update'])->name('asset-wellness.update');
             Route::delete('asset-wellness/{asset_wellness}', [AssetWellnessController::class, 'destroy'])->name('asset-wellness.destroy');
             
             // Detail Warning CRUD
             Route::get('detail-warning/create', [DetailWarningController::class, 'create'])->name('detail-warning.create');
             Route::post('detail-warning', [DetailWarningController::class, 'store'])->name('detail-warning.store');
             Route::get('detail-warning/{id}/edit', [DetailWarningController::class, 'edit'])->name('detail-warning.show');
             Route::put('detail-warning/{id}', [DetailWarningController::class, 'update'])->name('detail-warning.update');
             Route::delete('detail-warning/{id}', [DetailWarningController::class, 'destroy'])->name('detail-warning.destroy');

             // Detail Fault CRUD
             Route::get('detail-fault/create', [DetailFaultController::class, 'create'])->name('detail-fault.create');
             Route::post('detail-fault', [DetailFaultController::class, 'store'])->name('detail-fault.store');
             Route::get('detail-fault/{id}/edit', [DetailFaultController::class, 'edit'])->name('detail-fault.show');
             Route::put('detail-fault/{id}', [DetailFaultController::class, 'update'])->name('detail-fault.update');
             Route::delete('detail-fault/{id}', [DetailFaultController::class, 'destroy'])->name('detail-fault.destroy');
        });
    });

});
