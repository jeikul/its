-- MySQL dump 10.10
--
-- Host: localhost    Database: content
-- ------------------------------------------------------
-- Server version	5.0.27-standard

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES gbk */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbControlTable`
--

DROP TABLE IF EXISTS `tbControlTable`;
CREATE TABLE `tbControlTable` (
  `id` int(11) NOT NULL auto_increment,
  `fdName` varchar(32) default NULL,
  `fdDescription` varchar(32) default NULL,
  `fdClause` varchar(128) default NULL,
	`fdDatabase` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlTable`
--

LOCK TABLES `tbControlTable` WRITE;
/*!40000 ALTER TABLE `tbControlTable` DISABLE KEYS */;
INSERT INTO `tbControlTable` VALUES
(1,'tbControlUser','通用控制表-用户',NULL,'control'),
(2,'tbControlTable','通用控制表-表',NULL,'control'),
(3,'tbControlRelate','通用控制表-主从关系',NULL,'control'),
(4,'tbControlField','通用控制表-字段',NULL,'control'),
(5,'tbControlGrant','通用控制表-授权',NULL,'control'),
(6,'tbControlInitType','通用控制表-字段初始值类型',NULL,'control');
/*!40000 ALTER TABLE `tbControlTable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbControlField`
--

DROP TABLE IF EXISTS `tbControlField`;
CREATE TABLE `tbControlField` (
  `id` int(11) NOT NULL auto_increment,
  `fdTableID` int(11) default NULL,
  `fdName` varchar(32) default NULL,
  `fdMin` int(11) default NULL,
  `fdMax` int(11) default NULL,
  `fdRegular` varchar(128) default NULL,
  `fdHide` tinyint(4) default NULL,
  `fdInitType` smallint(6) default NULL,
  `fdInitValue` varchar(255) default NULL,
  `fdSelect` varchar(255) default NULL,
  `fdDescription` varchar(32) default NULL,
  `fdSort` int(11) NOT NULL default '0',
  `fdOrder` int(11) NOT NULL default '10',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlField`
--

LOCK TABLES `tbControlField` WRITE;
/*!40000 ALTER TABLE `tbControlField` DISABLE KEYS */;
INSERT INTO `tbControlField` VALUES 
(1,2,'fdName',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'名称',0,10),
(2,2,'fdDescription',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'描述',0,10),
(3,4,'fdName',0,0,'',0,-1,'','','名称',0,1),
(4,4,'fdMin',0,0,'',1,1,'0','','最小值',0,20),
(5,4,'fdMax',NULL,0,'',1,1,'0','','最大值',0,21),
(6,4,'fdRegular',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'正则表达式',0,10),
(7,4,'fdHide',0,0,'√',0,1,'0','','隐藏',0,15),
(8,4,'fdInitType',0,0,'',0,0,'0','SELECT id,fdName FROM control.tbControlInitType','初始化方式',0,10),
(9,4,'fdInitValue',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'初始值',0,10),
(10,4,'fdSelect',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'选择源',0,10),
(11,4,'fdDescription',0,0,'',0,-1,'','','描述',0,2),
(12,4,'fdTableID',0,0,'',1,3,'','','',0,30),
(13,3,'fdMaster',0,0,'',0,0,'','','主表名',0,10),
(14,3,'fdSlave',0,0,'',0,0,'','','从表名',0,10),
(15,3,'fdMasterField',0,0,'',0,0,'','','主表键',0,10),
(16,3,'fdSlaveField',0,0,'',0,0,'','','从表域',0,10),
(17,6,'fdName',0,0,'',0,0,'','','名称',0,10),
(18,3,'fdCommonField',0,0,'',0,1,'','','公共关联字段',0,10),
(19,4,'fdSort',0,0,'√',0,1,'0','','排序',0,10),
(20,4,'fdOrder',0,0,'',0,1,'10','','顺序',10,0),
(21,1,'fdName',0,0,'',0,-1,'','','名称',0,10),
(22,1,'fdPassword',0,0,'',0,-1,'','','密码',0,10),
(23,5,'fdUserID',0,0,'',0,3,'fdUserID','SELECT id,fdName FROM tbControlUser','用户',0,10),
(24,5,'fdTableID',0,0,'',0,3,'fdTableID','SELECT id,fdName FROM tbControlTable','表',0,10),
(25,5,'fdInsert',0,0,'',0,-1,'','','插入',0,10),
(26,5,'fdUpdate',0,0,'',0,-1,'','','修改',0,10),
(27,5,'fdDelete',0,0,'',0,-1,'','','删除',0,10),
(28,5,'fdControl',0,0,'',0,-1,'','','控制',0,10),
(29,2,'fdClause',0,0,'',0,-1,'','','条件',0,10),
(30,2,'fdDatabase',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'数据库',0,10) ;
/*!40000 ALTER TABLE `tbControlField` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbControlRelate`
--

DROP TABLE IF EXISTS `tbControlRelate`;
CREATE TABLE `tbControlRelate` (
  `fdMaster` varchar(32) default NULL,
  `fdSlave` varchar(32) default NULL,
  `fdMasterField` varchar(32) default NULL,
  `fdSlaveField` varchar(32) default NULL,
  `id` int(11) NOT NULL auto_increment,
  `fdCommonField` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `nxControlRelate_Master` (`fdMaster`),
  KEY `nxControlRelate_Slave` (`fdSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlRelate`
--

LOCK TABLES `tbControlRelate` WRITE;
/*!40000 ALTER TABLE `tbControlRelate` DISABLE KEYS */;
INSERT INTO `tbControlRelate` VALUES 
('tbControlTable','tbControlField','id','fdTableID',1,NULL),
('tbControlTable','tbControlGrant','id','fdTableID',4,''),
('tbControlUser','tbControlGrant','id','fdUserID',5,'');
/*!40000 ALTER TABLE `tbControlRelate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbControlClause`
--

DROP TABLE IF EXISTS `tbControlClause`;
CREATE TABLE `tbControlClause` (
  `id` int(11) NOT NULL auto_increment,
  `fdTableID` smallint(6) NOT NULL default '0',
  `fdName` varchar(32) NOT NULL default '',
  `fdClause` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `nxControlClause_Table` (`fdTableID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlClause`
--

LOCK TABLES `tbControlClause` WRITE;
/*!40000 ALTER TABLE `tbControlClause` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbControlClause` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbControlInitType`
--

DROP TABLE IF EXISTS `tbControlInitType`;
CREATE TABLE `tbControlInitType` (
  `id` int(11) NOT NULL auto_increment,
  `fdName` varchar(32) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlInitType`
--

LOCK TABLES `tbControlInitType` WRITE;
/*!40000 ALTER TABLE `tbControlInitType` DISABLE KEYS */;
INSERT INTO `tbControlInitType` VALUES (1,'立即数'),
(2,'$_SESSION变量'),
(3,'$_GET变量'),
(4,'最大已有值加1'),
(5,'最小可用2的N次方'),
(6,'最小可用值');
/*!40000 ALTER TABLE `tbControlInitType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbControlGrant`
--

DROP TABLE IF EXISTS `tbControlGrant`;
CREATE TABLE `tbControlGrant` (
  `id` int(11) NOT NULL auto_increment,
  `fdUserID` int(11) NOT NULL default '0',
  `fdTableID` int(11) NOT NULL default '0',
  `fdInsert` tinyint(4) NOT NULL default '0',
  `fdUpdate` tinyint(4) NOT NULL default '0',
  `fdDelete` tinyint(4) NOT NULL default '0',
  `fdControl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlGrant`
--

LOCK TABLES `tbControlGrant` WRITE;
/*!40000 ALTER TABLE `tbControlGrant` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbControlGrant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbControlUser`
--

DROP TABLE IF EXISTS `tbControlUser`;
CREATE TABLE `tbControlUser` (
  `id` int(11) NOT NULL auto_increment,
  `fdName` varchar(32) default NULL,
  `fdPassword` varchar(77) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbControlUser`
--

LOCK TABLES `tbControlUser` WRITE;
/*!40000 ALTER TABLE `tbControlUser` DISABLE KEYS */;
INSERT INTO `tbControlUser` VALUES (1,'admin','*73033D8956BA283971BD5F8FC135A28786D6C05F');
/*!40000 ALTER TABLE `tbControlUser` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-12-11  7:23:24
