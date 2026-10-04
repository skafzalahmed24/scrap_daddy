-- ==============================================================================
-- Scrap Daddy - Production / Server Database Dump with Comprehensive Seed Data
-- Application: Scrap Daddy (Doorstep Scrap Collection & Recycling Platform)
-- Format: MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.3+ Compatible
-- Character Set: utf8mb4 / utf8mb4_unicode_ci
-- Generated for Server Upload & phpMyAdmin / MySQL CLI Import
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ------------------------------------------------------------------------------
-- Table structure for table: migrations
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: personal_access_tokens
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: users
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uuid` char(36) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pin_code` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `device_unique_id` varchar(255) DEFAULT NULL,
  `device_details` text DEFAULT NULL,
  `platform_type` int(11) NOT NULL COMMENT '1 for web, 2 for android, 3 for ios',
  `otp` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for active, 0 for deleted',
  `reward_coins` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `users_phone_number_unique` (`phone_number`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: categories
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `uuid` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: subcategories
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE `subcategories` (
  `uuid` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `subcategories_category_id_foreign` (`category_id`),
  CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: banners
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `uuid` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `uploads` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: orders
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_uuid` char(36) NOT NULL,
  `category_uuid` char(36) DEFAULT NULL,
  `subcategory_uuid` char(36) DEFAULT NULL,
  `items` json DEFAULT NULL,
  `pickup_location` text DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_time` varchar(255) DEFAULT NULL,
  `images` json DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','accepted','completed','cancelled') NOT NULL DEFAULT 'pending',
  `estimated_pickup_date` datetime DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_id` varchar(255) DEFAULT NULL,
  `coins_earned` int(11) NOT NULL DEFAULT 0,
  `coins_redeemed` int(11) NOT NULL DEFAULT 0,
  `available_coins` int(11) NOT NULL DEFAULT 0 COMMENT 'Current balance of coins from this order',
  `coins_expires_at` timestamp NULL DEFAULT NULL,
  `discount_applied` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_uuid_foreign` (`user_uuid`),
  KEY `orders_category_uuid_foreign` (`category_uuid`),
  KEY `orders_subcategory_uuid_foreign` (`subcategory_uuid`),
  CONSTRAINT `orders_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `orders_category_uuid_foreign` FOREIGN KEY (`category_uuid`) REFERENCES `categories` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `orders_subcategory_uuid_foreign` FOREIGN KEY (`subcategory_uuid`) REFERENCES `subcategories` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: scrap_vehicles
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `scrap_vehicles`;
CREATE TABLE `scrap_vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_uuid` char(36) NOT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `vehicle_number` varchar(255) NOT NULL,
  `vehicle_brand` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `photos` json DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrap_vehicles_user_uuid_foreign` (`user_uuid`),
  CONSTRAINT `scrap_vehicles_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: feedback
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_uuid` char(36) NOT NULL,
  `star_rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_user_uuid_foreign` (`user_uuid`),
  CONSTRAINT `feedback_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: static_pages
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `static_pages`;
CREATE TABLE `static_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `static_pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: faqs
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `answer` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: reward_configurations
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `reward_configurations`;
CREATE TABLE `reward_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `min_amount` decimal(10,2) NOT NULL,
  `max_amount` decimal(10,2) NOT NULL,
  `reward_coins` int(11) NOT NULL,
  `validity_days` int(11) DEFAULT NULL COMMENT 'Number of days before earned coins expire',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table structure for table: reward_settings
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `reward_settings`;
CREATE TABLE `reward_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coin_value_in_rupees` decimal(10,2) NOT NULL DEFAULT 0.10,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==============================================================================
-- POPULATE DATA (SEED & FAKE DATA)
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- Dumping data for table: migrations
-- ------------------------------------------------------------------------------
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2026_06_14_053931_create_categories_table', 1),
(3, '2026_06_14_063842_create_subcategories_table', 1),
(4, '2026_06_14_070027_create_users_table', 1),
(5, '2026_06_14_074711_create_banners_table', 1),
(6, '2026_06_19_163627_create_feedback_table', 1),
(7, '2026_06_19_163627_create_orders_table', 1),
(8, '2026_06_19_163628_create_static_pages_table', 1),
(9, '2026_06_19_163629_add_latitude_and_longitude_to_users_table', 1),
(10, '2026_06_19_165641_add_pickup_details_to_orders_table', 1),
(11, '2026_06_19_171229_add_category_id_to_orders_table', 1),
(12, '2026_07_23_150459_add_otp_fields_to_users_table', 1),
(13, '2026_07_23_182921_add_profile_image_to_users_table', 1),
(14, '2026_07_23_190640_add_status_to_users_table', 1),
(15, '2026_08_01_135549_add_items_to_orders_table', 1),
(16, '2026_08_02_093834_add_type_to_banners_table', 1),
(17, '2026_08_23_044747_create_scrap_vehicles_table', 1),
(18, '2026_08_23_093813_create_faqs_table', 1),
(19, '2026_08_23_095416_add_reward_coins_to_users_table', 1),
(20, '2026_08_23_095418_add_reward_fields_to_orders_table', 1),
(21, '2026_08_23_095419_create_reward_configurations_table', 1),
(22, '2026_08_23_095421_create_reward_settings_table', 1),
(23, '2026_08_23_105758_add_validity_days_to_reward_configurations_table', 1),
(24, '2026_08_23_105822_add_coins_expiration_to_orders_table', 1);

-- ------------------------------------------------------------------------------
-- Dumping data for table: reward_settings
-- ------------------------------------------------------------------------------
INSERT INTO `reward_settings` (`id`, `coin_value_in_rupees`, `created_at`, `updated_at`) VALUES
(1, 0.25, '2026-08-23 10:00:00', '2026-08-23 10:00:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: reward_configurations
-- ------------------------------------------------------------------------------
INSERT INTO `reward_configurations` (`id`, `min_amount`, `max_amount`, `reward_coins`, `validity_days`, `status`, `created_at`, `updated_at`) VALUES
(1, 100.00, 499.00, 50, 30, 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(2, 500.00, 1499.00, 150, 60, 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(3, 1500.00, 4999.00, 500, 90, 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(4, 5000.00, 19999.00, 2000, 180, 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(5, 20000.00, 100000.00, 10000, 365, 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: static_pages
-- ------------------------------------------------------------------------------
INSERT INTO `static_pages` (`id`, `slug`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 'help-and-support', 'Help & Support', '<h3>Welcome to Scrap Daddy Support</h3><p>We are dedicated to providing you with seamless doorstep scrap collection services. If you have any inquiries, issues with your pickup, or payment questions, our support team is here to assist you.</p><h4>Frequently Asked Topics</h4><ul><li><strong>Booking a Pickup:</strong> Select your scrap items, enter your address, choose a preferred date and time, and submit your request. Our pickup agent will call you before arriving.</li><li><strong>Accurate Weighing:</strong> All our field executives carry certified ISO electronic weighing scales for 100% transparency.</li><li><strong>Instant Digital Payments:</strong> You can receive immediate payments via UPI (GPay, PhonePe, Paytm), Bank Transfer, or Cash upon weighing.</li><li><strong>Rewards & Coins:</strong> Earn reward coins on completed pickups. Redeem coins for instant discounts on future pickups.</li></ul><h4>Contact Details</h4><p>Email: <strong>support@scrapedaddy.com</strong><br>Helpline: <strong>+91 98765 43210</strong><br>Hours: <strong>Mon - Sun: 8:00 AM - 8:00 PM</strong></p>', '2026-06-19 17:00:00', '2026-08-23 10:00:00'),
(2, 'privacy-policy', 'Privacy Policy', '<h3>Privacy Policy for Scrap Daddy</h3><p>Your privacy is important to us. This privacy policy explains how Scrap Daddy collects, uses, and safeguards your personal data when you use our website and mobile application.</p><h4>Information We Collect</h4><ul><li><strong>Account Information:</strong> Full name, email address, phone number, and password.</li><li><strong>Location Data:</strong> Address, pincode, and GPS coordinates to facilitate timely doorstep scrap pickups.</li><li><strong>Pickup & Order Details:</strong> Scrap categories, estimated quantities, photos uploaded, and transaction receipts.</li><li><strong>Device Information:</strong> Device model, OS version, and unique identifiers to optimize performance and send push notifications.</li></ul><h4>How We Protect Your Data</h4><p>We implement robust industry-standard encryption protocols. We do not sell or lease your personal information to third parties.</p>', '2026-06-19 17:00:00', '2026-08-23 10:00:00'),
(3, 'terms-and-conditions', 'Terms & Conditions', '<h3>Terms and Conditions</h3><p>By using Scrap Daddy’s website or mobile application, you agree to comply with and be bound by the following terms and conditions.</p><h4>1. Scrap Pickup Guidelines</h4><p>All scrap items must be accessible and reasonably segregated before the arrival of our collection team. Hazardous or explosive materials are strictly prohibited.</p><h4>2. Valuation & Payment</h4><p>Rates listed on the platform are indicative and may vary slightly based on current market trends and the cleanliness/grade of the scrap. Final payout is determined after electronic weighing on-site.</p><h4>3. Cancellation Policy</h4><p>You may cancel or reschedule your pickup request free of charge before the field agent has been dispatched.</p><h4>4. Reward Coins & Loyalty</h4><p>Reward coins are credited upon successful order completion and may have an expiration period as configured in platform policies.</p>', '2026-06-19 17:00:00', '2026-08-23 10:00:00'),
(4, 'about-us', 'About Us', '<h3>About Scrap Daddy</h3><p>Scrap Daddy is a tech-enabled waste management and doorstep scrap pickup platform. We are on a mission to organize the informal recycling industry, promote environmental sustainability, and ensure maximum value and convenience for households and businesses.</p><h4>Why Choose Us?</h4><ul><li>Certified Digital Weighing Scales</li><li>Best Market Scrap Rates</li><li>Instant Digital Payment on the spot</li><li>Zero Landfill & Eco-Friendly Recycling Network</li><li>Professional & Background-Verified Staff</li></ul>', '2026-06-19 17:00:00', '2026-08-23 10:00:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: faqs
-- ------------------------------------------------------------------------------
INSERT INTO `faqs` (`id`, `question`, `answer`, `status`, `created_at`, `updated_at`) VALUES
(1, 'How does Scrap Daddy doorstep pickup work?', 'Simply select the scrap items you have, choose your preferred pickup date and time slot, and submit the request. Our verified executive visits your location with a digital weighing machine, calculates the value, pays you instantly, and takes care of the scrap.', 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(2, 'How is the scrap weighed and priced?', 'Our pickup executives carry certified, precision digital electronic scales. The rates are updated in real-time according to prevailing recycling market prices, ensuring transparent and fair valuation.', 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(3, 'When will I receive payment for my scrap?', 'Payment is transferred instantly to your UPI ID (Google Pay, PhonePe, Paytm), bank account, or paid in cash immediately after weighing on-site.', 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(4, 'What is the Scrap Vehicle service?', 'If you have an old, end-of-life two-wheeler, four-wheeler, or commercial vehicle, you can submit a vehicle scrapping request. We assist with legally compliant scrapping, deregistration documentation (RTO certificate), and offer the best scrap value.', 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(5, 'How do Reward Coins work?', 'You earn Scrap Daddy reward coins with every completed order. These coins can be redeemed on subsequent pickups to receive cash bonuses or discounts on platform services.', 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00'),
(6, 'Is there any minimum quantity or fee for pickup?', 'Doorstep pickup is 100% free! We recommend a minimum total scrap weight of 10-15 kg for residential pickups to optimize vehicle routing.', 1, '2026-08-23 10:00:00', '2026-08-23 10:00:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: banners
-- ------------------------------------------------------------------------------
INSERT INTO `banners` (`uuid`, `title`, `short_description`, `uploads`, `status`, `type`, `created_at`, `updated_at`) VALUES
('b1000000-0000-0000-0000-000000000001', 'Get Top Value for Your Scrap at Your Doorstep', 'Sell your paper, plastics, metals, and appliances with certified digital weighing and instant payout.', 'uploads/banners/1781424961_tokMyGxjpA.png', 1, 'web', '2026-08-01 10:00:00', '2026-08-01 10:00:00'),
('b1000000-0000-0000-0000-000000000002', 'Earn Reward Coins on Every Pickup!', 'Get rewarded for responsible recycling. Redeem coins for instant bonuses.', 'uploads/banners/1785663471_PlWaquWOQU.png', 1, 'web', '2026-08-02 11:00:00', '2026-08-02 11:00:00'),
('b1000000-0000-0000-0000-000000000003', 'Scrap Your Old Vehicle Legally & Safely', 'Hassle-free vehicle scrappage certificate and government approved recycling.', 'requestbanenr.png', 1, 'mobile', '2026-08-05 12:00:00', '2026-08-05 12:00:00'),
('b1000000-0000-0000-0000-000000000004', 'Commercial & Industrial Scrap Solutions', 'Custom bulk waste collection and recycling for offices, factories, and schools.', 'uploads/banners/1781424961_tokMyGxjpA.png', 1, 'mobile', '2026-08-10 09:30:00', '2026-08-10 09:30:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: categories
-- ------------------------------------------------------------------------------
INSERT INTO `categories` (`uuid`, `title`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
('c1000000-0000-0000-0000-000000000001', 'Paper & Cardboard', 'Newspapers, office paper, old textbooks, cartons, magazines, and packaging materials.', 'categories/1781426891_UigZQbzol0.png', 1, '2026-06-15 10:00:00', '2026-06-15 10:00:00'),
('c1000000-0000-0000-0000-000000000002', 'Plastics & Polymers', 'Plastic bottles, containers, chairs, household plastic items, and packaging wraps.', 'categories/1781428157_gUYrqRL6vL.png', 1, '2026-06-15 10:05:00', '2026-06-15 10:05:00'),
('c1000000-0000-0000-0000-000000000003', 'Metals & Iron', 'Iron scrap, copper wires, aluminium sections, brass, stainless steel, and tin sheets.', 'categories/1781428429_jwQ32pPJvh.png', 1, '2026-06-15 10:10:00', '2026-06-15 10:10:00'),
('c1000000-0000-0000-0000-000000000004', 'E-Waste & Electronics', 'Laptops, mobile phones, CPUs, computer peripherals, cables, and circuit boards.', 'categories/1781429445_I8e0Z270DJ.png', 1, '2026-06-15 10:15:00', '2026-06-15 10:15:00'),
('c1000000-0000-0000-0000-000000000005', 'Large Appliances', 'Refrigerators, air conditioners, washing machines, microwaves, and water heaters.', 'categories/1781429615_XK6TcjchKe.png', 1, '2026-06-15 10:20:00', '2026-06-15 10:20:00'),
('c1000000-0000-0000-0000-000000000006', 'Scrap Vehicles', 'End-of-life two wheelers, four wheelers, auto rickshaws, and automotive parts.', 'categories/1781429874_vqib2zDD4f.png', 1, '2026-06-15 10:25:00', '2026-06-15 10:25:00'),
('c1000000-0000-0000-0000-000000000007', 'Batteries & Glass', 'Inverter batteries, automotive lead-acid batteries, glass bottles, and jars.', 'categories/1781416787_XU3tKW2aF2.jpeg', 1, '2026-06-15 10:30:00', '2026-06-15 10:30:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: subcategories
-- ------------------------------------------------------------------------------
INSERT INTO `subcategories` (`uuid`, `category_id`, `name`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
('s1000000-0000-0000-0000-000000000001', 'c1000000-0000-0000-0000-000000000001', 'Newspaper (Raddi)', 'Old daily newspapers in English, Hindi, and regional languages. Rate approx ₹14-16/kg.', 'subcategories/1781419825_6T5qZvoMG3.png', 1, '2026-06-15 11:00:00', '2026-06-15 11:00:00'),
('s1000000-0000-0000-0000-000000000002', 'c1000000-0000-0000-0000-000000000001', 'Corrugated Cardboard / Carton', 'Clean packaging cartons, e-commerce delivery boxes, cardboard sheets.', 'subcategories/1781419847_TSp4N9RxF5.jpeg', 1, '2026-06-15 11:05:00', '2026-06-15 11:05:00'),
('s1000000-0000-0000-0000-000000000003', 'c1000000-0000-0000-0000-000000000001', 'Old Books & Magazines', 'School textbooks, college notebooks, novels, and glossy magazines.', 'subcategories/1781430669_RMgna40n13.png', 1, '2026-06-15 11:10:00', '2026-06-15 11:10:00'),
('s1000000-0000-0000-0000-000000000004', 'c1000000-0000-0000-0000-000000000001', 'Office White Paper', 'A4 office documents, letterheads, printer papers, shredded waste paper.', 'subcategories/1781430746_kYdY2O1kc3.png', 1, '2026-06-15 11:15:00', '2026-06-15 11:15:00'),
('s1000000-0000-0000-0000-000000000005', 'c1000000-0000-0000-0000-000000000002', 'Soft Plastic / PET Bottles', 'Mineral water bottles, beverage bottles, clean clear plastic containers.', 'subcategories/1781430788_r3cNzboEG8.png', 1, '2026-06-15 11:20:00', '2026-06-15 11:20:00'),
('s1000000-0000-0000-0000-000000000006', 'c1000000-0000-0000-0000-000000000002', 'Hard Plastic / Buckets & Chairs', 'Broken plastic chairs, bathroom buckets, storage tubs, crates, HDPE items.', 'subcategories/1781430902_yS6qWGhrMG.png', 1, '2026-06-15 11:25:00', '2026-06-15 11:25:00'),
('s1000000-0000-0000-0000-000000000007', 'c1000000-0000-0000-0000-000000000003', 'Iron & Heavy Steel', 'Pipes, construction rods, grills, angle iron, metal frames, heavy scrap.', 'subcategories/1781431012_B2Tdqb0big.png', 1, '2026-06-15 11:30:00', '2026-06-15 11:30:00'),
('s1000000-0000-0000-0000-000000000008', 'c1000000-0000-0000-0000-000000000003', 'Copper Wire & Pipes', 'Pure electrical copper wires, copper tubing, motors, plumbing copper scrap.', 'subcategories/1781431083_RRLZUXM1C8.png', 1, '2026-06-15 11:35:00', '2026-06-15 11:35:00'),
('s1000000-0000-0000-0000-000000000009', 'c1000000-0000-0000-0000-000000000003', 'Aluminium Sections & Cans', 'Window frames, utensils, cans, aluminium sheet clippings, ladders.', 'subcategories/1781431804_dkuFDW49rg.png', 1, '2026-06-15 11:40:00', '2026-06-15 11:40:00'),
('s1000000-0000-0000-0000-000000000010', 'c1000000-0000-0000-0000-000000000003', 'Brass & Bronze', 'Pooja brassware, antique items, taps, valves, bronze castings.', 'subcategories/1781431855_y5fTlWGWBA.png', 1, '2026-06-15 11:45:00', '2026-06-15 11:45:00'),
('s1000000-0000-0000-0000-000000000011', 'c1000000-0000-0000-0000-000000000004', 'Laptops & Computers', 'Scrap laptops, desktop towers, monitors, power supplies, keyboards.', 'subcategories/1781431902_uBsf8ndplc.png', 1, '2026-06-15 11:50:00', '2026-06-15 11:50:00'),
('s1000000-0000-0000-0000-000000000012', 'c1000000-0000-0000-0000-000000000004', 'Mobile Phones & Tablets', 'Old and damaged smartphones, feature phones, tablets, chargers, batteries.', 'subcategories/1781431982_kU3RN2mP5J.png', 1, '2026-06-15 11:55:00', '2026-06-15 11:55:00'),
('s1000000-0000-0000-0000-000000000013', 'c1000000-0000-0000-0000-000000000005', 'Air Conditioner (Split / Window)', 'Complete AC units, copper condenser coils, compressor scrap.', 'subcategories/1781419825_6T5qZvoMG3.png', 1, '2026-06-15 12:00:00', '2026-06-15 12:00:00'),
('s1000000-0000-0000-0000-000000000014', 'c1000000-0000-0000-0000-000000000005', 'Refrigerator / Fridge', 'Single and double door refrigerators, commercial deep freezers.', 'subcategories/1781419847_TSp4N9RxF5.jpeg', 1, '2026-06-15 12:05:00', '2026-06-15 12:05:00'),
('s1000000-0000-0000-0000-000000000015', 'c1000000-0000-0000-0000-000000000005', 'Washing Machine', 'Semi-automatic and fully automatic top/front load washing machines.', 'subcategories/1781430669_RMgna40n13.png', 1, '2026-06-15 12:10:00', '2026-06-15 12:10:00'),
('s1000000-0000-0000-0000-000000000016', 'c1000000-0000-0000-0000-000000000006', 'Two Wheeler (Bike / Scooter)', 'End of life motorcycles, gearless scooters, mopeds with valid RC copies.', 'subcategories/1781430746_kYdY2O1kc3.png', 1, '2026-06-15 12:15:00', '2026-06-15 12:15:00'),
('s1000000-0000-0000-0000-000000000017', 'c1000000-0000-0000-0000-000000000006', 'Four Wheeler (Car Scrap)', 'Accidental, condemned, or 15+ year expired petrol/diesel passenger cars.', 'subcategories/1781430788_r3cNzboEG8.png', 1, '2026-06-15 12:20:00', '2026-06-15 12:20:00'),
('s1000000-0000-0000-0000-000000000018', 'c1000000-0000-0000-0000-000000000007', 'Lead Acid Inverter / Car Battery', 'UPS batteries, tubular solar batteries, car & truck batteries. Rate ₹70-85/kg.', 'subcategories/1781430902_yS6qWGhrMG.png', 1, '2026-06-15 12:25:00', '2026-06-15 12:25:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: users
-- All accounts have password: "password" (Bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
-- Admin Web Panel Login: Email: admin@scrapedaddy.com / Password: Vzario@123
-- ------------------------------------------------------------------------------
INSERT INTO `users` (`uuid`, `full_name`, `profile_image`, `email`, `phone_number`, `password`, `pin_code`, `location`, `latitude`, `longitude`, `device_id`, `device_unique_id`, `device_details`, `platform_type`, `otp`, `is_verified`, `otp_expires_at`, `status`, `reward_coins`, `created_at`, `updated_at`) VALUES
('u1000000-0000-0000-0000-000000000001', 'Rahul Sharma', 'profiles/1784906319_Kg4MGtjuKf.jpg', 'rahul.sharma@example.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '400001', 'A-402, Sea View Towers, Worli, Mumbai', 19.01761470, 72.81534080, 'dev_token_web_1', 'uuid_unique_1', 'Chrome 120 / Windows 11', 1, '123456', 1, '2026-12-31 23:59:59', 1, 650, '2026-06-20 09:15:00', '2026-09-01 14:20:00'),
('u1000000-0000-0000-0000-000000000002', 'Priya Patel', NULL, 'priya.patel@example.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '380015', 'Flat 12, Sunrise Residency, SG Highway, Ahmedabad', 23.02250500, 72.57136210, 'dev_token_android_2', 'uuid_unique_2', 'Samsung Galaxy S23 / Android 14', 2, '123456', 1, '2026-12-31 23:59:59', 1, 150, '2026-06-25 11:30:00', '2026-08-28 10:15:00'),
('u1000000-0000-0000-0000-000000000003', 'Amit Kumar', NULL, 'amit.kumar@example.com', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '110001', 'House 56, Sector 14, Connaught Place, New Delhi', 28.61393910, 77.20902120, 'dev_token_ios_3', 'uuid_unique_3', 'iPhone 15 Pro / iOS 17', 3, '123456', 1, '2026-12-31 23:59:59', 1, 0, '2026-07-02 14:00:00', '2026-07-02 14:00:00'),
('u1000000-0000-0000-0000-000000000004', 'Sneha Reddy', 'profiles/1784906319_Kg4MGtjuKf.jpg', 'sneha.reddy@example.com', '9876543213', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '500081', 'Villa 8, Cyber Palm Meadows, Madhapur, Hyderabad', 17.44829300, 78.39148500, 'dev_token_android_4', 'uuid_unique_4', 'OnePlus 11 / Android 14', 2, '123456', 1, '2026-12-31 23:59:59', 1, 800, '2026-07-10 16:45:00', '2026-09-02 11:00:00'),
('u1000000-0000-0000-0000-000000000005', 'Vikram Singh', NULL, 'vikram.singh@example.com', '9876543214', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '560001', 'Flat 304, Green Glen Layout, Bellandur, Bangalore', 12.97159870, 77.59456270, 'dev_token_web_5', 'uuid_unique_5', 'Safari / macOS Sonoma', 1, '123456', 1, '2026-12-31 23:59:59', 1, 200, '2026-07-18 10:20:00', '2026-08-30 18:00:00'),
('u1000000-0000-0000-0000-000000000006', 'Ananya Deshmukh', NULL, 'ananya.deshmukh@example.com', '9876543215', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '411004', 'Row House 4, Deccan Gymkhana, Pune', 18.52043030, 73.85674370, 'dev_token_android_6', 'uuid_unique_6', 'Xiaomi Redmi Note 13 / Android 13', 2, '123456', 1, '2026-12-31 23:59:59', 1, 0, '2026-07-25 12:00:00', '2026-07-25 12:00:00'),
('u1000000-0000-0000-0000-000000000007', 'Rajesh Verma', NULL, 'rajesh.verma@example.com', '9876543216', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '600001', 'Plot 88, Anna Nagar West, Chennai', 13.08268020, 80.27071840, 'dev_token_web_7', 'uuid_unique_7', 'Firefox 122 / Windows 10', 1, '123456', 1, '2026-12-31 23:59:59', 1, 500, '2026-08-01 08:30:00', '2026-09-03 16:10:00'),
('u1000000-0000-0000-0000-000000000008', 'Pooja Mehta', 'profiles/1784906319_Kg4MGtjuKf.jpg', 'pooja.mehta@example.com', '9876543217', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '700001', 'Block B-101, Park Street, Kolkata', 22.57264600, 88.36389500, 'dev_token_ios_8', 'uuid_unique_8', 'iPhone 14 / iOS 17', 3, '123456', 1, '2026-12-31 23:59:59', 1, 1200, '2026-08-05 15:10:00', '2026-09-04 11:40:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: orders
-- ------------------------------------------------------------------------------
INSERT INTO `orders` (`id`, `user_uuid`, `category_uuid`, `subcategory_uuid`, `items`, `pickup_location`, `pickup_date`, `pickup_time`, `images`, `notes`, `status`, `estimated_pickup_date`, `total_amount`, `payment_status`, `payment_id`, `coins_earned`, `coins_redeemed`, `available_coins`, `coins_expires_at`, `discount_applied`, `created_at`, `updated_at`) VALUES
(1, 'u1000000-0000-0000-0000-000000000001', 'c1000000-0000-0000-0000-000000000001', 's1000000-0000-0000-0000-000000000001', '[{"name": "Newspaper (Raddi)", "quantity": 30, "subcategory_uuid": "s1000000-0000-0000-0000-000000000001"}, {"name": "Corrugated Cardboard / Carton", "quantity": 15, "subcategory_uuid": "s1000000-0000-0000-0000-000000000002"}]', 'A-402, Sea View Towers, Worli, Mumbai', '2026-06-22', '10:00 AM - 12:00 PM', '["subcategories/1781419825_6T5qZvoMG3.png"]', 'Please call 15 minutes before arrival.', 'completed', '2026-06-22 10:30:00', 680.00, 'completed', 'PAY_RZP_10001', 150, 150, '2026-12-22 10:30:00', 0.00, '2026-06-21 14:00:00', '2026-06-22 11:00:00'),
(2, 'u1000000-0000-0000-0000-000000000001', 'c1000000-0000-0000-0000-000000000003', 's1000000-0000-0000-0000-000000000007', '[{"name": "Iron & Heavy Steel", "quantity": 55, "subcategory_uuid": "s1000000-0000-0000-0000-000000000007"}, {"name": "Copper Wire & Pipes", "quantity": 5, "subcategory_uuid": "s1000000-0000-0000-0000-000000000008"}]', 'A-402, Sea View Towers, Worli, Mumbai', '2026-07-15', '02:00 PM - 04:00 PM', '["subcategories/1781431012_B2Tdqb0big.png"]', 'Old renovation steel scrap and copper wires.', 'completed', '2026-07-15 14:15:00', 3850.00, 'completed', 'PAY_RZP_10002', 500, 500, '2026-12-31 23:59:59', 0.00, '2026-07-14 09:30:00', '2026-07-15 15:30:00'),
(3, 'u1000000-0000-0000-0000-000000000002', 'c1000000-0000-0000-0000-000000000005', 's1000000-0000-0000-0000-000000000013', '[{"name": "Air Conditioner (Split / Window)", "quantity": 1, "subcategory_uuid": "s1000000-0000-0000-0000-000000000013"}]', 'Flat 12, Sunrise Residency, SG Highway, Ahmedabad', '2026-07-28', '11:00 AM - 01:00 PM', '["subcategories/1781419825_6T5qZvoMG3.png"]', 'Old non-working 1.5 ton split AC outdoor and indoor unit.', 'completed', '2026-07-28 11:30:00', 4200.00, 'completed', 'PAY_RZP_10003', 500, 0, '2026-10-28 11:30:00', 0.00, '2026-07-27 16:00:00', '2026-07-28 12:45:00'),
(4, 'u1000000-0000-0000-0000-000000000002', 'c1000000-0000-0000-0000-000000000001', 's1000000-0000-0000-0000-000000000003', '[{"name": "Old Books & Magazines", "quantity": 25, "subcategory_uuid": "s1000000-0000-0000-0000-000000000003"}]', 'Flat 12, Sunrise Residency, SG Highway, Ahmedabad', '2026-08-20', '03:00 PM - 05:00 PM', NULL, 'College books and packaging cartons.', 'completed', '2026-08-20 15:30:00', 450.00, 'completed', 'PAY_RZP_10004', 50, 0, '2026-11-20 15:30:00', 0.00, '2026-08-19 11:00:00', '2026-08-20 16:20:00'),
(5, 'u1000000-0000-0000-0000-000000000004', 'c1000000-0000-0000-0000-000000000004', 's1000000-0000-0000-0000-000000000011', '[{"name": "Laptops & Computers", "quantity": 3, "subcategory_uuid": "s1000000-0000-0000-0000-000000000011"}, {"name": "Mobile Phones & Tablets", "quantity": 4, "subcategory_uuid": "s1000000-0000-0000-0000-000000000012"}]', 'Villa 8, Cyber Palm Meadows, Madhapur, Hyderabad', '2026-08-12', '10:00 AM - 12:00 PM', '["subcategories/1781431902_uBsf8ndplc.png"]', 'Office electronics cleanout.', 'completed', '2026-08-12 10:45:00', 5500.00, 'completed', 'PAY_RZP_10005', 2000, 800, '2027-02-12 10:45:00', 0.00, '2026-08-11 18:00:00', '2026-08-12 11:30:00'),
(6, 'u1000000-0000-0000-0000-000000000005', 'c1000000-0000-0000-0000-000000000007', 's1000000-0000-0000-0000-000000000018', '[{"name": "Lead Acid Inverter / Car Battery", "quantity": 2, "subcategory_uuid": "s1000000-0000-0000-0000-000000000018"}]', 'Flat 304, Green Glen Layout, Bellandur, Bangalore', '2026-08-25', '04:00 PM - 06:00 PM', '["subcategories/1781430902_yS6qWGhrMG.png"]', '2 Old 150Ah Exide tubular batteries.', 'completed', '2026-08-25 16:30:00', 2600.00, 'completed', 'PAY_RZP_10006', 500, 200, '2026-11-25 16:30:00', 0.00, '2026-08-24 13:00:00', '2026-08-25 17:15:00'),
(7, 'u1000000-0000-0000-0000-000000000007', 'c1000000-0000-0000-0000-000000000003', 's1000000-0000-0000-0000-000000000009', '[{"name": "Aluminium Sections & Cans", "quantity": 20, "subcategory_uuid": "s1000000-0000-0000-0000-000000000009"}, {"name": "Brass & Bronze", "quantity": 6, "subcategory_uuid": "s1000000-0000-0000-0000-000000000010"}]', 'Plot 88, Anna Nagar West, Chennai', '2026-08-30', '11:00 AM - 01:00 PM', '["subcategories/1781431804_dkuFDW49rg.png"]', 'House renovation metal scraps.', 'completed', '2026-08-30 11:30:00', 4800.00, 'completed', 'PAY_RZP_10007', 500, 500, '2026-11-30 11:30:00', 0.00, '2026-08-29 10:15:00', '2026-08-30 12:15:00'),
(8, 'u1000000-0000-0000-0000-000000000008', 'c1000000-0000-0000-0000-000000000005', 's1000000-0000-0000-0000-000000000014', '[{"name": "Refrigerator / Fridge", "quantity": 1, "subcategory_uuid": "s1000000-0000-0000-0000-000000000014"}, {"name": "Washing Machine", "quantity": 1, "subcategory_uuid": "s1000000-0000-0000-0000-000000000015"}]', 'Block B-101, Park Street, Kolkata', '2026-09-02', '02:00 PM - 04:00 PM', '["subcategories/1781419847_TSp4N9RxF5.jpeg"]', 'Heavy appliances on 1st floor with lift access.', 'completed', '2026-09-02 14:30:00', 3200.00, 'completed', 'PAY_RZP_10008', 500, 1200, '2026-12-02 14:30:00', 0.00, '2026-09-01 16:20:00', '2026-09-02 15:40:00'),
(9, 'u1000000-0000-0000-0000-000000000001', 'c1000000-0000-0000-0000-000000000002', 's1000000-0000-0000-0000-000000000006', '[{"name": "Hard Plastic / Buckets & Chairs", "quantity": 18, "subcategory_uuid": "s1000000-0000-0000-0000-000000000006"}]', 'A-402, Sea View Towers, Worli, Mumbai', '2026-10-05', '10:00 AM - 12:00 PM', NULL, 'Plastic storage containers and buckets.', 'accepted', '2026-10-05 10:30:00', NULL, 'pending', NULL, 0, 0, 0, NULL, 0.00, '2026-10-04 09:00:00', '2026-10-04 10:15:00'),
(10, 'u1000000-0000-0000-0000-000000000003', 'c1000000-0000-0000-0000-000000000001', 's1000000-0000-0000-0000-000000000004', '[{"name": "Office White Paper", "quantity": 80, "subcategory_uuid": "s1000000-0000-0000-0000-000000000004"}, {"name": "Corrugated Cardboard / Carton", "quantity": 40, "subcategory_uuid": "s1000000-0000-0000-0000-000000000002"}]', 'House 56, Sector 14, Connaught Place, New Delhi', '2026-10-06', '01:00 PM - 03:00 PM', '["subcategories/1781430746_kYdY2O1kc3.png"]', 'Commercial bulk office papers.', 'pending', NULL, NULL, 'pending', NULL, 0, 0, 0, NULL, 0.00, '2026-10-04 11:30:00', '2026-10-04 11:30:00'),
(11, 'u1000000-0000-0000-0000-000000000006', 'c1000000-0000-0000-0000-000000000004', 's1000000-0000-0000-0000-000000000011', '[{"name": "Laptops & Computers", "quantity": 2, "subcategory_uuid": "s1000000-0000-0000-0000-000000000011"}]', 'Row House 4, Deccan Gymkhana, Pune', '2026-10-06', '04:00 PM - 06:00 PM', NULL, 'Non-booting old laptops.', 'pending', NULL, NULL, 'pending', NULL, 0, 0, 0, NULL, 0.00, '2026-10-04 13:45:00', '2026-10-04 13:45:00'),
(12, 'u1000000-0000-0000-0000-000000000005', 'c1000000-0000-0000-0000-000000000003', 's1000000-0000-0000-0000-000000000007', '[{"name": "Iron & Heavy Steel", "quantity": 10, "subcategory_uuid": "s1000000-0000-0000-0000-000000000007"}]', 'Flat 304, Green Glen Layout, Bellandur, Bangalore', '2026-08-05', '10:00 AM - 12:00 PM', NULL, 'Client requested cancellation due to travel.', 'cancelled', NULL, NULL, 'cancelled', NULL, 0, 0, 0, NULL, 0.00, '2026-08-04 15:00:00', '2026-08-05 09:30:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: scrap_vehicles
-- ------------------------------------------------------------------------------
INSERT INTO `scrap_vehicles` (`id`, `user_uuid`, `vehicle_type`, `vehicle_number`, `vehicle_brand`, `vehicle_model`, `photos`, `remark`, `status`, `created_at`, `updated_at`) VALUES
(1, 'u1000000-0000-0000-0000-000000000001', 'Two Wheeler', 'MH-01-AB-1234', 'Honda', 'Activa 3G (2010)', '["requestbanenr.png"]', 'Engine seized, RC book available for scrap certification.', 'completed', '2026-07-10 11:00:00', '2026-07-12 16:30:00'),
(2, 'u1000000-0000-0000-0000-000000000003', 'Four Wheeler', 'DL-03-CC-5678', 'Maruti Suzuki', 'Zen Estilo (2008)', '["requestbanenr.png"]', '15-year registration expired. Complete vehicle available at Connaught Place.', 'completed', '2026-08-01 14:20:00', '2026-08-05 10:00:00'),
(3, 'u1000000-0000-0000-0000-000000000004', 'Two Wheeler', 'TS-09-XY-9012', 'Bajaj', 'Pulsar 150 (2011)', '["requestbanenr.png"]', 'Accidental frame damage, engine parts intact.', 'pending', '2026-10-01 09:40:00', '2026-10-01 09:40:00'),
(4, 'u1000000-0000-0000-0000-000000000007', 'Commercial Vehicle', 'TN-02-ZZ-4321', 'Tata Motors', 'Ace Mini Truck (2009)', '["requestbanenr.png"]', 'Commercial scrap quotation requested with RTO deregistration.', 'pending', '2026-10-03 15:10:00', '2026-10-03 15:10:00');

-- ------------------------------------------------------------------------------
-- Dumping data for table: feedback
-- ------------------------------------------------------------------------------
INSERT INTO `feedback` (`id`, `user_uuid`, `star_rating`, `comment`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 'u1000000-0000-0000-0000-000000000001', 5, 'Super fast and punctual pickup! The pickup boy came with a digital weighing machine and paid immediately via UPI. Highly recommended.', 1, '2026-06-23 12:00:00', '2026-06-23 14:00:00'),
(2, 'u1000000-0000-0000-0000-000000000002', 5, 'Disposed of our old AC unit without any hassle. Very polite staff and transparent pricing compared to local scrap dealers.', 1, '2026-07-29 15:30:00', '2026-07-29 16:00:00'),
(3, 'u1000000-0000-0000-0000-000000000004', 5, 'Great initiative! Sold all our old office laptops and e-waste. Received certified reward coins too.', 1, '2026-08-13 18:00:00', '2026-08-13 18:30:00'),
(4, 'u1000000-0000-0000-0000-000000000005', 4, 'Very convenient doorstep pickup service in Bangalore. Weighing was accurate and payment was credited on the spot.', 1, '2026-08-26 10:15:00', '2026-08-26 11:00:00'),
(5, 'u1000000-0000-0000-0000-000000000007', 5, 'Best rates for copper and aluminium metal scrap. The mobile app makes booking extremely simple.', 1, '2026-08-31 16:40:00', '2026-08-31 17:00:00'),
(6, 'u1000000-0000-0000-0000-000000000008', 5, 'Wonderful team. They helped carry the old washing machine and fridge down the stairs safely.', 1, '2026-09-03 09:50:00', '2026-09-03 10:30:00');

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
