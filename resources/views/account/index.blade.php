<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDocs - Account Management</title>
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
                        <div class="submenu-item" onclick="window.location.href='{{ route('operasi.index') }}'">▸ Operasi</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('pemeliharaan.index') }}'">▸ Pemeliharaan</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('business-support.index') }}'">▸ Business Support</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('keamanan.index') }}'">▸ Keamanan</div>
                        <div class="submenu-item" onclick="window.location.href='{{ route('lingkungan.index') }}'">▸ Lingkungan</div>
                    </div>
                        <div class="submenu-item" onclick="navigateToBidang('Business Support')">▸ Business Support</div>
                        <div class="submenu-item" onclick="navigateToBidang('Keamanan')">▸ Keamanan</div>
                        <div class="submenu-item" onclick="navigateToBidang('Lingkungan')">▸ Lingkungan</div>
                    </div>

                   <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'">Folder</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'">Data Mesin</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="landing-main">
                <!-- Account Header -->
                <div class="engineering-page-header">
                    <div class="engineering-page-title">
                        <div style="font-size: 28px; font-weight: bold; color: #333333; margin-bottom: 20px;">
                            <span style="font-weight: bold;">Account</span>
                        </div>
                        <button class="btn-tambah-data" onclick="openAddModal()">
                            ➕ Tambah Account
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if ($message = Session::get('success'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-success">
                            {{ $message }}
                        </div>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div style="margin: 20px 30px 0 30px;">
                        <div class="alert alert-error">
                            {{ $message }}
                        </div>
                    </div>
                @endif

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-group" style="margin-left: auto;">
                        <label>Show</label>
                        <select name="per_page" class="entries-select" onchange="changePerPage(this.value)">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <span style="margin-left: 8px;">Entries</span>
                    </div>

                    <div class="search-box">
                        <form method="GET" action="{{ route('account.index') }}" style="display: flex; width: 100%;">
                            <input type="text" name="search" placeholder="Search" value="{{ $searchQuery }}" 
                                   style="flex: 1; margin-right: 10px;">
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #666;">
                                <img src="https://cdn-icons-png.flaticon.com/128/151/151773.png" loading="lazy" alt="Magnifying glass" title="Magnifying glass" width="16" height="16">
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-section">
                    <div class="table-header">
                        <span>Show {{ $perPage }} Entries</span>
                    </div>

                    <div class="table-content">
                        @if ($accounts->count() > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 25%;">Username</th>
                                        <th style="width: 20%;">Password</th>
                                        <th style="width: 20%;">Bidang</th>
                                        <th style="width: 15%;">Role</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accounts as $key => $account)
                                        <tr>
                                            <td class="no">{{ ($accounts->currentPage() - 1) * $perPage + $key + 1 }}</td>
                                            <td class="judul">{{ $account->name }}</td>
                                            <td>{{ $account->password }}</td>
                                            <td>{{ $account->bidang ?? '-' }}</td>
                                            <td>
                                                <span style="background-color: {{ $account->role === 'Administrator' ? '#4285F4' : '#34A853' }}; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                    {{ $account->role }}
                                                </span>
                                            </td>
                                            <td class="action">
                                                <button class="action-btn edit" title="Edit" onclick="openEditModal({{ $account->id }})">
                                                    <img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" width="32" height="32">
                                                </button>
                                                <form action="{{ route('account.destroy', $account->id) }}" method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn delete" title="Delete" style="border: none; background: none; cursor: pointer;">
                                                        <img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" loading="lazy" alt="Delete" width="32" height="32">
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <p>👤 Tidak ada akun ditemukan</p>
                            </div>
                        @endif
                    </div>

                    @if ($accounts->count() > 0)
                        <div class="pagination-section">
                            {{ $accounts->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div id="addModalOverlay" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Account</h2>
                <button type="button" class="modal-close" onclick="closeAddModal()">✕</button>
            </div>
            <form action="{{ route('account.store') }}" method="POST" id="addForm">
                <div class="modal-body">
                    @csrf

                    <!-- Username -->
                    <div class="form-group">
                        <label for="addName">Username <span class="required">*</span></label>
                        <input type="text" id="addName" name="name" placeholder="Masukkan username" required>
                        <div class="error-text hidden" id="nameError"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="addEmail">Email <span class="required">*</span></label>
                        <input type="email" id="addEmail" name="email" placeholder="Masukkan email" required>
                        <div class="error-text hidden" id="emailError"></div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="addPassword">Password <span class="required">*</span></label>
                        <input type="password" id="addPassword" name="password" placeholder="Masukkan password" required>
                        <div class="error-text hidden" id="passwordError"></div>
                    </div>

                    <!-- Bidang -->
                    <div class="form-group">
                        <label for="addBidang">Bidang <span class="required">*</span></label>
                        <select id="addBidang" name="bidang" required>
                            <option value="">Pilih Bidang</option>
                            @foreach ($bidangs as $bidang)
                                <option value="{{ $bidang }}">{{ $bidang }}</option>
                            @endforeach
                        </select>
                        <div class="error-text hidden" id="bidangError"></div>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="addRole">Role <span class="required">*</span></label>
                        <select id="addRole" name="role" required>
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                        <div class="error-text hidden" id="roleError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-submit">Submit</button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div id="editModalOverlay" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Account</h2>
                <button type="button" class="modal-close" onclick="closeEditModal()">✕</button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editAccountId" name="id">

                    <!-- Username -->
                    <div class="form-group">
                        <label for="editName">Username <span class="required">*</span></label>
                        <input type="text" id="editName" name="name" placeholder="Masukkan username" required>
                        <div class="error-text hidden" id="editNameError"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="editEmail">Email <span class="required">*</span></label>
                        <input type="email" id="editEmail" name="email" placeholder="Masukkan email" required>
                        <div class="error-text hidden" id="editEmailError"></div>
                    </div>

                    <!-- Password (Optional) -->
                    <div class="form-group">
                        <label for="editPassword">Password (Biarkan kosong jika tidak ingin mengubah)</label>
                        <input type="password" id="editPassword" name="password" placeholder="Masukkan password baru (opsional)">
                        <div class="error-text hidden" id="editPasswordError"></div>
                    </div>

                    <!-- Bidang -->
                    <div class="form-group">
                        <label for="editBidang">Bidang <span class="required">*</span></label>
                        <select id="editBidang" name="bidang" required>
                            <option value="">Pilih Bidang</option>
                            @foreach ($bidangs as $bidang)
                                <option value="{{ $bidang }}">{{ $bidang }}</option>
                            @endforeach
                        </select>
                        <div class="error-text hidden" id="editBidangError"></div>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="editRole">Role <span class="required">*</span></label>
                        <select id="editRole" name="role" required>
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                        <div class="error-text hidden" id="editRoleError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-submit" onclick="submitEditForm(event)">Submit</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #999;
            border-radius: 4px;
            font-size: 13px;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #333;
            box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.1);
        }

        .required {
            color: red;
        }

        .error-text {
            color: red;
            font-size: 12px;
            margin-top: 4px;
        }

        .error-text.hidden {
            display: none;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-submit {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }

        .btn-submit:hover {
            background-color: #555;
        }

        .btn-cancel {
            background-color: #999;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }

        .btn-cancel:hover {
            background-color: #777;
        }

        .btn-tambah-data {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }

        .btn-tambah-data:hover {
            background-color: #555;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 13px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        function openAddModal() {
            document.getElementById('addModalOverlay').classList.add('active');
            document.getElementById('addForm').reset();
        }

        function closeAddModal() {
            document.getElementById('addModalOverlay').classList.remove('active');
        }

        function openEditModal(accountId) {
            fetch(`/account/${accountId}/get`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editAccountId').value = data.id;
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editBidang').value = data.bidang || '';
                    document.getElementById('editRole').value = data.role || '';
                    document.getElementById('editPassword').value = '';
                    document.getElementById('editModalOverlay').classList.add('active');
                })
                .catch(error => {
                    alert('Error loading account data');
                    console.error(error);
                });
        }

        function closeEditModal() {
            document.getElementById('editModalOverlay').classList.remove('active');
        }

        function submitEditForm(e) {
            e.preventDefault();
            const accountId = document.getElementById('editAccountId').value;
            const formData = new FormData(document.getElementById('editForm'));

            fetch(`/account/${accountId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    bidang: formData.get('bidang'),
                    role: formData.get('role'),
                })
            })
            .then(response => response.json())
            .then(data => {
                alert('Account berhasil diperbarui');
                window.location.reload();
            })
            .catch(error => {
                alert('Error updating account');
                console.error(error);
            });
        }

        function changePerPage(value) {
            window.location.href = `?per_page=${value}`;
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

        // Close modal when clicking outside
        document.getElementById('addModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });

        document.getElementById('editModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
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
