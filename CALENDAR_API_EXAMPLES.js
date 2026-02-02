// 📝 CONTOH PENGGUNAAN CALENDAR API

// ============================================
// 1. TAMBAH REMINDER
// ============================================

fetch('/api/reminder', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        title: 'Rapat Tim',
        description: 'Diskusi tentang project DORI',
        date: '2025-02-15'
    })
})
.then(response => response.json())
.then(data => {
    console.log('Reminder added:', data);
    // data:
    // {
    //   "id": 1,
    //   "title": "Rapat Tim",
    //   "description": "Diskusi tentang project DORI",
    //   "date": "2025-02-15",
    //   "color": "yellow",
    //   "created_at": "2025-01-22T10:00:00Z",
    //   "updated_at": "2025-01-22T10:00:00Z"
    // }
})
.catch(error => console.error('Error:', error));

// ============================================
// 2. GET SEMUA REMINDER
// ============================================

fetch('/api/reminders')
    .then(response => response.json())
    .then(data => {
        console.log('All reminders:', data);
        // data: array of reminder objects
        // [
        //   {
        //     "id": 1,
        //     "title": "Rapat Tim",
        //     "description": "Diskusi tentang project DORI",
        //     "date": "2025-02-15",
        //     "color": "yellow",
        //     "created_at": "2025-01-22T10:00:00Z",
        //     "updated_at": "2025-01-22T10:00:00Z"
        //   },
        //   ...
        // ]
    })
    .catch(error => console.error('Error:', error));

// ============================================
// 3. GET CALENDAR DATA UNTUK BULAN TERTENTU
// ============================================

fetch('/api/calendar?year=2025&month=2')
    .then(response => response.json())
    .then(data => {
        console.log('Calendar data for Feb 2025:', data);
        // data: array of days with details
        // [
        //   null, null, null, null, null, null, 1,
        //   {
        //     "day": 1,
        //     "date": "2025-02-01",
        //     "isHoliday": false,
        //     "isToday": false,
        //     "reminders": []
        //   },
        //   ...
        // ]
    })
    .catch(error => console.error('Error:', error));

// ============================================
// 4. HAPUS REMINDER
// ============================================

fetch('/api/reminder/1', {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
})
.then(response => response.json())
.then(data => {
    console.log('Reminder deleted:', data);
    // data: { "message": "Reminder deleted successfully" }
})
.catch(error => console.error('Error:', error));

// ============================================
// 5. CONTOH FORM SUBMISSION
// ============================================

document.getElementById('addReminderBtn').addEventListener('click', function() {
    const dateInput = document.getElementById('reminderDate');
    const titleInput = document.getElementById('reminderTitle');
    const descriptionInput = document.getElementById('reminderDescription');

    if (!dateInput.value || !titleInput.value.trim()) {
        alert('Tanggal dan judul harus diisi!');
        return;
    }

    const reminderData = {
        title: titleInput.value.trim(),
        description: descriptionInput.value.trim() || null,
        date: dateInput.value,
        color: 'yellow'
    };

    fetch('/api/reminder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(reminderData)
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        console.log('Reminder berhasil ditambahkan:', data);
        // Clear form
        titleInput.value = '';
        descriptionInput.value = '';
        // Refresh calendar
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menambahkan reminder');
    });
});

// ============================================
// 6. HELPER FUNCTION - FORMAT TANGGAL
// ============================================

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Usage:
const today = new Date();
const todayStr = formatDate(today);
console.log(todayStr); // "2025-01-22"

// ============================================
// 7. CONTOH DENGAN ERROR HANDLING
// ============================================

async function addReminderAsync(title, description, date) {
    try {
        const response = await fetch('/api/reminder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ title, description, date })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Success:', data);
        return data;

    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Usage:
// addReminderAsync('Meeting', 'Team standup', '2025-02-20')
//     .then(data => console.log('Reminder added:', data))
//     .catch(error => console.error('Failed:', error));

// ============================================
// 8. VALIDASI INPUT
// ============================================

function validateReminderInput(title, date) {
    const errors = [];

    if (!title || title.trim().length === 0) {
        errors.push('Judul reminder tidak boleh kosong');
    }

    if (!date) {
        errors.push('Tanggal harus dipilih');
    }

    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) {
        errors.push('Format tanggal tidak valid');
    }

    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// Usage:
const validation = validateReminderInput('', '2025-02-15');
if (!validation.isValid) {
    console.log('Validation errors:', validation.errors);
}

// ============================================
// 9. RESPONSE HANDLING
// ============================================

async function handleReminderResponse(response) {
    if (response.status === 201) {
        // Success - Reminder created
        return { success: true, data: await response.json() };
    } else if (response.status === 400) {
        // Bad request
        const error = await response.json();
        return { success: false, message: 'Input tidak valid', error };
    } else if (response.status === 404) {
        // Not found
        return { success: false, message: 'Reminder tidak ditemukan' };
    } else {
        // Server error
        return { success: false, message: 'Server error' };
    }
}

// ============================================
// 10. LOADING STATE MANAGEMENT
// ============================================

function addReminderWithLoading() {
    const btn = document.getElementById('addReminderBtn');
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Loading...';

    fetch('/api/reminder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            title: document.getElementById('reminderTitle').value,
            description: document.getElementById('reminderDescription').value,
            date: document.getElementById('reminderDate').value
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        btn.textContent = '✓ Ditambahkan';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        btn.textContent = '✗ Gagal';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
        }, 2000);
    });
}

// ============================================
// TIPS & TRICKS
// ============================================

/*
1. Selalu sertakan X-CSRF-TOKEN header untuk POST/DELETE requests
   Dapatkan dari: document.querySelector('meta[name="csrf-token"]').getAttribute('content')

2. Gunakan async/await untuk cleaner code

3. Selalu validate input sebelum mengirim ke server

4. Handle error properly dengan try-catch

5. Berikan feedback user dengan loading states

6. Format tanggal dalam YYYY-MM-DD format

7. Refresh calendar setelah tambah/hapus reminder

8. Gunakan .catch() untuk error handling

9. Log response untuk debugging

10. Test dengan berbagai input untuk memastikan stability
*/
