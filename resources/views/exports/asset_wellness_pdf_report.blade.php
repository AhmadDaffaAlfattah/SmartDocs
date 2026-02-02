<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Asset Wellness</title>
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

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .page-title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0 20px 0;
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #999;
        }
            background-color: #f0f0f0;
            padding: 8px;
            border-left: 4px solid #0066cc;
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
        table tbody tr:hover {
            background-color: #f0f0f0;
        }
        .status-safe {
            background-color: #90EE90 !important;
            font-weight: bold;
        }
        .status-warning {
            background-color: #FFD700 !important;
            font-weight: bold;
        }
        .status-fault {
            background-color: #FF6B6B !important;
            color: white;
            font-weight: bold;
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
        .chart-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .chart-container svg {
            max-width: 100%;
            height: auto;
        }
        @media print {
            .page {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: FORM PENYAMPAIAN / ASSET WELLNESS -->
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
                    <th style="width: 6%;">TOTAL</th>
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
                    <td class="text-center {{ $safe > 0 ? 'status-safe' : '' }}">{{ $safe }}</td>
                    <td class="text-center {{ $warning > 0 && $fault == 0 ? 'status-warning' : '' }}">{{ $warning }}</td>
                    <td class="text-center {{ $fault > 0 ? 'status-fault' : '' }}">{{ $fault }}</td>
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

        <!-- CHART VISUALIZATION - Horizontal Stacked Bar Chart -->
        @php
            $totalSafe = $assets->sum('safe');
            $totalWarning = $assets->sum('warning');
            $totalFault = $assets->sum('fault');
            $grandTotal = $totalSafe + $totalWarning + $totalFault;
            
            // Calculate percentages
            $pctSafe = $grandTotal > 0 ? ($totalSafe / $grandTotal) * 100 : 0;
            $pctWarning = $grandTotal > 0 ? ($totalWarning / $grandTotal) * 100 : 0;
            $pctFault = $grandTotal > 0 ? ($totalFault / $grandTotal) * 100 : 0;
        @endphp
        
        <div class="chart-container" style="margin: 20px 0; padding: 0 10px;">
            <!-- Stacked Bar Chart using Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <tr style="height: 40px;">
                    @if($pctSafe > 0)
                    <td style="width: {{ $pctSafe }}%; background-color: #90EE90; text-align: center; vertical-align: middle; font-weight: bold; font-size: 12px; border: 1px solid #999;">
                        {{ round($pctSafe, 1) }}%
                    </td>
                    @endif
                    @if($pctWarning > 0)
                    <td style="width: {{ $pctWarning }}%; background-color: #FFD700; text-align: center; vertical-align: middle; font-weight: bold; font-size: 12px; border: 1px solid #999;">
                        {{ round($pctWarning, 1) }}%
                    </td>
                    @endif
                    @if($pctFault > 0)
                    <td style="width: {{ $pctFault }}%; background-color: #FF6B6B; text-align: center; vertical-align: middle; font-weight: bold; font-size: 12px; color: white; border: 1px solid #999;">
                        {{ round($pctFault, 1) }}%
                    </td>
                    @endif
                </tr>
            </table>
            
            <!-- Legend -->
            <div style="text-align: center; font-size: 10px; margin-top: 12px;">
                <div style="display: inline-block; margin: 0 15px;">
                    <span style="display: inline-block; width: 16px; height: 16px; background-color: #90EE90; margin-right: 6px; vertical-align: middle;"></span>
                    <span style="vertical-align: middle;">SAFE: {{ $totalSafe }} ({{ round($pctSafe, 1) }}%)</span>
                </div>
                <div style="display: inline-block; margin: 0 15px;">
                    <span style="display: inline-block; width: 16px; height: 16px; background-color: #FFD700; margin-right: 6px; vertical-align: middle;"></span>
                    <span style="vertical-align: middle;">WARNING: {{ $totalWarning }} ({{ round($pctWarning, 1) }}%)</span>
                </div>
                <div style="display: inline-block; margin: 0 15px;">
                    <span style="display: inline-block; width: 16px; height: 16px; background-color: #FF6B6B; margin-right: 6px; vertical-align: middle;"></span>
                    <span style="vertical-align: middle;">FAULT: {{ $totalFault }} ({{ round($pctFault, 1) }}%)</span>
                </div>
            </div>
        </div>
        {{-- <div class="no-data">
            Tidak ada data Form Penyampaian untuk periode yang dipilih
        </div> --}}
        @endif

        {{-- <div class="footer">
            Halaman 1 dari 3 - Form Penyampaian Asset Wellness
        </div> --}}
    </div>

    <!-- HALAMAN 2: PETA KESEHATAN UNIT -->
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

        <div style="padding: 20px; text-align: center;">
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td style="padding: 12px; border: 1px solid #999; text-align: center; width: 33%;">
                        <div style="font-size: 14px; margin-bottom: 8px;">🟢 SAFE</div>
                        <div style="font-size: 18px; font-weight: bold; color: #228B22;">{{ $assets->sum('safe') }} Unit</div>
                    </td>
                    <td style="padding: 12px; border: 1px solid #999; text-align: center; width: 33%;">
                        <div style="font-size: 14px; margin-bottom: 8px;">🟡 WARNING</div>
                        <div style="font-size: 18px; font-weight: bold; color: #FF8C00;">{{ $assets->sum('warning') }} Unit</div>
                    </td>
                    <td style="padding: 12px; border: 1px solid #999; text-align: center; width: 33%;">
                        <div style="font-size: 14px; margin-bottom: 8px;">🔴 FAULT</div>
                        <div style="font-size: 18px; font-weight: bold; color: #CC0000;">{{ $assets->sum('fault') }} Unit</div>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin: 30px 0;">
                <thead>
                    <tr style="background-color: #333; color: white;">
                        <th style="padding: 10px; border: 1px solid #333; font-size: 11px;">SENTRAL</th>
                        <th style="padding: 10px; border: 1px solid #333; font-size: 11px;">UNIT</th>
                        <th style="padding: 10px; border: 1px solid #333; font-size: 11px;">TOTAL</th>
                        <th style="padding: 10px; border: 1px solid #333; font-size: 11px; background-color: #90EE90; color: #000;">SAFE</th>
                        <th style="padding: 10px; border: 1px solid #333; font-size: 11px; background-color: #FFD700; color: #000;">WARNING</th>
                        <th style="padding: 10px; border: 1px solid #333; font-size: 11px; background-color: #FF6B6B; color: white;">FAULT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets->groupBy('sentral') as $sentral => $sentralAssets)
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td colspan="6" style="padding: 8px; border: 1px solid #ddd; background-color: #e8e8e8;">{{ $sentral ?? 'N/A' }}</td>
                    </tr>
                    @foreach($sentralAssets as $asset)
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ddd; font-size: 10px;"></td>
                        <td style="padding: 6px; border: 1px solid #ddd; font-size: 10px;">{{ $asset->unit_pembangkit_common ?? '-' }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd; font-size: 10px; text-align: center;">{{ $asset->total_equipment ?? 0 }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd; font-size: 10px; text-align: center; background-color: #f0f0f0;">{{ $asset->safe ?? 0 }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd; font-size: 10px; text-align: center; background-color: #f0f0f0;">{{ $asset->warning ?? 0 }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd; font-size: 10px; text-align: center; background-color: #f0f0f0;">{{ $asset->fault ?? 0 }}</td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
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
        {{-- <div class="no-data">
            Tidak ada data Detail Warning untuk periode yang dipilih
        </div> --}}
        @endif

        {{-- <div class="footer">
            Halaman 2 dari 3 - Detail Asset Wellness dengan Status WARNING
        </div> --}}
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
        <div class="no-data">
            Tidak ada data Detail Fault untuk periode yang dipilih
        </div>
        @endif

    <!-- HALAMAN 5: VISUALISASI DATA -->
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

        @php
            $totalSafe = $assets->sum('safe');
            $totalWarning = $assets->sum('warning');
            $totalFault = $assets->sum('fault');
            $grandTotal = $totalSafe + $totalWarning + $totalFault;
            
            $pctSafe = $grandTotal > 0 ? ($totalSafe / $grandTotal) * 100 : 0;
            $pctWarning = $grandTotal > 0 ? ($totalWarning / $grandTotal) * 100 : 0;
            $pctFault = $grandTotal > 0 ? ($totalFault / $grandTotal) * 100 : 0;
        @endphp

        <!-- Summary Statistics Cards -->
        <div style="margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 25%; padding: 15px; border: 2px solid #90EE90; text-align: center;">
                        <div style="font-weight: bold; color: #228B22; font-size: 12px;">🟢 EQUIPMENT SAFE</div>
                        <div style="font-size: 16px; font-weight: bold; color: #228B22; margin: 8px 0;">{{ $totalSafe }}</div>
                        <div style="font-size: 11px; color: #666;">{{ round($pctSafe, 2) }}%</div>
                    </td>
                    <td style="width: 25%; padding: 15px; border: 2px solid #FFD700; text-align: center;">
                        <div style="font-weight: bold; color: #FF8C00; font-size: 12px;">🟡 EQUIPMENT WARNING</div>
                        <div style="font-size: 16px; font-weight: bold; color: #FF8C00; margin: 8px 0;">{{ $totalWarning }}</div>
                        <div style="font-size: 11px; color: #666;">{{ round($pctWarning, 2) }}%</div>
                    </td>
                    <td style="width: 25%; padding: 15px; border: 2px solid #FF6B6B; text-align: center;">
                        <div style="font-weight: bold; color: #CC0000; font-size: 12px;">🔴 EQUIPMENT FAULT</div>
                        <div style="font-size: 16px; font-weight: bold; color: #CC0000; margin: 8px 0;">{{ $totalFault }}</div>
                        <div style="font-size: 11px; color: #666;">{{ round($pctFault, 2) }}%</div>
                    </td>
                    <td style="width: 25%; padding: 15px; border: 2px solid #667eea; text-align: center;">
                        <div style="font-weight: bold; color: #667eea; font-size: 12px;">📈 TOTAL EQUIPMENT</div>
                        <div style="font-size: 16px; font-weight: bold; color: #667eea; margin: 8px 0;">{{ $grandTotal }}</div>
                        <div style="font-size: 11px; color: #666;">100%</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Progress Bars -->
        <div style="margin: 30px 0;">
            <div style="margin-bottom: 15px;">
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 11px;">SAFE (Aman)</div>
                <div style="background-color: #f0f0f0; height: 20px; border-radius: 4px; overflow: hidden; border: 1px solid #ddd;">
                    <div style="background-color: #90EE90; height: 100%; width: {{ $pctSafe }}%; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 10px; font-weight: bold; color: #000;">{{ round($pctSafe, 1) }}%</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 11px;">WARNING (Perlu Perhatian)</div>
                <div style="background-color: #f0f0f0; height: 20px; border-radius: 4px; overflow: hidden; border: 1px solid #ddd;">
                    <div style="background-color: #FFD700; height: 100%; width: {{ $pctWarning }}%; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 10px; font-weight: bold; color: #000;">{{ round($pctWarning, 1) }}%</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 11px;">FAULT (Rusak/Gangguan)</div>
                <div style="background-color: #f0f0f0; height: 20px; border-radius: 4px; overflow: hidden; border: 1px solid #ddd;">
                    <div style="background-color: #FF6B6B; height: 100%; width: {{ $pctFault }}%; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 10px; font-weight: bold; color: white;">{{ round($pctFault, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Issues Chart -->
        <div style="margin-top: 30px;">
            <div style="font-weight: bold; margin-bottom: 10px; font-size: 12px;">TREN MONTHLY - WARNING + FAULT (Bulan {{ $bulanList[$bulan] ?? $bulan }}, Tahun {{ $tahun }})</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <tr style="background-color: #f0f0f0; border: 1px solid #ddd;">
                    @foreach($monthlyIssues as $month => $count)
                    <td style="text-align: center; padding: 8px; border-right: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold;">{{ substr($month, 0, 3) }}</div>
                        <div style="margin-top: 5px; font-size: 14px; font-weight: bold; color: #FF7B54;">{{ $count }}</div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>

        <div class="summary" style="margin-top: 30px;">
            <div class="summary-title">Kesimpulan</div>
            <div style="font-size: 10px; line-height: 1.6; color: #333;">
                Berdasarkan data yang dikumpulkan untuk bulan {{ $bulanList[$bulan] ?? $bulan }} tahun {{ $tahun }}, 
                status kesehatan equipment secara keseluruhan menunjukkan:
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>{{ $totalSafe }} unit ({{ round($pctSafe, 1) }}%) dalam kondisi SAFE</li>
                    <li>{{ $totalWarning }} unit ({{ round($pctWarning, 1) }}%) dalam status WARNING</li>
                    <li>{{ $totalFault }} unit ({{ round($pctFault, 1) }}%) dalam status FAULT</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
