CREATE DATABASE IF NOT EXISTS ibp_auth_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE ibp_auth_db;

CREATE TABLE IF NOT EXISTS tbl_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_sha256 CHAR(64) NOT NULL,
    role VARCHAR(30) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS fraud (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempted_email VARCHAR(100),
    attempted_password_sha256 CHAR(64),
    ip_address VARCHAR(50),
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO tbl_user (fullname, email, password_sha256, role)
VALUES (
    'Admin User',
    'admin@test.com',
    SHA2('123456', 256),
    'admin'
);