<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs - Folder</title>
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

<div class="submenu-item" onclick="window.location.href='{{ route('folder.index') }}'"> Folder</div>
                    <div class="submenu-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'"> Data Mesin</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>                    
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Header -->
                <div class="engineering-page-header">
                    <div class="engineering-page-title">
                        <div style="font-size: 28px; font-weight: bold; color: #333333; margin-bottom: 20px;">
                            Folder
                        </div>
                        <a href="{{ route('folder.create') }}" class="btn-tambah-data">
                            ➕ Tambah Folder
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if ($message = Session::get('success'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-success">
                            ✓ {{ $message }}
                        </div>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-error">
                            ✕ {{ $message }}
                        </div>
                    </div>
                @endif

                <!-- Folder Tree -->
                <div class="folder-tree-container">
                    @if($folders->isEmpty())
                        <div class="empty-state">
                            <p>📁 Belum ada folder. <a href="{{ route('folder.create') }}">Buat folder pertama Anda</a></p>
                        </div>
                    @else
                        <div class="folder-tree">
                            @foreach($folders as $folder)
                                @include('components.folder-tree-display', ['folder' => $folder, 'level' => 0])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle folder expand/collapse
        document.addEventListener('click', function(e) {
            if (e.target.closest('.folder-toggle-btn')) {
                const btn = e.target.closest('.folder-toggle-btn');
                const children = btn.closest('.folder-tree-item').querySelector('.folder-children');
                
                if (children) {
                    const isHidden = children.style.display === 'none';
                    children.style.display = isHidden ? 'block' : 'none';
                    btn.textContent = isHidden ? '▼' : '▶';
                }
                e.stopPropagation();
            }
        });

        // Toggle document menu
        document.getElementById('document-menu')?.addEventListener('click', function() {
            const submenu = document.getElementById('submenu-document');
            const toggle = this.querySelector('.toggle-icon');
            
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
            toggle.textContent = submenu.style.display === 'none' ? '▶' : '▼';
        });

        // Delete folder
        function deleteFolder(id) {
            const folderItem = event.target.closest('.folder-tree-item');
            const childrenDiv = folderItem.querySelector('.folder-children');
            const hasChildren = childrenDiv && childrenDiv.querySelector('.folder-tree-item');
            
            let confirmMessage = 'Yakin ingin menghapus folder ini?';
            if (hasChildren) {
                confirmMessage = 'Folder ini memiliki subfolder. Yakin ingin menghapus folder dan semua subfolder?';
            }
            
            if (confirm(confirmMessage)) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                fetch(`/folder/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Delete failed');
                    }
                })
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Fade out and reload
                        folderItem.style.opacity = '0';
                        folderItem.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menghapus folder! Silakan coba lagi.');
                });
            }
        }

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

    <style>
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
</body>
</html>
