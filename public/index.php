<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;

Env::load();
Session::start();

$user = Session::get('user');
$isLogin = !empty($user);

$dashboardUrl = $isLogin
    ? ($user['role'] === 'admin'
        ? 'admin/dashboard.php'
        : 'user/dashboard.php')
    : null;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>ThreeKey — Pengingat Deadline Akademik</title>
  <meta name="description" content="ThreeKey membantu mahasiswa mengatur dan mengingat deadline akademik agar tetap teratur dan tepat waktu.">

  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
  <div class="nav-left">
    <img src="assets/img/logo.png" class="logo" alt="ThreeKey">
    <span class="brand">ThreeKey</span>
  </div>

  <div class="nav-right">
    <?php if ($isLogin): ?>
      <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="btn-outline">Dashboard</a>
      <a href="auth/logout.php" class="btn-primary">Logout</a>
    <?php else: ?>
      <a href="auth/login.php" class="btn-outline">Masuk</a>
      <a href="auth/register.php" class="btn-primary">Daftar</a>
    <?php endif; ?>
  </div>
</header>

<!-- ================= HERO ================= -->
<section class="hero">
  <h1>
    Atur dan ingat semua<br>
    <span>deadline akademikmu</span>
  </h1>

  <p>
    ThreeKey membantu pelajar tetap teratur,<br>
    tepat waktu, dan bebas dari lupa deadline.
  </p>

  <div class="hero-action">
    <?php if ($isLogin): ?>
      <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="btn-primary large">
        Buka Dashboard
      </a>
    <?php else: ?>
      <a href="auth/register.php" class="btn-primary large">
        Mulai Sekarang
      </a>
    <?php endif; ?>
  </div>
</section>

<!-- ================= SECTION 1 ================= -->
<section class="feature section-yellow">
  <div class="feature-image grid-4">
  <img src="assets/img/demo/5.jpg">
  <img src="assets/img/demo/2.jpg">
  <img src="assets/img/demo/3.jpg">
  <img src="assets/img/demo/6.jpg">
</div>


  <div class="feature-text">
    <h2>Visi ThreeKey</h2>
    <p>
      Menjadi platform pengingat deadline akademik yang membantu pelajar
      mengatur waktu secara efektif, teratur, dan konsisten.
    </p>

    <h3>Misi</h3>
    <ul>
      <li>Mencatat seluruh deadline akademik</li>
      <li>Memberikan pengingat tepat waktu</li>
      <li>Mengurangi risiko lupa tugas</li>
      <li>Meningkatkan produktivitas belajar</li>
    </ul>
  </div>
</section>

<!-- ================= SECTION 2 (REVERSE) ================= -->
<section class="feature reverse section-green">
  <div class="feature-image">
    <img src="assets/img/demo/4.jpg" alt="">
  </div>

  <div class="feature-text">
    <h2>Masalah yang Sering Terjadi</h2>
    <p>
      Banyak pelajar lupa deadline karena jadwal yang padat,
      tugas menumpuk, dan tidak ada sistem pengingat yang terpusat.
    </p>

    <p>
      Akibatnya, tugas terlambat dikumpulkan dan nilai menjadi tidak maksimal.
    </p>
  </div>
</section>

<!-- ================= SECTION 3 ================= -->
<section class="feature section-white">
  <div class="feature-image grid-4">
  <img src="assets/img/demo/10.jpg">
  <img src="assets/img/demo/7.jpg">
  <img src="assets/img/demo/9.jpg">
  <img src="assets/img/demo/1.jpg">
</div>


  <div class="feature-text">
    <h2>Solusi dari ThreeKey</h2>
    <p>
      ThreeKey hadir sebagai solusi pengingat deadline akademik
      yang sederhana, mudah digunakan, dan fokus pada kebutuhan pelajar.
    </p>

    <ul>
      <li>Kelola deadline per mata pelajaran</li>
      <li>Lihat jadwal dengan jelas</li>
      <li>Notifikasi agar tidak lupa</li>
    </ul>
  </div>
</section>

<!-- ================= SECTION 4 (REVERSE) ================= -->
<section class="feature reverse section-yellow">
  <div class="feature-image">
    <img src="assets/img/demo/8.jpg" alt="">
  </div>

  <div class="feature-text">
    <h2>Mulai Lebih Teratur Hari Ini</h2>
    <p>
      Jangan biarkan deadline mengejar kamu.
      Atur semuanya dari awal dan fokus pada hasil terbaik.
    </p>

    <?php if (!$isLogin): ?>
      <a href="auth/register.php" class="btn-primary">
        Daftar Sekarang
      </a>
    <?php endif; ?>
  </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
  <p>© <?= date('Y') ?> ThreeKey. Pengingat Deadline Akademik.</p>
</footer>

</body>
</html>
