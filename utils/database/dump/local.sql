-- MySQL dump 10.13  Distrib 5.7.17, for Linux (x86_64)
--
-- Host: localhost    Database: local
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `local`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `local` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `local`;

--
-- Table structure for table `test_table`
--

DROP TABLE IF EXISTS `test_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'unknown title',
  `id_fd` varchar(254) NOT NULL,
  `shared_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'final',
  `versione` int(11) NOT NULL DEFAULT '1',
  `remote_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_table`
--

LOCK TABLES `test_table` WRITE;
/*!40000 ALTER TABLE `test_table` DISABLE KEYS */;
INSERT INTO `test_table` VALUES (33,'unknown title','unipg',0,'final',1,0),(34,'unknown title','unipg',0,'final',1,0),(35,'unknown title','unipg',0,'final',1,0),(36,'unknown title','unipg',0,'final',1,0),(37,'unknown title','unipg',0,'final',1,0),(111,'unknown title','unipg',0,'final',1,0),(175,'unknown title','unimil',211,'final',1,1),(176,'unknown title','unimil',213,'final',1,2),(177,'unknown title','unimil',263,'final',1,3),(178,'unknown title','unimil',286,'final',1,4),(179,'unknown title','unimil',304,'final',1,5),(181,'test_title','unimil',382,'final',1,6);
/*!40000 ALTER TABLE `test_table` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_log` AFTER UPDATE ON `test_table` FOR EACH ROW insert into update_log(local_id) values(NEW.id) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tmp_update_log`
--

DROP TABLE IF EXISTS `tmp_update_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmp_update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'final',
  PRIMARY KEY (`id`),
  KEY `FK_tmp_update_log_update_log` (`local_id`),
  CONSTRAINT `FK_tmp_update_log_update_log` FOREIGN KEY (`local_id`) REFERENCES `update_log` (`local_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmp_update_log`
--

LOCK TABLES `tmp_update_log` WRITE;
/*!40000 ALTER TABLE `tmp_update_log` DISABLE KEYS */;
INSERT INTO `tmp_update_log` VALUES (38,33,'final'),(39,34,'final'),(40,35,'final'),(41,36,'final'),(42,37,'final'),(43,111,'final'),(44,111,'final'),(45,111,'final'),(46,111,'final');
/*!40000 ALTER TABLE `tmp_update_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `update_log`
--

DROP TABLE IF EXISTS `update_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `local_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_update_log_test_table` (`local_id`),
  CONSTRAINT `FK_update_log_test_table` FOREIGN KEY (`local_id`) REFERENCES `test_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `update_log`
--

LOCK TABLES `update_log` WRITE;
/*!40000 ALTER TABLE `update_log` DISABLE KEYS */;
INSERT INTO `update_log` VALUES (38,33),(39,34),(40,35),(41,36),(42,37),(43,111),(44,111),(45,111),(46,111);
/*!40000 ALTER TABLE `update_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-23 10:01:14
