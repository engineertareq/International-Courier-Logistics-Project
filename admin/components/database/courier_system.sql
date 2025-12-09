-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 07:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `courier_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'gvdluefgqe;', '{}', '2025-12-09 15:45:47');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `type` enum('PICKUP','DELIVERY','TRANSFER') DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('assigned','in_progress','completed','failed') DEFAULT 'assigned',
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `rider_id`, `shipment_id`, `type`, `assigned_at`, `status`, `completed_at`) VALUES
(1, 1, 1, 'PICKUP', '2025-12-09 16:52:41', 'assigned', NULL),
(2, 1, 3, 'PICKUP', '2025-12-09 16:57:00', 'assigned', NULL),
(3, 1, 3, 'DELIVERY', '2025-12-09 16:57:07', 'assigned', NULL),
(4, 1, 3, 'DELIVERY', '2025-12-09 16:58:59', 'assigned', NULL),
(5, 4, 3, 'DELIVERY', '2025-12-09 17:00:08', 'assigned', NULL),
(6, 5, 3, 'DELIVERY', '2025-12-09 17:00:18', 'assigned', NULL),
(7, 5, 3, 'DELIVERY', '2025-12-09 17:00:40', 'assigned', NULL),
(8, 5, 3, 'DELIVERY', '2025-12-09 17:01:09', 'in_progress', NULL),
(9, 5, 3, 'DELIVERY', '2025-12-09 17:02:05', 'assigned', NULL),
(10, 1, 3, 'TRANSFER', '2025-12-09 17:07:25', 'assigned', NULL),
(11, 6, 3, 'TRANSFER', '2025-12-09 17:07:41', 'in_progress', NULL),
(12, 1, 3, 'PICKUP', '2025-12-09 17:40:32', 'completed', '2025-12-09 17:42:11'),
(13, 1, 3, 'PICKUP', '2025-12-09 17:41:16', 'in_progress', NULL),
(14, 2, 2, 'DELIVERY', '2025-12-09 17:46:51', 'completed', '2025-12-09 17:47:33');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `country_id` int(11) NOT NULL,
  `address` varchar(300) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `type` enum('Head Office','Hub','Depot','Local Branch') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `country_id`, `address`, `phone`, `type`, `created_at`, `updated_at`, `latitude`, `longitude`) VALUES
(1, 'Motijheel', 1, '95, City Center, Motijheel, 1000', '+8801795611971', 'Head Office', '2025-12-07 20:04:13', '2025-12-07 20:04:13', NULL, NULL),
(2, 'Head Office Bhutan', 8, 'Hub', NULL, 'Head Office', '2025-12-09 15:19:09', '2025-12-09 15:19:09', NULL, NULL),
(3, 'Head Office Bhutan', 2, 'Dellhi', NULL, 'Head Office', '2025-12-09 15:23:31', '2025-12-09 15:23:31', NULL, NULL),
(4, 'Berhington', 6, 'BR', NULL, 'Head Office', '2025-12-09 15:28:45', '2025-12-09 15:28:45', NULL, NULL),
(5, 'Head Office Bhutan', 11, 'Hub', NULL, 'Depot', '2025-12-09 15:30:13', '2025-12-09 15:30:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `carriers`
--

CREATE TABLE `carriers` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `service_code` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carriers`
--

INSERT INTO `carriers` (`id`, `name`, `service_code`) VALUES
(1, 'FEDEX', 'FEDEX_AIR'),
(2, 'DHL', 'DHL_AIR'),
(3, 'Aramex', 'Aramex_Air'),
(4, 'Aramex', 'Aramex_Ai'),
(5, 'Aramex', 'Aramex_A');

-- --------------------------------------------------------

--
-- Table structure for table `cod_orders`
--

CREATE TABLE `cod_orders` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `collected_by` int(11) DEFAULT NULL,
  `collection_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','collected','transferred_to_branch') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cod_orders`
--

INSERT INTO `cod_orders` (`id`, `shipment_id`, `amount`, `collected_by`, `collection_date`, `status`) VALUES
(1, 1, 1000.00, 4, '2025-12-09 15:04:26', 'collected'),
(3, 2, 20.00, NULL, '2025-12-09 15:46:21', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `cod_settlements`
--

CREATE TABLE `cod_settlements` (
  `id` int(11) NOT NULL,
  `cod_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `settlement_amount` decimal(10,2) DEFAULT NULL,
  `settlement_reference` varchar(100) DEFAULT NULL,
  `settlement_date` date DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cod_settlements`
--

INSERT INTO `cod_settlements` (`id`, `cod_id`, `customer_id`, `settlement_amount`, `settlement_reference`, `settlement_date`, `status`) VALUES
(1, 1, 1, 50.00, 'rhtyi7u', '2025-12-09', 'pending'),
(2, 1, 3, 10.00, NULL, '2025-12-09', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `iso_code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `iso_code`) VALUES
(1, 'Bangladesh', 'BD'),
(2, 'India', 'IN'),
(4, 'United State of America', 'USA'),
(6, 'United Kingdom', 'UK'),
(7, 'Nepal', 'NP'),
(8, 'Bhutan', 'BT'),
(9, 'Sri Lanka', 'LKA'),
(10, 'Japan', 'JPN'),
(11, 'Germany', 'DE'),
(12, 'Uganda', 'UGA'),
(13, 'Zimbabwe', 'ZWE');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(50) DEFAULT NULL,
  `contact_person_name` varchar(50) DEFAULT NULL,
  `billing_address` varchar(200) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `vat_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `company_name`, `contact_person_name`, `billing_address`, `country_id`, `city`, `postal_code`, `vat_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'USSOFT', 'Tareq', '95, Motijheel', 1, 'DHaka', '1000', '54547897486419', '2025-12-09 12:10:47', '2025-12-09 12:10:47'),
(3, 3, 'Queeny Limited', 'Sachinoor Sachi', NULL, 1, NULL, NULL, '4984894894', '2025-12-09 12:44:32', '2025-12-09 12:44:32'),
(5, 4, 'Queeny Limited', 'Sachinoor Sachi', NULL, 1, NULL, NULL, '4984894894', '2025-12-09 14:43:06', '2025-12-09 14:43:06'),
(8, 12, 'Wolf Express Limited', 'Tanjimul Islam Tareq', '93, Bernaiya, Shahrasti, Chandpur', 1, 'Chandpur', '3623', '4984894894', '2025-12-09 15:40:17', '2025-12-09 15:40:17');

-- --------------------------------------------------------

--
-- Table structure for table `customs_documents`
--

CREATE TABLE `customs_documents` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `hs_code` varchar(20) DEFAULT NULL,
  `value_usd` decimal(10,2) DEFAULT NULL,
  `origin_country_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customs_documents`
--

INSERT INTO `customs_documents` (`id`, `shipment_id`, `hs_code`, `value_usd`, `origin_country_id`, `description`, `document_url`) VALUES
(1, 1, NULL, 0.00, 1, 'retpuirepiuthue', '5yw6wu56u5ue'),
(2, 1, 'ftjtry', NULL, NULL, 'Commercial Invoice', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sub_total` decimal(10,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('paid','pending','voided','overdue') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_no`, `shipment_id`, `customer_id`, `sub_total`, `tax_amount`, `total_amount`, `currency`, `issue_date`, `due_date`, `status`, `created_at`) VALUES
(1, 'ru7ro87p9y8p89', 1, 1, 10.00, 10.00, 100.00, 'USD', '2025-12-09', '2025-12-10', 'pending', '2025-12-09 12:23:09'),
(2, 'INV-176529504928', 1, 3, NULL, NULL, 0.00, 'USD', '2025-12-09', NULL, 'pending', '2025-12-09 15:44:09'),
(3, 'INV-176529511718', 2, 3, NULL, NULL, 0.00, 'USD', '2025-12-09', NULL, 'pending', '2025-12-09 15:45:17');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipment_id` int(11) DEFAULT NULL,
  `type` enum('sms','email','whatsapp','system') DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('sent','failed','read') DEFAULT 'sent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `shipment_id`, `type`, `message`, `status`, `created_at`) VALUES
(1, 2, 1, 'sms', 'yrgperptiyep5it', 'sent', '2025-12-09 12:23:47'),
(2, 5, NULL, 'system', 'setp8e4p8t', 'sent', '2025-12-09 12:50:44'),
(3, 11, NULL, 'system', 'setp8e4p8t', 'sent', '2025-12-09 15:45:54');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` enum('credit_card','bank_transfer','cash','cheque') DEFAULT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` enum('completed','failed','refunded') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `amount`, `method`, `transaction_ref`, `payment_date`, `status`) VALUES
(1, 1, 1000.00, 'bank_transfer', 'hguyi7iyi', '2025-12-09 18:23:32', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `rate_slabs`
--

CREATE TABLE `rate_slabs` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `service_type` enum('Standard','Express','Economy') NOT NULL,
  `min_weight` decimal(10,3) DEFAULT NULL,
  `max_weight` decimal(10,3) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `price_per_kg` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_slabs`
--

INSERT INTO `rate_slabs` (`id`, `zone_id`, `service_type`, `min_weight`, `max_weight`, `base_price`, `price_per_kg`) VALUES
(1, 1, 'Express', 0.500, 150.000, 10.00, 1.00),
(2, 1, 'Express', 0.500, 150.000, 10.00, 1.00),
(3, 4, 'Express', 1.000, 10000.000, 1.00, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `rate_zones`
--

CREATE TABLE `rate_zones` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_zones`
--

INSERT INTO `rate_zones` (`id`, `name`, `description`) VALUES
(1, 'Asia', 'All Countries Exists in Asia'),
(2, 'Europe', 'USA, UK, Canada'),
(3, 'Europe', 'USA, UK, Canada'),
(4, 'Middle Est', 'All COuntries Across Middle Est'),
(5, 'Europe', 'All COuntries Across Middle Est');

-- --------------------------------------------------------

--
-- Table structure for table `riders`
--

CREATE TABLE `riders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `vehicle_type` enum('bike','van','truck','scooter') DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riders`
--

INSERT INTO `riders` (`id`, `user_id`, `branch_id`, `vehicle_type`, `availability`, `active`) VALUES
(1, 4, 1, 'bike', 1, 1),
(2, 1, 1, 'scooter', 1, 1),
(4, 2, 1, 'van', 1, 1),
(5, 3, 1, 'scooter', 1, 1),
(6, 12, 2, 'van', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `degignation` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `degignation`) VALUES
(1, 'admin', 'The Controller of the Whole Sy');

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL,
  `tracking_no` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `sender_name` varchar(50) DEFAULT NULL,
  `sender_address` varchar(300) DEFAULT NULL,
  `sender_city` varchar(30) DEFAULT NULL,
  `sender_postal_code` varchar(15) DEFAULT NULL,
  `sender_country_id` int(11) DEFAULT NULL,
  `receiver_name` varchar(50) DEFAULT NULL,
  `receiver_address` varchar(300) DEFAULT NULL,
  `receiver_city` varchar(30) DEFAULT NULL,
  `receiver_postal_code` varchar(20) DEFAULT NULL,
  `receiver_country_id` int(11) DEFAULT NULL,
  `origin_branch_id` int(11) DEFAULT NULL,
  `destination_branch_id` int(11) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `service_type` enum('Standard','Express','Economy') NOT NULL,
  `shipment_type` enum('document','parcel','freight') NOT NULL,
  `total_weight` decimal(10,3) DEFAULT NULL,
  `chargeable_weight` decimal(10,3) DEFAULT NULL,
  `length_cm` decimal(10,2) DEFAULT NULL,
  `width_cm` decimal(10,2) DEFAULT NULL,
  `height_cm` decimal(10,2) DEFAULT NULL,
  `cod_amount` decimal(10,2) DEFAULT 0.00,
  `status` varchar(60) DEFAULT 'Created',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transport_mode` enum('Air','Road','Sea','Rail') DEFAULT 'Air'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`id`, `tracking_no`, `customer_id`, `sender_name`, `sender_address`, `sender_city`, `sender_postal_code`, `sender_country_id`, `receiver_name`, `receiver_address`, `receiver_city`, `receiver_postal_code`, `receiver_country_id`, `origin_branch_id`, `destination_branch_id`, `carrier_id`, `service_type`, `shipment_type`, `total_weight`, `chargeable_weight`, `length_cm`, `width_cm`, `height_cm`, `cod_amount`, `status`, `created_at`, `updated_at`, `transport_mode`) VALUES
(1, '544874874984', 1, 'Tareq', '95, Motiheel Dhaka', 'Dhaka', '1000', 1, 'Hasib', 'hasibgt@gmail.com', 'Tokyo', '479846', 10, 1, 1, 1, 'Standard', 'document', 1.000, 1.200, 10.00, 10.00, 10.00, 100.00, 'Created', '2025-12-09 12:19:50', '2025-12-09 12:19:50', 'Road'),
(2, 'TRK-8C9469C045', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 4, NULL, 'Standard', 'parcel', 0.000, NULL, NULL, NULL, NULL, 0.00, 'Created', '2025-12-09 15:44:27', '2025-12-09 15:44:27', 'Road'),
(3, 'TRK-9DF4986655', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 4, NULL, 'Standard', 'parcel', 0.000, NULL, NULL, NULL, NULL, 0.00, 'Created', '2025-12-09 15:44:38', '2025-12-09 15:44:38', 'Road');

-- --------------------------------------------------------

--
-- Table structure for table `shipment_items`
--

CREATE TABLE `shipment_items` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `description` varchar(700) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `value_usd` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipment_items`
--

INSERT INTO `shipment_items` (`id`, `shipment_id`, `description`, `quantity`, `weight`, `value_usd`) VALUES
(1, 1, '2 pc pant, 2pc trowser, 7 pc shirt', 11, 2.000, 100.00),
(2, 3, '\'j', 1, NULL, 0.00),
(3, 3, 'dsfsef', 1, NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `shipment_status_log`
--

CREATE TABLE `shipment_status_log` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipment_status_log`
--

INSERT INTO `shipment_status_log` (`id`, `shipment_id`, `status`, `branch_id`, `user_id`, `note`, `timestamp`) VALUES
(1, 1, 'Processing', 1, 2, 'Shipment Under Processing', '2025-12-09 12:21:43');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `rate` decimal(5,2) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `rate`, `country_id`) VALUES
(1, 'Deshneta', 1.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `branch_id`, `created_at`, `updated_at`, `profile_image`) VALUES
(1, 'tareq', 'engineertareqbd@gmail.com', '0d20b93812a60f072cbcf2ac64b271a6', 1, '2025-12-07 20:09:09', '2025-12-07 20:09:09', 'default.png'),
(2, 'Hasib', 'absc1@gmail.com', '0d20b93812a60f072cbcf2ac64b271a6', 1, '2025-12-09 12:13:25', '2025-12-09 12:13:25', 'default.png'),
(3, 'Hasib', 'absc2@gmail.com', '0d20b93812a60f072cbcf2ac64b271a6', 1, '2025-12-09 12:13:53', '2025-12-09 12:13:53', 'default.png'),
(4, 'Hasib', 'absc3@gmail.com', '0d20b93812a60f072cbcf2ac64b271a6', 1, '2025-12-09 12:13:53', '2025-12-09 12:13:53', 'default.png'),
(5, 'Hasib', 'absc4@gmail.com', '0d20b93812a60f072cbcf2ac64b271a6', 1, '2025-12-09 12:13:53', '2025-12-09 12:13:53', 'default.png'),
(6, 'Karimul', 'karimul@gmail.com', '$2y$10$pizX4wpTQNts9dpY.C5HIOqmDyK4Z7Oa/dvcB1dBdMHV/HgQjh57q', 1, '2025-12-09 12:34:11', '2025-12-09 12:34:11', 'default.png'),
(8, 'fhtr', 'karimuggl@gmail.com', '$2y$10$ehS8D/gyQvXlTRDHBBfGVuaz1kZ3IJcNRwbpfRonykCB15NmbBNF6', 1, '2025-12-09 15:14:21', '2025-12-09 15:14:21', 'default.png'),
(10, 'yytk', 'kafhrdfrimula@gmail.com', '$2y$10$rFnv3A8RjzSBkQh5r5Xqi.rp5Ivwvhr807KnxSqgS30SmEe10sL5G', 1, '2025-12-09 15:16:25', '2025-12-09 15:16:25', 'default.png'),
(11, 'tjrt', 'dgdth@gmail.com', '$2y$10$maQpIvSwHjG6jcp8R98gTO5YtSq1SlNfDc3B9oxr2YoRSQ5luwcXi', 1, '2025-12-09 15:18:20', '2025-12-09 15:18:20', 'default.png'),
(12, 'gjgyhm', 'kaghnxtrgrimula@gmail.com', '$2y$10$RbiJtTDGQOeiiH86sUySL.8vT5pFW2bIiz/jmilx3AC3WX5dTv1Ta', 4, '2025-12-09 15:30:02', '2025-12-09 15:30:02', 'default.png'),
(13, 'fegslurgfyel', 'kafhrdfrirhystrhmula@gmail.com', '$2y$10$0KABaSGjUMv2YAMsnl1ULuLSwRYvumvZXmseRlq9HN5FLq9C4TpeC', 2, '2025-12-09 15:36:16', '2025-12-09 15:36:16', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `created_at`) VALUES
(8, 1, '2025-12-09 15:14:21'),
(10, 1, '2025-12-09 15:16:25'),
(11, 1, '2025-12-09 15:18:21'),
(12, 1, '2025-12-09 15:30:02'),
(13, 1, '2025-12-09 15:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `zone_countries`
--

CREATE TABLE `zone_countries` (
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zone_countries`
--

INSERT INTO `zone_countries` (`zone_id`, `country_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 12),
(4, 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rider_id` (`rider_id`),
  ADD KEY `shipment_id` (`shipment_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `carriers`
--
ALTER TABLE `carriers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cod_orders`
--
ALTER TABLE `cod_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shipment_id` (`shipment_id`),
  ADD KEY `collected_by` (`collected_by`);

--
-- Indexes for table `cod_settlements`
--
ALTER TABLE `cod_settlements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cod_id` (`cod_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `iso_code` (`iso_code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `customs_documents`
--
ALTER TABLE `customs_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipment_id` (`shipment_id`),
  ADD KEY `origin_country_id` (`origin_country_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `shipment_id` (`shipment_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shipment_id` (`shipment_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_ref` (`transaction_ref`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `rate_slabs`
--
ALTER TABLE `rate_slabs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `rate_zones`
--
ALTER TABLE `rate_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riders`
--
ALTER TABLE `riders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_no` (`tracking_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `sender_country_id` (`sender_country_id`),
  ADD KEY `receiver_country_id` (`receiver_country_id`),
  ADD KEY `origin_branch_id` (`origin_branch_id`),
  ADD KEY `destination_branch_id` (`destination_branch_id`),
  ADD KEY `carrier_id` (`carrier_id`);

--
-- Indexes for table `shipment_items`
--
ALTER TABLE `shipment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipment_id` (`shipment_id`);

--
-- Indexes for table `shipment_status_log`
--
ALTER TABLE `shipment_status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipment_id` (`shipment_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `zone_countries`
--
ALTER TABLE `zone_countries`
  ADD PRIMARY KEY (`zone_id`,`country_id`),
  ADD KEY `country_id` (`country_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `carriers`
--
ALTER TABLE `carriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cod_orders`
--
ALTER TABLE `cod_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cod_settlements`
--
ALTER TABLE `cod_settlements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customs_documents`
--
ALTER TABLE `customs_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rate_slabs`
--
ALTER TABLE `rate_slabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rate_zones`
--
ALTER TABLE `rate_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `riders`
--
ALTER TABLE `riders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shipment_items`
--
ALTER TABLE `shipment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shipment_status_log`
--
ALTER TABLE `shipment_status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`rider_id`) REFERENCES `riders` (`id`),
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`);

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `cod_orders`
--
ALTER TABLE `cod_orders`
  ADD CONSTRAINT `cod_orders_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`),
  ADD CONSTRAINT `cod_orders_ibfk_2` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `cod_settlements`
--
ALTER TABLE `cod_settlements`
  ADD CONSTRAINT `cod_settlements_ibfk_1` FOREIGN KEY (`cod_id`) REFERENCES `cod_orders` (`id`),
  ADD CONSTRAINT `cod_settlements_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `customs_documents`
--
ALTER TABLE `customs_documents`
  ADD CONSTRAINT `customs_documents_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customs_documents_ibfk_2` FOREIGN KEY (`origin_country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints for table `rate_slabs`
--
ALTER TABLE `rate_slabs`
  ADD CONSTRAINT `rate_slabs_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `rate_zones` (`id`);

--
-- Constraints for table `riders`
--
ALTER TABLE `riders`
  ADD CONSTRAINT `riders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `riders_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `shipments_ibfk_2` FOREIGN KEY (`sender_country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `shipments_ibfk_3` FOREIGN KEY (`receiver_country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `shipments_ibfk_4` FOREIGN KEY (`origin_branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `shipments_ibfk_5` FOREIGN KEY (`destination_branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `shipments_ibfk_6` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`);

--
-- Constraints for table `shipment_items`
--
ALTER TABLE `shipment_items`
  ADD CONSTRAINT `shipment_items_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipment_status_log`
--
ALTER TABLE `shipment_status_log`
  ADD CONSTRAINT `shipment_status_log_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipment_status_log_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `shipment_status_log_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `taxes`
--
ALTER TABLE `taxes`
  ADD CONSTRAINT `taxes_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zone_countries`
--
ALTER TABLE `zone_countries`
  ADD CONSTRAINT `zone_countries_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `rate_zones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `zone_countries_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
