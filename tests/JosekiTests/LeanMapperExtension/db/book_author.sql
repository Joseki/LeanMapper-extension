DROP TABLE IF EXISTS `book`;
DROP TABLE IF EXISTS `author`;

CREATE TABLE `author` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(50) NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `book` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(50) NOT NULL,
  `author` int(11) NOT NULL,
  FOREIGN KEY (`author`) REFERENCES `author` (`id`)
) ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

INSERT INTO `author` (`id`, `name`) VALUES ('1', 'Terry Goodkind');
INSERT INTO `book` (`id`, `name`, `author`) VALUES ('1', 'První čarodějovo pravidlo', '1');
INSERT INTO `book` (`id`, `name`, `author`) VALUES ('2', 'Kámen slz', '1');
