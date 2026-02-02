<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs - {{ $folder->nama_folder }}</title>
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
                        
                        <div class="submenu-item" onclick="window.location.href='{{ route('engineering.index') }}'">▸ Engineering</div>
                        <div class="submenu-item" data-menu="laporan">▸ Operasi</div>
                        <div class="submenu-item" data-menu="program">▸ Pemeliharaan</div>
                        <div class="submenu-item" data-menu="lccm">▸ Business Support</div>
                        <div class="submenu-item" data-menu="design">▸ Keamanan</div>
                        <div class="submenu-item" data-menu="peta">▸ Lingkungan</div>
                    </div>
                    <div class="submenu-item" onclick="window.location.href='{{ route('folder.index') }}'"> Folder</div>
                    <div class="submenu-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'"> Data Mesin</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Breadcrumb -->
                <div class="breadcrumb-section">
                    <a href="{{ route('folder.index') }}">📁 Folder</a>
                    @foreach($parentFolders as $parent)
                        / <a href="{{ route('folder.show', $parent->id) }}">{{ $parent->nama_folder }}</a>
                    @endforeach
                </div>

                <!-- Header -->
                <div class="engineering-page-header">
                    <div class="engineering-page-title">
                        <div style="font-size: 28px; font-weight: bold; color: #333333; margin-bottom: 20px;">
                            📁 {{ $folder->nama_folder }}
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('folder.create', ['parent_id' => $folder->id]) }}" class="btn-tambah-data">
                                ➕ Tambah Subfolder
                            </a>
                            <a href="{{ route('folder.edit', $folder->id) }}" class="btn-edit-main">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('folder.destroy', $folder->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus folder ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-main">🗑️ Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Folder Description -->
                @if($folder->deskripsi)
                    <div class="folder-description">
                        <p>{{ $folder->deskripsi }}</p>
                    </div>
                @endif

                <!-- Flash Messages -->
                @if ($message = Session::get('success'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-success">
                            ✓ {{ $message }}
                        </div>
                    </div>
                @endif

                <!-- Subfolders List -->
                <div class="subfolder-container">
                    <h3 style="margin: 20px 30px; color: #333;">Subfolder</h3>

                    @if($childFolders->isEmpty())
                        <div class="empty-state" style="margin-left: 30px;">
                            <p>📭 Tidak ada subfolder. <a href="{{ route('folder.create', ['parent_id' => $folder->id]) }}">Buat subfolder</a></p>
                        </div>
                    @else
                        <div class="folder-grid">
                            @foreach($childFolders as $child)
                                <div class="folder-card">
                                    <div class="folder-header">
                                        <h3 class="folder-name">
                                            <a href="{{ route('folder.show', $child->id) }}" class="folder-link">
                                                📁 {{ $child->nama_folder }}
                                            </a>
                                        </h3>
                                        <div class="folder-actions">
                                            <a href="{{ route('folder.edit', $child->id) }}" class="btn-action btn-edit" title="Edit">✏️</a>
                                            <a href="{{ route('folder.create', ['parent_id' => $child->id]) }}" class="btn-action btn-add" title="Tambah subfolder">➕</a>
                                            <form action="{{ route('folder.destroy', $child->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus folder ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete" title="Hapus">🗑️</button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="folder-desc">{{ $child->deskripsi ?? '-' }}</p>
                                    <div class="folder-info">
                                        <span class="subfolder-count">📂 {{ $child->children->count() }} subfolder</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
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
