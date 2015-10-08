SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `category` (`id`, `name`) VALUES
(1,	'PC'),
(2,	'Printer'),
(3,	'Laser'),
(4,	'Ink'),
(5,	'Mouse'),
(6,	'Optic'),
(7,	'Laser'),
(8,	'Keyboard'),
(9,	'Wired'),
(10,	'Wireless'),
(11,	'3-Bottons'),
(12,	'More-Bottons');

DROP TABLE IF EXISTS `category_closure`;
CREATE TABLE `category_closure` (
  `ancestor` int(11) NOT NULL,
  `descendant` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  KEY `ancestor` (`ancestor`),
  KEY `descendant` (`descendant`),
  CONSTRAINT `category_closure_ibfk_1` FOREIGN KEY (`ancestor`) REFERENCES `category` (`id`),
  CONSTRAINT `category_closure_ibfk_2` FOREIGN KEY (`descendant`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES
(1,	1,	0),
(1,	2,	1),
(1,	3,	2),
(1,	4,	2),
(1,	5,	1),
(1,	6,	2),
(1,	7,	2),
(1,	8,	1),
(1,	9,	2),
(1,	10,	2),
(1,	11,	3),
(1,	12,	3),
(2,	2,	0),
(2,	3,	1),
(2,	4,	1),
(3,	3,	0),
(4,	4,	0),
(5,	5,	0),
(5,	6,	1),
(5,	7,	1),
(5,	11,	2),
(5,	12,	2),
(6,	6,	0),
(6,	11,	1),
(6,	12,	1),
(7,	7,	0),
(8,	8,	0),
(8,	9,	1),
(8,	10,	1),
(9,	9,	0),
(10,	10,	0),
(11,	11,	0),
(12,	12,	0);
