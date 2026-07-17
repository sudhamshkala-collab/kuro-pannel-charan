<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__.'/includes/db.php';
$pdo = db();

$input = json_decode(file_get_contents('php://input'), true);
$game   = trim($input['game']    ?? '');
$key    = trim($input['user_key'] ?? '');
$serial = trim($input['serial']  ?? '');

if (!$game || !$key || !$serial) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing game, user_key, or serial.']);
    exit;
}

$row = $pdo->prepare("SELECT * FROM `keys` WHERE user_key = ?");
$row->execute([$key]);
$row = $row->fetch();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Key not found.']);
    exit;
}
if ($row['status'] == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Key has been banned.']);
    exit;
}
if ($row['expired_date'] && strtotime($row['expired_date']) < time()) {
    echo json_encode(['status' => 'error', 'message' => 'Key has expired.']);
    exit;
}
if ($row['game'] !== 'BGMI/PUBG' && strtoupper($row['game']) !== strtoupper($game)) {
    echo json_encode(['status' => 'error', 'message' => 'Key is not valid for this game.']);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM devices WHERE key_id = ?");
$stmt->execute([$row['id']]);
$devCount = $stmt->fetchColumn();

$devCheck = $pdo->prepare("SELECT id FROM devices WHERE key_id = ? AND serial = ?");
$devCheck->execute([$row['id'], $serial]);
$dev = $devCheck->fetch();

if (!$dev) {
    if ($devCount >= $row['max_devices']) {
        echo json_encode(['status' => 'error', 'message' => 'Max device limit reached.']);
        exit;
    }
    $pdo->prepare("INSERT INTO devices (key_id, serial) VALUES (?, ?)")->execute([$row['id'], $serial]);
    $devCount++;
}

$expiry = $row['expired_date'] ? $row['expired_date'] : '9999-12-31 23:59:59';

echo json_encode([
    'status'  => 'success',
    'message' => 'Key verified successfully.',
    'game'    => $row['game'],
    'devices' => (int)$devCount,
    'max_devices' => (int)$row['max_devices'],
    'expires' => $expiry,
]);
