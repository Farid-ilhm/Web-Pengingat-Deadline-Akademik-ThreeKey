<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Helpers\Session;

Session::start();

$auth = new AuthController();
$message = null;

$email = Session::get("reset_email");

// Jika email tidak ada, kembalikan ke lupa password
if (!$email) {
    header("Location: forgot_password.php");
    exit;
}

/* =====================================================
   KIRIM ULANG OTP (GET ?resend=1)
===================================================== */
if (isset($_GET['resend'])) {

    $lastSent = Session::get("otp_last_sent");

    if ($lastSent && (time() - $lastSent) < 60) {
        $message = "Silakan tunggu 60 detik sebelum mengirim ulang OTP.";
    } else {
        $res = $auth->requestResetOtp($email);
        Session::set("otp_last_sent", time());
        $message = $res['message'];
    }
}

/* =====================================================
   VERIFIKASI OTP
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otp = trim($_POST['otp']);

    $res = $auth->verifyResetOtp($email, $otp);
    $message = $res['message'];

    if ($res['success']) {
        header("Location: reset_password.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Verifikasi OTP</title>

    <!-- CSS sama dengan login / forgot password -->
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-container single-column">

    <!-- ================= NAVY SECTION (30%) ================= -->
    <div class="login-left navy-section">
        <h2>Pengingat Deadline Akademik</h2>

        <div class="logo-box small-logo">
            <img src="../assets/img/logo.png" alt="Logo">
        </div>
    </div>

    <!-- ================= WHITE SECTION (70%) ================= -->
    <div class="login-right white-section center-content">

        <h2>Verifikasi OTP</h2>

        <span class="subtitle">
            Masukkan kode OTP yang dikirim ke<br>
            <b><?= htmlspecialchars($email) ?></b>
        </span>

        <?php if ($message): ?>
            <div class="error-msg">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">

            <input
                type="text"
                name="otp"
                placeholder="KODE OTP"
                class="otp-input"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                required
            >

            <button type="submit" class="btn-login">
                Verifikasi
            </button>

            <p class="register">
                Tidak menerima kode?
                <a href="verify_reset_otp.php?resend=1">Kirim ulang OTP</a>
            </p>

            <p class="register">
                Salah email?
                <a href="forgot_password.php">Ulangi</a>
            </p>

        </form>

    </div>

</div>

</body>
</html>
