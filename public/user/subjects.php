<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Env;
use App\Helpers\Session;
use App\Models\Subject;
use App\Models\UserSubject;

Env::load();
Session::start();

$user = Session::get('user');
if (!$user) {
  header('Location: ../auth/login.php');
  exit;
}

/* ================= OPSI A ================= */
// halaman ini adalah menu Mata Pelajaran
$activeMenu = 'subjects';
/* ========================================== */

$subjectModel = new Subject();
$userSubjectModel = new UserSubject();

$msg = null;

/* ================= HANDLE MESSAGE ================= */
if (isset($_GET['msg'])) {
  if ($_GET['msg'] === 'added') {
    $msg = "Mata pelajaran berhasil ditambahkan.";
  } elseif ($_GET['msg'] === 'deleted') {
    $msg = "Mata pelajaran berhasil dihapus.";
  } elseif ($_GET['msg'] === 'forbidden') {
    $msg = "Gagal menghapus mata pelajaran.";
  } elseif ($_GET['msg'] === 'exists') {
    $msg = "Mata pelajaran tersebut sudah ada di daftar Anda.";
  }
}

/* ================= HANDLE DELETE ================= */
if (isset($_GET['delete'])) {
  $deleteId = (int) $_GET['delete'];
  $subject = $userSubjectModel->find($deleteId);

  if ($subject && $subject['user_id'] == $user['id']) {
    $userSubjectModel->delete($deleteId, $user['id']);
    header("Location: subjects.php?msg=deleted");
    exit;
  } else {
    header("Location: subjects.php?msg=forbidden");
    exit;
  }
}

/* ================= HANDLE ADD ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $globalId = !empty($_POST['global_subject'])
    ? (int) $_POST['global_subject']
    : null;

  $custom = trim($_POST['custom_name'] ?? '');
  $note = trim($_POST['note'] ?? '');

  if (strlen($note) > 500) {
    $msg = "Catatan maksimal 500 karakter.";
  } else {

    if ($globalId) {

      if ($exists) {
        header("Location: subjects.php?msg=exists#top");
        exit;
      }

      $g = $subjectModel->find($globalId);
      $name = $g ? $g['name'] : 'Unnamed Subject';

      // CEK LAGI SECARA NAMA (untuk mencegah duplikat dengan custom yg namanya sama)
      $existsByName = $userSubjectModel->findByUserAndName($user['id'], $name);
      if ($existsByName) {
        header("Location: subjects.php?msg=exists#top");
        exit;
      }

      $userSubjectModel->create(
        $user['id'],
        $globalId,
        $name,
        $note
      );

      header("Location: subjects.php?msg=added#top");
      exit;

    } elseif ($custom !== '') {

      // CEK DUPLIKAT NAMA
      $existsByName = $userSubjectModel->findByUserAndName($user['id'], $custom);
      if ($existsByName) {
        header("Location: subjects.php?msg=exists#top");
        exit;
      }

      $userSubjectModel->create(
        $user['id'],
        null,
        $custom,
        $note
      );

      header("Location: subjects.php?msg=added#top");
      exit;

    } else {
      $msg = "Pilih mata pelajaran global atau isi nama sendiri.";
    }
  }
}

/* ================= DATA ================= */
$globalSubjects = $subjectModel->all();
$mySubjects = $userSubjectModel->allByUser($user['id']);
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mata Pelajaran — ThreeKey</title>

  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/subjects.css">
</head>

<body>

  <?php include 'layout.php'; ?>

  <main class="content">
    <a id="top"></a>

    <h2 class="page-title">Mata Pelajaran</h2>

    <?php if ($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h3>Daftar Mata Pelajaran Saya</h3>
        <a href="#kelola-mapel" class="btn-add-mapel">+ Tambah Mata Pelajaran</a>
      </div>

      <?php if (empty($mySubjects)): ?>
        <div class="empty-state">Belum ada mata pelajaran</div>
      <?php else: ?>
        <ul class="subject-list">
          <?php foreach ($mySubjects as $s): ?>
            <li class="subject-item-card">
              <div class="subject-info">
                <div class="subject-title">
                  <?= htmlspecialchars($s['name']) ?>
                  <?php if ($s['global_name']): ?>
                    <span class="badge-global">(Global)</span>
                  <?php endif; ?>
                </div>

                <?php if (!empty($s['note'])): ?>
                  <div class="subject-desc">
                    <?= nl2br(htmlspecialchars($s['note'])) ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="subject-actions">
                <a href="edit_subjects.php?id=<?= $s['id'] ?>" class="btn-edit">Edit</a>
                <a href="subjects.php?delete=<?= $s['id'] ?>" class="btn-hapus"
                  onclick="return confirm('Hapus mata pelajaran ini?')">
                  Hapus
                </a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <div class="card" id="kelola-mapel">
      <h3>Kelola / Tambah Mata Pelajaran</h3>

      <form method="post" class="subject-form">

        <div class="form-group">
          <label>Mata Pelajaran Global (opsional)</label>
          <select name="global_subject">
            <option value="">-- pilih --</option>
            <?php foreach ($globalSubjects as $g): ?>
              <option value="<?= $g['id'] ?>">
                <?= htmlspecialchars($g['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="divider">atau</div>

        <div class="form-group">
          <label>Nama Mata Pelajaran Sendiri</label>
          <input type="text" name="custom_name" placeholder="Contoh: Basis Data">
        </div>

        <div class="form-group">
          <label>Catatan</label>
          <textarea name="note" maxlength="500"></textarea>
          <small class="char-counter">0 / 500</small>
        </div>

        <button class="btn-primary">Tambah Mata Pelajaran</button>
      </form>
    </div>

  </main>
  </div> <!-- Closing .dashboard -->

  <!-- ================= JS ================= -->
  <script src="../assets/js/subjects.js"></script>

</body>

</html>