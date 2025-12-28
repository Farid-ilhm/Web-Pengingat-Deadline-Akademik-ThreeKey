<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client;
use App\Config\Env;
use App\Helpers\Session;

Env::load();
Session::start();

/* ================= PROTEKSI ================= */
$user = Session::get('user');
if (!$user) {
    header('Location: ../auth/login.php');
    exit;
}

/* ================= GOOGLE CLIENT ================= */
$client = new Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);

/* PENTING: redirect ke callback yang SAMA */
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

/* ================= SCOPE KHUSUS CALENDAR ================= */
$client->addScope(\Google\Service\Calendar::CALENDAR);

/* WAJIB agar dapat refresh_token */
$client->setAccessType('offline');

/* WAJIB agar Google SELALU menampilkan consent */
$client->setPrompt('consent select_account');

/* ================= REDIRECT KE GOOGLE ================= */
header('Location: ' . $client->createAuthUrl());
exit;
