-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2024 at 03:29 PM
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
  `id` int(11) NOT NULL,
  `membership_no` varchar(50) NOT NULL,
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
  `sex` enum('male','female') DEFAULT NULL,
  `civil_status` enum('single','married','separated','single_parent','widow(er)','live_in','others') DEFAULT NULL,
  `homeowner_status` enum('legitimate','associate') DEFAULT NULL,
  `type` enum('with','without') DEFAULT NULL,
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

INSERT INTO `information` (`id`, `membership_no`, `last_name`, `first_name`, `middle_name`, `contact_no`, `email_address`, `occupation`, `address`, `educ_attainment`, `birthdate`, `date_submitted`, `sex`, `civil_status`, `homeowner_status`, `type`, `length_of_stay`, `owner_name`, `occupant_name_1`, `occupant_age_1`, `occupant_relationship_1`, `timestamp`) VALUES
(1, 'e', 'e', 'e', 'e', 'e', '1@gmail.com', 'e', 'e', 'e', '1212-12-12', '1212-12-12', 'female', 'single', 'legitimate', '', 0, 'ee', '', 0, '', '2024-07-12 12:05:07'),
(2, 'alvinn', 'alvinn', 'alvinn', 'alvinn', 'alvinn', 'alvinn@gmail.com', 'alvinn', 'alvinn', 'alvinn', '0023-11-12', '0123-03-12', 'female', 'separated', 'associate', '', 123123, '123123', '', 0, '', '2024-07-12 12:14:07'),
(3, 'alvinn', 'alvinn', 'alvinn', 'alvinn', 'alvinn', 'alvinn@gmail.com', 'alvinn', 'alvinn', 'alvinn', '0023-11-12', '0123-03-12', 'female', 'separated', 'associate', '', 123123, '123123', '', 0, '', '2024-07-12 12:19:00'),
(4, 'HILO', 'HILO', 'HILO', 'HILO', 'HILO', 'HILO@GMAIL.COM', 'HILO', 'HILO', 'HILO', '5555-05-05', '5555-05-05', 'female', 'single_parent', 'associate', '', 7, '8', '', 0, '', '2024-07-12 12:20:46'),
(5, '22-259296', 'Arteza', 'Alvin', 'Ramos', '09989321472', 'alvinarteza@gmail.com', 'Student', '29. E. Hermosa St. Pateros', 'College', '2002-10-15', '2024-07-12', 'male', 'single', 'legitimate', '', 2, 'Milo Delacruz', '', 0, '', '2024-07-12 12:38:39'),
(6, '19-00016', 'MENDOZA', 'MARCELO', 'DELA CRUZ', '09331234932', 'MILOMENDOZA3065@GMAIL.COM', 'NONE', '3807 SEAGULL ST. ANAKPAWIS BRGY SAN JUAN CAINTA RIZAL', 'HIGH SCHOOL GRADUATE', '1965-08-30', '2024-07-12', 'male', 'single', 'associate', '', 21, 'DIOMIDA QUIJANO', '', 0, '', '2024-07-12 12:46:17');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `membership_no` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `membership_no`, `password`) VALUES
(1, '12-3456', '12345'),
(2, 'admin', 'admin'),
(4, '12', '1212'),
(6, '101502', '951753'),
(7, 'bossalvin', 'bossalvin'),
(8, 'asd', '123'),
(10, '123123', '123123'),
(13, 'hola', 'hola'),
(14, '123123123', '123123123'),
(15, 'admin1', 'alvin123'),
(16, '19-00016', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
