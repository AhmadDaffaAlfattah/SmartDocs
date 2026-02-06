@extends('layouts.master')

@section('title', 'SmartDocs - Dashboard')

@section('content')
    <!-- Title Section -->
    <div class="title-section">
        <div class="pln-logo-container">
            <img src="{{ asset('images/smartdocs.png') }}" alt="SmartDocs Logo" class="pln-logo-main">
            <div class="pln-text">
                <div class="pln-title"></div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'super admin')
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <img src="https://cdn-icons-png.flaticon.com/128/476/476761.png" alt="Team" width="32" height="32">
            </div>
            <div class="stat-content">
                <div class="stat-label">Total User</div>
                <div class="stat-value">{{ $totalUsers }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <img src="https://cdn-icons-png.flaticon.com/128/2991/2991108.png" alt="Google docs" width="32" height="32">
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Dokumen</div>
                <div class="stat-value">{{ $totalDokumen }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <img src="https://cdn-icons-png.flaticon.com/128/4859/4859784.png" alt="Teamwork" width="32" height="32">
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Bidang</div>
                <div class="stat-value">{{ $totalBidang }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Kalender Section -->
    <div class="calendar-section-landing" style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 300px;">
            <div id="calendar"></div>
        </div>

        <!-- Reminder Section -->
        <div class="reminder-section-landing" style="flex: 1; min-width: 300px;">
            <h3>Reminder</h3>
            
            <div class="reminder-input-section">
                <input type="date" id="reminderDate" class="reminder-input">
                <input type="text" id="reminderTitle" class="reminder-input" placeholder="Judul reminder">
                <textarea id="reminderDescription" class="reminder-input" placeholder="Deskripsi (opsional)" rows="2"></textarea>
                <button id="addReminderBtn" class="btn-add-reminder">+ Tambah Reminder</button>
            </div>

            <div id="reminderList" class="reminder-list">
                <!-- Reminders will be displayed here via JS if needed, but Calendar shows them now -->
                <p style="color: #666; font-size: 13px;">Reminder tersimpan akan muncul di kalender.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                locale: 'id',
                editable: true,
                events: '/api/events', // Fetch events from API
                dateClick: function(info) {
                    document.getElementById('reminderDate').value = info.dateStr;
                }
            });
            calendar.render();

            loadReminders();

            // Add Reminder Handlers
            document.getElementById('addReminderBtn').addEventListener('click', function() {
                const title = document.getElementById('reminderTitle').value;
                const date = document.getElementById('reminderDate').value;
                const desc = document.getElementById('reminderDescription').value;

                if (!title || !date) {
                    alert('Judul dan Tanggal wajib diisi');
                    return;
                }

                fetch('/api/reminder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        title: title,
                        date: date,
                        description: desc,
                        color: 'yellow' 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert('Reminder berhasil ditambahkan');
                    calendar.refetchEvents(); // Refresh calendar
                    loadReminders(); // Refresh list
                    document.getElementById('reminderTitle').value = '';
                    document.getElementById('reminderDescription').value = '';
                })
                .catch(error => console.error('Error:', error));
            });
            
            function loadReminders() {
                fetch('/api/reminders')
                    .then(response => response.json())
                    .then(data => {
                        const list = document.getElementById('reminderList');
                        list.innerHTML = '';
                        if (data.length === 0) {
                            list.innerHTML = '<p style="color: #666; font-size: 13px;">Belum ada reminder.</p>';
                            return;
                        }
                        // Sort by date descending
                        data.sort((a, b) => new Date(b.date) - new Date(a.date));

                        data.forEach(rem => {
                            const item = document.createElement('div');
                            item.className = 'reminder-item';
                            item.style.borderBottom = '1px solid #eee';
                            item.style.padding = '8px 0';
                            item.innerHTML = `
                                <div style="font-weight: bold; font-size: 14px;">${rem.title}</div>
                                <div style="font-size: 12px; color: #555;">${rem.date}</div>
                                ${rem.description ? `<div style="font-size: 12px; color: #777;">${rem.description}</div>` : ''}
                                <button onclick="deleteReminder(${rem.id})" style="font-size: 10px; color: red; background: none; border: none; cursor: pointer; padding: 0;">Hapus</button>
                            `;
                            list.appendChild(item);
                        });
                    });
            }

            window.deleteReminder = function(id) {
                if(!confirm('Hapus reminder ini?')) return;
                fetch(`/api/reminder/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => {
                    calendar.refetchEvents();
                    loadReminders();
                });
            }
        });
    </script>
@endpush
