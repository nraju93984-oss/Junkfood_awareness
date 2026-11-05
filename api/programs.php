<?php
require __DIR__ . '/config.php';
require_login();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows = $pdo->query('SELECT p.program_id, p.title, p.tagline, p.description, p.date, p.location, p.icon,
                                COALESCE(SUM(r.participants),0) AS participants
                         FROM programs p LEFT JOIN reports r ON r.program_id = p.program_id
                         GROUP BY p.program_id ORDER BY p.date')->fetchAll();
    out(['programs' => $rows]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin();
    $in = body();
    $title = clean($in['title'] ?? '', 120);
    $loc = clean($in['location'] ?? '', 120);
    $date = $in['date'] ?? '';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ($title === '' || $loc === '' || !$d || $d->format('Y-m-d') !== $date) {
        out(['error' => 'Title, location and a valid date are required'], 400);
    }
    $pdo->prepare('INSERT INTO programs (title, tagline, description, date, location) VALUES (?,?,?,?,?)')
        ->execute([$title, clean($in['tagline'] ?? '', 160), clean($in['description'] ?? '', 2000), $date, $loc]);
    out(['ok' => true, 'id' => (int)$pdo->lastInsertId()], 201);
}
out(['error' => 'Method not allowed'], 405);
