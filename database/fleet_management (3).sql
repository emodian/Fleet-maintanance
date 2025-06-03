-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 25-05-28 13:06
-- 서버 버전: 10.4.32-MariaDB
-- PHP 버전: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `fleet_management`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `license_number` varchar(20) NOT NULL,
  `truck_plate_number` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `drivers`
--

INSERT INTO `drivers` (`driver_id`, `driver_name`, `email`, `phone`, `license_number`, `truck_plate_number`, `status`) VALUES
(1, 'Emodian B', 'emodianb@popestr.com', '1111111111', 'LIC1001', 'T1001', 'active'),
(2, 'Godlisten Stanley', 'godlistens@popestr.com', '1111111112', 'LIC1002', 'T1002', 'active'),
(3, 'Peter Japhet', 'peterj@popestr.com', '1111111113', 'LIC1003', 'T1003', 'active'),
(4, 'Godfrey Kasembe ', 'godfreyk@popestr.com', '1111111114', 'LIC1004', 'T1004', 'inactive'),
(5, 'Josia Mshana', 'josiam@popestr.com', '1111111115', 'LIC1005', 'T1005', 'active'),
(6, 'Amani Brian', 'amanib@popestr.com', '1111111116', 'LIC1006', 'T1006', 'active'),
(7, 'Derick Marco', 'derickm@popestr.com', '1111111117', 'LIC1007', 'T1007', 'active'),
(8, 'Aloyce Damas', 'aloyced@popestr.com', '1111111118', 'LIC1008', 'T1008', 'active'),
(9, 'Ashirafu Selemani', 'ashirafus@popestr.com', '1111111119', 'LIC1009', 'T1009', 'inactive'),
(10, 'Benson Chilewa', 'bensonc@popestr.com', '1111111120', 'LIC1010', 'T1010', 'active'),
(11, 'Dickson Dickson', 'dicksond@popestr.com', '1111111121', 'LIC1011', 'T1011', 'active'),
(12, 'Edson Pancras', 'edsonp@popestr.com', '1111111122', 'LIC1012', 'T1012', 'active'),
(13, 'Mohamed Kasim', 'mohamedk@popestr.com', '1111111123', 'LIC1013', 'T1013', 'active'),
(14, 'Frank Mfinanga', 'frankm@popestr.com', '1111111124', 'LIC1014', 'T1014', 'active'),
(15, 'John Msokulu', 'johnm@popestr.com', '1111111125', 'LIC1015', 'T1015', 'inactive'),
(16, 'Najim Brown', 'najimb@popestr.com', '1111111126', 'LIC1016', 'T1016', 'active'),
(17, 'Theresia Mathew', 'theresiam@poprstr.com', '1111111127', 'LIC1017', 'T1017', 'active'),
(18, 'Rose Mary', 'rosem@popestr.com', '1111111128', 'LIC1018', 'T1018', 'active'),
(19, 'Josepha Madulu', 'josepham@popestr.com', '1111111129', 'LIC1019', 'T1019', 'inactive'),
(20, 'Kapiri Mposhi', 'kapirim@popestr.com', '1111111131', 'LIC1020', 'T1020', 'active'),
(30, 'Kephas Ndembo', 'kephasn@popestr.com', '1111111140', 'LIC1030', 'T1030', 'suspended');

-- --------------------------------------------------------

--
-- 테이블 구조 `fuel_logs`
--

CREATE TABLE `fuel_logs` (
  `id` int(11) NOT NULL,
  `truck_plate_number` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `filled_by` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `fuel_logs`
--

INSERT INTO `fuel_logs` (`id`, `truck_plate_number`, `date`, `fuel_type`, `quantity`, `cost`, `filled_by`, `notes`) VALUES
(1, 'T1001', '2025-05-01', 'Diesel', 150.00, 450000.00, 'Depot A', 'Full tank before trip'),
(2, 'T1002', '2025-05-02', 'Diesel', 145.50, 437000.00, 'Depot B', 'Refilled in Morogoro'),
(3, 'T1030', '2025-05-30', 'Diesel', 148.20, 442000.00, 'Depot C', 'Routine top up'),
(4, 'T1030', '2025-05-27', 'Diesel', 3000.00, 400000.00, 'juma ', 'for trip to silowezi ');

-- --------------------------------------------------------

--
-- 테이블 구조 `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `part_number` varchar(50) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `truck_plate_number` varchar(20) DEFAULT NULL,
  `trailer_plate_number` varchar(20) DEFAULT NULL,
  `mechanics_name` varchar(100) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `part_number`, `part_name`, `category`, `quantity`, `truck_plate_number`, `trailer_plate_number`, `mechanics_name`, `timestamp`) VALUES
(1, '8374827', 'light bulbs', 'Lighting', 2, 'T1001', 'TR1030', 'Faraj Fabian', '2025-05-28 11:48:29'),
(2, '8374827', 'tyres', 'Tires &amp; Wheels', 10, 'T1004', 'TR1005', 'Faraj Fabian', '2025-05-28 12:41:45'),
(3, '8374', 'wind screen', 'Body Parts', 1, 'T1004', NULL, 'Faraj Fabian', '2025-05-28 12:45:06');

-- --------------------------------------------------------

--
-- 테이블 구조 `maintenance`
--

CREATE TABLE `maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `truck_plate_number` varchar(50) NOT NULL,
  `maintenance_type` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) NOT NULL,
  `mechanic_username` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `maintenance`
--

INSERT INTO `maintenance` (`maintenance_id`, `truck_plate_number`, `maintenance_type`, `date`, `cost`, `status`, `mechanic_username`, `created_at`) VALUES
(1, 'T123ABC', 'Oil Change', '2025-05-20', 0.00, 'Completed', 'john.doe', '2025-05-27 05:13:34'),
(2, 'T456DEF', 'Tire Rotation', '2025-05-22', 0.00, 'Completed', 'jane.smith', '2025-05-27 05:13:34'),
(3, 'T789GHI', 'Engine Repair', '2025-05-25', 0.00, 'Pending', 'mike.brown', '2025-05-27 05:13:34'),
(4, 'T101JKL', 'Brake Service', '2025-05-18', 0.00, 'Completed', 'john.doe', '2025-05-27 05:13:34'),
(5, 'T202MNO', 'Regular Service', '2025-05-28', 0.00, 'Pending', 'jane.smith', '2025-05-27 05:13:34'),
(6, 'T303PQR', 'Transmission Check', '2025-05-15', 0.00, 'Completed', 'mike.brown', '2025-05-27 05:13:34'),
(7, 'T404STU', 'Electrical System Fix', '2025-05-26', 0.00, 'In Progress', 'john.doe', '2025-05-27 05:13:34'),
(8, 'T505VWX', 'Suspension Repair', '2025-05-29', 0.00, 'Pending', 'jane.smith', '2025-05-27 05:13:34'),
(9, 'T606YZA', 'AC Recharge', '2025-05-21', 0.00, 'Completed', 'mike.brown', '2025-05-27 05:13:34'),
(10, 'T707BCD', 'Fuel System Cleaning', '2025-05-27', 0.00, 'Pending', 'john.doe', '2025-05-27 05:13:34'),
(11, 'T1030', 'wheel alignment', '2025-05-27', 300.00, 'Pending', '', '2025-05-27 10:46:30');

-- --------------------------------------------------------

--
-- 테이블 구조 `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `log_id` int(11) NOT NULL,
  `maintenance_id` int(11) NOT NULL,
  `task_details` text NOT NULL,
  `recommendations` text DEFAULT NULL,
  `completion_date` date NOT NULL,
  `mechanic_username` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`log_id`, `maintenance_id`, `task_details`, `recommendations`, `completion_date`, `mechanic_username`, `created_at`) VALUES
(1, 2, 'performed a wheel balance test and fixed rotation issues', 'kindly schedule foe another check up after trip', '2025-05-27', 'Faraj Fabian', '2025-05-27 09:18:51');

-- --------------------------------------------------------

--
-- 테이블 구조 `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `severity` varchar(50) DEFAULT 'Info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `notifications`
--

INSERT INTO `notifications` (`notification_id`, `type`, `title`, `message`, `user_id`, `link`, `severity`, `created_at`) VALUES
(1, 'News', 'General Meeting', 'we are expecting to have a general meeting this July, all staffs are required to attend. ', 1, '', 'Info', '2025-05-27 04:55:11');

-- --------------------------------------------------------

--
-- 테이블 구조 `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `generated_by` varchar(100) DEFAULT NULL,
  `generation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `report_data` longtext DEFAULT NULL,
  `report_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `trip_logs`
--

CREATE TABLE `trip_logs` (
  `trip_id` int(11) NOT NULL,
  `trip_date` date NOT NULL,
  `driver_id` int(11) NOT NULL,
  `truck_plate_number` varchar(255) NOT NULL,
  `trip_route` varchar(100) NOT NULL,
  `distance` decimal(10,2) NOT NULL,
  `fuel_consumed` decimal(10,2) NOT NULL,
  `cargo_type` varchar(50) NOT NULL,
  `cargo_weight` decimal(10,2) NOT NULL,
  `tolls_paid` decimal(10,2) NOT NULL,
  `other_expenses` decimal(10,2) NOT NULL,
  `trip_status` enum('Planned','In Transit','Completed','Cancelled') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `trip_logs`
--

INSERT INTO `trip_logs` (`trip_id`, `trip_date`, `driver_id`, `truck_plate_number`, `trip_route`, `distance`, `fuel_consumed`, `cargo_type`, `cargo_weight`, `tolls_paid`, `other_expenses`, `trip_status`, `created_at`) VALUES
(1, '2025-05-01', 1, 'T1001', 'Dar to Lusaka', 1800.50, 150.50, 'Electronics', 2500.00, 70000.00, 20000.00, 'Completed', '2025-05-24 16:04:52'),
(2, '2025-05-02', 2, 'T1002', 'Dar to Nairobi', 1500.00, 145.00, 'Textiles', 2000.00, 65000.00, 15000.00, 'Completed', '2025-05-24 16:04:52'),
(3, '2025-05-30', 30, 'T1030', 'Dar to Kigali', 1750.00, 148.20, 'Furniture', 3000.00, 68000.00, 22000.00, 'Planned', '2025-05-24 16:04:52');

-- --------------------------------------------------------

--
-- 테이블 구조 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('Administrator','Driver','Manager','Inventory Officer','Mechanic','Accountant') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`) VALUES
(1, 'Pope\'s Tr', '$2y$10$Efy87DnKVpqiQ4S9TlbUOuTKO9OPFfa6MoDsVs8xOBWMejcWdJbfC', 'emodian16@gmail.com', 'Driver'),
(2, 'Faraj Fabian', '$2y$10$H.AMMrTAi.6tSIUYPwOA1ugTJmsO6f4oKRavwbNOajQNDfpN3VnBK', 'farajfabian@superdoll.co.tz', 'Mechanic');

-- --------------------------------------------------------

--
-- 테이블 구조 `vehicles`
--

CREATE TABLE `vehicles` (
  `truck_plate_number` varchar(255) NOT NULL,
  `truck_make` varchar(255) NOT NULL,
  `truck_model` varchar(255) NOT NULL,
  `horsepower` int(11) DEFAULT NULL,
  `trailer_make` varchar(255) DEFAULT NULL,
  `trailer_type` enum('flatbed','refrigerated','lowboy','tanker','dump','car haulers','extendable') DEFAULT NULL,
  `trailer_plate_number` varchar(255) DEFAULT NULL,
  `truck_mileage` int(11) NOT NULL,
  `next_service_mileage` int(11) NOT NULL,
  `truck_status` enum('active','on route','in maintenance') NOT NULL DEFAULT 'active',
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `vehicles`
--

INSERT INTO `vehicles` (`truck_plate_number`, `truck_make`, `truck_model`, `horsepower`, `trailer_make`, `trailer_type`, `trailer_plate_number`, `truck_mileage`, `next_service_mileage`, `truck_status`, `driver_id`) VALUES
('T1001', 'Volvo', 'FH', 500, 'Schmitz', 'flatbed', 'TR1001', 100000, 110000, 'active', 18),
('T1002', 'Scania', 'R500', 480, 'Krone', 'tanker', 'TR1002', 90000, 100000, 'active', 2),
('T1003', 'MAN', 'TGX', 460, 'Lamberet', 'refrigerated', 'TR1003', 95000, 105000, 'active', 3),
('T1004', 'Mercedes', 'Actros', 530, 'Kögel', 'lowboy', 'TR1004', 120000, 130000, 'in maintenance', 4),
('T1005', 'DAF', 'XF', 510, 'Krone', 'dump', 'TR1005', 110000, 120000, 'active', 5),
('T1030', 'Iveco', 'Stralis', 470, 'Schmitz', 'extendable', 'TR1030', 80000, 90000, 'on route', 30);

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- 테이블의 인덱스 `fuel_logs`
--
ALTER TABLE `fuel_logs`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`);

--
-- 테이블의 인덱스 `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`maintenance_id`);

--
-- 테이블의 인덱스 `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `maintenance_id` (`maintenance_id`),
  ADD KEY `mechanic_username` (`mechanic_username`);

--
-- 테이블의 인덱스 `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- 테이블의 인덱스 `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_type` (`report_type`);
ALTER TABLE `reports` ADD FULLTEXT KEY `report_name` (`report_name`,`report_data`);

--
-- 테이블의 인덱스 `trip_logs`
--
ALTER TABLE `trip_logs`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `truck_plate_number` (`truck_plate_number`);

--
-- 테이블의 인덱스 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 테이블의 인덱스 `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`truck_plate_number`),
  ADD KEY `driver_id` (`driver_id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `fuel_logs`
--
ALTER TABLE `fuel_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 테이블의 AUTO_INCREMENT `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 테이블의 AUTO_INCREMENT `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 테이블의 AUTO_INCREMENT `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `trip_logs`
--
ALTER TABLE `trip_logs`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 덤프된 테이블의 제약사항
--

--
-- 테이블의 제약사항 `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`maintenance_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_logs_ibfk_2` FOREIGN KEY (`mechanic_username`) REFERENCES `users` (`username`);

--
-- 테이블의 제약사항 `trip_logs`
--
ALTER TABLE `trip_logs`
  ADD CONSTRAINT `trip_logs_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`),
  ADD CONSTRAINT `trip_logs_ibfk_2` FOREIGN KEY (`truck_plate_number`) REFERENCES `vehicles` (`truck_plate_number`);

--
-- 테이블의 제약사항 `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
