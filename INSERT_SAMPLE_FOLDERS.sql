-- Insert sample folder data according to mockup
-- First, clear existing data
TRUNCATE TABLE folders;

-- Root folder: Engineering
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('Engineering', 'Dokumen Engineering', NULL, 1, NOW(), NOW());

SET @eng_id = LAST_INSERT_ID();

-- Level 2: Program Kerja SO (first)
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('Program Kerja SO', 'Program Kerja SO', @eng_id, 1, NOW(), NOW());

SET @prog1_id = LAST_INSERT_ID();

-- Level 2: Program Kerja SO (second)
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('Program Kerja SO', 'Program Kerja SO', @eng_id, 2, NOW(), NOW());

-- Level 3: Usulan
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('Usulan', 'Usulan', @prog1_id, 1, NOW(), NOW());

SET @usulan_id = LAST_INSERT_ID();

-- Level 4: 2024
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('2024', 'Tahun 2024', @usulan_id, 1, NOW(), NOW());

SET @tahun2024_id = LAST_INSERT_ID();

-- Level 4: 2025
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('2025', 'Tahun 2025', @usulan_id, 2, NOW(), NOW());

SET @tahun2025_id = LAST_INSERT_ID();

-- Level 4: 2026
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('2026', 'Tahun 2026', @usulan_id, 3, NOW(), NOW());

SET @tahun2026_id = LAST_INSERT_ID();

-- Level 5: Peralatan under 2024
INSERT INTO folders
    (nama_folder, deskripsi, parent_id, urutan, created_at, updated_at)
VALUES
    ('1. Peralatan Alat Compersil UP Kaltimra', 'Peralatan Alat Compersil UP Kaltimra', @tahun2024_id, 1, NOW(), NOW());
