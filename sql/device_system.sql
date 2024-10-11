-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2024 at 10:56 PM
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
-- Database: `device_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `department_list`
--

CREATE TABLE `department_list` (
  `id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_list`
--

INSERT INTO `department_list` (`id`, `department_name`) VALUES
(1, '4 أ'),
(2, 'RL'),
(3, 'راكال'),
(4, '776'),
(5, 'WJ'),
(6, 'المكن و الشحن');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `serial_number` varchar(255) NOT NULL,
  `serial_number_type` enum('manual','factory') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_id`, `unit_id`, `serial_number`, `serial_number_type`) VALUES
(1, 1, 1, '12-أع', 'manual'),
(2, 2, 1, '12-ضض', 'manual'),
(3, 3, 1, '999', 'manual'),
(4, 1, 1, '4654e', 'factory'),
(5, 6, 1, '23983cj', 'manual'),
(6, 7, 1, '2343sdf', 'factory'),
(7, 2, 2, '963', 'manual');

-- --------------------------------------------------------

--
-- Table structure for table `devices_list`
--

CREATE TABLE `devices_list` (
  `id` int(11) NOT NULL,
  `device_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `devices_list`
--

INSERT INTO `devices_list` (`id`, `device_name`) VALUES
(1, 'mob'),
(2, 'lap'),
(3, 'pcp'),
(4, 'تنجرشحن'),
(5, 'موبيل'),
(6, 'R151'),
(7, 'motorola'),
(8, 'lap'),
(9, 'watch');

-- --------------------------------------------------------

--
-- Table structure for table `device_history`
--

CREATE TABLE `device_history` (
  `history_id` int(11) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `entry_date` date NOT NULL,
  `fix_date` date DEFAULT NULL,
  `exit_date` date DEFAULT NULL,
  `who_fixed` varchar(255) DEFAULT NULL,
  `operation_permission` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `fault_type` varchar(255) DEFAULT NULL,
  `tools_used` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `reviewed_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device_history`
--

INSERT INTO `device_history` (`history_id`, `serial_number`, `entry_date`, `fix_date`, `exit_date`, `who_fixed`, `operation_permission`, `department_id`, `fault_type`, `tools_used`, `is_approved`, `reviewed_by`) VALUES
(1, '12-أع', '2024-10-09', '2024-10-12', NULL, 'محمد', '123', 2, 'ram broken', 'ram', 1, 'karem '),
(2, '12-ضض', '2024-10-11', '2024-10-11', '2024-10-12', 'مساعد /محمد', '1122', 2, 'رام', '4 رام', 1, 'العقيد/ محمد عرفه'),
(3, '999', '2024-10-10', '2024-10-11', '2024-10-12', 'mohamed', '777-اب', 1, 'ram got broken', 'ram', 1, 'العقيد/ محمد عرفه'),
(4, '4654e', '2024-10-11', '2024-10-12', NULL, NULL, '789', 2, NULL, NULL, 0, ''),
(5, '23983cj', '2024-10-10', NULL, NULL, NULL, '988469', 8, NULL, NULL, 0, NULL),
(6, '2343sdf', '2024-10-10', NULL, NULL, NULL, '684364', 9, NULL, NULL, 0, NULL),
(7, '963', '2024-10-30', '2024-10-30', '2024-10-31', 'ramdan', '345AA', 1, 'hard', 'ssd', 1, 'رائد محمد'),
(8, '963', '2024-11-01', '2024-10-12', '2024-11-09', 'karem 3ali', '345ABC', 1, 'hard ram ', 'ssd m2', 1, 'mohamed eldeep');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `division_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `division_name`) VALUES
(1, 'فرقه 5'),
(2, 'فرقه 6');

-- --------------------------------------------------------

--
-- Table structure for table `regiments`
--

CREATE TABLE `regiments` (
  `id` int(11) NOT NULL,
  `regiment_name` varchar(255) NOT NULL,
  `division_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regiments`
--

INSERT INTO `regiments` (`id`, `regiment_name`, `division_id`) VALUES
(1, 'ل 10', 1),
(2, 'ل311', 2);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(255) NOT NULL,
  `regiment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`, `regiment_id`) VALUES
(1, 'ك33', 1),
(2, 'ك76', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('egra2at','tasle7') NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `department_id`, `is_admin`) VALUES
(1, 'A1', 'A1', 'egra2at', NULL, 0),
(2, 'B1', 'B1', 'tasle7', 1, 0),
(3, 'A2', 'A2', 'egra2at', NULL, 1),
(4, 'B2', 'B2', 'tasle7', 2, 0),
(5, 'B3', 'B3', 'tasle7', 3, 0),
(6, 'B4', 'B4', 'tasle7', 4, 0),
(7, 'B5', 'B5', 'tasle7', 5, 0),
(8, 'B6', 'B6', 'tasle7', 6, 0),
(9, 'A3', 'A3', 'egra2at', NULL, 1),
(10, 'A4', 'A4', 'egra2at', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department_list`
--
ALTER TABLE `department_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serial_number` (`serial_number`);

--
-- Indexes for table `devices_list`
--
ALTER TABLE `devices_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_history`
--
ALTER TABLE `device_history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regiments`
--
ALTER TABLE `regiments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regiment_id` (`regiment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department_list`
--
ALTER TABLE `department_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `devices_list`
--
ALTER TABLE `devices_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `device_history`
--
ALTER TABLE `device_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `regiments`
--
ALTER TABLE `regiments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `regiments`
--
ALTER TABLE `regiments`
  ADD CONSTRAINT `regiments_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_ibfk_1` FOREIGN KEY (`regiment_id`) REFERENCES `regiments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
