<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Schedule;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) {
  header('Location: ../auth/login.php');
  exit;
}

$scheduleModel = new Schedule();
$schedules = $scheduleModel->allByUser($user['id']);

/* ================= DATA GANTT ================= */
$datasets = [];
$minDate = null;
$maxDate = null;

$colorMap = [
  'Tugas' => '#f1c40f',
  'Ujian' => '#e74c3c',
  'Project' => '#3498db',
  'Hafalan' => '#e67e22',
  'Kuis' => '#9b59b6',
  'Pengingat Lainnya' => '#7f8c8d'
];

foreach ($schedules as $row) {
  $start = date('Y-m-d', strtotime($row['start_datetime']));
  $end = date('Y-m-d', strtotime($row['end_datetime']));

  $type = $row['template_name'] ?? 'Pengingat Lainnya';

  // Mapping 'Deadline' -> 'Hafalan'
  if ($type === 'Deadline') {
    $type = 'Hafalan';
  }

  $color = $colorMap[$type] ?? '#95a5a6';

  $datasets[] = [
    'x' => [$start, $end],
    'y' => $row['title'],
    'backgroundColor' => $color
  ];

  if (!$minDate || $start < $minDate)
    $minDate = $start;
  if (!$maxDate || $end > $maxDate)
    $maxDate = $end;
}

$minDate = $minDate ?? date('Y-m-01');
$maxDate = $maxDate ?? date('Y-m-t');

$activeMenu = 'gantt';
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Gantt Chart — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

  <style>
    /* ================= GANTT (LOCAL ONLY) ================= */
    .gantt-card {
      background: #fffbe6;
      border: 2px solid var(--gold);
      border-radius: 20px;
      padding: 24px;
    }

    .gantt-title {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 12px;
      color: var(--navy);
    }

    /* LEGEND */
    .gantt-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 16px;
      font-size: 14px;
      font-weight: 600;
    }

    .gantt-legend div {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .legend-color {
      width: 12px;
      height: 12px;
      border-radius: 3px;
      display: inline-block;
    }

    .legend-color.tugas {
      background: #f1c40f;
    }

    .legend-color.ujian {
      background: #e74c3c;
    }

    .legend-color.project {
      background: #3498db;
    }

    .legend-color.hafalan {
      background: #e67e22;
    }

    .legend-color.kuis {
      background: #9b59b6;
    }

    .legend-color.lainnya {
      background: #7f8c8d;
    }

    /* CANVAS FIX (ANTI MELAR) */
    .gantt-canvas-wrap {
      height: 360px;
    }
  </style>
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <div class="gantt-card">
      <div class="gantt-title">Timeline Jadwal</div>

      <?php if (empty($datasets)): ?>
        <div class="empty-state">Belum ada jadwal.</div>
      <?php else: ?>

        <!-- LEGEND -->
        <div class="gantt-legend">
          <div><span class="legend-color tugas"></span> Tugas</div>
          <div><span class="legend-color ujian"></span> Ujian</div>
          <div><span class="legend-color project"></span> Project</div>
          <div><span class="legend-color hafalan"></span> Hafalan</div>
          <div><span class="legend-color kuis"></span> Kuis</div>
          <div><span class="legend-color lainnya"></span> Pengingat Lainnya</div>
        </div>

        <div class="gantt-canvas-wrap">
          <canvas id="ganttChart"></canvas>
        </div>

      <?php endif; ?>
    </div>

  </main>

  <?php if (!empty($datasets)): ?>
    <script>
      const ganttDatasets = <?= json_encode($datasets) ?>;
      const ganttMinDate = '<?= $minDate ?>';
      const ganttMaxDate = '<?= $maxDate ?>';
    </script>
  <?php endif; ?>

  <script src="../assets/js/gantt.js"></script>

</body>

</html>