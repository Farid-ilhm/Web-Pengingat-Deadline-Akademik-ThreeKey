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

// Debugging: Pastikan ENV terisi
if (empty($_ENV['GOOGLE_CLIENT_ID']) || empty($_ENV['GOOGLE_CLIENT_SECRET'])) {
    exit("Error: Environment variables (Client ID/Secret) kosong di server hosting. Periksa file .env.");
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
    exit("Error OAuth Google (Exchange Token): " . $token['error'] . " - " . ($token['error_description'] ?? ''));
}

if (!isset($token['access_token'])) {
    echo "<h1>Debug Info</h1>";
    echo "<pre>";
    print_r($token);
    echo "</pre>";
    exit("Error: Access Token tidak ditemukan dalam respon Google.");
}

// Cek kadaluarsa (Kemungkinan masalah waktu server)
if ($client->isAccessTokenExpired()) {
    exit("Error: Token yang baru saja diambil sudah kadaluarsa. Ini biasanya karena jam di server hosting anda meleset/tidak akurat.");
}

$client->setAccessToken($token['access_token']);

/* ================= GET GOOGLE USER ================= */
try {
    $service = new Oauth2($client);
    $googleUser = $service->userinfo->get();
} catch (\Exception $e) {
    echo "<h1>Fatal Error pada Userinfo Request</h1>";
    echo "Pesan Error: " . $e->getMessage() . "<br>";
    echo "HTTP Code: " . $e->getCode() . "<br>";
    echo "<hr><h3>Detail Client State:</h3>";
    echo "Redirect URI: " . $client->getRedirectUri() . "<br>";
    echo "Scopes yang Aktif: <pre>" . print_r($client->getScopes(), true) . "</pre>";
    exit();
}

$email = $googleUser->email;
$name = $googleUser->name;
$gid = $googleUser->id;
$picture = $googleUser->picture ?? null;

$userModel = new User();
$user = $userModel->findByEmail($email);

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
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'],
    'provider' => $user['provider'],
    'profile_pic' => $user['profile_pic'] ?? null
]);

header("Location: ../user/dashboard.php");
exit;
