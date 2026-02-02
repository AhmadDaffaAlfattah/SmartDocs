<!-- Chart Data for Excel -->
<!-- This view generates table data that will be formatted as chart data in Excel -->
<table>
    <tr>
        <th colspan="3" style="font-size: 14px; font-weight: bold;">STATISTIK ASSET WELLNESS</th>
    </tr>
    <tr>
        <th style="background-color: #90EE90; padding: 10px;">Status</th>
        <th style="background-color: #90EE90; padding: 10px;">Jumlah</th>
        <th style="background-color: #90EE90; padding: 10px;">Persentase</th>
    </tr>
    <tr>
        <td style="background-color: #90EE90; padding: 8px;">Equipment Safe</td>
        <td style="background-color: #90EE90; padding: 8px;">{{ $totalSafe }}</td>
        <td style="background-color: #90EE90; padding: 8px;">{{ $persen_safe }}%</td>
    </tr>
    <tr>
        <td style="background-color: #FFD700; padding: 8px;">Equipment Warning</td>
        <td style="background-color: #FFD700; padding: 8px;">{{ $totalWarning }}</td>
        <td style="background-color: #FFD700; padding: 8px;">{{ $persen_warning }}%</td>
    </tr>
    <tr>
        <td style="background-color: #FF6B6B; padding: 8px; color: white;">Equipment Fault</td>
        <td style="background-color: #FF6B6B; padding: 8px; color: white;">{{ $totalFault }}</td>
        <td style="background-color: #FF6B6B; padding: 8px; color: white;">{{ $persen_fault }}%</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 8px;">TOTAL</td>
        <td style="font-weight: bold; padding: 8px;">{{ $totalEquipment }}</td>
        <td style="font-weight: bold; padding: 8px;">100%</td>
    </tr>
</table>
