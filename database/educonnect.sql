-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 23, 2025 at 08:03 AM
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
-- Database: `educonnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `connection_requests`
--

CREATE TABLE `connection_requests` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `scholarship_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `donation_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','failed','unverified','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_id`, `scholarship_id`, `amount`, `donation_date`, `payment_method`, `transaction_id`, `notes`, `created_at`, `status`) VALUES
(2, 4, 1, 40000.00, '2025-06-18', 'Easy Paisa', '123456789', 'Testing Transaction', '2025-06-18 18:50:58', 'approved'),
(3, 4, 1, 30000.00, '2025-07-18', 'Easy Paisa', '420109708088', 'Testing Note', '2025-06-18 19:28:05', 'approved'),
(4, 4, 1, 30000.00, '2025-08-18', 'Easy Paisa', '420109708089', 'qwertyuiop', '2025-06-18 19:37:00', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `donor_profiles`
--

CREATE TABLE `donor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `donation_preferences` text DEFAULT NULL,
  `annual_budget` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor_profiles`
--

INSERT INTO `donor_profiles` (`id`, `user_id`, `organization`, `phone`, `address`, `website`, `contact_person`, `donation_preferences`, `annual_budget`, `created_at`, `updated_at`) VALUES
(1, 4, 'Sahar Foundation', '03121234567', 'qwertyuio, asdfghjkl, zxcvbnm', 'https://www.test.com', 'Hina', 'No', 40000.00, '2025-06-18 18:42:25', '2025-06-18 18:42:25');

-- --------------------------------------------------------

--
-- Table structure for table `mentoring_sessions`
--

CREATE TABLE `mentoring_sessions` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `topic` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `duration` int(11) NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `meeting_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_connections`
--

CREATE TABLE `mentor_connections` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_profiles`
--

CREATE TABLE `mentor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expertise` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`availability`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL COMMENT 'Path to the image, e.g., ./images/news_image.jpg',
  `date` date NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `description`, `image`, `date`, `status`, `created_at`) VALUES
(2, 'Testing', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', './admin/uploads/news/68589dc0eac78_3.jpeg', '2025-06-26', 'active', '2025-06-22 23:28:58'),
(3, 'News 2', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', './admin/uploads/news/6858a0fe1d133_Eid Mubarak.png', '2025-06-23', 'active', '2025-06-23 00:34:06'),
(4, 'News 2', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', './admin/uploads/news/6858a10987930_Eid Mubarak.png', '2025-06-23', 'active', '2025-06-23 00:34:17'),
(5, 'News 2', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', './admin/uploads/news/6858a10bd8af9_Eid Mubarak.png', '2025-06-23', 'active', '2025-06-23 00:34:19'),
(6, 'News 2', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', './admin/uploads/news/6858a10e238bf_Eid Mubarak.png', '2025-06-23', 'active', '2025-06-23 00:34:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `requirements` text DEFAULT NULL,
  `deadline` date NOT NULL,
  `available_slots` int(11) NOT NULL DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`id`, `title`, `description`, `amount`, `requirements`, `deadline`, `available_slots`, `status`, `created_at`) VALUES
(1, 'Merit Scholarship 2024', 'Scholarship for outstanding academic performance', 50000.00, 'CGPA 3.5 or above', '2024-12-31', 9, 'active', '2025-06-18 18:37:27'),
(2, 'Merit Scholarship 2025', 'Scholarship for outstanding academic performance', 50000.00, 'CGPA 3.5 or above', '2025-09-12', 10, 'active', '2025-06-18 20:04:51');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applications`
--

CREATE TABLE `scholarship_applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_applications`
--

INSERT INTO `scholarship_applications` (`id`, `student_id`, `scholarship_id`, `status`, `application_date`, `notes`) VALUES
(1, 8, 1, 'pending', '2025-06-22 07:10:14', '');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_hike`
--

CREATE TABLE `scholarship_hike` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `requirements` text DEFAULT NULL,
  `application_deadline` date NOT NULL,
  `status` enum('active','closed','awarded') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `roll_no` varchar(50) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `supporters` varchar(150) DEFAULT NULL,
  `relatives` varchar(150) DEFAULT '0',
  `cgpa` decimal(10,0) DEFAULT NULL,
  `sgpa` decimal(10,0) DEFAULT NULL,
  `previous_semester_gpa` decimal(3,2) DEFAULT NULL,
  `utility_bills` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `name`, `father_name`, `roll_no`, `student_id`, `department`, `supporters`, `relatives`, `cgpa`, `sgpa`, `previous_semester_gpa`, `utility_bills`, `created_at`) VALUES
(10, 8, 'Abc Khan', 'Xyz Khan', '40', '11255', 'CS', 'Father, and Brothers', '5', 3, 3, 3.11, 'utility_bill_8_1750186054.jpeg', '2025-06-17 17:58:55'),
(11, 5, 'Zain Ul Basit', 'Muhammad Ismail', '40', '11255', 'CS', 'Father, and Brothers', '5', 3, 3, 3.13, 'utility_bill_5_1750639919.jpg', '2025-06-23 00:51:59');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL COMMENT 'e.g., ./images/team/member1.jpg',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `name`, `role`, `image`, `status`, `created_at`) VALUES
(4, 'Hina', 'CEO / Founder', './uploads/team/68589adbe16ee_1.jpeg', 'active', '2025-06-23 00:07:44'),
(5, 'Laraib', 'Co Founder', './uploads/team/68589c4642852_2.jpeg', 'active', '2025-06-23 00:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','student','donor','mentor','examination') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `created_at`, `updated_at`, `reset_token`, `reset_expires`) VALUES
(2, 'Test Student', 'tests@gmail.com', '$2y$10$/Kqp4pq4sfI8.Qw2eBHKfOK4n6hHxJ0w7pDMRMIkFJl30wA5YCKp.', 'student', '2025-05-11 18:57:38', '2025-05-11 18:57:38', NULL, NULL),
(3, 'Test M', 'testm@gmail.com', '$2y$10$gPiTA7RiBLeFwE3.qqfcnemb1Utfb6pIYD/GwvZZcNYiuTqol.WcO', 'mentor', '2025-05-11 18:59:34', '2025-05-11 18:59:34', NULL, NULL),
(4, 'Test D', 'testd@gmail.com', '$2y$10$kNPrUdzaGdRaQPALj353IudNWueTmd6cx4V.HGs6qDiq4UR02bKIS', 'donor', '2025-05-11 19:00:46', '2025-05-11 19:00:46', NULL, NULL),
(5, 'Zain Ul Basit', 'zainulbasit486@gmail.com', '$2y$10$DRV9gGvfzqycD7FeYWM3HOWd8SDCOB/E.lrZqZyb2o9T4G3.ovbdm', 'student', '2025-05-26 05:17:03', '2025-06-23 00:52:11', '143756e9ea2cb64ebcd17d2f33077bda35f9d516935b7a0e46d999692974bb97', '2025-06-23 03:52:11'),
(6, 'Donor Khan', 'dkhan@gmail.com', '$2y$10$T0CIt9DUPAo0oMJVo8hyh.AfblUNlHw4lV7GY5ZZa9fXJUuQaHOda', 'donor', '2025-05-26 05:17:38', '2025-05-26 05:17:38', NULL, NULL),
(7, 'qwerty', 'qwerty@gmail.com', '$2y$10$OpMQyvRcUI0S4xk//E0XQOz5Tpgc39/PvN60g/mVNloOA1XobNjoO', 'examination', '2025-05-26 06:30:24', '2025-05-26 06:30:24', NULL, NULL),
(8, 'hina', 'hina@gmail.com', '$2y$10$ihroPlOFKtF2yjyOhyGN.OmAWVWOST.FbXR/nE4IayWH8fzkfG6Gu', 'student', '2025-06-12 19:29:41', '2025-06-23 00:47:53', '13306b4c80db3e900a16b541e1796111449add171e26542fe5645cee5b2bcbf2', '2025-06-23 03:47:53'),
(9, 'Admin Hina', 'admin@gmail.com', '$2y$10$CAxDAvkK9kuYxh9U6asSR.xDSHc5GH32TdlfAygKwGbn8Fmp29lca', 'admin', '2025-06-17 19:01:14', '2025-06-17 19:01:14', NULL, NULL),
(10, 'Testing', 'testss@gmail.com', '$2y$10$8gKL/YjRT/EFC7Hu6J30qubfzi7KrvE1BNJNfSHjlxrvz9MWF0JHy', 'student', '2025-06-22 17:59:30', '2025-06-22 17:59:30', NULL, NULL),
(11, 'Zain Ul Basit', 'zainulbasit25425@gmail.com', '$2y$10$hwlwylgwuEDNJBGuJhY17uSFig5HsCUS9StC0ItDOyll22.EcIqFy', 'student', '2025-06-23 00:48:26', '2025-06-23 01:53:15', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(500) NOT NULL,
  `embed_url` varchar(500) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `description`, `video_url`, `embed_url`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Women University Swabi || Khyber Pakhtunkhwa', 'Women University Swabi, Khyber Pakhtunkhwa, Pakistan is a centre of excellence in higher learning and quality education by providing strong career foundation through highly qualified and experienced academic faculty. The University offers a vast range of financial assistance/scholarships to students and state of art facilities including well-equipped science laboratories, computer labs and libraries.', 'https://www.youtube.com/watch?v=Ne7EFD_yPSo', 'https://www.youtube.com/embed/Ne7EFD_yPSo', 'educational', 'active', '2025-06-22 07:52:41', '2025-06-22 07:52:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `connection_requests`
--
ALTER TABLE `connection_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_request_status` (`status`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `donations_ibfk_2` (`scholarship_id`);

--
-- Indexes for table `donor_profiles`
--
ALTER TABLE `donor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mentoring_sessions`
--
ALTER TABLE `mentoring_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_session_date` (`session_date`);

--
-- Indexes for table `mentor_connections`
--
ALTER TABLE `mentor_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_connection_status` (`status`);

--
-- Indexes for table `mentor_profiles`
--
ALTER TABLE `mentor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_message_read` (`read_status`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_notification_read` (`read_status`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `scholarship_hike`
--
ALTER TABLE `scholarship_hike`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_reset_token` (`reset_token`),
  ADD KEY `idx_reset_expires` (`reset_expires`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `connection_requests`
--
ALTER TABLE `connection_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `donor_profiles`
--
ALTER TABLE `donor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mentoring_sessions`
--
ALTER TABLE `mentoring_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_connections`
--
ALTER TABLE `mentor_connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_profiles`
--
ALTER TABLE `mentor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scholarship_hike`
--
ALTER TABLE `scholarship_hike`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `connection_requests`
--
ALTER TABLE `connection_requests`
  ADD CONSTRAINT `connection_requests_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connection_requests_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `donor_profiles`
--
ALTER TABLE `donor_profiles`
  ADD CONSTRAINT `donor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentoring_sessions`
--
ALTER TABLE `mentoring_sessions`
  ADD CONSTRAINT `mentoring_sessions_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentoring_sessions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_connections`
--
ALTER TABLE `mentor_connections`
  ADD CONSTRAINT `mentor_connections_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_connections_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_profiles`
--
ALTER TABLE `mentor_profiles`
  ADD CONSTRAINT `mentor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD CONSTRAINT `scholarship_applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `scholarship_applications_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`);

--
-- Constraints for table `scholarship_hike`
--
ALTER TABLE `scholarship_hike`
  ADD CONSTRAINT `scholarship_hike_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
