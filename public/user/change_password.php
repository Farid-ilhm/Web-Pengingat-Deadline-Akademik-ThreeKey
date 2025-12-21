<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\User;
use App\Helpers\Session;

Session::start();
$user = Session::get('user');
if (!$user) {
  header("Location: ../auth/login.php");
  exit;
}

/* ================= OPSI A ================= */
$activeMenu = 'profile';
/* ========================================== */

$error = null;
$success = null;

// BLOK GOOGLE USER
if (($user['provider'] ?? 'manual') === 'google') {
  $error = "Akun Google tidak dapat mengganti password.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {

  $old = $_POST['old'] ?? '';
  $new = $_POST['new'] ?? '';
  $confirm = $_POST['confirm'] ?? '';

  if ($new !== $confirm) {
    $error = "Konfirmasi password tidak cocok";
  } else {

    $userModel = new User();
    $dbUser = $userModel->findById($user['id']);

    if (!password_verify($old, $dbUser['password'])) {
      $error = "Password lama salah";
    } elseif (password_verify($new, $dbUser['password'])) {
      $error = "Password baru tidak boleh sama dengan password lama";
    } else {
      $userModel->changePassword($user['id'], $new);
      $success = "Password berhasil diperbarui";
    }
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Ganti Password — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/edit_profile.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <h2 class="page-title center-title">Ganti Password</h2>

    <div class="edit-profile-card center-card">

      <?php if (!empty($error)): ?>
        <div class="alert alert-error auto-hide">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success auto-hide">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="edit-profile-form">

        <div class="form-group">
          <label>Password Lama</label>
          <div class="password-field">
            <input type="password" name="old" id="oldPassword" required>
            <button type="button" class="toggle-password" data-target="oldPassword">
              <img src="../assets/img/eye-close.png" alt="toggle">
            </button>
          </div>
        </div>

        <div class="form-group">
          <label>Password Baru</label>
          <div class="password-field">
            <input type="password" name="new" id="newPassword" required>
            <button type="button" class="toggle-password" data-target="newPassword">
              <img src="../assets/img/eye-close.png" alt="toggle">
            </button>
          </div>
        </div>

        <div class="form-group">
          <label>Konfirmasi Password</label>
          <div class="password-field">
            <input type="password" name="confirm" id="confirmPassword" required>
            <button type="button" class="toggle-password" data-target="confirmPassword">
              <img src="../assets/img/eye-close.png" alt="toggle">
            </button>
          </div>
        </div>

        <button class="btn-save-profile">Ganti Password</button>
      </form>

    </div>

  </main>

  <script src="../assets/js/change_password.js"></script>

</body>

</html>