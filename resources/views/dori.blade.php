<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DORI - Dokumen Terintegrasi</title>
    <link rel="stylesheet" href="{{ asset('css/dori.css') }}">
</head>
<body>
    <!-- Header -->
    <div class="dori-header">
        <div class="dori-header-logo">
            <img src="{{ asset('images/logo_pln.png') }}" alt="PLN Logo" class="logo-pln">
        </div>
        <div class="dori-header-icons">
            <img src="{{ asset('images/akun.png') }}" alt="Profile" class="profile-icon">
        </div>
    </div>

    <!-- Main Container -->
    <div class="dori-container">
        <!-- Sidebar -->
        <div class="dori-sidebar">
            <div class="dori-sidebar-item">Worksheet System Owner</div>
            <div class="dori-sidebar-item">Laporan Lintas Bidang</div>
            <div class="dori-sidebar-item">Program Kerja SO</div>
            <div class="dori-sidebar-item">LCCM</div>
            <div class="dori-sidebar-item">Design Review</div>
            <div class="dori-sidebar-item">Peta Improvement</div>
            <div class="dori-sidebar-item">ECP</div>
            <div class="dori-sidebar-item">PKU</div>
            <div class="dori-sidebar-item">RCFA</div>
            <div class="dori-sidebar-item">RJPU</div>
            <div class="dori-sidebar-item">MPI</div>
            <div class="dori-sidebar-item">MATERI</div>
        </div>

        <!-- Main Content -->
        <div class="dori-main">
            <h1 class="dori-title">Welcome To<br>Dokumen Terintegrasi (DORI)</h1>
            
            <div class="dori-grid">
                <!-- Repeat cards 12 times for the layout shown -->
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📋</span>
                        <span>Worksheet System Owner</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📊</span>
                        <span>Laporan Lintas Bidang</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📈</span>
                        <span>Program Kerja SO</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">🔧</span>
                        <span>LCCM</span>
                    </div>
                </div>

                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">✏️</span>
                        <span>Design Review</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">🗺️</span>
                        <span>Peta Improvement</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">🎯</span>
                        <span>ECP</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📝</span>
                        <span>PKU</span>
                    </div>
                </div>

                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">🔍</span>
                        <span>RCFA</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📉</span>
                        <span>RJPU</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📊</span>
                        <span>MPI</span>
                    </div>
                </div>
                <div class="dori-card">
                    <div class="dori-card-content">
                        <span class="dori-card-icon">📚</span>
                        <span>MATERI</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/dori.js') }}"></script>
</body>
</html>
