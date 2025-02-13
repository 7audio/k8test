CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT,
    email TEXT,
    validts INTEGER,
    confirmed INTEGER,
    checked INTEGER,
    valid INTEGER
);
CREATE INDEX idx_user_validts ON user(validts);

CREATE TABLE queue_email (
    user_id INTEGER,
    days_left INTEGER,
    created_at TEXT,
    lock_at TEXT NULL,
    sent_at TEXT NULL
);
CREATE UNIQUE INDEX idx_queue_email_user_id_days_left ON queue_email(user_id, days_left);

CREATE TABLE queue_check_email (
    user_id INTEGER PRIMARY KEY,
    email TEXT,
    lock_at TEXT NULL,
    created_at TEXT
);
