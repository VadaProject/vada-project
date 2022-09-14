-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2021 at 01:28 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vadaProject`
--

-- --------------------------------------------------------

--
-- Table structure for table `claimsdb`
--

CREATE TABLE `claimsdb` (
  `claimID` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `targetP` varchar(255) NOT NULL,
  `supportMeans` varchar(255) NOT NULL,
  `supportID` varchar(255) NOT NULL,
  `example` varchar(255) NOT NULL,
  `URL` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `thesisST` varchar(255) NOT NULL,
  `reasonST` varchar(255) NOT NULL,
  `ruleST` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `active` int(1) NOT NULL,
  `vidtimestamp` text NOT NULL,
  `citation` text NOT NULL,
  `transcription` text NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `COS` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `flagsdb`
--

CREATE TABLE `flagsdb` (
  `claimIDFlagged` int(11) NOT NULL,
  `flagType` varchar(255) NOT NULL,
  `claimIDFlagger` int(11) NOT NULL,
  `flagID` int(11) NOT NULL,
  `isRootRival` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `claimsdb`
--
ALTER TABLE `claimsdb`
  ADD PRIMARY KEY (`claimID`);

--
-- Indexes for table `flagsdb`
--
ALTER TABLE `flagsdb`
  ADD PRIMARY KEY (`flagID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `claimsdb`
--
ALTER TABLE `claimsdb`
  MODIFY `claimID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1389;

--
-- AUTO_INCREMENT for table `flagsdb`
--
ALTER TABLE `flagsdb`
  MODIFY `flagID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8632;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
