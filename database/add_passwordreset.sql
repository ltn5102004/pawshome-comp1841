-- LIM-03 (22 Nov 2026): table for "Forgot your password?" links.
-- Only a SHA-256 hash of the token is stored, so a copy of the database cannot be used to reset anyone's password.
-- A link works once (used = 1) and for 30 minutes (expires). Deleting a user deletes their links (CASCADE).
-- Source: OWASP (no date) Forgot Password Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html
--         MySQL (no date) CREATE TABLE Statement, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/create-table.html
CREATE TABLE IF NOT EXISTS `passwordreset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `tokenhash` char(64) NOT NULL,
  `expires` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokenhash` (`tokenhash`),
  KEY `userid` (`userid`),
  CONSTRAINT `passwordreset_user` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
