<?php
require __DIR__ . '/config.php';
$user = require_login();
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $pdo->query('SELECT activity_id, activity_name, activity_date, location, status
                         FROM activities ORDER BY activity_date, activity_id')->fetchAll();
    out(['activities' => $rows]);
}

// Everything below changes data -> admin only
require_admin();
$in = body();

if ($method === 'POST') {
    $name = clean($in['activity_name'] ?? '', 120);
    $loc  = clean($in['location'] ?? '', 120);
    $date = $in['activity_date'] ?? '';
    $status = ($in['status'] ?? 'Pending') === 'Completed' ? 'Completed' : 'Pending';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ($name === '' || $loc === '' || !$d || $d->format('Y-m-d') !== $date) {
        out(['error' => 'Activity, location and a valid date are required'], 400);
    }
    $pdo->prepare('INSERT INTO activities (activity_name, activity_date, location, status) VALUES (?,?,?,?)')
        ->execute([$name, $date, $loc, $status]);
    out(['ok' => true, 'id' => (int)$pdo->lastInsertId()], 201);
}

if ($method === 'PUT') {
    $id = (int)($in['activity_id'] ?? 0);
    $status = ($in['status'] ?? '') === 'Completed' ? 'Completed' : 'Pending';
    $pdo->prepare('UPDATE activities SET status = ? WHERE activity_id = ?')->execute([$status, $id]);
    out(['ok' => true]);
}

if ($method === 'DELETE') {
    $id = (int)($in['activity_id'] ?? 0);
    $pdo->prepare('DELETE FROM activities WHERE activity_id = ?')->execute([$id]);
    out(['ok' => true]);
}
out(['error' => 'Method not allowed'], 405);
