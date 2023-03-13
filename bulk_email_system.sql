-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 12, 2023 at 03:52 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bulk_email_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

DROP TABLE IF EXISTS `lists`;
CREATE TABLE IF NOT EXISTS `lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(50) NOT NULL,
  `list_file` varchar(100) NOT NULL,
  `total_emails` int(11) NOT NULL,
  `date_created` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

DROP TABLE IF EXISTS `progress`;
CREATE TABLE IF NOT EXISTS `progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `track_id` varchar(50) NOT NULL,
  `total_sent` int(11) DEFAULT NULL,
  `total_left` int(11) DEFAULT NULL,
  `current_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scripts`
--

DROP TABLE IF EXISTS `scripts`;
CREATE TABLE IF NOT EXISTS `scripts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `track_id` varchar(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `temp_id` int(11) NOT NULL,
  `scr_interval` int(1) NOT NULL,
  `has_started` varchar(6) NOT NULL DEFAULT 'No',
  `time_started` varchar(20) DEFAULT NULL,
  `time_stopped` varchar(20) DEFAULT NULL,
  `is_completed` varchar(3) DEFAULT 'No',
  `time_completed` varchar(20) DEFAULT NULL,
  `date_created` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_name` varchar(50) NOT NULL,
  `temp_desc` varchar(100) NOT NULL,
  `temp_file` varchar(100) NOT NULL,
  `date_created` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `secret` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
