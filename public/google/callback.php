<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client;
use Google\Service\Oauth2;
use App\Config\Env;
use App\Helpers\Session;
use App\Models\User;
use App\Config\Database;

Env::load();
Session::start();

if (!isset($_GET['code'])) {
    exit("Google OAuth tidak mengembalikan kode.");
}

/* ================= GOOGLE CLIENT ================= */
$client = new Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->setAccessType('offline');
$client->setPrompt('consent');

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    exit("Error OAuth Google: " . $token['error']);
}

$client->setAccessToken($token['access_token']);

/* ================= GET GOOGLE USER ================= */
$service    = new Oauth2($client);
$googleUser = $service->userinfo->get();

$email   = $googleUser->email;
$name    = $googleUser->name;
$gid     = $googleUser->id;
$picture = $googleUser->picture ?? null;

$userModel = new User();
$user      = $userModel->findByEmail($email);

/* ================= DOWNLOAD FOTO GOOGLE ================= */
$photoName = null;
if ($picture) {
    $imgData = @file_get_contents($picture);
    if ($imgData !== false) {
        $photoName = 'google_' . md5($email) . '.jpg';
        $savePath = __DIR__ . '/../uploads/profiles/' . $photoName;

        if (!file_exists($savePath)) {
            file_put_contents($savePath, $imgData);
        }
    }
}

$pdo = Database::getConnection();

/* ================= USER SUDAH ADA ================= */
if ($user) {

    // Jika sebelumnya manual → convert ke google
    if ($user['provider'] !== 'google') {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET provider = 'google',
                provider_id = ?,
                is_verified = 1
            WHERE email = ?
        ");
        $stmt->execute([$gid, $email]);
    }

    // Simpan foto Google jika user belum punya foto
    if ($photoName && empty($user['profile_pic'])) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET profile_pic = ?
            WHERE email = ?
        ");
        $stmt->execute([$photoName, $email]);
    }

    $user = $userModel->findByEmail($email);

/* ================= USER BARU ================= */
} else {

    $stmt = $pdo->prepare("
        INSERT INTO users 
        (name, email, provider, provider_id, is_verified, profile_pic)
        VALUES (?, ?, 'google', ?, 1, ?)
    ");
    $stmt->execute([$name, $email, $gid, $photoName]);

    $user = $userModel->findByEmail($email);
}

/* ================= SIMPAN REFRESH TOKEN ================= */
if (!empty($token['refresh_token'])) {
    $userModel->setRefreshToken($email, $token['refresh_token']);
}

/* ================= SET SESSION (PENTING) ================= */
Session::set('user', [
    'id'          => $user['id'],
    'name'        => $user['name'],
    'email'       => $user['email'],
    'role'        => $user['role'],
    'provider'    => $user['provider'],
    'profile_pic' => $user['profile_pic'] ?? null
]);

header("Location: ../user/dashboard.php");
exit;
