<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartDocs - Detail Fault</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .asset-container {
            background: white;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .asset-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .asset-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .table-wrapper {
            overflow-x: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        thead tr {
            background: #FAFAFA;
            color: black;
            font-weight: 600;
        }
        th {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        tbody tr:hover {
            background: #f9f9f9;
        }
        .btn-tambah {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .btn-tambah:hover {
            background: #218838;
        }
        .btn-edit {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            border: none;
            margin-right: 5px;
        }
        .btn-hapus {
            padding: 6px 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-back {
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            display: inline-block;
            margin-bottom: 15px;
        }
        .alert-success {
            padding: 12px 15px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 15px;
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
                <div class="asset-container">
                    <div class="asset-header">
                        <div>
                            <h1>Detail Fault - Semua Mesin</h1>
                        </div>
                    </div>

                    @if($message = session('success'))
                        <div class="alert-success">✅ {{ $message }}</div>
                    @endif

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <a href="{{ route('asset-wellness.index', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral]) }}" class="btn-back">← Kembali</a>
                            <a href="{{ route('detail-fault.create') }}" class="btn-tambah">➕ Tambah Detail Fault</a>
                        </div>
                        
                        <form action="{{ route('detail-fault.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
                            <!-- Month Filter -->
                            <select name="bulan" onchange="this.form.submit()" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Semua Bulan --</option>
                                @foreach($months as $key => $name)
                                    <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>

                            <!-- Year Filter -->
                            <select name="tahun" onchange="this.form.submit()" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Semua Tahun --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>

                            <!-- Sentral Filter -->
                            <select name="sentral" onchange="this.form.submit()" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; max-width: 150px;">
                                <option value="">-- Semua Sentral --</option>
                                @foreach($sentralList as $s)
                                    <option value="{{ $s }}" {{ request('sentral') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>

                            <!-- Search -->
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode/Inisial..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 200px;">
                            <button type="submit" style="padding: 8px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer;">🔍 Cari</button>
                            
                            @if(request('search') || request('bulan') || request('tahun') || request('sentral'))
                                <a href="{{ route('detail-fault.index') }}" style="padding: 8px 12px; background: #999; color: white; border: none; border-radius: 4px; text-decoration: none;">Reset</a>
                            @endif
                        </form>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th style="text-align: center; width: 50px;">NO</th>
                                    <th>UNIT PEMBANGKIT</th>
                                    <th>TANGGAL IDENTIFIKASI</th>
                                    <th>STATUS SAAT INI</th>
                                    <th>ASSET DESCRIPTION</th>
                                    <th>KONDISI ASET</th>
                                    <th>ACTION PLAN</th>
                                    <th>TARGET SELESAI</th>
                                    <th>PROGRES SAAT INI</th>
                                    <th>REALISASI SELESAI</th>
                                    <th>MAIN ISSUE / KENDALA</th>
                                    <th>KETERANGAN</th>
                                    <th style="text-align: center; width: 120px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailFaults as $key => $detail)
                                    <tr>
                                        <td style="text-align: center;">{{ $key + 1 }}</td>
                                        <td>{{ $detail->unit_pembangkit }}</td>
                                        <td>{{ $detail->tanggal_identifikasi ? \Carbon\Carbon::parse($detail->tanggal_identifikasi)->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $detail->status_saat_ini ?? '-' }}</td>
                                        <td>{{ $detail->asset_description ?? '-' }}</td>
                                        <td>{{ $detail->kondisi_aset ?? '-' }}</td>
                                        <td>{{ $detail->action_plan ?? '-' }}</td>
                                        <td>{{ $detail->target_selesai ? \Carbon\Carbon::parse($detail->target_selesai)->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $detail->progres_saat_ini ?? '-' }}</td>
                                        <td>{{ $detail->realisasi_selesai ? \Carbon\Carbon::parse($detail->realisasi_selesai)->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $detail->main_issue_kendala ?? '-' }}</td>
                                        <td>{{ $detail->keterangan ?? '-' }}</td>
                                        <td style="text-align: center; display: flex; justify-content: center; gap: 5px;">
                                            <a href="{{ route('detail-fault.show', $detail->id) }}" class="btn-edit">✏️ Edit</a>
                                            <form action="{{ route('detail-fault.destroy', $detail->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-hapus">🗑️ Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" style="padding: 20px; text-align: center; color: #999;">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
