<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartDocs - Edit Data Mesin</title>
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
        input:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
            color: #666;
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
            background: #007bff;
            color: white;
        }
        .btn-simpan:hover {
            background: #0056b3;
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
        .readonly-note {
            font-size: 11px;
            color: #999;
            margin-top: 3px;
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
                        <h1>✏️ Edit Data Mesin</h1>
                    </div>

                    <form action="{{ route('asset-wellness.update', $assetWellness->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Kode Mesin SILM *</label>
                            <input type="text" name="kode_mesin_silm" value="{{ $assetWellness->kode_mesin_silm }}" disabled>
                            <div class="readonly-note">Tidak dapat diubah</div>
                        </div>

                        <div class="form-group @error('unit_pembangkit_common') error @enderror">
                            <label>Unit Pembangkit/Common *</label>
                            <input type="text" name="unit_pembangkit_common" value="{{ $assetWellness->unit_pembangkit_common }}" required>
                            @error('unit_pembangkit_common')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipe Aset</label>
                                <input type="text" name="tipe_aset" value="{{ $assetWellness->tipe_aset }}">
                            </div>
                            <div class="form-group">
                                <label>Kode Mesin (Short)</label>
                                <input type="text" name="kode_mesin" value="{{ $assetWellness->kode_mesin }}" disabled>
                                <div class="readonly-note">Primary Key</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Inisial Mesin (untuk Peta)</label>
                            <input type="text" name="inisial_mesin" value="{{ $assetWellness->inisial_mesin }}">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Daya Terpasang (MW)</label>
                                <input type="number" step="0.01" name="daya_terpasang" value="{{ $assetWellness->daya_terpasang }}">
                            </div>
                            <div class="form-group">
                                <label>Daya Mampu Netto (MW)</label>
                                <input type="number" step="0.01" name="daya_mampu_netto" value="{{ $assetWellness->daya_mampu_netto }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Daya Mampu Pasok (MW)</label>
                                <input type="number" step="0.01" name="daya_mampu_pasok" value="{{ $assetWellness->daya_mampu_pasok }}">
                            </div>
                            <div class="form-group">
                                <label>Total Equipment *</label>
                                <input type="number" name="total_equipment" value="{{ $assetWellness->total_equipment }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Safe *</label>
                                <input type="number" name="safe" value="{{ $assetWellness->safe }}" required>
                            </div>
                            <div class="form-group">
                                <label>Warning *</label>
                                <input type="number" name="warning" value="{{ $assetWellness->warning }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Fault *</label>
                                <input type="number" name="fault" value="{{ $assetWellness->fault }}" required>
                            </div>
                            {{-- <div class="form-group">
                                <label>Percentage Safe</label>
                                <input type="text" name="percentage_safe" value="{{ $assetWellness->percentage_safe }}">
                            </div> --}}
                        </div>

                        {{-- <div class="form-row">
                            <div class="form-group">
                                <label>Percentage Warning</label>
                                <input type="text" name="percentage_warning" value="{{ $assetWellness->percentage_warning }}">
                            </div>
                            <div class="form-group">
                                <label>Percentage Fault</label>
                                <input type="text" name="percentage_fault" value="{{ $assetWellness->percentage_fault }}">
                            </div>
                        </div> --}}

                        {{-- <div class="form-row">
                            <div class="form-group">
                                <label>Warning Equipment</label>
                                <input type="text" name="warning_equipment" value="{{ $assetWellness->warning_equipment }}">
                            </div>
                            <div class="form-group">
                                <label>Fault Equipment</label>
                                <input type="text" name="fault_equipment" value="{{ $assetWellness->fault_equipment }}">
                            </div>
                        </div> --}}

                        <div class="form-row">
                            <div class="form-group">
                                <label>Status Operasi</label>
                                <input type="text" name="status_operasi" value="{{ $assetWellness->status_operasi }}">
                            </div>
                            <div class="form-group">
                                <label>Tahun *</label>
                                <input type="text" name="tahun" value="{{ $assetWellness->tahun }}" disabled>
                                <div class="readonly-note">Tidak dapat diubah</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Bulan *</label>
                                <select name="bulan" disabled>
                                    <option value="01" {{ $assetWellness->bulan == '01' ? 'selected' : '' }}>Januari</option>
                                    <option value="02" {{ $assetWellness->bulan == '02' ? 'selected' : '' }}>Februari</option>
                                    <option value="03" {{ $assetWellness->bulan == '03' ? 'selected' : '' }}>Maret</option>
                                    <option value="04" {{ $assetWellness->bulan == '04' ? 'selected' : '' }}>April</option>
                                    <option value="05" {{ $assetWellness->bulan == '05' ? 'selected' : '' }}>Mei</option>
                                    <option value="06" {{ $assetWellness->bulan == '06' ? 'selected' : '' }}>Juni</option>
                                    <option value="07" {{ $assetWellness->bulan == '07' ? 'selected' : '' }}>Juli</option>
                                    <option value="08" {{ $assetWellness->bulan == '08' ? 'selected' : '' }}>Agustus</option>
                                    <option value="09" {{ $assetWellness->bulan == '09' ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ $assetWellness->bulan == '10' ? 'selected' : '' }}>Oktober</option>
                                    <option value="11" {{ $assetWellness->bulan == '11' ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ $assetWellness->bulan == '12' ? 'selected' : '' }}>Desember</option>
                                </select>
                                <div class="readonly-note">Tidak dapat diubah</div>
                            </div>
                            <div class="form-group">
                                <label>Sentral</label>
                                <input type="text" name="sentral" value="{{ $assetWellness->sentral }}" disabled>
                                <div class="readonly-note">Tidak dapat diubah</div>
                            </div>
                        </div>

                        <div class="form-group @error('keterangan') error @enderror">
                            <label>Keterangan *</label>
                            <textarea name="keterangan" required>{{ $assetWellness->keterangan }}</textarea>
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
