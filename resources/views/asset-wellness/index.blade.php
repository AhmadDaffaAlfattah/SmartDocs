@extends('layouts.master')

@section('title', 'SmartDocs - Kesehatan Mesin')

@push('styles')
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
            font-weight: 600;
            text-align: center;
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
    </style>
@endpush

@section('content')
    <div class="asset-container">
        <div class="asset-header">
            <div>
                <h1> Kesehatan Mesin</h1>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;"> Asset Wellness</p>
            </div>
        </div>

        @if($message = session('success'))
            <div class="alert-success">✅ {{ $message }}</div>
        @endif

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

            <div class="button-group" style="margin-left: auto;">
                <div class="btn-download" onclick="toggleDownloadMenu(event)" style="position: relative;">
                    📥 Download
                    <div id="downloadMenu" class="dropdown-menu">
                        <a href="{{ route('asset-wellness.download', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral, 'format' => 'excel']) }}" onclick="event.stopPropagation();">📊 Download Excel</a>
                        <a href="{{ route('asset-wellness.download', ['tahun' => $tahun, 'bulan' => $bulan, 'sentral' => $sentral, 'format' => 'pdf']) }}" onclick="event.stopPropagation();">📄 Download PDF</a>
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
                        <th style="width: 150px;">KODE MESIN UNIT PEMBANGKIT (SILM)</th>
                        <th style="width: 180px;">UNIT PEMBANGKIT/COMMON</th>
                        <th style="text-align: center; width: 100px;">Daya Terpasang (MW)</th>
                        <th style="text-align: center; width: 110px;">Daya Mampu Netto (MW)</th>
                        <th style="text-align: center; width: 110px;">Daya Mampu Pasok (MW)</th>
                        <th style="text-align: center; width: 100px;">Total Equipment</th>
                        <th style="text-align: center; width: 90px; background: #90EE90;">Equipment Safe</th>
                        <th style="text-align: center; width: 90px; background: #FFD700;">Equipment Warning</th>
                        <th style="text-align: center; width: 90px; background: #FF6B6B;">Equipment Fault</th>
                        <th style="width: 200px;">Keterangan</th>
                        <th style="text-align: center; width: 150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $key => $asset)
                        <tr>
                            <td style="text-align: center;">{{ $key + 1 }}</td>
                            <td><strong>{{ $asset->sentral ?? '-' }}</strong></td>
                            <td>{{ $asset->tipe_aset ?? '-' }}</td>
                            <td>{{ $asset->kode_mesin_silm ?? '-' }}</td>
                            <td>{{ $asset->unit_pembangkit_common }}</td>
                            <td style="text-align: center;">{{ $asset->daya_terpasang ?? '-' }}</td>
                            <td style="text-align: center;">{{ $asset->daya_mampu_netto ?? '-' }}</td>
                            <td style="text-align: center;">{{ $asset->daya_mampu_pasok ?? '-' }}</td>
                            <td style="text-align: center;">{{ $asset->total_equipment }}</td>
                            @php
                                // Logika prioritas warna - hanya kolom yang relevan
                                if ($asset->fault > 0) {
                                    $kelasWarna = 'col-fault-danger'; // Merah - Prioritas 1
                                } elseif ($asset->warning > 0) {
                                    $kelasWarna = 'col-warning'; // Kuning - Prioritas 2
                                } else {
                                    $kelasWarna = 'col-safe'; // Hijau - Prioritas 3
                                }
                            @endphp
                            <td class="{{ $kelasWarna == 'col-safe' ? 'col-safe' : '' }}" style="text-align: center;">{{ $asset->safe }}</td>
                            <td class="{{ $kelasWarna == 'col-warning' ? 'col-warning' : '' }}" style="text-align: center;">{{ $asset->warning }}</td>
                            <td class="{{ $kelasWarna == 'col-fault-danger' ? 'col-fault-danger' : '' }}" style="text-align: center;">{{ $asset->fault }}</td>
                            <td>{{ $asset->keterangan ?? '-' }}</td>
                            <td style="text-align: center; display: flex; justify-content: center; gap: 10px;">
                                <button class="action-btn edit" title="Edit" onclick="window.location.href='{{ route('asset-wellness.edit', $asset->id) }}'">
                                    <img src="https://cdn-icons-png.flaticon.com/128/14034/14034493.png" alt="Edit" title="Edit" width="32" height="32">
                                </button>
                                <form action="{{ route('asset-wellness.destroy', $asset->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Delete" style="border: none; background: none; cursor: pointer;">
                                        <img src="https://cdn-icons-png.flaticon.com/128/4980/4980658.png" loading="lazy" alt="Delete" title="Delete" width="32" height="32">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" style="padding: 20px; text-align: center; color: #999;">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDownloadMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('downloadMenu');
            menu.classList.toggle('active');
        }

        document.addEventListener('click', function() {
            const menu = document.getElementById('downloadMenu');
            if (menu) {
                menu.classList.remove('active');
            }
        });
    </script>
@endpush
