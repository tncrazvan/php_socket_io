-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              10.1.21-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win32
-- HeidiSQL Versione:            9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dump della struttura del database glorep_shared
CREATE DATABASE IF NOT EXISTS `glorep_shared` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `glorep_shared`;

-- Dump della struttura di tabella glorep_shared.lo_annotation
CREATE TABLE IF NOT EXISTS `lo_annotation` (
  `Id_Annotation` int(11) NOT NULL,
  `Id_Identifier` int(11) NOT NULL,
  `Entity` varchar(1000) NOT NULL,
  `Date` datetime NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `TimeUpd` int(11) NOT NULL,
  PRIMARY KEY (`Id_Annotation`,`Id_Identifier`),
  KEY `Id_Identifier` (`Id_Identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_annotation: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_annotation` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_annotation` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_contribute
CREATE TABLE IF NOT EXISTS `lo_contribute` (
  `Id_Contribute` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Entity` varchar(1000) NOT NULL,
  `Date` varchar(20) NOT NULL,
  PRIMARY KEY (`Id_Contribute`),
  KEY `FK_lo_contribute_lo_general` (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_contribute_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_contribute: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_contribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_contribute` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_educational
CREATE TABLE IF NOT EXISTS `lo_educational` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `InteractivityType` varchar(200) NOT NULL,
  `LearningResourceType` varchar(200) NOT NULL,
  `InteractivityLevel` varchar(200) NOT NULL,
  `SemanticDensity` varchar(200) NOT NULL,
  `IntendedEndUserRole` varchar(200) NOT NULL,
  `Context` varchar(200) NOT NULL,
  `TypicalAgeRange` varchar(1000) NOT NULL,
  `Difficulty` varchar(200) NOT NULL,
  `TypicalLearningTime` varchar(50) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `Language` varchar(100) NOT NULL,
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_educational_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_educational: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_educational` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_educational` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_federation
CREATE TABLE IF NOT EXISTS `lo_federation` (
  `ServerName` varchar(20) NOT NULL,
  `ServerAddress` varchar(250) NOT NULL,
  `Login` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `N_Lo` int(11) NOT NULL,
  `TimeUpd` int(11) NOT NULL,
  PRIMARY KEY (`ServerName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_federation: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_federation` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_federation` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_file
CREATE TABLE IF NOT EXISTS `lo_file` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `url` varchar(1000) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filesize` varchar(255) NOT NULL,
  `filemime` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  KEY `Id_Fd` (`Id_Fd`,`Id_Lo`),
  CONSTRAINT `FK_lo_file_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_file: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_file` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_general
CREATE TABLE IF NOT EXISTS `lo_general` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL,
  `Title` varchar(1000) DEFAULT NULL,
  `Language` varchar(100) DEFAULT NULL,
  `Description` varchar(2000) DEFAULT NULL,
  `Keyword` varchar(1000) DEFAULT NULL,
  `Coverage` varchar(1000) DEFAULT NULL,
  `Structure` varchar(1000) DEFAULT NULL,
  `Aggregation_Level` tinyint(4) DEFAULT NULL,
  `Deleted` varchar(5) NOT NULL DEFAULT 'no',
  `TimeUpd` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Id_Lo_Id_Fd` (`Id_Lo`,`Id_Fd`),
  KEY `Id_Fd` (`Id_Lo`,`Id_Fd`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_general: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_general` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_general` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_identifier
CREATE TABLE IF NOT EXISTS `lo_identifier` (
  `Id_Identifier` int(11) NOT NULL,
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Catalog` varchar(1000) NOT NULL,
  `Entry` varchar(1000) NOT NULL,
  `TimeUpd` int(11) NOT NULL,
  PRIMARY KEY (`Id_Identifier`),
  UNIQUE KEY `Id_Lo_Id_Fd` (`Id_Lo`,`Id_Fd`),
  KEY `Id_Identifier` (`Id_Identifier`,`Id_Fd`),
  KEY `Id_Lo` (`Id_Fd`),
  KEY `Id_Fd` (`Id_Fd`),
  CONSTRAINT `FK_lo_identifier_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `LO_Identifier_ibfk_2` FOREIGN KEY (`Id_Identifier`) REFERENCES `lo_relation` (`Id_Target`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_identifier: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_identifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_identifier` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_lifecycle
CREATE TABLE IF NOT EXISTS `lo_lifecycle` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Version` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_lifecycle_lo_metadata` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_metadata` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_lifecycle: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_lifecycle` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_lifecycle` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_metadata
CREATE TABLE IF NOT EXISTS `lo_metadata` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `MetadataSchema` varchar(30) NOT NULL DEFAULT 'LOM v1.0',
  `Language` varchar(100) NOT NULL DEFAULT 'En',
  PRIMARY KEY (`Id_Fd`,`Id_Lo`),
  KEY `FK_lo_metadata_lo_general` (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_metadata_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_metadata: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_metadata` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_orcomposite
CREATE TABLE IF NOT EXISTS `lo_orcomposite` (
  `Id_Composite` int(11) NOT NULL,
  `Type` varchar(1000) NOT NULL,
  `Name` varchar(1000) NOT NULL,
  `MinimumVersion` varchar(30) NOT NULL,
  `MaximumVersion` varchar(30) NOT NULL,
  PRIMARY KEY (`Id_Composite`),
  CONSTRAINT `LO_OrComposite_ibfk_1` FOREIGN KEY (`Id_Composite`) REFERENCES `lo_requirement` (`Id_Composite`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_orcomposite: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_orcomposite` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_orcomposite` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_relation
CREATE TABLE IF NOT EXISTS `lo_relation` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Kind` varchar(200) NOT NULL,
  `Id_Target` int(11) NOT NULL,
  `TimeUpd` int(11) NOT NULL,
  PRIMARY KEY (`Id_Target`,`Id_Lo`,`Id_Fd`),
  KEY `Id_Target` (`Id_Target`),
  KEY `Id_Lo` (`Id_Lo`,`Id_Fd`,`Id_Target`),
  CONSTRAINT `FK_lo_relation_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `LO_Relation_ibfk_1` FOREIGN KEY (`Id_Target`) REFERENCES `lo_annotation` (`Id_Identifier`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_relation: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_relation` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_requirement
CREATE TABLE IF NOT EXISTS `lo_requirement` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Id_Composite` int(11) NOT NULL,
  PRIMARY KEY (`Id_Lo`,`Id_Fd`,`Id_Composite`),
  KEY `Id_Lo_3` (`Id_Fd`,`Id_Composite`),
  KEY `Id_Composite` (`Id_Composite`),
  KEY `Id_Fd` (`Id_Fd`),
  CONSTRAINT `FK_lo_requirement_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_requirement: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_requirement` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_requirement` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_rights
CREATE TABLE IF NOT EXISTS `lo_rights` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Cost` varchar(5) NOT NULL,
  `Copyright` varchar(5) NOT NULL,
  `rights_Description` varchar(1000) NOT NULL,
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  KEY `Id_Lo` (`Id_Fd`),
  KEY `Id_Fd` (`Id_Fd`),
  CONSTRAINT `LO_Rights_ibfk_1` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_rights: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_rights` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_rights` ENABLE KEYS */;

-- Dump della struttura di tabella glorep_shared.lo_technical
CREATE TABLE IF NOT EXISTS `lo_technical` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL,
  `Format` varchar(500) NOT NULL,
  `Size` varchar(30) NOT NULL,
  `Location` varchar(1000) NOT NULL,
  `InstallationRemarks` varchar(1000) NOT NULL,
  `OtherPlatformRequirements` varchar(1000) NOT NULL,
  `Duration` varchar(50) NOT NULL,
  `TimeUpd` int(11) NOT NULL,
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  KEY `Id_Lo_2` (`Id_Fd`),
  KEY `Id_Fd` (`Id_Fd`),
  CONSTRAINT `LO_Technical_ibfk_1` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_file` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella glorep_shared.lo_technical: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_technical` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_technical` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
