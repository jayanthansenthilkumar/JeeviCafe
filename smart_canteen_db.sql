-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 06:31 AM
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
-- Database: `smart_canteen_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `order_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 2, 2, 5, 'AA', '2026-04-08 09:06:34');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `diet_type` varchar(20) DEFAULT 'Veg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `food_name`, `price`, `is_available`, `diet_type`) VALUES
(1, 'Burger', 4.50, 1, 'Veg'),
(2, 'Pizza', 6.00, 1, 'Veg'),
(3, 'Pasta', 5.50, 1, 'Veg'),
(4, 'Coffee', 2.00, 1, 'Veg'),
(5, 'Rava Salad', 6.50, 1, 'Veg');

-- --------------------------------------------------------

--
-- Table structure for table `menu_polls`
--

CREATE TABLE `menu_polls` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `votes` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_polls`
--

INSERT INTO `menu_polls` (`id`, `item_name`, `votes`, `is_active`) VALUES
(1, 'Rava Salad', 0, 0),
(2, 'Salad', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT 0,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `is_rated` tinyint(1) DEFAULT 0,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `pickup_time` varchar(50) DEFAULT 'ASAP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `food_name`, `quantity`, `order_date`, `user_id`, `status`, `is_rated`, `total_price`, `pickup_time`) VALUES
(1, 'Pizza', 5, '2026-04-08 08:38:42', 0, 'completed', 0, 0.00, 'ASAP'),
(2, 'Pasta', 2, '2026-04-08 08:48:48', 2, 'completed', 1, 0.00, 'ASAP'),
(3, 'Coffee', 1, '2026-04-08 09:05:38', 2, 'cancelled', 0, 2.00, 'ASAP'),
(4, 'Rava Salad', 1, '2026-04-08 09:07:00', 2, 'pending', 0, 6.50, '11:30 AM'),
(5, 'Burger', 1, '2026-04-08 09:12:04', 2, 'cancelled', 0, 4.50, 'ASAP'),
(6, 'Pasta', 1, '2026-04-08 09:25:43', 3, 'pending', 0, 5.50, 'ASAP'),
(7, 'Pizza', 1, '2026-04-08 09:32:07', 2, 'pending', 0, 6.00, 'ASAP'),
(8, 'Burger', 1, '2026-04-08 09:32:18', 2, 'pending', 0, 4.50, 'ASAP'),
(9, 'Burger', 1, '2026-04-08 09:57:50', 2, 'pending', 0, 4.50, 'ASAP');

-- --------------------------------------------------------

--
-- Table structure for table `predictions`
--

CREATE TABLE `predictions` (
  `id` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `predicted_qty` float NOT NULL,
  `prediction_date` datetime DEFAULT current_timestamp(),
  `peak_hour` varchar(50) DEFAULT 'Not enough data',
  `demand_category` varchar(20) DEFAULT 'Normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `predictions`
--

INSERT INTO `predictions` (`id`, `food_name`, `predicted_qty`, `prediction_date`, `peak_hour`, `demand_category`) VALUES
(1, 'Burger', 15, '2026-04-08 09:16:08', '9:00 to 10:00', 'Medium Demand'),
(2, 'Coffee', 15, '2026-04-08 09:16:08', '9:00 to 10:00', 'Medium Demand'),
(3, 'Pasta', 30, '2026-04-08 09:16:08', '8:00 to 9:00', 'High Demand'),
(4, 'Pizza', 75, '2026-04-08 09:16:08', '8:00 to 9:00', 'High Demand'),
(5, 'Rava Salad', 15, '2026-04-08 09:16:08', '9:00 to 10:00', 'Medium Demand');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','staff') NOT NULL DEFAULT 'user',
  `wallet` decimal(10,2) DEFAULT 50.00,
  `loyalty_points` int(11) DEFAULT 0,
  `register_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `wallet`, `loyalty_points`, `register_number`) VALUES
(1, 'admin', 'admin123', 'admin', 50.00, 0, NULL),
(2, 'student', 'student1', 'user', 143.50, 295, NULL),
(3, 'staff', 'staff123', 'staff', 44.50, 55, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_votes`
--

CREATE TABLE `user_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_votes`
--

INSERT INTO `user_votes` (`id`, `user_id`, `poll_id`) VALUES
(1, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_vouchers`
--

INSERT INTO `user_vouchers` (`id`, `user_id`, `voucher_id`) VALUES
(1, 2, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `max_uses` int(11) DEFAULT 1,
  `current_uses` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `target_role` enum('user','staff') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount_value`, `max_uses`, `current_uses`, `is_active`, `target_role`) VALUES
(1, 'FIRST10', 10.00, 50, 1, 1, 'user'),
(2, 'MEGA50', 5.00, 5, 1, 1, 'user'),
(3, 'STAFFNEW', 50.00, 100, 0, 0, 'user'),
(6, 'STAFFNEW50', 50.00, 100, 0, 1, 'staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_polls`
--
ALTER TABLE `menu_polls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `predictions`
--
ALTER TABLE `predictions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `register_number` (`register_number`);

--
-- Indexes for table `user_votes`
--
ALTER TABLE `user_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_polls`
--
ALTER TABLE `menu_polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `predictions`
--
ALTER TABLE `predictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_votes`
--
ALTER TABLE `user_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
