-- BDU Transportation Schedule & Tracking System Database Schema

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `bdu_transport`
-- CREATE DATABASE IF NOT EXISTS `bdu_transport`;
-- USE `bdu_transport`;

-- --------------------------------------------------------

-- Table: `users` (Admins/Staff)
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL, -- Hash passwords!
  `role` enum('admin','staff','student') NOT NULL DEFAULT 'student',
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin (Password: admin123) - REPLACE IN PRODUCTION
INSERT INTO `users` (`username`, `password`, `role`, `full_name`) VALUES
('admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'admin', 'System Administrator');

-- --------------------------------------------------------

-- Table: `buses`
CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_number` varchar(20) NOT NULL, -- e.g., BDU-001
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_contact` varchar(20) DEFAULT NULL,
  `capacity` int(11) DEFAULT 50,
  `status` enum('active','maintenance','inactive') DEFAULT 'active',
  PRIMARY KEY (`bus_id`),
  UNIQUE KEY `bus_number` (`bus_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `buses` (`bus_number`, `driver_name`, `status`) VALUES
('BDU-101', 'Abebe Bikila', 'active'),
('BDU-102', 'Kenenisa Bekele', 'active'),
('BDU-103', 'Haile Gebrselassie', 'maintenance');

-- --------------------------------------------------------

-- Table: `routes`
CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL AUTO_INCREMENT,
  `route_name` varchar(100) NOT NULL, -- e.g., Main Campus - City Center
  `description` text DEFAULT NULL,
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `routes` (`route_name`, `description`) VALUES
('Poly Campus - Zenzelma Campus', 'Express route from Poly Campus to Zenzelma Campus'),
('Poly Campus - Peda Campus', 'Route via Ring Road to Peda Campus'),
('Poly Campus - Selam Campus', 'Morning pickup from Selam Campus');

-- --------------------------------------------------------

-- Table: `pickup_points` (Stops)
CREATE TABLE `pickup_points` (
  `point_id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `sequence_order` int(11) NOT NULL, -- Order in the route
  PRIMARY KEY (`point_id`),
  FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pickup_points` (`route_id`, `location_name`, `sequence_order`) VALUES
(1, 'Poly Campus Gate', 1),
(1, 'City Center', 2),
(1, 'Zenzelma Campus', 3),
(2, 'Poly Campus Gate', 1),
(2, 'Kebele 10', 2),
(2, 'Peda Campus', 3);

-- --------------------------------------------------------

-- Table: `schedules`
CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `direction` enum('outbound','inbound') DEFAULT 'outbound',
  `departure_time` time NOT NULL,
  `arrival_time` time DEFAULT NULL, -- Estimated
  `operating_days` varchar(50) DEFAULT 'Mon,Tue,Wed,Thu,Fri', -- Simple text for now, or use bitmask
  PRIMARY KEY (`schedule_id`),
  FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`) ON DELETE CASCADE,
  FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `schedules` (`bus_id`, `route_id`, `departure_time`, `arrival_time`) VALUES
(1, 1, '07:30:00', '08:15:00'),
(2, 2, '07:45:00', '08:30:00'),
(1, 1, '16:30:00', '17:15:00');

-- --------------------------------------------------------

-- Table: `announcements`
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','alert') DEFAULT 'info',
  `is_active` boolean DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `date_posted` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`announcement_id`),
  FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `announcements` (`title`, `message`, `type`, `date_posted`) VALUES
('Schedule Change', 'Bus BDU-101 will depart 10 minutes late today due to traffic.', 'warning', NOW()),
('New Route Added', 'We have added a new route to Saris starting next week.', 'info', NOW());

COMMIT;
