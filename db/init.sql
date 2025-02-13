CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    validts INTEGER NOT NULL DEFAULT 0,
    confirmed INTEGER NOT NULL DEFAULT 0,
    checked INTEGER NOT NULL DEFAULT 0,
    valid INTEGER
);
CREATE INDEX idx_user_validts ON user(validts);

CREATE TABLE queue_email (
    user_id INTEGER NOT NULL,
    days_left INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    lock_at TEXT NULL,
    sent_at TEXT NULL
);
CREATE UNIQUE INDEX idx_queue_email_user_id_days_left ON queue_email(user_id, days_left);

CREATE TABLE queue_check_email (
    user_id INTEGER PRIMARY KEY,
    email TEXT NOT NULL,
    lock_at TEXT NULL,
    created_at TEXT NOT NULL
);
