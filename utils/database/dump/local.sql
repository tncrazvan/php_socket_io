-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              5.5.32 - MySQL Community Server (GPL)
-- S.O. server:                  Win32
-- HeidiSQL Versione:            9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dump della struttura del database local
CREATE DATABASE IF NOT EXISTS `local` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `local`;

-- Dump della struttura di tabella local.article
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT 'unknown title',
  `content` text NOT NULL,
  `time` int(10) NOT NULL,
  `user` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'final',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_article_user` (`user`),
  CONSTRAINT `FK_article_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.article: ~0 rows (circa)
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
INSERT INTO `article` (`id`, `title`, `content`, `time`, `user`, `status`, `version`) VALUES
	(6, 'unknown title', '', 0, 'tncrazvan', 'final', 1),
	(7, 'unknown title', '', 0, 'tncrazvan', 'final', 1),
	(8, 'unknown title', '', 0, 'tncrazvan', 'final', 1),
	(9, 'unknown title', '', 0, 'tncrazvan', 'final', 1),
	(10, 'unknown title', '', 0, 'tncrazvan', 'final', 1),
	(11, 'unknown title', '', 0, 'tncrazvan', 'final', 1),
	(12, 'unknown title3333', '', 0, 'tncrazvan', 'final', 1);
/*!40000 ALTER TABLE `article` ENABLE KEYS */;

-- Dump della struttura di tabella local.local_meta
CREATE TABLE IF NOT EXISTS `local_meta` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'unknown title',
  `status` varchar(50) NOT NULL DEFAULT 'final',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_local_meta_status` (`status`),
  CONSTRAINT `FK_local_meta_article` FOREIGN KEY (`id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_local_meta_status` FOREIGN KEY (`status`) REFERENCES `status` (`status_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.local_meta: ~0 rows (circa)
/*!40000 ALTER TABLE `local_meta` DISABLE KEYS */;
INSERT INTO `local_meta` (`id`, `title`, `status`, `version`) VALUES
	(6, 'unknown title', 'final', 1),
	(7, 'unknown title', 'final', 1),
	(8, 'unknown title', 'final', 1),
	(9, 'unknown title', 'final', 1),
	(10, 'unknown title', 'final', 1),
	(11, 'unknown title', 'final', 1),
	(12, 'unknown title3333', 'final', 1);
/*!40000 ALTER TABLE `local_meta` ENABLE KEYS */;

-- Dump della struttura di tabella local.shared_meta
CREATE TABLE IF NOT EXISTS `shared_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'unknown title',
  `id_fd` varchar(254) NOT NULL,
  `shared_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'final',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `remote_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_shared_meta_status` (`status`),
  CONSTRAINT `FK_shared_meta_status` FOREIGN KEY (`status`) REFERENCES `status` (`status_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.shared_meta: ~6 rows (circa)
/*!40000 ALTER TABLE `shared_meta` DISABLE KEYS */;
INSERT INTO `shared_meta` (`id`, `title`, `id_fd`, `shared_id`, `status`, `version`, `remote_id`) VALUES
	(189, 'unknown title', 'unimil', 211, 'final', 1, 1),
	(190, 'unknown title', 'unimil', 213, 'final', 1, 2),
	(191, 'unknown title', 'unimil', 263, 'final', 1, 3),
	(192, 'unknown title', 'unimil', 286, 'final', 1, 4),
	(193, 'unknown title', 'unimil', 304, 'final', 1, 5),
	(195, 'test_title', 'unimil', 382, 'final', 1, 6);
/*!40000 ALTER TABLE `shared_meta` ENABLE KEYS */;

-- Dump della struttura di tabella local.status
CREATE TABLE IF NOT EXISTS `status` (
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`status_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.status: ~4 rows (circa)
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` (`status_name`) VALUES
	('draft'),
	('final'),
	('revisited'),
	('unavailable');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;

-- Dump della struttura di tabella local.tmp_update_log
CREATE TABLE IF NOT EXISTS `tmp_update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_tmp_update_log_local_meta` (`local_id`),
  CONSTRAINT `FK_tmp_update_log_local_meta` FOREIGN KEY (`local_id`) REFERENCES `local_meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.tmp_update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `tmp_update_log` DISABLE KEYS */;
INSERT INTO `tmp_update_log` (`id`, `local_id`) VALUES
	(58, 12);
/*!40000 ALTER TABLE `tmp_update_log` ENABLE KEYS */;

-- Dump della struttura di tabella local.update_log
CREATE TABLE IF NOT EXISTS `update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_update_log_test_table` (`local_id`),
  CONSTRAINT `FK_update_log_test_table` FOREIGN KEY (`local_id`) REFERENCES `local_meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `update_log` DISABLE KEYS */;
INSERT INTO `update_log` (`id`, `local_id`) VALUES
	(58, 12);
/*!40000 ALTER TABLE `update_log` ENABLE KEYS */;

-- Dump della struttura di tabella local.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(50) NOT NULL,
  `sha1_password` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.user: ~1 rows (circa)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `sha1_password`) VALUES
	('tncrazvan', 'qwerty');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dump della struttura di trigger local.on_article_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `on_article_insert` AFTER INSERT ON `article` FOR EACH ROW BEGIN
insert into local_meta values(new.id,new.title,new.status,new.version);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dump della struttura di trigger local.on_article_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `on_article_update` AFTER UPDATE ON `article` FOR EACH ROW BEGIN
update local_meta set id=new.id, title=new.title, status=new.status, version=new.version where id=old.id;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dump della struttura di trigger local.on_local_meta_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `on_local_meta_update` AFTER UPDATE ON `local_meta` FOR EACH ROW insert into update_log(local_id) values(NEW.id)//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
