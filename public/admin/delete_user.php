<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Models\User;
use App\Helpers\Session;

Session::start();
$admin = Session::get('user');
if (!$admin || $admin['role'] !== 'admin') exit;

$id = intval($_GET['id']);

// ❌ ADMIN TIDAK BOLEH HAPUS DIRI SENDIRI
if ($admin['id'] == $id) {
    die("Admin tidak boleh menghapus dirinya sendiri.");
}

(new User())->deleteUser($id);
header("Location: users.php");
