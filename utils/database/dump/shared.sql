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

-- Dump della struttura di tabella shared.test_table
CREATE TABLE IF NOT EXISTS `test_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'unknown title',
  `remote_id` int(10) NOT NULL,
  `id_fd` varchar(254) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'final',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella shared.test_table: ~5 rows (circa)
/*!40000 ALTER TABLE `test_table` DISABLE KEYS */;
INSERT INTO `test_table` (`id`, `title`, `remote_id`, `id_fd`, `status`) VALUES
	(211, 'unknown title', 1, 'unimil', 'final'),
	(213, 'unknown title', 2, 'unimil', 'final'),
	(263, 'unknown title', 3, 'unimil', 'final'),
	(286, 'unknown title', 4, 'unimil', 'final'),
	(304, 'unknown title', 5, 'unimil', 'final');
/*!40000 ALTER TABLE `test_table` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
