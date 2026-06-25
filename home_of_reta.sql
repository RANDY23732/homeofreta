-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 07:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `home_of_reta`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `session_id`, `product_id`, `product_name`, `product_price`, `quantity`, `created_at`) VALUES
(56, 4, NULL, 1, NULL, NULL, 1, '2026-06-08 15:32:54'),
(57, 5, NULL, 2, NULL, NULL, 1, '2026-06-08 16:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `items` text DEFAULT NULL,
  `last_message` text DEFAULT NULL,
  `unread_count` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender` varchar(50) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT 'text',
  `content` text DEFAULT NULL,
  `file_data` text DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `reply_to_sender` varchar(50) DEFAULT NULL,
  `reply_to_text` text DEFAULT NULL,
  `visible_to` text DEFAULT NULL,
  `deleted_for_me` text DEFAULT NULL,
  `deleted_for_everyone` tinyint(1) DEFAULT 0,
  `deleted_for_everyone_at` timestamp NULL DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_notes`
--

CREATE TABLE `message_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pinned_messages`
--

CREATE TABLE `pinned_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `sender` varchar(50) DEFAULT NULL,
  `pinned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `rating` decimal(3,1) DEFAULT 4.5,
  `description` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `usage_instructions` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `bestseller` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `old_price`, `stock`, `rating`, `description`, `benefits`, `usage_instructions`, `image_url`, `bestseller`, `created_at`) VALUES
(1, 'RETA Mass 1500', 'Weight Gain', 69.99, 94.99, 45, 4.8, '1500 calories per serving for extreme mass gain', 'Rapid weight gain, muscle fullness', '2 scoops with milk or water post-workout', 'WhatsApp Image 2026-04-28 at 21.33.13 (1).jpeg', 1, '2026-06-06 01:14:52'),
(2, 'Serious Mass Gainer', 'Weight Gain', 59.99, 79.99, 32, 4.7, '1250 calories, 50g protein per serving', 'Lean mass gain, recovery support', '2 scoops daily between meals', 'Serious Mass Gainer.jpg', 1, '2026-06-06 01:14:52'),
(3, 'True Mass Heavyweight', 'Weight Gain', 64.99, 84.99, 28, 4.6, '700 calories, muscle builder formula', 'Clean bulk, steady gains', '1-2 scoops post-workout', 'True Mass Heavyweight.jfif', 0, '2026-06-06 01:14:52'),
(4, 'Pro Gainer Complex', 'Weight Gain', 72.99, 99.99, 35, 4.7, 'High protein mass gainer with digestive enzymes', 'Easy digestion, quality mass', '1 scoop with breakfast', 'Pro Gainer Complex.jpg', 0, '2026-06-06 01:14:52'),
(5, 'Mega Mass 2000', 'Weight Gain', 89.99, 119.99, 25, 4.8, '2000 calories per serving for hardgainers', 'Maximum calorie density', '2 scoops with whole milk', 'Mega Mass 2000.jpg', 0, '2026-06-06 01:14:52'),
(6, 'RETA Gold Whey Isolate', 'Build Muscle', 69.99, 94.99, 55, 4.9, '25g protein per scoop, zero sugar', 'Muscle protein synthesis, fast absorption', 'Post-workout or as needed', 'RETA Gold Whey Isolate.jfif', 1, '2026-06-06 01:14:52'),
(7, 'Creatine Monohydrate', 'Build Muscle', 39.99, 54.99, 70, 4.8, 'Micronized creatine for maximum absorption', 'Strength, power, muscle hydration', '5g daily with water', 'Creatine Monohydrate.jpg', 1, '2026-06-06 01:14:52'),
(8, 'Thermo Shred Fat Burner', 'Weight Loss', 49.99, 69.99, 50, 4.5, 'Thermogenic fat burner', 'Metabolism boost, energy', '2 capsules AM and PM', 'Thermo Shred Fat Burner.jpg', 0, '2026-06-06 01:14:52'),
(9, 'RETA Pre-Workout', 'Pre-Workout', 54.99, 74.99, 55, 4.9, 'Explosive energy + pump formula', 'Focus, endurance, vascularity', '1 scoop 30min pre-workout', 'RETA Pre-Workout.jfif', 1, '2026-06-06 01:14:52'),
(10, 'BCAA Recovery Xtreme', 'Recovery', 42.99, 58.99, 60, 4.7, '2:1:1 BCAA + electrolytes', 'Muscle repair, hydration', 'Intra-workout or post', 'BCAA Recovery Xtreme.jfif', 0, '2026-06-06 01:14:52');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(500) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(11, 5, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiNSIsImVtYWlsIjoicmFuZHludG9uZ3dlMjM3QGdtYWlsLmNvbSIsInJvbGUiOiJ1c2VyIiwiZXhwIjoxNzgxMDI0MTEyLCJpYXQiOjE3ODA5Mzc3MTJ9.KGPBPiTSI9_plams8T79OO4MzUPjyvVCJ1YWtQOv48M', '2026-07-08 17:55:12', '2026-06-08 16:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uid`, `email`, `fullname`, `phone`, `address`, `password`, `role`, `created_at`) VALUES
(1, 'admin_001', 'homeofreta@gmail.com', 'System Admin', '653622655', NULL, '$2y$10$bqxxCYX/9kzYSljfnHRK3O3FOKt6H1iyBQBgZwqHI7saPJ6wI/xry', 'admin', '2026-06-06 01:14:52'),
(4, 'user_1780746452_20958b31', 'khalidpayet75@gmail.com', 'khalid payet', '651693355', '', '$2y$10$7uIim9Og8qZkIWbUnT2p8.o.F/D9fRpLI/9zfVvlJdZG8E10V6Rx.', 'user', '2026-06-06 11:47:32'),
(5, 'user_1780838252_081d9e68', 'randyntongwe237@gmail.com', 'Randy Ntongwe', '651693355', '', '$2y$10$Tt9KYBy2T.LixETxYyC6d.zeM1Xahv3GTo0F7npiFtswv70k40IjK', 'user', '2026-06-07 13:17:32');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_email` (`customer_email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation_id` (`conversation_id`);

--
-- Indexes for table `message_notes`
--
ALTER TABLE `message_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `pinned_messages`
--
ALTER TABLE `pinned_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_bestseller` (`bestseller`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_notes`
--
ALTER TABLE `message_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pinned_messages`
--
ALTER TABLE `pinned_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
