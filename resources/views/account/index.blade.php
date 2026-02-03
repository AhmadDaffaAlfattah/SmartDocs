@extends('layouts.master')

@section('title', 'SmartDocs - Account Management')

@push('styles')
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 9999; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
            
            /* Center the modal content vertically and horizontally */
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto; /* Fallback */
            padding: 30px;
            border: 1px solid #888;
            width: 500px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 20px;
            top: 15px;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-row label {
            width: 120px;
            font-weight: 600;
            color: #333;
        }
        .form-row input, .form-row select {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ccc; /* Match image simple border */
            border-radius: 6px; /* Rounded corners */
            background-color: #f9f9f9; /* Slight background per image? hard to tell, usually white/light gray */
            font-size: 14px;
        }
        .btn-simpan {
            background-color: #ccc; /* Light grey as per image */
            color: #333;
            font-weight: bold;
            border: 1px solid #999;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-simpan:hover {
            background-color: #bbb;
        }
        
        .btn-batal {
             background-color: transparent;
             border: none;
             color: #666;
             cursor: pointer;
             margin-right: 15px;
             font-size: 14px;
        }
        .btn-batal:hover {
            color: #333;
            text-decoration: underline;
        }
        
        /* Error text style */
        .error-text {
            font-size: 12px;
            color: red;
            margin-top: 4px;
        }
        .hidden {
            display: none;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="engineering-page-header">
        <div class="engineering-page-title">
            <div style="font-size: 28px; font-weight: bold; color: #333333;">Account</div>
            <button class="btn-tambah-data" onclick="openAddModal()">
                ➕ Tambah Account
            </button>
        </div>
    </div>

    <!-- Flash Messages -->


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

                <!-- Pagination -->
                <div class="pagination">
                    {{ $accounts->links() }}
                </div>
            @else
                <div style="padding: 40px; text-align: center; color: #999;">
                    Tidak ada data account
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Add Account -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Tambah Account</h2>
            <form id="addForm" method="POST" action="{{ route('account.store') }}">
                @csrf
                <div class="form-row">
                    <label for="addName">Username</label>
                    <input type="text" id="addName" name="name" placeholder="" required>
                </div>

                <div class="form-row">
                    <label for="addEmail">Email</label>
                    <input type="email" id="addEmail" name="email" placeholder="" required>
                </div>

                <div class="form-row">
                    <label for="addPassword">Password</label>
                    <input type="text" id="addPassword" name="password" placeholder="" required>
                </div>

                <div class="form-row">
                    <label for="addBidang">Bidang</label>
                    <select id="addBidang" name="bidang">
                        <option value="">Pilih Bidang</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang }}">{{ $bidang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <label for="addRole">Role</label>
                    <select id="addRole" name="role" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-start; margin-top: 20px;">
                    <button type="submit" class="btn-simpan" style="padding: 8px 30px; border-radius: 20px;">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Account -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Edit Account</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-row">
                    <label for="editName">Username</label>
                    <input type="text" id="editName" name="name" placeholder="" required>
                </div>

                <div class="form-row">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" placeholder="" required>
                </div>

                <div class="form-row">
                    <label for="editPassword">Password</label>
                    <input type="text" id="editPassword" name="password" placeholder="">
                </div>

                <div class="form-row">
                    <label for="editBidang">Bidang</label>
                    <select id="editBidang" name="bidang">
                        <option value="">Pilih Bidang</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang }}">{{ $bidang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <label for="editRole">Role</label>
                    <select id="editRole" name="role" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-start; margin-top: 20px;">
                    <button type="submit" class="btn-simpan" style="padding: 8px 30px; border-radius: 20px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
            document.getElementById('addForm').reset();
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(accountId) {
            const url = "{{ route('account.get', ':id') }}".replace(':id', accountId);
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editBidang').value = data.bidang;
                    document.getElementById('editRole').value = data.role;
                    document.getElementById('editPassword').value = data.password; // Populate password
                    
                    const form = document.getElementById('editForm');
                    const updateUrl = "{{ route('account.update', ':id') }}".replace(':id', accountId);
                    form.action = updateUrl;
                    
                    document.getElementById('editModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error fetching account data:', error);
                    alert('Gagal mengambil data akun. Silakan coba lagi.');
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function changePerPage(perPage) {
            const searchQuery = new URLSearchParams(window.location.search).get('search') || '';
            window.location.href = `{{ route('account.index') }}?per_page=${perPage}&search=${searchQuery}`;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            
            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }
    </script>
@endpush
