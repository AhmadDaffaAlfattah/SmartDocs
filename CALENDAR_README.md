# Kalender - DORI Integration

## 📅 Overview

Kalender interaktif terintegrasi dengan DORI (Dokumen Terintegrasi) yang memungkinkan:
- ✅ Menampilkan kalender untuk tahun 2025-2030
- ✅ Menandai hari libur nasional (merah)
- ✅ Menampilkan hari ini (biru)
- ✅ Menambah/mengelola reminder (kuning)
- ✅ Menyimpan reminder ke database
- ✅ Navigasi antar bulan dan tahun

## 🎨 Sistem Warna

```
┌─────────────────────────────────────────┐
│            KALENDER 2025                │
├──────────────────────────────────────────┤
│ S  M  T  W  T  F  S                     │
│                    1  2  3              │
│ 4  5  6  7  8  9  10                    │
│ 11 12 13 14 15 16 17                    │
│ 18 19 20 21 22 23 24                    │
│ 25 26 27 28 29 30 31                    │
│                                         │
│ ⬜ Putih: Hari Biasa                     │
│ 🟥 Merah: Hari Libur                    │
│ 🟦 Biru: Hari Ini                       │
│ 🟨 Kuning: Ada Reminder                 │
└─────────────────────────────────────────┘
```

## 📁 Struktur File

```
dokumenintegrasi/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── CalendarController.php    ← Controller logika kalender
│   └── Models/
│       └── Reminder.php                   ← Model reminder
├── database/
│   └── migrations/
│       └── 2025_01_22_000000_create_reminders_table.php
├── resources/
│   └── views/
│       └── calendar/
│           └── index.blade.php           ← Template kalender
├── public/
│   ├── css/
│   │   └── calendar.css                  ← Styling kalender
│   └── js/
│       └── calendar.js                   ← Interaktivitas JS
├── routes/
│   └── web.php                           ← Routes (updated)
└── CALENDAR_SETUP.md                     ← Dokumentasi setup
```

## 🚀 Quick Start

### 1. Migrate Database
```bash
php artisan migrate
```

### 2. Akses Kalender
```
http://localhost:8000/calendar
```

### 3. Navigasi & Gunakan
- Pilih tahun dari dropdown (2025-2030)
- Gunakan tombol prev/next untuk navigasi bulan
- Klik tanggal untuk menambah reminder
- Input judul dan deskripsi reminder
- Klik "Tambah Reminder" untuk menyimpan

## 🔌 API Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/reminder` | Tambah reminder baru |
| DELETE | `/api/reminder/{id}` | Hapus reminder |
| GET | `/api/reminders` | Get semua reminder |
| GET | `/api/calendar?year=2025&month=2` | Get data kalender |

## 📊 Request/Response Examples

### Tambah Reminder
```bash
POST /api/reminder
Content-Type: application/json

{
  "title": "Rapat Tim",
  "description": "Membahas update DORI",
  "date": "2025-02-15"
}

Response 201:
{
  "id": 1,
  "title": "Rapat Tim",
  "description": "Membahas update DORI",
  "date": "2025-02-15",
  "color": "yellow",
  "created_at": "2025-01-22T10:00:00Z",
  "updated_at": "2025-01-22T10:00:00Z"
}
```

### Hapus Reminder
```bash
DELETE /api/reminder/1

Response 200:
{
  "message": "Reminder deleted successfully"
}
```

## 💾 Database Schema

```sql
CREATE TABLE reminders (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description LONGTEXT,
  date DATE NOT NULL,
  color VARCHAR(50) DEFAULT 'yellow',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🎯 Features Detail

### Kalender Display
- Seluruh bulan ditampilkan dalam grid 7x6
- Header menunjukkan nama bulan dan tahun
- Hari libur sudah pre-configured untuk 2025-2030
- Support responsive design (mobile-friendly)

### Reminder Management
- Input: Tanggal, Judul, Deskripsi (opsional)
- Simpan ke database
- Tampilkan di kalender (warna kuning)
- List reminder di sidebar
- Delete functionality

### Navigation
- Previous/Next month buttons
- Year selector (2025-2030)
- Auto-navigate saat bulan December/January

## 🎨 Styling

### Colors Used
- Putih (#ffffff) - Normal days
- Merah (#ff6b6b) - Holidays
- Biru (#0066ff) - Today
- Kuning (#ffff00) - Reminders
- Abu-abu (#f8f9fa) - Headers/empty

### Responsive Breakpoints
- Desktop: Full layout (calendar + reminder sidebar)
- Tablet (1024px): Stacked layout
- Mobile (768px): Adjusted sizing
- Small (480px): Minimal layout

## 🔄 Workflow

```
1. User akses /calendar
2. Load halaman + fetch reminders dari DB
3. Render kalender dengan data
4. User interaksi:
   - Navigasi bulan/tahun
   - Klik tanggal
   - Input reminder data
   - Submit via AJAX
5. Server validate & simpan ke DB
6. Update UI dengan data baru
7. Refresh kalender display
```

## ⚠️ Dependencies

- Laravel 10+ (Eloquent, Blade)
- PHP 8.1+
- Modern browser (ES6+ support)
- jQuery tidak diperlukan (vanilla JS)

## 📝 Customization

### Tambah/Update Holiday
Edit `CalendarController.php` property `$holidays`:
```php
private $holidays = [
    '2025-01-01',  // Tahun Baru
    '2025-04-10',  // Idul Fitri
    // ... tambah lebih banyak
];
```

### Ubah Warna
Edit `calendar.css`:
```css
.calendar td.day-holiday {
    background-color: #ff6b6b; /* Change this */
}

.calendar td.day-today {
    background-color: #0066ff; /* Or this */
}
```

### Ubah Bahasa
Edit `calendar.js` array `monthNames` dan `CalendarController` field labels

## 🧪 Testing

Kalender sudah ready untuk digunakan. Test dengan:
1. Akses URL `/calendar`
2. Verify kalender muncul untuk bulan saat ini
3. Test navigasi bulan/tahun
4. Test tambah reminder
5. Verify data disimpan ke database
6. Test hapus reminder

---

**Last Updated**: January 22, 2025  
**Version**: 1.0  
**Status**: Ready for Use ✅
