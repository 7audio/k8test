<?php

require_once __DIR__ . '/../init.php';

while (true) {
    $res = $db->query("SELECT user_id, email FROM queue_check_email WHERE lock_at IS NULL OR lock_at = '' LIMIT 10");

    $batch = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) $batch[] = $row;
    say(sprintf(
        "checking %d emails: %s",
        count($batch),
        implode(', ', array_column($batch, 'email')),
    ));

    if (!count($batch)) break;

    $ids = array_column($batch, 'user_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmtLock = $db->prepare("UPDATE queue_check_email SET lock_at = datetime('now') WHERE (lock_at IS NULL OR lock_at = '') AND user_id IN ($placeholders)");
    foreach ($ids as $index => $val) $stmtLock->bindValue($index+1, $val, SQLITE3_INTEGER);
    $stmtLock->execute();

    foreach ($batch as $item) {
        $userId = $item['user_id'];
        $email  = $item['email'];

        $valid = check_email($email);

        $updUserStmt = $db->prepare("UPDATE user SET checked = 1, valid = :valid WHERE id = :uid");
        $updUserStmt->bindValue(':valid', $valid, SQLITE3_INTEGER);
        $updUserStmt->bindValue(':uid', $userId, SQLITE3_INTEGER);
        $updUserStmt->execute();

        $delStmt = $db->prepare("DELETE FROM queue_check_email WHERE user_id = :uid");
        $delStmt->bindValue(':uid', $userId, SQLITE3_INTEGER);
        $delStmt->execute();

        say(sprintf(
            "checked email %s: %s",
            $email,
            $valid ? '✅' : '❌',
        ));
    }
}
