<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartDocs - Edit Detail Warning</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 1000px;
        }
        .form-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
            font-size: 13px;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            font-family: inherit;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 5px rgba(0, 102, 204, 0.3);
        }
        .btn-submit {
            padding: 10px 25px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background: #218838;
        }
        .btn-back {
            padding: 10px 25px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #5a6268;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        .error-message {
            color: #d32f2f;
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
                    </div>
                    <div class="sidebar-item" onclick="window.location.href='{{ route('asset-wellness.index') }}'">Data Mesin</div>
                </div>
            </div>

            <div class="landing-main">
                <div class="form-container">
                    <div class="form-header">
                        <h1>Edit Detail Warning</h1>
                    </div>

                    <form action="{{ route('detail-warning.update', $detailWarning->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Pilih Mesin -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Pilih Mesin *</label>
                                <select name="asset_wellness_id" required>
                                    <option value="">-- Pilih Mesin --</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ $detailWarning->asset_wellness_id == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->unit_pembangkit_common }} ({{ $asset->kode_mesin }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_wellness_id')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Unit Pembangkit *</label>
                                <input type="text" name="unit_pembangkit" value="{{ old('unit_pembangkit', $detailWarning->unit_pembangkit) }}" required>
                                @error('unit_pembangkit')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>Tanggal Identifikasi</label>
                                <input type="date" name="tanggal_identifikasi" value="{{ old('tanggal_identifikasi', $detailWarning->tanggal_identifikasi) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Status Saat Ini</label>
                                <input type="text" name="status_saat_ini" value="{{ old('status_saat_ini', $detailWarning->status_saat_ini) }}">
                            </div>
                            <div class="form-group">
                                <label>Asset Description</label>
                                <input type="text" name="asset_description" value="{{ old('asset_description', $detailWarning->asset_description) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Kondisi Aset</label>
                                <input type="text" name="kondisi_aset" value="{{ old('kondisi_aset', $detailWarning->kondisi_aset) }}">
                            </div>
                            <div class="form-group">
                                <label>Action Plan</label>
                                <input type="text" name="action_plan" value="{{ old('action_plan', $detailWarning->action_plan) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Target Selesai</label>
                                <input type="date" name="target_selesai" value="{{ old('target_selesai', $detailWarning->target_selesai) }}">
                            </div>
                            <div class="form-group">
                                <label>Progres Saat Ini</label>
                                <input type="text" name="progres_saat_ini" value="{{ old('progres_saat_ini', $detailWarning->progres_saat_ini) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Realisasi Selesai</label>
                                <input type="date" name="realisasi_selesai" value="{{ old('realisasi_selesai', $detailWarning->realisasi_selesai) }}">
                            </div>
                            <div class="form-group">
                                <label>Main Issue / Kendala</label>
                                <input type="text" name="main_issue_kendala" value="{{ old('main_issue_kendala', $detailWarning->main_issue_kendala) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan">{{ old('keterangan', $detailWarning->keterangan) }}</textarea>
                        </div>

                        <div class="button-group">
                            <a href="{{ route('detail-warning.index') }}" class="btn-back">← Kembali</a>
                            <button type="submit" class="btn-submit">💾 Perbarui</button>
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
