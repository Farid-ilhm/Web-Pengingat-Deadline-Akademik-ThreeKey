<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Helpers\Session;
use App\Config\Env;
use App\Services\AnalyticsService;

Env::load();
Session::start();

$admin = Session::get('user');
if (!$admin || $admin['role'] !== 'admin') exit;

$year = (int)($_GET['year'] ?? date('Y'));

$analytics = new AnalyticsService();

$totalUsers  = $analytics->countTotalUsersByYear($year);
$newUsers    = $analytics->countNewUsersByYear($year);
$newPerMonth = $analytics->newUsersPerMonth($year);

header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=laporan_admin_$year.csv");

$out = fopen('php://output', 'w');

fputcsv($out, ["LAPORAN ADMIN THREEKEY"]);
fputcsv($out, ["Tahun", $year]);
fputcsv($out, []);
fputcsv($out, ["Total User", $totalUsers]);
fputcsv($out, ["User Baru Tahun Ini", $newUsers]);
fputcsv($out, []);
fputcsv($out, ["Bulan", "User Baru"]);

$bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

foreach ($newPerMonth as $m => $v) {
    fputcsv($out, [$bulan[$m], $v]);
}

fclose($out);
exit;
