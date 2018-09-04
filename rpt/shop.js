var hlsUtil = require('./hls_util');
var iconvLite = require('iconv-lite');
var co = require('co');
var util = require('util');
var router = require('express').Router();
var bodyParser = require('body-parser');

router.use(bodyParser.urlencoded({extended: true}));
router.use(bodyParser.json());

var cooker, session;

// 模块构造函数
var shopClass = function(params) {
	cooker = params.cooker;
	session = params.session;
	return router;
};

router.use(require('./cookie'));

/**
 * 获取区域码
 * @auther fengyanjun
 * @dateTime 2018-05-11 10:50
 * @param {string}  pro 省
 * @param {string} city 市
 * @param {string} area 区
 * @param {string} alias 表别名
 * @returns {string}
 */
var getArea =function(pro,city,area,alias) {
    if(area!='0'){
        return ' and '+(area?alias+'.':'')+'areaCode = "'+area+'"';
    }
    if(city!='0' && area=='0'){
        return ' and SUBSTRING('+(area?alias+'.':'')+'areaCode,1,4) = "'+city.substring(0,4)+'"';
    }
    if(pro!='0' && city=='0' && area=='0'){
        return ' and SUBSTRING('+(area?alias+'.':'')+'areaCode,1,2) = "'+pro.substring(0,2)+'"';
    }
    return '';
};

/**
 * 单店明细->进货数据
 * @auther fengyanjun
 * @dateTime 2018-05-08 15:06
 */
router.post('/purchase_data', function(req, res) {
    var mchId = req.info.mchId;
    var params = req.body;

    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if (typeof params.shopId !== 'undefined' && params.shopId != '0') {
        whereClause += util.format(" and r1.shopId='%s' ", params.shopId);
    }
    if(typeof params.startTime !== 'undefined' && typeof params.endTime !== 'undefined' && params.startTime != '' && params.endTime != ''){
        whereClause += util.format(" and r1.signDate>='%s' and r1.signDate<='%s' ",params.startTime,params.endTime);
    }
    whereClause+=getArea(params.pro,params.city,params.area,'r2');
    var sql_cnt=`SELECT COUNT(r1.id) AS cnt FROM rpt_shop_purchase r1 LEFT JOIN shops r2 ON r2.id=r1.shopId LEFT JOIN products r3 ON r3.id=r1.productId  WHERE  ${whereClause}`;

    var sql=`SELECT r1.*,r2.name,r3.name AS productName FROM rpt_shop_purchase r1 LEFT JOIN shops r2 ON r2.id=r1.shopId LEFT JOIN products r3 ON r3.id=r1.productId WHERE  ${whereClause} limit ${req.body.start} , ${req.body.length}`;

    co(function*(){
        var data=yield cooker.serve(sql);
        var cnt=yield cooker.serve(sql_cnt);

        var jsonData = {
            draw: req.body.draw,
            recordsTotal: cnt[0].cnt,
            recordsFiltered: cnt[0].cnt,
            data: data
        };
        res.json(jsonData);
    });
});

/**
 * 区域汇总->库存量
 * @auther fengyanjun
 * @dateTime 2018-05-08 18:00
 */
router.post('/stock_data', function(req, res) {
    var mchId = req.info.mchId;
    var params = req.body;

    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if(typeof params.startTime !== 'undefined' && params.startTime != '' ){
        whereClause += util.format(" and r1.theDate='%s' ",params.startTime);
    }
    whereClause+=getArea(params.pro,params.city,params.area,'r2');
    var sql_cnt=`SELECT COUNT(r1.id) AS cnt FROM rpt_shop_stock r1 LEFT JOIN shops r2 ON r2.id=r1.shopId   where  ${whereClause}`;

    var sql=`SELECT r1.monthSale,r1.purchaseInterval,r1.stock,r1.theDate,r2.name,r2.address,r2.ownerPhoneNum,r2.shopType FROM rpt_shop_stock r1 LEFT JOIN shops r2 ON r2.id=r1.shopId  where  ${whereClause} limit ${req.body.start} , ${req.body.length}`;
    consoleSqlLog('区域分布-库存数据',sql);
    co(function*(){
        var data=yield cooker.serve(sql);
        var cnt=yield cooker.serve(sql_cnt);

        var jsonData = {
            draw: req.body.draw,
            recordsTotal: cnt[0].cnt,
            recordsFiltered: cnt[0].cnt,
            data: data
        };
        res.json(jsonData);
    });
});

/**
 * 单店明细->参加活动数据
 * @auther fengyanjun
 * @dateTime 2018-05-09 14:14
 */
router.post('/exhibit_data', function(req, res) {
    var mchId = req.info.mchId;
    var params = req.body;

    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if (typeof params.shopId !== 'undefined' && params.shopId != '0') {
        whereClause += util.format(" and r1.shopId='%s' ", params.shopId);
    }
    if(typeof params.startTime !== 'undefined' && typeof params.endTime !== 'undefined' && params.startTime != '' && params.endTime != ''){
        whereClause += util.format(" and r1.theDate>='%s' and r1.theDate<='%s' ",params.startTime,params.endTime);
    }
    whereClause+=getArea(params.pro,params.city,params.area,'r2');
    var sql_cnt=`SELECT COUNT(r1.id) AS cnt FROM rpt_shop_exhibit r1 
    LEFT JOIN shops r2 ON r2.id=r1.shopId 
    LEFT JOIN shop_activity_exhibit r3 ON r3.id=r1.exhibitId  
    LEFT JOIN categories r4 ON r4.id=r3.categoryId  
    LEFT JOIN products r5 ON r5.id=r3.productId  
    WHERE  ${whereClause}`;

    var sql=`SELECT r1.theDate,r1.reward,r2.name AS shopName,r3.name AS activityName,
    r4.name AS cateName,r5.name AS productName FROM rpt_shop_exhibit r1 
    LEFT JOIN shops r2 ON r2.id=r1.shopId 
    LEFT JOIN shop_activity_exhibit r3 ON r3.id=r1.exhibitId  
    LEFT JOIN categories r4 ON r4.id=r3.categoryId  
    LEFT JOIN products r5 ON r5.id=r3.productId  
    WHERE  ${whereClause} limit ${req.body.start} , ${req.body.length}`;

    co(function*(){
        var data=yield cooker.serve(sql);
        var cnt=yield cooker.serve(sql_cnt);

        var jsonData = {
            draw: req.body.draw,
            recordsTotal: cnt[0].cnt,
            recordsFiltered: cnt[0].cnt,
            data: data
        };
        res.json(jsonData);
    });
});


/**
 * 活动汇总
 * @auther fengyanjun
 * @dateTime 2018-05-10 09:43
 */
router.post('/activity_data', function(req, res) {
    var mchId = req.info.mchId;
    var params = req.body;

    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if (typeof params.shopId !== 'undefined' && params.shopId != '0') {
        whereClause += util.format(" and r1.shopId='%s' ", params.shopId);
    }
    if (typeof params.exhibitId !== 'undefined' && params.exhibitId != '0') {
        whereClause += util.format(" and r1.exhibitId='%s' ", params.exhibitId);
    }
    whereClause+=getArea(params.pro,params.city,params.area,'r3');
    var sql_cnt=`select COUNT(r1.id) AS cnt from rpt_shop_exhibit_sum as r1 right join
    (select mchId,exhibitId,shopId,theDate, max(theDate) as max_theDate from rpt_shop_exhibit_sum group by shopId,exhibitId) as r2 
    on r1.theDate= r2.max_theDate and  r1.exhibitId = r2.exhibitId AND r1.shopId=r2.shopId
    LEFT JOIN shops r3 ON r3.id=r1.shopId 
    LEFT JOIN shop_activity_exhibit r4 ON r4.id=r1.exhibitId
    WHERE  ${whereClause}`;

    var sql=`select r1.takeDays,r1.intervalDays,r1.sumFee,r1.saleCount,r1.feeRate,r3.name AS shopName,r4.name AS activityName,
    r3.address,r3.ownerPhoneNum,r3.shopType from rpt_shop_exhibit_sum as r1 right join
    (select mchId,exhibitId,shopId,theDate, max(theDate) as max_theDate from rpt_shop_exhibit_sum group by shopId,exhibitId) as r2 
    on r1.theDate= r2.max_theDate and  r1.exhibitId = r2.exhibitId AND r1.shopId=r2.shopId
    LEFT JOIN shops r3 ON r3.id=r1.shopId 
    LEFT JOIN shop_activity_exhibit r4 ON r4.id=r1.exhibitId 
    WHERE  ${whereClause} limit ${req.body.start} , ${req.body.length} `;

    co(function*(){
        var data=yield cooker.serve(sql);
        var cnt=yield cooker.serve(sql_cnt);

        var jsonData = {
            draw: req.body.draw,
            recordsTotal: cnt[0].cnt,
            recordsFiltered: cnt[0].cnt,
            data: data
        };
        res.json(jsonData);
    });
});
//控制台打印日志
var consoleSqlLog=function(title,sql){
     console.log('========================'+title+'开始==========');
     console.log(sql);
     console.log('========================'+title+'结束==========');
}
//区域分布-省份地图数据 /update by zht at 2018-05-13
router.post('/get_pro_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body;
    console.log(param);

    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if(typeof param.startTime !== 'undefined' && param.startTime != '' ){
        whereClause += util.format(" and r1.theDate='%s' ",param.startTime);
    }
    if (typeof param.productId !== 'undefined' && param.productId != '' ) {
        whereClause += util.format("and r1.productId='%s'  ", param.productId);
    }
    var sql=`select a.code,a.name,ifnull(sum(b.monthSale),0) value from areas a 
            left join (select r1.monthSale,CONCAT( IFNULL(substring(r2.areaCode,1,2),'00'),'0000') proCode 
				from rpt_shop_stock as r1 
				LEFT JOIN shops as r2 on r1.shopId = r2.id 
				WHERE ${whereClause}) b on a.code=b.proCode where a.level='0' group by a.code;`;

    consoleSqlLog('门店区域分布-省份地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        res.send(result);
    });
});
// 区域分布-城市地图数据 /update by cw at 2017-04-20
router.post('/get_city_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body;
    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if(typeof param.proCode !== 'undefined' && param.proCode != '' ){
    	whereClause += util.format(" and r2.areaCode>%s and r2.areaCode<%s ",param.proCode,param.proCode.substring(0, 2)+'9999');
    }else{
    	res.send('proCode is null').end();
        return;
    }
    if(typeof param.startTime !== 'undefined' && param.startTime != '' ){
        whereClause += util.format(" and r1.theDate='%s' ",param.startTime);
    }
    if (typeof param.productId !== 'undefined' && param.productId != '' ) {
        whereClause += util.format("and r1.productId='%s'  ", param.productId);
    }
    var zxs = ["110000", "310000", "120000", "500000"];
    //非直辖市的情况
    var cityCode = "IFNULL(CONCAT( substring(r2.areaCode,1,4),'00'),'000000')";
    var zxsParentCode = param.proCode;
    //如果是直辖市的情况
    if(zxs.indexOf(param.proCode)!=-1){
    	cityCode = " IFNULL(r2.areaCode ,'000000')";
        zxsParentCode = param.proCode.substring(0, 2)+'0100';
    }

    var sql=`select b.cityCode,a.name,ifnull(sum(b.monthSale),0) value from areas a 
            inner join (select r1.monthSale,r1.shopId,${cityCode} cityCode 
				from rpt_shop_stock as r1 
				LEFT JOIN shops as r2 on r1.shopId = r2.id 
				WHERE ${whereClause}) b on a.code=b.cityCode where a.level = 1 group by b.cityCode;`;

    var areaSql=`select name,0 value from areas where parentCode=${zxsParentCode}`;


    consoleSqlLog('门店区域分布-城市地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        var city = yield cooker.serve(areaSql);
        res.send({data:result,city:city});
    });
});
// 区域分布-区域地图数据 /update by cw at 2017-04-20
router.post('/get_area_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body;

    var whereClause=util.format(" r1.mchId=%s  ",mchId);

    if(typeof param.cityCode !== 'undefined' && param.cityCode != '' ){
    	whereClause +=util.format(" and r2.areaCode > %s and r2.areaCode < %s ",param.cityCode,param.cityCode.substring(0, 4)+'99');
    }else{
    	res.send('cityCode is null').end();
        return;

    }
    if(typeof param.startTime !== 'undefined' && param.startTime != '' ){
        whereClause += util.format(" and r1.theDate='%s' ",param.startTime);
    }
    if (typeof param.productId !== 'undefined' && param.productId != '' ) {
        whereClause += util.format("and r1.productId='%s'  ", param.productId);
    }
    var zxs = ["110000", "310000", "120000", "500000"];
    //非直辖市的情况
    var zxsParentCode = param.cityCode;
    //如果是直辖市的情况
    if(zxs.indexOf(param.cityCode)!=-1){
        zxsParentCode = param.cityCode.substring(0, 2)+'0100';
    }

    var sql=`select b.areaCode,a.name,ifnull(sum(b.monthSale),0) value from areas a 
            inner join (select r1.monthSale,r1.shopId,IFNULL(r2.areaCode ,'000000') areaCode
				from rpt_shop_stock as r1 
				LEFT JOIN shops as r2 on r1.shopId = r2.id 
				WHERE ${whereClause}) b on a.code=b.areaCode  where a.level = 2 group by b.areaCode;`;
    
    var areaSql=`select name,0 value from areas where parentCode=${zxsParentCode}`;


    
    consoleSqlLog('区域分布-区域地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        var city = yield cooker.serve(areaSql);
        res.send({data:result,city:city});
    });
});
//区域分布-省份地图数据 /update by zht at 2018-05-13
router.post('/get_pro_table_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body;
    console.log(param);

    var whereClause=util.format(" r1.mchId=%s  ",mchId);
    if(typeof param.startTime !== 'undefined' && param.startTime != '' ){
        whereClause += util.format(" and r1.theDate='%s' ",param.startTime);
    }
    if (typeof param.productId !== 'undefined' && param.productId != '' ) {
        whereClause += util.format("and r1.productId='%s'  ", param.productId);
    }
    var sql=`select a.code,a.name,ifnull(sum(b.monthSale),0) value from areas a 
            inner join (select r1.monthSale,CONCAT( IFNULL(substring(r2.areaCode,1,2),'00'),'0000') proCode 
				from rpt_shop_stock as r1 
				LEFT JOIN shops as r2 on r1.shopId = r2.id 
				WHERE ${whereClause}) b on a.code=b.proCode where a.level='0' group by a.code;`;

    consoleSqlLog('门店区域分布-省份地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        res.send({data:result});
    });
});
module.exports=shopClass;