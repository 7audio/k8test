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

CREATE TABLE email (
    user_id INTEGER,
    days_left INTEGER,
    date TEXT,
    PRIMARY KEY (user_id, days_left)
);

CREATE TABLE email_queue (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    email TEXT,
    message TEXT,
    status INTEGER,
    created_at TEXT,
    sent_at TEXT
);
