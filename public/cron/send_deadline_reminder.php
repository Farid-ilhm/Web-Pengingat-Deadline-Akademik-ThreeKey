<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Notification;
use App\Helpers\Email;

/*
|--------------------------------------------------------------------------
| KONFIGURASI HARI REMINDER
| H-7, H-3, H-1, H-0
|--------------------------------------------------------------------------
*/
$reminderDays = [7, 3, 1, 0];

$notification = new Notification();

foreach ($reminderDays as $day) {

    // Ambil schedule yang jatuh H-x
    $schedules = $notification->getSchedulesByDayOffset($day);

    foreach ($schedules as $item) {

        /* ================= SUBJECT EMAIL ================= */
        if ($day === 0) {
            $subject = "⏰ DEADLINE HARI INI: {$item['title']}";
        } else {
            $subject = "Pengingat Deadline {$item['title']} (H-{$day})";
        }

        /* ================= BODY EMAIL ================= */
        $body = "
            <h3>Halo, {$item['name']} 👋</h3>
            <p>Ini adalah pengingat untuk deadline berikut:</p>

            <ul>
                <li><b>Judul:</b> {$item['title']}</li>
                <li><b>Batas Akhir:</b> {$item['end_datetime']}</li>
                <li><b>Sisa Waktu:</b> H-{$day}</li>
            </ul>

            <p>Jangan lupa menyelesaikan sebelum batas waktu.</p>
            <br>
            <small>Email ini dikirim otomatis oleh sistem ThreeKey.</small>
        ";

        /* ================= KIRIM EMAIL ================= */
        Email::send($item['email'], $subject, $body);

        /* ================= SIMPAN NOTIFIKASI ================= */
        $notification->create(
            (int)$item['user_id'],
            (int)$item['schedule_id'],
            $day,
            $subject,
            strip_tags($body)
        );
    }
}

echo "✅ Reminder deadline & notifikasi berhasil dijalankan\n";

/*
|--------------------------------------------------------------------------
| Untuk testing manual (belum hosting):
| http://localhost/threekey/public/cron/send_deadline_reminder.php
|--------------------------------------------------------------------------
*/
