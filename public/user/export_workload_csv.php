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

$year     = $_GET['year'] ?? date('Y');
$semester = $_GET['semester'] ?? 'Ganjil';

$analytics = new AnalyticsService();

$weeklyData  = $analytics->workloadPerWeek($user['id'], (int)$year, $semester);
$subjectData = $analytics->workloadPerSubject($user['id'], (int)$year, $semester);
$summary     = $analytics->semesterSummary($user['id'], (int)$year, $semester);

header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=workload_{$semester}_{$year}.csv");

$out = fopen('php://output', 'w');

fputcsv($out, ['LAPORAN WORKLOAD SEMESTER']);
fputcsv($out, []);
fputcsv($out, ['Semester', $semester]);
fputcsv($out, ['Tahun', $year]);
fputcsv($out, ['Total Deadline', $summary['total_deadline']]);
fputcsv($out, ['Mata Pelajaran Terpadat', $summary['busiest_subject']]);
fputcsv($out, []);

fputcsv($out, ['WORKLOAD PER MINGGU']);
foreach ($weeklyData as $w => $t) {
    fputcsv($out, ["Minggu $w", $t]);
}

fputcsv($out, []);
fputcsv($out, ['WORKLOAD PER MATA PELAJARAN']);
foreach ($subjectData as $s => $t) {
    fputcsv($out, [$s, $t]);
}

fclose($out);
exit;
