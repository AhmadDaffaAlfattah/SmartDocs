<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kalender - DORI</title>
    <link rel="stylesheet" href="{{ asset('css/dori.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
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
        <div class="dori-main calendar-main">
            <div class="calendar-container">
                <!-- Calendar Section -->
                <div class="calendar-section">
                    <div class="calendar-header">
                        <div class="calendar-navigation">
                            <button id="prevBtn" class="nav-btn">← Sebelumnya</button>
                            <h2 id="monthYear"></h2>
                            <button id="nextBtn" class="nav-btn">Berikutnya →</button>
                        </div>

                        <div class="year-selector">
                            <label for="yearSelect">Tahun:</label>
                            <select id="yearSelect">
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                            </select>
                        </div>
                    </div>

                    <div class="calendar-wrapper">
                        <table class="calendar">
                            <thead>
                                <tr>
                                    <th>Ming</th>
                                    <th>Sen</th>
                                    <th>Sel</th>
                                    <th>Rab</th>
                                    <th>Kam</th>
                                    <th>Jum</th>
                                    <th>Sab</th>
                                </tr>
                            </thead>
                            <tbody id="calendarBody">
                                <!-- Filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #ffffff; border: 1px solid #333;"></span>
                            <span>Hari Biasa</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #ff0000;"></span>
                            <span>Hari Libur</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #0066ff;"></span>
                            <span>Hari Ini</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #ffff00;"></span>
                            <span>Ada Reminder</span>
                        </div>
                    </div>
                </div>

                <!-- Reminder Section -->
                <div class="reminder-section">
                    <h3>Reminder</h3>
                    
                    <div class="reminder-input-section">
                        <input type="date" id="reminderDate" class="reminder-input">
                        <input type="text" id="reminderTitle" class="reminder-input" placeholder="Judul reminder">
                        <textarea id="reminderDescription" class="reminder-input" placeholder="Deskripsi (opsional)" rows="3"></textarea>
                        <button id="addReminderBtn" class="btn-add-reminder">+ Tambah Reminder</button>
                    </div>

                    <div id="reminderList" class="reminder-list">
                        <!-- Reminders will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/calendar.js') }}"></script>
</body>
</html>
