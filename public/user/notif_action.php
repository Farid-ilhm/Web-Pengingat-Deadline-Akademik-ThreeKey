<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Session;
use App\Models\Notification;

header('Content-Type: application/json');

Session::start();
$user = Session::get('user');

if (!$user) {
    echo json_encode(['success' => false]);
    exit;
}

$id     = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id || !$action) {
    echo json_encode(['success' => false]);
    exit;
}

$notif = new Notification();

/* ===== MARK AS READ ===== */
if ($action === 'read') {
    $notif->markAsRead((int)$id, (int)$user['id']);

    echo json_encode([
        'success' => true,
        'unread'  => $notif->countUnread($user['id'])
    ]);
    exit;
}

/* ===== DELETE ===== */
if ($action === 'delete') {
    $notif->delete((int)$id, (int)$user['id']);

    echo json_encode([
        'success' => true,
        'unread'  => $notif->countUnread($user['id'])
    ]);
    exit;
}

echo json_encode(['success' => false]);
