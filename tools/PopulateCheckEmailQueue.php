<?php

require_once __DIR__ . '/../init.php';

$sql = "INSERT INTO queue_check_email (user_id, email, created_at) SELECT u.id, u.email, datetime('now') FROM user u WHERE u.checked = 0 ON CONFLICT(user_id) DO NOTHING";
$result = $db->exec($sql);
