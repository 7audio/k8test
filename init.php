<?php

require_once __DIR__ . '/functions/check_email.php';
require_once __DIR__ . '/functions/send_email.php';

$dbPath = __DIR__ . '/db/db.sqlite';
$db = new SQLite3($dbPath);
$db->enableExceptions(true);
