<?php

require_once __DIR__ . '/../init.php';

$usersTotal = 5000000;
$batchSize = 100000;

$percentHaveSubscription = 80;
$percentConfirmedEmail = 15;

$startTime = microtime(true);

$stmt = $db->prepare("INSERT INTO user (username, email, validts, confirmed, checked, valid) "
                    ."VALUES (:username, :email, :validts, :confirmed, :checked, :valid)");

$db->exec('BEGIN');
for ($i = 1; $i <= $usersTotal; $i++) {
    $validts = (mt_rand(1, 100) <= $percentHaveSubscription) ? 0 : (time() + mt_rand(1, 31) * 86400);
    $confirmed = (mt_rand(1, 100) <= $percentConfirmedEmail) ? 1 : 0;

    $stmt->bindValue(':username', "User $i", SQLITE3_TEXT);
    $stmt->bindValue(':email', sprintf('user_%d@mail.com', $i), SQLITE3_TEXT);
    $stmt->bindValue(':validts', $validts, SQLITE3_INTEGER);
    $stmt->bindValue(':confirmed', $confirmed, SQLITE3_INTEGER);
    $stmt->bindValue(':checked', 0, SQLITE3_INTEGER);
    $stmt->bindValue(':valid', 0, SQLITE3_INTEGER);
    $stmt->execute();
}
$db->exec('COMMIT');

echo sprintf(
    "Inserted %d users in %f seconds.\n",
    $usersTotal,
    microtime(true) - $startTime,
);
