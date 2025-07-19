-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 10:05 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `donations_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_name`, `phone`, `amount`, `date`) VALUES
(7, 'abdul hanan', '03109309317', 1000.00, '2025-04-22 22:22:50'),
(8, 'asad raiz', '03410324442', 1000.00, '2025-04-22 22:24:11'),
(9, 'ahsan zaheer', '03408922766', 1000.00, '2025-04-22 22:24:42'),
(10, 'anas azad', '03437355039', 1000.00, '2025-04-22 22:25:10'),
(12, 'hammad javed', '03448433073', 1000.00, '2025-04-22 22:32:50'),
(13, 'juanid ishaq', '03415759261', 1000.00, '2025-04-22 22:33:18'),
(14, 'saad rauf', '03556015421', 1000.00, '2025-04-22 22:33:50'),
(15, 'sajjad ', '03426671918', 1000.00, '2025-04-22 22:35:05'),
(16, 'slahudeen ', '03405210896', 1000.00, '2025-04-22 22:35:40'),
(17, 'zeshan ', '03429225251', 1000.00, '2025-04-22 22:36:14'),
(23, 'imran aziz', '03109309317', 15000.00, '2025-04-23 15:34:38'),
(25, 'abdul tawab', '3109309317', 1000.00, '2025-04-23 17:00:41'),
(28, 'existing amount', '3109309317', 49800.00, '2025-04-23 18:50:09'),
(30, 'wasiq naheem', '3109309317', 5000.00, '2025-04-23 18:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `image_path`, `created_at`) VALUES
(1, 'hanan', 'jslcsd jdjs fdf s', 'uploads/681bb55c5661b-hnan.jpg', '2025-05-07 19:32:44'),
(2, 'hanan', 'i am here', 'uploads/681bb5e836f08-logo without background.jpg', '2025-05-07 19:35:04'),
(3, 'food giving', 'we are giveing food to peoppe of mashim ajssd bsakass sjdsdsadji dsjdsakd skadmsadk', 'uploads/681e64db74445-hnan.jpg', '2025-05-09 20:26:03'),
(4, 'anothrr test', 'this is test for builting website in ower walfer oeuff', 'uploads/681e64fec9ac7-hnan.jpg', '2025-05-09 20:26:38'),
(5, 'this is dumy', 'how are you i am fine where is shelling happening', 'uploads/681e6877a69aa-hnan.jpg', '2025-05-09 20:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `spending`
--

CREATE TABLE `spending` (
  `id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spending`
--

INSERT INTO `spending` (`id`, `category`, `amount`, `date`) VALUES
(1, 'books', 45000.00, '2025-05-05 18:05:06'),
(4, 'food', 500.00, '2025-05-05 18:14:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'hanan', '$2y$10$v8jycN0GXgHKjHb9nyJ8wuUIqhyqRVoOJcfpkuq2KJJ.VF6jC5h/q');

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteers`
--

INSERT INTO `volunteers` (`id`, `full_name`, `phone`, `email`, `reason`, `status`, `submitted_at`) VALUES
(1, 'Test User', '+1234567890', 'test@example.com', 'Test reason', 'Rejected', '2025-05-06 02:52:44'),
(2, 'Abdul Hanan', '03109309317', 'abdulhanan7867a@gmail.com', 'i am abdul hanan', 'Rejected', '2025-05-06 03:01:27'),
(3, 'hasnain mir', '03435560907', 'abdulhanan7867a@gmail.com', 'i am her', 'Rejected', '2025-05-06 03:21:08'),
(4, 'waleed ahem', '03192152', 'test@gmail.com', 'im a iher', 'Approved', '2025-05-06 22:10:34'),
(5, 'Abdul Hanan', '03109309317', 'abdulhanan7867a@gmail.com', 'i am here', 'Approved', '2025-05-07 23:44:32'),
(6, 'Abdul Hanan', '03109309317', 'abdulhanan7867a@gmail.com', 'dded', 'Rejected', '2025-05-07 23:47:42'),
(7, 'Abdul Hanan', '03109309317', 'abdulhanan7867a@gmail.com', 'helow', 'Rejected', '2025-05-07 23:58:03'),
(8, 'Abdul Hanan', '03109309317', 'abdulhanan7867a@gmail.com', 'sdsd', 'Approved', '2025-05-08 00:00:11'),
(9, 'Abdul Hanan', '03109309317', 'abdulhanan7867a@gmail.com', 'jsd', 'Rejected', '2025-05-10 01:43:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spending`
--
ALTER TABLE `spending`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `spending`
--
ALTER TABLE `spending`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
