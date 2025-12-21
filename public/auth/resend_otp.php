<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AuthController;

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $auth->resendOtp($email);
}

header("Location: verify_otp.php");
exit;
