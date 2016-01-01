-- Adminer 4.2.0 MySQL dump

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `id` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `person1` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `person2` varchar(25) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person1` (`person1`),
  KEY `person2` (`person2`),
  CONSTRAINT `person_ibfk_2` FOREIGN KEY (`person2`) REFERENCES `person` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `person_ibfk_1` FOREIGN KEY (`person1`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2015-12-22 21:05:04
