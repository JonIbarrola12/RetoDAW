    -- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
    --
    -- Host: localhost    Database: redsocialopus
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
    -- Table structure for table `usuarios`
    --

    DROP TABLE IF EXISTS `usuarios`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `usuarios` (
    `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
    `Nombre` varchar(25) DEFAULT NULL,
    `Apellido` varchar(25) DEFAULT NULL,
    `Username` varchar(25) NOT NULL,
    `Email` varchar(75) NOT NULL,
    `Password` varchar(225) NOT NULL,
    `Pfp` VARCHAR(225) DEFAULT '../../Recursos/fotousuario.png',
    `Bio` VARCHAR(225) DEFAULT 'Estoy usando OPUSCORD!',
    `FechaNacimiento` DATE NULL,
    `FechaRegistro` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_usuario`),
    UNIQUE KEY `Username` (`Username`),
    UNIQUE KEY `Email` (`Email`)
    ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `usuarios`
    --

    /*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
    ALTER TABLE usuarios 
    ADD estado ENUM('Online','Offline') DEFAULT 'Offline',
    ADD last_activity DATETIME;
    /*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;


    --
    -- Table structure for table `amigos`
    --

    DROP TABLE IF EXISTS `amigos`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `amigos` (
    `id_amigo` int(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` int(11) NOT NULL,
    `id_amigo_usuario` int(11) NOT NULL,
    `Estado` enum('pendiente','aceptado','bloqueado') DEFAULT 'pendiente',
    `FechaSolicitud` datetime DEFAULT CURRENT_TIMESTAMP,
    `FechaAceptacion` datetime DEFAULT NULL,
    PRIMARY KEY (`id_amigo`),
    KEY `id_usuario` (`id_usuario`),
    KEY `id_amigo_usuario` (`id_amigo_usuario`),
    CONSTRAINT `amigos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
    CONSTRAINT `amigos_ibfk_2` FOREIGN KEY (`id_amigo_usuario`) REFERENCES `usuarios` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `amigos`
    --

    /*!40000 ALTER TABLE `amigos` DISABLE KEYS */;
    /*!40000 ALTER TABLE `amigos` ENABLE KEYS */;

    --
    -- Table structure for table `publicaciones`
    --

    DROP TABLE IF EXISTS `publicaciones`;   
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `publicaciones` (
    `id_publicacion` int(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` int(11) NOT NULL,
    `Contenido` text NOT NULL,
    `ImagenUrl` varchar(255) DEFAULT NULL,
    `FechaPublicacion` datetime DEFAULT CURRENT_TIMESTAMP,
    `Visibilidad` enum('publica','soloamigos','privada') DEFAULT 'publica',
    PRIMARY KEY (`id_publicacion`),
    KEY `id_usuario` (`id_usuario`),
    CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `publicaciones`
    --

    /*!40000 ALTER TABLE `publicaciones` DISABLE KEYS */;

    /*!40000 ALTER TABLE `publicaciones` ENABLE KEYS */;


    --
    -- Table structure for table `comentarios`
    --

    DROP TABLE IF EXISTS `comentarios`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `comentarios` (
    `id_comentario` int(11) NOT NULL AUTO_INCREMENT,
    `id_publicacion` int(11) NOT NULL,
    `id_usuario` int(11) NOT NULL,
    `Contenido` varchar(500) NOT NULL,
    `FechaComentario` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_comentario`),
    KEY `id_publicacion` (`id_publicacion`),
    KEY `id_usuario` (`id_usuario`),
    CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`),
    CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `comentarios`
    --

    /*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
    /*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;

    --
    -- Table structure for table `likes`
    --

    DROP TABLE IF EXISTS `likes`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `likes` (
    `id_like` int(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` int(11) NOT NULL,
    `id_publicacion` int(11) NOT NULL,
    PRIMARY KEY (`id_like`),
    UNIQUE KEY `UsuarioPublicacion` (`id_usuario`,`id_publicacion`),
    KEY `id_publicacion` (`id_publicacion`),
    CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
    CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `likes`
    --

    /*!40000 ALTER TABLE `likes` DISABLE KEYS */;
    /*!40000 ALTER TABLE `likes` ENABLE KEYS */;

    --
    -- Table structure for table `mensajesPrivados`
    --

    DROP TABLE IF EXISTS `mensajesPrivados`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `mensajesPrivados` (
    `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
    `id_emisor` int(11) NOT NULL,
    `id_receptor` int(11) NOT NULL,
    `Contenido` varchar(500) NOT NULL,
    `FechaEnvio` datetime DEFAULT CURRENT_TIMESTAMP,
    `Leido` tinyint(1) DEFAULT 0,
    PRIMARY KEY (`id_mensaje`),
    KEY `id_emisor` (`id_emisor`),
    KEY `id_receptor` (`id_receptor`),
    CONSTRAINT `mensajesPrivados_ibfk_1` FOREIGN KEY (`id_emisor`) REFERENCES `usuarios` (`id_usuario`),
    CONSTRAINT `mensajesPrivados_ibfk_2` FOREIGN KEY (`id_receptor`) REFERENCES `usuarios` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `mensajesPrivados`
    --

    /*!40000 ALTER TABLE `mensajesPrivados` DISABLE KEYS */;
    /*!40000 ALTER TABLE `mensajesPrivados` ENABLE KEYS */;

    --
    -- Table structure for table `mensajesGrupos`
    --

    DROP TABLE IF EXISTS `mensajesGrupos`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `mensajesGrupos` (
    `id_mensajeGrupo` int(11) NOT NULL AUTO_INCREMENT,
    `id_emisor` int(11) NOT NULL,
    `id_receptor` int(11) NOT NULL,
    `Contenido` varchar(500) NOT NULL,
    `FechaEnvio` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_mensajeGrupo`),
    KEY `id_emisor` (`id_emisor`),
    KEY `id_receptor` (`id_receptor`),
    CONSTRAINT `mensajesGrupos_ibfk_1` FOREIGN KEY (`id_emisor`) REFERENCES `miembros` (`id_miembro`),
    CONSTRAINT `mensajesGrupos_ibfk_2` FOREIGN KEY (`id_receptor`) REFERENCES `grupos` (`id_grupo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `mensajesGrupos`
    --

    /*!40000 ALTER TABLE `mensajesGrupos` DISABLE KEYS */;
    /*!40000 ALTER TABLE `mensajesGrupos` ENABLE KEYS */;

    --
    -- Table structure for table `grupos`
    --

    DROP TABLE IF EXISTS `grupos`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `grupos` (
    `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
    `Nombre` varchar(100) NOT NULL,
    `Descripcion` varchar(255) DEFAULT NULL,
    `id_creador` int(11) NOT NULL,
    `Pfp` VARCHAR(225) DEFAULT NULL,
    `FechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_grupo`),
    KEY `id_creador` (`id_creador`),
    CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`id_creador`) REFERENCES `usuarios` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `grupos`
    --

    /*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
    /*!40000 ALTER TABLE `grupos` ENABLE KEYS */;

    --
    -- Table structure for table `miembros`
    --

    DROP TABLE IF EXISTS `miembros`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `miembros` (
    `id_miembro` int(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` int(11) NOT NULL,
    `GrupoId` int(11) NOT NULL,
    `Rol` enum('admin','moderador','miembro') DEFAULT 'miembro',
    `FechaIngreso` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_miembro`),
    UNIQUE KEY `UsuarioGrupo` (`id_usuario`,`GrupoId`),
    KEY `GrupoId` (`GrupoId`),
    CONSTRAINT `miembros_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
    CONSTRAINT `miembros_ibfk_2` FOREIGN KEY (`GrupoId`) REFERENCES `grupos` (`id_grupo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;
    ALTER TABLE miembros ADD activo TINYINT(1) DEFAULT 1;

    --
    -- Dumping data for table `seguidores`
    --


    --
    -- Table structure for table `seguidores`
    --

    DROP TABLE IF EXISTS `seguidores`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    /*!50503 SET character_set_client = utf8mb4 */;
    CREATE TABLE `seguidores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `id_seguidor` int(11) NOT NULL,
    `id_seguido` int(11) NOT NULL,
    UNIQUE KEY `unico_seguimiento` (`id_seguidor`,`id_seguido`),
    PRIMARY KEY (`id`),
    CONSTRAINT `seguidores_ibfk_1` FOREIGN KEY (`id_seguidor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
    CONSTRAINT `seguidores_ibfk_2` FOREIGN KEY (`id_seguido`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    /*!40101 SET character_set_client = @saved_cs_client */;

    --
    -- Dumping data for table `seguidores`
    --

    /*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

    /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
    /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
    /*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
    /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

