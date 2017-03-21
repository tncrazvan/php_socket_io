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

-- Dump della struttura di tabella local.test_table
CREATE TABLE IF NOT EXISTS `test_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'unknown title',
  `id_fd` varchar(254) NOT NULL,
  `remote_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shared_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'final',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.test_table: ~6 rows (circa)
/*!40000 ALTER TABLE `test_table` DISABLE KEYS */;
INSERT INTO `test_table` (`id`, `title`, `id_fd`, `remote_id`, `shared_id`, `status`) VALUES
	(33, 'unknown title', 'unipg', 0, 0, 'final'),
	(34, 'unknown title', 'unipg', 0, 0, 'final'),
	(35, 'unknown title', 'unipg', 0, 0, 'final'),
	(36, 'unknown title', 'unipg', 0, 0, 'final'),
	(37, 'unknown title', 'unipg', 0, 0, 'final'),
	(111, 'unknown title', 'unipg', 0, 0, 'final');
/*!40000 ALTER TABLE `test_table` ENABLE KEYS */;

-- Dump della struttura di tabella local.tmp_update_log
CREATE TABLE IF NOT EXISTS `tmp_update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'final',
  PRIMARY KEY (`id`),
  KEY `FK_tmp_update_log_update_log` (`local_id`),
  CONSTRAINT `FK_tmp_update_log_update_log` FOREIGN KEY (`local_id`) REFERENCES `update_log` (`local_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.tmp_update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `tmp_update_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmp_update_log` ENABLE KEYS */;

-- Dump della struttura di tabella local.update_log
CREATE TABLE IF NOT EXISTS `update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_update_log_test_table` (`local_id`),
  CONSTRAINT `FK_update_log_test_table` FOREIGN KEY (`local_id`) REFERENCES `test_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella local.update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `update_log` DISABLE KEYS */;
INSERT INTO `update_log` (`id`, `local_id`) VALUES
	(38, 33),
	(39, 34),
	(40, 35),
	(41, 36),
	(42, 37),
	(43, 111);
/*!40000 ALTER TABLE `update_log` ENABLE KEYS */;

-- Dump della struttura di trigger local.update_log
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `update_log` AFTER UPDATE ON `test_table` FOR EACH ROW insert into update_log(local_id) values(NEW.id)//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
