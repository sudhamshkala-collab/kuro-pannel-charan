<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__.'/../includes/db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$game       = trim($input['game']       ?? 'BGMI');
$duration   = (int)($input['duration']  ?? 24);
$maxDevices = (int)($input['max_devices'] ?? 1);
$qty        = min((int)($input['qty']    ?? 1), 100);
$note       = trim($input['note']       ?? '');

if ($qty < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1.']);
    exit;
}

$pdo   = db();
$keys  = [];

for ($i = 0; $i < $qty; $i++) {
    $key    = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
    $expiry = date('Y-m-d H:i:s', time() + $duration * 3600);
    $pdo->prepare("INSERT INTO `keys` (user_key, game, duration, max_devices, expired_date, note) VALUES (?, ?, ?, ?, ?, ?)")
        ->execute([$key, $game, $duration, $maxDevices, $expiry, $note]);
    $keys[] = $key;
}

echo json_encode([
    'status'  => 'success',
    'message' => count($keys) . ' key(s) generated.',
    'keys'    => $keys,
]);
