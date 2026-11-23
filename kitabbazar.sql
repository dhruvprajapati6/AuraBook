-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2026 at 04:41 AM
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
-- Database: `kitabbazar`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `book_condition` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `village` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'available',
  `stock` int(11) NOT NULL DEFAULT 1,
  `category` varchar(255) NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(15) DEFAULT NULL,
  `customer_village` varchar(100) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `final_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `user_id`, `title`, `author`, `price`, `book_condition`, `description`, `village`, `status`, `stock`, `category`, `image1`, `image2`, `image3`, `image4`, `image5`, `created_at`, `customer_name`, `customer_phone`, `customer_village`, `delivery_address`, `final_price`) VALUES
(10, NULL, 'Class-10', 'Navneet Education Limited', 120.00, 'Bilkul Nayi Jaisi', 'page missing not conditiopn is good', 'Jalotra', 'available', 1, 'Standard 10', '1782999489_img1_8961.jpeg', '1782999489_img2_5669.jpeg', '1782999489_img3_2481.jpeg', '1782999489_img4_4748.jpeg', '1782999489_img5_7045.jpeg', '2026-07-02 13:38:09', 'Mr dhruv Prajapati', '63528457781', 'Jalotra', NULL, 140.00),
(11, NULL, 'Class-12', 'Navneet Education Limited', 150.00, 'Bilkul Nayi Jaisi', 'conditional is Best and Perfect so Very Like', 'Gola', 'available', 1, 'Standard 12', '1784982172_img1_4390.jpeg', '1784982172_img2_6795.jpeg', '1784982172_img3_4200.jpeg', '1784982172_img4_4281.jpeg', '1784982172_img5_7430.jpeg', '2026-07-25 12:22:52', '63528457781', 'Jalotra', 'Mr dhruv Prajapati', NULL, NULL),
(12, NULL, 'Data Structure', 'Govindas bhadve', 140.00, 'Bilkul Nayi Jaisi', 'Best |Conditional eductional purpose', 'Karnala', 'available', 1, 'College', '1785131502_img1_8453.jpeg', '1785131502_img2_7399.jpeg', NULL, NULL, NULL, '2026-07-27 05:51:42', 'Mr dhruv Prajapati', '9825673489', 'Karnala', NULL, NULL),
(13, 7, 'Python Full Course', 'Denise Richi', 140.00, 'Purani (Thodi Fati)', 'like a new condition', 'Vasan', 'deliverd', 1, 'College', '1785131880_img1_1359.jpeg', '1785131880_img2_7321.jpeg', '1785131880_img3_4552.jpeg', '1785131880_img4_8084.jpeg', '1785131880_img5_3910.jpeg', '2026-07-27 05:58:00', 'Mr Hitesh Patni', '9825673489', 'Vasan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `new_books`
--

CREATE TABLE `new_books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 10,
  `image1` varchar(255) NOT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_books`
--

INSERT INTO `new_books` (`id`, `title`, `author`, `category`, `price`, `mrp`, `stock`, `image1`, `image2`, `image3`, `image4`, `description`, `created_at`) VALUES
(1, 'Class-10', 'Nsp', 'Starderd 10', 130.00, 140.00, 10, '1784982172_img1_4390.jpeg', '1784982172_img1_4390.jpeg', 'upload/img1', 'upload/img1', 'hii', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `new_book_orders`
--

CREATE TABLE `new_book_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `buyer_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `village` varchar(100) NOT NULL,
  `full_address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `order_status` enum('Pending','Processing','Out for Delivery','Delivered','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_village` varchar(100) NOT NULL,
  `delivery_address` text NOT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `order_status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `book_id`, `customer_name`, `customer_phone`, `customer_email`, `customer_village`, `delivery_address`, `final_price`, `order_status`, `created_at`) VALUES
(1, 10, 'mr marvan', '9825673489', '', 'Gola', 'asdasdasdas', 120.00, 'Pending', '2026-07-24 06:19:48'),
(2, 10, 'Mr Marvan N', '9825673489', '', 'Gola', 'detxgdf', 120.00, 'Pending', '2026-07-24 06:22:36'),
(3, 10, 'Mr Dhruv Prajapati', '9825673489', '', 'Gola', 'dsfafsdf', 120.00, 'Pending', '2026-07-24 07:05:16'),
(4, 11, 'YouTube wallah YouTube wallah', '9825673489', 'marjan@gmail.com', 'Talepura', 'sadasdad', 170.00, 'Pending', '2026-07-26 05:07:56');

-- --------------------------------------------------------

--
-- Table structure for table `site_visitors`
--

CREATE TABLE `site_visitors` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `visit_date` date NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_visitors`
--

INSERT INTO `site_visitors` (`id`, `ip_address`, `visit_date`, `last_activity`) VALUES
(1, '::1', '2026-07-25', '2026-07-25 15:12:18'),
(62, '::1', '2026-07-26', '2026-07-26 08:17:59'),
(86, '::1', '2026-07-27', '2026-07-27 16:45:01'),
(100, '10.35.3.5', '2026-07-27', '2026-07-27 16:49:23'),
(103, '::1', '2026-07-28', '2026-07-28 17:30:22'),
(125, '::1', '2026-07-29', '2026-07-29 17:26:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `village_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `village_name`, `created_at`, `profile_image`) VALUES
(1, 'Mr Marvan N', 'marvan@gmail.com', '9879579384', '$2y$10$yhsAPkU5UcVc/alUJwu//efx2u8YD379GnWIYkfJDiX0iRMJaGBTy', 'Chandanpur', '2026-06-30 02:04:31', NULL),
(4, 'Mr Dhruv Prajapati', 'dhruv23@gmail.com', '6352870496', '$2y$10$J/kvTAsS1X6Gqw0v1lGE8usx3YhtBi5ueVs.RJqA18VLS/.6vkWmC', 'Rampur', '2026-06-30 02:23:27', NULL),
(5, 'Mr Marvan N', 'marvan44@gmail.com', '6352870421', '$2y$10$HEVJYfjLVgZx0tMPS.HiCe2FuqGr1FVMJiWPrdU8.pikEXYoceexS', 'Gola', '2026-07-24 06:10:07', NULL),
(6, 'Mr Dhruv Prajapati', 'dhruvi45@gmail.com', '9825613289', '$2y$10$viPhS5Ja.Ap25jZnsl2nEeSYTXPB3mc5rKIuc9FPAlZikhPLCL7cS', 'Rampur', '2026-07-24 06:58:45', NULL),
(7, 'YouTube wallah YouTube wallah', 'tbwllhtbwllh@gmail.com', '9879579384', '$2y$10$k441du2.YIsF2WM7nukzL.gSBAM8d6Va3/jY0cei.hQZqllEA2cq6', 'Gola', '2026-07-25 13:03:37', 'user_7_1785331823.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `new_books`
--
ALTER TABLE `new_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_book_orders`
--
ALTER TABLE `new_book_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `site_visitors`
--
ALTER TABLE `site_visitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_visit` (`ip_address`,`visit_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `new_books`
--
ALTER TABLE `new_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `new_book_orders`
--
ALTER TABLE `new_book_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `site_visitors`
--
ALTER TABLE `site_visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `new_book_orders`
--
ALTER TABLE `new_book_orders`
  ADD CONSTRAINT `new_book_orders_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `new_books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
