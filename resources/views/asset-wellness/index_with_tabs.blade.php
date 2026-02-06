@extends('layouts.master')

@section('title', 'SmartDocs - Kesehatan Mesin')

@push('styles')
    <style>
        .asset-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            /* Task 1: Border removed per user request */
            border: none;
            box-shadow: none; 
            min-height: 80vh;
            overflow: hidden; /* Ensure content stays inside border */
            width: 100%;
            box-sizing: border-box;
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

        /* TAB STYLING */
        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        .nav-tab {
            padding: 12px 20px;
            background: #f5f5f5;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            /* Task 4: Removed transition to reduce animation feel if desired, though usually acceptable */
            /* transition: all 0.3s; */
        }
        .nav-tab:hover {
            background: #e8e8e8;
        }
        .nav-tab.active {
            background: white;
            color: #0066cc;
            border-bottom-color: #0066cc;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-section select {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            background: white;
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
        }
        .btn-tambah:hover {
            background: #218838;
        }
        .btn-download {
            padding: 10px 20px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            position: relative;
        }
        .btn-download:hover {
            background: #0052a3;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            top: 100%;
            right: 0;
            min-width: 150px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            margin-top: 5px;
        }
        .dropdown-menu.active {
            display: block;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        .dropdown-menu a:last-child {
            border-bottom: none;
        }
        .dropdown-menu a:hover {
            background: #f0f0f0;
        }
        .button-group {
            display: flex;
            gap: 10px;
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
        .col-safe {
            background: #90EE90;
            color: #006600;
            font-weight: 600;
            text-align: center;
        }
        .col-warning {
            background: #FFD700;
            color: #FF6B00;
            font-weight: 600;
            text-align: center;
        }
        .col-fault-safe {
            background: #90EE90;
            color: #006600;
        }
        .col-fault-danger {
            background: #FF6B6B;
            color: #FFFFFF;
            font-weight: 600;
            text-align: center;
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
        .alert-success {
            padding: 12px 15px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        /* Health Map Styles */
        .org-chart-container {
            padding: 30px;
            background: white;
            min-height: 600px;
            overflow-x: auto;
        }
        .org-chart { text-align: center; }
        .up-box { margin: 0 auto 10px; display: inline-block; }
        .up-box img { width: 80px; height: auto; display: block; margin: 0 auto 5px; }
        .up-label { background: #FFB84D; color: white; padding: 8px 16px; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .connector-line { width: 2px; height: 20px; background: #999; margin: 0 auto; }
        .ul-container { display: flex; gap: 20px; justify-content: space-between; flex-wrap: wrap; margin-top: 15px; width: 100%; }
        .ul-box { background: white; border-radius: 4px; padding: 10px; min-width: 200px; flex: 1; }
        .ul-box img { width: 60px; height: auto; display: block; margin: 0 auto 8px; }
        .ul-header { background: #5dade2; color: white; padding: 8px; border-radius: 3px; font-weight: bold; font-size: 12px; margin-bottom: 12px; text-align: center; }
        .pltd-group { margin-bottom: 12px; padding: 8px; background: #f0f8ff; border-radius: 3px; border-left: 3px solid #5dade2; }
        .pltd-name { font-size: 11px; font-weight: bold; color: #333; margin-bottom: 6px; padding-bottom: 5px; border-bottom: 1px dashed #5dade2; }
        .machines-list { display: flex; flex-direction: column; gap: 4px; }
        .machine-item-text { padding: 5px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; width: 100%; text-align: left; cursor: pointer; transition: all 0.2s; position: relative; padding-left: 28px; display: flex; align-items: center; gap: 6px; }
        .machine-icon { position: absolute; left: 5px; width: 20px; height: 16px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .machine-icon img { width: 18px; height: auto; object-fit: contain; }
        .machine-item-text.safe { background: #e8f5e9; border-left: 3px solid #4CAF50; color: #2e7d32; }
        .machine-item-text.warning { background: #FFD700; border-left: 3px solid #FFA500; color: #333; font-weight: bold; }
        .machine-item-text.fault { background: #FF6B6B; border-left: 3px solid #E74C3C; color: white; font-weight: bold; animation: blink-fault 0.6s infinite; }
        @keyframes blink-fault { 0%, 100% { opacity: 1; background: #FF6B6B; } 50% { opacity: 0.3; background: #E74C3C; } }
        .machine-item-text:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.15); transform: translateX(3px); }
        .status-indicator-badge { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .status-indicator-badge.safe { background: #4CAF50; }
        .status-indicator-badge.warning { background: #FFD700; }
        .status-indicator-badge.fault { background: #FF6B6B; animation: pulse-fault 0.6s infinite; box-shadow: 0 0 6px rgba(255, 107, 107, 0.8); }
        @keyframes pulse-fault { 0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 8px rgba(255, 107, 107, 0.8); } 50% { transform: scale(0.5); opacity: 0.3; box-shadow: 0 0 2px rgba(255, 107, 107, 0.4); } }
        .legend-area { background: white; border: 1px solid #ccc; padding: 12px; margin-bottom: 20px; border-radius: 4px; display: flex; gap: 25px; justify-content: center; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; }
        .legend-dot { width: 14px; height: 14px; border-radius: 2px; }
        .empty-map { text-align: center; padding: 50px 20px; color: #999; }
    </style>
@endpush

@section('content')
    <div class="asset-container">
        <div class="asset-header">
            <div>
                <h1> Kesehatan Mesin</h1>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;"> Asset Wellness</p>
            </div>
            <!-- Task 3: Profile controlled by Layout -->
        </div>

        @if($message = session('success'))
            <div class="alert-success">✅ {{ $message }}</div>
        @endif

        @if($message = session('error'))
            <div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px 15px; border-radius: 4px; margin-bottom: 15px; border: 1px solid transparent;">
                ❌ {{ $message }}
            </div>
        @endif

        <!-- TAB NAVIGATION -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab(event, 'tab-form')">Form Penyimpanan</button>
            <button class="nav-tab" onclick="showTab(event, 'tab-health-map')"> Peta Kesehatan</button>
            <button class="nav-tab" onclick="showTab(event, 'tab-warning')">Detail Warning</button>
            <button class="nav-tab" onclick="showTab(event, 'tab-fault')">Detail Fault</button>
            <button class="nav-tab" onclick="showTab(event, 'tab-visualisasi')"> Visualisasi Data</button>
        </div>

        <!-- TAB 1: FORM PENYIMPANAN (INDEX DATA) -->
        <div id="view-tab-form" class="tab-content active">
            <div class="filter-section">
                <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    <select name="tahun" onchange="this.form.submit()" style="width: 120px;">
                        <option value="">-- Tahun --</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>

                    <select name="bulan" onchange="this.form.submit()" style="width: 140px;">
                        <option value="">-- Bulan --</option>
                        @foreach($bulanList as $key => $value)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>

                    <select name="sentral" onchange="this.form.submit()" style="width: 150px;">
                        <option value="">-- Sentral --</option>
                        @foreach($sentrlList as $s)
                            <option value="{{ $s }}" {{ $sentral == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="button-group" style="margin-left: auto; align-items: center; display: flex;">
                    <form id="bulk-delete-form" action="{{ route('asset-wellness.destroyPeriod') }}" method="POST" onsubmit="event.preventDefault(); showConfirmModal({ title: 'Hapus Data Periode', message: '⚠️ PERINGATAN: Anda yakin ingin menghapus SEMUA DATA untuk Periode {{ $bulan }}-{{ $tahun }}? Data yang dihapus tidak dapat dikembalikan!', type: 'danger', confirmText: 'Ya, Hapus Semua', onConfirm: () => document.getElementById('bulk-delete-form').submit() });" style="margin-right: 10px;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="sentral" value="{{ $sentral }}">
                        <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 7px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">🗑️ Hapus Data</button>
                    </form>
                    <form action="{{ route('asset-wellness.import') }}" method="POST" enctype="multipart/form-data" style="margin-right: 10px; display: inline-flex; align-items: center; gap: 5px;">
                        @csrf
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="sentral" value="{{ $sentral }}">
                        <input type="file" name="file" accept=".xlsx, .xls, .csv" required style="padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; width: 200px;">
                        <button type="submit" style="background: #17a2b8; color: white; border: none; padding: 7px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">📤 Upload Excel</button>
                    </form>

                    <div class="btn-download" onclick="toggleDownloadMenu(event)" style="position: relative;">
                        📥 Download
                        <div id="downloadMenu" class="dropdown-menu">
                            <a href="{{ route('asset-wellness.export', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral]) }}" onclick="event.stopPropagation();">📊  Laporan Excel</a>
                            <a href="{{ route('asset-wellness.pdf-report', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral]) }}" onclick="event.stopPropagation();">📋 Laporan PDF </a>
                        </div>
                    </div>
                    <a href="{{ route('asset-wellness.create') }}" class="btn-tambah">➕ Tambah Data</a>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 50px;">NO</th>
                            <th style="width: 150px;">SENTRAL</th>
                            <th style="width: 100px;">TIPE ASET</th>
                            <th style="width: 100px;">INISIAL</th>
                            <th style="width: 150px;">KODE MESIN UNIT PEMBANGKIT (SILM)</th>
                            <th style="width: 180px;">UNIT PEMBANGKIT/COMMON</th>
                            <th style="text-align: center; width: 100px;">Daya Terpasang (MW)</th>
                            <th style="text-align: center; width: 110px;">Daya Mampu Netto (MW)</th>
                            <th style="text-align: center; width: 110px;">Daya Mampu Pasok (MW)</th>
                            <th style="text-align: center; width: 100px;">Total Equipment</th>
                            <th style="text-align: center; width: 90px; background: #90EE90;">Equipment Safe</th>
                            <th style="text-align: center; width: 90px; background: #FFD700;">Equipment Warning</th>
                            <th style="text-align: center; width: 90px; background: #FF6B6B;">Equipment Fault</th>
                            <th style="text-align: center; width: 90px; background: #E8F5E9;">% Safe</th>
                            <th style="text-align: center; width: 90px; background: #FFF9C4;">% Warning</th>
                            <th style="text-align: center; width: 90px; background: #FFEBEE;">% Fault</th>
                            <th style="width: 200px;">Keterangan</th>
                            <th style="text-align: center; width: 200px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $key => $asset)
                            <tr>
                                <td style="text-align: center;">{{ $key + 1 }}</td>
                                <td><strong>{{ $asset->sentral ?? '-' }}</strong></td>
                                <td>{{ $asset->tipe_aset ?? '-' }}</td>
                                <td>{{ $asset->inisial_mesin ?? '-' }}</td>
                                <td>{{ $asset->kode_mesin_silm ?? '-' }}</td>
                                <td>{{ $asset->unit_pembangkit_common }}</td>
                                <td style="text-align: center;">{{ $asset->daya_terpasang ?? '-' }}</td>
                                <td style="text-align: center;">{{ $asset->daya_mampu_netto ?? '-' }}</td>
                                <td style="text-align: center;">{{ $asset->daya_mampu_pasok ?? '-' }}</td>
                                <td style="text-align: center;">{{ $asset->total_equipment }}</td>
                                @php
                                    if ($asset->fault > 0) {
                                        $kelasWarna = 'col-fault-danger';
                                    } elseif ($asset->warning > 0) {
                                        $kelasWarna = 'col-warning';
                                    } else {
                                        $kelasWarna = 'col-safe';
                                    }
                                @endphp
                                <td class="{{ $kelasWarna == 'col-safe' ? 'col-safe' : '' }}" style="text-align: center;">{{ $asset->safe }}</td>
                                <td class="{{ $kelasWarna == 'col-warning' ? 'col-warning' : '' }}" style="text-align: center;">{{ $asset->warning }}</td>
                                <td class="{{ $kelasWarna == 'col-fault-danger' ? 'col-fault-danger' : '' }}" style="text-align: center;">{{ $asset->fault }}</td>
                                @php
                                    $total = $asset->safe + $asset->warning + $asset->fault;
                                    $persen_safe = $total > 0 ? round(($asset->safe / $total) * 100, 2) : 0;
                                    $persen_warning = $total > 0 ? round(($asset->warning / $total) * 100, 2) : 0;
                                    $persen_fault = $total > 0 ? round(($asset->fault / $total) * 100, 2) : 0;
                                @endphp
                                <td style="text-align: center;">{{ $persen_safe }}%</td>
                                <td style="text-align: center;">{{ $persen_warning }}%</td>
                                <td style="text-align: center;">{{ $persen_fault }}%</td>
                                <td>{{ $asset->keterangan ?? '-' }}</td>
                                <td style="text-align: center; display: flex; justify-content: center; align-items: center; gap: 4px; white-space: nowrap;">
                                    <button class="action-btn edit" title="Edit" onclick="window.location.href='{{ route('asset-wellness.edit', $asset->id) }}'">
                                        <img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" title="Edit" width="24" height="24">
                                    </button>
                                    <form id="delete-form-asset-{{ $asset->id }}" action="{{ route('asset-wellness.destroy', $asset->id) }}" method="POST" style="display: inline;" onsubmit="event.preventDefault(); confirmDelete('delete-form-asset-{{ $asset->id }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete" style="border: none; background: none; cursor: pointer; padding: 0;">
                                            <img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" loading="lazy" alt="Delete" title="Delete" width="28" height="28">
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" style="padding: 20px; text-align: center; color: #999;">Tidak ada data</td>
                            </tr>
                        @endforelse
                        @if($assets->count() > 0)
                            @php
                                $total_daya_terpasang = $assets->sum('daya_terpasang');
                                $total_daya_mampu_netto = $assets->sum('daya_mampu_netto');
                                $total_daya_mampu_pasok = $assets->sum('daya_mampu_pasok');
                                $total_equipment = $assets->sum('total_equipment');
                                $total_safe = $assets->sum('safe');
                                $total_warning = $assets->sum('warning');
                                $total_fault = $assets->sum('fault');
                                $grand_total = $total_safe + $total_warning + $total_fault;
                                $total_persen_safe = $grand_total > 0 ? round(($total_safe / $grand_total) * 100, 2) : 0;
                                $total_persen_warning = $grand_total > 0 ? round(($total_warning / $grand_total) * 100, 2) : 0;
                                $total_persen_fault = $grand_total > 0 ? round(($total_fault / $grand_total) * 100, 2) : 0;
                            @endphp
                            <tr style="background-color: #E8F5E9; font-weight: bold; border-top: 3px solid #333;">
                                <td colspan="6" style="text-align: right; padding-right: 20px;">TOTAL</td>
                                <td style="text-align: center;">{{ $total_daya_terpasang }}</td>
                                <td style="text-align: center;">{{ $total_daya_mampu_netto }}</td>
                                <td style="text-align: center;">{{ $total_daya_mampu_pasok }}</td>
                                <td style="text-align: center;">{{ $total_equipment }}</td>
                                <td style="text-align: center; background: #C8E6C9;">{{ $total_safe }}</td>
                                <td style="text-align: center; background: #FFF59D;">{{ $total_warning }}</td>
                                <td style="text-align: center; background: #FFCDD2;">{{ $total_fault }}</td>
                                <td style="text-align: center;">{{ $total_persen_safe }}%</td>
                                <td style="text-align: center;">{{ $total_persen_warning }}%</td>
                                <td style="text-align: center;">{{ $total_persen_fault }}%</td>
                                <td colspan="2"></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: PETA KESEHATAN MESIN -->
        <div id="view-tab-health-map" class="tab-content">
            <!-- Legend -->
            <div class="legend-area">
                <div class="legend-item">
                    <div class="legend-dot" style="background: #4CAF50;"></div>
                    <span>SAFE</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #FFD700;"></div>
                    <span>WARNING</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #FF6B6B;"></div>
                    <span>FAULT</span>
                </div>
            </div>



            @if($assets->count() > 0)
                <div class="org-chart-container">
                    <div class="org-chart">
                        <div class="up-box">
                            <img src="/images/UP.png" alt="UP">
                            <div class="up-label">UP KALTIMRA</div>
                        </div>
                        <div class="connector-line"></div>
                        <div class="ul-container">
                            @php
                                // User defined structure
                                $ulStructure = [
                                    'UL NUNUKAN' => [
                                        'PLTD SEI BILAL',
                                        'PLTD SEBATIK', 
                                        'PLTD MALINAU', 
                                        'PLTD TIDUNG PALE',
                                        'PLTD TULIN ONSOI'
                                    ],
                                    'UL TARAKAN' => [
                                        'PLTMG GUNUNG BELAH', 
                                        'PLTD SEI BUAYA'
                                    ],
                                    'UL TANJUNG SELOR' => [
                                        'PLTD BUNYU', 
                                        'PLTD SAMBALIUNG', 
                                        'PLTD TALISAYAN'
                                    ],
                                    'UL BALIKPAPAN' => [
                                        'PLTD BATAKAN', 
                                        'PLTD GUNUNG MALANG', 
                                        'PLTD TANJUNG ARU'
                                    ]
                                ];
                                
                                // Group assets by PLTD/Sentral for easier lookup
                                $groupedAssets = [];
                                foreach($assets as $asset) {
                                    // Key by UL if available, or just Sentral/Unit
                                    // We will try to fuzzy match the Asset's Unit/Sentral to the hardcoded PLTD names
                                    
                                    // Improve matching by normalizing strings
                                    $unitNorm = strtoupper(trim($asset->unit_pembangkit_common . ' ' . $asset->sentral));
                                    $groupedAssets[] = [
                                        'asset' => $asset,
                                        'norm' => $unitNorm
                                    ];
                                }
                                
                                // Function to get assets for a specific PLTD name
                                function getAssetsForPltd($pltdName, $allAssets) {
                                    $matches = [];
                                    $pltdKey = str_replace(['PLTD', 'PLTMG', ' '], '', strtoupper($pltdName));
                                    
                                    foreach ($allAssets as $item) {
                                        $assetNorm = str_replace(['PLTD', 'PLTMG', ' '], '', $item['norm']);
                                        
                                        // Specific fixes for ambiguos names or substrings
                                        // e.g. "SEI BILAL" vs "SEI BUAYA"
                                        if (str_contains($assetNorm, $pltdKey)) {
                                            $matches[] = $item['asset'];
                                        } 
                                        // Handle specific cases if needed
                                        elseif ($pltdKey == 'TANJUNGARU' && str_contains($assetNorm, 'TJARU')) {
                                            $matches[] = $item['asset'];
                                        }
                                        elseif ($pltdKey == 'GUNUNGBELAH' && str_contains($assetNorm, 'GNBELAH')) {
                                            $matches[] = $item['asset'];
                                        }
                                    }
                                    
                                    // Sort by inisial or kode
                                    usort($matches, function($a, $b) {
                                        return strnatcmp($a->inisial_mesin ?? $a->kode_mesin, $b->inisial_mesin ?? $b->kode_mesin);
                                    });
                                    
                                    return $matches;
                                }
                            @endphp

                            @foreach($ulStructure as $ulName => $pltdList)
                                <div class="ul-box">
                                    <img src="/images/UL.png" alt="UL">
                                    <div class="ul-header">{{ $ulName }}</div>
                                    @foreach($pltdList as $pltdName)
                                        @php
                                            $pltdAssets = getAssetsForPltd($pltdName, $groupedAssets);
                                        @endphp
                                        <div class="pltd-group">
                                            <div class="pltd-name">{{ $pltdName }}</div>
                                            <div class="machines-list">
                                                @forelse($pltdAssets as $machine)
                                                    @php
                                                        $status = 'safe';
                                                        $displayName = $machine->kode_mesin;
                                                        // Priority: Inisial > Kode Mesin
                                                        if (!empty($machine->inisial_mesin) && $machine->inisial_mesin !== '-') {
                                                            $displayName = $machine->inisial_mesin;
                                                        }
                                                        
                                                        $safe = $machine->safe ?? 0;
                                                        $warning = $machine->warning ?? 0;
                                                        $fault = $machine->fault ?? 0;
                                                        
                                                        if ($fault > 0) $status = 'fault';
                                                        elseif ($warning > 0) $status = 'warning';
                                                        else $status = 'safe';
                                                        
                                                        $linkUrl = '#';
                                                        $isClickable = false;
                                                        if ($status === 'warning') {
                                                            $linkUrl = route('detail-warning.index', ['search' => $machine->kode_mesin]);
                                                            $isClickable = true;
                                                        } elseif ($status === 'fault') {
                                                            $linkUrl = route('detail-fault.index', ['search' => $machine->kode_mesin]);
                                                            $isClickable = true;
                                                        }
                                                    @endphp
                                                    
                                                    @if($isClickable) <a href="{{ $linkUrl }}" style="text-decoration: none; display: block;"> @endif
                                                    <div class="machine-item-text {{ $status }}" title="Kode: {{ $machine->kode_mesin }}">
                                                        <div class="machine-icon"><img src="/images/mesin.png" alt="mesin"></div>
                                                        <span>{{ $displayName }}</span>
                                                        <div class="status-indicator-badge {{ $status }}"></div>
                                                    </div>
                                                    @if($isClickable) </a> @endif
                                                @empty
                                                    <div style="font-size: 9px; color: #999; padding: 4px 0; font-style: italic;">(Tidak ada data)</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-map"><p>📭 Tidak ada data mesin</p></div>
            @endif
        </div>

        <!-- TAB 3: DETAIL WARNING -->
        <div id="view-tab-warning" class="tab-content">
            <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                <a href="{{ route('detail-warning.create') }}" class="btn-tambah">➕ Tambah Detail Warning</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 50px;">NO</th>
                            <th style="width: 150px;">ASSET / MESIN</th>
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
                            <th style="text-align: center; width: 100px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailWarningsAll as $key => $detail)
                            <tr>
                                <td style="text-align: center;">{{ $key + 1 }}</td>
                                <td><strong>{{ $detail->assetWellness->unit_pembangkit_common ?? '-' }}</strong></td>
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
                                <td style="text-align: center; display: flex; justify-content: center; gap: 5px;">
                                    <a href="{{ route('detail-warning.show', $detail->id) }}" class="action-btn edit" title="Edit"><img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" width="24" height="24"></a>
                                    <form action="{{ route('detail-warning.destroy', $detail->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('DELETE')
                                         <button type="submit" class="action-btn delete" title="Delete" style="border: none; background: none; cursor: pointer; padding: 0;"><img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" alt="Delete" width="28" height="28"></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="13" style="padding: 20px; text-align: center; color: #999;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: DETAIL FAULT -->
        <div id="view-tab-fault" class="tab-content">
            <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                <a href="{{ route('detail-fault.create') }}" class="btn-tambah">➕ Tambah Detail Fault</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 50px;">NO</th>
                            <th style="width: 150px;">ASSET / MESIN</th>
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
                            <th style="text-align: center; width: 100px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailFaultsAll as $key => $detail)
                            <tr>
                                <td style="text-align: center;">{{ $key + 1 }}</td>
                                <td><strong>{{ $detail->assetWellness->unit_pembangkit_common ?? '-' }}</strong></td>
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
                                <td style="text-align: center; display: flex; justify-content: center; gap: 5px;">
                                    <a href="{{ route('detail-fault.show', $detail->id) }}" class="action-btn edit" title="Edit"><img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" width="24" height="24"></a>
                                    <form action="{{ route('detail-fault.destroy', $detail->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete" style="border: none; background: none; cursor: pointer; padding: 0;"><img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" alt="Delete" width="28" height="28"></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="13" style="padding: 20px; text-align: center; color: #999;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 5: VISUALISASI DATA -->
        <div id="view-tab-visualisasi" class="tab-content">
            @php
                $total_safe = $assets->sum('safe');
                $total_warning = $assets->sum('warning');
                $total_fault = $assets->sum('fault');
                $grand_total = $total_safe + $total_warning + $total_fault;
                $persen_safe = $grand_total > 0 ? (($total_safe / $grand_total) * 100) : 0;
                $persen_warning = $grand_total > 0 ? (($total_warning / $grand_total) * 100) : 0;
                $persen_fault = $grand_total > 0 ? (($total_fault / $grand_total) * 100) : 0;
            @endphp
            
            <div style="padding: 40px 20px; background: white; min-height: 600px; display: flex; flex-direction: column; gap: 40px;">
                <div style="text-align: center;">
                    <h2 style="margin: 0; font-size: 32px; color: #333; margin-bottom: 10px;">📊 Visualisasi Status Equipment</h2>
                    <p style="margin: 0; font-size: 16px; color: #666;">Analisis Persentase dan Distribusi Status Kesehatan Mesin</p>
                </div>

                <!-- FILTER SECTION (Task 5: Fix redirect) -->
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; align-items: center;">
                    <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; justify-content: center;">
                        <input type="hidden" name="tab_visual" value="1">
                        <select name="sentral" onchange="submitVisualisasi(this)" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 14px; cursor: pointer; background: white; font-weight: 500; min-width: 140px; transition: all 0.3s;">
                            <option value="">-- Sentral --</option>
                            @foreach($sentrlList as $s)
                                <option value="{{ $s }}" {{ request('sentral') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        <select name="bulan" onchange="submitVisualisasi(this)" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 14px; cursor: pointer; background: white; font-weight: 500; min-width: 140px; transition: all 0.3s;">
                            <option value="">-- Bulan --</option>
                            @foreach($bulanList as $key => $value)
                                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" onchange="submitVisualisasi(this)" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 14px; cursor: pointer; background: white; font-weight: 500; min-width: 140px; transition: all 0.3s;">
                            <option value="">-- Tahun --</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div style="display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; align-items: center;">
                    <!-- CHART 1 -->
                    <div style="flex: 1; min-width: 350px; max-width: 450px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                        <h3 style="margin: 0 0 20px 0; text-align: center; color: #333; font-size: 20px;">Distribusi Status</h3>
                        <div style="position: relative; width: 100%; height: 300px;">
                            <canvas id="pieChartVisualisasi"></canvas>
                        </div>
                    </div>
                    <!-- CHART 2 -->
                    <div style="flex: 1; min-width: 350px; max-width: 450px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                        <h3 style="margin: 0 0 20px 0; text-align: center; color: #333; font-size: 20px;">Jumlah Equipment</h3>
                        <div style="position: relative; width: 100%; height: 300px;">
                            <canvas id="barChartVisualisasi"></canvas>
                        </div>
                    </div>
                </div>

                <!-- STATS -->
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                   <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); min-width: 280px; border-top: 6px solid #90EE90;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div><p style="margin: 0; font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600;">Equipment Safe</p><p style="margin: 5px 0 0 0; font-size: 28px; font-weight: 700; color: #90EE90;">{{ $total_safe }}</p></div>
                        </div>
                        <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 10px;"><div style="background: #90EE90; height: 100%; width: {{ $persen_safe }}%;"></div></div>
                        <p style="margin: 0; font-size: 14px; color: #666; font-weight: 600;">{{ number_format($persen_safe, 2) }}%</p>
                    </div>
                    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); min-width: 280px; border-top: 6px solid #FFD700;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div><p style="margin: 0; font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600;">Equipment Warning</p><p style="margin: 5px 0 0 0; font-size: 28px; font-weight: 700; color: #FFD700;">{{ $total_warning }}</p></div>
                        </div>
                        <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 10px;"><div style="background: #FFD700; height: 100%; width: {{ $persen_warning }}%;"></div></div>
                        <p style="margin: 0; font-size: 14px; color: #666; font-weight: 600;">{{ number_format($persen_warning, 2) }}%</p>
                    </div>
                    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); min-width: 280px; border-top: 6px solid #FF6B6B;">
                       <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div><p style="margin: 0; font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600;">Equipment Fault</p><p style="margin: 5px 0 0 0; font-size: 28px; font-weight: 700; color: #FF6B6B;">{{ $total_fault }}</p></div>
                        </div>
                        <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 10px;"><div style="background: #FF6B6B; height: 100%; width: {{ $persen_fault }}%;"></div></div>
                        <p style="margin: 0; font-size: 14px; color: #666; font-weight: 600;">{{ number_format($persen_fault, 2) }}%</p>
                    </div>
                     <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); min-width: 280px; border-top: 6px solid #667eea;">
                         <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <div style="font-size: 32px;">📈</div>
                            <div><p style="margin: 0; font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600;">Total Equipment</p><p style="margin: 5px 0 0 0; font-size: 28px; font-weight: 700; color: #667eea;">{{ $grand_total }}</p></div>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #666;">Seluruh unit yang terdaftar dalam sistem</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        // TAB SWITCHING FUNCTION
        function showTab(event, tabName) {
            event.preventDefault();
            
            // 1. Hide all tab contents
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // 2. Deactivate all buttons
            const buttons = document.querySelectorAll('.nav-tab');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // 3. Show target tab content (using prefix matches HTML)
            // tabName is e.g. 'tab-form', ID is 'view-tab-form'
            const targetContent = document.getElementById('view-' + tabName);
            if(targetContent) {
                targetContent.classList.add('active');
            }
            
            // 4. Activate the button clicked
            if(event.target.classList.contains('nav-tab')) {
                event.target.classList.add('active');
            } else {
                // Find button if function called programmatically
                const btn = document.querySelector(`button[onclick*="'${tabName}'"]`);
                if(btn) btn.classList.add('active');
            }
            
            // 5. Update URL hash WITHOUT scrolling (using pushState)
            if(history.pushState) {
                history.pushState(null, null, '#' + tabName);
            } else {
                // Fallback for very old browsers (might scroll)
                window.location.hash = tabName;
            }
        }

        function submitVisualisasi(el) {
            const form = el.form;
            const params = new URLSearchParams(new FormData(form)).toString();
            // This reload ensures data is fresh. 
            // The #tab-visualisasi will be caught by the load listener below.
            window.location.href = "{{ route('asset-wellness.index') }}?" + params + "#tab-visualisasi";
        }
        
        // On page load: Check hash and activate correct tab
        window.addEventListener('load', function() {
            const hash = window.location.hash.substr(1); // e.g. "tab-visualisasi"
            
            if (hash) {
                // Determine the content ID
                const viewId = 'view-' + hash;
                const tabElement = document.getElementById(viewId);
                
                if (tabElement) {
                    // Hide all
                    const tabs = document.querySelectorAll('.tab-content');
                    tabs.forEach(tab => tab.classList.remove('active'));
                    const buttons = document.querySelectorAll('.nav-tab');
                    buttons.forEach(btn => btn.classList.remove('active'));
                    
                    // Show target
                    tabElement.classList.add('active');
                    
                    // Activate button
                    const btn = document.querySelector(`button[onclick*="'${hash}'"]`);
                    if(btn) btn.classList.add('active');
                }
                
                // CRITICAL: Prevent browser from maintaining scroll position relative to hash
                // which often pushes the layout down.
                setTimeout(() => {
                   window.scrollTo(0, 0); 
                }, 10);
            }
        });

        function toggleDownloadMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('downloadMenu');
            menu.classList.toggle('active');
        }
        
        document.addEventListener('click', function() {
            const menu = document.getElementById('downloadMenu');
            if (menu) menu.classList.remove('active');
        });

        // CHARTS CONFIGURATION (Task 4: Animation Removed)
        const ctxPie = document.getElementById('pieChartVisualisasi').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: [' Safe', ' Warning', ' Fault'],
                datasets: [{
                    data: [{{ $total_safe }}, {{ $total_warning }}, {{ $total_fault }}],
                    backgroundColor: ['#90EE90', '#FFD700', '#FF6B6B'],
                    borderColor: ['#228B22', '#FFB700', '#FF4444'],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // Task 4: Animation removed
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 14, weight: 'bold' }, padding: 20, usePointStyle: true } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = {{ $grand_total }};
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                                return context.label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        const ctxBar = document.getElementById('barChartVisualisasi').getContext('2d');
        const monthlyData = @json($monthlyIssues);
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: Object.keys(monthlyData),
                datasets: [{
                    label: 'Warning + Fault',
                    data: Object.values(monthlyData),
                    backgroundColor: '#FF7B54',
                    borderColor: '#D1563D',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // Task 4: Animation removed
                plugins: {
                    legend: { display: true, labels: { font: { size: 12, weight: 'bold' }, padding: 15 } },
                    tooltip: { callbacks: { label: function(context) { return 'Total Issues: ' + context.parsed.y; } } }
                },
                scales: {
                    x: { ticks: { font: { size: 11, weight: 'bold' } }, grid: { display: true, drawBorder: true } },
                    y: { beginAtZero: true, ticks: { font: { size: 12, weight: 'bold' } }, grid: { display: true } }
                }
            }
        });
    </script>
@endpush
