<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Session;
use App\Config\Env;
use App\Services\AnalyticsService;

/* ================= INIT APP ================= */
Env::load();
Session::start();

/* ================= AUTH ================= */
$admin = Session::get('user');
if (!$admin || $admin['role'] !== 'admin') {
  header('Location: ../auth/login.php');
  exit;
}

/* ================= SET ACTIVE MENU ================= */
$active = 'dashboard';

/* ================= SERVICES ================= */
$analytics = new AnalyticsService();

/* ================= DATA ================= */
$availableYears = $analytics->getAvailableYears();
$year = isset($_GET['year'])
  ? (int) $_GET['year']
  : max($availableYears);

$totalUsers = $analytics->countTotalUsersByYear($year);
$newUsersYear = $analytics->countNewUsersByYear($year);
$totalPerMonth = $analytics->totalUsersPerMonth($year);
$newPerMonth = $analytics->newUsersPerMonth($year);

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Beranda Admin — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/admin_layout.css">
  <link rel="stylesheet" href="../assets/css/admin_dashboard.css">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <div class="admin-dashboard">

    <!-- ================= SIDEBAR ================= -->
    <?php include 'layout.php'; ?>

    <!-- ================= CONTENT ================= -->
    <main class="admin-content">
      <div class="admin-container">

        <!-- ================= HEADER ================= -->
        <div class="dashboard-header">
          <h2>Beranda Admin</h2>
          <p>Selamat datang, <strong><?= htmlspecialchars($admin['name']) ?></strong></p>
        </div>

        <!-- ================= FILTER ================= -->
        <form method="get" class="filter-bar" style="margin-bottom:24px;">
          <label><strong>Tahun:</strong></label>
          <select name="year" onchange="this.form.submit()">
            <?php foreach ($availableYears as $y): ?>
              <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>>
                <?= $y ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>

        <!-- ================= INFO CARDS ================= -->
        <div class="dashboard-cards">
          <div class="info-card">
            <h4>👥 Total User (hingga <?= $year ?>)</h4>
            <div class="value"><?= $totalUsers ?></div>
          </div>

          <div class="info-card">
            <h4>🆕 User Baru Tahun <?= $year ?></h4>
            <div class="value"><?= $newUsersYear ?></div>
          </div>
        </div>

        <!-- ================= CHARTS ================= -->
        <div class="charts">

          <div class="chart-box">
            <h3>📈 Pertumbuhan User</h3>
            <canvas id="chartTotalUser"></canvas>
            <p class="chart-note">
              Grafik akumulasi jumlah user terdaftar per bulan.
            </p>
          </div>

          <div class="chart-box">
            <h3>📊 User Baru per Bulan</h3>
            <canvas id="chartNewUser"></canvas>
            <p class="chart-note">
              Grafik jumlah user baru yang mendaftar setiap bulan.
            </p>
          </div>

        </div>

        <!-- ================= EXPORT ================= -->
        <a href="export_users_csv.php?year=<?= $year ?>" class="btn-export">
          Unduh Laporan CSV
        </a>

      </div>
    </main>

  </div>

  <!-- ================= CHART SCRIPT ================= -->
  <script>
    const adminChartLabels = <?= json_encode($months) ?>;
    const adminChartTotalUsers = <?= json_encode(array_values($totalPerMonth)) ?>;
    const adminChartNewUsers = <?= json_encode(array_values($newPerMonth)) ?>;
  </script>
  <script src="../assets/js/admin_dashboard.js"></script>


</body>

</html>