<?php
// layout.php (sidebar admin)
?>
<aside class="admin-sidebar">

  <div class="admin-brand">
    <img src="../assets/img/logo.png" class="admin-logo">
    <span>THREE KEY</span>
  </div>

  <nav class="admin-menu">
    <a href="dashboard.php" class="menu-item <?= $active === 'dashboard' ? 'active' : '' ?>">
      <img src="../assets/img/icons/home.png">
      Beranda
    </a>

    <a href="users.php" class="menu-item <?= $active === 'users' ? 'active' : '' ?>">
      <img src="../assets/img/icons/group.png">
      Kelola User
    </a>

    <a href="subjects.php" class="menu-item <?= $active === 'subjects' ? 'active' : '' ?>">
      <img src="../assets/img/icons/book.png">
      Mata Pelajaran
    </a>

    <a href="templates.php" class="menu-item <?= $active === 'templates' ? 'active' : '' ?>">
      <img src="../assets/img/icons/windows.png">
      Tipe Jadwal
    </a>

    <a href="#" class="menu-item logout" id="adminLogoutBtn">
      <img src="../assets/img/icons/logout.png">
      Logout
    </a>
    <!-- ================= ADMIN LOGOUT MODAL ================= -->
    <div class="logout-modal" id="adminLogoutModal">
      <div class="logout-box">
        <h3>Konfirmasi Logout</h3>
        <p>Apakah Anda yakin ingin logout dari akun admin?</p>

        <div class="logout-actions">
          <button class="btn-cancel" id="cancelAdminLogout">Batal</button>
          <a href="../auth/logout.php" class="btn-confirm">Ya, Logout</a>
        </div>
      </div>
    </div>

  </nav>

</aside>

<script src="../assets/js/admin_layout.js"></script>