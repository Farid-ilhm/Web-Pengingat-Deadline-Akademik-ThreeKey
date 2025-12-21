<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\UserSubject;
use App\Models\Notification;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) {
  header('Location: ../auth/login.php');
  exit;
}

/* ================= OPSI A ================= */
// halaman ini bagian dari menu Mata Pelajaran
$activeMenu = 'subjects';

// notifikasi (dipakai oleh layout.php)
$notif = new Notification();
$countNotif = $notif->countUnread($user['id']);
/* ========================================== */

$usModel = new UserSubject();

if (!isset($_GET['id'])) {
  exit("Invalid request");
}

$id = (int) $_GET['id'];
$data = $usModel->find($id);

if (!$data || $data['user_id'] != $user['id']) {
  exit("Data tidak ditemukan atau bukan milik Anda.");
}

$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $note = trim($_POST['note'] ?? '');

  if ($name === '') {
    $msg = "Nama mata pelajaran tidak boleh kosong.";
  } elseif (strlen($note) > 500) {
    $msg = "Catatan maksimal 500 karakter.";
  } else {
    $usModel->update($id, $user['id'], $name, $note);
    header("Location: subjects.php");
    exit;
  }
}

$noteValue = $data['note'] ?? '';
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Edit Mata Pelajaran — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/subjects.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <h2 class="page-title">Edit Mata Pelajaran</h2>

    <div class="card">
      <h3>Form Edit Mata Pelajaran</h3>

      <?php if ($msg): ?>
        <div class="alert alert-error">
          <?= htmlspecialchars($msg) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="subject-form">

        <div class="form-group">
          <label>Nama Mata Pelajaran</label>
          <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
        </div>

        <div class="form-group">
          <label>Catatan</label>
          <textarea name="note" maxlength="500"><?= htmlspecialchars($noteValue) ?></textarea>
          <small class="char-counter">
            <?= strlen($noteValue) ?> / 500
          </small>
        </div>

        <div style="display:flex; gap:12px;">
          <button class="btn-primary">Simpan Perubahan</button>
          <a href="subjects.php" class="btn-primary">Batal</a>
        </div>

      </form>
    </div>

  </main>

  <!-- ================= JS ================= -->
  <script src="../assets/js/edit_subjects.js"></script>

</body>

</html>