<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Template;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user || $user['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$tm = new Template();
$msg = null;
$active = 'templates';

/* =============== DELETE TEMPLATE =============== */
if (isset($_GET['delete'])) {
    $tm->delete((int)$_GET['delete']);
    header("Location: templates.php");
    exit;
}

/* =============== CREATE TEMPLATE =============== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['desc'] ?? '');

    if ($name) {
        $tm->create($name, $desc, $user['id']);
        $msg = "Template berhasil ditambahkan.";
    } else {
        $msg = "Nama template wajib diisi.";
    }
}

$templates = $tm->all();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kelola Template</title>

  <!-- WAJIB -->
  <link rel="stylesheet" href="../assets/css/admin_layout.css">
  <link rel="stylesheet" href="../assets/css/admin_templates.css">
</head>
<body>

<div class="admin-dashboard">

  <!-- SIDEBAR -->
  <?php include 'layout.php'; ?>

  <!-- CONTENT -->
  <main class="admin-content">

    <!-- WRAPPER BIAR TIDAK MENYEMPIT -->
    <div class="admin-container">

      <h2>Kelola Tipe Jadwal</h2>

      <?php if ($msg): ?>
        <div class="alert"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <!-- FORM -->
      <div class="card">
        <form method="post">

          <label>Nama Tipe Jadwal</label>
          <input type="text" name="name" required placeholder="Contoh: Deadline">

          <label>Deskripsi</label>
          <textarea name="desc" placeholder="Deskripsi tipe jadwal..."></textarea>

          <button type="submit">Tambah Tipe Jadwal</button>
        </form>
      </div>

      <!-- LIST -->
      <div class="card">
        <h3>Daftar Tipe Jadwal</h3>

        <?php if (empty($templates)): ?>
          <div class="empty-state">Belum ada tipe jadwal</div>
        <?php else: ?>
          <ul class="template-list">
            <?php foreach ($templates as $t): ?>
              <li class="template-item">

                <div class="template-info">
                  <strong><?= htmlspecialchars($t['name']) ?></strong>
                  <small><?= htmlspecialchars($t['description'] ?? '-') ?></small>
                </div>

                <div class="template-actions">
                  <a href="edit_template.php?id=<?= $t['id'] ?>" class="edit">Edit</a>
                  <a href="templates.php?delete=<?= $t['id'] ?>"
                     class="delete"
                     onclick="return confirm('Hapus template ini?')">
                     Hapus
                  </a>
                </div>

              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

      </div>

    </div>
  </main>

</div>

</body>
</html>
