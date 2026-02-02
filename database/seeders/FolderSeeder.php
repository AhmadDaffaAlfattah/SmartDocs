<?php

namespace Database\Seeders;

use App\Models\Folder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Engineering parent folder
        $engineeringParent = Folder::create([
            'nama_folder' => 'Engineering',
            'deskripsi' => 'Folder untuk dokumen engineering',
            'parent_id' => null,
            'urutan' => 1,
        ]);

        // Create sub-folders under Engineering
        $subFolders = [
            ['nama_folder' => 'Worksheet System Owner', 'urutan' => 1],
            ['nama_folder' => 'Laporan Lintas Bidang', 'urutan' => 2],
            ['nama_folder' => 'Program Kerja SO', 'urutan' => 3],
            ['nama_folder' => 'LCCM', 'urutan' => 4],
            ['nama_folder' => 'Design Review', 'urutan' => 5],
            ['nama_folder' => 'Peta Improvement', 'urutan' => 6],
            ['nama_folder' => 'ECP', 'urutan' => 7],
            ['nama_folder' => 'PKU', 'urutan' => 8],
            ['nama_folder' => 'RCFA', 'urutan' => 9],
            ['nama_folder' => 'RJPU', 'urutan' => 10],
            ['nama_folder' => 'MPI', 'urutan' => 11],
            ['nama_folder' => 'MATERI', 'urutan' => 12],
        ];

        foreach ($subFolders as $folder) {
            Folder::create([
                'nama_folder' => $folder['nama_folder'],
                'deskripsi' => null,
                'parent_id' => $engineeringParent->id,
                'urutan' => $folder['urutan'],
            ]);
        }
    }
}
