<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Helpers\Session;

Session::start();

$auth = new AuthController();
$message = null;

$email = Session::get("pending_email");

if (!$email) {
    header("Location: register.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    $res = $auth->verifyOtp($email, $code);
    $message = $res['message'];

    if ($res['success']) {
        Session::set("pending_email", null);
        header("Location: login.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Verifikasi OTP</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body class="otp-page">

<div class="login-container single-column">

    <!-- ===== NAVY SECTION ===== -->
    <div class="navy-section">
        <h2>Pengingat Deadline Akademik</h2>

        <div class="logo-box small-logo">
            <img src="../assets/img/logo.png" alt="Logo">
        </div>
    </div>

    <!-- ===== WHITE SECTION ===== -->
    <div class="white-section center-content">

        <h2>Verifikasi OTP</h2>
        <span>Kode OTP telah dikirim ke</span>
        <p class="otp-email"><?= htmlspecialchars($email) ?></p>

        <?php if ($message): ?>
            <div class="error-msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <input
                type="text"
                name="code"
                class="otp-input"
                placeholder="••••••"
                maxlength="6"
                required
            >

            <button type="submit" class="btn-login">
                Verifikasi
            </button>
        </form>

        <form method="post" action="resend_otp.php">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <button type="submit" class="btn-resend">
                Kirim Ulang OTP
            </button>
        </form>

    </div>

</div>

</body>
</html>
