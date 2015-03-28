/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.16 : Database - iron_acl
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Table structure for table `acl_grupo` */

DROP TABLE IF EXISTS `acl_grupo`;

CREATE TABLE `acl_grupo` (
  `id_acl_grupo` INT(10) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) DEFAULT NULL,
  `fecha_creacion` DATETIME DEFAULT NULL,
  `ultima_modificacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` CHAR(1) DEFAULT NULL,
  `estado` ENUM('VIGENTE','ELIMINADO') DEFAULT 'VIGENTE',
  PRIMARY KEY (`id_acl_grupo`)
) ENGINE=MYISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `acl_grupo` */

INSERT  INTO `acl_grupo`(`id_acl_grupo`,`nombre`,`fecha_creacion`,`ultima_modificacion`,`activo`,`estado`) VALUES (1,'Iron Admin','2014-11-24 19:11:29','2014-11-24 15:19:29','S','VIGENTE');

/*Table structure for table `acl_grupo_permiso` */

DROP TABLE IF EXISTS `acl_grupo_permiso`;

CREATE TABLE `acl_grupo_permiso` (
  `id_acl_grupo_permiso` int(10) NOT NULL AUTO_INCREMENT,
  `fk_acl_grupo` int(10) DEFAULT NULL,
  `fk_acl_permiso` int(10) DEFAULT NULL,
  PRIMARY KEY (`id_acl_grupo_permiso`)
) ENGINE=MyISAM AUTO_INCREMENT=172 DEFAULT CHARSET=latin1;

/*Data for the table `acl_grupo_permiso` */

insert  into `acl_grupo_permiso`(`id_acl_grupo_permiso`,`fk_acl_grupo`,`fk_acl_permiso`) values (171,1,32),(170,1,28),(169,1,17),(168,1,18),(167,1,19),(166,1,20),(165,1,21),(164,1,22),(163,1,23),(162,1,24),(161,1,25),(160,1,26),(159,1,27),(158,1,13),(157,1,14),(156,1,16),(155,1,3),(154,1,2),(153,1,4),(152,1,5),(151,1,6),(150,1,7),(149,1,9),(148,1,10),(147,1,11),(146,1,12),(145,1,1),(144,1,30),(143,1,29);

/*Table structure for table `acl_modulo_permiso` */

DROP TABLE IF EXISTS `acl_modulo_permiso`;

CREATE TABLE `acl_modulo_permiso` (
  `id_acl_modulo_permiso` int(10) NOT NULL AUTO_INCREMENT,
  `controlador` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_acl_modulo_permiso`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COMMENT='Guarda las descrip de los módulos de permisos (controllers)';

/*Data for the table `acl_modulo_permiso` */

insert  into `acl_modulo_permiso`(`id_acl_modulo_permiso`,`controlador`,`descripcion`) values (1,'acl_grupos','ACL - Grupos'),(2,'acl_permisos','ACL - Permisos'),(3,'acl_usuarios','ACL - Usuarios'),(4,'welcome','welcome'),(5,'acl_custom','Permisos personalizados');

/*Table structure for table `acl_permiso` */

DROP TABLE IF EXISTS `acl_permiso`;

CREATE TABLE `acl_permiso` (
  `id_acl_permiso` int(10) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `identificador` varchar(100) DEFAULT NULL,
  `controlador` varchar(100) DEFAULT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `whitelist` int(1) DEFAULT NULL,
  `blacklist` int(1) DEFAULT NULL,
  `activo` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_acl_permiso`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

/*Data for the table `acl_permiso` */

insert  into `acl_permiso`(`id_acl_permiso`,`descripcion`,`identificador`,`controlador`,`accion`,`whitelist`,`blacklist`,`activo`) values (1,'Cambiar el estado del grupo','acl_grupos/cambiar_activo','acl_grupos','cambiar_activo',0,1,1),(2,'Mostrar todos los grupos','acl_grupos/listar','acl_grupos','listar',0,1,1),(3,'','acl_grupos/guardar','acl_grupos','guardar',0,1,1),(4,'','acl_grupos/permisos_grupo','acl_grupos','permisos_grupo',0,1,1),(5,'','acl_grupos/guardar_permisos_grupo','acl_grupos','guardar_permisos_grupo',0,1,1),(6,'','acl_grupos/asignar_usuarios_grupo','acl_grupos','asignar_usuarios_grupo',0,1,1),(7,'','acl_grupos/grupos_usuario','acl_grupos','grupos_usuario',0,1,1),(9,'','acl_grupos/ajax_buscar_usuario','acl_grupos','ajax_buscar_usuario',0,1,1),(10,'','acl_grupos/guardar_usuario_grupo','acl_grupos','guardar_usuario_grupo',0,1,1),(11,'','acl_grupos/desasignar_usuarios_grupo','acl_grupos','desasignar_usuarios_grupo',0,1,1),(12,'','acl_grupos/eliminar_grupos','acl_grupos','eliminar_grupos',0,1,1),(13,'','acl_permisos/index','acl_permisos','index',0,1,1),(14,'','acl_permisos/listar','acl_permisos','listar',0,1,1),(16,'','acl_permisos/guardar','acl_permisos','guardar',0,1,1),(17,'','acl_usuarios/index','acl_usuarios','index',0,1,1),(18,'','acl_usuarios/listar','acl_usuarios','listar',0,1,1),(19,'','acl_usuarios/nuevo','acl_usuarios','nuevo',0,1,1),(20,'','acl_usuarios/editar','acl_usuarios','editar',0,1,1),(21,'','acl_usuarios/guardar','acl_usuarios','guardar',0,1,1),(22,'','acl_usuarios/permisos_usuario','acl_usuarios','permisos_usuario',0,1,1),(23,'','acl_usuarios/grupos_usuario','acl_usuarios','grupos_usuario',0,1,1),(24,'','acl_usuarios/guardar_grupos_usuario','acl_usuarios','guardar_grupos_usuario',0,1,1),(25,'','acl_usuarios/guardar_permisos_usuario','acl_usuarios','guardar_permisos_usuario',0,1,1),(26,'','acl_usuarios/cambiar_activo','acl_usuarios','cambiar_activo',0,1,1),(27,'','acl_usuarios/eliminar_usuarios','acl_usuarios','eliminar_usuarios',0,1,1),(28,'','welcome/index','welcome','index',0,1,1),(29,'Crear un nuevo grupo','grupos/crear','acl_custom','acl_custom',0,1,1),(30,'Editar el nombre de un grupo','grupos/editar','acl_custom','acl_custom',0,1,1);

/*Table structure for table `acl_usuario` */

DROP TABLE IF EXISTS `acl_usuario`;

CREATE TABLE `acl_usuario` (
  `id_acl_usuario` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `contrasenia` varchar(100) DEFAULT NULL,
  `email` varchar(132) DEFAULT NULL,
  `cii` int(10) DEFAULT NULL,
  `bloqueado` char(1) DEFAULT 'N',
  `activo` char(1) DEFAULT 'S',
  `estado` enum('VIGENTE','ELIMINADO') DEFAULT 'VIGENTE',
  PRIMARY KEY (`id_acl_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `acl_usuario` */

insert  into `acl_usuario`(`id_acl_usuario`,`nombre`,`apellido`,`usuario`,`contrasenia`,`email`,`cii`,`bloqueado`,`activo`,`estado`) values (1,'Iron','Admin','ironadmin','$2a$10$X9mBl1cPM/olzH4QwCdbJ.EUxHSdQT4LTzZGq7U3gCaklr7nqdYXG','example@example.com',NULL,'N','S','VIGENTE');

/*Table structure for table `acl_usuario_grupo` */

DROP TABLE IF EXISTS `acl_usuario_grupo`;

CREATE TABLE `acl_usuario_grupo` (
  `id_acl_usuario_grupo` int(10) NOT NULL AUTO_INCREMENT,
  `fk_acl_usuario` int(10) DEFAULT NULL,
  `fk_acl_grupo` int(10) DEFAULT NULL,
  PRIMARY KEY (`id_acl_usuario_grupo`),
  UNIQUE KEY `unico` (`fk_acl_usuario`,`fk_acl_grupo`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `acl_usuario_grupo` */

insert  into `acl_usuario_grupo`(`id_acl_usuario_grupo`,`fk_acl_usuario`,`fk_acl_grupo`) values (1,1,1);

/*Table structure for table `acl_usuario_permiso` */

DROP TABLE IF EXISTS `acl_usuario_permiso`;

CREATE TABLE `acl_usuario_permiso` (
  `id_acl_usuario_permiso` int(10) NOT NULL AUTO_INCREMENT,
  `fk_acl_usuario` int(10) DEFAULT NULL,
  `fk_acl_permiso` int(10) DEFAULT NULL,
  `tipo_permiso` int(1) DEFAULT NULL COMMENT '1= permitir; 0 = denegar',
  PRIMARY KEY (`id_acl_usuario_permiso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `acl_usuario_permiso` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
