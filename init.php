<?php

require_once __DIR__ . '/functions/check_email.php';
require_once __DIR__ . '/functions/send_email.php';

$dbPath = __DIR__ . '/db/db.sqlite';
$db = new SQLite3($dbPath);
$db->enableExceptions(true);

function say(string $message): void
{
    echo sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
}

function render(string $file, array $parameters = []): string
{
    ob_start();
    extract($parameters);
    include $file;
    return ob_get_clean();
}
