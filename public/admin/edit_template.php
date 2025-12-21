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

if (!isset($_GET['id'])) exit("ID tidak valid");

$id = intval($_GET['id']);
$data = $tm->find($id);

if (!$data) exit("Template tidak ditemukan");

$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['desc']);

    if ($name) {
        $tm->update($id, $name, $desc);
        header("Location: templates.php");
        exit;
    } else {
        $msg = "Nama template wajib diisi.";
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Template</title>
<link rel="stylesheet" href="../assets/css/admin_layout.css">
<link rel="stylesheet" href="../assets/css/admin_edit_template.css">
</head>
<body>

<div class="admin-dashboard">

  <?php
    $active = 'templates';
    include 'layout.php';
  ?>

  <main class="admin-content">
    <div class="admin-container">

      <div class="edit-wrapper">
        <div class="edit-card">

          <h2>Edit Template</h2>

          <?php if ($msg): ?>
            <div class="alert-error">
              <?= htmlspecialchars($msg) ?>
            </div>
          <?php endif; ?>

          <form method="post">

            <div class="form-group">
              <label>Nama Template</label>
              <input name="name"
                     value="<?= htmlspecialchars($data['name']) ?>"
                     required>
            </div>

            <div class="form-group">
              <label>Deskripsi</label>
              <textarea name="desc"><?= htmlspecialchars($data['description']) ?></textarea>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-save">Simpan</button>
              <a href="templates.php" class="btn-back">Kembali</a>
            </div>

          </form>

        </div>
      </div>

    </div>
  </main>

</div>

</body>

</html>
