<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\OTP;
use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Email;

Session::start();
$user = Session::get('user');
if (!$user) {
  header("Location: ../auth/login.php");
  exit;
}

$error = null;
$success = null;

$otpModel = new OTP();
$userModel = new User();

/* ================= RESEND OTP ================= */
if (isset($_POST['resend'])) {

  $last = $otpModel->latestActive($user['email']);

  if ($last && (time() - strtotime($last['created_at'])) < 60) {
    $error = "Silakan tunggu 1 menit sebelum meminta OTP ulang.";
  } else {
    $otpCode = rand(100000, 999999);
    $expired = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    $otpModel->create($user['email'], $otpCode, $expired);

    Email::send(
      $user['email'],
      "Kode OTP Perubahan Email",
      "Kode OTP Anda: <b>$otpCode</b><br>Berlaku 5 menit."
    );

    $success = "OTP baru berhasil dikirim ke email Anda.";
  }
}

/* ================= VERIFY OTP ================= */
if (isset($_POST['verify'])) {

  $inputOtp = trim($_POST['otp']);
  $otp = $otpModel->latestActive($user['email']);

  if (!$otp) {
    $error = "OTP tidak ditemukan.";
  } elseif ($otp['code'] !== $inputOtp) {
    $error = "Kode OTP salah.";
  } elseif (strtotime($otp['expires_at']) < time()) {
    $error = "Kode OTP sudah kadaluarsa.";
  } else {

    $otpModel->markUsed($otp['id']);

    $currentUser = $userModel->findById($user['id']);

    // Hapus foto lama jika diganti
    if (
      Session::get('pending_profile_photo') &&
      !empty($currentUser['profile_photo'])
    ) {
      $old = __DIR__ . '/../uploads/profiles/' . $currentUser['profile_photo'];
      if (file_exists($old))
        unlink($old);
    }

    // Update profil final
    $userModel->updateProfile(
      $user['id'],
      Session::get('pending_profile_name'),
      Session::get('pending_new_email'),
      Session::get('pending_profile_photo')
    );

    // Update session user
    Session::set('user', array_merge($user, [
      'name' => Session::get('pending_profile_name'),
      'email' => Session::get('pending_new_email')
    ]));

    // Bersihkan session sementara
    Session::set('pending_new_email', null);
    Session::set('pending_profile_name', null);
    Session::set('pending_profile_photo', null);

    header("Location: account.php?success=email_changed");
    exit;
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Verifikasi Email</title>

  <link rel="stylesheet" href="../assets/css/verify_otp.css">
</head>

<body>

  <div class="verify-card">

    <h3>Verifikasi Perubahan Email</h3>

    <?php if ($error): ?>
      <div class="alert alert-error auto-hide">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success auto-hide">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="verify-form">
      <input type="text" name="otp" placeholder="Masukkan OTP" maxlength="6" required>

      <button class="btn-primary" name="verify">
        Verifikasi
      </button>
    </form>

    <form method="post">
      <button class="btn-link" name="resend">
        Kirim ulang OTP
      </button>
    </form>

  </div>

  <script src="../assets/js/verify_change_email_otp.js"></script>

</body>

</html>