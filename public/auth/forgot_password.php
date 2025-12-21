<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Helpers\Session;

Session::start();

$auth = new AuthController();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Simpan email untuk halaman OTP
    Session::set("reset_email", $email);

    // Request OTP reset
    $res = $auth->requestResetOtp($email);
    $message = $res['message'];

    if ($res['success']) {
        header("Location: verify_reset_otp.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lupa Password</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/forgot.css">
</head>
<body>

<div class="login-container single-column">

    <!-- ================= NAVY SECTION (30%) ================= -->
    <header class="navy-section">
        <h2>Pengingat Deadline Akademik</h2>

        <div class="logo-box">
            <img src="../assets/img/logo.png" alt="Logo">
        </div>
    </header>

    <!-- ================= WHITE SECTION (70%) ================= -->
    <main class="white-section">

        <h2>Lupa Password</h2>
        <span class="subtitle">
            Masukkan email untuk menerima OTP reset password
        </span>

        <?php if ($message): ?>
            <div class="error-msg">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-box">

            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >

            <button type="submit" class="btn-login">
                Kirim OTP Reset
            </button>

            <p class="register">
                Ingat password?
                <a href="login.php">Login</a>
            </p>

        </form>

    </main>

</div>

</body>
</html>
