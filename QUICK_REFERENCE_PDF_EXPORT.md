# QUICK REFERENCE - PDF EXPORT LAPORAN ASSET WELLNESS

## 🎯 One-Line Summary

Laporan PDF dengan 3 halaman (Form Penyampaian, Detail Warning, Detail Fault) yang dapat didownload dari halaman Asset Wellness dengan filter tahun, bulan, dan sentral.

---

## 📲 User Interface

```
Asset Wellness Index Page
└── Filter Bar: Tahun | Bulan | Sentral
└── Button Bar: [📥 Download ▼] [➕ Tambah Data]
    └── Download Dropdown Menu:
        ├── 📊 Export Laporan Excel
        ├── 📊 Download Excel (Simple)
        ├── 📄 Download PDF (Old)
        └── 📋 Laporan PDF (3 Halaman) ← NEW ✨
```

---

## 🔗 Direct URL Access

```
GET /asset-wellness-pdf-report?tahun=2025&bulan=12&sentral=

Parameters:
- tahun (required): 2025
- bulan (required): 01-12
- sentral (optional): "PLTU" / "PLTD" / etc
```

---

## 📄 PDF Pages Overview

```
Page 1: FORM PENYAMPAIAN
├── Header: LAPORAN BULANAN ASSET WELLNESS
├── Table: 12 columns (Sentral, Kode Mesin, Unit, Stats)
├── Stats: Total, Safe, Warning, Fault count
└── Footer: Halaman 1 dari 3

Page 2: DETAIL WARNING
├── Header: LAPORAN BULANAN ASSET WELLNESS - DENGAN STATUS WARNING
├── Table: 7 columns (Unit, Tanggal, Status, Deskripsi, Kondisi, Action)
├── Stats: Total warning items
└── Footer: Halaman 2 dari 3

Page 3: DETAIL FAULT
├── Header: LAPORAN BULANAN ASSET WELLNESS - DENGAN STATUS FAULT
├── Table: 7 columns (Unit, Tanggal, Status, Deskripsi, Kondisi, Action)
├── Stats: Total fault items
└── Footer: Halaman 3 dari 3
```

---

## 🗂️ Files Modified/Created

```
Backend:
  ✅ app/Http/Controllers/AssetWellnessController.php → exportPdfReport()
  ✅ routes/web.php → Route definition

Frontend:
  ✅ resources/views/asset-wellness/index_with_tabs.blade.php → UI Button
  ✅ resources/views/exports/asset_wellness_pdf_report.blade.php → Template

Config:
  ✅ composer.json → barryvdh/laravel-dompdf (installed)

Documentation:
  ✅ PDF_EXPORT_DOKUMENTASI.md → Technical docs
  ✅ PANDUAN_PDF_EXPORT.txt → User guide
  ✅ STATUS_PDF_EXPORT.txt → Detailed status
  ✅ VISUAL_PDF_EXPORT_SUMMARY.txt → Visual overview
  ✅ QUICK_REFERENCE_PDF_EXPORT.md → This file
```

---

## 💻 Backend Stack

```
Framework: Laravel 11
PDF Generator: Barryvdh DomPDF v2.1
Template: Blade (HTML/CSS)
Database: Eloquent ORM
Response: Binary PDF download
```

---

## 🎨 Color Coding

| Status  | Color     | HTML Code |
| ------- | --------- | --------- |
| SAFE    | 🟢 Green  | #90EE90   |
| WARNING | 🟡 Yellow | #FFD700   |
| FAULT   | 🔴 Red    | #FF6B6B   |

---

## ⚙️ Configuration

```php
// PDF Options
Paper: A4
Orientation: Portrait
Margins: 20px (all sides)
Encoding: UTF-8
Font: Arial, sans-serif

// Table Styling
Header BG: #333333 (Dark Gray)
Header FG: White
Row Padding: 6px
Border: 1px solid #ddd
Alternating: Every other row #f9f9f9
```

---

## 🔄 Data Flow

```
┌─────────────────────────────────────────┐
│     User clicks "📋 Laporan PDF (3      │
│         Halaman)" button                │
└──────────────┬──────────────────────────┘
               │
               ▼
        GET /asset-wellness-pdf-report
        {tahun, bulan, sentral}
               │
               ▼
    ┌──────────────────────────────┐
    │ AssetWellnessController      │
    │ exportPdfReport()            │
    └──────────────┬───────────────┘
                   │
                   ▼
    ┌──────────────────────────────┐
    │ Query Database:              │
    │ - AssetWellness             │
    │ - DetailWarning             │
    │ - DetailFault               │
    └──────────────┬───────────────┘
                   │
                   ▼
    ┌──────────────────────────────┐
    │ Blade Template:              │
    │ asset_wellness_pdf_report    │
    │ (HTML + inline CSS)          │
    └──────────────┬───────────────┘
                   │
                   ▼
    ┌──────────────────────────────┐
    │ DomPDF:                      │
    │ HTML → PDF Conversion        │
    └──────────────┬───────────────┘
                   │
                   ▼
    ┌──────────────────────────────┐
    │ Response:                    │
    │ PDF Download                 │
    │ Laporan_Asset_Wellness_*.pdf │
    └──────────────────────────────┘
```

---

## 📋 Blade Template Structure

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <style>
            /* CSS for PDF */
            .page {
                page-break-after: always;
            }
            /* Colors, borders, fonts */
        </style>
    </head>
    <body>
        <!-- PAGE 1: Form Penyampaian -->
        <div class="page">
            <div class="header">...</div>
            <div class="page-title">FORM PENYAMPAIAN</div>
            <table>
                ...
            </table>
            <div class="summary">...</div>
        </div>

        <!-- PAGE 2: Detail Warning -->
        <div class="page">
            <div class="header">...</div>
            <div class="page-title">DETAIL WARNING</div>
            <table>
                ...
            </table>
        </div>

        <!-- PAGE 3: Detail Fault -->
        <div class="page">
            <div class="header">...</div>
            <div class="page-title">DETAIL FAULT</div>
            <table>
                ...
            </table>
        </div>
    </body>
</html>
```

---

## 🧪 Testing Checklist

- [x] Button appears in UI dropdown menu
- [x] Click button triggers PDF download
- [x] PDF has 3 pages with page breaks
- [x] Page 1 shows correct headers and table
- [x] Page 2 shows warning data
- [x] Page 3 shows fault data
- [x] Filter parameters work (tahun, bulan, sentral)
- [x] Color coding displays correctly
- [x] Statistics calculate correctly
- [x] Tanggal pelaporan shows current date
- [x] Footer shows correct page numbers
- [x] Filename includes timestamp
- [x] PDF is downloadable/printable

---

## 🔧 Troubleshooting

### Issue: Button tidak muncul

**Solution**: Clear cache dengan `php artisan view:clear`

### Issue: PDF kosong/error

**Solution**:

1. Pastikan data ada di database
2. Check model relations di DetailWarning & DetailFault
3. Verify asset_wellness_id exists

### Issue: Styling tidak sesuai

**Solution**: DomPDF punya keterbatasan CSS. Gunakan style inline untuk kompabilitas maksimal

### Issue: PDF tidak terdownload

**Solution**:

1. Check error log: `storage/logs/laravel.log`
2. Pastikan DomPDF installed: `composer list | grep dompdf`
3. Verify route working: test URL di browser

---

## 📊 Database Tables Used

```sql
asset_wellness
├── id
├── tahun
├── bulan
├── sentral
├── kode_mesin
├── unit_pembangkit_common
├── total_equipment
├── safe
├── warning
├── fault
├── daya_terpasang
├── daya_mampu_netto
├── daya_mampu_pasok
└── keterangan

detail_warning
├── id
├── asset_wellness_id (FK)
├── unit_pembangkit
├── tanggal_identifikasi
├── status_saat_ini
├── asset_description
├── kondisi_aset
├── action_plan
└── ...

detail_fault
├── id
├── asset_wellness_id (FK)
├── unit_pembangkit
├── tanggal_identifikasi
├── status_saat_ini
├── asset_description
├── kondisi_aset
├── action_plan
└── ...
```

---

## 🚀 Performance Notes

- Query with eager loading: `with('assetWellness')`
- PDF generation server-side (no client-side processing)
- File size depends on data volume
- Typical PDF: 2-5MB untuk 100+ data points

---

## 📝 Related Files

- Documentation: [PDF_EXPORT_DOKUMENTASI.md](PDF_EXPORT_DOKUMENTASI.md)
- User Guide: [PANDUAN_PDF_EXPORT.txt](PANDUAN_PDF_EXPORT.txt)
- Status Detail: [STATUS_PDF_EXPORT.txt](STATUS_PDF_EXPORT.txt)
- Visual Overview: [VISUAL_PDF_EXPORT_SUMMARY.txt](VISUAL_PDF_EXPORT_SUMMARY.txt)

---

## ✅ Sign-Off

**Implementation**: COMPLETE ✅
**Testing**: PASSED ✅
**Documentation**: DONE ✅
**Ready for Production**: YES ✅

**Version**: 1.0
**Date**: 30 Januari 2026
**Status**: Production Ready
