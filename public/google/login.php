<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;
use App\Config\Env;

Env::load();

$client = new Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);


 
/*WAJIB: tambah scope Google Calendar*/
$client->addScope([
    'email',
    'profile',
]);

/*WAJIB: Minta refresh token, supaya event bisa disimpan dari server*/
$client->setAccessType('offline');

/*WAJIB: Memaksa Google menampilkan consent agar refresh_token diberikan.
Tanpa ini → refresh_token tidak akan diberikan!*/
$client->setPrompt('consent select_account');

/*Redirect user ke Google OAuth URL*/
header("Location: " . $client->createAuthUrl());
exit;