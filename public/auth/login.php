<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Controllers\AuthController;
use App\Helpers\Session;

Env::load();
Session::start();

$message = null;
$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $res = $auth->loginManual($email, $password);

    if ($res['success']) {
        header('Location: ' . ($res['role'] === 'admin'
            ? '../admin/dashboard.php'
            : '../user/dashboard.php'));
        exit;
    } else {
        $message = $res['message'];
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

<div class="login-container">

    <!-- ================= LEFT ================= -->
    <aside class="login-left">
        <h2>Pengingat Deadline Akademik</h2>

        <div class="logo-box">
            <img src="../assets/img/logo.png" alt="Logo">
        </div>

        <p>
            Tetap teratur, tetap tepat waktu,<br>
            dan jangan lewatkan satu deadline pun.
        </p>
    </aside>

    <!-- ================= RIGHT ================= -->
    <main class="login-right">

        <h2>Selamat Datang!</h2>
        <span>Silakan login untuk melanjutkan</span>

        <!-- ERROR MESSAGE -->
        <?php if ($message): ?>
            <div class="error-msg">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="on">

            <input
                type="email"
                name="email"
                placeholder="Email"
                autocomplete="username"
                required
            >

            <div class="password-wrapper">
                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    autocomplete="current-password"
                    required
                >
                <img
                    src="../assets/img/eye-close.png"
                    class="eye-icon"
                    alt="Toggle Password"
                >
            </div>

            <a href="forgot_password.php" class="forgot">
                Lupa Password?
            </a>

            <button type="submit" class="btn-login">
                Login
            </button>

            <br><br>
            
            <!-- LOGIN GOOGLE (BENAR) -->
            <a href="../google/login.php" class="btn-google">
                <img src="../assets/img/google.png" alt="Google">
                <span>Login dengan Google</span>
            </a>

            <p class="register">
                Belum punya akun?
                <a href="register.php">Daftar</a>
            </p>

        </form>

    </main>

</div>

<script src="../assets/js/login.js"></script>
</body>
</html>
