<?php
require __DIR__ . '/config.php';
require_login();
$pdo = db();

$totals = $pdo->query('SELECT COUNT(*) AS programs FROM programs')->fetch();
$rep = $pdo->query('SELECT COALESCE(SUM(participants),0) AS participants,
                           COALESCE(SUM(volunteers),0)   AS volunteers,
                           COALESCE(ROUND(AVG(after_score)),0) AS awareness
                    FROM reports')->fetch();
$locations = $pdo->query('SELECT COUNT(DISTINCT location) AS n FROM activities')->fetch();

$recent = $pdo->query('SELECT activity_name, location, status
                       FROM activities ORDER BY activity_date DESC, activity_id DESC LIMIT 5')->fetchAll();

$byProgram = $pdo->query('SELECT p.title, COALESCE(SUM(r.participants),0) AS participants
                          FROM programs p LEFT JOIN reports r ON r.program_id = p.program_id
                          GROUP BY p.program_id, p.title ORDER BY p.date')->fetchAll();

out([
    'programs'     => (int)$totals['programs'],
    'volunteers'   => (int)$rep['volunteers'],
    'participants' => (int)$rep['participants'],
    'locations'    => (int)$locations['n'],
    'awareness'    => (int)$rep['awareness'],
    'healthy_pct'  => (int)$rep['awareness'],
    'junk_pct'     => 100 - (int)$rep['awareness'],
    'recent'       => $recent,
    'by_program'   => $byProgram,
]);
