<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Controllers\AuthController;
use App\Helpers\Session;

Env::load();
Session::start();

$auth = new AuthController();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "Konfirmasi password tidak sama!";
    } else {
        $res = $auth->register($name, $email, $password);
        $message = $res['message'];

        if ($res['success']) {
            Session::set("pending_email", $email);
            header("Location: verify_otp.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Registrasi</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-container">

    <!-- LEFT (FORM REGISTER) -->
    <div class="login-right">

        <h2>Buat Akun Baru</h2>
        <span>Silakan isi formulir di bawah ini</span>

        <?php if ($message): ?>
            <p class="error-msg"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">

            <input
                type="text"
                name="name"
                placeholder="Nama Lengkap"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >

            <!-- PASSWORD -->
            <div class="password-wrapper">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    required
                >
                <img
                    src="../assets/img/eye-close.png"
                    class="eye-icon"
                    id="togglePassword"
                >
            </div>

            <!-- KONFIRMASI PASSWORD -->
            <div class="password-wrapper">
                <input
                    type="password"
                    name="confirm_password"
                    id="confirmPassword"
                    placeholder="Konfirmasi Password"
                    required
                >
                <img
                    src="../assets/img/eye-close.png"
                    class="eye-icon"
                    id="toggleConfirmPassword"
                >
            </div>

            <button type="submit" class="btn-login">
                Daftar
            </button>

            <p class="register">
                Sudah punya akun? <a href="login.php">Login</a>
            </p>

        </form>
    </div>

    <!-- RIGHT (INFO / LOGO) -->
    <div class="login-left">

        <h2>Pengingat Deadline Akademik</h2>

        <div class="logo-box">
            <img src="../assets/img/logo.png" alt="Logo">
        </div>

        <p>
            Tetap teratur, tetap tepat waktu,<br>
            dan jangan lewatkan satu deadline pun.
        </p>

    </div>

</div>

<!-- JS -->
<script src="../assets/js/login.js"></script>

</body>
</html>
