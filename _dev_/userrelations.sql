-- user relationships (source has relation to target)
CREATE TABLE `ktt_ec_user_relations` (
	`id` bigint(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`source_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
	`target_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
	`key` varchar(255) COLLATE utf8mb4_unicode_ci,
	`value` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;