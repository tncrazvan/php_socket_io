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
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `title` varchar(50) NOT NULL DEFAULT 'unknown title',
  `content` text NOT NULL,
  `time` int(10) NOT NULL,
  `user` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'final',
  `has_zip` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`version`),
  KEY `FK_article_user` (`user`),
  KEY `FK_article_status` (`status`),
  CONSTRAINT `FK_article_status` FOREIGN KEY (`status`) REFERENCES `status` (`status_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_article_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.article: ~1 rows (circa)
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
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

-- Dump dei dati della tabella local.local_meta: ~1 rows (circa)
/*!40000 ALTER TABLE `local_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `local_meta` ENABLE KEYS */;

-- Dump della struttura di tabella local.revisit
CREATE TABLE IF NOT EXISTS `revisit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` int(10) unsigned NOT NULL DEFAULT '0',
  `revisit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_revisit_article` (`reference`),
  KEY `FK_revisit_article_2` (`revisit`),
  CONSTRAINT `FK_revisit_article` FOREIGN KEY (`reference`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_revisit_article_2` FOREIGN KEY (`revisit`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COMMENT='list of revisits of articles';

-- Dump dei dati della tabella local.revisit: ~0 rows (circa)
/*!40000 ALTER TABLE `revisit` DISABLE KEYS */;
/*!40000 ALTER TABLE `revisit` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.shared_meta: ~6 rows (circa)
/*!40000 ALTER TABLE `shared_meta` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.tmp_update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `tmp_update_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmp_update_log` ENABLE KEYS */;

-- Dump della struttura di tabella local.update_log
CREATE TABLE IF NOT EXISTS `update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_update_log_test_table` (`local_id`),
  CONSTRAINT `FK_update_log_test_table` FOREIGN KEY (`local_id`) REFERENCES `local_meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `update_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `update_log` ENABLE KEYS */;

-- Dump della struttura di tabella local.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(50) NOT NULL,
  `sha1_password` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.user: ~3 rows (circa)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
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
