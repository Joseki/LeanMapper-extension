-- Adminer 3.5.0 SQLite 3 dump

DROP TABLE IF EXISTS `category_closure`;
CREATE TABLE `category_closure` (
  `ancestor`  INTEGER NOT NULL,
  `descendant` INTEGER NOT NULL,
  `depth`      INTEGER NOT NULL,
  FOREIGN KEY (`ancestor`) REFERENCES `category` (`id`),
  FOREIGN KEY (`descendant`) REFERENCES `category` (`id`)
);

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id`   INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT    NOT NULL
);

INSERT INTO `category` (`id`, `name`) VALUES (1, 'PC');
INSERT INTO `category` (`id`, `name`) VALUES (2, 'Printer');
INSERT INTO `category` (`id`, `name`) VALUES (3, 'Laser');
INSERT INTO `category` (`id`, `name`) VALUES (4, 'Ink');
INSERT INTO `category` (`id`, `name`) VALUES (5, 'Mouse');
INSERT INTO `category` (`id`, `name`) VALUES (6, 'Optic');
INSERT INTO `category` (`id`, `name`) VALUES (7, 'Laser');
INSERT INTO `category` (`id`, `name`) VALUES (8, 'Keyboard');
INSERT INTO `category` (`id`, `name`) VALUES (9, 'Wired');
INSERT INTO `category` (`id`, `name`) VALUES (10, 'Wireless');
INSERT INTO `category` (`id`, `name`) VALUES (11, '3-Bottons');
INSERT INTO `category` (`id`, `name`) VALUES (12, 'More-Bottons');

INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 1, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 2, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 3, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 4, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 5, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 6, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 7, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 8, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 9, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 10, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 11, 3);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (1, 12, 3);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (2, 2, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (2, 3, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (2, 4, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (3, 3, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (4, 4, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (5, 5, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (5, 6, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (5, 7, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (5, 11, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (5, 12, 2);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (6, 6, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (6, 11, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (6, 12, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (7, 7, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (8, 8, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (8, 9, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (8, 10, 1);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (9, 9, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (10, 10, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (11, 11, 0);
INSERT INTO `category_closure` (`ancestor`, `descendant`, `depth`) VALUES (12, 12, 0);
