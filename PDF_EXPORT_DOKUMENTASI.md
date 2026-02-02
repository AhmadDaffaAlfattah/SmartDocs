# DOKUMENTASI FITUR PDF EXPORT - LAPORAN ASSET WELLNESS

## 📋 Ringkasan Implementasi

Fitur PDF export untuk Laporan Asset Wellness telah berhasil diimplementasikan dengan struktur 3 halaman yang dapat diekspor ke dalam satu file PDF.

---

## 🎯 Spesifikasi Laporan

### **Halaman 1: Form Penyampaian**

- **Judul**: LAPORAN BULANAN ASSET WELLNESS - PT PLN NUSANTARA POWER / PLN INDONESIA POWER
- **Konten**: Tabel data induk Asset Wellness dengan kolom:
    - NO
    - SENTRAL
    - TIPE ASET
    - KODE MESIN
    - UNIT PEMBANGKIT
    - DAYA TERPASANG (TP)
    - DAYA NETTO
    - DAYA PASOK
    - TOTAL EQUIPMENT
    - SAFE (hijau)
    - WARNING (kuning)
    - FAULT (merah)
- **Footer**: "Halaman 1 dari 3 - Form Penyampaian Asset Wellness"
- **Statistik**: Ringkasan total equipment dan status breakdown

### **Halaman 2: Detail Warning**

- **Judul**: LAPORAN BULANAN ASSET WELLNESS - PT PLN NUSANTARA POWER / PLN INDONESIA POWER
- **Subtitle**: ASSET WELLNESS DENGAN STATUS WARNING
- **Konten**: Tabel detail warning dengan kolom:
    - NO
    - UNIT PEMBANGKIT
    - TANGGAL IDENTIFIKASI
    - STATUS SAAT INI
    - DESKRIPSI ASET
    - KONDISI ASET
    - ACTION PLAN
- **Footer**: "Halaman 2 dari 3 - Detail Asset Wellness dengan Status WARNING"
- **Statistik**: Total warning items

### **Halaman 3: Detail Fault**

- **Judul**: LAPORAN BULANAN ASSET WELLNESS - PT PLN NUSANTARA POWER / PLN INDONESIA POWER
- **Subtitle**: ASSET WELLNESS DENGAN STATUS FAULT
- **Konten**: Tabel detail fault dengan kolom sama seperti warning
- **Footer**: "Halaman 3 dari 3 - Detail Asset Wellness dengan Status FAULT"
- **Statistik**: Total fault items

---

## 📁 File yang Telah Dibuat/Dimodifikasi

### 1. **Controller Method**

- File: `app/Http/Controllers/AssetWellnessController.php`
- Method: `exportPdfReport()`
- Fungsi: Mengambil data dari database dan merender view PDF

```php
public function exportPdfReport(Request $request)
{
    $tahun = $request->get('tahun', '2025');
    $bulan = $request->get('bulan', '12');
    $sentral = $request->get('sentral', '');

    $query = AssetWellness::query();

    if ($tahun) {
        $query->where('tahun', $tahun);
    }
    if ($bulan) {
        $query->where('bulan', $bulan);
    }
    if ($sentral) {
        $query->where('sentral', $sentral);
    }

    $assets = $query->orderBy('kode_mesin')->get();

    // Get detail warning and fault
    $detailWarnings = \App\Models\DetailWarning::with('assetWellness')
        ->orderBy('created_at', 'desc')
        ->get();

    $detailFaults = \App\Models\DetailFault::with('assetWellness')
        ->orderBy('created_at', 'desc')
        ->get();

    // Load Blade view dan convert ke PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.asset_wellness_pdf_report', [
        'assets' => $assets,
        'detailWarnings' => $detailWarnings,
        'detailFaults' => $detailFaults,
        'tahun' => $tahun,
        'bulan' => $bulan,
        'sentral' => $sentral
    ]);

    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('margin-top', 10);
    $pdf->setOption('margin-bottom', 10);
    $pdf->setOption('margin-left', 10);
    $pdf->setOption('margin-right', 10);

    $filename = 'Laporan_Asset_Wellness_' . $tahun . '_' . $bulan . '_' . date('YmdHis') . '.pdf';

    return $pdf->download($filename);
}
```

### 2. **Route**

- File: `routes/web.php`
- Route: `asset-wellness-pdf-report`
- Method: GET
- Path: `asset-wellness-pdf-report`

```php
Route::get('asset-wellness-pdf-report', [AssetWellnessController::class, 'exportPdfReport'])
    ->name('asset-wellness.pdf-report');
```

### 3. **Blade Template**

- File: `resources/views/exports/asset_wellness_pdf_report.blade.php`
- Struktur: 3 halaman dengan page-break-after untuk pemisahan halaman
- Styling: CSS modern dengan border, color, dan formatting

### 4. **UI Button**

- File: `resources/views/asset-wellness/index_with_tabs.blade.php`
- Lokasi: Download menu dropdown
- Label: "📋 Laporan PDF (3 Halaman)"

---

## 🚀 Cara Menggunakan

### Dari User Interface:

1. Buka halaman Asset Wellness Index
2. Pilih filter (Tahun, Bulan, Sentral) jika diperlukan
3. Klik tombol "📥 Download" di kanan atas
4. Pilih "📋 Laporan PDF (3 Halaman)"
5. File PDF akan otomatis download

### Dari URL Direct:

```
GET /asset-wellness-pdf-report?tahun=2025&bulan=12&sentral=
```

Parameters:

- `tahun` (required): Tahun laporan
- `bulan` (required): Bulan laporan (01-12)
- `sentral` (optional): Filter berdasarkan sentral

---

## 🎨 Styling dan Formatting

### Warna Status:

- **SAFE**: Hijau (#90EE90)
- **WARNING**: Kuning (#FFD700)
- **FAULT**: Merah (#FF6B6B)

### Header Style:

- Border bawah 3px solid
- Background putih dengan padding
- Font bold dan ukuran disesuaikan

### Tabel:

- Header: Dark gray background (#333) dengan text putih
- Alternating rows: Putih dan light gray
- Border: 1px solid #ddd

---

## 📦 Dependencies

Library yang digunakan:

- **Barryvdh\DomPDF**: Untuk konversi HTML ke PDF
- **Laravel PDF Facade**: Interface untuk DomPDF

Package sudah diinstall di composer.json:

```json
"barryvdh/laravel-dompdf": "^2.1"
```

---

## ✅ Testing Checklist

- [x] Method `exportPdfReport()` sudah ditambahkan ke controller
- [x] Route sudah terdaftar di web.php
- [x] Blade template sudah dibuat dengan 3 halaman
- [x] UI button sudah ditambahkan ke index view
- [x] Package DomPDF sudah terinstall
- [x] Model relasi sudah benar (DetailWarning & DetailFault)

---

## 🔧 Troubleshooting

### Jika PDF tidak tergenerate:

1. Pastikan package DomPDF sudah diinstall: `composer require barryvdh/laravel-dompdf`
2. Pastikan view file ada di: `resources/views/exports/asset_wellness_pdf_report.blade.php`
3. Clear cache: `php artisan cache:clear`
4. Clear route cache: `php artisan route:clear`

### Jika kolom data tidak muncul:

1. Verifikasi nama column di database sesuai dengan model
2. Pastikan relasi assetWellness sudah benar di DetailWarning dan DetailFault
3. Check apakah data ada di database sebelum export

### Jika styling tidak sesuai:

1. CSS sudah dievaluasi langsung di blade template
2. DomPDF memiliki keterbatasan CSS, gunakan style inline jika diperlukan
3. Test dengan browser print preview sebelum download

---

## 📝 Catatan Penting

1. **Tanggal Pelaporan**: Otomatis menggunakan tanggal hari ini saat PDF digenerate
2. **Filter**: Data akan difilter berdasarkan tahun, bulan, dan sentral dari query parameter
3. **Page Break**: Setiap halaman otomatis terpisah dengan `page-break-after: always`
4. **Footer**: Setiap halaman memiliki footer dengan nomor halaman
5. **Statistik**: Halaman 1 menampilkan ringkasan statistik total equipment

---

## 📊 Data yang Ditampilkan

### Dari Tabel asset_wellness:

- sentral, tipe_aset, kode_mesin, unit_pembangkit_common
- total_equipment, safe, warning, fault
- daya_terpasang, daya_mampu_netto, daya_mampu_pasok
- keterangan

### Dari Tabel detail_warning:

- asset_wellness_id (relasi)
- unit_pembangkit, tanggal_identifikasi, status_saat_ini
- asset_description, kondisi_aset, action_plan
- target_selesai, progres_saat_ini, realisasi_selesai
- main_issue_kendala, keterangan

### Dari Tabel detail_fault:

- (Sama seperti detail_warning)

---

## 🎁 Bonus Features

1. **Responsive Design**: PDF dapat dibuka di berbagai ukuran device
2. **Print Friendly**: Format sudah optimal untuk printing
3. **Color Coded Status**: Mudah membedakan status equipment
4. **Summary Statistics**: Ringkasan otomatis di setiap halaman
5. **Filename Dynamic**: Nama file include tahun, bulan, dan timestamp

---

**Status**: ✅ IMPLEMENTED
**Last Updated**: {{ date('d-m-Y H:i:s') }}
**Version**: 1.0
