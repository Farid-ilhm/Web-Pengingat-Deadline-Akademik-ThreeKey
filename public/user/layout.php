<?php
use App\Models\Notification;

$activeMenu = $activeMenu ?? '';

// ================== SESSION USER ==================
$user = $_SESSION['user'] ?? null;
$userId = $user['id'] ?? null;

// ================== NOTIFICATION INIT ==================
$notif = new Notification();
$countNotif = $userId ? $notif->countUnread($userId) : 0;
$items = $userId ? $notif->unreadByUser($userId) : [];

// ================== AVATAR INIT ==================
$profilePic = $user['profile_pic'] ?? null;

/*
Struktur:
public/
├── user/layout.php
├── uploads/profiles/xxxx.jpg
*/
if ($profilePic && file_exists(__DIR__ . '/../uploads/profiles/' . $profilePic)) {
  $avatarUrl = '../uploads/profiles/' . $profilePic;
} else {
  $avatarUrl = '../assets/img/default-avatar.png';
}
?>

<!-- ================= TOPBAR ================= -->
<header class="topbar">
  <div class="topbar-left">
    <button id="toggleSidebar" class="toggle-btn">
      <img src="../assets/img/icons/menu.png" alt="menu">
    </button>
    <img src="../assets/img/logo.png" class="logo" alt="logo">
    <span class="brand">THREE KEY</span>
  </div>

  <div class="topbar-right">

    <!-- 🔔 NOTIFICATION -->
    <div class="notif-wrapper">
      <img src="../assets/img/icons/bell.png" class="icon" id="notifToggle" alt="notif">

      <?php if ($countNotif > 0): ?>
        <span class="notif-badge" id="notifBadgeTop">
          <?= $countNotif ?>
        </span>
      <?php endif; ?>

      <!-- DROPDOWN -->
      <div class="notif-dropdown" id="notifDropdown">

        <div class="notif-header">
          <strong>Notifikasi</strong>
        </div>

        <div class="notif-list">
          <?php if (empty($items)): ?>
            <div class="notif-empty">
              Tidak ada notifikasi baru
            </div>
          <?php else: ?>
            <?php foreach ($items as $n): ?>
              <div class="notif-item unread">
                <div class="notif-title">
                  <?= htmlspecialchars($n['title']) ?>
                </div>
                <div class="notif-message">
                  <?= htmlspecialchars($n['message']) ?>
                </div>
                <div class="notif-time">
                  <?= date('d M Y H:i', strtotime($n['created_at'])) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="notif-footer">
          <a href="notifications.php">Lihat semua</a>
        </div>

      </div>
    </div>

    <!-- 👤 USER AVATAR (KLIK → KELOLA AKUN) -->
    <a href="account.php" class="user-profile" title="Kelola Akun">
      <span class="user-avatar">
        <img src="<?= $avatarUrl ?>" alt="Foto Profil">
      </span>
      <span class="user-name">
        <?= htmlspecialchars($user['name'] ?? '') ?>
      </span>
    </a>

  </div>
</header>

<div class="dashboard">

  <!-- ================= SIDEBAR ================= -->
  <aside class="sidebar">

    <a href="dashboard.php" class="menu <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
      <img src="../assets/img/icons/home.png">
      <span>Beranda</span>
    </a>

    <a href="subjects.php" class="menu <?= $activeMenu === 'subjects' ? 'active' : '' ?>">
      <img src="../assets/img/icons/book.png">
      <span>Mata Pelajaran</span>
    </a>

    <a href="schedules.php" class="menu <?= $activeMenu === 'schedules' ? 'active' : '' ?>">
      <img src="../assets/img/icons/schedule.png">
      <span>Jadwal</span>
    </a>

    <a href="gantt.php" class="menu <?= $activeMenu === 'gantt' ? 'active' : '' ?>">
      <img src="../assets/img/icons/gantt-chart.png">
      <span>Bagan Aktivitas</span>
    </a>

    <a href="notifications.php" class="menu <?= $activeMenu === 'notifications' ? 'active' : '' ?>"
      style="position: relative;">
      <img src="../assets/img/icons/bell.png">
      <span>Notifikasi</span>

      <?php if ($countNotif > 0): ?>
        <span class="notif-badge" id="notifBadgeSide" style="position:absolute; right:12px; top:10px;">
          <?= $countNotif ?>
        </span>
      <?php endif; ?>
    </a>

    <a href="account.php" class="menu <?= $activeMenu === 'profile' ? 'active' : '' ?>">
      <img src="../assets/img/icons/profile.png">
      <span>Akun</span>
    </a>

    <a href="#" class="menu logout" id="logoutBtn">
      <img src="../assets/img/icons/logout.png">
      <span>Logout</span>
    </a>

  </aside>

  <!-- ================= LOGOUT MODAL ================= -->
  <div class="logout-modal" id="logoutModal">
    <div class="logout-box">
      <h3>Konfirmasi Logout</h3>
      <p>Apakah kamu yakin ingin logout?</p>

      <div class="logout-actions">
        <button class="btn-cancel" id="cancelLogout">Batal</button>
        <a href="../auth/logout.php" class="btn-confirm">Ya, Logout</a>
      </div>
    </div>
  </div>

  <!-- ================= JS ================= -->
  <script src="../assets/js/layout.js"></script>