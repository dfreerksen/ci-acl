# Sequel Pro dump
# Version 2492
# http://code.google.com/p/sequel-pro
#
# Host: localhost (MySQL 5.1.44)
# Database: acl
# Generation Time: 2011-05-06 14:42:38 -0700
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table a_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `a_permissions`;

CREATE TABLE `a_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `group` varchar(255) NOT NULL DEFAULT 'Unknown',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

LOCK TABLES `a_permissions` WRITE;
/*!40000 ALTER TABLE `a_permissions` DISABLE KEYS */;
INSERT INTO `a_permissions` (`id`,`key`,`name`,`description`,`group`,`order`)
VALUES
	(1,'products-edit','Edit Products','Edit product information such as SKU, brand, description, specs, etc','Products',2),
	(2,'products-delete','Delete Products','Remove products from the site.','Products',3),
	(3,'products-add','Add Products','Add products to the site complete with SKU, brand, price, etc.','Products',1),
	(4,'products-edit-price','Edit Product Price','Ability to edit product price once it has been set.','Products',4),
	(5,'category-add','Create Categories','Ability to create new categories.','Category',1),
	(6,'category-edit','Edit Categories','Ability to edit categories after they have been created. This includes modifying parent/child relationships of categories.','Category',2),
	(7,'category-delete','Delete Categories','Remove categories.','Category',3),
	(8,'something-else','Something','Ability to do something','Unknown',1);

/*!40000 ALTER TABLE `a_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table a_role_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `a_role_permissions`;

CREATE TABLE `a_role_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

LOCK TABLES `a_role_permissions` WRITE;
/*!40000 ALTER TABLE `a_role_permissions` DISABLE KEYS */;
INSERT INTO `a_role_permissions` (`id`,`role_id`,`permission_id`)
VALUES
	(1,1,1),
	(2,1,2),
	(3,1,3),
	(4,1,4),
	(5,1,5),
	(6,1,6),
	(7,1,7),
	(8,1,8),
	(9,2,1),
	(10,2,2),
	(11,2,8);

/*!40000 ALTER TABLE `a_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table a_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `a_roles`;

CREATE TABLE `a_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `a_roles` WRITE;
/*!40000 ALTER TABLE `a_roles` DISABLE KEYS */;
INSERT INTO `a_roles` (`id`,`name`,`description`)
VALUES
	(1,'Admin','Administrator. Has access to everything.'),
	(2,'Another Account','Another account where permissions can be assigned to.');

/*!40000 ALTER TABLE `a_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table a_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `a_users`;

CREATE TABLE `a_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

LOCK TABLES `a_users` WRITE;
/*!40000 ALTER TABLE `a_users` DISABLE KEYS */;
INSERT INTO `a_users` (`id`,`role_id`,`username`)
VALUES
	(1,1,'Administrator'),
	(2,2,'User');

/*!40000 ALTER TABLE `a_users` ENABLE KEYS */;
UNLOCK TABLES;





/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
