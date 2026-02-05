-- Aurave Security Mailer - Login notification tables
-- Run this after schema.sql: mysql -u root -p aurave_erp < database/migrations/security_mailer.sql

-- Login events (one per login that triggers security email)
CREATE TABLE IF NOT EXISTS `login_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `login_events_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verification tokens (Yes/No links in email)
CREATE TABLE IF NOT EXISTS `login_verification_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `login_event_id` int unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `action` enum('yes','no') NOT NULL,
  `clicked_at` datetime DEFAULT NULL,
  `click_ip` varchar(45) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `login_event_id` (`login_event_id`),
  CONSTRAINT `login_verification_event_fk` FOREIGN KEY (`login_event_id`) REFERENCES `login_events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
