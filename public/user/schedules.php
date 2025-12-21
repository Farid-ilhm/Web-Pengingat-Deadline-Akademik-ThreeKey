<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Template;
use App\Models\UserSubject;
use App\Models\Subject;
use App\Controllers\ScheduleController;
use App\Models\Schedule;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) {
  header('Location: ../auth/login.php');
  exit;
}

/* ================= OPSI A ================= */
$activeMenu = 'schedules';
/* ========================================== */

$templateModel = new Template();
$userSubjectModel = new UserSubject();
$subjectModel = new Subject();
$scheduleCtrl = new ScheduleController();
$scheduleModel = new Schedule();

$msg = null;
$templates = $templateModel->all();
$mySubjects = $userSubjectModel->allByUser($user['id']);
$globalSubjects = $subjectModel->all();
$schedules = $scheduleModel->allByUser($user['id']);

/* ================= HANDLE POST ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $userSubjectId = !empty($_POST['user_subject_id']) ? (int) $_POST['user_subject_id'] : null;
  $globalSubjectId = !empty($_POST['global_subject_id']) ? (int) $_POST['global_subject_id'] : null;
  $customName = trim($_POST['custom_subject_name'] ?? '');

  $templateId = (int) ($_POST['template_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');

  $startRaw = $_POST['start_datetime'] ?? '';
  $endRaw = $_POST['end_datetime'] ?? '';

  $startDatetime = $startRaw ? date('Y-m-d H:i:s', strtotime($startRaw)) : null;
  $endDatetime = $endRaw ? date('Y-m-d H:i:s', strtotime($endRaw)) : null;

  if (!$templateId || !$title || !$startDatetime) {
    $msg = "Template, judul, dan waktu mulai wajib diisi.";
  } else {

    $subjectChoice = [
      'existing_user_subject_id' => $userSubjectId,
      'global_subject_id' => $globalSubjectId,
      'custom_name' => $customName
    ];

    $result = $scheduleCtrl->addSchedule(
      $user['id'],
      $subjectChoice,
      $templateId,
      $title,
      $description,
      $startDatetime,
      $endDatetime
    );

    if ($result['success']) {
      header("Location: schedules.php?msg=added#top");
      exit;
    } else {
      $msg = "Gagal menambahkan jadwal.";
    }
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Jadwal — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/schedules.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">

    <a id="top"></a>

    <h2 class="page-title">Jadwal Akademik</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
      <div class="alert alert-success">
        ✅ <strong>Berhasil!</strong><br>
        Jadwal berhasil ditambahkan dan kini muncul di daftar Anda.
      </div>
    <?php endif; ?>

    <?php if ($msg): ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <!-- ================= LIST ================= -->
    <div class="card">
      <div class="card-header">
        <h3>Daftar Jadwal Saya</h3>
        <a href="#form-jadwal" class="btn-add">+ Tambah Jadwal</a>
      </div>

      <?php if (empty($schedules)): ?>
        <div class="empty-state">Belum ada jadwal</div>
      <?php else: ?>
        <ul class="schedule-list">
          <?php foreach ($schedules as $s): ?>
            <li class="schedule-item">
              <div class="schedule-info">
                <div class="schedule-title"><?= htmlspecialchars($s['title']) ?></div>
                <div class="schedule-meta">
                  <?= htmlspecialchars($s['subj_name']) ?> • <?= htmlspecialchars($s['template_name']) ?>
                </div>

                <?php if ($s['description']): ?>
                  <div class="schedule-desc">
                    <?= nl2br(htmlspecialchars($s['description'])) ?>
                  </div>
                <?php endif; ?>

                <div class="schedule-time">
                  <?= htmlspecialchars($s['start_datetime']) ?>
                </div>
              </div>

              <div class="schedule-actions">
                <a href="edit_schedule.php?id=<?= $s['id'] ?>" class="btn-edit">Edit</a>
                <a href="delete_schedule.php?id=<?= $s['id'] ?>" class="btn-hapus"
                  onclick="return confirm('Hapus jadwal ini?')">
                  Hapus
                </a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <!-- ================= FORM ================= -->
    <div class="card" id="form-jadwal">
      <h3>Kelola / Tambah Jadwal</h3>

      <form method="post" class="subject-form">

        <div class="form-group">
          <label>Mata Pelajaran Saya (opsional)</label>
          <select name="user_subject_id" id="userSubject">
            <option value="">-- pilih --</option>
            <?php foreach ($mySubjects as $ms): ?>
              <option value="<?= $ms['id'] ?>">
                <?= htmlspecialchars($ms['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="divider">atau</div>

        <div class="form-group">
          <label>Mata Pelajaran Global (opsional)</label>
          <select name="global_subject_id" id="globalSubject">
            <option value="">-- pilih global --</option>
            <?php foreach ($globalSubjects as $g): ?>
              <option value="<?= $g['id'] ?>">
                <?= htmlspecialchars($g['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="divider">atau</div>

        <div class="form-group">
          <label>Nama Mata Pelajaran Baru</label>
          <input type="text" name="custom_subject_name" id="customSubject">
        </div>

        <div class="form-group">
          <label>Tipe Jadwal</label>
          <select name="template_id" required>
            <option value="">-- pilih jadwal --</option>
            <?php foreach ($templates as $t): ?>
              <option value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Judul Jadwal</label>
          <input type="text" name="title" required>
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" maxlength="500"></textarea>
          <small class="char-counter">0 / 500</small>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Mulai</label>
            <input type="datetime-local" name="start_datetime" required>
          </div>
          <div class="form-group">
            <label>Selesai</label>
            <input type="datetime-local" name="end_datetime">
          </div>
        </div>

        <button class="btn-primary">Tambah Jadwal</button>
      </form>
    </div>

  </main>

  <!-- ================= JS ================= -->
  <script src="../assets/js/schedules.js"></script>

</body>

</html>