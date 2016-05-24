/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.29 : Database - framework
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cores_department` */

DROP TABLE IF EXISTS `cores_department`;

CREATE TABLE `cores_department` (
  `pk` int(11) NOT NULL AUTO_INCREMENT,
  `depCode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `depName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `depFk` int(11) DEFAULT '0',
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stt` tinyint(4) DEFAULT '1',
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`pk`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_department` */

insert  into `cores_department`(`pk`,`depCode`,`depName`,`depFk`,`path`,`stt`,`deleted`) values (2,'trien-khai','Trien khai',3,'/3/2/',1,0),(3,'kinh-doanh','Kinh Doanh',0,'/3/',1,0),(4,'hanh-chinh','Hanh chinh',2,'/3/2/4/',1,0),(5,'tes','tes',0,'/5/',1,0),(7,'adasdsad','sdasd',0,'/7/',1,0),(11,'asdasdasdasd|11569b4a0f24585','asdsad',3,'/3/11/',1,1);

/*Table structure for table `cores_group` */

DROP TABLE IF EXISTS `cores_group`;

CREATE TABLE `cores_group` (
  `pk` int(11) NOT NULL AUTO_INCREMENT,
  `groupCode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `groupName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stt` tinyint(4) DEFAULT '1',
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`pk`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_group` */

insert  into `cores_group`(`pk`,`groupCode`,`groupName`,`stt`,`deleted`) values (1,'g1','Nhom 1',1,0),(2,'g2','Nhom 2',1,0),(3,'g3|56b1fc1ec50eb3','Nhom 3',1,1),(4,'g456b1fad5cb22a4|56b1fb10ad7ba4','g4',1,1),(5,'g3','sddasd',1,0);

/*Table structure for table `cores_group_permission` */

DROP TABLE IF EXISTS `cores_group_permission`;

CREATE TABLE `cores_group_permission` (
  `groupFk` int(11) NOT NULL,
  `permission` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`groupFk`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_group_permission` */

insert  into `cores_group_permission`(`groupFk`,`permission`) values (1,'quyen1'),(1,'vietnhap');

/*Table structure for table `cores_group_user` */

DROP TABLE IF EXISTS `cores_group_user`;

CREATE TABLE `cores_group_user` (
  `userFk` int(11) NOT NULL,
  `groupFk` int(11) NOT NULL,
  PRIMARY KEY (`groupFk`,`userFk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_group_user` */

insert  into `cores_group_user`(`userFk`,`groupFk`) values (1,1),(5,1),(6,1),(5,2),(6,2),(1,4);

/*Table structure for table `cores_preference` */

DROP TABLE IF EXISTS `cores_preference`;

CREATE TABLE `cores_preference` (
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `preference` text COLLATE utf8_unicode_ci,
  `userFk` int(11) DEFAULT '0',
  PRIMARY KEY (`uri`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_preference` */

/*Table structure for table `cores_storage` */

DROP TABLE IF EXISTS `cores_storage`;

CREATE TABLE `cores_storage` (
  `pk` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_storage` */

/*Table structure for table `cores_user` */

DROP TABLE IF EXISTS `cores_user`;

CREATE TABLE `cores_user` (
  `pk` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jobTitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `depFk` int(11) DEFAULT NULL,
  `account` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stt` tinyint(4) DEFAULT '1',
  `isAdmin` tinyint(4) DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`pk`),
  UNIQUE KEY `unqAccount` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_user` */

insert  into `cores_user`(`pk`,`fullName`,`jobTitle`,`depFk`,`account`,`pass`,`email`,`phone`,`stt`,`isAdmin`,`deleted`) values (1,'Admin','Admin',0,'admin','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,1,1,0),(4,'test','test',3,'test','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,0,0,0),(6,'Dương Tuấn Anh','job',3,'duongtuananh','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,0,0,0);

/*Table structure for table `cores_user_permission` */

DROP TABLE IF EXISTS `cores_user_permission`;

CREATE TABLE `cores_user_permission` (
  `userFk` int(11) NOT NULL,
  `permission` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userFk`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cores_user_permission` */

insert  into `cores_user_permission`(`userFk`,`permission`) values (1,'bientap'),(1,'MANAGE_USERS'),(1,'quyen1'),(1,'quyen2'),(1,'vietnhap'),(3,'MANAGE_USERS'),(4,'MANAGE_USERS'),(5,'vietnhap'),(6,'vietnhap');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
