-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2024 at 12:44 AM
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
-- Database: `aphoadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `member_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `created_at`, `member_id`) VALUES
(23, 'Hello', 'Please buy now', '2024-07-18 13:25:48', 39),
(24, 'Hello', 'This is officer ', '2024-07-18 13:30:31', 14),
(25, 'PAY BILLS', 'Pay now!', '2024-07-18 14:15:49', 14),
(26, 'Hello guys', 'Bakit?', '2024-07-18 15:10:41', 14),
(27, 'upuser', 'upuser', '2024-07-18 16:22:16', 14),
(28, 'doremi', 'doremi faso la tido doremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tidodoremi faso la tido', '2024-07-18 16:22:42', 14),
(29, 'Pogi ba ko?', 'Pagka di kayo napogian sakin akin na yang 100pesos nyo isa isa kayo!', '2024-07-18 16:25:40', 14),
(30, 'fifty', 'forty fortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyfortyforty', '2024-07-18 16:35:55', 14),
(31, 'ayay', 'ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ay ayay ay ay ay ay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ayay ay ay ay', '2024-07-18 16:36:41', 14);

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE `information` (
  `info_id` int(11) NOT NULL,
  `member_id` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `educ_attainment` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `date_submitted` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `homeowner_status` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `length_of_stay` int(11) DEFAULT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `occupant_name_1` varchar(100) DEFAULT NULL,
  `occupant_age_1` int(11) DEFAULT NULL,
  `occupant_relationship_1` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `information`
--

INSERT INTO `information` (`info_id`, `member_id`, `last_name`, `first_name`, `middle_name`, `contact_no`, `email_address`, `occupation`, `address`, `educ_attainment`, `birthdate`, `date_submitted`, `sex`, `civil_status`, `homeowner_status`, `type`, `length_of_stay`, `owner_name`, `occupant_name_1`, `occupant_age_1`, `occupant_relationship_1`, `timestamp`) VALUES
(17, '30', 'Dullon', 'Kurt', 'Tugonon', '0929123456789', 'kurt@gmail.com', 'student', 'mandaluyong', 'college', '2024-07-13', '2024-07-13', 'male', 'single', 'legitimate', '0', 1, 'Kurt Jansen Dullon', NULL, NULL, NULL, '2024-07-12 22:04:44'),
(20, '33', 'test', 'tester', 't', '0929123456788', 'tester@gmail.com', 'student', '', '', '2024-07-18', '2024-07-18', 'male', 'single', 'legitimate', '0', 1, 'iw', NULL, NULL, NULL, '2024-07-18 11:13:05'),
(21, '14', 'OFFICER', '', 't', '092955555', 'kurt@gmail.com', 'student', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-18 11:43:49'),
(22, '36', 'test', 'test', 'test', 'test', 'test@gmail.com', 'test', 'test', 'test', '2024-07-18', '2024-07-18', 'male', 'single', 'associate', '0', 1, '1', NULL, NULL, NULL, '2024-07-18 12:35:23'),
(24, '888', 'john', 'doe', 'tugonon', '09291234', 'johndoe@gmail.com', 'studyante', 'manda', 'college', '2024-07-31', '2024-07-31', 'm', 'single', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-18 17:28:04'),
(25, '39', 'tangina', 'ayaw', 'maayos', '0956789123123', 'tanginamo@gmail.com', 'kupalka', 'manda', 'bobo', '2024-07-19', '2024-07-19', 'male', 'single', 'legitimate', '0', 1, 'pogi', NULL, NULL, NULL, '2024-07-18 17:36:13'),
(26, '40', 'Dullon', 'Kurt', 'Tugonon', '09296665765', 'kurtjansendullon@gmail.com', 'Student', 'Mandaluyong', 'College', '2024-07-19', '2024-07-19', 'male', 'married', 'legitimate', '0', 1, 'kurt', NULL, NULL, NULL, '2024-07-18 20:13:42');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `membership_no` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `group` tinyint(4) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `membership_no`, `password`, `group`) VALUES
(14, 'officer', 'officer', 3),
(30, '15-300188', '123', 2),
(33, '1', '123', 2),
(36, 'test', '123', 2),
(38, '888', '123', 2),
(39, '555', '123', 3),
(40, '1012', '123123', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`info_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `membership_no` (`membership_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
