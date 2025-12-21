<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Session;
use App\Config\Env;
use App\Models\Schedule;
use App\Models\User;
use App\Helpers\GoogleCalendar;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) { header('Location: ../auth/login.php'); exit; }

if (!isset($_GET['id'])) exit("Invalid request");

$id = intval($_GET['id']);

$model = new Schedule();
$data = $model->find($id, $user['id']);

if (!$data) exit("Jadwal tidak ditemukan atau bukan milik Anda");

// Hapus dari Google Calendar jika ada
if (!empty($data['google_event_id'])) {
    $u = (new User())->findById($user['id']);
    if (!empty($u['provider_refresh_token'])) {
        GoogleCalendar::deleteEvent($u['provider_refresh_token'], $data['google_event_id']);
    }
}

// Hapus dari database
$model->delete($id, $user['id']);

header("Location: schedules.php");
exit;
