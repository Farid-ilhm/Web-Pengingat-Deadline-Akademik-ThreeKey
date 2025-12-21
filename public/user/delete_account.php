<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Models\User;
use App\Helpers\Session;

Session::start();
$user = Session::get('user');
if (!$user) {
    header("Location: ../auth/login.php");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $u = (new User())->findById($user['id']);

    // Jika akun manual → cek password
    if ($u['provider'] === 'manual') {
        $password = $_POST['password'] ?? '';

        if (!password_verify($password, $u['password'])) {
            $error = "Password yang Anda masukkan salah.";
            goto render;
        }
    }

    // Hapus user
    (new User())->deleteUser($user['id']);

    // Hancurkan session
    Session::destroy();

    header("Location: ../auth/login.php");
    exit;
}

render:
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Hapus Akun — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/delete_account.css">
</head>
<body>

<div class="delete-card">

  <h3>Hapus Akun</h3>

  <p>
    Tindakan ini <strong>tidak dapat dibatalkan</strong>.
    Semua data Anda akan dihapus secara permanen.
  </p>

  <?php if ($error): ?>
    <div class="alert alert-error">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="post"
        onsubmit="return confirm('Yakin ingin menghapus akun secara permanen?')">

    <?php if ($user['provider'] === 'manual'): ?>
      <label>Password</label>
      <input type="password" name="password" required>
    <?php endif; ?>

    <button class="btn-danger">
      Hapus Akun Permanen
    </button>
  </form>

  <a href="account.php" class="btn-cancel">
    Batal
  </a>

</div>

</body>
</html>
