-- CREATE DATABASE IF NOT EXISTS tank_data;
USE tank_data;
-- USE bd_tankistan;
CREATE TABLE IF NOT EXISTS users
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username VARCHAR(100),
    password VARCHAR(100)
);
CREATE TABLE IF NOT EXISTS tanks
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    game_id VARCHAR(100),
    name VARCHAR(100),
    x INT DEFAULT -1,
    y INT DEFAULT -1,
	actions INT DEFAULT 2,
    health INT DEFAULT 3,
    bullet_range INT DEFAULT 2
);

CREATE TABLE IF NOT EXISTS games
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(100),
    size INT DEFAULT 0,
    owner VARCHAR(100),
    started BOOL DEFAULT FALSE,
    public BOOL DEFAULT FALSE,
    map TEXT DEFAULT NULL,
    random_map BOOL DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS logs
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    author VARCHAR(100),
    action_id INT,
    other VARCHAR(100) DEFAULT NULL,
    game_id VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS user_tokens
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
    selector         VARCHAR(255) NOT NULL,
    hashed_validator VARCHAR(255) NOT NULL,
    user_id          INT      NOT NULL,
    expiry           DATETIME NOT NULL,
    CONSTRAINT fk_user_id
        FOREIGN KEY (user_id)
            REFERENCES users (id) ON DELETE CASCADE
);