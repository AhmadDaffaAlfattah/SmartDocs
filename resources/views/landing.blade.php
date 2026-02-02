<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
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
                        <div class="submenu-item" onclick="window.location.href='{{ route('operasi.index') }}'">▸ Operasi</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('pemeliharaan.index') }}'">▸ Pemeliharaan</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('business-support.index') }}'">▸ Business Support</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('keamanan.index') }}'">▸ Keamanan</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('lingkungan.index') }}'">▸ Lingkungan</div>
                    </div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'">Folder</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'">Data Mesin</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Title Section -->
                <div class="title-section">
                    <div class="pln-logo-container">
                        <img src="{{ asset('images/smartdocs.png') }}" alt="SmartDocs Logo" class="pln-logo-main">
                        <div class="pln-text">
                            <div class="pln-title"></div>
                            {{-- <div class="dori-title">SmartDocs</div> --}}
                        </div>
                    </div>
                    {{-- <div class="dori-title">SmartDocs</div>
                    <div class="dori-subtitle">DOKUMEN TERINTEGRASI</div> --}}
                </div>

                <!-- Stats Section -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon"><a href="https://www.flaticon.com/free-icon/team_476761" class="view link-icon-detail related-icon" title="Team" data-id="476761" data-src="?term=user&amp;page=2&amp;position=10&amp;origin=search">
    <img src="https://cdn-icons-png.flaticon.com/128/476/476761.png" data-src="https://cdn-icons-png.flaticon.com/128/476/476761.png" alt="Team" title="Team" width="32" height="32" class="lzy lazyload--done" srcset="https://cdn-icons-png.flaticon.com/128/476/476761.png 4x">
</a></div>
                        <div class="stat-content">
                            <div class="stat-label">Total User</div>
                            <div class="stat-value">30</div>
                        </div>
                    </div>
                    <div class="stat-card">
                         <div class="stat-icon"><a class="link-icon-detail active related-icon" href="https://www.flaticon.com/free-icon/google-docs_2991108" data-id="2991108" data-color="2" data-premium="0" data-selection="1" data-team_id="267" data-pack_id="2991107" data-pack_slug="packs/google-suite-18" data-src="?related_id=2991108&amp;origin=search">
          <img src="https://cdn-icons-png.flaticon.com/128/2991/2991108.png" srcset="https://cdn-icons-png.flaticon.com/128/2991/2991108.png 4x" alt="Google docs" width="32" height="32">
        </a></div>
                        <div class="stat-content">
                            <div class="stat-label">Total Dokumen</div>
                            <div class="stat-value">150</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><a href="https://www.flaticon.com/free-icon/teamwork_4859784" class="view link-icon-detail" title="Teamwork" data-id="4859784" data-src="?term=work&amp;page=1&amp;position=23&amp;origin=search">
  <img src="https://cdn-icons-png.flaticon.com/128/4859/4859784.png" loading="lazy" alt="Teamwork " title="Teamwork " width="32" height="32">
</a></div>
                        <div class="stat-content">
                            <div class="stat-label">Total Bidang</div>
                            <div class="stat-value">6</div>
                        </div>
                    </div>
                </div>

                <!-- Kalender Section -->
                <div class="calendar-section-landing">
                    <div>
                        <div class="calendar-header">
                            <div class="calendar-navigation">
                                <button id="prevBtn" class="nav-btn">← Sebelumnya</button>
                                <h2 id="monthYear"></h2>
                                <button id="nextBtn" class="nav-btn">Berikutnya →</button>
                            </div>

                            <div class="year-selector">
                                <label for="yearSelect">Tahun:</label>
                                <select id="yearSelect">
    <?php
    for ($i = 1900; $i <= 3000; $i++) {
        // Cek jika tahun $i adalah tahun sekarang, tambahkan atribut 'selected'
        $selected = ($i == date('Y')) ? 'selected' : '';
        echo "<option value='$i' $selected>$i</option>";
    }
    ?>
</select>
                            </div>
                        </div>

                        <div class="calendar-wrapper">
                            <table class="calendar">
                                <thead>
                                    <tr>
                                        <th>Ming</th>
                                        <th>Sen</th>
                                        <th>Sel</th>
                                        <th>Rab</th>
                                        <th>Kam</th>
                                        <th>Jum</th>
                                        <th>Sab</th>
                                    </tr>
                                </thead>
                                <tbody id="calendarBody">
                                    <!-- Filled by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Legend -->
                        <div class="calendar-legend">
                            {{-- <div class="legend-item">
                                <span class="legend-color" style="background-color: #ffffff; border: 1px solid #333;"></span>
                                <span>Hari Biasa</span>
                            </div> --}}
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #ff0000;"></span>
                                <span>Hari Libur</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #0066ff;"></span>
                                <span>Hari Ini</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #ffff00;"></span>
                                <span>Reminder</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reminder Section -->
                    <div class="reminder-section-landing">
                        <h3>Reminder</h3>
                        
                        <div class="reminder-input-section">
                            <input type="date" id="reminderDate" class="reminder-input">
                            <input type="text" id="reminderTitle" class="reminder-input" placeholder="Judul reminder">
                            <textarea id="reminderDescription" class="reminder-input" placeholder="Deskripsi (opsional)" rows="2"></textarea>
                            <button id="addReminderBtn" class="btn-add-reminder">+ Tambah Reminder</button>
                        </div>

                        <div id="reminderList" class="reminder-list">
                            <!-- Reminders will be displayed here -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .profile-dropdown {
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .profile-icon {
            width: 32px;
            height: 32px;
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
        function navigateToBidang(bidang) {
            // Redirect ke engineering page dengan bidang sebagai filter
            window.location.href = '{{ route('engineering.index') }}?bidang=' + encodeURIComponent(bidang);
        }

        function toggleProfileMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('profileMenu');
            menu.classList.toggle('active');
        }

        // Close profile menu when clicking outside
        document.addEventListener('click', function(event) {
            const profileDropdown = document.querySelector('.profile-dropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            // Only close if click is outside profile dropdown and not in sidebar/submenu
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                // Check if click is in sidebar or submenu area
                const sidebar = document.querySelector('.landing-sidebar');
                if (!sidebar || !sidebar.contains(event.target)) {
                    profileMenu.classList.remove('active');
                }
            }
        });

        // Document Menu Toggle
        document.getElementById('document-menu')?.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('open');
            const submenu = document.getElementById('submenu-document');
            if (submenu) {
                submenu.classList.toggle('open');
            }
        });

        // Submenu item click handler
        document.querySelectorAll('.submenu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>

    <script src="{{ asset('js/landing.js') }}"></script>
    <script src="{{ asset('js/calendar.js') }}"></script>
</body>
</html>
