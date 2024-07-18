-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2024 at 02:22 PM
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
(21, '14', 'ADMIN', '', 't', '092955555', 'kurt@gmail.com', 'student', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-18 11:43:49');

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
(14, 'admin', 'admin', 1),
(30, '15-300188', '123', 2),
(33, '1', '123', 2);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
