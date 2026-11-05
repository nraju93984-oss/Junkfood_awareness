<?php
// ---- Database settings (XAMPP defaults: user root, empty password) ----
const DB_HOST = '127.0.0.1';
const DB_NAME = 'jfams';
const DB_USER = 'root';
const DB_PASS = '';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (PDOException $e) {
            out(['error' => 'Database connection failed. Is MySQL running and schema.sql imported?'], 500);
        }
    }
    return $pdo;
}

function out($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_login(): array {
    if (empty($_SESSION['user'])) out(['error' => 'Not logged in'], 401);
    return $_SESSION['user'];
}

function require_admin(): array {
    $u = require_login();
    if ($u['role'] !== 'admin') out(['error' => 'Admin access required'], 403);
    return $u;
}

function clean(?string $s, int $max = 200): string {
    return mb_substr(trim((string)$s), 0, $max);
}
