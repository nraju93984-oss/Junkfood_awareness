<?php
require __DIR__ . '/config.php';
require_login();
$pdo = db();

$rows = $pdo->query('SELECT p.title, p.date, p.location, r.participants, r.volunteers,
                            r.male_count, r.female_count, r.feedback, r.before_score, r.after_score
                     FROM reports r JOIN programs p ON p.program_id = r.program_id
                     ORDER BY p.date')->fetchAll();

$sum = $pdo->query('SELECT COUNT(*) AS programs,
                           COALESCE(SUM(participants),0) AS participants,
                           COALESCE(SUM(volunteers),0)   AS volunteers,
                           COALESCE(SUM(male_count),0)   AS male,
                           COALESCE(SUM(female_count),0) AS female,
                           COALESCE(ROUND(AVG(feedback),1),0) AS feedback
                    FROM reports')->fetch();

out(['summary' => $sum, 'rows' => $rows]);
