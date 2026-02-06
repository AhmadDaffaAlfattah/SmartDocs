<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartDocs')</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/engineering.css') }}">
    <link rel="stylesheet" href="{{ asset('css/folder.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    
    <!-- Stack for additional CSS -->
    @stack('styles')

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <style>
        /* FullCalendar Customization */
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .fc-header-toolbar {
            font-size: 14px;
        }
        .fc-button {
            background-color: #0066cc !important;
            border-color: #0066cc !important;
        }
        .fc-daygrid-day-number {
            color: #333;
            text-decoration: none;
        }
        .fc-col-header-cell-cushion {
            color: #555;
            text-decoration: none;
        }


    /* Global Table Action Styles */
        .table tbody td.action {
            display: flex; /* Ensure flex behavior */
            align-items: center; /* Vertically align */
            justify-content: flex-end !important; /* Align to right */
            white-space: nowrap; 
            width: auto !important; /* Allow column to expand */
            padding-right: 50px !important; /* Space from right edge */
            padding-left: 10px !important;
        }
        .table tbody td.action a, 
        .table tbody td.action button,
        .table tbody td.action form {
            display: inline-block; 
            margin-left: 5px; /* Spacing between buttons */
            margin-right: 0;
        }
        .table td.action form {
            margin-right: 0;
        }


        /* FORCE PROFILE MENU STYLES (Inline to fix caching) */
        .profile-menu {
            display: none; 
            position: absolute; 
            right: 0; 
            top: 60px; 
            background-color: #b0b0b0 !important; /* Grey Card */
            min-width: 250px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important; 
            border-radius: 12px !important; 
            z-index: 99999; 
            padding: 20px; 
            border: 1px solid #333 !important;
        }
        .profile-menu.active {
            display: block !important;
        }
        .profile-header { 
            padding-bottom: 10px; 
            text-align: center; 
            border-bottom: none !important; 
            background-color: transparent !important; 
        }
        .profile-menu-icon-container {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        .profile-menu-icon {
            width: 80px !important; /* Larger icon per design */
            height: 80px !important;
            border-radius: 50%;
            border: 2px solid #333;
            background-color: #fff;
        }
        .profile-name { 
            font-weight: 800 !important; 
            color: #000 !important; 
            font-size: 14px !important; 
            margin-bottom: 5px; 
            text-transform: uppercase;
            text-align: center;
        }
        .profile-item { 
            padding: 5px 0 !important; 
            display: block !important; 
            color: #000 !important; 
            text-decoration: none !important; 
            transition: all 0.2s ease; 
            font-size: 14px !important; 
            font-weight: 500 !important; 
            text-align: left !important;
            background-color: transparent !important; /* Remove any hover bg */
        }
        .profile-item:hover { 
            text-decoration: underline !important;
            background-color: transparent !important; 
        }
        /* Hide old icons if present in HTML logic */
        .profile-item-icon { display: none !important; }
        .profile-divider { display: none !important; }

    /* Global Confirmation Modal Styles */
        .confirm-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .confirm-modal-overlay.active {
            display: flex;
            opacity: 1;
        }
        
        .confirm-modal {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .confirm-modal-overlay.active .confirm-modal {
            transform: scale(1);
        }
        
        .confirm-modal-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }
        
        .confirm-modal-icon.warning {
            background-color: #fff4e5;
            color: #ff9800;
        }
        
        .confirm-modal-icon.danger {
            background-color: #fee2e2;
            color: #ef4444;
        }
        
        .confirm-modal h3 {
            margin: 0 0 10px;
            color: #333;
            font-size: 22px;
            font-weight: 700;
        }
        
        .confirm-modal p {
            margin: 0 0 25px;
            color: #666;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .confirm-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        
        .confirm-btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            flex: 1;
        }
        
        .confirm-btn-cancel {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        
        .confirm-btn-cancel:hover {
            background-color: #e5e7eb;
        }
        
        .confirm-btn-confirm {
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .confirm-btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .confirm-btn-confirm.danger {
            background-color: #ef4444;
        }
        
        .confirm-btn-confirm.danger:hover {
            background-color: #dc2626;
        }

        .confirm-btn-confirm.warning {
            background-color: #f59e0b;
        }
        
        .confirm-btn-confirm.warning:hover {
            background-color: #d97706;
        }
    </style>
    </style>
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
                            <div class="profile-menu-icon-container">
                                <img src="{{ asset('images/akun.png') }}" alt="User" class="profile-menu-icon">
                            </div>
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
                        <a href="{{ route('logout') }}" class="profile-item" onclick="handleLogout(event)">
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
                    <div class="submenu" id="submenu-document" style="display: {{ (str_contains(Route::currentRouteName(), 'engineering') || str_contains(Route::currentRouteName(), 'operasi') || str_contains(Route::currentRouteName(), 'pemeliharaan') || str_contains(Route::currentRouteName(), 'business-support') || str_contains(Route::currentRouteName(), 'keamanan') || str_contains(Route::currentRouteName(), 'lingkungan')) ? 'block' : 'none' }}">
                        @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'engineering')
                            <div class="submenu-item {{ Route::is('engineering.*') ? 'active' : '' }}" onclick="window.location.href='{{ route('engineering.index') }}'">▸ Engineering</div>
                        @endif
                        @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'operasi')
                            <div class="submenu-item {{ Route::is('operasi.*') ? 'active' : '' }}" onclick="window.location.href='{{ route('operasi.index') }}'">▸ Operasi</div>
                        @endif
                        @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'pemeliharaan')
                            <div class="submenu-item {{ Route::is('pemeliharaan.*') ? 'active' : '' }}" onclick="window.location.href='{{ route('pemeliharaan.index') }}'">▸ Pemeliharaan</div>
                        @endif
                        @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'business support')
                            <div class="submenu-item {{ Route::is('business-support.*') ? 'active' : '' }}" onclick="window.location.href='{{ route('business-support.index') }}'">▸ Business Support</div>
                        @endif
                        @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'keamanan')
                            <div class="submenu-item {{ Route::is('keamanan.*') ? 'active' : '' }}" onclick="window.location.href='{{ route('keamanan.index') }}'">▸ Keamanan</div>
                        @endif
                        @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'lingkungan')
                            <div class="submenu-item {{ Route::is('lingkungan.*') ? 'active' : '' }}" onclick="window.location.href='{{ route('lingkungan.index') }}'">▸ Lingkungan</div>
                        @endif
                    </div>
                    
                    @if(Auth::user()->role !== 'user')
                    <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'">Folder</div>
                    @endif
                    
                    @if(Auth::user()->role === 'super_admin' || strtolower(Auth::user()->bidang ?? '') === 'mesin')
                        <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'">Data Mesin</div>
                    @endif
                    
                    @if(Auth::user()->role === 'super_admin')
                        <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                @if(session('success'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-success">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-error">{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>



    <!-- Global Confirmation Modal -->
    <div id="globalConfirmModal" class="confirm-modal-overlay">
        <div class="confirm-modal">
            <div id="confirmIcon" class="confirm-modal-icon warning">
                ⚠️
            </div>
            <h3 id="confirmTitle">Konfirmasi</h3>
            <p id="confirmMessage">Apakah Anda yakin?</p>
            <div class="confirm-modal-actions">
                <button type="button" class="confirm-btn confirm-btn-cancel" onclick="closeConfirmModal()">Batal</button>
                <button type="button" id="confirmActionBtn" class="confirm-btn confirm-btn-confirm danger">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Use Global Helper for Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Profile Menu
             window.toggleProfileMenu = function(event) {
                event.stopPropagation();
                const menu = document.getElementById('profileMenu');
                menu.classList.toggle('active');
            }

            document.addEventListener('click', function(event) {
                const profileDropdown = document.querySelector('.profile-dropdown');
                const profileMenu = document.getElementById('profileMenu');
                if (profileDropdown && !profileDropdown.contains(event.target)) {
                    if (profileMenu) profileMenu.classList.remove('active');
                }
            });

            // Document Menu Toggle
            const docMenu = document.getElementById('document-menu');
            if (docMenu) {
                docMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('open');
                    const submenu = document.getElementById('submenu-document');
                    if (submenu) {
                        const isHidden = submenu.style.display === 'none' || getComputedStyle(submenu).display === 'none';
                        submenu.style.display = isHidden ? 'block' : 'none';
                        this.querySelector('.toggle-icon').textContent = isHidden ? '▲' : '▼';
                    }
                });
            }
            // Global Confirmation Modal Logic
            let confirmCallback = null;
            
            window.showConfirmModal = function({ title, message, type = 'warning', confirmText = 'Ya', cancelText = 'Batal', onConfirm }) {
                const modal = document.getElementById('globalConfirmModal');
                const icon = document.getElementById('confirmIcon');
                const titleEl = document.getElementById('confirmTitle');
                const msgEl = document.getElementById('confirmMessage');
                const confirmBtn = document.getElementById('confirmActionBtn');
                
                titleEl.textContent = title;
                msgEl.textContent = message;
                confirmBtn.textContent = confirmText;
                
                // Set styles based on type
                icon.className = `confirm-modal-icon ${type}`;
                confirmBtn.className = `confirm-btn confirm-btn-confirm ${type}`;
                
                // Set icon content
                icon.textContent = type === 'danger' ? '🗑️' : '🚪';
                if(type === 'warning') icon.textContent = '🚪'; // Logout icon
                
                confirmCallback = onConfirm;
                
                modal.classList.add('active');
            };
            
            window.closeConfirmModal = function() {
                const modal = document.getElementById('globalConfirmModal');
                modal.classList.remove('active');
                confirmCallback = null;
            };
            
            document.getElementById('confirmActionBtn').addEventListener('click', function() {
                if (confirmCallback) confirmCallback();
                closeConfirmModal();
            });
            
            // Close on click outside
            document.getElementById('globalConfirmModal').addEventListener('click', function(e) {
                if(e.target === this) closeConfirmModal();
            });

            // Logout Handler with Custom Modal
            window.handleLogout = function(event) {
                event.preventDefault();
                event.stopPropagation();
                
                // Hide profile menu
                const menu = document.getElementById('profileMenu');
                if(menu && menu.classList.contains('active')) menu.classList.remove('active');
                
                showConfirmModal({
                    title: 'Konfirmasi Logout',
                    message: 'Apakah Anda yakin ingin keluar dari aplikasi?',
                    type: 'warning',
                    confirmText: 'Ya, Logout',
                    onConfirm: () => {
                        document.getElementById('logout-form').submit();
                    }
                });
            }
            
            // Expose global delete confirmation helper for usage in Index pages
            window.confirmDelete = function(formId) {
                showConfirmModal({
                    title: 'Hapus Data',
                    message: 'Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.',
                    type: 'danger',
                    confirmText: 'Ya, Hapus',
                    onConfirm: () => {
                        document.getElementById(formId).submit();
                    }
                });
                return false;
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
