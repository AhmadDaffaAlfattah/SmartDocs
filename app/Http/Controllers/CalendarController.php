<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    // Hari libur nasional Indonesia 2025-2030
    private $holidays = [
        // 2025
        '2025-01-01', // Tahun Baru
        '2025-03-29', // Isra & Miraj
        '2025-04-10', // Idul Fitri
        '2025-04-11', // Idul Fitri
        '2025-04-12', // Cuti Bersama
        '2025-04-13', // Cuti Bersama
        '2025-04-14', // Cuti Bersama
        '2025-04-15', // Cuti Bersama
        '2025-04-16', // Cuti Bersama
        '2025-04-25', // Hari Raya Imlek
        '2025-05-01', // Hari Buruh
        '2025-05-14', // Hari Raya Waisak
        '2025-05-19', // Hari Pendidikan
        '2025-06-01', // Idul Adha
        '2025-06-21', // Tahun Baru Hijriah
        '2025-08-17', // Hari Kemerdekaan
        '2025-09-16', // Maulid Nabi Muhammad
        '2025-12-25', // Natal
        '2025-12-26', // Cuti Bersama

        // 2026
        '2026-01-01',
        '2026-02-14',
        '2026-03-31',
        '2026-04-01',
        '2026-04-02',
        '2026-04-03',
        '2026-04-04',
        '2026-04-05',
        '2026-04-06',
        '2026-05-01',
        '2026-05-14',
        '2026-05-16',
        '2026-05-26',
        '2026-06-01',
        '2026-06-03',
        '2026-08-17',
        '2026-09-02',
        '2026-12-25',
        '2026-12-31',

        // 2027
        '2027-01-01',
        '2027-02-22',
        '2027-03-20',
        '2027-04-20',
        '2027-04-21',
        '2027-04-22',
        '2027-04-23',
        '2027-04-24',
        '2027-04-25',
        '2027-05-01',
        '2027-05-13',
        '2027-05-15',
        '2027-06-01',
        '2027-06-07',
        '2027-08-17',
        '2027-08-20',
        '2027-12-25',

        // 2028
        '2028-01-01',
        '2028-02-11',
        '2028-04-09',
        '2028-04-10',
        '2028-04-11',
        '2028-04-12',
        '2028-04-13',
        '2028-04-14',
        '2028-04-15',
        '2028-05-01',
        '2028-05-22',
        '2028-06-01',
        '2028-06-27',
        '2028-08-17',
        '2028-09-16',
        '2028-12-25',
        '2028-12-26',

        // 2029
        '2029-01-01',
        '2029-01-30',
        '2029-03-30',
        '2029-03-31',
        '2029-04-01',
        '2029-04-02',
        '2029-04-03',
        '2029-04-04',
        '2029-04-05',
        '2029-05-01',
        '2029-05-19',
        '2029-06-01',
        '2029-06-15',
        '2029-08-17',
        '2029-09-05',
        '2029-12-25',

        // 2030
        '2030-01-01',
        '2030-02-19',
        '2030-04-20',
        '2030-04-21',
        '2030-04-22',
        '2030-04-23',
        '2030-04-24',
        '2030-04-25',
        '2030-04-26',
        '2030-05-01',
        '2030-05-09',
        '2030-06-01',
        '2030-06-03',
        '2030-08-17',
        '2030-08-31',
        '2030-12-25',
        '2030-12-26',
    ];

    public function index()
    {
        $year = request('year', Carbon::now()->year);
        $month = request('month', Carbon::now()->month);

        return view('calendar.index', [
            'year' => $year,
            'month' => $month,
            'holidays' => $this->holidays,
        ]);
    }

    public function getCalendar(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $startingDayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $today = Carbon::now();

        // Get all reminders for this month
        $reminders = Reminder::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($reminder) {
                return $reminder->date->format('Y-m-d');
            });

        $calendarDays = [];

        // Add empty days for days before month starts
        for ($i = 0; $i < $startingDayOfWeek; $i++) {
            $calendarDays[] = null;
        }

        // Add days of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $isHoliday = in_array($dateStr, $this->holidays);
            $isToday = $today->format('Y-m-d') === $dateStr;
            $dayReminders = $reminders->get($dateStr, collect());

            $calendarDays[] = [
                'day' => $day,
                'date' => $dateStr,
                'isHoliday' => $isHoliday,
                'isToday' => $isToday,
                'reminders' => $dayReminders,
            ];
        }

        return response()->json($calendarDays);
    }

    public function storeReminder(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'color' => 'nullable|in:yellow,red,blue,white',
        ]);

        $validated['color'] = $validated['color'] ?? 'yellow';

        $reminder = Reminder::create($validated);

        return response()->json($reminder, 201);
    }

    public function deleteReminder($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted successfully']);
    }

    public function getReminders(Request $request)
    {
        $reminders = Reminder::all();
        return response()->json($reminders);
    }
    public function getEvents(Request $request)
    {
        $reminders = Reminder::all();
        $events = [];

        foreach ($reminders as $reminder) {
            $events[] = [
                'id' => $reminder->id,
                'title' => $reminder->title,
                'start' => $reminder->date->format('Y-m-d'),
                'description' => $reminder->description,
                'backgroundColor' => '#f1c40f', // Yellowish for reminders
                'borderColor' => '#f1c40f',
                'extendedProps' => [
                    'type' => 'reminder'
                ]
            ];
        }

        // Add holidays to events (simplified)
        foreach ($this->holidays as $holidayDate) {
            $events[] = [
                'id' => 'holiday-' . $holidayDate,
                'title' => 'Hari Libur Nasional',
                'start' => $holidayDate,
                'display' => 'background',
                'backgroundColor' => '#ffdddd', // Light red background
                'extendedProps' => [
                    'type' => 'holiday'
                ]
            ];
        }

        return response()->json($events);
    }
}
