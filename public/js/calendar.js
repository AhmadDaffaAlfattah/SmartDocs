// Calendar.js - Interaktivitas Kalender
// Works on both /calendar page dan landing page (/)

let currentYear = new Date().getFullYear();
let currentMonth = new Date().getMonth() + 1;

const monthNames = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];

const holidays = [
    // 2025
    '2025-01-01', '2025-03-29', '2025-04-10', '2025-04-11', '2025-04-12',
    '2025-04-13', '2025-04-14', '2025-04-15', '2025-04-16', '2025-04-25',
    '2025-05-01', '2025-05-14', '2025-05-19', '2025-06-01', '2025-06-21',
    '2025-08-17', '2025-09-16', '2025-12-25', '2025-12-26',

    // 2026
    '2026-01-01', '2026-02-14', '2026-03-31', '2026-04-01', '2026-04-02',
    '2026-04-03', '2026-04-04', '2026-04-05', '2026-04-06', '2026-05-01',
    '2026-05-14', '2026-05-16', '2026-05-26', '2026-06-01', '2026-06-03',
    '2026-08-17', '2026-09-02', '2026-12-25', '2026-12-31',

    // 2027
    '2027-01-01', '2027-02-22', '2027-03-20', '2027-04-20', '2027-04-21',
    '2027-04-22', '2027-04-23', '2027-04-24', '2027-04-25', '2027-05-01',
    '2027-05-13', '2027-05-15', '2027-06-01', '2027-06-07', '2027-08-17',
    '2027-08-20', '2027-12-25',

    // 2028
    '2028-01-01', '2028-02-11', '2028-04-09', '2028-04-10', '2028-04-11',
    '2028-04-12', '2028-04-13', '2028-04-14', '2028-04-15', '2028-05-01',
    '2028-05-22', '2028-06-01', '2028-06-27', '2028-08-17', '2028-09-16',
    '2028-12-25', '2028-12-26',

    // 2029
    '2029-01-01', '2029-01-30', '2029-03-30', '2029-03-31', '2029-04-01',
    '2029-04-02', '2029-04-03', '2029-04-04', '2029-04-05', '2029-05-01',
    '2029-05-19', '2029-06-01', '2029-06-15', '2029-08-17', '2029-09-05',
    '2029-12-25',

    // 2030
    '2030-01-01', '2030-02-19', '2030-04-20', '2030-04-21', '2030-04-22',
    '2030-04-23', '2030-04-24', '2030-04-25', '2030-04-26', '2030-05-01',
    '2030-05-09', '2030-06-01', '2030-06-03', '2030-08-17', '2030-08-31',
    '2030-12-25', '2030-12-26',
];

// Store reminders
let reminders = {};

// Initialize
function initCalendarOnReady() {
    const calendarBody = document.getElementById('calendarBody');
    if (!calendarBody) return; // Kalender tidak ada di page ini

    initializeCalendar();
    loadReminders();

    // Event listeners
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const yearSelect = document.getElementById('yearSelect');
    const addReminderBtn = document.getElementById('addReminderBtn');
    const reminderDate = document.getElementById('reminderDate');

    if (prevBtn) prevBtn.addEventListener('click', previousMonth);
    if (nextBtn) nextBtn.addEventListener('click', nextMonth);
    if (yearSelect) yearSelect.addEventListener('change', changeYear);
    if (addReminderBtn) addReminderBtn.addEventListener('click', addReminder);

    // Set today's date in reminder input
    if (reminderDate) {
        reminderDate.valueAsDate = new Date();
    }

    // Watch for reminder date changes
    if (reminderDate) {
        reminderDate.addEventListener('change', function() {
            loadReminderList(this.value);
        });
    }
}

// Check if DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalendarOnReady);
} else {
    initCalendarOnReady();
}

function initializeCalendar() {
    renderCalendar();
}

function renderCalendar() {
    const firstDay = new Date(currentYear, currentMonth - 1, 1);
    const lastDay = new Date(currentYear, currentMonth, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    const monthYearElement = document.getElementById('monthYear');
    monthYearElement.textContent = `${monthNames[currentMonth - 1]} ${currentYear}`;

    // Update year selector
    document.getElementById('yearSelect').value = currentYear;

    const calendarBody = document.getElementById('calendarBody');
    calendarBody.innerHTML = '';

    let date = 1;
    let row = document.createElement('tr');

    // Empty cells for days before month starts
    for (let i = 0; i < startingDayOfWeek; i++) {
        const cell = document.createElement('td');
        cell.classList.add('day-empty');
        cell.innerHTML = '';
        row.appendChild(cell);
    }

    // Days of the month
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('td');
        const dateStr = formatDate(currentYear, currentMonth, day);
        const isHoliday = holidays.includes(dateStr);
        const isToday = isToday_date(dateStr, today);
        const hasReminders = reminders[dateStr] && reminders[dateStr].length > 0;

        // Set classes
        cell.classList.add('day-cell');
        if (isHoliday) {
            cell.classList.add('day-holiday');
        } else {
            cell.classList.add('day-normal');
        }

        if (isToday) {
            cell.classList.add('day-today');
        }

        if (hasReminders) {
            cell.classList.add('day-reminder');
        }

        // Create day content
        let dayContent = `<div class="calendar-day">`;
        dayContent += `<div class="day-number">${day}</div>`;

        if (hasReminders) {
            dayContent += `<div class="day-reminders">`;
            reminders[dateStr].forEach(reminder => {
                dayContent += `<div class="day-reminder-item" title="${reminder.title}">${reminder.title}</div>`;
            });
            dayContent += `</div>`;
        }

        dayContent += `</div>`;
        cell.innerHTML = dayContent;

        // Click event to show reminders
        cell.addEventListener('click', function() {
            if (!isToday_date(dateStr, today) || day > 0) {
                showReminderForDate(dateStr);
            }
        });

        row.appendChild(cell);

        // New row after Saturday (day 6)
        if ((startingDayOfWeek + day) % 7 === 0) {
            calendarBody.appendChild(row);
            row = document.createElement('tr');
        }
    }

    // Add remaining cells
    while (row.children.length > 0 && row.children.length < 7) {
        const cell = document.createElement('td');
        cell.classList.add('day-empty');
        row.appendChild(cell);
    }

    if (row.children.length > 0) {
        calendarBody.appendChild(row);
    }
}

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

function isToday_date(dateStr, today) {
    const todayStr = formatDate(today.getFullYear(), today.getMonth() + 1, today.getDate());
    return dateStr === todayStr;
}

function previousMonth() {
    currentMonth--;
    if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }
    renderCalendar();
    loadReminderListForMonth();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    }
    renderCalendar();
    loadReminderListForMonth();
}

function changeYear() {
    const selectedYear = parseInt(document.getElementById('yearSelect').value);
    currentYear = selectedYear;
    currentMonth = 1;
    renderCalendar();
    loadReminderListForMonth();
}

function showReminderForDate(dateStr) {
    const reminderInput = document.getElementById('reminderDate');
    reminderInput.value = dateStr;
    
    const dateObj = new Date(dateStr + 'T00:00:00');
    reminderInput.valueAsDate = dateObj;
}

function addReminder() {
    const dateInput = document.getElementById('reminderDate');
    const titleInput = document.getElementById('reminderTitle');
    const descriptionInput = document.getElementById('reminderDescription');

    if (!dateInput.value || !titleInput.value.trim()) {
        alert('Tanggal dan judul reminder harus diisi!');
        return;
    }

    const reminderData = {
        title: titleInput.value.trim(),
        description: descriptionInput.value.trim(),
        date: dateInput.value,
        color: 'yellow'
    };

    // Send to server
    fetch('/api/reminder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(reminderData)
    })
    .then(response => response.json())
    .then(data => {
        // Add to local reminders
        if (!reminders[dateInput.value]) {
            reminders[dateInput.value] = [];
        }
        reminders[dateInput.value].push({
            id: data.id,
            title: reminderData.title,
            description: reminderData.description
        });

        // Clear inputs
        titleInput.value = '';
        descriptionInput.value = '';

        // Refresh calendar and reminder list
        renderCalendar();
        loadReminderListForMonth();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menambah reminder');
    });
}

function loadReminders() {
    // Fetch reminders from server
    fetch('/api/reminders')
        .then(response => response.json())
        .then(data => {
            reminders = {};
            data.forEach(reminder => {
                if (!reminders[reminder.date]) {
                    reminders[reminder.date] = [];
                }
                reminders[reminder.date].push({
                    id: reminder.id,
                    title: reminder.title,
                    description: reminder.description
                });
            });
            renderCalendar();
            // Load reminder list for current month
            loadReminderListForMonth();
        })
        .catch(error => console.error('Error loading reminders:', error));
}

function loadReminderListForMonth() {
    const reminderList = document.getElementById('reminderList');
    if (!reminderList) return;

    // Get all reminders for current month
    const monthReminders = [];
    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
    
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = formatDate(currentYear, currentMonth, day);
        if (reminders[dateStr] && reminders[dateStr].length > 0) {
            reminders[dateStr].forEach(reminder => {
                monthReminders.push({
                    date: dateStr,
                    day: day,
                    ...reminder
                });
            });
        }
    }

    // Sort by day
    monthReminders.sort((a, b) => a.day - b.day);

    reminderList.innerHTML = '';

    if (monthReminders.length === 0) {
        reminderList.innerHTML = '<div class="reminder-empty">Tidak ada reminder di bulan ini</div>';
        return;
    }

    monthReminders.forEach(reminder => {
        const reminderItem = document.createElement('div');
        reminderItem.classList.add('reminder-item');
        
        // Format date to show day and month
        const dateObj = new Date(reminder.date);
        const dayName = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'][dateObj.getDay()];
        const formattedDate = `${reminder.day} ${monthNames[currentMonth - 1]} ${currentYear}`;
        
        reminderItem.innerHTML = `
            <div class="reminder-item-content">
                <div class="reminder-item-date">${formattedDate}</div>
                <div class="reminder-item-title">${reminder.title}</div>
                ${reminder.description ? `<div class="reminder-item-description">${reminder.description}</div>` : ''}
            </div>
            <button class="reminder-item-delete" data-id="${reminder.id}">Hapus</button>
        `;

        reminderItem.querySelector('.reminder-item-delete').addEventListener('click', function() {
            deleteReminder(reminder.id, reminder.date);
        });

        reminderList.appendChild(reminderItem);
    });
}

function loadReminderList(dateStr) {
    const reminderList = document.getElementById('reminderList');
    if (!reminderList) return;

    const listReminders = reminders[dateStr] || [];

    reminderList.innerHTML = '';

    if (listReminders.length === 0) {
        reminderList.innerHTML = '<div class="reminder-empty">Tidak ada reminder untuk tanggal ini</div>';
        return;
    }

    listReminders.forEach(reminder => {
        const reminderItem = document.createElement('div');
        reminderItem.classList.add('reminder-item');
        reminderItem.innerHTML = `
            <div class="reminder-item-content">
                <div class="reminder-item-date">${dateStr}</div>
                <div class="reminder-item-title">${reminder.title}</div>
                ${reminder.description ? `<div class="reminder-item-description">${reminder.description}</div>` : ''}
            </div>
            <button class="reminder-item-delete" data-id="${reminder.id}">Hapus</button>
        `;

        reminderItem.querySelector('.reminder-item-delete').addEventListener('click', function() {
            deleteReminder(reminder.id, dateStr);
        });

        reminderList.appendChild(reminderItem);
    });
}

function deleteReminder(reminderId, dateStr) {
    if (confirm('Yakin ingin menghapus reminder ini?')) {
        fetch(`/api/reminder/${reminderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            // Remove from local reminders
            reminders[dateStr] = reminders[dateStr].filter(r => r.id !== reminderId);
            if (reminders[dateStr].length === 0) {
                delete reminders[dateStr];
            }

            // Refresh
            renderCalendar();
            loadReminderListForMonth();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menghapus reminder');
        });
    }
}

// Watch for reminder date changes
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        const reminderDate = document.getElementById('reminderDate');
        if (reminderDate) {
            reminderDate.addEventListener('change', function() {
                loadReminderList(this.value);
            });
        }
    });
} else {
    const reminderDate = document.getElementById('reminderDate');
    if (reminderDate) {
        reminderDate.addEventListener('change', function() {
            loadReminderList(this.value);
        });
    }
}
