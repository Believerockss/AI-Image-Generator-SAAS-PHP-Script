
DELETE FROM `settings`WHERE `key`="watermark";

INSERT INTO `settings` (`key`, `value`) VALUES
('watermark', '{\"position\":\"bottom-right\",\"width\":\"120\",\"height\":\"30\",\"add_to\":\"3\",\"status\":0,\"logo\":\"images\\/watermark\\/R4Kz6AU3D4I8w5q_1707874022.png\"}'),
('image', '{\"original\":{\"webp_convert\":\"1\"},\"thumbnail\":{\"status\":1,\"width\":\"312\",\"height\":\"420\",\"webp_convert\":\"1\"}}'),
('limits', '{\"home_page_images\":\"32\",\"explore_page_images\":\"32\"}');


DELETE FROM `extensions` WHERE `alias`="trustip";

DROP TABLE IF EXISTS generated_images;
DROP TABLE IF EXISTS storage_providers;

CREATE TABLE `storage_providers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `handler` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credentials` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Disabled 1:Enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `storage_providers` (`id`, `name`, `alias`, `handler`, `logo`, `credentials`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Local Storage', 'local', 'App\\Http\\Controllers\\Frontend\\Storage\\LocalController', 'images/storage/local.png', NULL, 1, '2022-02-20 22:13:06', '2023-01-29 11:04:04'),
(2, 'Amazon S3', 's3', 'App\\Http\\Controllers\\Frontend\\Storage\\AmazonController', 'images/storage/amazon.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-02-20 22:12:55', '2024-02-14 12:55:55'),
(4, 'Digitalocean Spaces', 'digitalocean', 'App\\Http\\Controllers\\Frontend\\Storage\\DigitaloceanController', 'images/storage/digitalocean.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-02-20 22:13:06', '2022-04-13 00:23:18'),
(5, 'Wasabi Cloud Storage', 'wasabi', 'App\\Http\\Controllers\\Frontend\\Storage\\WasabiController', 'images/storage/wasabi.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-02-20 22:13:01', '2023-11-11 12:41:40'),
(6, 'Cloudflare R2', 'cloudflare', 'App\\Http\\Controllers\\Frontend\\Storage\\CloudflareR2Controller', 'images/storage/cloudflare.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-05-17 23:23:27', '2023-03-18 15:03:13'),
(7, 'Idrive E2', 'idrive', 'App\\Http\\Controllers\\Frontend\\Storage\\IdriveE2Controller', 'images/storage/idrive.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-05-17 23:23:27', '2023-02-18 00:23:18'),
(8, 'Storj', 'storj', 'App\\Http\\Controllers\\Frontend\\Storage\\StorjController', 'images/storage/storj.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-05-17 23:23:27', '2024-02-14 13:20:16'),
(9, 'Backblaze B2 Cloud Storage ', 'backblaze', 'App\\Http\\Controllers\\Frontend\\Storage\\BackblazeController', 'images/storage/backblaze.png', '{\"access_key_id\":null,\"secret_access_key\":null,\"default_region\":null,\"bucket\":null,\"url\":null,\"endpoint\":null}', 0, '2022-02-20 21:13:01', '2024-02-13 13:50:40');

ALTER TABLE `storage_providers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `storage_providers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;


CREATE TABLE `generated_images` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `storage_provider_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prompt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `negative_prompt` text COLLATE utf8mb4_unicode_ci,
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `main` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `views` bigint UNSIGNED NOT NULL DEFAULT '0',
  `downloads` bigint UNSIGNED NOT NULL DEFAULT '0',
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  `is_viewed` tinyint(1) NOT NULL DEFAULT '0',
  `expiry_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `generated_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_images_user_id_foreign` (`user_id`),
  ADD KEY `generated_images_storage_provider_id_foreign` (`storage_provider_id`);

ALTER TABLE `generated_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `generated_images`
  ADD CONSTRAINT `generated_images_storage_provider_id_foreign` FOREIGN KEY (`storage_provider_id`) REFERENCES `storage_providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `generated_images_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

