<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs - Edit Folder</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/engineering.css') }}">
    <link rel="stylesheet" href="{{ asset('css/folder.css') }}">
</head>
<body>
    <div class="landing-container">
        <!-- Header -->
        <div class="landing-header">
            <div class="header-left">
                <img src="{{ asset('images/logo_pln.png') }}" alt="Logo Aplikasi" class="logo-aplikasi">
            </div>
            <div class="header-center">
                <!-- Spacer -->
            </div>
            <div class="header-right">
                <img src="{{ asset('images/akun.png') }}" alt="Profile" class="profile-icon">
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <div class="landing-wrapper">
            <!-- Sidebar -->
            <div class="landing-sidebar">
                <div class="sidebar-items">
                    <div class="sidebar-item" onclick="window.location.href='/'">Dashboard</div>

                    <div class="sidebar-item collapsible" id="document-menu">
                        <span class="toggle-icon">▼</span>
                        <span class="menu-text">Document</span>
                    </div>
                    <div class="submenu" id="submenu-document">
                        <div class="submenu-item" onclick="window.location.href='{{ route('folder.index') }}'">📁 Folder</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('engineering.index') }}'">▸ Engineering</div>
                        <div class="submenu-item" data-menu="laporan">▸ Operasi</div>
                        <div class="submenu-item" data-menu="program">▸ Pemeliharaan</div>
                        <div class="submenu-item" data-menu="lccm">▸ Business Support</div>
                        <div class="submenu-item" data-menu="design">▸ Keamanan</div>
                        <div class="submenu-item" data-menu="peta">▸ Lingkungan</div>
                    </div>

                    <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'">Folder</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'">Data Mesin</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Header -->
                <div class="engineering-page-header">
                    <div class="engineering-page-title">
                        <div style="font-size: 28px; font-weight: bold; color: #333333; margin-bottom: 20px;">
                            ✏️ Edit Folder
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="form-container">
                    <form action="{{ route('folder.update', $folder->id) }}" method="POST" class="form-group">
                        @csrf
                        @method('PUT')

                        <div class="form-field">
                            <label for="nama_folder">Nama Folder <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="nama_folder" 
                                name="nama_folder" 
                                class="form-input @error('nama_folder') is-invalid @enderror"
                                placeholder="Masukkan nama folder"
                                value="{{ old('nama_folder', $folder->nama_folder) }}"
                                required
                            >
                            @error('nama_folder')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="form-field">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea 
                                id="deskripsi" 
                                name="deskripsi" 
                                class="form-textarea @error('deskripsi') is-invalid @enderror"
                                placeholder="Masukkan deskripsi folder (opsional)"
                                rows="4"
                            >{{ old('deskripsi', $folder->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="form-actions">
                            <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
                            <a href="{{ route('folder.index') }}" class="btn-cancel">❌ Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle submenu
        document.getElementById('document-menu').addEventListener('click', function() {
            const submenu = document.getElementById('submenu-document');
            const toggle = this.querySelector('.toggle-icon');
            
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
            toggle.textContent = submenu.style.display === 'none' ? '▶' : '▼';
        });
    </script>
</body>
</html>
