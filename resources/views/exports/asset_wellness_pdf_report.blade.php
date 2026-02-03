<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Asset Wellness</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #333; }
        .page { page-break-after: always; padding: 20px; min-height: 27.7cm; }
        .page:last-child { page-break-after: avoid; }
        .header-report { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #333; padding-bottom: 10px; }
        .header-report h1 { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .header-report p { font-size: 12px; margin-bottom: 3px; }
        .report-date { font-size: 11px; margin-top: 5px; font-style: italic; }
        
        .section-title { font-size: 14px; font-weight: bold; text-align: center; margin: 20px 0; background-color: #f0f0f0; padding: 8px; border-left: 4px solid #0066cc; border-right: 1px solid #ddd; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table thead { background-color: #333; color: white; }
        table th { padding: 6px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid #333; }
        table td { padding: 5px; border: 1px solid #ddd; font-size: 10px; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        
        .status-safe { background-color: #90EE90 !important; font-weight: bold; }
        .status-warning { background-color: #FFD700 !important; font-weight: bold; }
        .status-fault { background-color: #FF6B6B !important; color: white; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .summary { margin-top: 10px; padding: 10px; background-color: #f5f5f5; border: 1px solid #ddd; }
        .summary-title { font-weight: bold; margin-bottom: 10px; font-size: 11px; }
        .summary-item { display: block; border-bottom: 1px solid #ddd; padding: 3px 0; }
        .summary-item strong { float: right; }

        /* Map Styles */
        .map-table td { border: none !important; padding: 0; vertical-align: top; }
        .ul-box { border: 2px solid #5dade2; padding: 5px; margin: 5px; border-radius: 4px; background: white; width: 100%; }
        .ul-header { background: #5dade2; color: white; font-weight: bold; text-align: center; padding: 4px; font-size: 10px; margin-bottom: 5px; }
        .pltd-group { background: #f0f8ff; padding: 4px; margin-bottom: 5px; border-left: 2px solid #5dade2; }
        .pltd-name { font-weight: bold; font-size: 9px; margin-bottom: 3px; border-bottom: 1px dashed #ccc; }
        .machine-item { font-size: 8px; padding: 2px 4px; margin-bottom: 2px; border-radius: 2px; }
        .machine-safe { background: #e8f5e9; color: #2e7d32; border-left: 2px solid #4CAF50; }
        .machine-warning { background: #fffde7; color: #333; border-left: 2px solid #FFD700; }
        .machine-fault { background: #ffebee; color: #c62828; border-left: 2px solid #FF6B6B; }

        /* Vis Styles */
        .vis-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: #fff; page-break-inside: avoid; }
        .vis-card-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; text-align: center; color: #333; }
        .stat-box { padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 10px; border: 1px solid #eee; }
    </style>
</head>
<body>

    @php
        // Helper function data prep for Map
        $ulStructure = [
            'UL NUNUKAN' => [
                'PLTD Kuala Lapang' => ['1001', '1002', '1004', '1005', '1007', '1015'],
                'PLTD Sei Bilal' => ['2001', '2002', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014'],
                'PLTD Sebatik' => ['3001', '3003', '3006'],
                'PLTD Tulin Onsoi' => ['4001', '4002', '4003', '4004', '4005', '4006', '4007', '4021'],
            ],
            'UL TANJUNG SELOR' => [
                'PLTD Sambaliung' => ['5001', '5005', '5006', '5007', '5010', '5011', '5012', '5015'],
                'PLTD Sei Buaya' => ['6002', '6007', '6008', '6009'],
                'PLTD Bunyu' => [],
                'PLTD Talisayan' => ['7001', '7002', '7003', '7005'],
            ],
            'UL TARAKAN' => [
                'PLTMG GN Belah' => [],
            ],
            'UL BALIKPAPAN' => [
                'PLTD Batakan' => ['1001', '1002'],
                'PLTD Gunung Malang' => ['5004', '5008', '5009'],
                'PLTD Tj Aru' => ['3010', '3011', '8005', '8010', '8011'],
            ],
        ];

        $assetLookup = [];
        $assetLookupBySilm = [];
        foreach($assets as $asset) {
            $kodemesinnorm = strtoupper(preg_replace('/\s+/', '', trim($asset->kode_mesin)));
            $silmnorm = strtoupper(preg_replace('/\s+/', '', trim($asset->kode_mesin_silm ?? '')));
            if (!empty($kodemesinnorm)) $assetLookup[$kodemesinnorm] = $asset;
            if (!empty($silmnorm)) $assetLookupBySilm[$silmnorm] = $asset;
        }

        // Reuse strict logic
        if (!function_exists('findMachinePdf')) {
            function findMachinePdf($machineName, $assetLookup, $assetLookupBySilm) {
                $searchNorm = strtoupper(preg_replace('/\s+/', '', trim($machineName)));
                if (isset($assetLookup[$searchNorm])) return $assetLookup[$searchNorm];
                if (isset($assetLookupBySilm[$searchNorm])) return $assetLookupBySilm[$searchNorm];
                foreach ($assetLookup as $key => $asset) {
                    if (strpos($key, $searchNorm) !== false || strpos($searchNorm, $key) !== false) return $asset;
                }
                if (preg_match('/\d+$/', $searchNorm, $matches)) {
                    $trailingDigits = $matches[0];
                    foreach ($assetLookup as $key => $asset) {
                        if (preg_match('/' . preg_quote($trailingDigits) . '$/', $key)) return $asset;
                    }
                }
                return null;
            }
        }
    @endphp

    <!-- PAGE 1: FORM PENYAMPAIAN -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PT PLN NUSANTARA POWER / PLN INDONESIA POWER</p>
            <div class="report-date">Tanggal Pelaporan: {{ now()->format('d-m-Y') }}</div>
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
                    <th style="width: 6%; text-align: center;">% Safe</th>
                    <th style="width: 6%; text-align: center;">% Warn</th>
                    <th style="width: 6%; text-align: center;">% Fault</th>
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

        <!-- Summary -->
        <div class="summary">
            <div class="summary-title" style="border-bottom: 2px solid #ccc; padding-bottom:5px; margin-bottom:5px;">Ringkasan Statistik</div>
            <div style="width: 100%; overflow: hidden;">
                <div style="float: left; width: 48%;">
                    <div class="summary-item">Total Equipment: <strong>{{ $assets->sum('total_equipment') }}</strong></div>
                    <div class="summary-item">Equipment SAFE: <strong style="color:green">{{ $assets->sum('safe') }}</strong></div>
                </div>
                <div style="float: right; width: 48%;">
                    <div class="summary-item">Equipment WARNING: <strong style="color:orange">{{ $assets->sum('warning') }}</strong></div>
                    <div class="summary-item">Equipment FAULT: <strong style="color:red">{{ $assets->sum('fault') }}</strong></div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- PAGE 2: PETA MESIN -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>PETA KESEHATAN MESIN</p>
            <div class="report-date">Tanggal Pelaporan: {{ now()->format('d-m-Y') }}</div>
        </div>

        <div class="section-title">Peta Kesehatan Mesin</div>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="display:inline-block; background:#FFB84D; color:white; padding:5px 15px; font-weight:bold; border-radius:4px;">UP KALTIMRA</div>
        </div>

        <!-- Legend -->
        <div style="text-align:center; font-size:9px; margin-bottom:15px; border:1px solid #ddd; padding:5px; display:inline-block; width:100%;">
            <span style="background:#4CAF50; width:10px; height:10px; display:inline-block; margin-right:3px;"></span> SAFE
            <span style="background:#FFD700; width:10px; height:10px; display:inline-block; margin:0 3px 0 10px;"></span> WARNING
            <span style="background:#FF6B6B; width:10px; height:10px; display:inline-block; margin:0 3px 0 10px;"></span> FAULT
        </div>

        <table class="map-table">
            <tr>
                @foreach($ulStructure as $ulName => $pltdGroups)
                <td style="width: 25%; padding: 0 5px;">
                    <div class="ul-box">
                        <div class="ul-header">{{ $ulName }}</div>
                        @foreach($pltdGroups as $pltdName => $machineNames)
                            <div class="pltd-group">
                                <div class="pltd-name">{{ $pltdName }}</div>
                                @forelse($machineNames as $machineName)
                                    @php
                                        $machine = findMachinePdf($machineName, $assetLookup, $assetLookupBySilm);
                                        $statusClass = 'machine-safe'; 
                                        $statusIcon = '🟢';
                                        if ($machine) {
                                            if ($machine->fault > 0) { $statusClass = 'machine-fault'; $statusIcon='🔴'; }
                                            elseif ($machine->warning > 0) { $statusClass = 'machine-warning'; $statusIcon='🟡'; }
                                        }
                                        $displayName = $machine ? ($machine->kode_mesin_silm ?: $machineName) : $machineName;
                                    @endphp
                                    <div class="machine-item {{ $statusClass }}">
                                        {{ $displayName }}
                                    </div>
                                @empty
                                    <div style="font-size:7px; color:#999;">-</div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    <!-- PAGE 3: DETAIL WARNING -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p style="color: #FF8C00;">ASSET WELLNESS - DETAIL WARNING</p>
        </div>
        <div class="section-title">Detail Warning</div>
        
        @if($detailWarnings->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:5%">NO</th>
                    <th>UNIT</th>
                    <th style="width:12%">TGL IDENTIFIKASI</th>
                    <th>STATUS</th>
                    <th>DESKRIPSI</th>
                    <th>KONDISI</th>
                    <th>ACTION PLAN</th>
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
        @else
        <div class="no-data">Tidak ada data Detail Warning.</div>
        @endif
    </div>

    <!-- PAGE 4: DETAIL FAULT -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p style="color: #FF0000;">ASSET WELLNESS - DETAIL FAULT</p>
        </div>
        <div class="section-title">Detail Fault</div>

        @if($detailFaults->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:5%">NO</th>
                    <th>UNIT</th>
                    <th style="width:12%">TGL IDENTIFIKASI</th>
                    <th>STATUS</th>
                    <th>DESKRIPSI</th>
                    <th>KONDISI</th>
                    <th>ACTION PLAN</th>
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
        @else
        <div class="no-data">Tidak ada data Detail Fault.</div>
        @endif
    </div>

    <!-- PAGE 5: VISUALISASI DATA (NEW) -->
    <div class="page">
        <div class="header-report">
            <h1>Laporan Bulanan Asset Wellness</h1>
            <p>VISUALISASI DATA</p>
        </div>
        <div class="section-title">Visualisasi Status Equipment</div>

        <!-- Stats Boxes -->
         <div style="margin-bottom: 20px; overflow: hidden; padding: 10px;">
            <table style="width: 100%; border: none;">
                <tr style="background: white;">
                    <td style="border: none; width: 33%; padding: 5px;">
                        <div class="stat-box" style="background-color: #e8f5e9; border: 2px solid #90EE90;">
                            <div style="font-size: 14px; font-weight: bold; color: #2e7d32; margin-bottom: 5px;">SAFE</div>
                            <div style="font-size: 24px; font-weight: bold;">{{ $totalSafe }}</div>
                            <div style="font-size: 10px;">Equipment</div>
                        </div>
                    </td>
                    <td style="border: none; width: 33%; padding: 5px;">
                        <div class="stat-box" style="background-color: #fffde7; border: 2px solid #FFD700;">
                            <div style="font-size: 14px; font-weight: bold; color: #f57f17; margin-bottom: 5px;">WARNING</div>
                            <div style="font-size: 24px; font-weight: bold;">{{ $totalWarning }}</div>
                            <div style="font-size: 10px;">Equipment</div>
                        </div>
                    </td>
                    <td style="border: none; width: 33%; padding: 5px;">
                        <div class="stat-box" style="background-color: #ffebee; border: 2px solid #FF6B6B;">
                            <div style="font-size: 14px; font-weight: bold; color: #c62828; margin-bottom: 5px;">FAULT</div>
                            <div style="font-size: 24px; font-weight: bold;">{{ $totalFault }}</div>
                            <div style="font-size: 10px;">Equipment</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Charts -->
        <table style="width: 100%; border: none; margin-top: 10px;">
            <tr style="background: white;">
                <td style="border: none; width: 50%; padding: 10px; vertical-align: top;">
                    <div class="vis-card">
                        <div class="vis-card-title">Distribusi Status</div>
                        <div style="text-align: center;">
                            @if(isset($pieChartBase64) && $pieChartBase64)
                                <img src="{{ $pieChartBase64 }}" style="width: 100%; max-width: 300px; height: auto;">
                            @else
                                <p style="color: #999; font-style: italic; padding: 20px;">Chart tidak dapat dimuat (Koneksi)</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="border: none; width: 50%; padding: 10px; vertical-align: top;">
                    <div class="vis-card">
                        <div class="vis-card-title">Tren Bulanan (Warning + Fault)</div>
                        <div style="text-align: center;">
                            @if(isset($barChartBase64) && $barChartBase64)
                                <img src="{{ $barChartBase64 }}" style="width: 100%; max-width: 300px; height: auto;">
                            @else
                                <p style="color: #999; font-style: italic; padding: 20px;">Chart tidak dapat dimuat (Koneksi)</p>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
