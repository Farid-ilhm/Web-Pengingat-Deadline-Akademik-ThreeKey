<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Session;
use App\Models\User;

Session::start();
$user = Session::get('user');
if (!$user) {
    header("Location: ../auth/login.php");
    exit;
}

$activeMenu = 'profile';

$userModel = new User();
$data = $userModel->findById($user['id']);

/* ================= FOTO PROFIL ================= */
$uploadDir    = __DIR__ . '/../uploads/profiles/';
$defaultPhoto = '../assets/img/default-avatar.png';

$profilePhoto = $data['profile_pic'] ?? null;
if (!empty($profilePhoto) && file_exists($uploadDir . $profilePhoto)) {
    $photo = '../uploads/profiles/' . $profilePhoto;
} else {
    $photo = $defaultPhoto;
}

/* ================= STATUS GOOGLE CALENDAR ================= */
/*
 * FINAL RULE:
 * - User manual ATAU Google -> BISA connect Calendar
 * - Penentu HANYA provider_refresh_token
 */
$isCalendarConnected = !empty($data['provider_refresh_token']);
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kelola Akun — ThreeKey</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/account.css">
</head>

<body>

<?php include 'layout.php'; ?>

<main class="content">

    <h2 class="page-title">Kelola Akun</h2>

    <!-- ================= PROFILE CARD ================= -->
    <div class="account-card">
        <div class="account-header">

            <img src="<?= htmlspecialchars($photo) ?>" class="profile-photo" alt="Foto Profil">

            <div class="account-info">
                <div class="account-name">
                    <?= htmlspecialchars($data['name']) ?>
                </div>
                <div class="account-email">
                    <?= htmlspecialchars($data['email']) ?>
                </div>
            </div>

            <a href="edit_profile.php" class="btn-edit-profile">
                Edit Profil
            </a>

        </div>
    </div>

    <!-- ================= GOOGLE CALENDAR ================= -->
    <div class="account-card">
        <h3>Integrasi Google Calendar</h3>

        <?php if ($isCalendarConnected): ?>
            <p class="text-success">
                ✅ Google Calendar sudah terhubung.
            </p>
        <?php else: ?>
            <p class="text-warning">
                ⚠️ Google Calendar belum terhubung.
            </p>

            <p class="text-muted">
                Hubungkan Google Calendar untuk menyinkronkan jadwal akademik secara otomatis.
            </p>

            <a href="../google/connect_calendar.php" class="btn-primary">
                Hubungkan Google Calendar
            </a>
        <?php endif; ?>
    </div>

    <!-- ================= ACTIONS ================= -->
    <div class="account-actions">
        <a href="change_password.php" class="btn-secondary">
            Ganti Password
        </a>

        <a href="delete_account.php"
           onclick="return confirm('Yakin hapus akun?')"
           class="btn-danger">
            Hapus Akun
        </a>
    </div>

</main>

<script src="../assets/js/account.js"></script>

</body>
</html>
