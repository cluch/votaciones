-- MySQL dump 10.13  Distrib 5.1.52, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: CLUCH
-- ------------------------------------------------------
-- Server version	5.1.52

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
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modelo_opciones`
--

DROP TABLE IF EXISTS `modelo_opciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modelo_opciones` (
  `opcion_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `opcion_hash_privado` varchar(40) NOT NULL,
  `opcion_pregunta_id` int(11) unsigned NOT NULL,
  `opcion_texto` varchar(256) NOT NULL,
  PRIMARY KEY (`opcion_id`),
  UNIQUE KEY `opcion_hash_privado` (`opcion_hash_privado`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modelo_preguntas`
--

DROP TABLE IF EXISTS `modelo_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modelo_preguntas` (
  `pregunta_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pregunta_hash_privado` varchar(40) NOT NULL,
  `pregunta_voto_id` int(11) unsigned NOT NULL,
  `pregunta_limite` tinyint(2) unsigned NOT NULL,
  `pregunta_texto` varchar(1024) NOT NULL,
  PRIMARY KEY (`pregunta_id`),
  UNIQUE KEY `pregunta_hash_privado` (`pregunta_hash_privado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modelo_votos`
--

DROP TABLE IF EXISTS `modelo_votos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modelo_votos` (
  `voto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `voto_fecha_inicio` datetime NOT NULL,
  `voto_fecha_termino` datetime NOT NULL,
  `voto_hash_privado` varchar(40) NOT NULL,
  `voto_mesa_id` int(11) unsigned NOT NULL,
  `voto_titulo` varchar(256) NOT NULL,
  `voto_texto` varchar(2048) NOT NULL,
  PRIMARY KEY (`voto_id`),
  UNIQUE KEY `voto_hash_privado` (`voto_hash_privado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sufragios_registros`
--

DROP TABLE IF EXISTS `sufragios_registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sufragios_registros` (
  `registro_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `registro_fecha` datetime NOT NULL,
  `registro_ip` varchar(15) NOT NULL,
  `registro_user_hash` varchar(40) NOT NULL,
  `registro_voto_id` int(11) unsigned NOT NULL,
  `registro_mesa_id` varchar(5) NOT NULL,
  PRIMARY KEY (`registro_id`),
  KEY `voto_unico` (`registro_user_hash`,`registro_voto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sufragios_votos`
--

DROP TABLE IF EXISTS `sufragios_votos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sufragios_votos` (
  `sufragio_hash` varchar(40) NOT NULL,
  `voto_id` int(10) unsigned NOT NULL,
  `voto_mesa_id` varchar(5) NOT NULL,
  `voto_llave_publica` text NOT NULL,
  `voto_firma_digital` text NOT NULL,
  `voto_datos_encriptados` text NOT NULL,
  PRIMARY KEY (`sufragio_hash`),
  KEY `voto_unico` (`sufragio_hash`,`voto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-08-24 23:59:24
