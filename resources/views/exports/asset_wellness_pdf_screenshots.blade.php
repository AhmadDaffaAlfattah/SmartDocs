<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Asset Wellness dengan Screenshots</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .page {
            page-break-after: always;
            padding: 20px;
            min-height: 27.7cm;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .header-report {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header-report h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-report p {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .report-date {
            font-size: 10px;
            font-weight: bold;
            color: #666;
            margin-top: 5px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table thead {
            background-color: #333;
            color: white;
        }

        table th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #333;
        }

        table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .screenshot-container {
            margin: 20px 0;
            text-align: center;
        }

        .screenshot-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }

        @media print {
            .page {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: FORM PENYAMPAI -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PT PLN NUSANTARA POWER / PLN INDONESIA POWER</p>
            <div class="report-date">
                Tanggal Pelaporan: {{ now()->format('d-m-Y') }}
            </div>
        </div>

        <div class="section-title">Form Penyampaian</div>

        @if($assets->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">NO</th>
                    <th style="width: 8%;">SENTRAL</th>
                    <th style="width: 8%;">TIPE ASET</th>
                    <th style="width: 7%;">KODE MESIN</th>
                    <th style="width: 10%;">UNIT PEMBANGKIT</th>
                    <th style="width: 6%; text-align: center;">TOTAL</th>
                    <th style="width: 5%; text-align: center;">SAFE</th>
                    <th style="width: 5%; text-align: center;">WARN</th>
                    <th style="width: 5%; text-align: center;">FAULT</th>
                    <th style="width: 6%; text-align: center;">% SAFE</th>
                    <th style="width: 6%; text-align: center;">% WARN</th>
                    <th style="width: 6%; text-align: center;">% FAULT</th>
                    <th style="width: 15%;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $index => $asset)
                @php
                    $total = $asset->total_equipment ?? 0;
                    $safe = $asset->safe ?? 0;
                    $warning = $asset->warning ?? 0;
                    $fault = $asset->fault ?? 0;
                    
                    $pct_safe = $total > 0 ? round(($safe / $total) * 100, 1) : 0;
                    $pct_warning = $total > 0 ? round(($warning / $total) * 100, 1) : 0;
                    $pct_fault = $total > 0 ? round(($fault / $total) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $asset->sentral ?? '-' }}</td>
                    <td>{{ $asset->tipe_aset ?? '-' }}</td>
                    <td>{{ $asset->kode_mesin ?? '-' }}</td>
                    <td>{{ $asset->unit_pembangkit_common ?? '-' }}</td>
                    <td class="text-center">{{ $total }}</td>
                    <td class="text-center" style="background-color: #90EE90;">{{ $safe }}</td>
                    <td class="text-center" style="background-color: #FFD700;">{{ $warning }}</td>
                    <td class="text-center" style="background-color: #FF6B6B; color: white;">{{ $fault }}</td>
                    <td class="text-center">{{ $pct_safe }}%</td>
                    <td class="text-center">{{ $pct_warning }}%</td>
                    <td class="text-center">{{ $pct_fault }}%</td>
                    <td>{{ $asset->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-title">Ringkasan Statistik</div>
            <div class="summary-item">
                <span>Total Equipment:</span>
                <span><strong>{{ $assets->sum('total_equipment') }}</strong></span>
            </div>
            <div class="summary-item">
                <span>Equipment SAFE:</span>
                <span><strong>{{ $assets->sum('safe') }}</strong></span>
            </div>
            <div class="summary-item">
                <span>Equipment WARNING:</span>
                <span><strong>{{ $assets->sum('warning') }}</strong></span>
            </div>
            <div class="summary-item">
                <span>Equipment FAULT:</span>
                <span><strong>{{ $assets->sum('fault') }}</strong></span>
            </div>
        </div>
        @endif
    </div>

    <!-- HALAMAN 2: PETA KESEHATAN (SCREENSHOT) -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PT PLN NUSANTARA POWER / PLN INDONESIA POWER</p>
            <p style="font-weight: bold; color: #0066cc;">PETA KESEHATAN UNIT PEMBANGKIT</p>
            <div class="report-date">
                Tanggal Pelaporan: {{ now()->format('d-m-Y') }}
            </div>
        </div>

        <div class="section-title">Peta Kesehatan Unit Pembangkit</div>

        <div class="screenshot-container">
            @if($petaImage)
                <img src="{{ $petaImage }}" style="max-height: 600px;" alt="Peta Kesehatan">
            @else
                <div class="no-data">Gambar Peta Kesehatan tidak tersedia</div>
            @endif
        </div>
    </div>

    <!-- HALAMAN 3: DETAIL WARNING -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PT PLN NUSANTARA POWER / PLN INDONESIA POWER</p>
            <p style="font-weight: bold; color: #FF8C00;">ASSET WELLNESS DENGAN STATUS WARNING</p>
            <div class="report-date">
                Tanggal Pelaporan: {{ now()->format('d-m-Y') }}
            </div>
        </div>

        <div class="section-title">Detail Warning</div>

        @if($detailWarnings->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 12%;">UNIT PEMBANGKIT</th>
                    <th style="width: 12%;">TANGGAL IDENTIFIKASI</th>
                    <th style="width: 15%;">STATUS SAAT INI</th>
                    <th style="width: 15%;">DESKRIPSI ASET</th>
                    <th style="width: 15%;">KONDISI ASET</th>
                    <th style="width: 16%;">ACTION PLAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailWarnings as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->assetWellness->unit_pembangkit_common ?? $detail->unit_pembangkit ?? '-' }}</td>
                    <td class="text-center">{{ $detail->tanggal_identifikasi ? \Carbon\Carbon::parse($detail->tanggal_identifikasi)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $detail->status_saat_ini ?? '-' }}</td>
                    <td>{{ $detail->asset_description ?? '-' }}</td>
                    <td>{{ $detail->kondisi_aset ?? '-' }}</td>
                    <td>{{ $detail->action_plan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-title">Ringkasan Detail Warning</div>
            <div class="summary-item">
                <span>Total Warning Items:</span>
                <span><strong>{{ $detailWarnings->count() }}</strong></span>
            </div>
        </div>
        @else
        <div class="no-data">Tidak ada data Detail Warning untuk periode yang dipilih</div>
        @endif
    </div>

    <!-- HALAMAN 4: DETAIL FAULT -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PT PLN NUSANTARA POWER / PLN INDONESIA POWER</p>
            <p style="font-weight: bold; color: #FF0000;">ASSET WELLNESS DENGAN STATUS FAULT</p>
            <div class="report-date">
                Tanggal Pelaporan: {{ now()->format('d-m-Y') }}
            </div>
        </div>

        <div class="section-title">Detail Fault</div>

        @if($detailFaults->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 12%;">UNIT PEMBANGKIT</th>
                    <th style="width: 12%;">TANGGAL IDENTIFIKASI</th>
                    <th style="width: 15%;">STATUS SAAT INI</th>
                    <th style="width: 15%;">DESKRIPSI ASET</th>
                    <th style="width: 15%;">KONDISI ASET</th>
                    <th style="width: 16%;">ACTION PLAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailFaults as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->assetWellness->unit_pembangkit_common ?? $detail->unit_pembangkit ?? '-' }}</td>
                    <td class="text-center">{{ $detail->tanggal_identifikasi ? \Carbon\Carbon::parse($detail->tanggal_identifikasi)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $detail->status_saat_ini ?? '-' }}</td>
                    <td>{{ $detail->asset_description ?? '-' }}</td>
                    <td>{{ $detail->kondisi_aset ?? '-' }}</td>
                    <td>{{ $detail->action_plan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-title">Ringkasan Detail Fault</div>
            <div class="summary-item">
                <span>Total Fault Items:</span>
                <span><strong>{{ $detailFaults->count() }}</strong></span>
            </div>
        </div>
        @else
        <div class="no-data">Tidak ada data Detail Fault untuk periode yang dipilih</div>
        @endif
    </div>

    <!-- HALAMAN 5: VISUALISASI DATA (SCREENSHOT) -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PT PLN NUSANTARA POWER / PLN INDONESIA POWER</p>
            <p style="font-weight: bold; color: #667eea;">VISUALISASI DATA STATUS EQUIPMENT</p>
            <div class="report-date">
                Tanggal Pelaporan: {{ now()->format('d-m-Y') }}
            </div>
        </div>

        <div class="section-title">Visualisasi Data Status Equipment</div>

        <div class="screenshot-container">
            @if($visualisasiImage)
                <img src="{{ $visualisasiImage }}" style="max-height: 600px;" alt="Visualisasi Data">
            @else
                <div class="no-data">Gambar Visualisasi Data tidak tersedia</div>
            @endif
        </div>
    </div>

</body>
</html>
