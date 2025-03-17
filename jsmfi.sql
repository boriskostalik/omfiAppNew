-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: Št 10.Okt 2024, 10:18
-- Verzia serveru: 10.11.6-MariaDB-0+deb12u1
-- Verzia PHP: 8.2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `casopis`
--
CREATE DATABASE IF NOT EXISTS `casopis` DEFAULT CHARACTER SET cp1250 COLLATE cp1250_general_ci;
USE `casopis`;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `aigaiongeneral`
--

CREATE TABLE IF NOT EXISTS `aigaiongeneral` (
  `version` varchar(10) NOT NULL DEFAULT '',
  `releaseversion` varchar(10) NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `author`
--

CREATE TABLE IF NOT EXISTS `author` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `surname` varchar(255) NOT NULL,
  `von` varchar(255) NOT NULL DEFAULT '',
  `firstname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `institute` varchar(255) NOT NULL,
  `specialchars` enum('FALSE','TRUE') NOT NULL DEFAULT 'FALSE',
  `cleanname` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `availablerights`
--

CREATE TABLE IF NOT EXISTS `availablerights` (
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `setting` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `institutions`
--

CREATE TABLE IF NOT EXISTS `institutions` (
  `institution_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `address` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `acronym` varchar(10) NOT NULL,
  PRIMARY KEY (`institution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `notecrossrefid`
--

CREATE TABLE IF NOT EXISTS `notecrossrefid` (
  `note_id` int(10) NOT NULL,
  `xref_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `theme` varchar(255) NOT NULL DEFAULT 'darkdefault',
  `newwindowforatt` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `summarystyle` varchar(255) NOT NULL DEFAULT 'author',
  `authordisplaystyle` varchar(5) NOT NULL DEFAULT 'vlf',
  `liststyle` smallint(6) NOT NULL DEFAULT 0,
  `login` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `initials` varchar(10) DEFAULT NULL,
  `firstname` varchar(20) DEFAULT NULL,
  `betweenname` varchar(10) DEFAULT NULL,
  `surname` varchar(30) DEFAULT NULL,
  `csname` varchar(10) DEFAULT NULL,
  `abbreviation` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(30) NOT NULL DEFAULT '',
  `u_rights` tinyint(2) NOT NULL DEFAULT 0,
  `lastreviewedtopic` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `personpublicationmark`
--

CREATE TABLE IF NOT EXISTS `personpublicationmark` (
  `pub_id` int(10) NOT NULL DEFAULT 0,
  `person_id` int(11) NOT NULL DEFAULT 0,
  `mark` enum('1','2','3','4','5') NOT NULL DEFAULT '3',
  `read` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`pub_id`,`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `personpublicationnote`
--

CREATE TABLE IF NOT EXISTS `personpublicationnote` (
  `note_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `person_id` int(11) NOT NULL DEFAULT 0,
  `rights` enum('public','private') NOT NULL DEFAULT 'public',
  `text` text DEFAULT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `persontopic`
--

CREATE TABLE IF NOT EXISTS `persontopic` (
  `collapsed` int(2) NOT NULL DEFAULT 0,
  `person_id` int(10) NOT NULL DEFAULT 0,
  `topic_id` int(10) NOT NULL DEFAULT 0,
  `star` int(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`person_id`,`topic_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `persontopic_temp`
--

CREATE TABLE IF NOT EXISTS `persontopic_temp` (
  `collapsed` int(2) NOT NULL DEFAULT 0,
  `person_id` int(10) NOT NULL DEFAULT 0,
  `topic_id` int(10) NOT NULL DEFAULT 0,
  `star` int(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`person_id`,`topic_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `publication`
--

CREATE TABLE IF NOT EXISTS `publication` (
  `pub_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entered_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `year` varchar(12) NOT NULL DEFAULT '0000',
  `actualyear` varchar(12) NOT NULL DEFAULT '0000',
  `title` text NOT NULL,
  `title_eng` text DEFAULT NULL,
  `mesc` varchar(50) DEFAULT NULL,
  `bibtex_id` varchar(255) NOT NULL,
  `pub_type` varchar(255) NOT NULL DEFAULT '',
  `type` enum('Article','Book','Booklet','Inbook','Incollection','Inproceedings','Manual','Mastersthesis','Misc','Phdthesis','Proceedings','Techreport','Unpublished') DEFAULT NULL,
  `survey` tinyint(1) NOT NULL DEFAULT 0,
  `mark` int(11) NOT NULL DEFAULT 5,
  `series` varchar(64) NOT NULL DEFAULT '',
  `volume` varchar(16) NOT NULL DEFAULT '',
  `publisher` varchar(127) NOT NULL DEFAULT '',
  `location` varchar(127) NOT NULL DEFAULT '',
  `issn` varchar(32) NOT NULL DEFAULT '',
  `isbn` varchar(32) NOT NULL DEFAULT '',
  `firstpage` varchar(10) NOT NULL DEFAULT '0',
  `lastpage` varchar(10) NOT NULL DEFAULT '0',
  `journal` varchar(255) NOT NULL DEFAULT '',
  `booktitle` varchar(255) NOT NULL DEFAULT '',
  `number` varchar(255) NOT NULL DEFAULT '',
  `institution` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `chapter` varchar(10) NOT NULL DEFAULT '0',
  `edition` varchar(255) NOT NULL DEFAULT '',
  `howpublished` varchar(255) NOT NULL DEFAULT '',
  `month` varchar(255) NOT NULL DEFAULT '',
  `organization` varchar(255) NOT NULL DEFAULT '',
  `school` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `keywords` text NOT NULL,
  `abstract` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `doi` varchar(255) NOT NULL DEFAULT '',
  `crossref` varchar(255) NOT NULL,
  `namekey` varchar(255) NOT NULL,
  `userfields` text NOT NULL,
  `specialchars` enum('FALSE','TRUE') NOT NULL DEFAULT 'FALSE',
  `cleanjournal` varchar(255) NOT NULL DEFAULT '',
  `cleantitle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `publicationauthor`
--

CREATE TABLE IF NOT EXISTS `publicationauthor` (
  `pub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rank` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `is_editor` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`pub_id`,`author`,`is_editor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `publicationcitation`
--

CREATE TABLE IF NOT EXISTS `publicationcitation` (
  `id_ref` int(11) NOT NULL AUTO_INCREMENT,
  `cited_id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `cas` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `publicationfile`
--

CREATE TABLE IF NOT EXISTS `publicationfile` (
  `pub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `location` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `ismain` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `person_id` int(11) NOT NULL DEFAULT 0,
  `mime` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `isremote` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`location`,`pub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `topic`
--

CREATE TABLE IF NOT EXISTS `topic` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `topicpublication`
--

CREATE TABLE IF NOT EXISTS `topicpublication` (
  `topic_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`topic_id`,`pub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `topictopiclink`
--

CREATE TABLE IF NOT EXISTS `topictopiclink` (
  `source_topic_id` int(10) NOT NULL DEFAULT 0,
  `target_topic_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`source_topic_id`),
  KEY `target_topic_id` (`target_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci COMMENT='Hierarchy of topics; typed relations';

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `userrights`
--

CREATE TABLE IF NOT EXISTS `userrights` (
  `user_id` int(10) NOT NULL,
  `right_name` varchar(20) NOT NULL,
  PRIMARY KEY (`right_name`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
