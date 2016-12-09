
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 09, 2016 at 05:40 PM
-- Server version: 10.0.22-MariaDB
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `u285953378_permi`
--

-- --------------------------------------------------------

--
-- Table structure for table `ActionPermission`
--

CREATE TABLE IF NOT EXISTS `ActionPermission` (
  `ActPerId` int(255) NOT NULL AUTO_INCREMENT,
  `UserId` int(255) NOT NULL,
  `GroupId` int(255) NOT NULL,
  `PageId` int(255) NOT NULL,
  `PermissionId` int(255) NOT NULL,
  `IsSuperUser` tinyint(1) NOT NULL,
  `ActPerAvailable` int(255) NOT NULL,
  PRIMARY KEY (`ActPerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Error`
--

CREATE TABLE IF NOT EXISTS `Error` (
  `ErrorId` int(255) NOT NULL AUTO_INCREMENT,
  `ErrorName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ErrorDescription` text COLLATE utf8_unicode_ci NOT NULL,
  `ErrorCodeName` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ErrorId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Group`
--

CREATE TABLE IF NOT EXISTS `Group` (
  `GroupId` int(255) NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`GroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Page`
--

CREATE TABLE IF NOT EXISTS `Page` (
  `PageId` int(255) NOT NULL AUTO_INCREMENT,
  `PageUrl` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `PageName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `PageAvailable` tinyint(1) NOT NULL,
  `PageMeta` text COLLATE utf8_unicode_ci NOT NULL,
  `PageIsSpecial` tinyint(1) NOT NULL,
  PRIMARY KEY (`PageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Permission`
--

CREATE TABLE IF NOT EXISTS `Permission` (
  `PermissionId` int(255) NOT NULL AUTO_INCREMENT,
  `PermissionName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`PermissionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `UserId` int(255) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `UserPassword` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `UserLevel` tinyint(1) NOT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
