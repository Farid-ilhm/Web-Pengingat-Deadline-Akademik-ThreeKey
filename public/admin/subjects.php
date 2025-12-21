<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Subject;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user || $user['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$subjectModel = new Subject();
$msg = null;
$active = 'subjects';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($name) {
        $subjectModel->create($name, $code, $desc, $user['id']);
        $msg = "Mata pelajaran global berhasil ditambahkan.";
    } else {
        $msg = "Nama mata pelajaran wajib diisi.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($subjectModel->find($id)) {
        $subjectModel->delete($id);
        header("Location: subjects.php");
        exit;
    }
}

$subjects = $subjectModel->all();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kelola Mata Pelajaran Global</title>

  <link rel="stylesheet" href="../assets/css/admin_layout.css">
  <link rel="stylesheet" href="../assets/css/admin_subjects.css">
</head>
<body>

<div class="admin-dashboard">

  <?php include 'layout.php'; ?>

  <main class="admin-content">
    <div class="admin-container">

      <h2>Kelola Mata Pelajaran Global</h2>

      <?php if ($msg): ?>
        <div class="alert"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <div class="card">
        <form method="post">
          <label>Kode Mata Pelajaran</label>
          <input type="text" name="code" placeholder="Contoh: BD101">

          <label>Nama Mata Pelajaran</label>
          <input type="text" name="name" required placeholder="Contoh: Basis Data">

          <label>Deskripsi</label>
          <textarea name="description" placeholder="Deskripsi singkat mata pelajaran..."></textarea>

          <button type="submit">Tambah Mata Pelajaran</button>
        </form>
      </div>

      <div class="card">
        <h3>Daftar Mata Pelajaran</h3>

        <?php if (empty($subjects)): ?>
          <div class="empty-state">Belum ada mata pelajaran global</div>
        <?php else: ?>
          <ul class="subject-list">
            <?php foreach ($subjects as $s): ?>
              <li class="subject-item">
                <div class="subject-info">
                  <div class="subject-name"><?= htmlspecialchars($s['name']) ?></div>
                  <div class="subject-code"><?= htmlspecialchars($s['code']) ?></div>
                </div>

                <div class="subject-actions">
                  <a href="edit_subject.php?id=<?= $s['id'] ?>" class="edit">Edit</a>
                  <a href="subjects.php?delete=<?= $s['id'] ?>" class="delete"
                     onclick="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
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
