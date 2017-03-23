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


-- Dump della struttura del database shared
CREATE DATABASE IF NOT EXISTS `shared` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `shared`;

-- Dump della struttura di tabella shared.fed
CREATE TABLE IF NOT EXISTS `fed` (
  `id_fd` varchar(50) NOT NULL,
  PRIMARY KEY (`id_fd`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella shared.fed: ~2 rows (circa)
/*!40000 ALTER TABLE `fed` DISABLE KEYS */;
INSERT INTO `fed` (`id_fd`) VALUES
	('unimil'),
	('unipg');
/*!40000 ALTER TABLE `fed` ENABLE KEYS */;

-- Dump della struttura di tabella shared.shared_meta
CREATE TABLE IF NOT EXISTS `shared_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'unknown title',
  `remote_id` int(10) NOT NULL,
  `id_fd` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'final',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_shared_meta_fed` (`id_fd`),
  KEY `FK_shared_meta_status` (`status`),
  CONSTRAINT `FK_shared_meta_status` FOREIGN KEY (`status`) REFERENCES `status` (`status_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_shared_meta_fed` FOREIGN KEY (`id_fd`) REFERENCES `fed` (`id_fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella shared.shared_meta: ~17 rows (circa)
/*!40000 ALTER TABLE `shared_meta` DISABLE KEYS */;
INSERT INTO `shared_meta` (`id`, `title`, `remote_id`, `id_fd`, `status`, `version`) VALUES
	(211, 'unknown title', 1, 'unimil', 'final', 1),
	(213, 'unknown title', 2, 'unimil', 'final', 1),
	(263, 'unknown title', 3, 'unimil', 'final', 1),
	(286, 'unknown title', 4, 'unimil', 'final', 1),
	(304, 'unknown title', 5, 'unimil', 'final', 1),
	(378, 'test title', 6, 'unimil', 'final', 1),
	(382, 'test_title', 6, 'unimil', 'final', 1),
	(424, 'unknown title', 6, 'unipg', 'final', 1),
	(425, 'unknown title', 7, 'unipg', 'final', 1),
	(426, 'unknown title', 8, 'unipg', 'final', 1),
	(427, 'unknown title', 9, 'unipg', 'final', 1),
	(428, 'unknown title', 10, 'unipg', 'final', 1),
	(429, 'unknown title', 11, 'unipg', 'final', 1),
	(433, 'unknown title3333', 12, 'unipg', 'final', 1);
/*!40000 ALTER TABLE `shared_meta` ENABLE KEYS */;

-- Dump della struttura di tabella shared.status
CREATE TABLE IF NOT EXISTS `status` (
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`status_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella shared.status: ~3 rows (circa)
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` (`status_name`) VALUES
	('final'),
	('revisited'),
	('unavailable');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
