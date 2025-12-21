<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Services\AnalyticsService;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) {
    header('Location: ../auth/login.php');
    exit;
}

$activeMenu = 'dashboard';

$analytics = new AnalyticsService();
$year     = $_GET['year'] ?? date('Y');
$semester = $_GET['semester'] ?? 'Ganjil';

$weeklyData  = $analytics->workloadPerWeek($user['id'], (int)$year, $semester);
$subjectData = $analytics->workloadPerSubject($user['id'], (int)$year, $semester);
$summary     = $analytics->semesterSummary($user['id'], (int)$year, $semester);

/* LABEL MINGGU */
function weekToMonthLabel(int $week, int $year): string {
    $date = new DateTime();
    $date->setISODate($year, $week);

    $bulan = [
        'January'=>'Januari','February'=>'Februari','March'=>'Maret',
        'April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli',
        'August'=>'Agustus','September'=>'September','October'=>'Oktober',
        'November'=>'November','December'=>'Desember'
    ];

    return "Minggu ke-" . ceil($date->format('j') / 7) . " " . $bulan[$date->format('F')];
}

$weeklyLabels = array_map(
    fn($w) => weekToMonthLabel((int)$w, (int)$year),
    array_keys($weeklyData)
);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>User Dashboard — ThreeKey</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'layout.php'; ?>

<main class="content">

<form method="get" class="filter-bar">
  <label>Tahun</label>
  <select name="year" onchange="this.form.submit()">
    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
      <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
    <?php endfor; ?>
  </select>

  <label>Semester</label>
  <select name="semester" onchange="this.form.submit()">
    <option <?= $semester === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
    <option <?= $semester === 'Genap' ? 'selected' : '' ?>>Genap</option>
  </select>
</form>

<section class="summary">
  <h3>Ringkasan Workload Semester (<?= $semester ?>) - <?= $year ?></h3>

  <div class="summary-wrapper">
    <p><strong>Total Deadline:</strong> <?= $summary['total_deadline'] ?></p>
    <p><strong>Minggu Paling Padat:</strong>
      <?= $summary['busiest_week'] ? weekToMonthLabel($summary['busiest_week'], $year) : '-' ?>
    </p>
    <p><strong>Mata Pelajaran Terpadat:</strong> <?= $summary['busiest_subject'] ?? '-' ?></p>
    <p><strong>Rekomendasi:</strong> <?= $summary['recommendation'] ?></p>
  </div>
</section>

<section class="charts">
  <div class="chart-box">
    <h4>Workload per Minggu</h4>
    <canvas id="chartWeek"></canvas>
  </div>

  <div class="chart-box">
    <h4>Workload per Mata Pelajaran</h4>
    <canvas id="chartSubject"></canvas>
  </div>
</section>

<div class="export-area">
  <a href="export_workload_csv.php?year=<?= $year ?>&semester=<?= $semester ?>" class="btn-export">
    Unduh Laporan CSV
  </a>
</div>

</main>

<script>
  // Data from PHP for Charts
  const chartWeekLabels = <?= json_encode($weeklyLabels) ?>;
  const chartWeekData = <?= json_encode(array_values($weeklyData)) ?>;
  const chartSubjectLabels = <?= json_encode(array_keys($subjectData) ?: []) ?>;
  const chartSubjectData = <?= json_encode(array_values($subjectData) ?: []) ?>;
</script>
<script src="../assets/js/dashboard.js"></script>

</body>
</html>
