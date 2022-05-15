CREATE TABLE users
(
    id         INTEGER PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    first_name VARCHAR(255),
    last_name  VARCHAR(255),
    avatar     TEXT
);

CREATE TABLE echoes
(
    id         INTEGER PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(255) NOT NULL REFERENCES users (username),
    content    TEXT         NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);