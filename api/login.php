<?php
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(['error' => 'POST required'], 405);

$in = body();
$username = clean($in['username'] ?? '', 50);
$password = (string)($in['password'] ?? '');
if ($username === '' || $password === '') out(['error' => 'Enter username and password'], 400);

$stmt = db()->prepare('SELECT u.user_id, u.username, u.password, u.role, p.name
                       FROM users u LEFT JOIN profile p ON p.user_id = u.user_id
                       WHERE u.username = ?');
$stmt->execute([$username]);
$row = $stmt->fetch();

if (!$row || !password_verify($password, $row['password'])) {
    usleep(300000); // slow down brute-force guessing
    out(['error' => 'Invalid username or password'], 401);
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'user_id'  => (int)$row['user_id'],
    'username' => $row['username'],
    'role'     => $row['role'],
    'name'     => $row['name'] ?: $row['username'],
];
out(['user' => $_SESSION['user']]);
