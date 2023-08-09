CREATE DATABASE IF NOT EXISTS tank_data;
USE tank_data;
CREATE TABLE IF NOT EXISTS users
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username VARCHAR(100),
    password VARCHAR(100)
);
CREATE TABLE IF NOT EXISTS tanks
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(100),
    x INT,
    y INT,
	actions INT,
    health INT,
    bullet_range INT DEFAULT 2
);