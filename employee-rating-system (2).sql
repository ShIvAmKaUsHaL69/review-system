-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 12:55 PM
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
-- Database: `employee-rating-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `id` int(10) UNSIGNED NOT NULL,
  `yearmonth` char(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`id`, `yearmonth`) VALUES
(8, '2024-10'),
(7, '2024-11'),
(6, '2024-12'),
(5, '2025-01'),
(4, '2025-02'),
(3, '2025-03'),
(2, '2025-04'),
(1, '2025-05');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` varchar(255) NOT NULL,
  `for_role` tinyint(3) UNSIGNED NOT NULL,
  `quater` int(11) NOT NULL COMMENT '0 for monthly | 1 for jan- march | 2 for april - june | 3 for july to sept | 4 for oct to december',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `text`, `for_role`, `quater`, `created_at`) VALUES
(2, 'how is your employee performing', 2, 0, '2025-05-06 16:28:41'),
(3, 'how is your tl ', 3, 0, '2025-05-06 16:28:48'),
(4, 'how is your fellow employee', 4, 0, '2025-05-07 10:18:56'),
(5, 'how well is he learning', 2, 0, '2025-05-09 10:17:30'),
(6, 'how is he behaving', 2, 0, '2025-05-09 10:17:37'),
(7, 'how quick is his mind', 2, 0, '2025-05-09 10:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `role_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(3, 'employee'),
(4, 'employee_to_employee'),
(2, 'tl');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `submitter_id` int(10) UNSIGNED NOT NULL,
  `target_id` int(10) UNSIGNED NOT NULL,
  `period_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `submitter_id`, `target_id`, `period_id`, `created_at`) VALUES
(16, 3, 5, 1, '2025-05-09 13:37:35'),
(17, 3, 4, 1, '2025-05-09 13:37:51'),
(18, 5, 3, 1, '2025-05-09 13:38:17'),
(19, 5, 4, 1, '2025-05-09 13:38:26');

-- --------------------------------------------------------

--
-- Table structure for table `submission_answers`
--

CREATE TABLE `submission_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_answers`
--

INSERT INTO `submission_answers` (`id`, `submission_id`, `question_id`, `rating`, `comment`) VALUES
(31, 16, 2, 8, 'nice employee'),
(32, 16, 5, 2, 'not learning '),
(33, 16, 6, 1, ''),
(34, 16, 7, 1, 'low'),
(35, 17, 2, 10, 'good'),
(36, 17, 5, 10, ''),
(37, 17, 6, 10, ''),
(38, 17, 7, 10, ''),
(39, 18, 3, 1, 'not good'),
(40, 19, 4, 10, 'nice employee');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `tl_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `designation`, `role_id`, `tl_id`, `created_at`) VALUES
(2, 'admin', 'shivamkaushal181@gmail.com', 'password@123', '', 1, 0, '2025-05-06 16:15:40'),
(3, 'ShIvAmKaUsHaL', '9720sk41@gmail.com', 'shivam0432', '', 2, 0, '2025-05-06 16:25:25'),
(4, 'Gautam', 'gautammehra444@gmail.com', 'gautam@123', '', 3, 3, '2025-05-06 16:25:59'),
(5, 'aman', 'aman@gmail.com', 'aman@123', '', 3, 3, '2025-05-06 16:41:17'),
(6, 'new', 'new@gmail.com', 'new', 'developer', 3, 3, '2025-05-13 13:57:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `yearmonth` (`yearmonth`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `for_role` (`for_role`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_period` (`submitter_id`,`target_id`,`period_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `submission_answers`
--
ALTER TABLE `submission_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `tl_id` (`tl_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `submission_answers`
--
ALTER TABLE `submission_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`for_role`) REFERENCES `roles` (`id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `submissions_ibfk_3` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`);

--
-- Constraints for table `submission_answers`
--
ALTER TABLE `submission_answers`
  ADD CONSTRAINT `submission_answers_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
