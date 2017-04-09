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


-- Dump della struttura del database lom_local
CREATE DATABASE IF NOT EXISTS `lom_local` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `lom_local`;

-- Dump della struttura di tabella lom_local.annotation
CREATE TABLE IF NOT EXISTS `annotation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.annotation: ~0 rows (circa)
/*!40000 ALTER TABLE `annotation` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.classification
CREATE TABLE IF NOT EXISTS `classification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purpose` enum('discipline','idea','prerequisite','educational object','accessibility','restrictions','educational level','skill level','security level','competency') NOT NULL,
  `taxon_path` int(10) unsigned DEFAULT NULL,
  `description` text,
  `keyword` text,
  PRIMARY KEY (`id`),
  KEY `taxon_path` (`taxon_path`),
  CONSTRAINT `classification_ibfk_1` FOREIGN KEY (`taxon_path`) REFERENCES `taxon_path` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.classification: ~0 rows (circa)
/*!40000 ALTER TABLE `classification` DISABLE KEYS */;
/*!40000 ALTER TABLE `classification` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.contribute
CREATE TABLE IF NOT EXISTS `contribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_fd` varchar(50) NOT NULL,
  `remote_id` int(10) unsigned NOT NULL,
  `role` enum('author','publisher','unknown','initiator','terminator','validator','editor','graphical designer','technical validator','educational validator','script writer','instructional designer','subject matter expert') NOT NULL,
  `entity` text,
  `date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_contribute_general` (`id_fd`,`remote_id`),
  CONSTRAINT `FK_contribute_general` FOREIGN KEY (`id_fd`, `remote_id`) REFERENCES `general` (`id_fd`, `remote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.contribute: ~0 rows (circa)
/*!40000 ALTER TABLE `contribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `contribute` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.educational
CREATE TABLE IF NOT EXISTS `educational` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_fd` varchar(50) NOT NULL,
  `remote_id` int(10) unsigned NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `FK_educational_general` (`id_fd`,`remote_id`),
  CONSTRAINT `FK_educational_general` FOREIGN KEY (`id_fd`, `remote_id`) REFERENCES `general` (`id_fd`, `remote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.educational: ~1 rows (circa)
/*!40000 ALTER TABLE `educational` DISABLE KEYS */;
INSERT INTO `educational` (`id`, `id_fd`, `remote_id`, `interactivity_type`, `learning_resource_type`, `semantic_density`, `intended_end_user_role`, `context`, `typica_age_range`, `difficulty`, `typical_learning_time`, `description`, `language`) VALUES
	(1, 'unipg', 9, 'active', 'exercise', 'very low', 'teacher', 'school', 21, 'very easy', 123421, 'description test', 'it');
/*!40000 ALTER TABLE `educational` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.general
CREATE TABLE IF NOT EXISTS `general` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_fd` varchar(50) DEFAULT NULL,
  `remote_id` int(10) unsigned DEFAULT NULL,
  `shared_id` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.general: ~1 rows (circa)
/*!40000 ALTER TABLE `general` DISABLE KEYS */;
INSERT INTO `general` (`id`, `id_fd`, `remote_id`, `shared_id`, `status`, `title`, `language`, `description`, `keyword`, `coverage`, `structure`, `aggregation_level`) VALUES
	(9, 'unipg', 9, NULL, 'final', 'qwrq updated2235', 'it', 'q23r45q', '3qw25rt', 'tr53w2', 'qw2345r', 1),
	(11, 'unipg', 11, NULL, 'final', 'qwrq updated2235', 'it', 'q23r45q', '3qw25rt', 'tr53w2', 'qw2345r', 1),
	(12, 'unige', 1, 52, 'final', 'qwrq updated2235', 'it', 'q23r45q', '3qw25rt', 'tr53w2', 'qw2345r', 1);
/*!40000 ALTER TABLE `general` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.identifier
CREATE TABLE IF NOT EXISTS `identifier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catalog` text,
  `entry` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.identifier: ~0 rows (circa)
/*!40000 ALTER TABLE `identifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `identifier` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.meta_metadata
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

-- Dump dei dati della tabella lom_local.meta_metadata: ~0 rows (circa)
/*!40000 ALTER TABLE `meta_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `meta_metadata` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.orcomposite
CREATE TABLE IF NOT EXISTS `orcomposite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `minimum_version` varchar(255) DEFAULT NULL,
  `maximum_version` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.orcomposite: ~0 rows (circa)
/*!40000 ALTER TABLE `orcomposite` DISABLE KEYS */;
/*!40000 ALTER TABLE `orcomposite` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.relation
CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kind` enum('ispartof','haspart','isversionof','isformatof','hasformat','references','isreferencedby','isbasedon','isbasisfor','requires','isrequiredby') NOT NULL,
  `resource` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource` (`resource`),
  CONSTRAINT `relation_ibfk_1` FOREIGN KEY (`resource`) REFERENCES `resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.relation: ~0 rows (circa)
/*!40000 ALTER TABLE `relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `relation` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.requirement
CREATE TABLE IF NOT EXISTS `requirement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orcomposite` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orcomposite` (`orcomposite`),
  CONSTRAINT `requirement_ibfk_1` FOREIGN KEY (`orcomposite`) REFERENCES `orcomposite` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.requirement: ~0 rows (circa)
/*!40000 ALTER TABLE `requirement` DISABLE KEYS */;
/*!40000 ALTER TABLE `requirement` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.resource
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` int(10) unsigned DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`identifier`) REFERENCES `identifier` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.resource: ~0 rows (circa)
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.rights
CREATE TABLE IF NOT EXISTS `rights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cost` enum('yes','no') NOT NULL,
  `copyright_and_other_restrictions` enum('yes','no') NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.rights: ~0 rows (circa)
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.taxon
CREATE TABLE IF NOT EXISTS `taxon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry` text,
  `description` text,
  `keyword` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.taxon: ~0 rows (circa)
/*!40000 ALTER TABLE `taxon` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxon` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.taxon_path
CREATE TABLE IF NOT EXISTS `taxon_path` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` text NOT NULL,
  `taxon` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `taxon` (`taxon`),
  CONSTRAINT `taxon_path_ibfk_1` FOREIGN KEY (`taxon`) REFERENCES `taxon` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.taxon_path: ~0 rows (circa)
/*!40000 ALTER TABLE `taxon_path` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxon_path` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.technical
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

-- Dump dei dati della tabella lom_local.technical: ~0 rows (circa)
/*!40000 ALTER TABLE `technical` DISABLE KEYS */;
/*!40000 ALTER TABLE `technical` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.tmp_update_log
CREATE TABLE IF NOT EXISTS `tmp_update_log` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_tmp_update_log_general` (`local_id`),
  CONSTRAINT `FK_tmp_update_log_general` FOREIGN KEY (`local_id`) REFERENCES `general` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.tmp_update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `tmp_update_log` DISABLE KEYS */;
INSERT INTO `tmp_update_log` (`id`, `local_id`) VALUES
	(8, 9),
	(11, 9),
	(12, 9),
	(13, 9),
	(14, 11);
/*!40000 ALTER TABLE `tmp_update_log` ENABLE KEYS */;

-- Dump della struttura di tabella lom_local.update_log
CREATE TABLE IF NOT EXISTS `update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__general` (`local_id`),
  CONSTRAINT `FK__general` FOREIGN KEY (`local_id`) REFERENCES `general` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella lom_local.update_log: ~0 rows (circa)
/*!40000 ALTER TABLE `update_log` DISABLE KEYS */;
INSERT INTO `update_log` (`id`, `local_id`) VALUES
	(8, 9),
	(11, 9),
	(12, 9),
	(13, 9),
	(14, 11);
/*!40000 ALTER TABLE `update_log` ENABLE KEYS */;

-- Dump della struttura di trigger lom_local.general_after_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `general_after_update` AFTER UPDATE ON `general` FOR EACH ROW BEGIN
insert into update_log(local_id) values(NEW.id);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
