<?php
// Run ONCE in the browser: http://localhost/jfams/install.php  (then delete this file)
require __DIR__ . '/api/config.php';
$pdo = db();

if ($pdo->query("SELECT COUNT(*) AS n FROM users")->fetch()['n'] > 0) {
    out(['message' => 'Already installed. Delete install.php.']);
}

$users = [
    ['admin',     'admin123',     'admin', 'admin@college.edu',     'Admin',     '21XX0001', 'Computer Science'],
    ['volunteer', 'volunteer123', 'user',  'volunteer@college.edu', 'Volunteer', '21XX0002', 'Computer Science'],
];
foreach ($users as [$u, $p, $role, $email, $name, $roll, $branch]) {
    $pdo->prepare('INSERT INTO users (username, password, role, email) VALUES (?,?,?,?)')
        ->execute([$u, password_hash($p, PASSWORD_DEFAULT), $role, $email]);
    $id = $pdo->lastInsertId();
    $pdo->prepare('INSERT INTO profile (user_id, name, roll_no, branch, college, phone, batch) VALUES (?,?,?,?,?,?,?)')
        ->execute([$id, $name, $roll, $branch, '[College Name]', '9000000000', '2026 — Junk Food Awareness Drive']);
}
out(['message' => 'Installed. Login with admin / admin123 or volunteer / volunteer123. Now delete install.php.']);
