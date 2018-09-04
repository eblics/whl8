-- 瓶盖激活数
CREATE TEMPORARY TABLE if not exists caps_points_unique (  
  mchId int NOT NULL, 
  activityId int NOT NULL,  
  detailId int NOT NULL,
  points int NOT NULL,
  caps int NOT NULL
);

CREATE TEMPORARY TABLE if not exists caps_points (  
  mchId int NOT NULL, 
  activityId int NOT NULL,  
  detailId int NOT NULL,
  points int NOT NULL,
  caps int NOT NULL,
  UNIQUE KEY `mad_index` (`mchId`,`activityId`,`detailId`)
);

delete from caps_points;
delete from caps_points_unique;

set @mchId = 173;
set @initDate = date(date_add(now(), interval -1 day));
-- set @initDate = date(now());

insert into caps_points_unique (mchId, activityId, detailId, points, caps) 
select t5.mchId, t5.id activityId, t4.id detailId, t3.amount * (t1.weight) points, t1.weight caps
from sub_activities t5
join mix_strategies t4 on t4.id = t5.detailId
join mix_strategies_sub t1 on t1.parentId = t4.id
join points t2 on t1.strategyId = t2.id and t1.strategyType = 3
join points_sub t3 on t3.parentId = t2.id
where t5.activityType = 3 and t4.mchId = @mchId group by t5.id, t1.id;

insert into caps_points 
select * from caps_points_unique
on duplicate key update caps_points.points = caps_points.points + caps_points_unique.points, caps_points.caps = caps_points.caps + caps_points_unique.caps;

-- 二维码瓶盖
delete from rpt_wusu_code_report where theDate = @initDate and mchId = @mchId;
REPLACE into rpt_wusu_code_report (mchId, productId, productName, activityId, activityName, areaName, batchId, batchNo, codeCount, capsCount, pointsCount, theDate)
select t1.mchId, ifnull(t1.productId, -1), ifnull(t2.name, '未关联'), t1.id, t1.name, ifnull(t5.name, '全国'), t1.batchId, t3.batchNo, (t3.end - t3.start + 1), t6.caps, t6.points, @initDate 
from sub_activities t1 
left join products t2 on t2.id = t1.productId
join batchs t3 on t3.id = t1.batchId
join caps_points t6 on t6.detailId = t1.detailId and t1.activityType = 3
left join areas t5 on t5.code = t1.areaCode
where t1.mchId = @mchId and t1.rowStatus = 0;

insert into rpt_wusu_code_report (mchId, activityId, batchId, theDate, strategyName, strategyLevel)
select t1.mchId, t1.activityId, t1.batchId, t1.theDate, ifnull(t4.name, '已删除'), '组合策略'
from rpt_wusu_code_report t1
join sub_activities t2 on t2.id = t1.activityId and t2.activityType = 3 
left join mix_strategies t4 on t4.id = t2.detailId
on duplicate key update rpt_wusu_code_report.strategyName = ifnull(t4.name, '已删除'),
rpt_wusu_code_report.strategyLevel = '组合策略';

-- 积分核对
delete from rpt_wusu_score_report where theDate = @initDate and mchId = @mchId;
REPLACE into rpt_wusu_score_report (mchId, productId, productName, activityId, activityName, areaName, totalCaps, totalPoints, theDate)
select t1.mchId, ifnull(t1.productId, -1), ifnull(t2.name, '未关联'), t1.id, t1.name, ifnull(t5.name, '全国'), t6.caps, t6.points, @initDate
from sub_activities t1 
left join products t2 on t2.id = t1.productId
join caps_points t6 on t6.activityId = t1.id and t1.activityType = 3
left join areas t5 on t5.code = t1.areaCode
where t1.mchId = @mchId and t1.rowStatus = 0;

insert into rpt_wusu_score_report (mchId, activityId, theDate, strategyName, strategyLevel, scanedCaps, scanedPoints)
select t1.mchId, t1.activityId, t1.theDate, ifnull(t4.name, '已删除'), '组合策略', 0, 0
from rpt_wusu_score_report t1
join sub_activities t2 on t2.id = t1.activityId and t2.activityType = 3 -- 累计
left join mix_strategies t4 on t4.id = t2.detailId
on duplicate key update rpt_wusu_score_report.strategyName = ifnull(t4.name, '已删除'),
rpt_wusu_score_report.strategyLevel = '组合策略';


-- 以扫瓶盖数
update rpt_wusu_code_report set scanNum = 0 where theDate = @initDate and mchId = @mchId;
insert into rpt_wusu_code_report (mchId, activityId, batchId, theDate, scanNum)
select t1.mchId, t1.activityId, t1.batchId, date(from_unixtime(t1.scanTime)), 1
from scan_log t1 
join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
join batchs t3 on t3.id = t1.batchId
where t1.activityId is not null and t1.mchId = @mchId and scanTime >= unix_timestamp(concat(@initDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(@initDate, ' 23:59:59'))
on duplicate key update rpt_wusu_code_report.scanNum = rpt_wusu_code_report.scanNum + 1;


-- 已扫积分数量
update rpt_wusu_code_report set pointsNum = 0 where theDate = @initDate and mchId = @mchId;
insert into rpt_wusu_code_report (mchId, activityId, batchId, theDate, pointsNum)
select t1.mchId, t1.activityId, t1.batchId, date(from_unixtime(t1.scanTime)), t4.amount
from scan_log t1 
join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
join batchs t3 on t3.id = t1.batchId
join user_points t4 on t4.id = t1.rewardId
where t1.activityId is not null and t1.mchId = @mchId and t1.rewardTable = 'user_points' and scanTime >= unix_timestamp(concat(@initDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(@initDate, ' 23:59:59'))
on duplicate key update rpt_wusu_code_report.pointsNum = rpt_wusu_code_report.pointsNum + t4.amount;


delete from rpt_wusu_code_report where productId is null;
update rpt_wusu_code_report t1 join caps_points t2 on t2.activityId = t1.activityId set t1.capsCount = t2.caps, t1.pointsCount = t2.points;

-- 扫码地区
update rpt_wusu_scan_area set scanNum = 0, pointsNum = 0 where theDate = @initDate;
insert into rpt_wusu_scan_area (batchId, areaCode, theDate, scanNum, pointsNum)
select t2.batchId, ifnull(t1.areaCode, 0) areaCode, t2.theDate, 1 scanNum, ifnull(t3.amount, 0) pointsNum from scan_log t1
join rpt_wusu_code_report t2 on t2.batchId = t1.batchId and t2.theDate = @initDate
left join user_points t3 on t3.id = t1.rewardId
where t1.mchId = @mchId and t1.scanTime >= unix_timestamp(concat(@initDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(@initDate, ' 23:59:59')) and t1.rewardTable = 'user_points'
on duplicate key update rpt_wusu_scan_area.scanNum = rpt_wusu_scan_area.scanNum + 1, rpt_wusu_scan_area.pointsNum = rpt_wusu_scan_area.pointsNum + ifnull(t3.amount, 0);



 -- 以扫瓶盖数
update rpt_wusu_score_report set scanedCaps = 0 where theDate = @initDate and mchId = @mchId;
insert into rpt_wusu_score_report (mchId, activityId, theDate, scanedCaps)
select t1.mchId, t1.activityId, date(from_unixtime(t1.scanTime)), 1
from scan_log t1 
join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
where t1.activityId is not null and t1.mchId = @mchId and scanTime >= unix_timestamp(concat(@initDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(@initDate, ' 23:59:59'))
on duplicate key update rpt_wusu_score_report.scanedCaps = rpt_wusu_score_report.scanedCaps + 1;


-- 已扫积分数量
update rpt_wusu_score_report set scanedPoints = 0 where theDate = @initDate and mchId = @mchId;
insert into rpt_wusu_score_report (mchId, activityId, theDate, scanedPoints)
select t1.mchId, t1.activityId, date(from_unixtime(t1.scanTime)), t4.amount
from scan_log t1 
join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
join user_points t4 on t4.id = t1.rewardId
where t1.activityId is not null and t1.mchId = @mchId and t1.rewardTable = 'user_points' and scanTime >= unix_timestamp(concat(@initDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(@initDate, ' 23:59:59'))
on duplicate key update rpt_wusu_score_report.scanedPoints = rpt_wusu_score_report.scanedPoints + t4.amount;

delete from rpt_wusu_score_report where productId is null;
update rpt_wusu_score_report t1 join caps_points t2 on t2.activityId = t1.activityId set t1.totalCaps = t2.caps, t1.totalPoints = t2.points;


-- 定义存储过程
DROP PROCEDURE IF EXISTS `procedure_wusu_charts`;
DELIMITER ;;
CREATE PROCEDURE `procedure_wusu_charts`(IN mDate date, IN mchId int)
BEGIN
  IF mDate IS NULL THEN SET mDate = curdate();
  END IF;

  IF mchId IS NULL THEN SET mchId = 173;
  END IF;

  CREATE TEMPORARY TABLE if not exists caps_points_unique (  
    mchId int NOT NULL, 
    activityId int NOT NULL,  
    detailId int NOT NULL,
    points int NOT NULL,
    caps int NOT NULL
  );

  CREATE TEMPORARY TABLE if not exists caps_points (  
    mchId int NOT NULL, 
    activityId int NOT NULL,  
    detailId int NOT NULL,
    points int NOT NULL,
    caps int NOT NULL,
    UNIQUE KEY `mad_index` (`mchId`,`activityId`,`detailId`)
  );

  delete from caps_points;
  delete from caps_points_unique;

  insert into caps_points_unique (mchId, activityId, detailId, points, caps) 
  select t5.mchId, t5.id activityId, t4.id detailId, t3.amount * (t1.weight) points, t1.weight caps
  from sub_activities t5
  join mix_strategies t4 on t4.id = t5.detailId
  join mix_strategies_sub t1 on t1.parentId = t4.id
  join points t2 on t1.strategyId = t2.id and t1.strategyType = 3
  join points_sub t3 on t3.parentId = t2.id
  where t5.activityType = 3 and t4.mchId = mchId group by t5.id, t1.id;

  insert into caps_points 
  select * from caps_points_unique
  on duplicate key update caps_points.points = caps_points.points + caps_points_unique.points, caps_points.caps = caps_points.caps + caps_points_unique.caps;

  -- 二维码瓶盖
  delete from rpt_wusu_code_report where theDate = mDate and mchId = mchId;
  REPLACE into rpt_wusu_code_report (mchId, productId, productName, activityId, activityName, areaName, batchId, batchNo, codeCount, capsCount, pointsCount, theDate)
  select t1.mchId, ifnull(t1.productId, -1), ifnull(t2.name, '未关联'), t1.id, t1.name, ifnull(t5.name, '全国'), t1.batchId, t3.batchNo, (t3.end - t3.start + 1), t6.caps, t6.points, mDate 
  from sub_activities t1 
  left join products t2 on t2.id = t1.productId
  join batchs t3 on t3.id = t1.batchId
  join caps_points t6 on t6.detailId = t1.detailId and t1.activityType = 3
  left join areas t5 on t5.code = t1.areaCode
  where t1.mchId = mchId and t1.rowStatus = 0;

  insert into rpt_wusu_code_report (mchId, activityId, batchId, theDate, strategyName, strategyLevel)
  select t1.mchId, t1.activityId, t1.batchId, t1.theDate, ifnull(t4.name, '已删除'), '组合策略'
  from rpt_wusu_code_report t1
  join sub_activities t2 on t2.id = t1.activityId and t2.activityType = 3 
  left join mix_strategies t4 on t4.id = t2.detailId
  on duplicate key update rpt_wusu_code_report.strategyName = ifnull(t4.name, '已删除'),
  rpt_wusu_code_report.strategyLevel = '组合策略';

  -- 积分核对
  delete from rpt_wusu_score_report where theDate = mDate and mchId = mchId;
  REPLACE into rpt_wusu_score_report (mchId, productId, productName, activityId, activityName, areaName, totalCaps, totalPoints, theDate)
  select t1.mchId, ifnull(t1.productId, -1), ifnull(t2.name, '未关联'), t1.id, t1.name, ifnull(t5.name, '全国'), t6.caps, t6.points, mDate
  from sub_activities t1 
  left join products t2 on t2.id = t1.productId
  join caps_points t6 on t6.activityId = t1.id and t1.activityType = 3
  left join areas t5 on t5.code = t1.areaCode
  where t1.mchId = mchId and t1.rowStatus = 0;

  insert into rpt_wusu_score_report (mchId, activityId, theDate, strategyName, strategyLevel, scanedCaps, scanedPoints)
  select t1.mchId, t1.activityId, t1.theDate, ifnull(t4.name, '已删除'), '组合策略', 0, 0
  from rpt_wusu_score_report t1
  join sub_activities t2 on t2.id = t1.activityId and t2.activityType = 3 -- 累计
  left join mix_strategies t4 on t4.id = t2.detailId
  on duplicate key update rpt_wusu_score_report.strategyName = ifnull(t4.name, '已删除'),
  rpt_wusu_score_report.strategyLevel = '组合策略';


  -- 以扫瓶盖数
  update rpt_wusu_code_report set scanNum = 0 where theDate = mDate and mchId = mchId;
  insert into rpt_wusu_code_report (mchId, activityId, batchId, theDate, scanNum)
  select t1.mchId, t1.activityId, t1.batchId, date(from_unixtime(t1.scanTime)), 1
  from scan_log t1 
  join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
  join batchs t3 on t3.id = t1.batchId
  where t1.activityId is not null and t1.mchId = mchId and scanTime >= unix_timestamp(concat(mDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(mDate, ' 23:59:59'))
  on duplicate key update rpt_wusu_code_report.scanNum = rpt_wusu_code_report.scanNum + 1;


  -- 已扫积分数量
  update rpt_wusu_code_report set pointsNum = 0 where theDate = mDate and mchId = mchId;
  insert into rpt_wusu_code_report (mchId, activityId, batchId, theDate, pointsNum)
  select t1.mchId, t1.activityId, t1.batchId, date(from_unixtime(t1.scanTime)), t4.amount
  from scan_log t1 
  join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
  join batchs t3 on t3.id = t1.batchId
  join user_points t4 on t4.id = t1.rewardId
  where t1.activityId is not null and t1.mchId = mchId and t1.rewardTable = 'user_points' and scanTime >= unix_timestamp(concat(mDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(mDate, ' 23:59:59'))
  on duplicate key update rpt_wusu_code_report.pointsNum = rpt_wusu_code_report.pointsNum + t4.amount;


  delete from rpt_wusu_code_report where productId is null;
  update rpt_wusu_code_report t1 join caps_points t2 on t2.activityId = t1.activityId set t1.capsCount = t2.caps, t1.pointsCount = t2.points;

  -- 扫码地区
  update rpt_wusu_scan_area set scanNum = 0, pointsNum = 0 where theDate = mDate;
  insert into rpt_wusu_scan_area (batchId, areaCode, theDate, scanNum, pointsNum)
  select t2.batchId, ifnull(t1.areaCode, 0) areaCode, t2.theDate, 1 scanNum, ifnull(t3.amount, 0) pointsNum from scan_log t1
  join rpt_wusu_code_report t2 on t2.batchId = t1.batchId and t2.theDate = mDate
  left join user_points t3 on t3.id = t1.rewardId
  where t1.mchId = mchId and t1.scanTime >= unix_timestamp(concat(mDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(mDate, ' 23:59:59')) and t1.rewardTable = 'user_points'
  on duplicate key update rpt_wusu_scan_area.scanNum = rpt_wusu_scan_area.scanNum + 1, rpt_wusu_scan_area.pointsNum = rpt_wusu_scan_area.pointsNum + ifnull(t3.amount, 0);


  -- 以扫瓶盖数
  update rpt_wusu_score_report set scanedCaps = 0 where theDate = mDate and mchId = mchId;
  insert into rpt_wusu_score_report (mchId, activityId, theDate, scanedCaps)
  select t1.mchId, t1.activityId, date(from_unixtime(t1.scanTime)), 1
  from scan_log t1 
  join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
  where t1.activityId is not null and t1.mchId = mchId and scanTime >= unix_timestamp(concat(mDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(mDate, ' 23:59:59'))
  on duplicate key update rpt_wusu_score_report.scanedCaps = rpt_wusu_score_report.scanedCaps + 1;


  -- 已扫积分数量
  update rpt_wusu_score_report set scanedPoints = 0 where theDate = mDate and mchId = mchId;
  insert into rpt_wusu_score_report (mchId, activityId, theDate, scanedPoints)
  select t1.mchId, t1.activityId, date(from_unixtime(t1.scanTime)), t4.amount
  from scan_log t1 
  join sub_activities t2 on t1.activityId = t2.id and t2.rowStatus = 0 and t2.activityType = 3
  join user_points t4 on t4.id = t1.rewardId
  where t1.activityId is not null and t1.mchId = mchId and t1.rewardTable = 'user_points' and scanTime >= unix_timestamp(concat(mDate, ' 00:00:00')) and scanTime <= unix_timestamp(concat(mDate, ' 23:59:59'))
  on duplicate key update rpt_wusu_score_report.scanedPoints = rpt_wusu_score_report.scanedPoints + t4.amount;

  delete from rpt_wusu_score_report where productId is null;
  update rpt_wusu_score_report t1 join caps_points t2 on t2.activityId = t1.activityId set t1.totalCaps = t2.caps, t1.totalPoints = t2.points;
END
;;
DELIMITER ;

call procedure_wusu_charts('2017-01-01', 173);


DROP PROCEDURE IF EXISTS `procedure_wusu_charts_task`;
DELIMITER ;;
CREATE PROCEDURE `procedure_wusu_charts_task`()
BEGIN
  call procedure_wusu_charts('2017-08-30', 173);
  call procedure_wusu_charts('2017-08-29', 173);
  call procedure_wusu_charts('2017-08-28', 173);
  call procedure_wusu_charts('2017-08-27', 173);
  call procedure_wusu_charts('2017-08-26', 173);
  call procedure_wusu_charts('2017-08-25', 173);
  call procedure_wusu_charts('2017-08-24', 173);
  call procedure_wusu_charts('2017-08-23', 173);
  call procedure_wusu_charts('2017-08-22', 173);
  call procedure_wusu_charts('2017-08-21', 173);
  call procedure_wusu_charts('2017-08-20', 173);
  call procedure_wusu_charts('2017-08-19', 173);
  call procedure_wusu_charts('2017-08-18', 173);
  call procedure_wusu_charts('2017-08-17', 173);
  call procedure_wusu_charts('2017-08-16', 173);
  call procedure_wusu_charts('2017-08-15', 173);
  call procedure_wusu_charts('2017-08-14', 173);
  call procedure_wusu_charts('2017-08-13', 173);
  call procedure_wusu_charts('2017-08-12', 173);
  call procedure_wusu_charts('2017-08-11', 173);
  call procedure_wusu_charts('2017-08-10', 173);
  call procedure_wusu_charts('2017-08-09', 173);
  call procedure_wusu_charts('2017-08-08', 173);
  call procedure_wusu_charts('2017-08-07', 173);
  call procedure_wusu_charts('2017-08-06', 173);
  call procedure_wusu_charts('2017-08-05', 173);
  call procedure_wusu_charts('2017-08-04', 173);
  call procedure_wusu_charts('2017-08-03', 173);
  call procedure_wusu_charts('2017-08-02', 173);
  call procedure_wusu_charts('2017-08-01', 173);
END
;;
DELIMITER ;