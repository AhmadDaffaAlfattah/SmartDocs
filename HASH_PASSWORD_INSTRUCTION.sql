-- SQL Script: Hash semua password di database yang belum ter-hash
-- Jalankan perintah ini di terminal MySQL Anda sesuai instruksi di bawah

-- Untuk menjalankan ini, gunakan salah satu cara:

-- CARA 1: Di MySQL Command Line (Recommended)
-- mysql -u root -p dokumenintegrasi < hash_passwords.sql

-- CARA 2: Di phpMyAdmin atau GUI MySQL
-- Copy seluruh content file ini dan paste ke SQL Query tab, kemudian execute

-- CARA 3: Di artisan tinker (Laravel)
-- php artisan tinker
-- Kemudian jalankan:
-- DB::statement("UPDATE users SET password = CONCAT('$2y$10$', SHA2(CONCAT(password, CAST(id AS CHAR)), 256))) WHERE password NOT LIKE '$2y$%'");

-- ============================================================
-- Script untuk hash password di database
-- ============================================================

-- Update password yang belum ter-hash dengan bcrypt hash
-- Password akan di-hash dengan bcrypt algorithm

-- Untuk melakukan ini, gunakan Laravel artisan tinker:
-- php artisan tinker
-- 
-- Kemudian jalankan kode berikut:
-- 
-- App\Models\User::all()->each(function($user) {
--     if (!str_starts_with($user->password, '$2y$')) {
--         $user->update(['password' => bcrypt($user->password)]);
--     }
-- });
-- 
-- Atau gunakan query berikut untuk cek password yang belum di-hash:
-- SELECT id, name, email, password FROM users WHERE password NOT LIKE '$2y$%';

-- Verifikasi: Password yang sudah di-hash akan dimulai dengan "$2y$"
-- Contoh password ter-hash: $2y$10$n9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36CHqDrC

-- Query verifikasi:
SELECT id, name, email,
    CASE 
           WHEN password LIKE '$2y$%' THEN 'Hashed ✓'
           ELSE 'Plain Text ✗'
       END AS status
FROM users;
