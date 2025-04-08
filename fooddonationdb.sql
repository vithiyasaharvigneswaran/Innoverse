-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 01:06 PM
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
-- Database: `fooddonationdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `donationrequest`
--

CREATE TABLE `donationrequest` (
  `RequestID` int(11) NOT NULL,
  `RegNo` int(11) NOT NULL,
  `FoodID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL CHECK (`Quantity` > 0),
  `Status` enum('Pending','Approved','Rejected') NOT NULL,
  `RequestDate` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventorganizer`
--

CREATE TABLE `eventorganizer` (
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Rating` int(11) NOT NULL CHECK (`Rating` between 1 and 5),
  `Comments` text DEFAULT NULL,
  `Date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homecharity`
--

CREATE TABLE `homecharity` (
  `RegNo` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surplusfood`
--

CREATE TABLE `surplusfood` (
  `FoodID` int(11) NOT NULL,
  `EventOrganizerID` int(11) NOT NULL,
  `FoodType` varchar(255) NOT NULL,
  `NoOfParcel` int(11) NOT NULL CHECK (`NoOfParcel` > 0),
  `Date` date NOT NULL,
  `Status` enum('Available','Claimed','Expired') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `Location` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donationrequest`
--
ALTER TABLE `donationrequest`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `RegNo` (`RegNo`),
  ADD KEY `FoodID` (`FoodID`);

--
-- Indexes for table `eventorganizer`
--
ALTER TABLE `eventorganizer`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `homecharity`
--
ALTER TABLE `homecharity`
  ADD PRIMARY KEY (`RegNo`),
  ADD UNIQUE KEY `UserID` (`UserID`);

--
-- Indexes for table `surplusfood`
--
ALTER TABLE `surplusfood`
  ADD PRIMARY KEY (`FoodID`),
  ADD KEY `EventOrganizerID` (`EventOrganizerID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Phone` (`Phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donationrequest`
--
ALTER TABLE `donationrequest`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homecharity`
--
ALTER TABLE `homecharity`
  MODIFY `RegNo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `surplusfood`
--
ALTER TABLE `surplusfood`
  MODIFY `FoodID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donationrequest`
--
ALTER TABLE `donationrequest`
  ADD CONSTRAINT `donationrequest_ibfk_1` FOREIGN KEY (`RegNo`) REFERENCES `homecharity` (`RegNo`) ON DELETE CASCADE,
  ADD CONSTRAINT `donationrequest_ibfk_2` FOREIGN KEY (`FoodID`) REFERENCES `surplusfood` (`FoodID`) ON DELETE CASCADE;

--
-- Constraints for table `eventorganizer`
--
ALTER TABLE `eventorganizer`
  ADD CONSTRAINT `eventorganizer_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `homecharity`
--
ALTER TABLE `homecharity`
  ADD CONSTRAINT `homecharity_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `surplusfood`
--
ALTER TABLE `surplusfood`
  ADD CONSTRAINT `surplusfood_ibfk_1` FOREIGN KEY (`EventOrganizerID`) REFERENCES `eventorganizer` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
