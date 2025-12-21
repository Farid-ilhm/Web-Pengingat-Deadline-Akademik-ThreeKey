<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Subject;

/* ================= INIT ================= */
Env::load();
Session::start();

/* ================= AUTH ================= */
$user = Session::get('user');
if (!$user || $user['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

/* ================= ACTIVE MENU ================= */
$active = 'subjects';

/* ================= MODEL ================= */
$subjectModel = new Subject();

/* ================= VALIDASI ID ================= */
if (!isset($_GET['id'])) {
    exit("ID subject tidak ditemukan");
}

$id = (int) $_GET['id'];
$data = $subjectModel->find($id);

if (!$data) {
    exit("Data subject tidak ditemukan");
}

$msg = null;

/* ================= UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);

    if ($name !== '') {
        $subjectModel->update($id, $name, $code, $desc);
        header("Location: subjects.php");
        exit;
    } else {
        $msg = "Nama wajib diisi.";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Mata Pelajaran — ThreeKey</title>

  <!-- ADMIN STYLE -->
  <link rel="stylesheet" href="../assets/css/admin_layout.css">
  <link rel="stylesheet" href="../assets/css/admin_edit_template.css">
</head>
<body>

<div class="admin-dashboard">

  <!-- ================= SIDEBAR ================= -->
  <?php include 'layout.php'; ?>

  <!-- ================= CONTENT ================= -->
  <main class="admin-content">
    <div class="admin-container">

      <div class="edit-wrapper">
        <div class="edit-card">

          <h2>Edit Mata Pelajaran</h2>

          <?php if ($msg): ?>
            <div class="alert-error">
              <?= htmlspecialchars($msg) ?>
            </div>
          <?php endif; ?>

          <form method="post">

            <div class="form-group">
              <label>Kode</label>
              <input type="text"
                     name="code"
                     value="<?= htmlspecialchars($data['code']) ?>">
            </div>

            <div class="form-group">
              <label>Nama</label>
              <input type="text"
                     name="name"
                     required
                     value="<?= htmlspecialchars($data['name']) ?>">
            </div>

            <div class="form-group">
              <label>Deskripsi</label>
              <textarea name="description"><?= htmlspecialchars($data['description']) ?></textarea>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-save">
                Simpan Perubahan
              </button>

              <a href="subjects.php" class="btn-back">
                Kembali
              </a>
            </div>

          </form>

        </div>
      </div>

    </div>
  </main>

</div>

</body>
</html>
