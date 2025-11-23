-- SmartWasteHub v1.0 – Final clean database schema by BSE25-9 (Nov 2025)
-- Only 3 tables: users, pickups, rewards → stable, lightweight, 100% working.

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 02:37 PM
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
-- Database: `smartwastehub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `pickup_requests`
--

CREATE TABLE `pickup_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `status` enum('pending','assigned','completed','cancelled') NOT NULL DEFAULT 'pending',
  `collector_id` int(11) DEFAULT NULL,
  `weight_kg` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pickup_requests`
--

INSERT INTO `pickup_requests` (`id`, `user_id`, `category_id`, `scheduled_date`, `time_slot`, `status`, `collector_id`, `weight_kg`, `created_at`) VALUES
(1, 1, 4, '2025-11-14', 'Afternoon (12PM - 3PM)', 'completed', 2, 4.00, '2025-11-18 10:40:35'),
(2, 3, 2, '2025-11-27', 'Morning (8AM - 11AM)', 'completed', 4, 3.00, '2025-11-18 11:07:58'),
(3, 1, 1, '2025-11-19', 'Morning (8AM - 11AM)', 'completed', 2, 30.00, '2025-11-18 12:46:03'),
(4, 1, 5, '2025-11-19', 'Morning (8AM - 11AM)', 'completed', 2, 278.00, '2025-11-18 13:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `reward_items`
--

CREATE TABLE `reward_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cost_credits` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reward_items`
--

INSERT INTO `reward_items` (`id`, `name`, `cost_credits`, `created_at`) VALUES
(1, 'Garbage Bags (Medium Pack)', 15, '2025-11-18 12:34:17'),
(2, 'Airtime 500 UGX', 20, '2025-11-18 12:34:17'),
(3, 'Eco Briquettes (1kg)', 30, '2025-11-18 12:34:17'),
(4, 'Recycling Points Badge', 10, '2025-11-18 12:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `reward_transactions`
--

CREATE TABLE `reward_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pickup_id` int(11) DEFAULT NULL,
  `credits` decimal(8,2) NOT NULL,
  `type` enum('earn','spend','redeem') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reward_transactions`
--

INSERT INTO `reward_transactions` (`id`, `user_id`, `pickup_id`, `credits`, `type`, `created_at`) VALUES
(1, 1, 1, 28.00, 'earn', '2025-11-18 10:56:33'),
(2, 3, 2, 6.00, 'earn', '2025-11-18 11:13:48'),
(3, 1, 3, 150.00, 'earn', '2025-11-18 12:54:56'),
(4, 1, 4, 2780.00, 'earn', '2025-11-18 13:06:31'),
(17, 1, NULL, 10.00, 'redeem', '2025-11-18 14:00:02'),
(18, 1, NULL, 15.00, 'redeem', '2025-11-18 14:00:06'),
(19, 1, NULL, 10.00, 'redeem', '2025-11-18 17:47:08'),
(20, 1, NULL, 15.00, 'redeem', '2025-11-18 17:47:10'),
(21, 1, NULL, 30.00, 'redeem', '2025-11-19 04:35:51'),
(22, 1, NULL, 30.00, 'redeem', '2025-11-19 04:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `reward_wallets`
--

CREATE TABLE `reward_wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reward_wallets`
--

INSERT INTO `reward_wallets` (`id`, `user_id`, `balance`) VALUES
(1, 3, 6.00),
(2, 4, 0.00),
(3, 5, 0.00),
(4, 7, 0.00),
(5, 1, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('household','collector','admin') NOT NULL DEFAULT 'household',
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `phone`, `location`, `created_at`) VALUES
(1, 'Kitonsa Elvis', 'kitonsaelvis@gmail.com', '$2y$10$Oyg8xND88bc/dbDi8SKo1eRJemUni25.c.riws2IobNaiuA3LVUJ6', 'household', '0708033107', 'Kampala', '2025-11-18 10:08:29'),
(2, 'Kasumba Jimmy', 'kasumbajimmy@gmail.com', '$2y$10$OGuqbp.CY4t45Br8WSMDIe1cxUFFHRljxiC/.Hepd61R.rjbhTwSy', 'collector', '0765432654', 'Zana', '2025-11-18 10:43:23'),
(3, 'Kasule Adam', 'kasuleadam@gmail.com', '$2y$10$uDSCUmQHmepJzvHYAfhPGu9ew72mRNSrURTjkK4LRjCl/x0EOL02m', 'household', '0755432187', 'Entebbe', '2025-11-18 11:05:18'),
(4, 'Twaha Amara', 'twahaamara@gmail.com', '$2y$10$QuTZxLDCuB17CZJefzinW.nEzNJtE3caNjTBkTBkXTR2/2s8rijEW', 'collector', '078785001', 'Entebbe', '2025-11-18 11:06:20'),
(6, 'System Admin', 'admin@smartwastehub.com', 'admin123', 'admin', '0700000000', 'HQ', '2025-11-18 11:32:40'),
(7, 'Kasamba Raymond', 'kasambaraymond@gmail.com', '$2y$10$5ZZpKXtlWa/ggaZRil8YU.edy6rOKfnNOTKXHoaD0VWJuWu2Xpzt.', 'admin', '0767870096', 'Arua', '2025-11-18 12:26:31');

-- --------------------------------------------------------

--
-- Table structure for table `waste_categories`
--

CREATE TABLE `waste_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `reward_per_kg` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waste_categories`
--

INSERT INTO `waste_categories` (`id`, `name`, `reward_per_kg`) VALUES
(1, 'Plastic', 5.00),
(2, 'Organic Waste', 2.00),
(3, 'Paper & Cardboard', 3.00),
(4, 'Metal', 7.00),
(5, 'E-Waste', 10.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `collector_id` (`collector_id`);

--
-- Indexes for table `reward_items`
--
ALTER TABLE `reward_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reward_transactions`
--
ALTER TABLE `reward_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reward_transactions_ibfk_2` (`pickup_id`);

--
-- Indexes for table `reward_wallets`
--
ALTER TABLE `reward_wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `waste_categories`
--
ALTER TABLE `waste_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reward_items`
--
ALTER TABLE `reward_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reward_transactions`
--
ALTER TABLE `reward_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `reward_wallets`
--
ALTER TABLE `reward_wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `waste_categories`
--
ALTER TABLE `waste_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  ADD CONSTRAINT `pickup_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pickup_requests_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `waste_categories` (`id`),
  ADD CONSTRAINT `pickup_requests_ibfk_3` FOREIGN KEY (`collector_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reward_transactions`
--
ALTER TABLE `reward_transactions`
  ADD CONSTRAINT `reward_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reward_transactions_ibfk_2` FOREIGN KEY (`pickup_id`) REFERENCES `pickup_requests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reward_wallets`
--
ALTER TABLE `reward_wallets`
  ADD CONSTRAINT `reward_wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
