-- Adminer 5.4.1 MySQL 8.0.27 dump
CREATE DATABASE IF NOT EXISTS toni_garage;
USE toni_garage;


SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `engine`;
CREATE TABLE `engine` (
  `engine_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `horse_power` int DEFAULT NULL,
  PRIMARY KEY (`engine_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `engine` (`engine_id`, `name`, `description`, `type`, `horse_power`) VALUES
(1,	'EcoBoost 1.5',	'Turbocharged inline-4',	'Petrol',	180),
(2,	'TDI 2.0',	'Turbo diesel',	'Diesel',	150);

DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `feature_id` int NOT NULL AUTO_INCREMENT,
  `engine_id` int DEFAULT NULL,
  `transmission_id` int DEFAULT NULL,
  `interior_id` int DEFAULT NULL,
  `fuel` varchar(25) DEFAULT NULL,
  `mileage` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`feature_id`),
  KEY `idx_features_engine` (`engine_id`),
  KEY `idx_features_transmission` (`transmission_id`),
  KEY `idx_features_interior` (`interior_id`),
  CONSTRAINT `fk_features_engine` FOREIGN KEY (`engine_id`) REFERENCES `engine` (`engine_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_features_interior` FOREIGN KEY (`interior_id`) REFERENCES `interior` (`interior_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_features_transmission` FOREIGN KEY (`transmission_id`) REFERENCES `transmission` (`transmission_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `features` (`feature_id`, `engine_id`, `transmission_id`, `interior_id`, `fuel`, `mileage`) VALUES
(1,	1,	1,	1,	'Petrol',	'45,000 km'),
(2,	2,	2,	2,	'Diesel',	'82,300 km'),
(3,	1,	2,	2,	'Petrol',	'12,500 km');

DROP TABLE IF EXISTS `interior`;
CREATE TABLE `interior` (
  `interior_id` int NOT NULL AUTO_INCREMENT,
  `materials_1` varchar(25) DEFAULT NULL,
  `materials_2` varchar(25) DEFAULT NULL,
  `lighting_description` varchar(25) DEFAULT NULL,
  `heated_seats` tinyint(1) DEFAULT NULL,
  `ac` tinyint(1) DEFAULT NULL,
  `smart_screen` tinyint(1) DEFAULT NULL,
  `custom_steering` tinyint(1) DEFAULT NULL,
  `color_1` varchar(6) DEFAULT NULL,
  `color_2` varchar(6) DEFAULT NULL,
  `color_3` varchar(6) DEFAULT NULL,
  `color_4` varchar(6) DEFAULT NULL,
  `image_id` int DEFAULT NULL,
  PRIMARY KEY (`interior_id`),
  KEY `fk_interior_image` (`image_id`),
  CONSTRAINT `fk_interior_image` FOREIGN KEY (`image_id`) REFERENCES `interior_images` (`image_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `interior` (`interior_id`, `materials_1`, `materials_2`, `lighting_description`, `heated_seats`, `ac`, `smart_screen`, `custom_steering`, `color_1`, `color_2`, `color_3`, `color_4`, `image_id`) VALUES
(1,	'Leather',	'Alcantara',	'Ambient',	1,	1,	1,	0,	'000000',	'333333',	'666666',	'999999',	1),
(2,	'Fabric',	'Plastic',	'Standard',	0,	1,	1,	0,	'111111',	'222222',	'444444',	'888888',	2);

DROP TABLE IF EXISTS `interior_images`;
CREATE TABLE `interior_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `interior_images` (`image_id`, `image_url`) VALUES
(1,	'https://pics.example.com/interiors/int-1.jpg'),
(2,	'https://pics.example.com/interiors/int-2.jpg');

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `credit_holder_fname` varchar(100) DEFAULT NULL,
  `credit_holder_lname` varchar(100) DEFAULT NULL,
  `card_token` varchar(100) DEFAULT NULL,
  `last_four` int DEFAULT NULL,
  `transaction_code` int DEFAULT NULL,
  `transaction_time` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uk_role_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `role` (`role_id`, `name`) VALUES
(1,	'Admin'),
(3,	'Buyer'),
(2,	'Dealer');

DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `sales_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `vehicle_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `payment_id` int DEFAULT NULL,
  PRIMARY KEY (`sales_id`),
  KEY `fk_sales_payment` (`payment_id`),
  KEY `idx_sales_customer` (`customer_id`),
  KEY `idx_sales_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_sales_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_sales_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `transmission`;
CREATE TABLE `transmission` (
  `transmission_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`transmission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `transmission` (`transmission_id`, `name`, `type`, `description`) VALUES
(1,	'ZF-8HP',	'Automatic',	'8-speed'),
(2,	'Getrag-6',	'Manual',	'6-speed manual');

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `type_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `types` (`type_id`, `name`) VALUES
(1,	'Sedan'),
(2,	'SUV'),
(3,	'Truck');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` int DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `fk_users_role` (`role_id`),
  KEY `idx_users_email` (`email`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`user_id`, `role_id`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `address`, `state`, `zip_code`, `country`) VALUES
(1,	2,	'Lea',	'Kovač',	'$2y$10$hash',	'lea.dealer@example.com',	'+385-20-111-222',	'Iza Grada 5',	'Dubrovnik',	20000,	'Croatia'),
(2,	3,	'Marko',	'Šimić',	'$2y$10$hash',	'marko.buyer@example.com',	'+385-20-333-444',	'Svetog Dominika 3',	'Dubrovnik',	20000,	'Croatia');

DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE `vehicle` (
    `vehicle_id` int NOT NULL AUTO_INCREMENT,
    `user_id` int DEFAULT NULL,
    `features_id` int DEFAULT NULL,
    `type_id` int DEFAULT NULL,
    `name` varchar(100) DEFAULT NULL,
    `doc` date DEFAULT NULL,
    `price` int DEFAULT NULL,
    `model` varchar(50) DEFAULT NULL,
    `owned_before` tinyint(1) DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `number_sold` int DEFAULT NULL,
    `color_1` varchar(6) DEFAULT NULL,
    `color_2` varchar(6) DEFAULT NULL,
    `color_3` varchar(6) DEFAULT NULL,
    `color_4` varchar(6) DEFAULT NULL,
    `image_id` int DEFAULT NULL,
    `is_featured` tinyint(1) DEFAULT 0,
    PRIMARY KEY (`vehicle_id`),
    KEY `fk_vehicle_image` (`image_id`),
    KEY `idx_vehicle_user` (`user_id`),
    KEY `idx_vehicle_type` (`type_id`),
    KEY `idx_vehicle_features` (`features_id`),
    CONSTRAINT `fk_vehicle_features` FOREIGN KEY (`features_id`) REFERENCES `features` (`feature_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_vehicle_image` FOREIGN KEY (`image_id`) REFERENCES `vehicle_images` (`image_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_vehicle_type` FOREIGN KEY (`type_id`) REFERENCES `types` (`type_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_vehicle_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `vehicle` (`vehicle_id`, `user_id`, `features_id`, `type_id`, `name`, `doc`, `price`, `model`, `owned_before`, `description`, `number_sold`, `color_1`, `color_2`, `color_3`, `color_4`, `image_id`) VALUES
(1,	1,	1,	1,	'Ford Focus',	'2023-06-01',	9500,	'Focus SE',	1,	'Clean, one owner',	12,	'ffffff',	'0000ff',	'000000',	'cccccc',	16),
(2,	1,	2,	2,	'VW Tiguan',	'2022-10-12',	18500,	'Tiguan TDI',	1,	'Well maintained',	5,	'ffffff',	'00ff00',	'000000',	'cccccc',	7),
(3,	1,	3,	3,	'Ford Ranger',	'2024-03-20',	26500,	'Ranger XLT',	0,	'Low mileage demo',	2,	'ffffff',	'ff0000',	'000000',	'cccccc',	8);

DROP TABLE IF EXISTS `vehicle_images`;
CREATE TABLE `vehicle_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `vehicle_images` (`image_id`, `image_url`) VALUES
(1,	'https://pics.example.com/vehicles/veh-1.jpg'),
(2,	'https://pics.example.com/vehicles/veh-2.jpg'),
(3,	'https://pics.example.com/vehicles/veh-3.jpg'),
(4,	'https://www.grandauto.hr/EasyEdit/UserFiles/Catalog/5937/ford-focus-titanium-15-ecoblue-115-ks-a8-4v-diesel-638569147706457546_785_442.jpeg'),
(5,	'https://di-uploads-pod10.dealerinspire.com/sutliffvolkswagen/uploads/2022/10/Sutliff-Volkswagen-Tiguan.png'),
(6,	'https://www.grandauto.hr/EasyEdit/UserFiles/Catalog/5937/ford-focus-titanium-15-ecoblue-115-ks-a8-4v-diesel-638569147706457546_785_442.jpeg'),
(7,	'https://di-uploads-pod10.dealerinspire.com/sutliffvolkswagen/uploads/2022/10/Sutliff-Volkswagen-Tiguan.png'),
(8,	'https://www.grandauto.hr/EasyEdit/UserFiles/Catalog/6856/ford-ranger-p703-dcab-wildtrak-x-4wd-20-tdci-205-k-638855058553552219_785_442@2x.jpeg'),
(9,	'https://thumbs.dreamstime.com/b/portrait-indian-sadhu-baba-varanasi-elderly-man-bright-orange-robes-headgear-has-long-white-beard-wears-365340278.jpg'),
(10,	'https://www.grandauto.hr/EasyEdit/UserFiles/Catalog/5937/ford-focus-titanium-15-ecoblue-115-ks-a8-4v-diesel-638569147706457546_785_442.jpeg'),
(11,	'https://thumbs.dreamstime.com/b/portrait-indian-sadhu-baba-varanasi-elderly-man-bright-orange-robes-headgear-has-long-white-beard-wears-365340278.jpg'),
(12,	'https://thumbs.dreamstime.com/b/portrait-indian-sadhu-baba-varanasi-elderly-man-bright-orange-robes-headgear-has-long-white-beard-wears-365340278.jpg'),
(13,	'https://thumbs.dreamstime.com/b/portrait-indian-sadhu-baba-varanasi-elderly-man-bright-orange-robes-headgear-has-long-white-beard-wears-365340278.jpg'),
(14,	'https://www.grandauto.hr/EasyEdit/UserFiles/Catalog/5937/ford-focus-titanium-15-ecoblue-115-ks-a8-4v-diesel-638569147706457546_785_442.jpeg'),
(15,	'https://thumbs.dreamstime.com/b/portrait-indian-sadhu-baba-varanasi-elderly-man-bright-orange-robes-headgear-has-long-white-beard-wears-365340278.jpg'),
(16,	'https://www.grandauto.hr/EasyEdit/UserFiles/Catalog/5937/ford-focus-titanium-15-ecoblue-115-ks-a8-4v-diesel-638569147706457546_785_442.jpeg');

-- 2025-11-09 16:01:18 UTC