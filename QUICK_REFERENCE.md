# 🚀 DORI Quick Reference

## File-File Penting

| File | Lokasi | Fungsi |
|------|--------|--------|
| **dori.blade.php** | `resources/views/` | Template halaman utama |
| **dori.css** | `resources/css/` | Stylesheet utama |
| **dori.js** | `resources/js/` | JavaScript interaktif |
| **web.php** | `routes/` | Route configuration |
| **logo_pln.png** | `public/images/` | Logo PLN ✓ Sudah ada |
| **akun.png** | `public/images/` | Profile icon ✓ Sudah ada |

## 🎯 URL Akses

```
http://localhost:8000        ← Halaman Utama DORI
```

## 📊 Layout Structure

```
┌─────────────────────────────────────────────┐
│         HEADER (Logo + Profile)             │
├──────────────┬──────────────────────────────┤
│              │                              │
│   SIDEBAR    │         MAIN CONTENT         │
│   (Menu)     │     (Grid 4x3 Cards)        │
│              │                              │
└──────────────┴──────────────────────────────┘
```

## 📋 Dokumen yang Ditampilkan

1. Worksheet System Owner
2. Laporan Lintas Bidang
3. Program Kerja SO
4. LCCM
5. Design Review
6. Peta Improvement
7. ECP
8. PKU
9. RCFA
10. RJPU
11. MPI
12. MATERI

## 🎨 Warna Utama

- **Background Card**: `#d3d3d3` (Abu-abu)
- **Card Hover**: `#c0c0c0` (Abu-abu lebih gelap)
- **Active State**: `#9a9a9a` (Abu-abu gelap)
- **Header**: White (`#fff`)
- **Background Page**: `#f5f5f5` (Abu-abu sangat muda)

## 🔧 Terminal Commands

```bash
# Jalankan server (Port 8000)
php artisan serve

# Bersihkan cache
php artisan optimize:clear

# Melihat routes
php artisan route:list

# Compile assets (jika menggunakan Vite)
npm run build
npm run dev
```

## 📝 Modifikasi Umum

### Mengubah Jumlah Kolom Grid

Edit `resources/css/dori.css`:
```css
.dori-grid {
    grid-template-columns: repeat(3, 1fr);  /* Ubah dari 4 ke 3 */
}
```

### Mengubah Ukuran Card

Edit `resources/css/dori.css`:
```css
.dori-card {
    height: 150px;      /* Ubah tinggi card */
    font-size: 13px;    /* Ubah ukuran font */
}
```

### Menambah Item Sidebar

Edit `resources/views/dori.blade.php`:
```html
<div class="dori-sidebar-item">Item Baru</div>
```

### Menambah Card Baru

Edit `resources/views/dori.blade.php`:
```html
<div class="dori-card">
    <div class="dori-card-content">
        <span class="dori-card-icon">🎯</span>
        <span>Nama Dokumen</span>
    </div>
</div>
```

## 🎪 Icons yang Tersedia

Emoji yang digunakan di cards:
- 📋 Documents
- 📊 Reports/Analytics
- 📈 Growth/Progress
- 🔧 Tools/Settings
- ✏️ Edit/Write
- 🗺️ Map/Routes
- 🎯 Target/Goals
- 📝 Notes
- 🔍 Search/Review
- 📉 Analysis
- 📚 Learning/Materials

## ✅ Checklist Verifikasi

- [x] Route / sudah mengarah ke dori.blade.php
- [x] CSS file ada di public/css/dori.css
- [x] JavaScript file ada di public/js/dori.js
- [x] Logo PLN ada di public/images/logo_pln.png
- [x] Profile icon ada di public/images/akun.png
- [x] Template dori.blade.php sudah ada
- [x] 12 item sudah ditampilkan di sidebar
- [x] 12 card sudah ditampilkan di grid
- [x] Responsive design sudah diimplementasikan

## 🌐 Responsive Breakpoints

| Device | Breakpoint | Kolom | Sidebar |
|--------|-----------|-------|---------|
| Desktop | 1400px+ | 4 | Vertical |
| Laptop | 1200px+ | 3 | Vertical |
| Tablet | 1024px+ | 2 | Grid |
| Mobile | 768px+ | 2 | Grid |
| Small Mobile | <768px | 1 | Grid |

## 🔐 Permissions

Pastikan folder dapat ditulis:
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 📞 Kontak Support

- **Documentation**: Lihat SETUP.md
- **API Docs**: Lihat DORI_README.md
- **Issues**: Check TROUBLESHOOTING section di SETUP.md

---

**Version**: 1.0.0  
**Last Updated**: January 20, 2026  
**Status**: ✅ Production Ready
