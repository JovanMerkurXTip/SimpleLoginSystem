-- INFORMATION ---------------------------------------------------

-- Database info:
--      Server version: 10.4.28 MariaDB
--      Protocol version: 10
-- Web server info:
--      Apache 2.4.56 (Win64)
--      OpenSSL 1.1.1t
-- PHPMyAdmin info:
--      Version information: 5.2.1
--      PHP 8.2.4

-- DATABASE ------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `simplelogin`;

-- TABLES --------------------------------------------------------

DROP TABLE IF EXISTS login_otp_codes;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS registration_verification_tokens;
DROP TABLE IF EXISTS users;

CREATE TABLE `login_otp_codes` (
  `user_id` int(11) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

CREATE TABLE `password_reset_tokens` (
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

CREATE TABLE `registration_verification_tokens` (
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

-- INDEXES -------------------------------------------------------

ALTER TABLE `login_otp_codes`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_otp` (`user_id`,`otp`),
  ADD UNIQUE KEY `user_id_2` (`user_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_token` (`user_id`,`token`);

ALTER TABLE `registration_verification_tokens`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

-- CONSTRAINTS ---------------------------------------------------

ALTER TABLE `login_otp_codes`
  ADD CONSTRAINT `login_otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- ---------------------------------------------------------------

COMMIT;
