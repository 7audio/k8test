<?php

require_once __DIR__ . '/../init.php';

$now = time();
$oneDayStart = $now + 1 * 86400;
$oneDayEnd = $now + 2 * 86400;

$threeDaysStart = $now + 3 * 86400;
$threeDaysEnd = $now + 4 * 86400;

$sqlOneDay = "INSERT INTO queue_email (user_id, days_left, created_at) SELECT u.id, 1, datetime('now') FROM user u WHERE u.confirmed = 1 AND u.valid = 1 AND u.validts != 0 AND u.validts BETWEEN :start1 AND :end1 ON CONFLICT(user_id, days_left) DO NOTHING";

$stmt1 = $db->prepare($sqlOneDay);
$stmt1->bindValue(':start1', $oneDayStart, SQLITE3_INTEGER);
$stmt1->bindValue(':end1', $oneDayEnd, SQLITE3_INTEGER);
$stmt1->execute();

$sqlThreeDays = "INSERT INTO queue_email (user_id, days_left, created_at) SELECT u.id, 3, datetime('now') FROM user u WHERE u.confirmed = 1 AND u.valid = 1 AND u.validts != 0 AND u.validts BETWEEN :start3 AND :end3 ON CONFLICT(user_id, days_left) DO NOTHING";

$stmt2 = $db->prepare($sqlThreeDays);
$stmt2->bindValue(':start3', $threeDaysStart, SQLITE3_INTEGER);
$stmt2->bindValue(':end3', $threeDaysEnd, SQLITE3_INTEGER);
$stmt2->execute();
