-- MySQL dump 10.13  Distrib 5.7.23, for Linux (x86_64)
--
-- Host: localhost    Database: projekt
-- ------------------------------------------------------
-- Server version	5.7.23-0ubuntu0.18.04.1

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
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `PK_idComments` int(10) NOT NULL AUTO_INCREMENT,
  `FK_idPosts` int(10) NOT NULL,
  `content` text NOT NULL,
  `idUsers` int(10) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`PK_idComments`),
  KEY `FK_idPosts` (`FK_idPosts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `PK_idConversations` int(10) NOT NULL AUTO_INCREMENT,
  `FK_idUsers` varchar(10) NOT NULL,
  PRIMARY KEY (`PK_idConversations`),
  KEY `FK_idUsers` (`FK_idUsers`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (1,'1');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `PK_time` datetime NOT NULL,
  `FK_idConversations` int(10) NOT NULL,
  `FK_idUsers` int(10) NOT NULL,
  `content` varchar(45) NOT NULL,
  PRIMARY KEY (`PK_time`,`FK_idConversations`,`FK_idUsers`),
  KEY `FK_idConversations` (`FK_idConversations`),
  KEY `FK_idUsers` (`FK_idUsers`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`FK_idConversations`) REFERENCES `conversations` (`PK_idConversations`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`FK_idUsers`) REFERENCES `users` (`PK_idUsers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES ('1000-01-01 00:00:00',1,1,''),('1000-01-01 00:06:00',1,2,'');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants` (
  `FK_idConversations` int(10) NOT NULL,
  `FK_idUsers` int(10) NOT NULL,
  KEY `FK_idConversations` (`FK_idConversations`),
  KEY `FK_idUsers` (`FK_idUsers`),
  CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`FK_idConversations`) REFERENCES `conversations` (`PK_idConversations`),
  CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`FK_idUsers`) REFERENCES `users` (`PK_idUsers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participants`
--

LOCK TABLES `participants` WRITE;
/*!40000 ALTER TABLE `participants` DISABLE KEYS */;
INSERT INTO `participants` VALUES (1,1),(1,2);
/*!40000 ALTER TABLE `participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `PK_idPosts` int(10) NOT NULL AUTO_INCREMENT,
  `FK_idUsers` int(10) NOT NULL,
  `content` text NOT NULL,
  `visibility` int(3) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  `idMedia` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`PK_idPosts`),
  KEY `FK_idUsers` (`FK_idUsers`),
  CONSTRAINT `FK_idUsers` FOREIGN KEY (`FK_idUsers`) REFERENCES `users` (`PK_idUsers`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'To jest pierwszy testowy post w bazie danych sprawdzający poprawność wyświetlania danych',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','0'),(2,1,'drugi',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','1'),(3,1,'trzeci',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','2'),(4,1,'czwarty',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','3'),(5,1,'piąty',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','4'),(6,1,'szosty',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','5'),(7,1,'siodmy',1,'1000-01-01 00:00:00','1000-01-01 00:00:00','6'),(8,1,'aggsgas',NULL,'2018-08-17 22:56:09','2018-08-17 22:56:09',NULL),(9,2,'Udało się?',NULL,'2018-08-17 22:56:40','2018-08-17 22:56:40',NULL),(10,1,'gasgas',NULL,'2018-08-17 23:00:40','2018-08-17 23:00:40',NULL),(11,1,'NO CO TAM',NULL,'2018-08-19 20:14:00','2018-08-19 20:14:02',NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ROLE_ADMIN'),(2,'ROLE_USER');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `PK_idUsers` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(64) NOT NULL,
  `idPicture` varchar(10) DEFAULT NULL,
  `role_id` int(1) DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  PRIMARY KEY (`PK_idUsers`),
  UNIQUE KEY `PK_idUsers_UNIQUE` (`PK_idUsers`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Konrad','Szewczuk','admin@admin.com','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu','0',1,NULL),(2,'Test','User','test@user.com','$2y$13$gU49/WTt/SZYxuRS82m2y.hv8yh64U22nshc.PfZ.vIyR6mFuOw4a','1',1,NULL),(3,'Jerzy','Owsiak','j@o.pl','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu','1',1,NULL),(4,'a','b','a@','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(5,'2','2','k','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(6,'a','a','a','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(7,'e','e','e','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(8,'test','test','test@test.test','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(9,'asd','asd','asdasd','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(10,'Jerzy','Owsiak','j@owsiak.pl','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(11,'jurek','owsiak','jowsiak@wp.pl','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(12,'Konrad','Szewczuk','konrabana@gmail.com','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(13,'Alan','Andersz','a@andersz.com','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(14,'Konrad','Szewczuk','konrad@szewczuk.com','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(15,'aaaaaaaa','aaaaaaaa','aaaaaaaa@aa.com','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu',NULL,NULL,NULL),(16,'Konrad','Szewczuk','konrad@sz.pl','$2y$13$NES1/nXeRTAzX03LhbWMpeRoK940Yhg3F9./H4JaeKhdVoxwiU4Wu','1',1,NULL),(18,'A','B','abb@gmail.com','$2y$13$Eu8Z4cSOC/pk9vmCcBzwuuLtnKHvFhJKsPAFgJ2IkrEGBv8nCCUpO',NULL,NULL,NULL),(19,'W','B','W@gmail.com','$2y$13$zGEQMfNGfVzU6QkwnOFIwONAiIT79De64sCvk4hTwt/awRL/XeZmq',NULL,NULL,NULL),(20,'konrrad','szewczuk','konrad@szewczuk.pl','$2y$13$CD6VpsiQEjRo1ItNwN4LwOKG/Cd4F8fCfyptLof8FFULp.u.j5/Ty',NULL,2,NULL),(21,'Alfred','Hitchcock','alfred@hi.com','$2y$13$bCO.y01mVTesqj4QVBrIx.pQT1up8ybLm.ZMyPLpo915KCr7.gKc2',NULL,2,'1972-04-07'),(23,'Alfred','Hitchcock','alfred@h.com','$2y$13$PwNwi60t2HEXFKBzD9D5ReSqBE2hpiv0kr2S4juRcn6LipN6NhDuS',NULL,2,'1972-04-07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-28 22:36:00
