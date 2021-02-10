-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2021 at 04:47 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `location_service`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `zipcode` varchar(4) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  `region` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`zipcode`, `name_en`, `name_de`, `region`) VALUES
('1219', 'Chatelaine', '', 'GE'),
('1233', 'Bernex', '', 'GE'),
('1483', 'Vesin', '', 'FR'),
('1730', 'Ecuvillens', '', 'FR'),
('3216', 'Agriswil', '', 'FR');

-- --------------------------------------------------------

--
-- Table structure for table `location_services`
--

CREATE TABLE `location_services` (
  `location_zipcode` varchar(4) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `location_services`
--

INSERT INTO `location_services` (`location_zipcode`, `service_id`) VALUES
('1219', 1),
('1233', 1),
('1483', 2),
('1730', 1),
('1730', 3);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name_en`, `name_de`) VALUES
(1, 'Garbage calendar', ''),
(2, 'Gemeinde news', ''),
(3, 'Weather notifications', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`zipcode`) USING BTREE;

--
-- Indexes for table `location_services`
--
ALTER TABLE `location_services`
  ADD PRIMARY KEY (`location_zipcode`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `location_services`
--
ALTER TABLE `location_services`
  ADD CONSTRAINT `location_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `location_services_ibfk_3` FOREIGN KEY (`location_zipcode`) REFERENCES `locations` (`zipcode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
