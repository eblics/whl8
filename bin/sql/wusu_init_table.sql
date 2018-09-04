DROP TABLE IF EXISTS `rpt_wusu_code_report`;
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
  `pointsCount` int(11) DEFAULT NULL,
  `pointsNum` int(11) DEFAULT NULL,
  `scanNum` int(11) DEFAULT NULL,
  `scanAreaCode` longtext COLLATE utf8mb4_bin,
  `theDate` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mabd_index` (`mchId`,`activityId`,`batchId`,`theDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

DROP TABLE IF EXISTS `rpt_wusu_score_report`;
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
  `totalPoints` int(11) DEFAULT NULL,
  `scanedCaps` int(11) DEFAULT NULL,
  `scanedPoints` int(11) DEFAULT NULL,
  `theDate` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mabd_index` (`mchId`,`activityId`,`theDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

DROP TABLE IF EXISTS `rpt_wusu_scan_area`;
CREATE TABLE `rpt_wusu_scan_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batchId` int(11) NOT NULL,
  `areaCode` varchar(11) DEFAULT NULL,
  `scanNum` int(11) NOT NULL DEFAULT 0,
  `pointsNum` int (11) NOT NULL DEFAULT 0,
  `theDate` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rpt_code_index` (`batchId`,`theDate`,`areaCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
