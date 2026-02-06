<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs - Change Password</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
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
                    <div class="submenu" id="submenu-document">
                        <div class="submenu-item" onclick="window.location.href='{{ route('engineering.index') }}'">▸ Engineering</div>
                        <div class="submenu-item" onclick="navigateToBidang('Operasi')">▸ Operasi</div>
                        <div class="submenu-item" onclick="navigateToBidang('Pemeliharaan')">▸ Pemeliharaan</div>
                        <div class="submenu-item" onclick="navigateToBidang('Business Support')">▸ Business Support</div>
                        <div class="submenu-item" onclick="navigateToBidang('Keamanan')">▸ Keamanan</div>
                        <div class="submenu-item" onclick="navigateToBidang('Lingkungan')">▸ Lingkungan</div>
                    </div>

                    <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'">Folder</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Change Password Header -->
                <div class="engineering-page-header">
                    <div class="engineering-page-title">
                        <div style="font-size: 28px; font-weight: bold; color: #333;">
                            <span style="font-weight: bold;">🔑 Change Password</span>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if ($message = Session::get('success'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div style="background-color: #d4edda; color: #155724; padding: 12px 16px; border-radius: 4px; border: 1px solid #c3e6cb; font-size: 13px;">
                            {{ $message }}
                        </div>
                    </div>
                @endif

                <!-- Form Container -->
                <div style="background: white; margin: 30px; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 500px;">
                    <form action="{{ route('change-password') }}" method="POST">
                        @csrf

                        <!-- Current Password -->
                        <div style="margin-bottom: 20px;">
                            <label for="current_password" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Current Password <span style="color: red;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" id="current_password" name="current_password" 
                                       style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; @error('current_password') border-color: red; @enderror"
                                       placeholder="Masukkan password saat ini" required autocomplete="current-password"
                                       value="{{ Auth::user()->password }}">
                                <span onclick="togglePassword('current_password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                                    <img src="https://cdn-icons-png.flaticon.com/128/2355/2355322.png" alt="Show" width="20" style="opacity: 0.6;">
                                </span>
                            </div>
                            @error('current_password')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div style="margin-bottom: 20px;">
                            <label for="new_password" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">New Password <span style="color: red;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" id="new_password" name="new_password" 
                                       style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; @error('new_password') border-color: red; @enderror"
                                       placeholder="Masukkan password baru (minimal 6 karakter)" required autocomplete="new-password">
                                <span onclick="togglePassword('new_password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                                    <img src="https://cdn-icons-png.flaticon.com/128/2355/2355322.png" alt="Show" width="20" style="opacity: 0.6;">
                                </span>
                            </div>
                            @error('new_password')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div style="margin-bottom: 20px;">
                            <label for="new_password_confirmation" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Confirm Password <span style="color: red;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                       style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box;"
                                       placeholder="Konfirmasi password baru" required autocomplete="new-password">
                                <span onclick="togglePassword('new_password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                                    <img src="https://cdn-icons-png.flaticon.com/128/2355/2355322.png" alt="Show" width="20" style="opacity: 0.6;">
                                </span>
                            </div>
                            @error('new_password_confirmation')
                                <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 12px; margin-top: 30px;">
                            <button type="submit" style="background-color: #333; color: white; border: none; padding: 10px 24px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">
                                Update Password
                            </button>
                            <a href="{{ route('landing') }}" 
                               style="background-color: #999; color: white; border: none; padding: 10px 24px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-block;">
                                Batal
                            </a>
                        </div>
                    </form>

                    <script>
                        function togglePassword(fieldId, iconSpan) {
                            const field = document.getElementById(fieldId);
                            const img = iconSpan.querySelector('img');
                            if (field.type === "password") {
                                field.type = "text";
                                // Change to 'hide' icon if desired, or just keep same
                                // img.src = "..."; 
                            } else {
                                field.type = "password";
                            }
                        }
                    </script>
                </div>

            </div>
        </div>
    </div>

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
    
    <script>
        function navigateToBidang(bidang) {
            // Redirect ke engineering page dengan bidang sebagai filter
            window.location.href = '{{ route('engineering.index') }}?bidang=' + encodeURIComponent(bidang);
        }

        function toggleProfileMenu(event) {
            if (event) {
                event.stopPropagation();
            }
            const menu = document.getElementById('profileMenu');
            menu.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const profileDropdown = document.querySelector('.profile-dropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                const sidebar = document.querySelector('.landing-sidebar');
                if (!sidebar || !sidebar.contains(event.target)) {
                    profileMenu.classList.remove('active');
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
    </script>
</body>
</html>
