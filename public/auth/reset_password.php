<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Helpers\Session;

Session::start();

$auth = new AuthController();
$message = null;
$success = false;

$email = Session::get("reset_email");

if (!$email) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'];
    $confirm = $_POST['password_confirm'];

    if ($pass !== $confirm) {
        $message = "Password dan konfirmasi tidak cocok.";
    } else {
        $res = $auth->setNewPassword($email, $pass);
        $message = $res['message'];
        $success = $res['success'];

        if ($success) {
            Session::set("reset_email", null);
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Buat Password Baru</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/forgot.css">
</head>
<body>

<div class="login-container single-column">

    <!-- ===== NAVY (30%) ===== -->
    <div class="login-left navy-section">
        <h2>Pengingat Deadline Akademik</h2>

        <div class="logo-box small-logo">
            <img src="../assets/img/logo.png" alt="Logo">
        </div>
    </div>

    <!-- ===== WHITE (70%) ===== -->
    <div class="login-right white-section center-content">

        <h2>Buat Password Baru</h2>
        <span class="subtitle">
            Silakan buat password baru untuk akun<br>
            <b><?= htmlspecialchars($email) ?></b>
        </span>

        <?php if ($message): ?>
            <div class="<?= $success ? 'success-msg' : 'error-msg' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="post">

            <div class="password-wrapper">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password Baru"
                    required
                >
                <img
                    src="../assets/img/eye-close.png"
                    id="togglePassword"
                    class="eye-icon"
                   
                >
            </div>

            <div class="password-wrapper">
                <input
                    type="password"
                    name="password_confirm"
                    id="passwordConfirm"
                    placeholder="Konfirmasi Password"
                    required
                >
                <img
                    src="../assets/img/eye-close.png"
                    id="togglePasswordConfirm"
                    class="eye-icon"
                    
                >
            </div>

            <button type="submit" class="btn-login">
                Simpan Password
            </button>

        </form>

        <?php else: ?>
            <a href="login.php" class="btn-link">
                Login Sekarang
            </a>
        <?php endif; ?>

    </div>

</div>

<script src="../assets/js/login.js"></script>
</body>
</html>
