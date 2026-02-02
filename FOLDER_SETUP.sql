-- ===================================
-- SQL untuk Folder Hierarchy System
-- ===================================
-- File: FOLDER_SETUP.sql
-- Created: 2025-01-23

-- ========== OPTIONAL: DELETE OLD DATA ==========
-- Uncomment baris di bawah jika ingin reset semua data
-- DELETE FROM folders;
-- ALTER TABLE folders AUTO_INCREMENT = 1;

-- ========== CREATE TABLE FOLDERS (IF NOT EXISTS) ==========
CREATE TABLE
IF NOT EXISTS folders
(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_folder VARCHAR
(255) NOT NULL,
    deskripsi TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key untuk parent folder (self-referencing)
    CONSTRAINT fk_folders_parent_id
FOREIGN KEY
(parent_id) 
        REFERENCES folders
(id) 
        ON
DELETE CASCADE 
        ON
UPDATE CASCADE,
    
    -- Index untuk performa query
    INDEX idx_parent_id (parent_id),
    INDEX idx_urutan (urutan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========== INSERT DATA SAMPLE ==========
-- Folder Level 1 (Root Folders)
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan)
VALUES
    ('Engineering', 'Dokumen Engineering dan Design', NULL, 1),
    ('Operasi', 'Dokumen Operasional Harian', NULL, 2),
    ('Pemeliharaan', 'Dokumen Pemeliharaan dan Maintenance', NULL, 3),
    ('Business Support', 'Dokumen Business Support', NULL, 4),
    ('Keamanan', 'Dokumen Keamanan dan K3', NULL, 5),
    ('Lingkungan', 'Dokumen Lingkungan dan Sustainability', NULL, 6);

-- Folder Level 2 untuk Engineering
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan)
VALUES
    ('Program Kerja SO', 'Program Kerja System Owner', 1, 1),
    ('Design Review', 'Dokumen Design Review', 1, 2),
    ('LCCM', 'Life Cycle Cost Management', 1, 3),
    ('Peta Improvement', 'Peta Improvement Process', 1, 4);

-- Folder Level 3 untuk Program Kerja SO
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan)
VALUES
    ('Usulan', 'Folder Usulan Program Kerja', 2, 1),
    ('Laporan', 'Laporan Pelaksanaan Program Kerja', 2, 2);

-- Folder Level 4 untuk Usulan (tahun)
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan)
VALUES
    ('2024', 'Usulan Program Kerja Tahun 2024', 7, 1),
    ('2025', 'Usulan Program Kerja Tahun 2025', 7, 2),
    ('2026', 'Usulan Program Kerja Tahun 2026', 7, 3);

-- Folder Level 5 untuk 2024
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan)
VALUES
    ('1. Peralatan Alat Compersil UP Kaltimra', 'Usulan Peralatan Alat Compersil UP Kaltimra', 9, 1),
    ('2. Sistem Monitoring', 'Usulan Sistem Monitoring', 9, 2),
    ('3. Pelatihan Operator', 'Program Pelatihan Operator', 9, 3);

-- ========== QUERY UNTUK MENAMPILKAN HIERARKI ==========
-- Query 1: Lihat semua folder dengan parent
SELECT
    f.id,
    f.nama_folder,
    f.deskripsi,
    f.parent_id,
    p.nama_folder as parent_nama,
    f.urutan,
    f.created_at
FROM folders f
    LEFT JOIN folders p ON f.parent_id = p.id
ORDER BY f.parent_id, f.urutan;

-- Query 2: Lihat folder root saja
SELECT *
FROM folders
WHERE parent_id IS NULL
ORDER BY urutan;

-- Query 3: Lihat sub-folder dari Engineering (id=1)
SELECT *
FROM folders
WHERE parent_id = 1
ORDER BY urutan;

-- Query 4: Lihat full path dari suatu folder (misal id=13)
WITH RECURSIVE folder_path AS
    (
        SELECT id, nama_folder, parent_id, 1 as level
    FROM folders
    WHERE id = 13

UNION ALL

    SELECT f.id, f.nama_folder, f.parent_id, fp.level + 1
    FROM folders f
        INNER JOIN folder_path fp ON f.id = fp.parent_id
)
SELECT REVERSE(GROUP_CONCAT(nama_folder SEPARATOR ' / ')) as path
FROM folder_path
ORDER BY level DESC;

-- Query 5: Hitung jumlah sub-folder per folder
SELECT
    f.id,
    f.nama_folder,
    COUNT(c.id) as jumlah_subfolder
FROM folders f
    LEFT JOIN folders c ON c.parent_id = f.id
GROUP BY f.id, f.nama_folder
ORDER BY jumlah_subfolder DESC;

-- Query 6: Lihat folder tree (4 level kedalaman)
SELECT
    CONCAT(REPEAT('  ', (
        SELECT COUNT(*)
    FROM folders f2
    WHERE f2.id = f1.parent_id OR
        f2.id = (SELECT parent_id
        FROM folders
        WHERE id = f1.parent_id) OR
        f2.id = (SELECT parent_id
        FROM folders
        WHERE id = (SELECT parent_id
        FROM folders
        WHERE id = f1.parent_id))
    )), '├─ ', f1.nama_folder) as folder_tree,
    f1.id,
    f1.parent_id,
    f1.deskripsi
FROM folders f1
ORDER BY f1.parent_id, f1.urutan;

-- ========== UPDATE FOLDER ==========
UPDATE folders SET deskripsi = 'Updated description' WHERE id = 1;

-- ========== DELETE FOLDER ==========
-- Perhatian: Menghapus folder akan menghapus semua sub-folder (CASCADE)
DELETE FROM folders WHERE id = 13;

-- ========== STATISTICS ==========
SELECT
    COUNT(*) as total_folders,
    COUNT(DISTINCT parent_id) as jumlah_parent_folder,
    MAX((SELECT COUNT(*)
    FROM folders f2
    WHERE f2.parent_id = folders.id)) as max_subfolder
FROM folders;
