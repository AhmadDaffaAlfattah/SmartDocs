<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartDocs - Tambah Data Mesin</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 700px;
        }
        .form-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            box-sizing: border-box;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        button, .btn-kembali {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            flex: 1;
            text-align: center;
        }
        .btn-simpan {
            background: #28a745;
            color: white;
        }
        .btn-simpan:hover {
            background: #218838;
        }
        .btn-kembali {
            background: #999;
            color: white;
        }
        .btn-kembali:hover {
            background: #777;
        }
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="landing-header">
            <div class="header-left">
                <img src="{{ asset('images/logo_pln.png') }}" alt="Logo" class="logo-aplikasi">
            </div>
            <div class="header-right">
                <img src="{{ asset('images/akun.png') }}" alt="Profile" class="profile-icon">
            </div>
        </div>

        <div class="landing-wrapper">
            <div class="landing-sidebar">
                <div class="sidebar-items">
                    <div class="sidebar-item" onclick="window.location.href='/'">Dashboard</div>
                    <div class="sidebar-item collapsible" id="document-menu">
                        <span class="toggle-icon">▼</span>
                        <span class="menu-text">Document</span>
                    </div>
                    <div class="submenu" id="submenu-document">
                        <div class="submenu-item" onclick="window.location.href='{{ route('engineering.index') }}'">▸ Engineering</div>
                        <div class="submenu-item">▸ Operasi</div>
                        <div class="submenu-item">▸ Pemeliharaan</div>
                        <div class="submenu-item">▸ Business Support</div>
                        <div class="submenu-item">▸ Keamanan</div>
                        <div class="submenu-item">▸ Lingkungan</div>
                    </div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('folder.index') }}'">Folder</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'">Data Mesin</div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('account.index') }}'">Account</div>
                </div>
            </div>

            <div class="landing-main">
                <div class="form-container">
                    <div class="form-header">
                        <h1>➕ Tambah Data Mesin</h1>
                    </div>

                    <form action="{{ route('asset-wellness.store') }}" method="POST">
                        @csrf

                        <div class="form-group @error('kode_mesin') error @enderror">
                            <label>Kode Mesin *</label>
                            <input type="text" name="kode_mesin" value="{{ old('kode_mesin') }}" required>
                            @error('kode_mesin')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group @error('unit_pembangkit_common') error @enderror">
                            <label>Unit Pembangkit/Common *</label>
                            <input type="text" name="unit_pembangkit_common" value="{{ old('unit_pembangkit_common') }}" required>
                            @error('unit_pembangkit_common')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipe Aset</label>
                                <input type="text" name="tipe_aset" value="{{ old('tipe_aset') }}">
                            </div>
                            <div class="form-group">
                                <label>Kode Mesin SILM</label>
                                <input type="text" name="kode_mesin_silm" value="{{ old('kode_mesin_silm') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Daya Terpasang (MW)</label>
                                <input type="number" step="0.01" name="daya_terpasang" value="{{ old('daya_terpasang') }}">
                            </div>
                            <div class="form-group">
                                <label>Daya Mampu Netto (MW)</label>
                                <input type="number" step="0.01" name="daya_mampu_netto" value="{{ old('daya_mampu_netto') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Daya Mampu Pasok (MW)</label>
                                <input type="number" step="0.01" name="daya_mampu_pasok" value="{{ old('daya_mampu_pasok') }}">
                            </div>
                            <div class="form-group">
                                <label>Total Equipment *</label>
                                <input type="number" name="total_equipment" value="{{ old('total_equipment') }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Safe *</label>
                                <input type="number" name="safe" value="{{ old('safe') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Warning *</label>
                                <input type="number" name="warning" value="{{ old('warning') }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Fault *</label>
                                <input type="number" name="fault" value="{{ old('fault') }}" required>
                            </div>
                            {{-- <div class="form-group">
                                <label>Percentage Safe</label>
                                <input type="text" name="percentage_safe" value="{{ old('percentage_safe') }}">
                            </div> --}}
                        </div>

                        {{-- <div class="form-row">
                            <div class="form-group">
                                <label>Percentage Warning</label>
                                <input type="text" name="percentage_warning" value="{{ old('percentage_warning') }}">
                            </div>
                            <div class="form-group">
                                <label>Percentage Fault</label>
                                <input type="text" name="percentage_fault" value="{{ old('percentage_fault') }}">
                            </div>
                        </div> --}}

                        {{-- <div class="form-row">
                            <div class="form-group">
                                <label>Warning Equipment</label>
                                <input type="text" name="warning_equipment" value="{{ old('warning_equipment') }}">
                            </div>
                            <div class="form-group">
                                <label>Fault Equipment</label>
                                <input type="text" name="fault_equipment" value="{{ old('fault_equipment') }}">
                            </div>
                        </div> --}}

                        <div class="form-row">
                            <div class="form-group">
                                <label>Status Operasi</label>
                                <input type="text" name="status_operasi" value="{{ old('status_operasi') }}">
                            </div>
                            <div class="form-group">
                                <label>Tahun *</label>
                                <input type="text" name="tahun" value="{{ old('tahun', '2025') }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Bulan *</label>
                                <select name="bulan" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12" selected>Desember</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sentral</label>
                                <input type="text" name="sentral" value="{{ old('sentral') }}">
                            </div>
                        </div>

                        <div class="form-group @error('keterangan') error @enderror">
                            <label>Keterangan *</label>
                            <textarea name="keterangan" required>{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-simpan">💾 Simpan</button>
                            <a href="{{ route('asset-wellness.index') }}" class="btn-kembali">← Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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
