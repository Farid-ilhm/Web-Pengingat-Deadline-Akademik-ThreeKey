<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Session;
use App\Models\Notification;

Session::start();
$user = Session::get('user');
if (!$user) {
  header('Location: ../auth/login.php');
  exit;
}

$activeMenu = 'notifications';

$notifModel = new Notification();
$list = $notifModel->allByUser($user['id']);

/* ================== GROUPING ================== */
$today = [];
$thisWeek = [];
$nextWeek = [];

$now = new DateTime();

foreach ($list as $n) {
  $created = new DateTime($n['created_at']);
  $diff = (int) $now->diff($created)->format('%r%a');

  if ($diff === 0) {
    $today[] = $n;
  } elseif ($diff >= -7) {
    $thisWeek[] = $n;
  } else {
    $nextWeek[] = $n;
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Notifikasi — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/notifications-list.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <h1 class="page-title">Semua Notifikasi</h1>

    <div class="notif-page-wrapper">

      <div class="notif-wrapper">

        <?php if (empty($list)): ?>
          <div class="notif-empty">🎉 Tidak ada notifikasi.</div>
        <?php endif; ?>

        <?php foreach ([
          'HARI INI' => $today,
          'MINGGU INI' => $thisWeek,
          'MINGGU BERIKUTNYA' => $nextWeek
        ] as $label => $group): ?>

          <?php if (!empty($group)): ?>
            <div class="notif-group">
              <div class="notif-group-title"><?= $label ?></div>
              <?php foreach ($group as $n)
                include 'partials/notif_item.php'; ?>
            </div>
          <?php endif; ?>

        <?php endforeach; ?>

      </div>

  </main>

  <!-- ================= JS FINAL ================= -->
  <script src="../assets/js/notifications.js"></script>
</body>

</html>