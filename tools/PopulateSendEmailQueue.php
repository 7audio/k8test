<?php

require_once __DIR__ . '/../init.php';

function populateSendEmailQueue(SQLite3 $db, int $days): void
{
    $now = time();
    $start = $now + $days * 86400;
    $end = $now + ($days + 1) * 86400;

    $stmt1 = $db->prepare("INSERT INTO queue_email (user_id, days_left, created_at) SELECT u.id, 1, datetime('now') FROM user u WHERE u.confirmed = 1 AND u.valid = 1 AND u.validts != 0 AND u.validts BETWEEN :start AND :end ON CONFLICT DO NOTHING");
    $stmt1->bindValue(':start', $start, SQLITE3_INTEGER);
    $stmt1->bindValue(':end', $end, SQLITE3_INTEGER);
    $stmt1->execute();

    say(sprintf(
        "added %d emails to %d day queue",
        $insertedRows = $db->changes(),
        $days,
    ));
}

populateSendEmailQueue($db, 1);
populateSendEmailQueue($db, 3);
