SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `tmp_pages_articles_tmp` (
  `hash` char(32) NOT NULL,
  `id` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  PRIMARY KEY (`hash`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
