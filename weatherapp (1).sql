-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2023 at 07:56 AM
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
-- Database: `database`
--

-- --------------------------------------------------------

--
-- Table structure for table `weatherapp`
--

CREATE TABLE `weatherapp` (
  `date` date DEFAULT NULL,
  `temperature` int(5) DEFAULT NULL,
  `humidity` int(5) DEFAULT NULL,
  `pressure` int(5) DEFAULT NULL,
  `wind` int(5) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weatherapp`
--

INSERT INTO `weatherapp` (`date`, `temperature`, `humidity`, `pressure`, `wind`, `city`) VALUES
('2023-05-15', 7, 56, 1026, 1, 'toronto'),
('2023-05-16', 10, 58, 1008, 0, 'toronto'),
('2023-05-17', 2, 70, 1013, 2, 'toronto'),
('2023-05-18', 1, 80, 1022, 0, 'toronto'),
('2023-05-19', 5, 75, 1016, 1, 'toronto'),
('2023-05-20', 14, 96, 1011, 4, 'toronto'),
('2023-05-21', 10, 85, 1013, 1, 'toronto'),
('2023-05-15', 26, 33, 1019, 3, 'Mesa'),
('2023-05-16', 28, 30, 1012, 2, 'Mesa'),
('2023-05-17', 25, 50, 1011, 4, 'Mesa'),
('2023-05-18', 24, 53, 1009, 2, 'Mesa'),
('2023-05-19', 22, 59, 1013, 4, 'Mesa'),
('2023-05-20', 22, 51, 1014, 3, 'Mesa'),
('2023-05-21', 25, 30, 1011, 4, 'Mesa');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
