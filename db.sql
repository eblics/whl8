-- MySQL dump 10.13  Distrib 5.7.17, for Linux (x86_64)
--
-- Host: localhost    Database: hls_prod
-- ------------------------------------------------------
-- Server version	5.7.17-log

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
-- Table structure for table `accum_strategies`
--

DROP TABLE IF EXISTS `accum_strategies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accum_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3011 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accum_strategies_bonus`
--

DROP TABLE IF EXISTS `accum_strategies_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accum_strategies_bonus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accumStrategyId` int(10) unsigned NOT NULL COMMENT '绑定到的累计策略编号',
  `strategyType` tinyint(4) NOT NULL COMMENT '策略类型',
  `strategyId` int(10) unsigned NOT NULL COMMENT '策略编号',
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `chance` int(11) NOT NULL COMMENT '可中得大奖的次数',
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_accumStrategyId` (`accumStrategyId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19065 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accum_strategies_bonus_plan`
--

DROP TABLE IF EXISTS `accum_strategies_bonus_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accum_strategies_bonus_plan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bonusId` int(10) unsigned NOT NULL COMMENT '绑定到的累计策略大奖编号',
  `userId` int(10) unsigned NOT NULL,
  `scanNum` int(11) NOT NULL COMMENT '扫码次数',
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_userId` (`userId`),
  KEY `idx_bonusId` (`bonusId`),
  KEY `uidx_bonusId_userId_rowStatus` (`bonusId`,`userId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=144542557 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accum_strategies_sub`
--

DROP TABLE IF EXISTS `accum_strategies_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accum_strategies_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `parentId` int(11) NOT NULL COMMENT 'mix_strategies ID',
  `strategyType` int(11) NOT NULL COMMENT '策略类型：0红包 1欢乐币 2乐券 3积分',
  `strategyId` int(11) NOT NULL COMMENT '策略ID',
  `start` int(11) NOT NULL COMMENT '扫码范围开始',
  `end` int(11) NOT NULL COMMENT '扫码范围结束',
  `rowStatus` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_parentId` (`parentId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=92271 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `startTime` int(11) DEFAULT NULL,
  `endTime` int(11) DEFAULT NULL,
  `imgUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL COMMENT '活动状态。0，新建；1，启用；2，停用',
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态。0，正常；1，删除',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=3113 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `activityId` int(11) DEFAULT NULL COMMENT '活动id',
  `categoryId` int(11) NOT NULL DEFAULT '0' COMMENT '产品分类',
  `productId` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
  `batchId` int(11) NOT NULL COMMENT '码批次',
  `policyLevel` int(11) NOT NULL COMMENT '策略类型',
  `policyName` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '策略名称',
  `Json` longtext,
  `theTime` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uidx_mchId_activityId_theTime` (`mchId`,`activityId`,`theTime`)
) ENGINE=InnoDB AUTO_INCREMENT=20023 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_center_token`
--

DROP TABLE IF EXISTS `app_center_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_center_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instId` int(11) DEFAULT NULL COMMENT '应用实例id',
  `mchId` int(11) DEFAULT NULL,
  `appId` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '接口调用appId',
  `appSecret` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '接口调用appSecret',
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '接口调用token',
  `tokenTime` int(11) DEFAULT '0' COMMENT '接口调用token过期时间',
  `status` int(11) DEFAULT '0' COMMENT '应用实例状态：0新建/下单 1正常 2到期',
  `rowStatus` int(11) DEFAULT '0' COMMENT '删除状态：0：正常 1：删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_inst`
--

DROP TABLE IF EXISTS `app_inst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_inst` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `mchId` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `config` varchar(1000) DEFAULT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `startTime` int(10) unsigned NOT NULL,
  `endTime` int(10) unsigned NOT NULL,
  `strategyType` int(10) unsigned DEFAULT NULL COMMENT '策略类型 0：红包，1:欢乐币，2：卡券，3：组合',
  `strategyId` int(10) unsigned DEFAULT NULL COMMENT '策略ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：新建，1：正常，2：到期',
  `prepayId` varchar(64) DEFAULT NULL,
  `codeUrl` varchar(64) DEFAULT NULL,
  `orderNumber` varchar(32) DEFAULT NULL,
  `price` int(10) unsigned NOT NULL DEFAULT '0',
  `codeUrlExpireTime` int(10) unsigned NOT NULL DEFAULT '0',
  `payStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：未支付，1：已支付',
  `rowStatus` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `apps`
--

DROP TABLE IF EXISTS `apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `desc` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL COMMENT '价格单位：d:天、m:月、y:年',
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态：0：正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `fullName` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `parentCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `north` double(9,6) DEFAULT NULL,
  `south` double(9,6) DEFAULT NULL,
  `west` double(9,6) DEFAULT NULL,
  `east` double(9,6) DEFAULT NULL,
  `centerLng` double(9,6) DEFAULT NULL,
  `centerLat` double(9,6) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0省 1直辖市 2特别行政区',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_name` (`name`) USING BTREE,
  KEY `idx_parentCode` (`parentCode`) USING BTREE,
  KEY `idx_level` (`level`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=17864 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_token`
--

DROP TABLE IF EXISTS `auth_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `appId` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `appSecret` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `accessToken` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `expireTime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mchId` (`mchId`) USING BTREE,
  UNIQUE KEY `accessToken` (`accessToken`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batchs`
--

DROP TABLE IF EXISTS `batchs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `batchNo` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `versionNum` varchar(1) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '码版本号',
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  `len` int(11) DEFAULT NULL,
  `expireTime` int(11) DEFAULT NULL,
  `ifPubCode` tinyint(1) DEFAULT NULL COMMENT '是否有对应明码',
  `state` int(11) DEFAULT NULL COMMENT '批次状态。0：申请；1：激活；2：停用',
  `createTime` int(11) DEFAULT NULL,
  `activeTime` int(11) DEFAULT NULL,
  `stopTime` int(11) DEFAULT '0',
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` int(11) DEFAULT NULL COMMENT '0，正常；1，删除',
  `productId` int(11) DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `fromWLL` tinyint(1) DEFAULT NULL COMMENT '是否是从welinklink加入的',
  `isDownloaded` tinyint(1) DEFAULT '0' COMMENT '是否下载过',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mchId_batchNo` (`mchId`,`batchNo`)
) ENGINE=InnoDB AUTO_INCREMENT=7263 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `title` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '卡片名称',
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL COMMENT '简介',
  `allowTransfer` tinyint(4) NOT NULL DEFAULT '1' COMMENT '乐券是否可以转移',
  `imgUrl` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT '图片地址',
  `totalNum` int(11) DEFAULT NULL COMMENT '数量',
  `remainNum` int(11) DEFAULT NULL,
  `probability` float DEFAULT NULL COMMENT '中奖概率',
  `parentId` int(11) NOT NULL COMMENT '父券组ID',
  `cardType` tinyint(2) NOT NULL DEFAULT '0',
  `couponGroupId` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `goodsId` int(11) NOT NULL DEFAULT '-1' COMMENT '卡券可兑换的礼品编号，如果是-1，那么表示不对应礼品',
  `pointQuantity` int(11) NOT NULL DEFAULT '0' COMMENT '可兑换的积分数量，cardType为2时，此字段有效',
  `createTime` int(11) NOT NULL,
  `updateTime` int(11) NOT NULL,
  `rowStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_cards` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=808 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cards_group`
--

DROP TABLE IF EXISTS `cards_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cards_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '券组名称',
  `description` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '简介',
  `imgUrl` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '图片地址',
  `priority` int(11) NOT NULL DEFAULT '0' COMMENT '乐券优先级。0，随机；1，按中奖概率从小到大；2，按中奖概率从大到小',
  `hasGroupBonus` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有集齐奖励',
  `bonusType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '奖励类型，0 积分，默认为0',
  `bonusQuantity` int(11) NOT NULL DEFAULT '0' COMMENT '奖励数量',
  `rowStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=506 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `parentCategoryId` int(11) DEFAULT NULL COMMENT '父类Id，如果没有，则为-1',
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `desc` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1393 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chart_orders`
--

DROP TABLE IF EXISTS `chart_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chart_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(10) unsigned NOT NULL,
  `chartId` int(10) unsigned NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `prepayId` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `codeUrl` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `orderNumber` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `payStatus` tinyint(4) NOT NULL DEFAULT '0',
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charts`
--

DROP TABLE IF EXISTS `charts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL DEFAULT '0',
  `probation` int(10) unsigned NOT NULL DEFAULT '0',
  `payment` tinyint(4) NOT NULL DEFAULT '0',
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_version`
--

DROP TABLE IF EXISTS `code_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versionNum` varchar(1) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '版本号',
  `mchCodeLen` tinyint(4) NOT NULL COMMENT '企业编码长度',
  `serialLen` int(11) DEFAULT NULL,
  `validLen` int(11) DEFAULT NULL,
  `createTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `offsetLen` int(11) DEFAULT '0' COMMENT '修正码长度',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deadlocks`
--

DROP TABLE IF EXISTS `deadlocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deadlocks` (
  `server` char(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `thread` int(10) unsigned NOT NULL,
  `txn_id` bigint(20) unsigned NOT NULL,
  `txn_time` smallint(5) unsigned NOT NULL,
  `user` char(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `hostname` char(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ip` char(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `db` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `tbl` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `idx` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lock_type` char(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lock_mode` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `wait_hold` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `victim` tinyint(3) unsigned NOT NULL,
  `query` text NOT NULL,
  PRIMARY KEY (`server`,`ts`,`thread`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geo_gps`
--

DROP TABLE IF EXISTS `geo_gps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_gps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lng` double(9,6) DEFAULT NULL COMMENT 'GPS坐标（经度）',
  `lat` double(9,6) DEFAULT NULL COMMENT 'GPS坐标（纬度）',
  `lngBaidu` double(9,6) DEFAULT NULL COMMENT '百度地图坐标（经度）',
  `latBaidu` double(9,6) DEFAULT NULL COMMENT '百度地图坐标（纬度）',
  `areaCode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '行政区划代码',
  `address` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '详细地址',
  `expireTime` int(11) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_lng_lat` (`lng`,`lat`) USING BTREE,
  KEY `idx_lngBaidu_latBaidu` (`lngBaidu`,`latBaidu`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13459285 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geo_gps2`
--

DROP TABLE IF EXISTS `geo_gps2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_gps2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lng` double(9,6) DEFAULT NULL COMMENT 'GPS坐标（经度）',
  `lat` double(9,6) DEFAULT NULL COMMENT 'GPS坐标（纬度）',
  `lngBaidu` double(9,6) DEFAULT NULL COMMENT '百度地图坐标（经度）',
  `latBaidu` double(9,6) DEFAULT NULL COMMENT '百度地图坐标（纬度）',
  `areaCode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '行政区划代码',
  `address` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '详细地址',
  `expireTime` int(11) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_lng_lat` (`lng`,`lat`) USING BTREE,
  KEY `idx_lngBaidu_latBaidu` (`lngBaidu`,`latBaidu`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11775750 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geo_gps4`
--

DROP TABLE IF EXISTS `geo_gps4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_gps4` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lng` double(9,6) DEFAULT NULL COMMENT 'GPS坐标（经度）',
  `lat` double(9,6) DEFAULT NULL COMMENT 'GPS坐标（纬度）',
  `lngBaidu` double(9,6) DEFAULT NULL COMMENT '百度地图坐标（经度）',
  `latBaidu` double(9,6) DEFAULT NULL COMMENT '百度地图坐标（纬度）',
  `areaCode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '行政区划代码',
  `address` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '详细地址',
  `expireTime` int(11) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_lng_lat` (`lng`,`lat`) USING BTREE,
  KEY `idx_lngBaidu_latBaidu` (`lngBaidu`,`latBaidu`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geo_ip`
--

DROP TABLE IF EXISTS `geo_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_ip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'GPS坐标（经度）',
  `geoId` int(11) DEFAULT NULL COMMENT '地理位置id',
  `expireTime` int(11) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ip` (`ip`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=354422 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `groupName` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '群名称',
  `groupImg` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '群图标',
  `memberNum` int(11) DEFAULT '0' COMMENT '群成员人数',
  `maxMemberNum` int(11) DEFAULT '1000' COMMENT '群人数上限',
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '进群口令',
  `status` int(11) DEFAULT '0' COMMENT '群状态：0正常 1解散 2公开',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_password` (`password`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19037 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_fishing`
--

DROP TABLE IF EXISTS `groups_fishing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_fishing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `groupId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL COMMENT '金额（单位：分）',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态：0有效 1已拆（炸了） 2已拆（没炸）',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` tinyint(2) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1972 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_fishing_log`
--

DROP TABLE IF EXISTS `groups_fishing_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_fishing_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `groupId` int(11) DEFAULT NULL,
  `fishingId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `fType` tinyint(2) DEFAULT NULL COMMENT '状态：0扔炸弹（负）1爆炸成功（正） 2捞到红包（正） 3捞到炸弹（负）',
  `amount` int(11) DEFAULT NULL COMMENT '金额（单位：分）',
  `createTime` int(11) DEFAULT NULL,
  `rowStatus` tinyint(2) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3566 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_members`
--

DROP TABLE IF EXISTS `groups_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) DEFAULT NULL COMMENT '群id',
  `nickName` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '群昵称',
  `headImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `userId` int(11) DEFAULT NULL COMMENT '用户ID',
  `role` int(11) DEFAULT '0' COMMENT '角色：0成员 1群主 2群管理',
  `msgNum` int(11) DEFAULT '0' COMMENT '已发消息数',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '成员状态：0正常 1已退出',
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_groupId_userId` (`groupId`,`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=31815 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_scanpk`
--

DROP TABLE IF EXISTS `groups_scanpk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_scanpk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) DEFAULT NULL COMMENT '群id',
  `userId` int(11) DEFAULT NULL COMMENT 'PK发起人ID',
  `pkType` int(11) DEFAULT NULL COMMENT '0 红包 1 积分 2 乐券',
  `pkAmount` int(11) DEFAULT '0' COMMENT 'PK额度：红包（精确到分） 积分(整数)',
  `startTime` int(11) DEFAULT NULL COMMENT 'PK开始时间',
  `endTime` int(11) DEFAULT NULL COMMENT 'PK结束时间',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT 'PK状态：0进行中 1结算中 2已完成',
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2091 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_scanpk_users`
--

DROP TABLE IF EXISTS `groups_scanpk_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_scanpk_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) DEFAULT NULL COMMENT '群id',
  `scanpkId` int(11) DEFAULT NULL COMMENT '群昵称',
  `userId` int(11) DEFAULT NULL COMMENT '用户ID',
  `role` int(11) DEFAULT '0' COMMENT '角色：0 PK参与人 1 PK发起人',
  `scanNum` int(11) DEFAULT '0' COMMENT 'PK周期内的扫码量',
  `winner` tinyint(1) DEFAULT '0' COMMENT '0 非赢家 1 赢家',
  `status` tinyint(1) DEFAULT '0' COMMENT '0未结算 1已结算',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`),
  KEY `idx_pkid_userid` (`scanpkId`,`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2521 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_setting`
--

DROP TABLE IF EXISTS `groups_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `productName` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '群名称',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` int(11) DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mchId` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `heartbeat`
--

DROP TABLE IF EXISTS `heartbeat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `heartbeat` (
  `ts` varchar(26) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `file` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `position` bigint(20) unsigned DEFAULT NULL,
  `relay_master_log_file` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `exec_master_log_pos` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_activity`
--

DROP TABLE IF EXISTS `hr_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `name` varchar(45) NOT NULL COMMENT '活动名称',
  `productId` int(11) unsigned NOT NULL COMMENT '指定的经销商编号，默认不指定',
  `beginDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '活动开始时间',
  `endDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '活动结束时间',
  `probability` int(11) unsigned NOT NULL COMMENT '中奖概率',
  `templateId` int(11) unsigned NOT NULL COMMENT '默认活动奖品模板',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '活动状态：0 未启用，1 启用',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100023 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_activity_config`
--

DROP TABLE IF EXISTS `hr_activity_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_activity_config` (
  `mchId` int(11) unsigned NOT NULL,
  `areasLimit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '指定大区开启活动',
  `productLimit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '指定产品开启活动',
  `specialCodeLimit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '指定特征码开启活动',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`mchId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_activity_strategy`
--

DROP TABLE IF EXISTS `hr_activity_strategy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_activity_strategy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activityId` int(11) unsigned NOT NULL COMMENT '活动编号',
  `name` varchar(45) NOT NULL COMMENT '策略名称',
  `priority` int(11) NOT NULL COMMENT '优先级',
  `type` set('1','2','3','4','5','6','7','8') NOT NULL COMMENT '策略类型：1 指定大区，2 指定特征码，3 指定经纬度，4 指定时间段 可扩展',
  `config` json DEFAULT NULL,
  `templateId` int(11) unsigned NOT NULL COMMENT '奖品模板',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '策略状态：0 未启用，1 已启用',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idxActivityId` (`activityId`)
) ENGINE=InnoDB AUTO_INCREMENT=1005 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_areas`
--

DROP TABLE IF EXISTS `hr_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_areas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) unsigned NOT NULL,
  `areas` varchar(45) NOT NULL COMMENT '大区名称',
  `prizeAreas` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启该地区中奖',
  `prizeStartDate` date NOT NULL DEFAULT '0000-00-00' COMMENT '中奖开始日期',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mchId_areas` (`mchId`,`areas`)
) ENGINE=InnoDB AUTO_INCREMENT=1235 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_charts`
--

DROP TABLE IF EXISTS `hr_charts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_charts` (
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `redpacketCount` int(10) unsigned NOT NULL COMMENT '红包发放个数',
  `redpacketAmount` int(10) unsigned NOT NULL COMMENT '红包发放金额',
  `withdrawAmount` int(10) unsigned NOT NULL COMMENT '红包提现金额',
  `serviceAmount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '服务费',
  `topupAmount` int(10) unsigned NOT NULL COMMENT '充值金额',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mchId`,`dealerId`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_code`
--

DROP TABLE IF EXISTS `hr_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `dmCode` varchar(45) NOT NULL COMMENT '二维码标签dm码',
  `orderId` int(10) unsigned NOT NULL,
  `openid` varchar(28) NOT NULL,
  `latitude` float(9,6) NOT NULL,
  `longitude` float(9,6) NOT NULL,
  `adcode` char(6) NOT NULL DEFAULT '000000',
  `address` varchar(45) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '扫码时间',
  `timestamp` int(10) unsigned NOT NULL,
  `productId` int(10) unsigned NOT NULL,
  `activityId` int(10) unsigned NOT NULL COMMENT '活动id',
  `templateId` int(10) unsigned NOT NULL COMMENT '模板id',
  `mchId` int(10) unsigned NOT NULL,
  `dealerCode` varchar(32) NOT NULL COMMENT '经销商编码',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code_index` (`code`),
  KEY `order_id_index` (`orderId`),
  KEY `openid_index` (`openid`),
  KEY `dealer_code_index` (`dealerCode`),
  KEY `idxTime` (`time`)
) ENGINE=InnoDB AUTO_INCREMENT=2466829 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_code_lib`
--

DROP TABLE IF EXISTS `hr_code_lib`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_code_lib` (
  `requestId` int(11) NOT NULL,
  `mchId` int(11) NOT NULL,
  `code` varchar(45) NOT NULL COMMENT '暗码',
  `dmCode` varchar(45) NOT NULL COMMENT '明码',
  PRIMARY KEY (`dmCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_code_lib_req`
--

DROP TABLE IF EXISTS `hr_code_lib_req`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_code_lib_req` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `codeNum` int(10) unsigned NOT NULL COMMENT '上传码的个数',
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_config`
--

DROP TABLE IF EXISTS `hr_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_config` (
  `mchId` int(11) NOT NULL,
  `serviceCharge` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启服务费收取',
  `nocodeAmount` int(11) NOT NULL COMMENT '未采集到的码中奖金额，0 不开启中奖',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`mchId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_dealer_activity_status`
--

DROP TABLE IF EXISTS `hr_dealer_activity_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_dealer_activity_status` (
  `dealerId` int(11) unsigned NOT NULL,
  `activityId` int(11) unsigned NOT NULL COMMENT '活动编号',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '经销商对应的活动状态：0 未启用，1 已启用',
  `beginDate` date NOT NULL DEFAULT '0000-00-00',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`dealerId`,`activityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_dealer_prize`
--

DROP TABLE IF EXISTS `hr_dealer_prize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_dealer_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `prizeLevel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '中奖等级，1 一等奖，2 二等奖',
  `total` int(10) unsigned NOT NULL COMMENT '奖品的总数量',
  `remainder` int(11) NOT NULL COMMENT '奖品的剩余数量',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mchId` (`mchId`,`dealerId`,`prizeLevel`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_dealer_topup`
--

DROP TABLE IF EXISTS `hr_dealer_topup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_dealer_topup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `topup_time` datetime NOT NULL,
  `topup_info` varchar(255) DEFAULT NULL COMMENT '充值说明',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '充值状态，0 未付款，1 已付款，2 发生错误',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mchId_dealerId_index` (`mchId`,`dealerId`),
  KEY `dealerId_index` (`dealerId`)
) ENGINE=InnoDB AUTO_INCREMENT=1915 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_dealers`
--

DROP TABLE IF EXISTS `hr_dealers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_dealers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL COMMENT '经销商编码',
  `parentCode` varchar(32) NOT NULL COMMENT '上级经销商编码',
  `mchId` int(10) unsigned NOT NULL,
  `name` varchar(28) NOT NULL,
  `areas` varchar(32) NOT NULL DEFAULT '未知区域' COMMENT '经销商所属区域',
  `tel` varchar(11) NOT NULL COMMENT '经销商联系方式',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_index` (`code`),
  KEY `tel_index` (`tel`),
  KEY `mch_id_index` (`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=2603 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_nocode_count`
--

DROP TABLE IF EXISTS `hr_nocode_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_nocode_count` (
  `userId` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `count` int(11) unsigned NOT NULL,
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_permissions`
--

DROP TABLE IF EXISTS `hr_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_permissions` (
  `module` varchar(32) NOT NULL COMMENT '所属模块，比如：乐码管理，活动管理',
  `name` varchar(32) NOT NULL COMMENT '权限名称',
  `key` varchar(32) NOT NULL,
  `uris` varchar(2000) NOT NULL COMMENT '权限对应的uri列表，以半角逗号分割',
  PRIMARY KEY (`module`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_product`
--

DROP TABLE IF EXISTS `hr_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) unsigned NOT NULL,
  `productCode` varchar(45) NOT NULL COMMENT '产品编码',
  `productName` varchar(45) NOT NULL COMMENT '产品名称',
  `prizeProduct` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启该产品中奖',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mchId_productCode` (`mchId`,`productCode`)
) ENGINE=InnoDB AUTO_INCREMENT=1161 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_redpacket`
--

DROP TABLE IF EXISTS `hr_redpacket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_redpacket` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `templateId` int(11) unsigned NOT NULL COMMENT '所属模版编号',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '奖励类型',
  `name` varchar(45) NOT NULL COMMENT '奖励名称',
  `amount` int(11) NOT NULL COMMENT '红包金额',
  `probability` int(11) unsigned NOT NULL COMMENT '中奖概率，此处记录的是万分之',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_template_id` (`templateId`)
) ENGINE=InnoDB AUTO_INCREMENT=1091 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_redpacket_template`
--

DROP TABLE IF EXISTS `hr_redpacket_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_redpacket_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `name` varchar(45) NOT NULL COMMENT '模版名称',
  `webAppId` int(10) unsigned NOT NULL COMMENT 'H5id',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1025 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_special_code`
--

DROP TABLE IF EXISTS `hr_special_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_special_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) unsigned NOT NULL,
  `specialCode` varchar(45) NOT NULL COMMENT '特征码',
  `prizeSpecial` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启该特征码中奖',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mchId_prizeSpecial` (`mchId`,`specialCode`)
) ENGINE=InnoDB AUTO_INCREMENT=1031 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_store`
--

DROP TABLE IF EXISTS `hr_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_store` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) unsigned NOT NULL,
  `storeCode` varchar(45) NOT NULL,
  `storeName` varchar(45) NOT NULL,
  `codeNum` int(11) NOT NULL COMMENT '已上传的码数量',
  `finished` tinyint(1) NOT NULL DEFAULT '0' COMMENT '码是否上传结束',
  `finished_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一次码上传结束时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idxStoreCode` (`storeCode`,`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8 COMMENT='终端表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_store_code`
--

DROP TABLE IF EXISTS `hr_store_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_store_code` (
  `code` varchar(45) NOT NULL,
  `storeId` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='终端码表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_user_accounts`
--

DROP TABLE IF EXISTS `hr_user_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_user_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `moneyType` tinyint(4) NOT NULL,
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId_mchId_dealerId_index` (`userId`,`mchId`,`dealerId`),
  KEY `dealerId_index` (`dealerId`)
) ENGINE=InnoDB AUTO_INCREMENT=2288819 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_user_prize`
--

DROP TABLE IF EXISTS `hr_user_prize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_user_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `prizeLevel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '中奖等级，1 一等奖，2 二等奖',
  `prizeName` varchar(16) NOT NULL,
  `getTime` datetime NOT NULL,
  `code` varchar(45) NOT NULL,
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_user_redpackets`
--

DROP TABLE IF EXISTS `hr_user_redpackets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_user_redpackets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `rewardType` tinyint(4) NOT NULL DEFAULT '1' COMMENT '奖品类型',
  `rewardName` varchar(45) NOT NULL DEFAULT '红包' COMMENT '奖品名称',
  `amount` int(11) NOT NULL,
  `getTime` datetime NOT NULL,
  `receive` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否领取了奖品',
  `code` varchar(45) NOT NULL,
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_index` (`code`),
  KEY `userId_index` (`userId`),
  KEY `dealerId_index` (`dealerId`)
) ENGINE=InnoDB AUTO_INCREMENT=2289079 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_user_withdraw`
--

DROP TABLE IF EXISTS `hr_user_withdraw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_user_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mainWithdrawId` int(10) unsigned NOT NULL COMMENT '对应hr_user_withdraw_main表的id',
  `userId` int(10) unsigned NOT NULL,
  `mchId` int(10) unsigned NOT NULL,
  `dealerId` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `serviceAmount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '提现服务费',
  `withdraw_time` datetime NOT NULL,
  `withdraw_info` varchar(1024) DEFAULT NULL COMMENT '微信红包接口返回的详细信息',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '提现状态，0 处理中，1 完成，2 失败',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId_mchId_dealerId_index` (`userId`,`mchId`,`dealerId`),
  KEY `dealerId_index` (`dealerId`),
  KEY `idx_mainWithdrawId` (`mainWithdrawId`)
) ENGINE=InnoDB AUTO_INCREMENT=248411 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_user_withdraw_main`
--

DROP TABLE IF EXISTS `hr_user_withdraw_main`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_user_withdraw_main` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `mchId` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `withdraw_time` datetime NOT NULL,
  `withdraw_info` varchar(1000) DEFAULT NULL COMMENT '微信红包接口返回的详细信息',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '提现状态，0 处理中，1 完成，2 失败',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=225359 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hr_users`
--

DROP TABLE IF EXISTS `hr_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wxId` varchar(16) NOT NULL COMMENT '微信公众号原始ID',
  `openid` varchar(28) NOT NULL,
  `userId` int(10) unsigned NOT NULL COMMENT '欢乐扫平台对应的users表id',
  `del_flag` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=64709 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_blacklist`
--

DROP TABLE IF EXISTS `ip_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'ip',
  `expireTime` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=63049 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jokes`
--

DROP TABLE IF EXISTS `jokes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jokes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '笑话题目',
  `text` varchar(500) COLLATE utf8_bin NOT NULL COMMENT '笑话文本',
  `theTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `machine_apps`
--

DROP TABLE IF EXISTS `machine_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `machine_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `appSecret` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `token` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `expireTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mall_addresses`
--

DROP TABLE IF EXISTS `mall_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mall_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mallId` int(11) DEFAULT NULL COMMENT '商城ID',
  `userId` int(11) DEFAULT NULL COMMENT '用户ID',
  `receiver` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '收货人姓名',
  `zipcode` int(11) DEFAULT NULL COMMENT '邮政编码',
  `areaCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '区域编码',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '收货地址详细',
  `phoneNum` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '收货人电话',
  `isDefault` int(11) DEFAULT '0' COMMENT '默认地址：0否 1是',
  `rowStatus` int(11) NOT NULL DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10259 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mall_categories`
--

DROP TABLE IF EXISTS `mall_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mall_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mallId` int(11) DEFAULT NULL,
  `parentCategoryId` int(11) DEFAULT NULL COMMENT '父类Id，如果没有，则为-1',
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `desc` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mallId`) USING BTREE,
  KEY `idx_parentCategoryId` (`parentCategoryId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mall_goods`
--

DROP TABLE IF EXISTS `mall_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mall_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mallId` int(11) DEFAULT NULL COMMENT '商城ID',
  `goodsName` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '产品名称',
  `categoryId` int(11) DEFAULT NULL,
  `oPrice` int(11) DEFAULT NULL COMMENT '原始价格(积分)',
  `price` int(11) DEFAULT NULL COMMENT '销售价格(积分)',
  `description` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '产品描述',
  `exchangeType` int(11) NOT NULL DEFAULT '0' COMMENT '0：积分兑换，1：乐券兑换',
  `isViral` int(11) NOT NULL DEFAULT '0' COMMENT '是否是虚拟商品，0：不是，1：是',
  `viralPlatform` int(11) DEFAULT NULL COMMENT '虚拟平台，用于指定平台的调用接口',
  `viralAmount` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟商品的兑换数量',
  `createOrder` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否需要创建订单',
  `createTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updateTime` int(11) DEFAULT NULL COMMENT '更新时间',
  `rowStatus` int(11) NOT NULL DEFAULT '0' COMMENT '行状态：0正常 1删除',
  `activeStart` int(10) unsigned NOT NULL COMMENT '活动开始时间',
  `activeEnd` int(10) unsigned NOT NULL COMMENT '活动结束时间',
  `isActived` tinyint(3) unsigned DEFAULT '0' COMMENT '是否参与活动：0 不参与 1 参与',
  `inventory` int(10) unsigned DEFAULT '0' COMMENT '库存量',
  `listings` int(10) unsigned DEFAULT '0' COMMENT '上架量',
  `exchanged` int(10) unsigned DEFAULT '0' COMMENT '已兑换量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mall_goods_images`
--

DROP TABLE IF EXISTS `mall_goods_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mall_goods_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodsId` int(11) DEFAULT NULL COMMENT '商品ID',
  `default` int(11) NOT NULL DEFAULT '0' COMMENT '默认封面：0否 1是',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '图片路径',
  `rowStatus` int(11) NOT NULL DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=864 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mall_orders`
--

DROP TABLE IF EXISTS `mall_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mall_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mallId` int(11) DEFAULT NULL COMMENT '商城ID',
  `userId` int(11) DEFAULT NULL COMMENT '用户ID',
  `amount` int(11) DEFAULT NULL COMMENT '订单金额',
  `orderNum` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `addressId` int(11) DEFAULT NULL COMMENT '收货信息ID',
  `addressText` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '收货地址拼接保存，以|分隔',
  `reMark` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态：0创建 1完成 ',
  `shippingStatus` int(11) NOT NULL DEFAULT '0' COMMENT '物流状态：0未发货 1已发货 2已收货 ',
  `payStatus` int(11) NOT NULL DEFAULT '0' COMMENT '支付状态：0未支付 1已支付 ',
  `rowStatus` int(11) NOT NULL DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=987 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mall_orders_goods`
--

DROP TABLE IF EXISTS `mall_orders_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mall_orders_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mallId` int(11) DEFAULT NULL COMMENT '商城ID',
  `userId` int(11) DEFAULT NULL COMMENT '用户ID',
  `orderId` int(11) DEFAULT NULL COMMENT '订单ID：mall_orders表ID',
  `goodsId` int(11) DEFAULT NULL COMMENT '商品ID',
  `goodsName` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '商品名称',
  `goodsOprice` int(11) DEFAULT NULL COMMENT '商品原价',
  `goodsPrice` int(11) DEFAULT NULL COMMENT '商品现价',
  `goodsNumber` int(11) DEFAULT NULL COMMENT '商品ID',
  `isViral` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是虚拟商品',
  `viralPlatform` tinyint(4) DEFAULT NULL COMMENT '对应的虚拟发放平台',
  `viralAmount` int(11) DEFAULT NULL COMMENT '发放的数量',
  `rowStatus` int(11) NOT NULL DEFAULT '0' COMMENT '行状态：0正常 1删除',
  `cardName` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `cardId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1053 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `malls`
--

DROP TABLE IF EXISTS `malls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `malls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '企业ID',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '商城名称',
  `desc` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '商城描述',
  `startTime` int(11) DEFAULT NULL COMMENT '服务开始时间',
  `endTime` int(11) DEFAULT NULL COMMENT '服务结束时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '商城状态：0申请 1正常 2关闭 3到期',
  `rowStatus` int(11) NOT NULL DEFAULT '0' COMMENT '行状态：0正常 1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_accounts`
--

DROP TABLE IF EXISTS `mch_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `realName` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `salt` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mail` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phoneNum` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` int(11) DEFAULT NULL COMMENT '角色，默认0 管理员,1生产者,2活动制定者,3,活动发布者',
  `mchId` int(11) DEFAULT NULL,
  `idCardNum` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `idCardImgUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `noSms` int(11) DEFAULT NULL COMMENT '免短信验证登录 0 不免，1 免,功能预留扩展',
  `cStatus` tinyint(2) DEFAULT '0' COMMENT '1状态代表短信验证成功',
  `sessionId` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'session参数',
  `cExpired` int(11) DEFAULT NULL COMMENT '过期时间',
  `status` int(11) DEFAULT NULL COMMENT '0,正常;2,锁定;3,删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ma_usename` (`phoneNum`,`userName`) USING BTREE,
  KEY `idx_ma_mch` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=707 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_accounts_ext`
--

DROP TABLE IF EXISTS `mch_accounts_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_accounts_ext` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL,
  `mchId` int(11) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '0',
  `createTime` int(10) unsigned NOT NULL DEFAULT '0',
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_balances`
--

DROP TABLE IF EXISTS `mch_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_balances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mchId` (`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_charts`
--

DROP TABLE IF EXISTS `mch_charts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_charts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(10) unsigned NOT NULL,
  `chartId` int(10) unsigned NOT NULL,
  `expireTime` int(10) unsigned NOT NULL DEFAULT '0',
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_opr_log`
--

DROP TABLE IF EXISTS `mch_opr_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_opr_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oprdetail` varchar(4096) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '客户的操作，记录格式json\r\n{time:时间戳，detail:操作内容,table:操作的表，detailId:操作表中对应的字段ID}',
  `userid` int(11) NOT NULL,
  `oprtime` int(11) NOT NULL COMMENT '记录操作的时间戳',
  `oprobject` tinyint(4) NOT NULL COMMENT 'static $BATCH = 1;//乐码操作\r\nstatic $Activity = 2;//活动操作\r\nstatic $Card = 3;//乐券操作\r\nstatic $Product =4;// 产品操作\r\nstatic $RedPacket =5;//红包操作\r\nstatic $Setting = 6;//设置操作\r\nstatic $User = 7; //用户信息操作\r\n	',
  PRIMARY KEY (`id`),
  KEY `mchid` (`userid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=239785 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_orders`
--

DROP TABLE IF EXISTS `mch_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `orderId` varchar(32) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 待支付， 1 已支付',
  `createTime` int(10) unsigned NOT NULL,
  `updateTime` int(10) unsigned NOT NULL,
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  `level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '订单类型 0 企业账户余额充值订单（红包代发） 1 企业vip购买订单 可拓展',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_permissions`
--

DROP TABLE IF EXISTS `mch_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '权限名称',
  `module` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '所属模块，比如：乐码管理，活动管理',
  `key` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `uris` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '权限对应的uri列表，以半角都好分割',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_role_permissions`
--

DROP TABLE IF EXISTS `mch_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_role_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roleId` int(10) unsigned NOT NULL COMMENT '角色编号',
  `permissionKey` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '权限的键',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4751 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_roles`
--

DROP TABLE IF EXISTS `mch_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roleName` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '角色名称',
  `mchId` int(10) unsigned NOT NULL COMMENT '商户编号，不同的商户可以管理自己角色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=381 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_salesman`
--

DROP TABLE IF EXISTS `mch_salesman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_salesman` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(10) unsigned NOT NULL,
  `realName` varchar(16) NOT NULL COMMENT '业务员真实姓名',
  `mobile` char(11) NOT NULL,
  `idCardNo` char(18) NOT NULL COMMENT '身份证号码',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `rowStatus` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_mobile_idCardNo` (`mobile`,`idCardNo`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_scan_rules`
--

DROP TABLE IF EXISTS `mch_scan_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_scan_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL COMMENT '商户id',
  `times` int(11) NOT NULL COMMENT '限制次数',
  `unit` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '限制单位：i，分钟；h，小时；m，月；y，年',
  `scan_other_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扫描他人的码次数限制',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mchId` (`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_templates`
--

DROP TABLE IF EXISTS `mch_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `template_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_mchId_title` (`mchId`,`title`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_template_id` (`template_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1302483 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mch_trans`
--

DROP TABLE IF EXISTS `mch_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mch_trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL COMMENT '交易金额，正数为充值，负数为提取',
  `theTime` int(11) DEFAULT NULL,
  `notes` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '交易备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `merchants`
--

DROP TABLE IF EXISTS `merchants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `merchants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codeVersion` varchar(8) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '使用的码版本',
  `code` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '企业编码',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mail` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phoneNum` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `licenseNo` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `licenseImgUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `idCardNum` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `idCardImgUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `desc` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `checkTime` int(11) DEFAULT NULL,
  `checkReason` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0,新建;1,已审核;2,已驳回;3,冻结;4,待审;5预审核',
  `wxName` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '微信ID name',
  `wxYsId` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '原始ID',
  `wxAppId` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAppSecret` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxQrcodeUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '微信公众号二维码',
  `wxMchId` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxPayKey` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '微信支付API密钥',
  `certPath` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '支付证书cert路径',
  `keyPath` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '支付证书key路径',
  `caPath` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '支付证书ca路径',
  `wxApiToken` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '公众平台开发者TOKEN（审核时自动生成）',
  `updateTime` int(11) DEFAULT NULL,
  `version` float DEFAULT NULL,
  `subscribeMsg` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '订阅消息',
  `subscribeImgUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '订阅时显示的图片',
  `baseToken` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '全局基础token',
  `baseTokenTime` int(11) DEFAULT NULL,
  `jsapiTicket` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'jsapi接口ticket',
  `jsapiTicketTime` int(11) DEFAULT NULL,
  `wxSendName` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '红包发送者名称',
  `wxActName` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '红包活动名称',
  `wxRpTotalNum` int(11) DEFAULT NULL COMMENT '裂变红包默认裂变人数',
  `wxWishing` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '红包祝福语',
  `wxRemark` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '红包备注',
  `wxName_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxYsId_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAppId_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAppSecret_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxQrcodeUrl_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxMchId_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxPayKey_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `certPath_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `keyPath_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `caPath_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxApiToken_shop` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `subscribeMsg_shop` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `subscribeImgUrl_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `baseToken_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `baseTokenTime_shop` int(11) DEFAULT NULL,
  `jsapiTicket_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `jsapiTicketTime_shop` int(11) DEFAULT NULL,
  `wxSendName_shop` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxActName_shop` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxRpTotalNum_shop` int(11) DEFAULT NULL,
  `wxWishing_shop` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxRemark_shop` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAuthStatus` tinyint(4) DEFAULT '0' COMMENT '微信公众号授权状态：0未授权 1已授权',
  `wxAuthorizerAccessToken` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '授权方接口调用凭据',
  `wxAuthorizerAccessTokenTime` int(11) DEFAULT NULL COMMENT '授权方接口调用凭据 过期时间',
  `wxAuthorizerRefreshToken` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '接口调用凭据刷新令牌',
  `wxAuthorizerJsapiTicket` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '第三方授权 jssdk ticket',
  `wxAuthorizerJsapiTicketTime` int(11) DEFAULT NULL COMMENT '第三方授权 jssdk ticket过期时间',
  `wxAuthorizerInfo` varchar(3000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '授权方的公众号帐号基本信息json串保存',
  `wxAuthStatus_shop` tinyint(4) DEFAULT '0',
  `wxAuthorizerAccessToken_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAuthorizerAccessTokenTime_shop` int(11) DEFAULT NULL,
  `wxAuthorizerRefreshToken_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAuthorizerJsapiTicket_shop` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxAuthorizerJsapiTicketTime_shop` int(11) DEFAULT NULL,
  `wxAuthorizerInfo_shop` varchar(3000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `youzanAppId` varchar(18) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '有赞商户平台APPID',
  `youzanAppSecret` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '有赞商户平台appSecret',
  `marcketUrl` varchar(1024) DEFAULT NULL,
  `fromWLL` tinyint(1) DEFAULT NULL COMMENT '是否是从welinklink加入的',
  `WLLCode` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `wxSendType` int(2) DEFAULT '0' COMMENT '0微信红包,1微信企业付款',
  `wxSendTip` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '金额大于1元即可提取现金红包' COMMENT '用户提现界面通知内容',
  `withCaptcha` tinyint(1) DEFAULT '0' COMMENT '扫码开启验证码：0否 1是',
  `geoLocation` tinyint(1) DEFAULT '0' COMMENT '扫码获取地理位置：0开启 1关闭',
  `payAccountType` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `codeLimited` bigint(20) DEFAULT NULL COMMENT '码申请数量限制',
  `expired` date DEFAULT NULL COMMENT '新注册用户 试用过期时间 在is_formal 为0（试用期）时有效',
  `is_formal` tinyint(2) DEFAULT '1' COMMENT '0 试用期 1 转正',
  `grade` tinyint(3) DEFAULT '0' COMMENT '企业当前vip等级 0 企业vip基础版 1 企业vip-基础版 2 企业vip-升级版 3 企业vip-高级版',
  `concurrencyNum` int(10) unsigned NOT NULL DEFAULT '10' COMMENT '企业的扫码单位时间并发数',
  `dealer_code` varchar(10) DEFAULT NULL COMMENT '代理商代码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_wxAppId` (`wxAppId`),
  UNIQUE KEY `idx_wxYsId` (`wxYsId`) USING BTREE,
  UNIQUE KEY `idx_wxYsId_shop` (`wxYsId_shop`) USING BTREE,
  UNIQUE KEY `idx_wxAppId_shop` (`wxAppId_shop`) USING BTREE,
  UNIQUE KEY `idx_code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=427 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `merchants_ext`
--

DROP TABLE IF EXISTS `merchants_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `merchants_ext` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(10) unsigned NOT NULL,
  `isHr` tinyint(4) NOT NULL DEFAULT '0',
  `withdrawLimit` int(10) unsigned NOT NULL,
  `mchLogo` varchar(255) DEFAULT NULL,
  `mchShop` tinyint(4) DEFAULT '0' COMMENT '0:未开通，1:开通',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mchId_index` (`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mix_strategies`
--

DROP TABLE IF EXISTS `mix_strategies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mix_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `hasEnabled` tinyint(2) DEFAULT '0' COMMENT '奖品发完子策略是否失效',
  `rowStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=458 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mix_strategies_sub`
--

DROP TABLE IF EXISTS `mix_strategies_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mix_strategies_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `parentId` int(11) NOT NULL COMMENT 'mix_strategies ID',
  `strategyType` int(11) NOT NULL COMMENT '策略类型：0红包 1欢乐币 2乐券 3积分',
  `strategyId` int(11) NOT NULL COMMENT '策略ID',
  `weight` int(11) NOT NULL COMMENT '策略匹配权重',
  `rowStatus` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_parentId` (`parentId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5958 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `multi_strategies`
--

DROP TABLE IF EXISTS `multi_strategies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multi_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `multi_strategies_sub`
--

DROP TABLE IF EXISTS `multi_strategies_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multi_strategies_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `parentId` int(11) NOT NULL COMMENT 'mix_strategies ID',
  `strategyType` int(11) NOT NULL COMMENT '策略类型：0红包 1欢乐币 2乐券 3积分',
  `strategyId` int(11) NOT NULL COMMENT '策略ID',
  `weight` int(11) NOT NULL COMMENT '策略匹配权重',
  `rowStatus` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_parentId` (`parentId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1008 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `obj_tickets`
--

DROP TABLE IF EXISTS `obj_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obj_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `objId` int(11) DEFAULT NULL COMMENT '对象id，即happy_coins,cards,red_packtes等表的id',
  `objType` int(11) DEFAULT NULL COMMENT '对象类型。0:红包；1：欢乐币；2：卡片',
  `ticket` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '票据，字符串，sha1(id+time())，据此生成字符串',
  `expireTime` int(11) DEFAULT NULL COMMENT '票据过期时间',
  `scaned` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '扫描状态',
  `confirmed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户是否确认转移',
  PRIMARY KEY (`id`),
  KEY `idx_ot` (`userId`,`role`,`objId`,`objType`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opp_accounts`
--

DROP TABLE IF EXISTS `opp_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opp_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `realName` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `salt` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `phoneNum` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mail` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` int(2) DEFAULT NULL COMMENT '角色，0超级管理员,默认1管理员,2普通操作人员',
  `auth_token` int(11) NOT NULL DEFAULT '0',
  `sessionKeys` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `status` int(2) DEFAULT NULL COMMENT '0未登录,1激活,2禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName` (`userName`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opp_dynamic`
--

DROP TABLE IF EXISTS `opp_dynamic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opp_dynamic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `adminId` int(11) unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `occTime` int(11) unsigned NOT NULL,
  `targetId` int(11) unsigned NOT NULL,
  `targetTable` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `target` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44919 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `points`
--

DROP TABLE IF EXISTS `points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '积分策略名称',
  `mchId` int(11) DEFAULT NULL COMMENT '企业ID',
  `priority` int(11) NOT NULL DEFAULT '0' COMMENT '积分获取优先级。0，随机；1，额度从小到大；2，额度从大到小',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` tinyint(1) DEFAULT '0' COMMENT '0正常 1删除',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=767 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `points_sub`
--

DROP TABLE IF EXISTS `points_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL COMMENT '积分额度',
  `num` int(11) NOT NULL COMMENT '数量',
  `remainNum` int(11) NOT NULL COMMENT '剩余数量',
  `probability` float NOT NULL COMMENT '中奖概率',
  `third_number` tinyint(4) NOT NULL DEFAULT '0' COMMENT '积分所属平台',
  `parentId` int(11) NOT NULL COMMENT '父id',
  PRIMARY KEY (`id`),
  KEY `idx_parentId` (`parentId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1464 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `description` varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `imgUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `unit` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `specification` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `fromWLL` tinyint(1) DEFAULT NULL COMMENT '是否是从welinklink加入的',
  `WLLCode` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `red_packets`
--

DROP TABLE IF EXISTS `red_packets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `red_packets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `rpType` int(1) DEFAULT NULL COMMENT '红包类型：0，普通；1，裂变',
  `amtType` int(11) DEFAULT NULL COMMENT '数量的生成方法，0：固定，1：随机',
  `levelType` int(11) NOT NULL DEFAULT '0' COMMENT '是否是分级红包，如果是分级红包，需要读取子表',
  `priority` int(11) NOT NULL DEFAULT '0' COMMENT '分级红包优先级。0，随机；1，金额从小到大；2，金额从大到小',
  `amount` int(11) DEFAULT NULL COMMENT '固定金额',
  `minAmount` int(11) DEFAULT NULL COMMENT '最小金额',
  `maxAmount` int(11) DEFAULT NULL COMMENT '最大金额',
  `ruleStr` varchar(255) NOT NULL DEFAULT '',
  `totalAmount` int(11) DEFAULT NULL COMMENT '总金额',
  `remainAmount` int(11) DEFAULT NULL COMMENT '剩余金额',
  `reAmtType` int(11) DEFAULT NULL COMMENT '裂变红包金额的设置方法，默认0，随机，对应微信接口的ALL_RAND',
  `subActivityId` int(11) DEFAULT NULL COMMENT '子活动ID',
  `limitType` int(11) DEFAULT NULL COMMENT '上限类型： 0（数量） 1（金额）',
  `totalNum` int(11) DEFAULT NULL COMMENT '总数',
  `remainNum` int(11) DEFAULT NULL COMMENT '剩余数量',
  `probability` float DEFAULT NULL COMMENT '中奖概率',
  `failureType` int(11) NOT NULL COMMENT '如果没有匹配到红包，对应的失败策略。0：笑话',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `rowStatus` tinyint(1) DEFAULT '0',
  `payment` int(1) DEFAULT '0' COMMENT '0 微信红包,1 企业付款',
  `isDirect` int(1) DEFAULT '0' COMMENT '1 直接发放',
  `withBalance` tinyint(1) DEFAULT '0' COMMENT '合并余额发放：0否 1是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8847 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `red_packets_sub`
--

DROP TABLE IF EXISTS `red_packets_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `red_packets_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL COMMENT '金额，单位分',
  `num` int(11) NOT NULL COMMENT '数量',
  `remainNum` int(11) NOT NULL COMMENT '剩余数量',
  `probability` float NOT NULL COMMENT '概率',
  `parentId` int(11) NOT NULL COMMENT '分级红包id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19895 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_activity_evaluating`
--

DROP TABLE IF EXISTS `rpt_activity_evaluating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_activity_evaluating` (
  `mchId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `activityId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `batchId` int(11) NOT NULL,
  `theDate` date NOT NULL,
  `rpAmount` int(11) NOT NULL COMMENT '红包金额',
  `scanCount` int(11) NOT NULL COMMENT '扫码次数',
  `rpNum` int(11) NOT NULL,
  PRIMARY KEY (`mchId`,`userId`,`activityId`,`categoryId`,`productId`,`batchId`,`theDate`),
  KEY `uidx_1` (`mchId`,`theDate`),
  KEY `uidx_2` (`mchId`,`activityId`,`theDate`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_area_daily`
--

DROP TABLE IF EXISTS `rpt_area_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_area_daily` (
  `mchId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `proCode` varchar(8) NOT NULL DEFAULT '0',
  `cityCode` varchar(8) NOT NULL DEFAULT '0',
  `areaCode` varchar(8) NOT NULL DEFAULT '0',
  `productId` int(11) NOT NULL,
  `batchId` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `scanNum` int(11) NOT NULL COMMENT '日扫码量',
  `redNum` int(11) NOT NULL COMMENT '日红包金额',
  `pointAmount` int(11) NOT NULL COMMENT '积分金额',
  PRIMARY KEY (`mchId`,`userId`,`areaCode`,`date`,`batchId`,`productId`),
  KEY `idx_proCode` (`proCode`),
  KEY `idx_areaCode` (`areaCode`),
  KEY `idx_cityCode` (`cityCode`),
  KEY `uidx_quyu_city` (`mchId`,`date`,`cityCode`),
  KEY `uidx_useIndex_pro` (`mchId`,`date`,`userId`,`proCode`),
  KEY `uidx_useIndex` (`mchId`,`date`,`userId`,`areaCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_area_scanall`
--

DROP TABLE IF EXISTS `rpt_area_scanall`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_area_scanall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `date` date DEFAULT '1970-01-01',
  `hour` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `productid` int(11) DEFAULT NULL,
  `batchid` int(11) DEFAULT NULL,
  `scanNum` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_mchId_date_hour_productid_batchid` (`mchId`,`date`,`hour`,`productid`,`batchid`) USING BTREE,
  KEY `idx_hour` (`hour`) USING BTREE,
  KEY `uidx_mchId_date` (`mchId`,`date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8743834 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_geo_daily`
--

DROP TABLE IF EXISTS `rpt_geo_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_geo_daily` (
  `scanCount` int(11) NOT NULL,
  `mchId` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `scanDate` date NOT NULL,
  `batchId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `cityCode` varchar(8) NOT NULL,
  `proCode` varchar(20) NOT NULL,
  `latScale` int(11) NOT NULL,
  `lngScale` int(11) NOT NULL,
  `scale` bigint(20) NOT NULL,
  PRIMARY KEY (`mchId`,`level`,`scanDate`,`batchId`,`productId`,`cityCode`,`proCode`,`latScale`,`lngScale`,`scale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_geo_monthly`
--

DROP TABLE IF EXISTS `rpt_geo_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_geo_monthly` (
  `scanCount` int(11) NOT NULL,
  `mchId` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `scanDate` varchar(7) NOT NULL,
  `batchId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `cityCode` varchar(8) NOT NULL,
  `proCode` varchar(8) NOT NULL,
  `latScale` int(11) NOT NULL,
  `lngScale` int(11) NOT NULL,
  `scale` bigint(20) NOT NULL,
  PRIMARY KEY (`mchId`,`level`,`scanDate`,`batchId`,`productId`,`cityCode`,`proCode`,`latScale`,`lngScale`,`scale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_geo_weekly`
--

DROP TABLE IF EXISTS `rpt_geo_weekly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_geo_weekly` (
  `scanCount` int(11) NOT NULL,
  `mchId` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `scanDate` varchar(7) NOT NULL,
  `batchId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `cityCode` varchar(8) NOT NULL,
  `proCode` varchar(8) NOT NULL,
  `latScale` int(11) NOT NULL,
  `lngScale` int(11) NOT NULL,
  `scale` bigint(20) NOT NULL,
  PRIMARY KEY (`mchId`,`level`,`scanDate`,`batchId`,`productId`,`cityCode`,`proCode`,`latScale`,`lngScale`,`scale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_user_daily`
--

DROP TABLE IF EXISTS `rpt_user_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_user_daily` (
  `mchId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `proCode` varchar(8) NOT NULL DEFAULT '000000' COMMENT '省份',
  `cityCode` varchar(8) NOT NULL DEFAULT '000000' COMMENT '城市',
  `areaCode` varchar(8) NOT NULL DEFAULT '000000' COMMENT '区域',
  `batchId` int(11) NOT NULL COMMENT '乐码批次',
  `productId` int(11) NOT NULL COMMENT '产品',
  `theDate` date NOT NULL,
  `rpAmount` int(11) NOT NULL COMMENT '红包金额',
  `transAmount` int(11) NOT NULL COMMENT '提现金额',
  `pointAmount` int(11) NOT NULL COMMENT '积分金额',
  `scanCount` int(11) NOT NULL COMMENT '扫码次数',
  `cardCount` int(11) NOT NULL COMMENT '卡券数量',
  `pointUsed` int(11) NOT NULL COMMENT '积分使用',
  PRIMARY KEY (`userId`,`mchId`,`proCode`,`cityCode`,`areaCode`,`batchId`,`productId`,`theDate`),
  KEY `idx_areaCode` (`areaCode`),
  KEY `idx_cityCode` (`cityCode`),
  KEY `idx_proCode` (`proCode`),
  KEY `uidx_mchId_theDate_trans` (`mchId`,`theDate`,`transAmount`),
  KEY `uidx_mchId_theDate_scanCount` (`mchId`,`theDate`,`scanCount`),
  KEY `uidx_useIndex` (`mchId`,`theDate`,`userId`,`scanCount`,`rpAmount`,`transAmount`,`cardCount`,`pointAmount`,`pointUsed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_user_monthly`
--

DROP TABLE IF EXISTS `rpt_user_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_user_monthly` (
  `mchId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `proCode` varchar(8) NOT NULL DEFAULT '000000' COMMENT '省份',
  `cityCode` varchar(8) NOT NULL DEFAULT '000000' COMMENT '城市',
  `areaCode` varchar(8) NOT NULL DEFAULT '000000' COMMENT '区域',
  `batchId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `theDate` varchar(7) NOT NULL,
  `rpAmount` int(11) NOT NULL COMMENT '红包金额',
  `transAmount` int(11) NOT NULL COMMENT '提现金额',
  `pointAmount` int(11) NOT NULL COMMENT '积分金额',
  `scanCount` int(11) NOT NULL COMMENT '扫码次数',
  `cardCount` int(11) NOT NULL COMMENT '卡券数量',
  `pointUsed` int(11) NOT NULL COMMENT '积分使用',
  PRIMARY KEY (`mchId`,`userId`,`theDate`,`proCode`,`cityCode`,`areaCode`,`batchId`,`productId`),
  KEY `uidx_useIndex` (`mchId`,`theDate`,`userId`,`batchId`,`productId`),
  KEY `idx_areaCode` (`areaCode`),
  KEY `idx_cityCode` (`cityCode`),
  KEY `idx_proCode` (`proCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_user_portrait`
--

DROP TABLE IF EXISTS `rpt_user_portrait`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_user_portrait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `proCode` varchar(255) DEFAULT '0' COMMENT '省份',
  `cityCode` varchar(255) DEFAULT '0' COMMENT '城市',
  `areaCode` varchar(255) DEFAULT '0' COMMENT '区域',
  `age` varchar(20) DEFAULT '0' COMMENT '年龄',
  `sex` int(11) DEFAULT '1' COMMENT '性别（1、男 2、女）',
  `constellation` varchar(20) DEFAULT '0' COMMENT '星座1-12 星座（白羊座、金牛座、双子座、巨蟹座、狮子座、处女座、天秤座、天蝎座、射手座、摩羯座、水瓶座、双鱼座）',
  `time` varchar(50) DEFAULT '0' COMMENT '消费段',
  `total` int(11) DEFAULT '0' COMMENT '这个特征的扫码总数',
  `num` int(11) DEFAULT '0' COMMENT '人数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_index` (`mchId`,`proCode`,`cityCode`,`areaCode`,`age`,`sex`,`constellation`,`time`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE,
  KEY `idx_cityCode` (`cityCode`) USING BTREE,
  KEY `idx_proCode` (`proCode`) USING BTREE,
  KEY `uidx_useindex` (`mchId`,`age`,`sex`,`constellation`,`time`,`total`) USING BTREE,
  KEY `uidx_mchId_time` (`mchId`,`time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_user_rank`
--

DROP TABLE IF EXISTS `rpt_user_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_user_rank` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户标识',
  `userId` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `proCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT '-1' COMMENT '省份代码',
  `cityCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT '-1' COMMENT '城市代码',
  `areaCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT '-1' COMMENT '区域代码',
  `batchId` int(11) DEFAULT '-1',
  `productId` int(11) DEFAULT '-1',
  `theDate` date DEFAULT '1970-01-01' COMMENT '扫码时间',
  `scanNum` int(11) NOT NULL DEFAULT '0' COMMENT '日扫码量',
  `transAmount` int(11) DEFAULT '0' COMMENT '提现金额',
  `pointAmount` int(11) DEFAULT '0' COMMENT '积分金额',
  `pointUsed` int(11) DEFAULT '0' COMMENT '积分使用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_mchId_userId_areaCode_batchId_productId_theDate` (`mchId`,`userId`,`areaCode`,`batchId`,`productId`,`theDate`) USING BTREE,
  KEY `idx_proCode` (`proCode`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE,
  KEY `idx_cityCode` (`cityCode`) USING BTREE,
  KEY `uidx_useIndex` (`mchId`,`theDate`,`userId`,`scanNum`,`transAmount`,`pointAmount`,`pointUsed`) USING BTREE,
  KEY `uidx_proCode` (`mchId`,`proCode`,`theDate`,`userId`,`scanNum`,`transAmount`,`pointAmount`,`pointUsed`) USING BTREE,
  KEY `uidx_cityCode` (`mchId`,`cityCode`,`theDate`,`userId`,`scanNum`,`transAmount`,`pointAmount`,`pointUsed`) USING BTREE,
  KEY `uidx_mchId_theDate` (`mchId`,`theDate`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15855494 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_user_rank_all`
--

DROP TABLE IF EXISTS `rpt_user_rank_all`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_user_rank_all` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户标识',
  `userId` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `scanNum` int(11) NOT NULL DEFAULT '0' COMMENT '日扫码量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_userId` (`userId`) USING BTREE,
  UNIQUE KEY `uidx_mchId_userId` (`mchId`,`userId`) USING BTREE,
  KEY `idx_scanNum` (`scanNum`) USING BTREE,
  KEY `uidx_useIndex` (`mchId`,`userId`,`scanNum`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8743824 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_user_weekly`
--

DROP TABLE IF EXISTS `rpt_user_weekly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_user_weekly` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户标识',
  `userId` int(11) NOT NULL,
  `proCode` varchar(255) CHARACTER SET utf8 DEFAULT '000000' COMMENT '省份',
  `cityCode` varchar(255) CHARACTER SET utf8 DEFAULT '000000' COMMENT '城市',
  `areaCode` varchar(255) CHARACTER SET utf8 DEFAULT '000000' COMMENT '区域',
  `batchId` int(11) DEFAULT '0',
  `productId` int(11) DEFAULT '0',
  `theDate` varchar(20) COLLATE utf8_bin NOT NULL,
  `rpAmount` int(11) NOT NULL DEFAULT '0' COMMENT '红包金额',
  `transAmount` int(11) NOT NULL DEFAULT '0' COMMENT '提现金额',
  `pointAmount` int(11) NOT NULL DEFAULT '0' COMMENT '积分金额',
  `scanCount` int(11) NOT NULL DEFAULT '0' COMMENT '扫码次数',
  `cardCount` int(11) NOT NULL DEFAULT '0' COMMENT '卡券数量',
  `pointUsed` int(11) NOT NULL DEFAULT '0' COMMENT '积分使用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_mchId_userId_theDate_batchId_productId` (`userId`,`theDate`,`mchId`,`proCode`,`cityCode`,`areaCode`,`batchId`,`productId`) USING BTREE,
  KEY `idx_theDate` (`mchId`,`theDate`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE,
  KEY `idx_cityCode` (`cityCode`) USING BTREE,
  KEY `idx_proCode` (`proCode`) USING BTREE,
  KEY `uidx_useIndex` (`mchId`,`theDate`,`userId`,`scanCount`,`rpAmount`,`transAmount`,`cardCount`,`pointAmount`,`pointUsed`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23348688 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_wusu_code_report`
--

DROP TABLE IF EXISTS `rpt_wusu_code_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_wusu_code_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `productId` int(11) DEFAULT NULL,
  `productName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `activityId` int(11) DEFAULT NULL,
  `activityName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `areaName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `strategyName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `strategyLevel` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `batchId` int(11) DEFAULT NULL,
  `batchNo` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `codeCount` int(11) DEFAULT NULL,
  `capsCount` int(11) DEFAULT NULL,
  `capsCount2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '瓶盖激活数，瓶盖采购数除以1.05',
  `pointsCount` int(11) DEFAULT NULL,
  `pointsCount2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '激活的积分数：pointsCount*capsCount2/capsCount',
  `pointsNum` int(11) DEFAULT NULL,
  `scanNum` int(11) DEFAULT NULL,
  `scanAreaCode` longtext COLLATE utf8mb4_bin,
  `theDate` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mabd_index` (`mchId`,`activityId`,`batchId`,`theDate`)
) ENGINE=InnoDB AUTO_INCREMENT=10966171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_wusu_scan_area`
--

DROP TABLE IF EXISTS `rpt_wusu_scan_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_wusu_scan_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batchId` int(11) NOT NULL,
  `areaCode` varchar(11) DEFAULT NULL,
  `scanNum` int(11) NOT NULL DEFAULT '0',
  `pointsNum` int(11) NOT NULL DEFAULT '0',
  `theDate` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rpt_code_index` (`batchId`,`theDate`,`areaCode`)
) ENGINE=InnoDB AUTO_INCREMENT=1497649 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rpt_wusu_score_report`
--

DROP TABLE IF EXISTS `rpt_wusu_score_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpt_wusu_score_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `productId` int(11) DEFAULT NULL,
  `productName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `activityId` int(11) DEFAULT NULL,
  `activityName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `areaName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `strategyName` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `strategyLevel` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `totalCaps` int(11) DEFAULT NULL,
  `totalCaps2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '瓶盖激活数，瓶盖采购数除以1.05',
  `totalPoints` int(11) DEFAULT NULL,
  `totalPoints2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '激活的积分数',
  `scanedCaps` int(11) DEFAULT NULL,
  `scanedPoints` int(11) DEFAULT NULL,
  `theDate` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mabd_index` (`mchId`,`activityId`,`theDate`)
) ENGINE=InnoDB AUTO_INCREMENT=335413 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesman`
--

DROP TABLE IF EXISTS `salesman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(45) NOT NULL DEFAULT '',
  `mchId` int(11) NOT NULL,
  `subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `nickName` varchar(45) DEFAULT NULL,
  `sex` bit(1) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `subscribe_time` int(11) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `qq` varchar(45) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `mchSalesmanId` int(10) unsigned DEFAULT NULL,
  `realName` varchar(32) DEFAULT NULL,
  `mobile` char(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`openid`),
  KEY `uidx_openId` (`openid`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `index_mchSalesmanId` (`mchSalesmanId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9377 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesman_statements`
--

DROP TABLE IF EXISTS `salesman_statements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesman_statements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smId` int(11) DEFAULT NULL COMMENT '业务员id',
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `statementNo` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `settleTime` int(11) DEFAULT NULL COMMENT '结算时间',
  `submitTime` int(11) unsigned NOT NULL,
  `state` int(11) DEFAULT NULL COMMENT '状态。0，提交；1，已提交',
  `settleCode` int(11) DEFAULT NULL COMMENT '结算码，由tts返回。0：成功；大于0，失败',
  `settleResult` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `rowStatus` int(11) DEFAULT NULL COMMENT '行状态。0，正常；1，删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_mchId_statementNo` (`mchId`,`statementNo`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesman_statements_objs`
--

DROP TABLE IF EXISTS `salesman_statements_objs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesman_statements_objs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objId` int(11) DEFAULT NULL COMMENT '对象id',
  `objType` int(11) DEFAULT NULL COMMENT '对象类型',
  `num` int(11) DEFAULT NULL COMMENT '数量',
  `statementsId` int(11) DEFAULT NULL,
  `errcode` int(11) DEFAULT NULL COMMENT '反馈的错误码',
  `errmsg` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `scanId` int(11) NOT NULL COMMENT '对应的扫码记录',
  PRIMARY KEY (`id`),
  KEY `idx_statementId` (`statementsId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scan_log`
--

DROP TABLE IF EXISTS `scan_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scan_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `ip` varchar(15) DEFAULT NULL,
  `areaCode` varchar(11) DEFAULT NULL,
  `gps` varchar(50) DEFAULT NULL,
  `isFirst` bit(1) DEFAULT b'0' COMMENT '是否是第一次扫描',
  `batchId` int(11) DEFAULT NULL,
  `activityId` int(11) DEFAULT NULL,
  `rewardTable` varchar(45) DEFAULT NULL,
  `rewardId` int(11) DEFAULT NULL,
  `scanTime` int(11) DEFAULT NULL,
  `over` tinyint(1) DEFAULT '0' COMMENT '扫码业务处理是否已结束',
  `openId` varchar(255) DEFAULT NULL,
  `lat` double(9,6) DEFAULT NULL COMMENT '原始纬度',
  `lng` double(9,6) DEFAULT NULL COMMENT '原始经度',
  `geoId` int(11) DEFAULT '-1' COMMENT '地理位置信息，不同于此表的lat和lng，存储的是100*100的范围',
  `geoLat` double(9,6) DEFAULT NULL,
  `geoLng` double(9,6) DEFAULT NULL,
  `latScale` int(11) DEFAULT NULL,
  `lngScale` int(11) DEFAULT NULL,
  `fromWLL` tinyint(1) DEFAULT NULL COMMENT '是否是从welinklink加入的',
  PRIMARY KEY (`id`,`code`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_batchId` (`batchId`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_userId` (`userId`) USING BTREE,
  KEY `idx_scanTime` (`scanTime`),
  KEY `idx_activityId` (`activityId`) USING BTREE,
  KEY `idx_over` (`over`) USING BTREE,
  KEY `idx_openId` (`openId`) USING BTREE,
  KEY `idx_geoId` (`geoId`) USING BTREE,
  KEY `idx_rewardTable` (`rewardTable`) USING BTREE,
  KEY `idx_rewardId` (`rewardId`) USING BTREE,
  KEY `uidx_init_scanCount` (`mchId`,`userId`,`batchId`,`geoId`,`scanTime`)
) ENGINE=InnoDB AUTO_INCREMENT=251264921 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT
/*!50500 PARTITION BY RANGE  COLUMNS(`code`)
(PARTITION p00 VALUES LESS THAN ('00') ENGINE = InnoDB,
 PARTITION p01 VALUES LESS THAN ('01') ENGINE = InnoDB,
 PARTITION p02 VALUES LESS THAN ('02') ENGINE = InnoDB,
 PARTITION p03 VALUES LESS THAN ('03') ENGINE = InnoDB,
 PARTITION p04 VALUES LESS THAN ('04') ENGINE = InnoDB,
 PARTITION p05 VALUES LESS THAN ('05') ENGINE = InnoDB,
 PARTITION p06 VALUES LESS THAN ('06') ENGINE = InnoDB,
 PARTITION p07 VALUES LESS THAN ('07') ENGINE = InnoDB,
 PARTITION p08 VALUES LESS THAN ('08') ENGINE = InnoDB,
 PARTITION p09 VALUES LESS THAN ('09') ENGINE = InnoDB,
 PARTITION p10 VALUES LESS THAN ('0A') ENGINE = InnoDB,
 PARTITION p11 VALUES LESS THAN ('0B') ENGINE = InnoDB,
 PARTITION p12 VALUES LESS THAN ('0C') ENGINE = InnoDB,
 PARTITION p13 VALUES LESS THAN ('0D') ENGINE = InnoDB,
 PARTITION p14 VALUES LESS THAN ('0E') ENGINE = InnoDB,
 PARTITION p15 VALUES LESS THAN ('0F') ENGINE = InnoDB,
 PARTITION p16 VALUES LESS THAN ('0G') ENGINE = InnoDB,
 PARTITION p17 VALUES LESS THAN ('0H') ENGINE = InnoDB,
 PARTITION p18 VALUES LESS THAN ('0I') ENGINE = InnoDB,
 PARTITION p19 VALUES LESS THAN ('0J') ENGINE = InnoDB,
 PARTITION p20 VALUES LESS THAN ('0K') ENGINE = InnoDB,
 PARTITION p21 VALUES LESS THAN ('0L') ENGINE = InnoDB,
 PARTITION p22 VALUES LESS THAN ('0M') ENGINE = InnoDB,
 PARTITION p23 VALUES LESS THAN ('0N') ENGINE = InnoDB,
 PARTITION p24 VALUES LESS THAN ('0O') ENGINE = InnoDB,
 PARTITION p25 VALUES LESS THAN ('0P') ENGINE = InnoDB,
 PARTITION p26 VALUES LESS THAN ('0Q') ENGINE = InnoDB,
 PARTITION p27 VALUES LESS THAN ('0R') ENGINE = InnoDB,
 PARTITION p28 VALUES LESS THAN ('0S') ENGINE = InnoDB,
 PARTITION p29 VALUES LESS THAN ('0T') ENGINE = InnoDB,
 PARTITION p30 VALUES LESS THAN ('0U') ENGINE = InnoDB,
 PARTITION p31 VALUES LESS THAN ('0V') ENGINE = InnoDB,
 PARTITION p32 VALUES LESS THAN ('0W') ENGINE = InnoDB,
 PARTITION p33 VALUES LESS THAN ('0X') ENGINE = InnoDB,
 PARTITION p34 VALUES LESS THAN ('0Y') ENGINE = InnoDB,
 PARTITION p35 VALUES LESS THAN ('0Z') ENGINE = InnoDB,
 PARTITION p36 VALUES LESS THAN ('0a') ENGINE = InnoDB,
 PARTITION p37 VALUES LESS THAN ('0b') ENGINE = InnoDB,
 PARTITION p38 VALUES LESS THAN ('0c') ENGINE = InnoDB,
 PARTITION p39 VALUES LESS THAN ('0d') ENGINE = InnoDB,
 PARTITION p40 VALUES LESS THAN ('0e') ENGINE = InnoDB,
 PARTITION p41 VALUES LESS THAN ('0f') ENGINE = InnoDB,
 PARTITION p42 VALUES LESS THAN ('0g') ENGINE = InnoDB,
 PARTITION p43 VALUES LESS THAN ('0h') ENGINE = InnoDB,
 PARTITION p44 VALUES LESS THAN ('0i') ENGINE = InnoDB,
 PARTITION p45 VALUES LESS THAN ('0j') ENGINE = InnoDB,
 PARTITION p46 VALUES LESS THAN ('0k') ENGINE = InnoDB,
 PARTITION p47 VALUES LESS THAN ('0l') ENGINE = InnoDB,
 PARTITION p48 VALUES LESS THAN ('0m') ENGINE = InnoDB,
 PARTITION p49 VALUES LESS THAN ('0n') ENGINE = InnoDB,
 PARTITION p50 VALUES LESS THAN ('0o') ENGINE = InnoDB,
 PARTITION p51 VALUES LESS THAN ('0p') ENGINE = InnoDB,
 PARTITION p52 VALUES LESS THAN ('0q') ENGINE = InnoDB,
 PARTITION p53 VALUES LESS THAN ('0r') ENGINE = InnoDB,
 PARTITION p54 VALUES LESS THAN ('0s') ENGINE = InnoDB,
 PARTITION p55 VALUES LESS THAN ('0t') ENGINE = InnoDB,
 PARTITION p56 VALUES LESS THAN ('0u') ENGINE = InnoDB,
 PARTITION p57 VALUES LESS THAN ('0v') ENGINE = InnoDB,
 PARTITION p58 VALUES LESS THAN ('0w') ENGINE = InnoDB,
 PARTITION p59 VALUES LESS THAN ('0x') ENGINE = InnoDB,
 PARTITION p60 VALUES LESS THAN ('0y') ENGINE = InnoDB,
 PARTITION p61 VALUES LESS THAN ('0z') ENGINE = InnoDB,
 PARTITION p62 VALUES LESS THAN (MAXVALUE) ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scan_log_waiters`
--

DROP TABLE IF EXISTS `scan_log_waiters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scan_log_waiters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `userId` int(10) unsigned DEFAULT NULL,
  `mchId` int(10) unsigned DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `areaCode` varchar(11) DEFAULT NULL,
  `gps` varchar(50) DEFAULT NULL,
  `lng` double(9,6) DEFAULT NULL COMMENT '原始经度',
  `lat` double(9,6) DEFAULT NULL COMMENT '原始纬度',
  `lngBaidu` float(9,6) DEFAULT NULL,
  `latBaidu` float(9,6) DEFAULT NULL,
  `batchId` int(11) DEFAULT NULL,
  `activityId` int(11) DEFAULT NULL,
  `rewardTable` varchar(45) DEFAULT NULL,
  `rewardId` int(11) DEFAULT NULL,
  `scanTime` int(11) DEFAULT NULL,
  `over` tinyint(1) DEFAULT '0' COMMENT '扫码业务处理是否已结束',
  `openId` varchar(255) DEFAULT NULL,
  `geoId` int(11) DEFAULT NULL COMMENT '地理位置信息，不同于此表的lat和lng，存储的是100*100的范围',
  `geoLat` double(9,6) DEFAULT NULL,
  `geoLng` double(9,6) DEFAULT NULL,
  `latScale` int(11) DEFAULT NULL,
  `lngScale` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `idx_lat_lng` (`lat`,`lng`) USING BTREE,
  KEY `idx_openId` (`openId`) USING BTREE,
  KEY `idx_geoId` (`geoId`) USING BTREE,
  KEY `idx_mchId_userId` (`mchId`,`userId`) USING BTREE,
  KEY `idx_batchId` (`batchId`) USING BTREE,
  KEY `idx_scanTime` (`scanTime`) USING BTREE,
  KEY `idx_geoLng_geoLat` (`geoLat`,`geoLng`) USING BTREE,
  KEY `idx_areaCode` (`areaCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_appeal`
--

DROP TABLE IF EXISTS `service_appeal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_appeal` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `phoneNum` varchar(20) DEFAULT NULL,
  `openId` varchar(45) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `refuse` varchar(255) DEFAULT NULL COMMENT '拒绝理由',
  `mark` varchar(255) DEFAULT NULL COMMENT '备注',
  `QRimg` varchar(255) DEFAULT NULL,
  `createTime` int(11) NOT NULL,
  `refuseTime` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 新增,1 已提交,2 成功,3 驳回, 4 拒绝结束',
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`openId`) USING BTREE,
  KEY `idx_status` (`status`),
  KEY `idx_createTime` (`createTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=78087 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `val` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_activity_exhibit`
--

DROP TABLE IF EXISTS `shop_activity_exhibit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_activity_exhibit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '活动名称',
  `content` varchar(255) DEFAULT NULL COMMENT '描述',
  `productId` int(10) unsigned DEFAULT '0' COMMENT '所属产品',
  `categoryId` int(11) DEFAULT NULL COMMENT '分类或者品牌',
  `mchId` int(11) unsigned DEFAULT NULL COMMENT '所属企业',
  `startTime` int(11) unsigned DEFAULT NULL COMMENT '开始时间',
  `endTime` int(11) unsigned DEFAULT NULL COMMENT '结束时间',
  `shopType` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '门店类型(json)',
  `putSection` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '商品摆放区域(json)',
  `putMode` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '商品摆放方式(json)',
  `putPosition` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '商品摆放位置(json)',
  `putNum` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '商品摆放数量(json)',
  `createTime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updateTime` int(11) unsigned DEFAULT NULL COMMENT '修改时间',
  `state` int(11) unsigned DEFAULT '0' COMMENT '活动状态。0，新建；1，启用；2，停用',
  `rowStatus` int(11) unsigned DEFAULT '0' COMMENT '行状态。0，正常；1，删除',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='门店商品陈列奖励设置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_activity_sku`
--

DROP TABLE IF EXISTS `shop_activity_sku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_activity_sku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '活动名称',
  `content` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '描述',
  `mchId` int(11) unsigned DEFAULT NULL COMMENT '所属企业',
  `productId` int(11) unsigned DEFAULT NULL COMMENT '产品ID',
  `categoryId` int(11) unsigned DEFAULT NULL COMMENT '分类或品牌ID',
  `areaCode` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '扫码区域行政区划码',
  `saleAreaCode` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '销售区域（出库单）',
  `startTime` int(11) unsigned DEFAULT NULL COMMENT '活动开始时间',
  `endTime` int(11) unsigned DEFAULT NULL COMMENT '活动结束时间',
  `binding` set('product','category','areacode','salecode') COLLATE utf8mb4_bin DEFAULT '',
  `signReward` double(10,2) unsigned DEFAULT NULL COMMENT '签到奖励',
  `isMotivate` tinyint(4) unsigned DEFAULT NULL COMMENT '是否有销售激励',
  `saleReward` double(10,2) DEFAULT NULL COMMENT '激励奖励（每瓶）',
  `state` int(11) unsigned DEFAULT '0' COMMENT '活动状态。0，新建；1，启用；2，停用',
  `createTime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updateTime` int(11) unsigned DEFAULT NULL COMMENT '修改时间',
  `rowStatus` int(11) unsigned DEFAULT '0' COMMENT '行状态。0，正常；1，删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='签到、销售奖励';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_cash`
--

DROP TABLE IF EXISTS `shop_cash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerId` int(11) DEFAULT NULL,
  `commonId` int(11) DEFAULT NULL COMMENT 'common用户的id',
  `money` double(10,2) DEFAULT NULL,
  `state` tinyint(4) DEFAULT NULL COMMENT '1：申请，2：执行中，3：提现成功，4：审核失败，5：提现失败',
  `createTime` int(11) DEFAULT NULL,
  `wxMchBillno` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `wxStatus` tinyint(2) DEFAULT NULL COMMENT '商户订单状态：0处理中 1成功 2失败 3微信处理中',
  `wxErrCode` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `wxSendTime` int(11) DEFAULT NULL,
  `wxFinalStatus` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `succTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='提现表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_devices`
--

DROP TABLE IF EXISTS `shop_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deviceId` int(11) DEFAULT NULL,
  `uuid` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `major` int(11) DEFAULT NULL,
  `minor` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `comment` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '描述',
  `shopId` int(11) unsigned DEFAULT NULL COMMENT '门店id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_exhibit`
--

DROP TABLE IF EXISTS `shop_exhibit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_exhibit` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `shopType` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '门店类型:0:不限定,1:便利店,2:小型超市,3:大型超市',
  `putSection` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品摆放区域:0:不限定,1:饮料,2:食品,3:杂货,4:前厅,5:活动厅,6:独立区域',
  `putMode` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品摆放方式:0:不限定,1:端架,2:堆箱,3:货架,4:冰箱,5:异形,6:吧台',
  `putPosition` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品摆放位置:0:不限定,1:上,2:中,3:下,4:前,5:后',
  `long` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '摆放数量：长',
  `wide` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '摆放数量：宽',
  `high` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '高',
  `money` double(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '奖励金额',
  `activityId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门店商品陈列奖励设置id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='陈列奖励详情';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_mch`
--

DROP TABLE IF EXISTS `shop_mch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_mch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shopId` int(11) unsigned DEFAULT NULL COMMENT '门店id',
  `mchId` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `state` tinyint(4) DEFAULT NULL COMMENT '1:申请，2：审核通过，3：失败',
  `certificate` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '营业执照或授权书图片(备用）',
  `shopType` tinyint(4) unsigned DEFAULT NULL COMMENT '店铺类型（便利店、小型超市、大型超市等）',
  `createTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updateTime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='门店企业关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_mch_tags`
--

DROP TABLE IF EXISTS `shop_mch_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_mch_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_message`
--

DROP TABLE IF EXISTS `shop_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '企业ID',
  `ownerId` int(11) DEFAULT NULL,
  `title` varchar(32) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标题',
  `type` tinyint(4) DEFAULT NULL COMMENT '1：系统，2：企业宣传，3：拍一拍消息，4：扫一扫奖励',
  `exhibitActId` int(11) DEFAULT '0' COMMENT '拍一拍活动Id',
  `signActId` int(11) DEFAULT '0' COMMENT '签到活动Id',
  `status` tinyint(4) DEFAULT NULL COMMENT '0：未读，1：已读',
  `content` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '消息内容',
  `createTime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3385 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='消息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_owner`
--

DROP TABLE IF EXISTS `shop_owner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_owner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(64) COLLATE utf8mb4_bin DEFAULT NULL,
  `unionid` varchar(64) COLLATE utf8mb4_bin DEFAULT NULL,
  `nickname` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '昵称',
  `realname` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '姓名',
  `avatar` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '头像',
  `mobile` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '手机号',
  `balance` double(10,2) unsigned DEFAULT '0.00' COMMENT '资产总额',
  `putReward` double(10,2) unsigned DEFAULT '0.00' COMMENT '拍一拍奖励总额(累加结果)',
  `signReward` double(10,2) unsigned DEFAULT '0.00' COMMENT '签到奖励总额',
  `saleReward` double(10,2) unsigned DEFAULT '0.00' COMMENT '销售激励奖励总额',
  `token` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '登录凭证',
  `expireTime` int(11) DEFAULT NULL,
  `createTime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_openid` (`openid`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='店主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_picture`
--

DROP TABLE IF EXISTS `shop_picture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskId` int(11) DEFAULT NULL COMMENT '任务id(作废）',
  `shopId` int(11) DEFAULT NULL COMMENT '门店ID',
  `ownerId` int(11) DEFAULT NULL,
  `formId` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '用于消息通知',
  `date` varchar(16) COLLATE utf8mb4_bin DEFAULT NULL,
  `imagePath` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '可能有多个，以分号分隔',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `reward` double(10,2) DEFAULT NULL COMMENT '奖励金额',
  `analyzeId` int(11) DEFAULT NULL COMMENT 'OPP识别用户ID',
  `state` tinyint(4) DEFAULT NULL COMMENT '1:上传成功，2：识别中，3：识别完成,4：申诉中,5：申诉失败',
  `reason` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '申诉理由',
  `dismiss` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '驳回理由',
  `analysisTime` int(11) DEFAULT NULL COMMENT '识别时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='拍一拍任务图片';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_picture_info`
--

DROP TABLE IF EXISTS `shop_picture_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_picture_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pictureId` int(11) DEFAULT NULL COMMENT '图片Id',
  `productId` int(11) DEFAULT NULL COMMENT '识别的产品ID',
  `taskId` int(11) DEFAULT NULL COMMENT '该识别关联的任务ID',
  `workerId` int(11) DEFAULT NULL COMMENT '识别人Id',
  `analysisTime` int(11) DEFAULT NULL COMMENT '识别时间',
  `shopType` tinyint(4) DEFAULT NULL COMMENT '门店类型(json)',
  `putSection` tinyint(4) DEFAULT NULL COMMENT '商品摆放区域(json)',
  `putMode` tinyint(4) DEFAULT NULL COMMENT '商品摆放方式(json)',
  `putPosition` tinyint(4) DEFAULT NULL COMMENT '商品摆放位置(json)',
  `putNum` varchar(32) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '商品摆放数量(json)',
  `long` tinyint(3) unsigned DEFAULT '0' COMMENT '摆放数量：长',
  `wide` tinyint(3) unsigned DEFAULT '0' COMMENT '摆放数量：宽',
  `high` tinyint(3) unsigned DEFAULT '0' COMMENT '高',
  `reward` double(10,2) DEFAULT NULL COMMENT '单个任务的奖励',
  `describe` varchar(255) COLLATE utf8mb4_bin DEFAULT '' COMMENT '奖励详情描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='图片识别信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_reward_record`
--

DROP TABLE IF EXISTS `shop_reward_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_reward_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerId` int(11) unsigned DEFAULT NULL COMMENT '店主id',
  `mchId` int(11) unsigned DEFAULT NULL COMMENT '企业Id',
  `shopId` int(11) unsigned DEFAULT NULL COMMENT '门店Id',
  `productId` int(11) unsigned DEFAULT NULL COMMENT '关联产品Id',
  `categoryId` int(11) unsigned DEFAULT NULL COMMENT '分类Id',
  `actType` tinyint(4) unsigned DEFAULT NULL COMMENT '1：拍一拍活动，2：扫一扫活动,3：非活动',
  `actId` int(11) unsigned DEFAULT NULL COMMENT '奖励对应的活动id，如果不是活动相关的为空',
  `taskId` int(11) DEFAULT NULL COMMENT '关联任务Id',
  `relationType` tinyint(4) unsigned DEFAULT NULL COMMENT '1:拍一拍奖励，2：签到奖励，3：销售激励，4：提现',
  `relationId` int(11) DEFAULT NULL COMMENT '关联主体id',
  `amount` double(10,2) DEFAULT NULL COMMENT '奖励金额',
  `state` tinyint(4) DEFAULT '0' COMMENT '0：未合计到用户总余额，1：已合计到用户总余额，2企业余额不足，待余额足后发放',
  `content` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '奖励或提现描述',
  `createTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=463 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='店主奖励，提现记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_sale_reward`
--

DROP TABLE IF EXISTS `shop_sale_reward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_sale_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scanId` int(11) DEFAULT NULL COMMENT '关联扫码表（scan_log)Id',
  `code` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '扫描乐码',
  `skuCode` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '关联箱码',
  `productId` int(11) DEFAULT NULL COMMENT '产品Id',
  `categoryId` int(11) DEFAULT NULL COMMENT '分类Id',
  `ownerId` int(10) DEFAULT NULL COMMENT '奖励店主',
  `activityId` int(11) DEFAULT NULL COMMENT '关联激励活动',
  `reward` double(10,2) DEFAULT NULL COMMENT '奖励金额',
  `createTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='销售激励表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_sku_scan`
--

DROP TABLE IF EXISTS `shop_sku_scan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_sku_scan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerId` int(11) DEFAULT NULL COMMENT '扫码店主',
  `skuCode` varchar(40) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '扫描箱码',
  `activityId` int(11) DEFAULT NULL COMMENT '关联签到活动',
  `productId` int(11) DEFAULT NULL COMMENT '产品id',
  `categoryId` int(11) DEFAULT NULL COMMENT '分类Id',
  `areaCode` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '扫码行政区划代码',
  `reward` double(10,2) DEFAULT NULL COMMENT '奖励金额',
  `piece` int(11) unsigned DEFAULT '0' COMMENT '分组查询的标示(以10分钟内扫码数量为一组)',
  `createTime` int(11) DEFAULT NULL COMMENT '扫码时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='箱码扫描签到奖励表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_tag`
--

DROP TABLE IF EXISTS `shop_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagId` int(11) DEFAULT NULL,
  `shopId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_task`
--

DROP TABLE IF EXISTS `shop_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '企业id',
  `activityId` int(11) DEFAULT NULL COMMENT '拍一拍活动ID',
  `shopId` int(11) DEFAULT NULL COMMENT '门店ID',
  `ownerId` int(11) DEFAULT NULL COMMENT '店主Id',
  `date` varchar(16) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '任务日期',
  `title` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '任务标题',
  `state` tinyint(4) DEFAULT NULL COMMENT '1:可做，2：已做，3：过期，4：获得奖励，5：申诉，6：结束',
  `createTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `expireTime` int(11) DEFAULT NULL COMMENT '超时时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2515 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_users_common`
--

DROP TABLE IF EXISTS `shop_users_common`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_users_common` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commonId` int(11) DEFAULT NULL COMMENT 'common user用户id',
  `unionid` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '如关联了开放平台，存储微信unionid',
  `status` tinyint(4) DEFAULT NULL COMMENT '备用字段（状态）',
  `extInt` int(11) DEFAULT NULL COMMENT '备用整形字段',
  `extString` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '备用字符串字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_commonid` (`commonId`),
  KEY `idx_unionid` (`unionid`)
) ENGINE=InnoDB AUTO_INCREMENT=29007515 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `address` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `areaCode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `lat` double(9,6) DEFAULT NULL,
  `lng` double(9,6) DEFAULT NULL,
  `ownerName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ownerPhoneNum` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '1gps 2蓝牙',
  `areaLen` int(11) DEFAULT NULL,
  `openid` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `ownerId` int(11) DEFAULT NULL COMMENT '店主Id',
  `shopType` tinyint(4) DEFAULT '1' COMMENT '店铺类型（1便利店、2小型超市、3大型超市等）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ownerid` (`ownerId`)
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strategy_log`
--

DROP TABLE IF EXISTS `strategy_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `strategy_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '数据类型：0，红包；1，欢乐币；2，乐券；3组合策略 4活动 5积分策略 6叠加策略 7累计策略',
  `opration` varchar(45) DEFAULT NULL COMMENT '操作：create update delete start stop 等等',
  `data` varchar(2000) DEFAULT NULL COMMENT 'json串格式数据',
  `theTime` int(11) DEFAULT NULL COMMENT '快照时间',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`) USING BTREE,
  KEY `idx_opration` (`opration`) USING BTREE,
  KEY `idx_theTime` (`theTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=121161 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_activities`
--

DROP TABLE IF EXISTS `sub_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `content` varchar(500) DEFAULT NULL,
  `binding` int(11) DEFAULT NULL COMMENT '二进制标志绑定信息，第1位表示绑定时间；第2位表示绑定区域；第3位表示绑定批次；第4位绑定生产批次；第5位绑定出库单；第6位绑定销售区域;第7位绑定过期时间；第8位绑定产品；',
  `startTime` int(11) DEFAULT NULL,
  `endTime` int(11) DEFAULT NULL,
  `role` tinyint(4) DEFAULT NULL COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  `webAppId` int(11) DEFAULT NULL COMMENT '对应的h5的webApp',
  `activityType` int(11) DEFAULT NULL COMMENT '活动类型。0，红包；1，欢乐币；2，卡券；3组合策略；4积分策略；5叠加策略；6累计策略',
  `detailId` int(11) DEFAULT NULL COMMENT '对应的具体活动内容表id',
  `areaCode` varchar(10) DEFAULT NULL,
  `batchId` int(11) DEFAULT NULL,
  `prodInOrderId` varchar(100) DEFAULT NULL COMMENT '生产入库单ID',
  `outOrderId` varchar(100) DEFAULT NULL COMMENT '出库单号ID',
  `saletoagc` varchar(10) DEFAULT NULL COMMENT '销售区域的行政区域编码',
  `expireOprt` varchar(2) DEFAULT NULL COMMENT '过期时间计算式。''=''：等于；''<=''：小于等于；''<''：小于',
  `expireTime` int(11) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `parentId` int(11) DEFAULT NULL,
  `geoNeeded` tinyint(4) DEFAULT NULL COMMENT '允许获取地理位置才能匹配活动：0否 1是',
  `subscribeNeeded` tinyint(4) DEFAULT '1' COMMENT '是否需要订阅后才能发放',
  `productId` int(11) DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `tagId` varchar(100) DEFAULT NULL COMMENT '用户标签ID',
  `state` int(11) DEFAULT '0' COMMENT '0，新建；1，启用；2，停用',
  `rowStatus` int(11) DEFAULT '0' COMMENT '0，正常；1，删除',
  `forEvil` set('1','2','3','4') DEFAULT NULL COMMENT '是否针对羊毛党的活动',
  PRIMARY KEY (`id`),
  KEY `idx_parentId` (`parentId`),
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_binding` (`binding`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13619 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tencent_cloud`
--

DROP TABLE IF EXISTS `tencent_cloud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tencent_cloud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '腾讯云APPID',
  `secretId` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT '腾讯云秘钥ID',
  `secretKey` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT '腾讯云秘钥Key',
  `mchId` int(11) NOT NULL COMMENT 'app对应的商户Id。如果为-1，则对所有商户启用',
  `isUse` tinyint(4) DEFAULT NULL COMMENT '是否启用',
  `validLevel` int(11) DEFAULT NULL COMMENT '可参与活动用户级别',
  `ignoreLevel` int(11) DEFAULT NULL COMMENT '禁止参加活动级别',
  `expireTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tts_apps`
--

DROP TABLE IF EXISTS `tts_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tts_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `appSecret` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `token` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `expireTime` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL COMMENT 'app对应的商户Id。如果为-1，则对所有商户启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tts_orders`
--

DROP TABLE IF EXISTS `tts_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tts_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `orderNo` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '单号',
  `orderType` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT '单据类型。produce:生产入库；in：普通入库；out：普通出库；',
  `factoryCode` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT '工厂编号',
  `factoryName` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT '工厂名称',
  `produceTime` int(11) DEFAULT NULL COMMENT '生产时间',
  `shelfLifeStr` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT '保质期的字符串格式。1d|1m|1w|1y。d为天，m为月，w为周，y为年',
  `expireTime` int(11) DEFAULT NULL COMMENT '过期时间',
  `productCode` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '产品编码',
  `productName` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '产品名称',
  `saletoCode` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '销往企业编码',
  `saletoName` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT '销往企业名称',
  `saletoAGC` varchar(45) COLLATE utf8_bin NOT NULL COMMENT '销往经销商',
  `specialCode` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT '特征码',
  `orderTime` int(11) DEFAULT NULL COMMENT '单据时间',
  `putTime` int(11) DEFAULT NULL COMMENT '上传时间',
  `errmsg` longtext COLLATE utf8_bin,
  `processStatus` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7569 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tts_orders_codes`
--

DROP TABLE IF EXISTS `tts_orders_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tts_orders_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '码',
  `orderId` int(11) DEFAULT NULL COMMENT '单据id',
  `pubCode` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `isScan` tinyint(1) NOT NULL DEFAULT '0',
  `skuCode` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_code_orderId` (`code`,`orderId`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_orderId` (`orderId`) USING BTREE,
  KEY `idx_pub_code` (`pubCode`),
  KEY `idx_sku_code` (`skuCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=75954923 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tts_orders_codes_copy`
--

DROP TABLE IF EXISTS `tts_orders_codes_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tts_orders_codes_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '码',
  `orderId` int(11) DEFAULT NULL COMMENT '单据id',
  `pubCode` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `isScan` tinyint(1) NOT NULL DEFAULT '0',
  `skuCode` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_code_orderId` (`code`,`orderId`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_orderId` (`orderId`) USING BTREE,
  KEY `idx_pub_code` (`pubCode`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_accounts`
--

DROP TABLE IF EXISTS `user_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) NOT NULL COMMENT '商户id',
  `amount` int(11) DEFAULT NULL COMMENT '最小单位分',
  `notes` varchar(200) DEFAULT NULL,
  `moneyType` int(11) DEFAULT NULL COMMENT '红包类型，0：普通红包，1：裂变红包',
  `role` int(11) DEFAULT NULL COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_account` (`moneyType`,`userId`,`mchId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=72352557 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_accounts_used`
--

DROP TABLE IF EXISTS `user_accounts_used`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_accounts_used` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `doTable` varchar(50) DEFAULT NULL COMMENT '消费记录表：groups_scanpk_users（扫码PK表），更多待补充',
  `doId` int(11) DEFAULT NULL COMMENT 'doTable对应的表名里的具体ID',
  `amount` int(11) DEFAULT NULL COMMENT '额度',
  `role` int(11) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  `createTime` int(11) DEFAULT NULL COMMENT '消费时间',
  PRIMARY KEY (`id`),
  KEY `idx_userId_mchId_getTime` (`userId`,`mchId`,`createTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4115 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_cards`
--

DROP TABLE IF EXISTS `user_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `role` int(11) DEFAULT '0' COMMENT '角色。0，普通用户；1，服务员；2，业务员',
  `cardId` int(11) DEFAULT NULL COMMENT '卡片id',
  `scanId` int(11) DEFAULT NULL COMMENT '相关的扫码记录',
  `instId` int(11) DEFAULT NULL COMMENT 'app应用实例ID',
  `sended` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否发放：0未发放，1已发放',
  `getTime` int(11) DEFAULT NULL,
  `processing` int(11) unsigned zerofill DEFAULT NULL COMMENT '处理状态',
  `status` int(11) DEFAULT NULL COMMENT '状态。0，正常；1，已转移；2，已结算',
  `transId` int(11) DEFAULT '-1' COMMENT '转移记录的id',
  `code` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cardId` (`cardId`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `uidx_cardId_code` (`cardId`,`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=729741 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_cards_account`
--

DROP TABLE IF EXISTS `user_cards_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_cards_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `role` tinyint(1) DEFAULT NULL COMMENT '角色。0，普通用户；1，服务员；2，业务员',
  `cardId` int(11) DEFAULT NULL COMMENT '卡片id',
  `num` int(11) DEFAULT NULL COMMENT '卡片数量',
  `mchId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_userid_role_mchid_cardid` (`userId`,`role`,`cardId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=666689 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_points`
--

DROP TABLE IF EXISTS `user_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `pointsId` int(11) DEFAULT NULL COMMENT '对应的红包策略id',
  `amount` int(11) DEFAULT NULL COMMENT '积分额度',
  `getTime` int(11) DEFAULT NULL COMMENT '积分获得时间',
  `scanId` int(11) DEFAULT NULL COMMENT '扫码ID',
  `sended` tinyint(4) DEFAULT NULL COMMENT '是否已发放 0未发放 1已发放',
  `role` int(11) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  `instId` int(11) DEFAULT NULL COMMENT '应用实例ID',
  `code` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_userId_mchId_getTime` (`userId`,`mchId`,`getTime`) USING BTREE,
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_userId` (`userId`),
  KEY `uidx_mchId_role_amount` (`mchId`,`role`,`amount`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=78478407 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_points_accounts`
--

DROP TABLE IF EXISTS `user_points_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_points_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL COMMENT '用户id',
  `mchId` int(11) DEFAULT NULL COMMENT '商户id',
  `amount` int(11) DEFAULT NULL COMMENT '积分余额',
  `role` int(11) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_userId_mchId_role` (`userId`,`mchId`,`role`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=75774123 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_points_get`
--

DROP TABLE IF EXISTS `user_points_get`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_points_get` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL COMMENT '数额',
  `doTable` varchar(50) DEFAULT NULL COMMENT '对应来源表',
  `doId` int(11) DEFAULT NULL COMMENT '对应来源表数据',
  `getTime` int(11) DEFAULT NULL,
  `role` tinyint(1) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  PRIMARY KEY (`id`),
  KEY `idx_userId_mchId_getTime` (`userId`,`mchId`,`getTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=339 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_points_used`
--

DROP TABLE IF EXISTS `user_points_used`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_points_used` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `doTable` varchar(50) DEFAULT NULL COMMENT '消费记录表：mall_orders（积分商城订单表），更多待补充',
  `doId` int(11) DEFAULT NULL COMMENT 'doTable对应的表名里的具体ID',
  `amount` int(11) DEFAULT NULL COMMENT '积分额度',
  `role` int(11) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  `createTime` int(11) DEFAULT NULL COMMENT '积分获得时间',
  PRIMARY KEY (`id`),
  KEY `idx_userId_mchId_getTime` (`userId`,`mchId`,`createTime`) USING BTREE,
  KEY `idx_userId` (`userId`),
  KEY `idx_role` (`role`),
  KEY `uidx_mchId_doTable_role` (`mchId`,`doTable`,`role`,`amount`)
) ENGINE=InnoDB AUTO_INCREMENT=10884441 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_redpackets`
--

DROP TABLE IF EXISTS `user_redpackets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_redpackets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `rpId` int(11) DEFAULT NULL COMMENT '对应的红包策略id',
  `amount` int(11) DEFAULT NULL COMMENT '最小单位分',
  `getTime` int(11) DEFAULT NULL,
  `scanId` int(11) NOT NULL,
  `sended` tinyint(4) DEFAULT NULL COMMENT '是否已发放',
  `role` int(11) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  `instId` int(11) DEFAULT NULL COMMENT '应用实例ID',
  `code` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`,`scanId`),
  KEY `idx_mchId_userId` (`mchId`,`userId`),
  KEY `idx_userId` (`userId`),
  KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_role` (`role`) USING BTREE,
  KEY `uidx_mchId_userId_scanId_amount_sended_role` (`mchId`,`userId`,`scanId`,`amount`,`sended`,`role`) USING BTREE,
  KEY `idx_rpId` (`rpId`) USING BTREE,
  KEY `idx_sended` (`sended`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=130721989 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_redpackets_get`
--

DROP TABLE IF EXISTS `user_redpackets_get`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_redpackets_get` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL COMMENT '数额',
  `doTable` varchar(50) DEFAULT NULL COMMENT '对应来源表',
  `doId` int(11) DEFAULT NULL COMMENT '对应来源表数据',
  `getTime` int(11) DEFAULT NULL,
  `role` tinyint(1) DEFAULT '0' COMMENT '角色id。0：普通用户；1：服务员；2：业务员',
  PRIMARY KEY (`id`),
  KEY `idx_userId_mchId_getTime` (`userId`,`mchId`,`getTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11901 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_template_msg`
--

DROP TABLE IF EXISTS `user_template_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_template_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `openid` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `formatMsg` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '模板消息内容',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态：0处理中 1已发送 2发送失败',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_openid` (`openid`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_createTime` (`createTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8701939 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_trans`
--

DROP TABLE IF EXISTS `user_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `role` int(11) DEFAULT '0' COMMENT '角色。0，普通用户；1，服务员；2，业务员',
  `amount` int(11) DEFAULT NULL COMMENT '最小单位分',
  `theTime` int(11) DEFAULT NULL,
  `mchId` int(11) NOT NULL COMMENT '商户id',
  `isAuto` tinyint(2) DEFAULT '0' COMMENT '是否系统自动提现发放：0否 1是',
  `moneyType` tinyint(2) DEFAULT '0' COMMENT '红包类型：0普通红包 1裂变红包',
  `payType` tinyint(2) DEFAULT NULL COMMENT '支付方式：0微信红包 1企业付款',
  `wxMchBillno` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '红包商户订单号',
  `wxSendListId` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '红包订单的微信单号',
  `wxSendTime` int(11) DEFAULT NULL COMMENT '微信接口成功发放时间',
  `wxStatus` tinyint(2) DEFAULT '0' COMMENT '商户订单状态：0处理中 1成功 2失败 3微信处理中',
  `wxErrCode` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '微信接口返回错误码',
  `wxFinalStatus` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '微信最终状态',
  `action` tinyint(2) NOT NULL DEFAULT '0' COMMENT '提现方式：0 普通提现，1 积分提现',
  `payAccountType` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_userId` (`userId`) USING BTREE,
  KEY `idx_role` (`role`) USING BTREE,
  KEY `idx_amount` (`amount`) USING BTREE,
  KEY `idx_theTime` (`theTime`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_isAuto` (`isAuto`) USING BTREE,
  KEY `idx_moneyType` (`moneyType`) USING BTREE,
  KEY `idx_payType` (`payType`) USING BTREE,
  KEY `idx_wxMchBillno` (`wxMchBillno`) USING BTREE,
  KEY `idx_wxStatus` (`wxStatus`) USING BTREE,
  KEY `idx_action` (`action`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=84614333 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_transfers`
--

DROP TABLE IF EXISTS `user_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromId` int(11) DEFAULT NULL COMMENT '所有人id',
  `fromRole` int(11) DEFAULT NULL COMMENT '所有人角色',
  `toId` int(11) DEFAULT NULL,
  `toRole` int(11) DEFAULT NULL,
  `objId` int(11) NOT NULL COMMENT '对象id，即happy_coins,cards,red_packtes等表的id',
  `objType` int(11) DEFAULT NULL COMMENT '对象类型。0:红包；1：欢乐币；2：卡片',
  `num` int(10) unsigned DEFAULT NULL COMMENT '数量，如果转移的是数量型物品，如“再来一瓶”等，则填写此值',
  `transferTime` int(11) DEFAULT NULL COMMENT '获取时间',
  PRIMARY KEY (`id`,`objId`),
  KEY `idx_from` (`fromId`,`fromRole`,`toId`,`toRole`,`transferTime`,`objId`),
  KEY `idx_to` (`toId`,`toRole`,`fromId`,`fromRole`,`transferTime`,`objId`)
) ENGINE=InnoDB AUTO_INCREMENT=1192 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_update`
--

DROP TABLE IF EXISTS `user_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxYsId` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '公众号原始ID',
  `openid` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '用户openid',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态：0待更新 1已更新 2更新失败',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uidx_wxYsId_openid_status` (`wxYsId`,`openid`,`status`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10182441 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(45) NOT NULL,
  `mchId` int(11) DEFAULT NULL,
  `subscribe` tinyint(1) DEFAULT NULL,
  `nickName` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `subscribe_time` int(11) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL COMMENT 'app采集的手机号',
  `email` varchar(45) DEFAULT NULL,
  `qq` varchar(45) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL COMMENT 'app采集的生日',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `fromHLS` bit(1) DEFAULT NULL COMMENT '是否是从欢乐扫关注公众号的',
  `language` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `groupid` int(11) DEFAULT NULL,
  `tagid_list` varchar(500) DEFAULT NULL COMMENT '标签列表',
  `areaCode` int(11) DEFAULT NULL COMMENT 'app采集的区域编码',
  `realName` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `address` varchar(200) DEFAULT NULL COMMENT '收货地址',
  `fromWLL` tinyint(1) DEFAULT NULL COMMENT '是否是从welinklink加入的',
  PRIMARY KEY (`id`,`openid`),
  UNIQUE KEY `uidx_openId` (`openid`) USING BTREE,
  KEY `uidx_createTime` (`createTime`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_mobile` (`mchId`,`mobile`) USING BTREE,
  KEY `uidx_mchId_createTime` (`mchId`,`createTime`)
) ENGINE=InnoDB AUTO_INCREMENT=53998203 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_common`
--

DROP TABLE IF EXISTS `users_common`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_common` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(45) NOT NULL,
  `mchId` int(11) DEFAULT '-1',
  `subscribe` tinyint(1) DEFAULT '0' COMMENT '是否关注：0是 1否',
  `nickName` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `subscribe_time` int(11) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL COMMENT 'app采集的手机号',
  `email` varchar(45) DEFAULT NULL,
  `qq` varchar(45) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL COMMENT 'app采集的生日',
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `fromHLS` bit(1) DEFAULT NULL COMMENT '是否是从欢乐扫关注公众号的',
  `fromWLL` tinyint(1) DEFAULT NULL COMMENT '是否是从welinklink加入的',
  `language` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `groupid` int(11) DEFAULT NULL,
  `tagid_list` varchar(500) DEFAULT NULL COMMENT '标签列表',
  `areaCode` int(11) DEFAULT NULL COMMENT 'app采集的区域编码',
  `realName` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `address` varchar(200) DEFAULT NULL COMMENT '收货地址',
  `commonStatus` tinyint(2) DEFAULT '0' COMMENT '全局状态：0正常 1封禁',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_openId` (`openid`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `uidx_createTime` (`createTime`) USING BTREE,
  KEY `idx_subscribe` (`subscribe`) USING BTREE,
  KEY `idx_commonStatus` (`commonStatus`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48881079 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_common_blacklist`
--

DROP TABLE IF EXISTS `users_common_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_common_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `openid` varchar(45) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `createTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_userId_openid` (`userId`,`openid`) USING BTREE,
  KEY `idx_openid` (`openid`) USING BTREE,
  KEY `idx_userId` (`userId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17796 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_common_log`
--

DROP TABLE IF EXISTS `users_common_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_common_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL COMMENT 'commonUserId',
  `logType` tinyint(4) unsigned zerofill DEFAULT NULL COMMENT '事件类型：1非法操作封号 2码已过期 3活动已停止 4扫码过于频繁 5正常扫码 6扫了别人的码 7再次扫自己的码',
  `logDesc` varchar(500) DEFAULT NULL,
  `lecode` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '操作对应乐码',
  `logIp` varchar(15) DEFAULT NULL,
  `logUrl` varchar(500) DEFAULT NULL,
  `referer` varchar(500) DEFAULT NULL,
  `agent` varchar(500) DEFAULT NULL COMMENT '客户端身份信息',
  `createTime` int(11) DEFAULT NULL,
  `mchId` int(11) DEFAULT NULL,
  `mchUserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logType` (`logType`) USING BTREE,
  KEY `idx_userId` (`userId`) USING BTREE,
  KEY `idx_createTime` (`createTime`) USING BTREE,
  KEY `idx_lecode` (`lecode`) USING BTREE,
  KEY `idx_logIp` (`logIp`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_mchUserId` (`mchUserId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=390718829 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_common_sub`
--

DROP TABLE IF EXISTS `users_common_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_common_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `openid` varchar(45) NOT NULL,
  `mchId` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态：0正常 1封禁',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_parentId_openid` (`parentId`,`openid`) USING BTREE,
  KEY `uidx_parentId_userId_mchId` (`parentId`,`userId`,`mchId`) USING BTREE,
  KEY `idx_openid` (`openid`) USING BTREE,
  KEY `idx_userId` (`userId`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=269953503 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_common_whitelist`
--

DROP TABLE IF EXISTS `users_common_whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_common_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `openid` varchar(45) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_userId_openid` (`userId`,`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_tags`
--

DROP TABLE IF EXISTS `users_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `tagId` int(11) DEFAULT NULL COMMENT '标签id',
  `name` varchar(90) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标签名称',
  `count` int(11) DEFAULT NULL COMMENT '此标签下粉丝数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_mchId_tagId` (`mchId`,`tagId`) USING BTREE,
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_tagId` (`tagId`) USING BTREE,
  KEY `idx_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=599 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_tags_update`
--

DROP TABLE IF EXISTS `users_tags_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_tags_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) DEFAULT NULL,
  `tagId` int(11) DEFAULT NULL COMMENT '标签id',
  `openid` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '用户id',
  `createTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updateTime` int(11) DEFAULT NULL COMMENT '更新时间',
  `status` int(11) DEFAULT '0' COMMENT '处理状态：0待处理 1处理成功 2处理失败',
  PRIMARY KEY (`id`),
  KEY `idx_mchId` (`mchId`) USING BTREE,
  KEY `idx_tagId` (`tagId`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_openid` (`openid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2576735 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `variables`
--

DROP TABLE IF EXISTS `variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL COMMENT '日利率',
  `val` varchar(200) DEFAULT NULL COMMENT '日服务费率',
  `theTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22453 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `waiters`
--

DROP TABLE IF EXISTS `waiters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `waiters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(45) NOT NULL,
  `mchId` int(11) DEFAULT NULL,
  `subscribe` bit(1) DEFAULT NULL,
  `nickName` varchar(45) DEFAULT NULL,
  `sex` bit(1) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `subscribe_time` int(11) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `qq` varchar(45) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `updateTime` int(11) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `groupid` int(11) DEFAULT NULL,
  `realName` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(45) DEFAULT NULL,
  `idCardNo` varchar(20) DEFAULT NULL COMMENT '身份证号',
  `shopName` varchar(200) DEFAULT NULL COMMENT '门店名称',
  `lastGPS` varchar(50) DEFAULT NULL COMMENT '上报信息时的gps',
  `areaCode` int(11) DEFAULT NULL COMMENT 'app采集的区域编码',
  PRIMARY KEY (`id`,`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=9513 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warning_accounts`
--

DROP TABLE IF EXISTS `warning_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warning_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `mchId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warning_log`
--

DROP TABLE IF EXISTS `warning_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warning_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mchId` int(11) NOT NULL,
  `type` int(2) NOT NULL,
  `createTime` int(11) NOT NULL,
  `desc` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=122353 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webApps`
--

DROP TABLE IF EXISTS `webApps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webApps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appName` varchar(255) DEFAULT NULL,
  `appPath` varchar(255) DEFAULT NULL COMMENT '所在文件路径，用相对路径表示，如：redpack/',
  `mchId` int(11) NOT NULL,
  `config` int(11) DEFAULT '0' COMMENT '需要配置：0不需要 1需要',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webapp_config`
--

DROP TABLE IF EXISTS `webapp_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webapp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webappId` int(11) DEFAULT NULL,
  `mchId` int(11) NOT NULL,
  `data` varchar(500) DEFAULT '0' COMMENT '配置数据json串',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-09 15:11:37
