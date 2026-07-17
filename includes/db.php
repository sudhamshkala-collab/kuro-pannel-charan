<?php
define('APP_NAME', 'Charan Panel');
define('OWNER_USER', 'CHARANYT');
define('OWNER_PASS', 'Charan@123');
define('DB_HOST', 'localhost');
define('DB_NAME', 'charan_panel');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_URL', 'http://localhost/charan-panel');
define('TG_BOT_TOKEN', '8131839739:AAETEepNJZ0_117EFmUmckg2dH4_RLcYx3s');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die(json_encode(['status' => false, 'reason' => 'DB Error']));
        }
    }
    return $pdo;
}

function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin'])) {
        header('Location: login.php');
        exit;
    }
}
