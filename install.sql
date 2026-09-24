CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `todolist` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `label` varchar(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_todolist_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `todoist_accounts` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_todoist_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- admin:admin
INSERT IGNORE INTO `users` (`username`, `password_hash`) VALUES ('admin', '$2y$12$EOg47Nwzf6dRaggt3cx6YuD9v6p.pa.vJy96i1x/ZvsQGBr8uYkNe');