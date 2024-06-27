-- --------------------------------------------------------
-- Database info:
--      Server version: 10.4.28 MariaDB
--      Protocol version: 10
-- Web server info:
--      Apache 2.4.56 (Win64)
--      OpenSSL 1.1.1t
-- PHPMyAdmin info:
--      Version information: 5.2.1
--      PHP 8.2.4
-- --------------------------------------------------------

-- TODO: FIX WITH NEW MODEL

-- ! DO NOT USE THIS SCRIPT

CREATE DATABASE IF NOT EXISTS `simplelogin`;

DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS login_otp_codes;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL,
    remember_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE login_otp_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp VARCHAR(6) NOT NULL,
    otp_expiration DATETIME NOT NULL,
    UNIQUE KEY user_otp (user_id, otp),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiration DATETIME NOT NULL,
    UNIQUE KEY user_token (user_id, token),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
