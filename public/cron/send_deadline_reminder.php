<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Notification;
use App\Helpers\Email;
use App\Config\Env;

// LOAD .env (WAJIB UNTUK CRON)
Env::load();

// SET TIMEZONE
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Jakarta');

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
            <small>Email ini dikirim otomatis oleh sistem ThreeKey.</small>
        ";

        Email::send($item['email'], $subject, $body);

        // Simpan notifikasi (pastikan tidak dobel)
        $notification->create(
            (int)$item['user_id'],
            (int)$item['schedule_id'],
            $day,
            $subject,
            strip_tags($body)
        );
    }
}