-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2025 at 12:55 PM
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
-- Database: `zamfara_crime_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$b2d5kdwXeK3etK4FxujXA.OGKU0ZfUzMaKf4Ev.c1VXRMWQdTPLZ.', 'Admin User', 'admin@example.com', 'admin', '2025-01-01 14:05:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(2, 1, 'login', 'Admin logged in successfully: admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0', '2025-01-01 18:26:18');

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `success`, `timestamp`) VALUES
(1, 'admin', '127.0.0.1', 1, '2025-01-01 18:26:18');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('report','status','system') NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `read_status`, `created_at`) VALUES
(1, NULL, 'New Crime Report', 'A new report has been submitted: ZCRS-20240124-1234', 'report', 0, '2024-12-23 20:34:10'),
(2, NULL, 'Status Update', 'Case ZCRS-20240123-5678 has been moved to investigation', 'status', 0, '2024-12-23 20:34:10'),
(3, NULL, 'System Update', 'New features have been added to the dashboard', 'system', 0, '2024-12-23 20:34:10');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reference_number` varchar(20) DEFAULT NULL,
  `crime_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `lga` varchar(50) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `latitude` varchar(20) DEFAULT NULL,
  `longitude` varchar(20) DEFAULT NULL,
  `crime_date` date DEFAULT NULL,
  `crime_time` time DEFAULT NULL,
  `evidence_files` text DEFAULT NULL,
  `voice_recording` varchar(255) DEFAULT NULL,
  `status` enum('pending','investigating','resolved','closed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reference_number`, `crime_type`, `description`, `lga`, `location`, `latitude`, `longitude`, `crime_date`, `crime_time`, `evidence_files`, `voice_recording`, `status`, `created_at`) VALUES
(1, 'ZCRS-20241223-1837', 'terrorism', 'afdsfsff', 'gusau', 'Ahmadu Bello Way, Central Business District, Municipal Area Council, Federal Capital Territory, 223140, Nigeria', '9.0562646', '7.4985259', '2024-12-20', '15:54:00', '[\"ZCRS-20241223-1837-Abubuwan Da Samari Da Yan Mata Ya kamata Su Sani Gameda Rayuwar Aure - Mal. Amin.mp4\"]', '', 'pending', '2024-12-23 15:38:45'),
(2, 'ZCRS-20241223-8814', 'banditry', 'dffgdfgdf', 'kaura_namoda', 'Ahmadu Bello Way, Central Business District, Municipal Area Council, Federal Capital Territory, 223140, Nigeria', '9.0562646', '7.4985259', '2024-12-13', '15:44:00', '[\"ZCRS-20241223-8814-3_20241118_114958_0000.png\"]', '', 'pending', '2024-12-23 15:44:23'),
(3, 'ZCRS-20241223-9770', 'assault', 'gfvfhnbhghgjh', 'gusau', 'Ahmadu Bello Way, Central Business District, Municipal Area Council, Federal Capital Territory, 223140, Nigeria', '9.0562646', '7.4985259', '2024-12-20', '23:03:00', '[\"ZCRS-20241223-9770-3_20241118_113735_0000.png\"]', 'ZCRS-20241223-9770-voice.wav', 'pending', '2024-12-23 16:34:14'),
(4, 'ZCRS-20241223-8977', 'vandalism', 'bvcnhmn ', 'zurmi', 'Ahmadu Bello Way, Central Business District, Municipal Area Council, Federal Capital Territory, 223140, Nigeria', '9.0562646', '7.4985259', '2024-12-19', '04:30:00', '[]', 'ZCRS-20241223-8977-voice.wav', 'pending', '2024-12-23 16:42:40'),
(5, 'ZCRS-20241225-7314', 'theft', 'thief', 'gusau', 'A. E. Ekukinam Street, Utako, Abuja, Municipal Area Council, Federal Capital Territory, 240102, Nigeria', '9.0669056', '7.4448896', '2024-12-24', '03:00:00', '[\"ZCRS-20241225-7314-3_20241118_115645_0000.png\"]', 'ZCRS-20241225-7314-voice.wav', 'pending', '2024-12-25 18:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `report_updates`
--

CREATE TABLE `report_updates` (
  `id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `update_text` text NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','police') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', '08012345678', '$2y$10$somehashedpassword', 'user', 'active', '2024-12-23 21:52:50'),
(2, 'Jane Smith', 'jane@example.com', '08087654321', '$2y$10$somehashedpassword', 'user', 'active', '2024-12-23 21:52:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_activity` (`admin_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`);

--
-- Indexes for table `report_updates`
--
ALTER TABLE `report_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `report_updates`
--
ALTER TABLE `report_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD CONSTRAINT `fk_admin_activity` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `report_updates`
--
ALTER TABLE `report_updates`
  ADD CONSTRAINT `report_updates_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  ADD CONSTRAINT `report_updates_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `staff` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
