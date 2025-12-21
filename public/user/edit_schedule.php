<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Schedule;
use App\Models\User;
use App\Helpers\GoogleCalendar;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) {
  header('Location: ../auth/login.php');
  exit;
}

$activeMenu = 'schedules';

if (!isset($_GET['id'])) {
  exit("Invalid ID");
}

$id = (int) $_GET['id'];

$model = new Schedule();
$data = $model->find($id, $user['id']);

if (!$data) {
  exit("Jadwal tidak ditemukan atau bukan milik Anda");
}

/* ================= HANDLE POST ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $title = trim($_POST['title']);
  $desc = trim($_POST['description']);

  $start_raw = $_POST['start_datetime'];
  $end_raw = $_POST['end_datetime'];

  $start_dt = date("Y-m-d H:i:s", strtotime($start_raw));
  $end_dt = $end_raw ? date("Y-m-d H:i:s", strtotime($end_raw)) : null;

  // Update database
  $model->updateSchedule(
    $id,
    $user['id'],
    $title,
    $desc,
    $start_dt,
    $end_dt
  );

  // Update Google Calendar (jika ada)
  if (!empty($data['google_event_id'])) {
    $u = (new User())->findById($user['id']);

    GoogleCalendar::updateEvent(
      $u['provider_refresh_token'],
      $data['google_event_id'],
      $title,
      $desc,
      date('c', strtotime($start_dt)),
      $end_dt ? date('c', strtotime($end_dt)) : null,
      $_ENV['TIMEZONE'] ?? 'Asia/Jakarta'
    );
  }

  header("Location: schedules.php");
  exit;
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Edit Jadwal — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/schedules.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <h2 class="page-title">Edit Jadwal</h2>

    <div class="card">
      <h3>Form Edit Jadwal</h3>

      <form method="post" class="schedule-form">

        <div class="form-group">
          <label>Judul Jadwal</label>
          <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" required>
        </div>

        <div class="form-group">
          <label>Deskripsi Singkat</label>
          <textarea name="description" maxlength="500"><?= htmlspecialchars($data['description']) ?></textarea>
          <small class="char-counter">
            <?= strlen($data['description']) ?> / 500
          </small>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Mulai</label>
            <input type="datetime-local" name="start_datetime" required
              value="<?= date('Y-m-d\TH:i', strtotime($data['start_datetime'])) ?>">
          </div>

          <div class="form-group">
            <label>Selesai</label>
            <input type="datetime-local" name="end_datetime" value="<?= $data['end_datetime']
              ? date('Y-m-d\TH:i', strtotime($data['end_datetime']))
              : '' ?>">
          </div>
        </div>

        <button class="btn-primary">Simpan Perubahan</button>

      </form>
    </div>

  </main>

  <!-- ================= JS ================= -->
  <script src="../assets/js/edit_schedule.js"></script>

</body>

</html>