-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 08:57 AM
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
-- Database: `quickgrilldb2`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `orderTime` datetime DEFAULT current_timestamp(),
  `total_price` int(11) NOT NULL,
  `status` enum('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
  `order_type` enum('Dine In','Take Away') NOT NULL DEFAULT 'Dine In'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `orderTime`, `total_price`, `status`, `order_type`) VALUES
(1, 2, '2025-01-17 13:34:47', 0, 'Completed', 'Dine In'),
(2, 2, '2025-01-17 13:37:49', 0, 'Completed', 'Dine In'),
(3, 2, '2025-01-17 13:40:24', 72, 'Completed', 'Dine In'),
(4, 2, '2025-01-17 13:46:09', 72, 'Completed', 'Dine In'),
(5, 1, '2025-01-17 14:42:15', 34, 'Cancelled', 'Dine In'),
(6, 1, '2025-01-17 14:59:27', 40, 'Completed', 'Dine In'),
(7, 1, '2025-01-17 17:29:16', 140, 'Completed', 'Dine In'),
(8, 1, '2025-01-17 17:56:57', 20, 'Cancelled', 'Dine In'),
(9, 1, '2025-01-17 18:02:05', 34, 'Pending', 'Take Away'),
(10, 1, '2025-01-17 18:04:14', 34, 'Pending', 'Take Away'),
(11, 1, '2025-01-17 18:06:33', 68, 'Pending', 'Dine In'),
(12, 1, '2025-01-17 18:07:50', 34, 'Pending', 'Dine In'),
(13, 1, '2025-01-17 18:25:29', 52, 'Pending', 'Take Away'),
(14, 3, '2025-01-17 19:08:19', 34, 'Pending', 'Take Away'),
(15, 3, '2025-01-17 19:08:59', 32, 'Pending', 'Take Away'),
(16, 3, '2025-01-17 19:09:12', 20, 'Pending', 'Take Away'),
(17, 3, '2025-01-17 19:09:27', 40, 'Pending', 'Take Away'),
(18, 3, '2025-01-17 19:11:23', 20, 'Pending', 'Take Away'),
(19, 3, '2025-01-17 19:14:30', 32, 'Pending', 'Take Away'),
(20, 3, '2025-01-17 19:14:52', 32, 'Pending', 'Dine In'),
(21, 3, '2025-01-17 19:15:08', 32, 'Pending', 'Take Away'),
(22, 3, '2025-01-17 19:17:24', 32, 'Pending', 'Take Away'),
(23, 3, '2025-01-17 19:18:05', 32, 'Pending', 'Take Away'),
(24, 3, '2025-01-17 19:18:54', 20, 'Pending', 'Take Away'),
(25, 1, '2025-01-17 19:37:32', 40, 'Pending', 'Dine In'),
(26, 999, '2025-01-22 01:16:06', 64, 'Completed', 'Dine In'),
(27, 999, '2025-01-22 12:23:15', 32, 'Pending', 'Dine In'),
(28, 999, '2025-01-22 12:31:39', 34, 'Completed', 'Take Away'),
(29, 1007, '2025-01-22 12:45:20', 96, 'Completed', 'Dine In'),
(30, 999, '2025-01-22 13:19:47', 32, 'Completed', 'Take Away'),
(31, 1008, '2025-01-22 13:47:04', 136, 'Completed', 'Dine In'),
(32, 4, '2025-01-23 15:51:19', 40, 'Pending', 'Dine In');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int(11) NOT NULL,
  `stall_menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_id`, `stall_menu_id`, `quantity`, `total_amount`, `created_at`) VALUES
(1, 34, 2, 40.00, '2025-01-17 05:34:47'),
(1, 35, 1, 32.00, '2025-01-17 05:34:47'),
(2, 34, 2, 40.00, '2025-01-17 05:37:49'),
(2, 35, 1, 32.00, '2025-01-17 05:37:49'),
(3, 34, 2, 40.00, '2025-01-17 05:40:24'),
(3, 35, 1, 32.00, '2025-01-17 05:40:24'),
(4, 34, 2, 40.00, '2025-01-17 05:46:09'),
(4, 35, 1, 32.00, '2025-01-17 05:46:09'),
(5, 36, 1, 34.00, '2025-01-17 06:42:15'),
(6, 34, 2, 40.00, '2025-01-17 06:59:27'),
(7, 34, 2, 40.00, '2025-01-17 09:29:16'),
(7, 35, 1, 32.00, '2025-01-17 09:29:16'),
(7, 36, 2, 68.00, '2025-01-17 09:29:16'),
(8, 34, 1, 20.00, '2025-01-17 09:56:57'),
(9, 36, 1, 34.00, '2025-01-17 10:02:05'),
(10, 36, 1, 34.00, '2025-01-17 10:04:14'),
(11, 36, 2, 68.00, '2025-01-17 10:06:33'),
(12, 36, 1, 34.00, '2025-01-17 10:07:50'),
(13, 34, 1, 20.00, '2025-01-17 10:25:29'),
(13, 35, 1, 32.00, '2025-01-17 10:25:29'),
(14, 36, 1, 34.00, '2025-01-17 11:08:19'),
(15, 35, 1, 32.00, '2025-01-17 11:08:59'),
(16, 34, 1, 20.00, '2025-01-17 11:09:12'),
(17, 34, 2, 40.00, '2025-01-17 11:09:27'),
(18, 34, 1, 20.00, '2025-01-17 11:11:23'),
(19, 35, 1, 32.00, '2025-01-17 11:14:30'),
(20, 35, 1, 32.00, '2025-01-17 11:14:52'),
(21, 35, 1, 32.00, '2025-01-17 11:15:08'),
(22, 35, 1, 32.00, '2025-01-17 11:17:24'),
(23, 35, 1, 32.00, '2025-01-17 11:18:05'),
(24, 34, 1, 20.00, '2025-01-17 11:18:54'),
(25, 34, 2, 40.00, '2025-01-17 11:37:32'),
(26, 38, 2, 64.00, '2025-01-21 17:16:06'),
(27, 35, 1, 32.00, '2025-01-22 04:23:15'),
(28, 40, 1, 16.00, '2025-01-22 04:31:39'),
(28, 41, 1, 18.00, '2025-01-22 04:31:39'),
(29, 40, 6, 96.00, '2025-01-22 04:45:20'),
(30, 40, 2, 32.00, '2025-01-22 05:19:47'),
(31, 38, 3, 96.00, '2025-01-22 05:47:04'),
(31, 39, 1, 40.00, '2025-01-22 05:47:04'),
(32, 39, 1, 40.00, '2025-01-23 07:51:19');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `paymentMethod` varchar(50) NOT NULL,
  `paymentStatus` enum('pending','completed','failed') DEFAULT 'pending',
  `amountPaid` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `paymentMethod`, `paymentStatus`, `amountPaid`) VALUES
(1, 1, 'credit_card', 'completed', 72.00),
(2, 2, 'credit_card', 'completed', 72.00),
(3, 4, 'credit_card', 'completed', 72.00),
(4, 5, 'paypal', 'completed', 34.00),
(5, 6, 'credit_card', 'completed', 40.00),
(6, 7, 'credit_card', 'completed', 140.00),
(7, 8, 'credit_card', 'completed', 20.00),
(8, 9, 'credit_card', 'completed', 34.00),
(9, 10, 'credit_card', 'completed', 34.00),
(10, 11, 'credit_card', 'completed', 68.00),
(11, 12, 'credit_card', 'completed', 34.00),
(12, 13, 'credit_card', 'completed', 52.00),
(13, 14, 'credit_card', 'completed', 34.00),
(14, 15, 'credit_card', 'completed', 32.00),
(15, 16, 'credit_card', 'completed', 20.00),
(16, 17, 'credit_card', 'completed', 40.00),
(17, 18, 'credit_card', 'completed', 20.00),
(18, 19, 'credit_card', 'completed', 32.00),
(19, 20, 'credit_card', 'completed', 32.00),
(20, 21, 'credit_card', 'completed', 32.00),
(21, 22, 'credit_card', 'completed', 32.00),
(22, 23, 'credit_card', 'completed', 32.00),
(23, 24, 'credit_card', 'completed', 20.00),
(24, 25, 'credit_card', 'completed', 40.00),
(25, 26, 'credit_card', 'completed', 64.00),
(26, 27, 'paypal', 'completed', 32.00),
(27, 28, 'bank_transfer', 'completed', 34.00),
(28, 29, 'bank_transfer', 'completed', 96.00),
(29, 30, 'credit_card', 'completed', 32.00),
(30, 31, 'credit_card', 'completed', 136.00),
(31, 32, 'credit_card', 'completed', 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `stalls`
--

CREATE TABLE `stalls` (
  `stall_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `review_rating` decimal(2,1) DEFAULT 0.0,
  `price_range` varchar(50) DEFAULT NULL,
  `permit_number` varchar(20) NOT NULL,
  `permit_document` varchar(255) NOT NULL,
  `additional_document` varchar(255) NOT NULL,
  `operating_hours` varchar(100) NOT NULL DEFAULT '9:00 AM - 6:00 PM',
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stalls`
--

INSERT INTO `stalls` (`stall_id`, `name`, `description`, `image_url`, `address`, `review_rating`, `price_range`, `permit_number`, `permit_document`, `additional_document`, `operating_hours`, `contact_email`, `contact_phone`, `is_approved`, `user_id`) VALUES
(8, 'BurgerShot', 'The Best Burgers', 'uploads/img_6789bec3d4bba.jpg', 'I-City, Multimedia', 0.0, '20', '', '', '', '9:00 AM - 6:00 PM', '', '', 1, 1),
(9, 'CluckenBell', 'The tastiest chicken', 'uploads/img_6789fb62bdceb_6789bc9ee8856_ramly.jpg', 'I-City, Multimedia', 0.0, '20-30', '', '', '', '9:00 AM - 6:00 PM', '', '', 1, 2),
(13, 'Burger Best', 'The best burgers in the city', 'uploads/file_678fb031b6f4b_QuickGrill_Stall.jpg', 'I-City, Multimedia', 0.0, '20-30', '202222', 'uploads/file_678fb031b71b1_Stall_Licence.pdf', 'uploads/file_678fb031b74e4_Stall_Licence.pdf', '9:00 AM - 10:00AM', 'random@gmail.com', '01112141807', 1, 3),
(21, 'Hottest Foods', 'The hottest foof on the West', 'uploads/file_678fd53ca7b28_Stall8.jpg', 'I-City, Multimedia', 0.0, '20-40', '202222', 'uploads/file_678fd53ca7d6d_Stall_Licence.pdf', 'uploads/file_678fd53ca7f1d_Stall_Licence.pdf', '9:00 AM - 10:00AM', 'random@gmail.com', '01112141807', 1, 1003),
(22, 'Spicy Joe', 'Very spicy food for real Cowboys', 'uploads/file_678fd59906f00_burger2.jpg', 'I-City, Multimedia', 0.0, '10-20', '202222', 'uploads/file_678fd5990713c_Stall_Licence.pdf', 'uploads/file_678fd59907316_Stall_Licence.pdf', '10:00 AM - 12:00 AM', 'random@gmail.com', '01112141807', 1, 1004),
(24, 'XAUUSD burger', 'Come eat here with traders', 'uploads/file_679078fe89660_Stall2.jpg', 'shah alam', 0.0, '1-100', '202333', 'uploads/file_679078fe89c31_Stall_Licence.pdf', 'uploads/file_679078fe8a0be_home-burger.png', '9:00 am- 4:00 pm', 'random@gmail.com', '01112141807', 1, 1007);

-- --------------------------------------------------------

--
-- Table structure for table `stall_menus`
--

CREATE TABLE `stall_menus` (
  `id` int(11) NOT NULL,
  `stall_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stall_menus`
--

INSERT INTO `stall_menus` (`id`, `stall_id`, `name`, `description`, `price`, `image_url`) VALUES
(34, 8, 'Burger', 'Spicy', 20.00, 'uploads/6789d9b4cf38a_6789c1fac104c_home-burger.png'),
(35, 8, 'BigBurger', 'Spiciest', 32.00, 'uploads/6789d9ea01260_6789c1fac104c_home-burger.png'),
(36, 9, 'Double Burger', 'Double meat', 34.00, 'uploads/6789fb9a9d013_6789d70dad036_home-burger.png'),
(38, 21, 'Double Spicy', 'Very tasty', 32.00, 'uploads/678fd5dc8ceaa_burger1.jpg'),
(39, 21, 'New Choice Burger', 'The very new', 40.00, 'uploads/678fd5fd41a65_burger4.jpg'),
(40, 22, 'Meet', 'Very Famous', 16.00, 'uploads/678fd61bd2d2d_burger2.jpg'),
(41, 22, 'Beef', 'Delicious', 18.00, 'uploads/678fd6315d3aa_burger3.jpg'),
(42, 21, 'Triple Burger', 'Very tasty', 100.00, 'uploads/679076057abff_burger4.jpg'),
(43, 21, 'burger', 'ds', 12.00, 'uploads/679086bf250f2_home-burger.png'),
(45, 24, 'Burger', 'Tasyty', 12.00, 'uploads/6791f54ab7339_home-burger.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','vendor','admin') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Alexandr', 'alexandr@gmail.com', '01112141807', '$2y$10$44QG1KNY7Jt9G46CfOKRI..cIc3l3KO.m5QN9NgfEwsMpi1Y9dgPi', 'vendor', '2025-01-16 12:59:40'),
(2, 'User', 'user@gmail.com', '01112141807', '$2y$10$84p/Fq5ecvb93hXWSdr7dOJZbOh8lo5vEtNnseJsAiY/CBSBBOtAi', 'vendor', '2025-01-16 15:30:53'),
(3, 'User2', 'user2@gmail.com', '01112141807', '$2y$10$hSi/VTxXqmbQ6GXJRUMMi.PieGqkAYK8OBy8b8WVqY9HH3j0CPmWO', 'vendor', '2025-01-17 10:50:39'),
(4, 'User3', 'User3@gmail.com', '1234567890', '$2y$10$GZMaKqP4t/LqDcutPSI59OPJb/yoA9nZjo.CoRpLmVHFAs.HaGwPa', 'customer', '2025-01-21 14:46:39'),
(999, 'Admin', 'Admin@gmail.com', '01112141807', '$2y$10$6r.q6CebeD3HzWmNHtCAEeqbboM9XZTNVyMa9q3aI2i2udpsvq0G2', 'admin', '2025-01-21 14:25:14'),
(1003, 'Rick', 'rick@gmail.com', '1234567890', '$2y$10$PfvblaSVDrJH87uYDrsyCOWfXLPxvdnD79XSs7k5hPcuostjhGBEq', 'vendor', '2025-01-21 17:09:55'),
(1004, 'Daryl', 'daryl@gmail.com', '1234567890', '$2y$10$LvpMLKd/ht/FMAo667xVpejEoGiXtDxeoIMjP3gt6K/TLLu563Uki', 'vendor', '2025-01-21 17:10:11'),
(1005, 'test', 'test@gmail.com', '01112141807', '$2y$10$Ik16ZwDROn4rorKMsx9bl.n0KksSGKXITaDoDE56.T6SHeNWOV45a', 'customer', '2025-01-22 04:27:38'),
(1006, 'asyrafadmin', 'asyraf123@gmail.com', '0123456789', '$2y$10$8ja2MqF0pnl94Vafk1ViEuhp2C4cgfJ7ri65zwMCBJ6wKjI8Doakq', 'admin', '2025-01-22 04:34:48'),
(1007, 'asyrafuser', 'asyrafuser@gmail.com', '0123456789', '$2y$10$sBZA7kj2R4Rx9dDkn059B.TUGEO/qOU.K99Fke15jP6rJ6NMV2Hr2', 'vendor', '2025-01-22 04:43:58'),
(1008, 'test2', 'test2@gmail.com', '01112141807', '$2y$10$.y7xZdRI/GQLxeUPNldiL.E6PLasHi73i89sxtyl8iWJzenAr4A.i', 'customer', '2025-01-22 05:44:49'),
(1009, 'Admin2', 'Admin2@gmail.com', '01112141807', '$2y$10$jj28dlPCSf3euqsh8AIXd.NrmQxmGtVT9WxQTNN/cUv.cuSE0BWzu', 'admin', '2025-01-22 06:03:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`stall_menu_id`),
  ADD KEY `fk_order_details_menu` (`stall_menu_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_order` (`order_id`);

--
-- Indexes for table `stalls`
--
ALTER TABLE `stalls`
  ADD PRIMARY KEY (`stall_id`),
  ADD KEY `fk_stalls_user` (`user_id`);

--
-- Indexes for table `stall_menus`
--
ALTER TABLE `stall_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_stall` (`stall_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `stalls`
--
ALTER TABLE `stalls`
  MODIFY `stall_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `stall_menus`
--
ALTER TABLE `stall_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1010;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_details_menu` FOREIGN KEY (`stall_menu_id`) REFERENCES `stall_menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stalls`
--
ALTER TABLE `stalls`
  ADD CONSTRAINT `fk_stalls_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `stall_menus`
--
ALTER TABLE `stall_menus`
  ADD CONSTRAINT `fk_menu_stall` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`stall_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
