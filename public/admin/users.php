<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Models\User;
use App\Helpers\Session;
use App\Config\Env;

Env::load();
Session::start();

$admin = Session::get('user');
if (!$admin || $admin['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$users = (new User())->allUsers();
$active = 'users';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kelola User</title>

  <link rel="stylesheet" href="../assets/css/admin_layout.css">
  <link rel="stylesheet" href="../assets/css/admin_users.css">
</head>
<body>

<div class="admin-dashboard">

  <?php include 'layout.php'; ?>

  <main class="admin-content">
    <div class="admin-container">

      <h2>Kelola User</h2>

      <div class="card">
        <?php if (empty($users)): ?>
          <div class="empty-state">Belum ada user terdaftar</div>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <div class="user-item">
              <div>
                <strong><?= htmlspecialchars($u['name']) ?></strong><br>
                <small><?= htmlspecialchars($u['email']) ?></small>
              </div>

              <a href="delete_user.php?id=<?= $u['id'] ?>"
                 class="btn-delete"
                 onclick="return confirm('Hapus akun ini?')">
                 Hapus
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </main>

</div>

</body>
</html>
