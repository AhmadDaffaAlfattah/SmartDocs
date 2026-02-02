# 📅 Setup Kalender - DORI

## 📋 File-File yang Dibuat

### Backend
- `app/Http/Controllers/CalendarController.php` - Controller untuk handle kalender dan reminder
- `app/Models/Reminder.php` - Model untuk data reminder
- `database/migrations/2025_01_22_000000_create_reminders_table.php` - Migration untuk tabel reminders

### Frontend
- `resources/views/calendar/index.blade.php` - Template kalender utama
- `public/css/calendar.css` - Styling kalender dan reminder
- `public/js/calendar.js` - JavaScript untuk interaktivitas

### Routes
- `routes/web.php` - Updated dengan route calendar dan API

## 🚀 Langkah Setup

### 1. Jalankan Migration
```bash
php artisan migrate
```

Atau jika hanya menjalankan migration terbaru:
```bash
php artisan migrate --step
```

### 2. Akses Kalender
Buka browser ke URL:
```
http://localhost:8000/calendar
```

## ✨ Fitur-Fitur

### 🎨 Warna Kalender
- **Putih** - Hari biasa (normal)
- **Merah** - Hari libur nasional
- **Biru** - Hari/tanggal hari ini
- **Kuning** - Tanggal yang memiliki reminder

### 📅 Tahun Tersedia
- 2025
- 2026
- 2027
- 2028
- 2029
- 2030

### 💾 Reminder Features
- ✅ Tambah reminder ke tanggal tertentu
- ✅ Lihat semua reminder untuk tanggal yang dipilih
- ✅ Hapus reminder
- ✅ Disimpan ke database (persistent)
- ✅ Input: Tanggal, Judul, Deskripsi (opsional)

### 🗓️ Holiday Management
Holiday sudah dikonfigurasi untuk tahun 2025-2030 berdasarkan kalender nasional Indonesia

## 📌 API Endpoints

### POST `/api/reminder`
Menambah reminder baru
```json
{
  "title": "Rapat Tim",
  "description": "Rapat project DORI",
  "date": "2025-02-15"
}
```

### DELETE `/api/reminder/{id}`
Menghapus reminder

### GET `/api/reminders`
Mendapatkan semua reminder

### GET `/api/calendar`
Mendapatkan data kalender untuk bulan tertentu
```
?year=2025&month=2
```

## 🔧 Troubleshooting

### Database Error
Jika mendapat error database:
```bash
# Rebuild database
php artisan migrate:refresh

# Atau hanya reset
php artisan migrate:reset
php artisan migrate
```

### CSRF Token Error
CSRF token sudah tersedia di meta tag di template

### JavaScript Error
Pastikan file `calendar.js` sudah di-load dengan benar di browser console

## 📝 Database Schema

### Tabel: reminders
```
- id: bigint (primary key)
- title: string(255)
- description: text (nullable)
- date: date
- color: string(50) default 'yellow'
- created_at: timestamp
- updated_at: timestamp
```

## 🎯 Cara Menggunakan

1. **Navigasi Bulan**: Gunakan tombol "Sebelumnya" dan "Berikutnya"
2. **Pilih Tahun**: Gunakan dropdown tahun
3. **Klik Tanggal**: Klik pada tanggal di kalender untuk fokus pada reminder tanggal itu
4. **Tambah Reminder**:
   - Pilih tanggal di input tanggal
   - Masukkan judul reminder
   - Opsi: Tambah deskripsi
   - Klik "Tambah Reminder"
5. **Lihat Reminder**: Tanggal dengan reminder akan berwarna kuning
6. **Hapus Reminder**: Klik tombol "Hapus" di reminder list

## 📊 Legend
- Ming = Minggu (Sunday)
- Sen = Senin (Monday)
- Sel = Selasa (Tuesday)
- Rab = Rabu (Wednesday)
- Kam = Kamis (Thursday)
- Jum = Jumat (Friday)
- Sab = Sabtu (Saturday)

## ⚙️ Konfigurasi

Untuk menambah atau mengubah holiday, edit file:
```
app/Http/Controllers/CalendarController.php
```

Di property `$holidays`, tambahkan tanggal dengan format `YYYY-MM-DD`
