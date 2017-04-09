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


-- Dump della struttura del database lom_shared
CREATE DATABASE IF NOT EXISTS `lom_shared` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `lom_shared`;

-- Dump della struttura di tabella lom_shared.annotation
CREATE TABLE IF NOT EXISTS `annotation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.annotation: ~0 rows (circa)
/*!40000 ALTER TABLE `annotation` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.classification
CREATE TABLE IF NOT EXISTS `classification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purpose` text,
  `taxon_path` int(10) unsigned DEFAULT NULL,
  `description` text,
  `keyword` text,
  PRIMARY KEY (`id`),
  KEY `taxon_path` (`taxon_path`),
  CONSTRAINT `classification_ibfk_1` FOREIGN KEY (`taxon_path`) REFERENCES `taxon_path` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.classification: ~0 rows (circa)
/*!40000 ALTER TABLE `classification` DISABLE KEYS */;
/*!40000 ALTER TABLE `classification` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.contribute
CREATE TABLE IF NOT EXISTS `contribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` enum('creator','validator') NOT NULL,
  `entity` text,
  `date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.contribute: ~0 rows (circa)
/*!40000 ALTER TABLE `contribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `contribute` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.educational
CREATE TABLE IF NOT EXISTS `educational` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `interactivity_type` enum('active','expositive','mixed') NOT NULL,
  `learning_resource_type` enum('exercise','simulation','questionnaire','diagram','figure','graph','index','slide','table','narrative text','exam','experiment','problem statement','self assesment','lecture') NOT NULL,
  `semantic_density` enum('very low','low','medium','high','very high') NOT NULL,
  `intended_end_user_role` enum('teacher','author','learner','manager') NOT NULL,
  `context` enum('school','higher education','training','other') NOT NULL,
  `typica_age_range` int(10) unsigned DEFAULT NULL,
  `difficulty` enum('very easy','easy','medium','difficult','very difficult') NOT NULL,
  `typical_learning_time` int(10) unsigned DEFAULT NULL,
  `description` text,
  `language` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.educational: ~0 rows (circa)
/*!40000 ALTER TABLE `educational` DISABLE KEYS */;
/*!40000 ALTER TABLE `educational` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.general
CREATE TABLE IF NOT EXISTS `general` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_fd` varchar(50) DEFAULT NULL,
  `remote_id` int(10) unsigned DEFAULT NULL,
  `status` enum('draft','final','revisited','unavailable') DEFAULT NULL,
  `title` text,
  `language` varchar(50) DEFAULT NULL,
  `description` text,
  `keyword` text,
  `coverage` text,
  `structure` text,
  `aggregation_level` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_fd_remote_id` (`id_fd`,`remote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.general: ~1 rows (circa)
/*!40000 ALTER TABLE `general` DISABLE KEYS */;
INSERT INTO `general` (`id`, `id_fd`, `remote_id`, `status`, `title`, `language`, `description`, `keyword`, `coverage`, `structure`, `aggregation_level`) VALUES
	(49, 'unipg', 9, 'final', 'qwrq updated2235', 'it', 'q23r45q', '3qw25rt', 'tr53w2', 'qw2345r', 1),
	(51, 'unipg', 11, 'final', 'qwrq updated2235', 'it', 'q23r45q', '3qw25rt', 'tr53w2', 'qw2345r', 1),
	(52, 'unige', 1, 'final', 'qwrq updated2235', 'it', 'q23r45q', '3qw25rt', 'tr53w2', 'qw2345r', 1);
/*!40000 ALTER TABLE `general` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.identifier
CREATE TABLE IF NOT EXISTS `identifier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catalog` text,
  `entry` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.identifier: ~0 rows (circa)
/*!40000 ALTER TABLE `identifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `identifier` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.meta_metadata
CREATE TABLE IF NOT EXISTS `meta_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` int(10) unsigned DEFAULT NULL,
  `contribute` int(10) unsigned DEFAULT NULL,
  `metadata_schema` varchar(255) DEFAULT 'LOMv1.0',
  `language` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  KEY `contribute` (`contribute`),
  CONSTRAINT `meta_metadata_ibfk_1` FOREIGN KEY (`identifier`) REFERENCES `identifier` (`id`),
  CONSTRAINT `meta_metadata_ibfk_2` FOREIGN KEY (`contribute`) REFERENCES `contribute` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.meta_metadata: ~0 rows (circa)
/*!40000 ALTER TABLE `meta_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `meta_metadata` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.orcomposite
CREATE TABLE IF NOT EXISTS `orcomposite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `minimum_version` varchar(255) DEFAULT NULL,
  `maximum_version` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.orcomposite: ~0 rows (circa)
/*!40000 ALTER TABLE `orcomposite` DISABLE KEYS */;
/*!40000 ALTER TABLE `orcomposite` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.relation
CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kind` varchar(255) DEFAULT NULL,
  `resource` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource` (`resource`),
  CONSTRAINT `relation_ibfk_1` FOREIGN KEY (`resource`) REFERENCES `resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.relation: ~0 rows (circa)
/*!40000 ALTER TABLE `relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `relation` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.requirement
CREATE TABLE IF NOT EXISTS `requirement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orcomposite` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orcomposite` (`orcomposite`),
  CONSTRAINT `requirement_ibfk_1` FOREIGN KEY (`orcomposite`) REFERENCES `orcomposite` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.requirement: ~0 rows (circa)
/*!40000 ALTER TABLE `requirement` DISABLE KEYS */;
/*!40000 ALTER TABLE `requirement` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.resource
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` int(10) unsigned DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`identifier`) REFERENCES `identifier` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.resource: ~0 rows (circa)
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.rights
CREATE TABLE IF NOT EXISTS `rights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cost` enum('yes','no') NOT NULL,
  `copyright_and_other_restrictions` longtext NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.rights: ~0 rows (circa)
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.taxon
CREATE TABLE IF NOT EXISTS `taxon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry` text,
  `description` text,
  `keyword` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.taxon: ~0 rows (circa)
/*!40000 ALTER TABLE `taxon` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxon` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.taxon_path
CREATE TABLE IF NOT EXISTS `taxon_path` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` text NOT NULL,
  `taxon` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `taxon` (`taxon`),
  CONSTRAINT `taxon_path_ibfk_1` FOREIGN KEY (`taxon`) REFERENCES `taxon` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.taxon_path: ~0 rows (circa)
/*!40000 ALTER TABLE `taxon_path` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxon_path` ENABLE KEYS */;

-- Dump della struttura di tabella lom_shared.technical
CREATE TABLE IF NOT EXISTS `technical` (
  `id` int(10) unsigned NOT NULL,
  `format` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `location` longtext,
  `requirement` int(10) unsigned DEFAULT NULL,
  `installation_remarks` text,
  `other_platform_requirements` text,
  `duration` int(10) unsigned DEFAULT NULL,
  KEY `requirement` (`requirement`),
  CONSTRAINT `technical_ibfk_1` FOREIGN KEY (`requirement`) REFERENCES `requirement` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_shared.technical: ~0 rows (circa)
/*!40000 ALTER TABLE `technical` DISABLE KEYS */;
/*!40000 ALTER TABLE `technical` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
