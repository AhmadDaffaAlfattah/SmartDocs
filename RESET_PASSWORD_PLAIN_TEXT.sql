-- UPDATE PASSWORD DARI HASH MENJADI PLAIN TEXT
-- Karena sebelumnya password sudah di-hash, kita perlu reset ke plain text

-- Caranya: Jalankan query update manual untuk setiap user
-- ATAU reset password melalui halaman Account

-- Jika ingin menggunakan halaman Account:
-- 1. Login ke halaman Account
-- 2. Klik Edit pada setiap user
-- 3. Ubah password dengan password baru
-- 4. Simpan

-- Atau gunakan query ini untuk set password ke default:
UPDATE users SET password = 'password123' WHERE id = 1;

-- Ganti password123 dengan password yang Anda inginkan
-- Ganti id = 1 dengan ID user yang ingin di-update
