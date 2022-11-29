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

--
-- Database: `vadaProject`
--
CREATE DATABASE IF NOT EXISTS `VadaProject_DB` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `VadaProject_DB`;

-- --------------------------------------------------------

--
-- Table structure for table `claimsdb`
--

CREATE TABLE `claimsdb` (
  `claimID` int(11) AUTO_INCREMENT PRIMARY KEY,
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
  `flagID` int(11) AUTO_INCREMENT PRIMARY KEY,
  `isRootRival` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

COMMIT;
