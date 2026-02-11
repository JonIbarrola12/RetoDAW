CREATE DATABASE  IF NOT EXISTS `opusbooks` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `opusbooks`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: opusbooks
-- ------------------------------------------------------
-- Server version	5.7.43-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `autores`
--

DROP TABLE IF EXISTS `autores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autores` (
  `id_autor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  PRIMARY KEY (`id_autor`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autores`
--

LOCK TABLES `autores` WRITE;
/*!40000 ALTER TABLE `autores` DISABLE KEYS */;
INSERT INTO `autores` VALUES (1,'Pepe','Perez'),(2,'Gabriel','García Márquez'),(3,'Isabel','Allende'),(4,'J.K.','Rowling'),(5,'Stephen','King'),(6,'George','Orwell');
/*!40000 ALTER TABLE `autores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Drama'),(2,'Ficción'),(3,'No Ficción'),(4,'Terror'),(5,'Ciencia Ficción'),(6,'Romance');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_compra` date NOT NULL,
  `dni` varchar(9) NOT NULL,
  PRIMARY KEY (`id_compra`),
  KEY `fk_dni_cliente` (`dni`),
  CONSTRAINT `fk_dni_cliente` FOREIGN KEY (`dni`) REFERENCES `usuarios` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (2,'2026-01-29','21311213A'),(3,'2026-01-29','21311213A');
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datoscompras`
--

DROP TABLE IF EXISTS `datoscompras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `datoscompras` (
  `id_compra` int(5) NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `cantidad` int(3) NOT NULL,
  PRIMARY KEY (`id_compra`,`isbn`),
  KEY `fk_datos_compra_libro` (`isbn`),
  CONSTRAINT `fk_datos_compra_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_datos_compra_libro` FOREIGN KEY (`isbn`) REFERENCES `libros` (`isbn`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datoscompras`
--

LOCK TABLES `datoscompras` WRITE;
/*!40000 ALTER TABLE `datoscompras` DISABLE KEYS */;
INSERT INTO `datoscompras` VALUES (3,'9788497592208',2),(3,'9788499890950',1);
/*!40000 ALTER TABLE `datoscompras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `editoriales`
--

DROP TABLE IF EXISTS `editoriales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `editoriales` (
  `id_editorial` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_editorial`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `editoriales`
--

LOCK TABLES `editoriales` WRITE;
/*!40000 ALTER TABLE `editoriales` DISABLE KEYS */;
INSERT INTO `editoriales` VALUES (1,'Edevives'),(2,'Planeta'),(3,'Penguin Random House'),(4,'Santillana'),(5,'Alianza Editorial'),(6,'Debolsillo'),(7,'Planeta'),(8,'Penguin Random House'),(9,'Santillana'),(10,'Alianza Editorial'),(11,'Debolsillo');
/*!40000 ALTER TABLE `editoriales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libros`
--

DROP TABLE IF EXISTS `libros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libros` (
  `isbn` varchar(13) NOT NULL,
  `precio` double(10,2) NOT NULL,
  `stock` int(5) NOT NULL,
  `titulo` varchar(75) NOT NULL,
  `id_autor` int(5) NOT NULL,
  `id_editorial` int(5) NOT NULL,
  `id_categoria` int(5) NOT NULL,
  `id_poblacion` int(11) DEFAULT NULL,
  `fecha_edicion` date NOT NULL,
  PRIMARY KEY (`isbn`),
  KEY `fk_id_autor` (`id_autor`),
  KEY `fk_id_editorial` (`id_editorial`),
  KEY `fk_id_categoria` (`id_categoria`),
  KEY `fk_id_poblacion` (`id_poblacion`),
  CONSTRAINT `fk_id_autor` FOREIGN KEY (`id_autor`) REFERENCES `autores` (`id_autor`),
  CONSTRAINT `fk_id_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `fk_id_editorial` FOREIGN KEY (`id_editorial`) REFERENCES `editoriales` (`id_editorial`),
  CONSTRAINT `fk_id_poblacion` FOREIGN KEY (`id_poblacion`) REFERENCES `poblacion` (`id_poblacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libros`
--

LOCK TABLES `libros` WRITE;
/*!40000 ALTER TABLE `libros` DISABLE KEYS */;
INSERT INTO `libros` VALUES ('9780140283334',15.50,25,'Paula',3,3,3,1,'2024-01-01'),('9780451524935',16.50,45,'1984',6,6,5,1,'2024-01-01'),('9780451526342',17.00,50,'Rebelión en la granja',6,6,5,1,'2024-01-01'),('9780451526343',15.50,40,'Sin blanca',6,6,3,1,'2024-01-01'),('9780451526344',16.00,30,'Homenaje a Cataluña',6,6,3,1,'2024-01-01'),('9780451526345',18.00,35,'Los días de Birmania',6,6,5,1,'2024-01-01'),('9780747532743',22.99,60,'Harry Potter y la piedra filosofal',4,4,2,1,'2024-01-01'),('9780747538493',24.50,50,'Harry Potter y la cámara secreta',4,4,2,1,'2024-01-01'),('9780747572481',23.99,40,'Harry Potter y el prisionero de Azkaban',4,4,2,1,'2024-01-01'),('9780747581086',25.00,35,'Harry Potter y el cáliz de fuego',4,4,2,1,'2024-01-01'),('9780747581087',24.50,30,'Harry Potter y la Orden del Fénix',4,4,2,1,'2024-01-01'),('9781501142970',20.00,40,'It',5,5,4,1,'2024-01-01'),('9781501142971',19.50,20,'Carrie',5,5,4,1,'2024-01-01'),('9781501142972',22.00,25,'Misery',5,5,4,1,'2024-01-01'),('9781501143510',21.50,25,'Doctor Sueño',5,5,4,1,'2024-01-01'),('9781501143519',21.00,35,'El resplandor',5,5,4,1,'2024-01-01'),('9788497592208',19.99,48,'Cien años de soledad',2,2,2,1,'2024-01-01'),('9788498385972',18.50,20,'Eva Luna',3,3,3,1,'2024-01-01'),('9788498385989',16.99,30,'Hija de la fortuna',3,3,3,1,'2024-01-01'),('9788498385996',17.50,40,'La isla bajo el mar',3,3,2,1,'2024-01-01'),('9788498411961',14.50,30,'El amor en los tiempos del cólera',2,3,2,1,'2024-01-01'),('9788499890943',13.99,30,'Del amor y otros demonios',2,2,2,1,'2024-01-01'),('9788499890950',14.99,24,'Crónica de una muerte anunciada',2,2,2,1,'2024-01-01'),('9788499890967',13.50,50,'Memoria de mis putas tristes',2,2,2,1,'2024-01-01'),('9789561040919',18.00,40,'La casa de los espíritus',3,2,2,1,'2024-01-01');
/*!40000 ALTER TABLE `libros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pais`
--

DROP TABLE IF EXISTS `pais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pais` (
  `id_pais` int(11) NOT NULL,
  `nom_pais` varchar(100) NOT NULL,
  PRIMARY KEY (`id_pais`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pais`
--

LOCK TABLES `pais` WRITE;
/*!40000 ALTER TABLE `pais` DISABLE KEYS */;
INSERT INTO `pais` VALUES (1,'Francia');
/*!40000 ALTER TABLE `pais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poblacion`
--

DROP TABLE IF EXISTS `poblacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `poblacion` (
  `id_poblacion` int(11) NOT NULL,
  `nom_poblacion` varchar(100) NOT NULL,
  `id_pais` int(11) NOT NULL,
  PRIMARY KEY (`id_poblacion`),
  KEY `fk_poblacion_pais` (`id_pais`),
  CONSTRAINT `fk_poblacion_pais` FOREIGN KEY (`id_pais`) REFERENCES `pais` (`id_pais`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poblacion`
--

LOCK TABLES `poblacion` WRITE;
/*!40000 ALTER TABLE `poblacion` DISABLE KEYS */;
INSERT INTO `poblacion` VALUES (1,'Franceses',1);
/*!40000 ALTER TABLE `poblacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `email` varchar(75) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(1) NOT NULL DEFAULT 'C',
  PRIMARY KEY (`dni`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES ('21311213A','Jon','Ibarrola','Pulido','c\\ 1234','2006-04-21','jon@gmail.com','jj','123','C');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-11  9:40:18
