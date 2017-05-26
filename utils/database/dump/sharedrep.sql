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


-- Dump della struttura del database sharedrep
CREATE DATABASE IF NOT EXISTS `sharedrep` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `sharedrep`;

-- Dump della struttura di tabella sharedrep.lo_category
CREATE TABLE IF NOT EXISTS `lo_category` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Categoria_LO` int(11) NOT NULL COMMENT 'Refers to the category'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Technical capabilities necessary for using this learning...';

-- Dump dei dati della tabella sharedrep.lo_category: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_category` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_contribute
CREATE TABLE IF NOT EXISTS `lo_contribute` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Role` varchar(50) NOT NULL COMMENT 'Kind of contribution.',
  `Entity` varchar(1000) DEFAULT NULL COMMENT 'The identification of and information about entities contributing to this learnin object.',
  `Date` varchar(20) DEFAULT NULL COMMENT 'The date of contribution.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_contribute_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Those Entities that have contibuted to the state of this...';

-- Dump dei dati della tabella sharedrep.lo_contribute: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_contribute` DISABLE KEYS */;
INSERT INTO `lo_contribute` (`Id_Lo`, `Id_Fd`, `Role`, `Entity`, `Date`) VALUES
	(55, 'glorep.unipg.it', 'creator', 'test 1', '2017-02-05 22:37:31');
/*!40000 ALTER TABLE `lo_contribute` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_educational
CREATE TABLE IF NOT EXISTS `lo_educational` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `InteractivityType` varchar(200) NOT NULL COMMENT 'Predominant mode of learning supported by this learning object.',
  `LearningResourceType` varchar(200) NOT NULL COMMENT 'Specific kind of learning object.',
  `InteractivityLevel` varchar(200) NOT NULL COMMENT 'The degree of interactivity characterizing this learning object.',
  `SemanticDensity` varchar(200) NOT NULL COMMENT 'The degree of concisness of a learning object.',
  `IntendedEndUserRole` varchar(200) NOT NULL COMMENT 'Principal users for which this learning object was designed.',
  `Context` varchar(200) NOT NULL COMMENT 'The principal environment within which the learning and use of this learning object intended to take place.',
  `TypicalAgeRange` varchar(1000) DEFAULT NULL COMMENT 'Age of the typical intended user.',
  `Difficulty` varchar(200) NOT NULL COMMENT 'How hard it is to work with this learning object.',
  `TypicalLearningTime` varchar(50) DEFAULT NULL COMMENT 'Time takes to work with this learning object.',
  `edu_Description` varchar(1000) DEFAULT NULL COMMENT 'Comments on how this learning object is to be used.',
  `edu_Language` varchar(100) DEFAULT NULL COMMENT 'The language used by the typical intended user.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_educational_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Technical capabilities necessary for using this learning...';

-- Dump dei dati della tabella sharedrep.lo_educational: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_educational` DISABLE KEYS */;
INSERT INTO `lo_educational` (`Id_Lo`, `Id_Fd`, `InteractivityType`, `LearningResourceType`, `InteractivityLevel`, `SemanticDensity`, `IntendedEndUserRole`, `Context`, `TypicalAgeRange`, `Difficulty`, `TypicalLearningTime`, `edu_Description`, `edu_Language`) VALUES
	(55, 'glorep.unipg.it', 'active', 'exercise', 'very low', 'very low', 'teacher', 'school', '', 'very easy', '', '', 'en');
/*!40000 ALTER TABLE `lo_educational` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_federation
CREATE TABLE IF NOT EXISTS `lo_federation` (
  `ServerName` varchar(20) NOT NULL COMMENT 'A list of name of federated',
  `ServerAddress` varchar(250) NOT NULL COMMENT 'A list of federated address',
  `N_Lo` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of Node.',
  `TimeUpd` int(11) NOT NULL DEFAULT '0' COMMENT 'Time for sinc.',
  PRIMARY KEY (`ServerName`),
  UNIQUE KEY `ServerName` (`ServerName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains special Learning Object properties';

-- Dump dei dati della tabella sharedrep.lo_federation: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_federation` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_federation` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_file
CREATE TABLE IF NOT EXISTS `lo_file` (
  `Id_Lo` int(10) unsigned NOT NULL,
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `url` varchar(255) NOT NULL COMMENT 'The server path where the file is located.',
  `filename` varchar(255) NOT NULL COMMENT 'Name of the file.',
  `filesize` varchar(255) NOT NULL COMMENT 'Size of the file.',
  `filemime` varchar(255) NOT NULL COMMENT 'Name of the file.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_file_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Learning Object files';

-- Dump dei dati della tabella sharedrep.lo_file: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_file` DISABLE KEYS */;
INSERT INTO `lo_file` (`Id_Lo`, `Id_Fd`, `url`, `filename`, `filesize`, `filemime`) VALUES
	(55, 'glorep.unipg.it', 'http://127.0.0.1/glorep//glorep/sites/default/files/data%20-%20controller.png', 'data - controller.png', '36598', 'image/png');
/*!40000 ALTER TABLE `lo_file` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_general
CREATE TABLE IF NOT EXISTS `lo_general` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Title` varchar(1000) DEFAULT NULL COMMENT 'Title of this Learning Object',
  `Language` varchar(100) DEFAULT NULL COMMENT 'Language of this learning object.',
  `Description` varchar(2000) DEFAULT NULL COMMENT 'Description of this Learning Object',
  `Keyword` varchar(1000) DEFAULT NULL COMMENT 'Keyword of this Learning Object',
  `Coverage` varchar(1000) DEFAULT NULL COMMENT 'Coverage of this learning object.',
  `Structure` varchar(1000) DEFAULT NULL COMMENT 'Structure of this learning object.',
  `Aggregation_Level` tinyint(4) DEFAULT NULL COMMENT 'Aggregation Level of this learning object.',
  `Deleted` varchar(5) NOT NULL DEFAULT 'no' COMMENT 'set a yes if this learning object is deleted',
  `TimeUpd` int(11) NOT NULL DEFAULT '0' COMMENT 'Time for sinc.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node_id` (`Id_Lo`,`Id_Fd`),
  KEY `Id_Lo_Id_Fd` (`Id_Lo`,`Id_Fd`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Contains special Learning Object properties';

-- Dump dei dati della tabella sharedrep.lo_general: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_general` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_general` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_identifier
CREATE TABLE IF NOT EXISTS `lo_identifier` (
  `Id_Identifier` int(10) unsigned NOT NULL COMMENT 'A globally unique refers to a learning object.',
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Catalog` varchar(1000) NOT NULL COMMENT 'The name or designator of the identifications or cataloging scheme for this entry. A namespace scheme.',
  `Entry` varchar(1000) NOT NULL COMMENT 'The value of the identifier within the identification or cataloging scheme that designates or identifies the target learning object. A namespace specific string.',
  `TimeUpd` int(11) NOT NULL DEFAULT '0' COMMENT 'Time for sinc.',
  PRIMARY KEY (`Id_Identifier`),
  KEY `FK_lo_identifier_lo_general` (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_identifier_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='a globally unique label that identifies the target...';

-- Dump dei dati della tabella sharedrep.lo_identifier: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_identifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_identifier` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_lifecycle
CREATE TABLE IF NOT EXISTS `lo_lifecycle` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Version` varchar(50) DEFAULT NULL COMMENT 'The version of the Learning Object.',
  `Status` varchar(50) NOT NULL COMMENT 'The status or condition of the Learning Object.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_lifecycle_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the story and the current state of the Learning...';

-- Dump dei dati della tabella sharedrep.lo_lifecycle: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_lifecycle` DISABLE KEYS */;
INSERT INTO `lo_lifecycle` (`Id_Lo`, `Id_Fd`, `Version`, `Status`) VALUES
	(55, 'glorep.unipg.it', '1.0', 'final');
/*!40000 ALTER TABLE `lo_lifecycle` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_metadata
CREATE TABLE IF NOT EXISTS `lo_metadata` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `MetadataSchema` varchar(30) DEFAULT NULL COMMENT 'Name and version of the authoritative specificationused to create this metadata istance.',
  `Language` varchar(100) DEFAULT NULL COMMENT 'Language of this metadata istance.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_metadata_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The category describes the metadata itself.';

-- Dump dei dati della tabella sharedrep.lo_metadata: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_metadata` DISABLE KEYS */;
INSERT INTO `lo_metadata` (`Id_Lo`, `Id_Fd`, `MetadataSchema`, `Language`) VALUES
	(55, 'glorep.unipg.it', 'LOMv1.0', 'en');
/*!40000 ALTER TABLE `lo_metadata` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_orcomposite
CREATE TABLE IF NOT EXISTS `lo_orcomposite` (
  `Id_Composite` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Type` varchar(1000) DEFAULT NULL COMMENT 'Technology required to use this learning object.',
  `Name` varchar(1000) DEFAULT NULL COMMENT 'Name of the required technology to use this learning object.',
  `MinimumVersion` varchar(30) DEFAULT NULL COMMENT 'Lowest possible version of the required technology to use this learning object.',
  `MaximumVersion` varchar(30) DEFAULT NULL COMMENT 'Highest possible version of the required technology to use this learning object.',
  PRIMARY KEY (`Id_Composite`),
  UNIQUE KEY `Id_Composite` (`Id_Composite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Technical capabilities necessary for using this learning...';

-- Dump dei dati della tabella sharedrep.lo_orcomposite: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_orcomposite` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_orcomposite` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_relation
CREATE TABLE IF NOT EXISTS `lo_relation` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Id_Target` int(10) unsigned NOT NULL COMMENT 'A globally unique refers to a learning object.',
  `kind` varchar(200) NOT NULL COMMENT 'Describe the nature of the relationship between this learning object and the target learning object',
  `TimeUpd` int(11) NOT NULL DEFAULT '0' COMMENT 'Time for sinc.',
  PRIMARY KEY (`Id_Target`,`Id_Lo`,`Id_Fd`),
  KEY `FK_lo_relation_lo_general` (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_relation_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='a globally unique label that identifies the target...';

-- Dump dei dati della tabella sharedrep.lo_relation: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_relation` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_requirement
CREATE TABLE IF NOT EXISTS `lo_requirement` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Id_Composite` int(10) unsigned NOT NULL COMMENT 'Technology required to use this learning object.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_requirement_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Technical capabilities necessary for using this learning...';

-- Dump dei dati della tabella sharedrep.lo_requirement: ~0 rows (circa)
/*!40000 ALTER TABLE `lo_requirement` DISABLE KEYS */;
/*!40000 ALTER TABLE `lo_requirement` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_rights
CREATE TABLE IF NOT EXISTS `lo_rights` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Cost` varchar(5) NOT NULL COMMENT 'Whether use of this learning object requires payment.',
  `Copyright` varchar(5) NOT NULL COMMENT 'Whether copyright or other restriction apply to the use of this learning object.',
  `rights_Description` varchar(1000) NOT NULL COMMENT 'Comments on the conditions of use of this learning object.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_rights_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intellectual property rights and condition for use this...';

-- Dump dei dati della tabella sharedrep.lo_rights: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_rights` DISABLE KEYS */;
INSERT INTO `lo_rights` (`Id_Lo`, `Id_Fd`, `Cost`, `Copyright`, `rights_Description`) VALUES
	(55, 'glorep.unipg.it', 'no', 'no', '');
/*!40000 ALTER TABLE `lo_rights` ENABLE KEYS */;

-- Dump della struttura di tabella sharedrep.lo_technical
CREATE TABLE IF NOT EXISTS `lo_technical` (
  `Id_Lo` int(10) unsigned NOT NULL COMMENT 'Refers to a Node Id.',
  `Id_Fd` varchar(255) NOT NULL COMMENT 'A federation-unique ID.',
  `Format` varchar(40) DEFAULT NULL COMMENT 'Technical datatypes of the learning object.',
  `Size` varchar(30) DEFAULT NULL COMMENT 'The size of the digital learning object in bytes.',
  `Location` varchar(1000) DEFAULT NULL COMMENT 'A string (URI or URL) that is used to access this learning object.',
  `InstallationRemarks` varchar(1000) DEFAULT NULL COMMENT 'Description of how to install this learning object.',
  `OtherPlatformRequirements` varchar(1000) DEFAULT NULL COMMENT 'Information about other software and hardware requirements.',
  `Duration` varchar(50) DEFAULT NULL COMMENT 'Time a continuous learning object takes when played at intended speed.',
  PRIMARY KEY (`Id_Lo`,`Id_Fd`),
  CONSTRAINT `FK_lo_technical_lo_general` FOREIGN KEY (`Id_Lo`, `Id_Fd`) REFERENCES `lo_general` (`Id_Lo`, `Id_Fd`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Technical requirements and characteristics of the...';

-- Dump dei dati della tabella sharedrep.lo_technical: ~1 rows (circa)
/*!40000 ALTER TABLE `lo_technical` DISABLE KEYS */;
INSERT INTO `lo_technical` (`Id_Lo`, `Id_Fd`, `Format`, `Size`, `Location`, `InstallationRemarks`, `OtherPlatformRequirements`, `Duration`) VALUES
	(55, 'glorep.unipg.it', 'test 1', NULL, '', '', '', '');
/*!40000 ALTER TABLE `lo_technical` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
