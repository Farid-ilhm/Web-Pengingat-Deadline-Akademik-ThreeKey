<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\User;
use App\Models\OTP;
use App\Helpers\Session;
use App\Helpers\Email;

Session::start();
$sessionUser = Session::get('user');

if (!$sessionUser) {
  header("Location: ../auth/login.php");
  exit;
}

$activeMenu = 'profile';

$userModel = new User();
$data = $userModel->findById($sessionUser['id']);

$error = null;

/* ================= FORM SUBMIT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');

  // User Google tidak boleh ganti email
  if ($data['provider'] === 'google') {
    $email = $data['email'];
  }

  /* ================= FOTO PROFIL ================= */
  $uploadDir = __DIR__ . '/../uploads/profiles/';
  $photoName = $data['profile_pic']; // default pakai foto lama

  if (!empty($_FILES['photo']['name'])) {

    $allowedExt = ['jpg', 'jpeg', 'png'];
    $tmp = $_FILES['photo']['tmp_name'];
    $size = $_FILES['photo']['size'];
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
      $error = "Format foto harus JPG atau PNG.";
    } elseif ($size > 2 * 1024 * 1024) {
      $error = "Ukuran foto maksimal 2MB.";
    } elseif (!getimagesize($tmp)) {
      $error = "File bukan gambar yang valid.";
    } else {

      // hapus foto lama (jika ada)
      if (!empty($data['profile_pic'])) {
        $oldPhoto = $uploadDir . $data['profile_pic'];
        if (file_exists($oldPhoto)) {
          unlink($oldPhoto);
        }
      }

      // simpan foto baru
      $photoName = uniqid('profile_') . '.' . $ext;
      move_uploaded_file($tmp, $uploadDir . $photoName);
    }
  }

  if (!$error) {

    /* ================= EMAIL TIDAK BERUBAH ================= */
    if ($email === $data['email']) {

      $userModel->updateProfile(
        $data['id'],
        $name,
        $email,
        $photoName
      );

      // 🔥 UPDATE SESSION (SATU KALI, FINAL)
      Session::set('user', [
        'id' => $sessionUser['id'],
        'name' => $name,
        'email' => $email,
        'role' => $sessionUser['role'],
        'provider' => $sessionUser['provider'],
        'profile_pic' => $photoName
      ]);

      header("Location: account.php");
      exit;
    }

    /* ================= EMAIL BERUBAH → OTP ================= */
    $otpCode = rand(100000, 999999);
    $expired = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    (new OTP())->create($data['email'], $otpCode, $expired);

    Session::set('pending_new_email', $email);
    Session::set('pending_profile_name', $name);
    Session::set('pending_profile_photo', $photoName);

    Email::send(
      $data['email'],
      "Verifikasi Perubahan Email",
      "Kode OTP Anda: <b>$otpCode</b><br>Berlaku selama 5 menit."
    );

    header("Location: verify_change_email_otp.php");
    exit;
  }
}
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Edit Profil — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/edit_profile.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <h2 class="page-title center-title">Edit Profil</h2>

    <div class="edit-profile-card center-card">

      <?php if ($error): ?>
        <div class="edit-profile-error">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="edit-profile-form">

        <div class="form-group">
          <label>Nama</label>
          <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>"
            <?= $data['provider'] === 'google' ? 'readonly' : '' ?>>
        </div>

        <div class="form-group">
          <label>Foto Profil</label>
          <input type="file" name="photo" accept=".jpg,.jpeg,.png">
        </div>

        <button type="submit" class="btn-save-profile">
          Simpan Perubahan
        </button>

      </form>

    </div>

  </main>

  <script src="../assets/js/edit_profile.js"></script>

</body>

</html>