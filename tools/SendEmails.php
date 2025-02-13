<?php

require_once __DIR__ . '/../init.php';

$db->exec('BEGIN');

$sql = "SELECT user_id, days_left FROM queue_email WHERE sent_at IS NULL AND lock_at IS NULL LIMIT 100";
$res = $db->query($sql);

$tasks = [];
while ($row = $res->fetchArray(SQLITE3_ASSOC)) $tasks[] = $row;

foreach ($tasks as $task) {
    $userId = $task['user_id'];
    $daysLeft = $task['days_left'];

    $updLockStmt = $db->prepare("UPDATE queue_email SET lock_at = datetime('now') WHERE user_id = :uid AND days_left = :days AND lock_at IS NULL AND sent_at IS NULL");
    $updLockStmt->bindValue(':uid', $userId, SQLITE3_INTEGER);
    $updLockStmt->bindValue(':days', $daysLeft, SQLITE3_INTEGER);
    $updLockStmt->execute();

    $uStmt = $db->prepare("SELECT username, email FROM user WHERE id = :uid");
    $uStmt->bindValue(':uid', $userId, SQLITE3_INTEGER);
    $uRes = $uStmt->execute();
    $uRow = $uRes->fetchArray(SQLITE3_ASSOC);

    if (!$uRow) continue;

    $template = __DIR__ . '/../templates/email_subscription_expiring.php';
    $content = render($template, ['username' => $uRow['username'], /*'daysLeft' => $daysLeft*/]);

    if (send_email('noreply@mail.com', $uRow['email'], $content)) {
        $updateSentSql = "UPDATE queue_email SET sent_at = datetime('now') WHERE user_id = :uid AND days_left = :days";
        $updSentStmt = $db->prepare($updateSentSql);
        $updSentStmt->bindValue(':uid',  $userId,  SQLITE3_INTEGER);
        $updSentStmt->bindValue(':days', $daysLeft,SQLITE3_INTEGER);
        $updSentStmt->execute();
    }
}
