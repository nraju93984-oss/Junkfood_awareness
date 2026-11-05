<?php
require __DIR__ . '/config.php';
$user = require_login();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT p.name, p.roll_no, p.branch, p.college, p.phone, p.address, p.batch,
                                  u.email, u.username, u.role
                           FROM users u LEFT JOIN profile p ON p.user_id = u.user_id
                           WHERE u.user_id = ?');
    $stmt->execute([$user['user_id']]);
    out(['profile' => $stmt->fetch()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = body();
    $name = clean($in['name'] ?? '', 100);
    $email = clean($in['email'] ?? '', 100);
    if ($name === '') out(['error' => 'Name is required'], 400);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) out(['error' => 'Enter a valid email'], 400);

    $phone = clean($in['phone'] ?? '', 20);
    if ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) out(['error' => 'Enter a valid mobile number'], 400);

    $pdo->beginTransaction();
    $pdo->prepare('UPDATE users SET email = ? WHERE user_id = ?')->execute([$email, $user['user_id']]);
    $pdo->prepare('UPDATE profile SET name=?, roll_no=?, branch=?, college=?, phone=?, batch=? WHERE user_id=?')
        ->execute([$name, clean($in['roll_no'] ?? '', 30), clean($in['branch'] ?? '', 60),
                   clean($in['college'] ?? '', 120), $phone, clean($in['batch'] ?? '', 80), $user['user_id']]);
    $pdo->commit();

    $_SESSION['user']['name'] = $name;
    out(['ok' => true, 'name' => $name]);
}
out(['error' => 'Method not allowed'], 405);
