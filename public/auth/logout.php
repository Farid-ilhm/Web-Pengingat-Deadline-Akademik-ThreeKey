<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Session;

Session::start();
Session::destroy();

/* ARAHKAN LANGSUNG KE LOGIN */
header('Location: login.php');
exit;
