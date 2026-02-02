<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs - Tambah Dokumen</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/engineering.css') }}">
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
                <div class="profile-dropdown">
                    <img src="{{ asset('images/akun.png') }}" alt="Profile" class="profile-icon" onclick="event.stopPropagation(); toggleProfileMenu(event)">
                    <div class="profile-menu" id="profileMenu">
                        <div class="profile-header">
                            @auth
                                <div class="profile-name">{{ strtoupper(Auth::user()->name) }}</div>
                            @else
                                <div class="profile-name">GUEST</div>
                            @endauth
                        </div>
                        <div class="profile-divider"></div>
                        <a href="{{ route('change-password') }}" class="profile-item" onclick="event.stopPropagation();">
                            <span class="profile-item-icon">🔑</span>
                            <span class="profile-item-text">Change Password</span>
                        </a>
                        <a href="{{ route('logout') }}" class="profile-item" onclick="event.stopPropagation(); event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="profile-item-icon">🚪</span>
                            <span class="profile-item-text">Logout</span>
                        </a>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
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
                        <div class="submenu-item" onclick="window.location.href='{{ route('engineering.index') }}'">▸ Engineering</div>
                        <div class="submenu-item" onclick="navigateToBidang('Operasi')">▸ Operasi</div>
                        <div class="submenu-item" onclick="navigateToBidang('Pemeliharaan')">▸ Pemeliharaan</div>
                        <div class="submenu-item" onclick="navigateToBidang('Business Support')">▸ Business Support</div>
                        <div class="submenu-item" onclick="navigateToBidang('Keamanan')">▸ Keamanan</div>
                        <div class="submenu-item" onclick="navigateToBidang('Lingkungan')">▸ Lingkungan</div>
                    </div>

                    <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'"> Folder</div>

                    <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'"> Data Mesin</div>

                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'"> Account</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Engineering Header -->
                <div class="engineering-page-header">
                    <div class="engineering-page-title">
                        <div style="font-size: 28px; font-weight: bold; color: #333;">
                            Document <span style="font-weight: bold;">» Tambah Dokumen</span>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div style="background: white; margin: 30px; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 800px;">
                    <form action="{{ route('pemeliharaan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Judul -->
                        <div style="margin-bottom: 20px;">
                            <label for="judul" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Judul <span style="color: red;">*</span></label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" 
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; @error('judul') border-color: red; @enderror"
                                   placeholder="Masukkan judul dokumen">
                            @error('judul')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori (Folder) -->
                        <div style="margin-bottom: 20px;">
                            <label for="folder_id" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Kategori <span style="color: red;">*</span></label>
                            <div id="folderTree" style="border: 1px solid #999; border-radius: 4px; max-height: 300px; overflow-y: auto; background: white;"></div>
                            <input type="hidden" id="folder_id" name="folder_id" value="">
                            @error('folder_id')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div style="margin-bottom: 20px;">
                            <label for="file" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Upload File</label>
                            <input type="file" id="file" name="file" 
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; @error('file') border-color: red; @enderror">
                            <p style="color: #666; font-size: 12px; margin-top: 5px;">Format: Excel, PDF, Word, dll | Ukuran maksimal: 10MB</p>
                            @error('file')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- OR -->
                        <div style="margin-bottom: 20px; text-align: center; color: #999; font-size: 12px; font-weight: 600;">
                            ATAU
                        </div>

                        <!-- Link -->
                        <div style="margin-bottom: 20px;">
                            <label for="link" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Masukkan Link</label>
                            <input type="url" id="link" name="link" value="{{ old('link') }}"
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; @error('link') border-color: red; @enderror"
                                   placeholder="https://docs.google.com/... atau URL lainnya">
                            <p style="color: #666; font-size: 12px; margin-top: 5px;">Masukkan URL lengkap (contoh: https://docs.google.com/spreadsheets/...)</p>
                            @error('link')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 12px; margin-top: 30px;">
                            <button type="submit" style="background-color: #333; color: white; border: none; padding: 10px 24px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">
                                Simpan
                            </button>
                            <a href="{{ route('pemeliharaan.index') }}" 
                               style="background-color: #999; color: white; border: none; padding: 10px 24px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-block;">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <style>
        .folder-tree-item {
            padding: 10px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            user-select: none;
        }

        .folder-tree-item:hover {
            background-color: #f0f0f0;
        }

        .folder-tree-item.selected {
            background-color: #0066cc;
            color: white;
        }

        .profile-dropdown {
            position: relative;
            cursor: pointer;
        }

        .profile-icon {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .profile-icon:hover {
            transform: scale(1.1);
        }

        .profile-menu {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            z-index: 1000;
            overflow: hidden;
        }

        .profile-menu.active {
            display: block;
        }

        .profile-header {
            padding: 16px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #e0e0e0;
        }

        .profile-name {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-align: center;
        }

        .profile-divider {
            height: 1px;
            background-color: #e0e0e0;
        }

        .profile-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }

        .profile-item:last-child {
            border-bottom: none;
        }

        .profile-item:hover {
            background-color: #f9f9f9;
        }

        .profile-item-icon {
            font-size: 16px;
        }

        .profile-item-text {
            font-size: 13px;
            color: #333;
        }
    </style>

    <script>
        // Render folder tree untuk create page
        const folderTreeData = {!! json_encode($folderTree) !!};
        
        function renderFolderTree(folderTree, selectedFolderId = null) {
            const renderFolder = (folder, level = 0) => {
                const indent = (level * 16) + 'px';
                const isSelected = folder.id == selectedFolderId ? 'selected' : '';
                const hasChildren = folder.children && folder.children.length > 0;
                let html = `<div class="folder-tree-item ${isSelected}" data-folder-id="${folder.id}" style="padding-left: ${indent}">
                    📁 ${folder.nama_folder}
                </div>`;
                
                if (hasChildren) {
                    folder.children.forEach(child => {
                        html += renderFolder(child, level + 1);
                    });
                }
                return html;
            };
            
            let html = '';
            folderTree.forEach(folder => {
                html += renderFolder(folder);
            });
            return html || '<p style="color: #999; padding: 10px;">Tidak ada folder</p>';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Render folder tree
            const folderTreeHtml = renderFolderTree(folderTreeData);
            document.getElementById('folderTree').innerHTML = folderTreeHtml;
            
            // Add event listeners for folder selection
            document.querySelectorAll('.folder-tree-item').forEach(item => {
                item.addEventListener('click', function() {
                    const folderId = this.dataset.folderId;
                    document.getElementById('folder_id').value = folderId;
                    
                    // Update visual selection
                    document.querySelectorAll('.folder-tree-item').forEach(i => i.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        });

        function navigateToBidang(bidang) {
            // Redirect ke engineering page dengan bidang sebagai filter
            window.location.href = '{{ route('engineering.index') }}?bidang=' + encodeURIComponent(bidang);
        }

        function toggleProfileMenu(event) {
            if (event) {
                event.stopPropagation();
            }
            const menu = document.getElementById('profileMenu');
            if (menu) {
                menu.classList.toggle('active');
            }
        }

        document.addEventListener('click', function(event) {
            const profileDropdown = document.querySelector('.profile-dropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                const sidebar = document.querySelector('.landing-sidebar');
                if (!sidebar || !sidebar.contains(event.target)) {
                    if (profileMenu) {
                        profileMenu.classList.remove('active');
                    }
                }
            }
        });

        // Collapsible menu
        const documentMenu = document.getElementById('document-menu');
        if (documentMenu) {
            documentMenu.addEventListener('click', function() {
                const submenu = document.getElementById('submenu-document');
                submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
