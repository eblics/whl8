var hlsUtil = require('./hls_util');
var Iconv  = require('iconv').Iconv;
var iconvLite = require('iconv-lite');
var co = require('co');
var util = require('util');
var router = require('express').Router();
var bodyParser = require('body-parser');

//router.use(bodyParser.urlencoded({extended: false}));
router.use(bodyParser.urlencoded({extended: true}));
router.use(bodyParser.json());
//router.use(cookieParser);

var cooker, session;

// 模块构造函数
var chartsClass = function(params) {
	cooker = params.cooker;
	session = params.session;
	return router;
};

router.use(require('./cookie'));
// HTTP Request handler
/************************用户扫码统计开始**********************************/
// 用户扫码统计-用户扫码记录 /update by cw at 2017-04-28
router.post('/get_userscan_data', function(req, res, next) {
    var mchId = req.info.mchId;
    var param = req.body.param;
    if(typeof param.level!=='undefined' && param.level==='week'){
        param.startTime=hlsUtil.getWeekNumberOfMonth(param.startTime);
        param.endTime=hlsUtil.getWeekNumberOfMonth(param.endTime);
    }

    var whereClause=util.format("mchId = %s AND theDate >= '%s' AND theDate <= '%s' ",mchId,param.startTime,param.endTime);

    if(param.pro!=0){
        whereClause += util.format("and proCode='%s'  ", param.pro);
    }
    if(param.city!=0){
        whereClause += util.format("and cityCode='%s'  ", param.city);
    }
    if(param.area!=0){
        whereClause += util.format("and areaCode='%s'  ", param.area);
    }
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param.productid);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param.batchid);
    }

    var sql=`SELECT theDate, SUM(scanCount) scanNum FROM %s WHERE ${whereClause} GROUP BY theDate;`;
    
    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(typeof param.level!=='undefined' && param.level==='week'){
            sql=util.format(sql, 'rpt_waiter_weekly');
        }else{
            sql=util.format(sql,'rpt_waiter_daily');
        }
    }else{
        if(typeof param.level!=='undefined' && param.level==='week'){
            sql=util.format(sql, 'rpt_user_weekly');
        }else{
            sql=util.format(sql,'rpt_user_daily');
        }
    }
    
    cooker.serve(sql).then(function(data) {
        var jsonData = {theDate: [], scanNum: []};
        if (param.month == 0) {

        } else if (param.week == 0) {
            if (typeof param.level !== 'undefined' && param.level === 'week') {
                    data.forEach(function(item) {
                        jsonData.theDate.push('第'+item.theDate+'周');
                        jsonData.scanNum.push(item.scanNum);
                    });
            } else {
                data.forEach(function(item) {
                    jsonData.theDate.push(item.theDate);
                    jsonData.scanNum.push(item.scanNum);
                });
            }
        } else {
            jsonData = {theDate: [], scanNum: []};
            data.forEach(function(item) {
                jsonData.theDate.push(item.theDate);
                jsonData.scanNum.push(item.scanNum);
            });
        }
        res.send(jsonData);
    });
});
///用户扫码表格数据 start /update by cw at 2017-04-28
router.post('/get_userscan_data_table', function(req, res) {
    var mchId = req.info.mchId;
    var param = req.body.param;

    if(typeof param.level!=='undefined' && param.level==='week'){
        param.startTime=hlsUtil.getWeekNumberOfMonth(param.startTime);
        param.endTime=hlsUtil.getWeekNumberOfMonth(param.endTime);
    }

    var whereClause=util.format("mchId=%s and theDate>='%s' and theDate<='%s' ",mchId,param.startTime,param.endTime);

    if(param.pro!=0){
        whereClause += util.format("and proCode='%s'  ", param.pro);
    }
    if(param.city!=0){
        whereClause += util.format("and cityCode='%s'  ", param.city);
    }
    if(param.area!=0){
        whereClause += util.format("and areaCode='%s'  ", param.area);
    }
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param.productid);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param.batchid);
    }

    var sql_cnt=`select count(id) cnt from(select id from %s where ${whereClause} group by theDate,userId)m`;

    var sql=`select ifnull(if(u.nickName='',null,u.nickName),'欢乐扫用户') nickName,m.* from (
        select userId,theDate,
        sum(scanCount) scanNum,
        round(sum(rpAmount)/100,2) redNum,
        round(sum(transAmount)/100,2) transNum,
        sum(cardCount) cardNum,
        sum(pointAmount) pointAmount,
        sum(pointUsed) pointUsed,
        '%s' level
        from %s 
        where ${whereClause} 
        group by theDate,userId ORDER BY theDate DESC, userId DESC limit %d,%d)m left join users u on m.userId=u.id ;`;
    
    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(typeof param.level!=='undefined' && param.level==='week'){
            sql_cnt=util.format(sql_cnt, 'rpt_waiter_weekly');
            sql=util.format(sql, 'week','rpt_waiter_weekly',req.body.start,req.body.length);
        }else{
            sql_cnt=util.format(sql_cnt, 'rpt_waiter_daily');
            sql=util.format(sql,'day', 'rpt_waiter_daily',req.body.start,req.body.length);
        }
    }else{
        if(typeof param.level!=='undefined' && param.level==='week'){
            sql_cnt=util.format(sql_cnt, 'rpt_user_weekly');
            sql=util.format(sql, 'week','rpt_user_weekly',req.body.start,req.body.length);
        }else{
            sql_cnt=util.format(sql_cnt, 'rpt_user_daily');
            sql=util.format(sql,'day', 'rpt_user_daily',req.body.start,req.body.length);
        }
    }
    
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
///用户扫码表格数据 end /update by cw at 2017-04-28
///用户表格数据下载 start /update by cw at 2017-04-28
router.post('/down_userscan_data', function(req, res) {
    var mchId = req.info.mchId;
    var param = req.body;

    if(typeof param.level!=='undefined' && param.level==='week'){
        param.startTime=hlsUtil.getWeekNumberOfMonth(param.startTime);
        param.endTime=hlsUtil.getWeekNumberOfMonth(param.endTime);
    }

    var whereClause=util.format("mchId=%s and theDate>='%s' and theDate<='%s' ",mchId,param.startTime,param.endTime);

    if(param.pro!=0){
        whereClause += util.format("and proCode='%s'  ", param.pro);
    }
    if(param.city!=0){
        whereClause += util.format("and cityCode='%s'  ", param.city);
    }
    if(param.area!=0){
        whereClause += util.format("and areaCode='%s'  ", param.area);
    }
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param.productid);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param.batchid);
    }

    var sql=`select ifnull(if(u.nickName='',null,u.nickName),'欢乐扫用户') nickName,m.* from (
        select userId,theDate,
        sum(scanCount) scanNum,
        round(sum(rpAmount)/100,2) redNum,
        round(sum(transAmount)/100,2) transNum,
        sum(cardCount) cardNum,
        sum(pointAmount) pointAmount,
        sum(pointUsed) pointUsed
        from %s 
        where ${whereClause} 
        group by theDate,userId ORDER BY theDate DESC, userId DESC)m left join users u on m.userId=u.id ;`;
    
    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(typeof param.level!=='undefined' && param.level==='week'){
            sql=util.format(sql,'rpt_waiter_weekly');
        }else{
            sql=util.format(sql,'rpt_waiter_daily');
        }
    }else{
        if(typeof param.level!=='undefined' && param.level==='week'){
            sql=util.format(sql,'rpt_user_weekly');
        }else{
            sql=util.format(sql,'rpt_user_daily');
        }
    }

    
    co(function*(){
        var title=get_down_title(req.body);
        var filename=encodeURI(util.format('用户扫码统计量_%s',title.time));
        res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
        res.write(iconvLite.encode('用户ID,微信昵称,日期,扫码次数,红包金额（元）,积分,提现金额（元）,乐券（张）,积分使用\n','GBK'));
        var data=yield cooker.serve(sql);
        for(var i=0;i<data.length;i++){
            var d = data[i];
            res.write(iconvLite.encode(`${d.userId},${d.nickName},"\t"${d.theDate},${d.scanNum},${d.redNum},${d.pointAmount},${d.transNum},${d.cardNum},${d.pointUsed}\n`,'GBK'));
        }
        res.end();
    });
});
///用户表格数据下载 end /update by cw at 2017-04-28
// 用户扫码统计详细数据
router.post('/down_userscan_detail_data', function(req, res) {
    var mchId = req.info.mchId;
        var sql = `
            select a.id scanId,a.userId,ifnull(d.nickName,'欢乐扫用户') nickName,
            FROM_UNIXTIME(a.scanTime) scanTime,ifnull(FROM_UNIXTIME(c.getTime),'') getTime,
            a.batchId,a.activityId,count(b.id) redCount,
            ifnull(GROUP_CONCAT(round(b.amount/100,2) SEPARATOR ' 、'),0) redList,
            IFNULL(count(c.id),0) cardCount,
            ifnull(GROUP_CONCAT(f.title SEPARATOR ' 、'),'') cardTitle,
            e.address from scan_log a
            left join user_redpackets b on b.mchId=%s and b.code=a.code and b.role=0
            left join user_cards c on c.code=a.code and c.code is not null and c.sended=1 and c.transId=-1 and c.role=0
            left join users d on d.id=a.userId
            left join geo_gps e on a.geoId=e.id
            left join cards f on f.id=c.cardId
            left join batchs g on a.batchId=g.id
            where a.mchId=%s and a.scanTime>=UNIX_TIMESTAMP('%s') and a.scanTime<=UNIX_TIMESTAMP('%s')
        `;

    if (req.body.month == 0) {
        // 查询指定年1月到12月的所有数据（没有指定月和周）
        var theDateFrom = req.body.year + '-01 00:00:00',
            theDateTo = req.body.year + '-12 23:59:59';
        sql = util.format(sql, mchId,mchId, theDateFrom, theDateTo);

    } else if (req.body.week == 0) {
        // 查询指定月份中所有周的数据（按周）
        if (typeof req.body.level !== 'undefined' && req.body.level === 'week') {
            var weeksArr = hlsUtil.getWeeks(parseInt(req.body.year), parseInt(req.body.month) - 1);

            var theDateFrom = weeksArr[0]['start'].toDateString(),
                theDateTo = weeksArr[weeksArr.length - 1]['start'].toDateString();
            // 获取theDateFrom属于某年的第多少周，如2016-32
            theDateFrom = hlsUtil.getWeekNumberOfMonth(theDateFrom)+' 00:00:00';
            theDateTo = hlsUtil.getWeekNumberOfMonth(theDateTo)+' 23:59:59';
            // 获取theDateTo属于某年的第多少周，如2016-37

            sql = util.format(sql, mchId,mchId, theDateFrom, theDateTo);
        } else {
            var theDateFrom = req.body.year + '-' + req.body.month + '-01',
            theDateTo = req.body.year + '-' + req.body.month + '-31';
            // 查询指定月份的数据（按天）
            sql = util.format(sql, mchId,mchId, theDateFrom, theDateTo);
        }
    } else {
        // 查询指定周的数据（指定了年月周）
        var theDateFrom = req.body.weektime.split('_')[0]+' 00:00:00';
        var theDateTo = req.body.weektime.split('_')[1]+' 23:59:59';
        //为毛一会day一会daily的
        sql = util.format(sql, mchId,mchId, theDateFrom, theDateTo);
    }

    if (req.body.pro != 0) {
        sql += util.format("and concat(substring(e.areaCode,1,2),'0000')='%s' ", req.body.pro);
    }
    if (req.body.city != 0) {
        sql += util.format("and concat(substring(e.areaCode,1,4),'00')='%s' ", req.body.city);
    }
    if (req.body.area !=0) {
        sql += util.format("and e.areaCode='%s' ", req.body.area);
    }

    if (req.body.productid != 0) {
        sql += util.format("and g.productId='%s' ", req.body.productid);
    }
    if(req.body.batchid != 0) {
        sql += util.format("and a.batchId='%s' ", req.body.batchid);
    }

    sql += "group by a.id;";
    cooker.serve(sql).then(function(data) {
        var title=get_down_title(req.body);
        var filename=encodeURI(util.format('用户扫码统计量_%s',title.time))
        res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
        res.write(iconvLite.encode('扫码记录id,用户id,微信昵称,扫码时间,卡券发放时间,乐码id,活动id,红包个数,红包金额（分项）,乐券个数,乐券名称（分项）,扫码地址\n','GBK'));
        for(var i=0;i<data.length;i++){
            var d = data[i];
            res.write(iconvLite.encode(`${d.scanId},${d.userId},${d.nickName},"\t"${d.scanTime},"\t"${d.getTime},${d.batchId},${d.activityId},${d.redCount},${d.redList},${d.cardCount},${d.cardTitle},${d.address}\n`,'GBK'));
        }
        res.end();
    });
});
// 用户扫码统计-end
// ------------------------------------------------
// 获取时段扫码统计数据
router.post('/period_get_data', function(req, res) {
	//var params = JSON.parse(req.body.param),
	var param = req.body.param,
		mchId = req.info.mchId;

	var sql = `
            SELECT hour time, sum(scanNum) scanNum
            FROM %s
            WHERE mchId=%s AND date >= '%s' AND date <= '%s'
		`;
    var queryDate = getQueryDate(param);
    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql = util.format(sql, 'rpt_waiter_area_scanall',mchId, queryDate.from, queryDate.to);
    }else{
        sql = util.format(sql, 'rpt_area_scanall',mchId, queryDate.from, queryDate.to);
    }
    
    sql = generateSql(param, sql);
    sql += "GROUP BY hour";
    cooker.serve(sql).then(function(data) {
        // 初始化时段扫码对象
        var times = [
            '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00',
            '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
            '18:00', '19:00', '20:00', '21:00', '22:00', '23:00',
        ];
        var timeArr = [];
        for (var i = 0; i < times.length; i++) {
            timeArr[i] = {
                'time': times[i],
                'scanNum': 0,
            };
        }

        // 循环迭代出每个时段的扫码量
        var concatResultArr = timeArr.concat(data);
        var result = {};

        concatResultArr.forEach(function(item) {
            if (typeof result[item.time] === 'undefined') {
                result[item.time] = item;
            } else {
                result[item.time]['scanNum'] += parseInt(item['scanNum']);
            }
        });

        // 去除result中得键，只要值，相当于把一个关联数组变成普通的索引数据
        var str = '';
        var resultArr = [];
        for (key in result) {
            resultArr.push(result[key]);
        }
        resultArr.forEach(function(item) {
            str += item['scanNum'] + ',';
        });
        str = str.substr(0, str.length - 1);
        res.json({data: resultArr, string: str});
    });
});

router.post('/down_period_date', function(req, res) {
        var mchId = req.info.mchId;
        var param = JSON.parse(req.body.param);
        var data = JSON.parse(req.body.data);
        var title=get_down_title(param);
        var iconv = new Iconv('UTF-8', 'GBK');
        var filename=encodeURI(util.format('用户扫码统计量_%s',title['time']))
        res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
        res.write(iconv.convert('时间,扫码次数\n'));
        for(var i=0;i<data.length;i++){
            var d = data[i];
            res.write(iconv.convert(`${d.time},${d.scanNum}\n`));
        }
        res.end();
});


///区域分布start /update by cw at 2017-04-13
router.post('/get_sum_of_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body.param;
    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' ",mchId,param['startTime'],param['endTime']);

    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }
    // 处理乌苏数据 只在新疆
    if(mchId==173){
        sql+=' and proCode=650000 ';
    }

    var sql=`SELECT m.*, CASE WHEN m.scan_num / m.scan_all > 1 THEN '100.00' ELSE ifnull(round(m.scan_num / m.scan_all * 100, 2), '0.00') END AS per FROM (
SELECT ifnull(( SELECT COUNT(a.city) FROM (
SELECT COUNT(*) AS city FROM %s force index(uidx_quyu_city) WHERE ${whereClause} AND cityCode != '000000' AND scanNum > '0' GROUP BY cityCode 
) a ), '0') AS city, 
ifnull(( SELECT COUNT(b.red_city) AS red FROM (
SELECT COUNT(id) AS red_city FROM %s force index(uidx_quyu_city) WHERE ${whereClause} AND cityCode != '000000' AND redNum > '0' GROUP BY cityCode 
) b ), '0') AS red_city, 
ifnull((SELECT SUM(scanNum) AS scan_num FROM %s force index(uidx_quyu_city) WHERE ${whereClause} AND cityCode != '000000' 
), '0') AS scan_num, 
ifnull((SELECT SUM(scanNum) AS scanNum FROM %s WHERE ${whereClause} 
), '0') AS scan_all ) m;`;
    
    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql = util.format(sql, 'rpt_waiter_area_daily','rpt_waiter_area_daily','rpt_waiter_area_daily','rpt_waiter_area_scanall');
    }else{
        sql = util.format(sql, 'rpt_area_daily','rpt_area_daily','rpt_area_daily','rpt_area_scanall');
    }
    
    consoleSqlLog('区域分布汇总',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        res.send(result[0]);
    });
});

// 区域分布-卡片数据 /update by cw at 2017-04-13
router.post('/get_total_data',function(req,res){
    var mchId=req.info.mchId;
    var param=req.body.param;
    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' ",mchId,param['startTime'],param['endTime']);

    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }

    // 查询出有位置的总和
    var sql=`select ifnull(sum(scanNum),0) scanNum,ifnull(round(sum(redNum)/100,2),0) redNum,ifnull(sum(pointAmount),0) pointAmount from %s 
    where ${whereClause} `;
    // 查询出没有位置的总和
    var sql_none=`select ifnull(sum(scanNum),0) scanNum,ifnull(round(sum(redNum)/100,2),0) redNum,ifnull(sum(pointAmount),0) pointAmount from %s force index(uidx_useIndex) 
    where ${whereClause} and areaCode='000000' `;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql = util.format(sql, 'rpt_waiter_area_daily');
        sql_none = util.format(sql_none, 'rpt_waiter_area_daily');
    }else{
        sql = util.format(sql, 'rpt_area_daily');
        sql_none = util.format(sql_none, 'rpt_area_daily');
    }

    co(function*(){
        consoleSqlLog('区域分布-卡片数据_all',sql);
        consoleSqlLog('区域分布-卡片数据_none',sql_none);
        var total = yield cooker.serve(sql);
        var total_none = yield cooker.serve(sql_none);
        var respData = {
            total: total[0], 
            total_none: total_none[0],
        };
        res.send(respData);
    });
});
// 区域分布-省份地图数据 /update by cw at 2017-04-13
router.post('/get_pro_map_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body.param;
    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' ",mchId,param['startTime'],param['endTime']);
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }
    var sql=`select a.code,a.name,ifnull(b.scan_nums,0) value from areas a 
            left join (select proCode,sum(scanNum) scan_nums from %s where ${whereClause}  
            group by proCode) b on a.code=b.proCode where a.level='0' group by a.code;`;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql = util.format(sql, 'rpt_waiter_area_daily');
    }else{
        sql = util.format(sql, 'rpt_area_daily');
    }

    consoleSqlLog('区域分布-省份地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        res.send(result);
    });
});
// 区域分布-城市地图数据 /update by cw at 2017-04-20
router.post('/get_city_map_data', function (req, res) {
    var mchId=req.info.mchId;
    var id=req.body.id;
    var param=req.body.param;

    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' and areaCode>%s and areaCode<%s ",mchId,param['startTime'],param['endTime'],param['proCode'],param['proCode'].substring(0, 2)+'9999');
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }

    var sql=`select t.name,s.* from (
select %s cityCode,sum(m.scanNum) scanNum from (
select areaCode,sum(scanNum) scanNum
        from %s force index(uidx_useIndex) where ${whereClause}
group by areaCode,userId)m group by cityCode) s inner join areas t on s.cityCode=t.code;`;

    //查询出城市的默认
    var areaSql=`select name,0 value from areas where parentCode=%s`;

    var zxs = ["110000", "310000", "120000", "500000"];

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(zxs.indexOf(id)!=-1){
            var sql=util.format(sql,'m.areaCode','rpt_waiter_area_daily');
            var areaSql=util.format(areaSql,param['proCode'].substring(0, 2)+'0100');
        }else{
            var sql=util.format(sql,'concat(substring(m.areaCode,1,4),"00")','rpt_waiter_area_daily');
            var areaSql=util.format(areaSql,param['proCode']);
        }
    }else{
        if(zxs.indexOf(id)!=-1){
            var sql=util.format(sql,'m.areaCode','rpt_area_daily');
            var areaSql=util.format(areaSql,param['proCode'].substring(0, 2)+'0100');
        }else{
            var sql=util.format(sql,'concat(substring(m.areaCode,1,4),"00")','rpt_area_daily');
            var areaSql=util.format(areaSql,param['proCode']);
        }
    }
    

    consoleSqlLog('区域分布-城市地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        var city = yield cooker.serve(areaSql);
        res.send({data:result,city:city});
    });
});
// 区域分布-区域地图数据 /update by cw at 2017-04-20
router.post('/get_area_map_data', function (req, res) {
    var mchId=req.info.mchId;
    var id=req.body.id;
    var param=req.body.param;

    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' and areaCode>%s and areaCode<%s ",mchId,param['startTime'],param['endTime'],param['cityCode'],param['cityCode'].substring(0, 4)+'99');
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }

    var sql=`select t.name,s.* from (
        select %s areaCode,sum(m.scanNum) scanNum from (select areaCode,sum(scanNum) scanNum from %s force index(uidx_useIndex) 
        where ${whereClause}
        group by areaCode,userId)m group by areaCode) s inner join areas t on s.areaCode=t.code;`;
    //查询出城市的默认
    var areaSql=`select name,0 value from areas where parentCode=%s`;

    var zxs = ["110000", "310000", "120000", "500000"];

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(zxs.indexOf(id)!=-1){
            var sql=util.format(sql,'m.areaCode','rpt_waiter_area_daily');
            var areaSql=util.format(areaSql,param['cityCode'].substring(0, 2)+'0100');
        }else{
            var sql=util.format(sql,'m.areaCode','rpt_waiter_area_daily');
            var areaSql=util.format(areaSql,param['cityCode']);
        }
    }else{
        if(zxs.indexOf(id)!=-1){
            var sql=util.format(sql,'m.areaCode','rpt_area_daily');
            var areaSql=util.format(areaSql,param['cityCode'].substring(0, 2)+'0100');
        }else{
            var sql=util.format(sql,'m.areaCode','rpt_area_daily');
            var areaSql=util.format(areaSql,param['cityCode']);
        }
    }
    
    consoleSqlLog('区域分布-区域地图数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        var city = yield cooker.serve(areaSql);
        res.send({data:result,city:city});
    });
});

router.post('/get_table_pro_data', function (req, res) {
    var mchId=req.info.mchId;
    var param=req.body.param;
    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' ",mchId,param['startTime'],param['endTime']);
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }
    var sql=`select t.name,s.* from (
select m.proCode,sum(m.scanNum) scanNum,round(sum(m.redNum)/100,2) redNum,sum(m.pointAmount) pointAmount,count(m.uscanNum) userId from (
select proCode,sum(scanNum) scanNum,
        sum(redNum) redNum,
        sum(pointAmount) pointAmount,
        COUNT(userId) uscanNum
        from %s force index(uidx_useIndex_pro) where ${whereClause}
group by proCode,userId
)m group by proCode) s inner join areas t on s.proCode=t.code order by s.scanNum desc;`;
    
    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql=util.format(sql,'rpt_waiter_area_daily');
    }else{
        sql=util.format(sql,'rpt_area_daily');
    }
    consoleSqlLog('区域分布-表格省份数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        res.send({data:result});
    });
});

router.post('/get_table_city_data', function (req, res) {
    var mchId=req.info.mchId;
    var id=req.body.id;
    var param=req.body.param;

    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' and areaCode>%s and areaCode<%s ",mchId,param['startTime'],param['endTime'],param['proCode'],param['proCode'].substring(0, 2)+'9999');
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }

    var sql=`select t.name,s.* from (
select %s cityCode,sum(m.scanNum) scanNum,round(sum(m.redNum)/100,2) redNum,sum(m.pointAmount) pointAmount,count(m.uscanNum) userId from (
select areaCode,sum(scanNum) scanNum,
        sum(redNum) redNum,
        sum(pointAmount) pointAmount,
        COUNT(userId) uscanNum
        from %s force index(uidx_useIndex) where ${whereClause}
group by areaCode,userId)m group by cityCode) s inner join areas t on s.cityCode=t.code order by s.scanNum desc;`;

    var zxs = ["110000", "310000", "120000", "500000"];

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(zxs.indexOf(id)!=-1){
            var sql=util.format(sql,'m.areaCode','rpt_waiter_area_daily');
        }else{
            var sql=util.format(sql,'concat(substring(m.areaCode,1,4),"00")','rpt_waiter_area_daily');
        }
    }else{
        if(zxs.indexOf(id)!=-1){
            var sql=util.format(sql,'m.areaCode','rpt_area_daily');
        }else{
            var sql=util.format(sql,'concat(substring(m.areaCode,1,4),"00")','rpt_area_daily');
        }
    }

    consoleSqlLog('区域分布-表格城市数据',sql);
    co(function*(){
        var result = yield cooker.serve(sql);
        res.send({data:result});
    });
});


// 区域分布数据下载 /update by cw at 2017-05-31
router.post('/down_area_date',function(req,res){
    var mchId=req.info.mchId;
    var param=req.body;
    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' and areaCode!='000000' ",mchId,param['startTime'],param['endTime']);
    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param['productid']);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param['batchid']);
    }
    //是否是日数据
    if(param.is_daily==0){
        var sql=`select b.name proName,
            ifnull(c.name,b.name) cityName,
            ifnull(ifnull(d.name,c.name),b.name) areaName,
            a.scanNum,
            a.userId,
            a.redNum,
            a.pointAmount from (
            select concat(substring(areaCode,1,2),'0000') proCode,
            concat(substring(areaCode,1,4),'00') cityCode,
            areaCode,ifnull(sum(scanNum),0) scanNum,
            COUNT(DISTINCT userId) userId,
            ifnull(round(sum(redNum)/100,2),0) redNum,
            ifnull(sum(pointAmount),0) pointAmount 
            from %s
            where ${whereClause} group by areaCode) a 
            left join areas b on a.proCode=b.code 
            left join areas c on a.cityCode=c.code 
            left join areas d on a.areaCode=d.code;`;

        co(function*(){
            var title=get_down_title(req.body);
            var filename=encodeURI(util.format('地域统计量_%s_%s',param['startTime'],param['endTime']));
            res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
            res.write(iconvLite.encode('省份,城市,区域,扫码次数,参与人数,红包金额（元）,积分\n','GBK'));
            
            //兼容服务员 tab=0 消费者 tab=1 服务员
            if(typeof param.tab!=='undefined' && param.tab==1){
                sql=util.format(sql,'rpt_waiter_area_daily');
            }else{
                sql=util.format(sql,'rpt_area_daily');
            }

            consoleSqlLog('区域分布数据下载-按照省市区',sql);
            var data=yield cooker.serve(sql);

            for(var i=0;i<data.length;i++){
                var d = data[i];
                res.write(iconvLite.encode(`${d.proName},${d.cityName},${d.areaName},${d.scanNum},${d.userId},${d.redNum},${d.pointAmount}\n`,'GBK'));
            }
            res.end();
        });
    }

    if(param.is_daily==1){
        var data_arr=getDays(param['startTime'],param['endTime']);
        var sql=`select b.name Province,ifnull(c.name,b.name) City,ifnull(d.name,c.name) Area,a.* FROM(select concat(substring(areaCode,1,2),'0000') proCode,concat(substring(areaCode,1,4),'00') cityCode,areaCode,`;
        for(var i=0;i<data_arr.length;i++){
            sql+="ifnull(sum(case when date='"+data_arr[i]+"' then scanNum end),0) '"+data_arr[i]+"',";
        }
        sql+=`ifnull(sum(scanNum),0) scanNum from %s where ${whereClause} group by areaCode) a 
            left join areas b on a.proCode=b.code 
            left join areas c on a.cityCode=c.code 
            left join areas d on a.areaCode=d.code;`;

        co(function*(){
            var title=get_down_title(req.body);
            var filename=encodeURI(util.format('地域统计量_%s_%s_日扫码数据',param['startTime'],param['endTime']));
            res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
            //表头
            var subtitle="省份,城市,区域,";
            for(var t=0;t<data_arr.length;t++){
                subtitle+=data_arr[t]+",";
            }
            subtitle+="合计\n";

            res.write(iconvLite.encode(subtitle,'GBK'));

            //兼容服务员 tab=0 消费者 tab=1 服务员
            if(typeof param.tab!=='undefined' && param.tab==1){
                sql=util.format(sql,'rpt_waiter_area_daily');
            }else{
                sql=util.format(sql,'rpt_area_daily');
            }
            
            consoleSqlLog('区域分布数据下载-按照日扫码数据',sql);
            var data=yield cooker.serve(sql);

            for(var i=0;i<data.length;i++){
                delete data[i]['proCode'];
                delete data[i]['cityCode'];
                delete data[i]['areaCode'];

                var row=[];
                for(var name in data[i]){
                    row.push(data[i][name]);
                }
                res.write(iconvLite.encode("\t"+row.join(',')+"\r\n",'GBK'));
            }
            res.end();
        });
    }
});
///区域分布end
// 热力图开始
router.post('/get_scan_area_data', function (req, res) {
    var mchId=req.info.mchId;
    var north=parseInt(req.body.north);
    var south=parseInt(req.body.south);
    var west=parseInt(req.body.west);
    var east=parseInt(req.body.east);
    var level=parseInt(req.body.level);
    var westLng=parseFloat(req.body.westlng);
    var eastLng=parseFloat(req.body.eastlng);
    var times=parseInt(req.body.times);
    //var start=req.body.start;
    //var end=req.body.end;

    var batchId=parseInt(req.body.batchid);
    var productId=parseInt(req.body.productid);
    var year=req.body.year;
    var month=req.body.month;
    var week=req.body.week;
    var day=req.body.day;
    var pro=req.body.pro;
    var city=req.body.city;

    var tableName='';
    var scanDate='';

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof req.body.tab!=='undefined' && req.body.tab==1){
        if(day!='0'){
            tableName='rpt_waiter_geo_daily';
            scanDate=day;
        }
        else if(week!='0'){
            tableName='rpt_waiter_geo_weekly';
            scanDate=week;
        }
        else{
            tableName='rpt_waiter_geo_monthly';
            scanDate=year+'-'+month;
        }
    }else{
        if(day!='0'){
            tableName='rpt_geo_daily';
            scanDate=day;
        }
        else if(week!='0'){
            tableName='rpt_geo_weekly';
            scanDate=week;
        }
        else{
            tableName='rpt_geo_monthly';
            scanDate=year+'-'+month;
        }
    }

    var conSql='mchId=? and level=? and scanDate=?';
    var conParams=[mchId,level,scanDate];
    if(batchId!=0){
        conSql+=' and batchId=?';
        conParams.push(batchId);
    }
    if(productId!=0){
        conSql+=' and productId=?';
        conParams.push(productId);
    }
    if(city!='0'){
        conSql+=' and cityCode=?';
        conParams.push(city);
    }
    else if(pro!='0'){
        conSql+=' and proCode=?';
        conParams.push(pro);
    }

    var dig='0x00000000FFFFFFFF';
    var sql='select scale>>32 lngScale,scale&'+dig+' latScale,sum(scanCount) count from '+tableName+
    ' where '+conSql+' and latScale is not null and\
    latScale<? and latScale>? and lngScale>? and lngScale<? and\
    f_geo_get_lng_by_scale(lngScale*?,latScale*?)<? and f_geo_get_lng_by_scale((lngScale+1)*?,(latScale+1)*?)>? group by scale;';
    var parmas=conParams.concat([north,south,west,east,times,times,eastLng,times,times,westLng]);

    var sql2='select max(count) maxCount from (select scale,sum(scanCount) count\
    from '+tableName+' where '+conSql+' and\
    latScale is not null group by scale) t;';

    cooker.serve(sql.toSql(parmas)).then(function(data) {
        cooker.serve(sql2.toSql(conParams)).then(function(data2) {
            var jsonData = {
                data: data==null?[]:data,
                max: data2[0].maxCount*0.618
            };
        	res.json(jsonData);
        });
    });
});

var get_down_title=function(param){
    var dTitle='';
    var dTime='';
    if(param['month']==0){//年份数据
        dTitle="月份";
        dTime=param['year']+'年';
    }
    if(param['week']==0&&param['month']!=0){//月份数据1-31天
        if(param['level']!=null&&param['level']=='week'){
            dTitle="周";
            dTime=param['year']+'-'+param['month']+'(周数据)';
        }else{
            dTitle="日期";
            if(param['is_daily']!=null&&param['is_daily']==1){
                dTime=param['year']+'-'+param['month']+'(日数据)';
            }else{
                dTime=param['year']+'-'+param['month'];
            }
        }
    }
    if(param['week']!=0){//具体周的数据
        dTitle="日期";
        dTime=param['week']+' 周数据';
    }
    if(param['day']!=null&&param['day']!=0){
        dTitle="日期";
        dTime=param['day']+'数据';
    }
    return {'title':dTitle,'time':dTime};
}

// 热力图end
// 业务分析开始
router.post('/get_business_data', function (req, res) {
    var mchId = req.info.mchId;
    var param = req.body.param;

    if(typeof param.level!=='undefined' && param.level==='week'){
        param.startTime=hlsUtil.getWeekNumberOfMonth(param.startTime);
        param.endTime=hlsUtil.getWeekNumberOfMonth(param.endTime);
    }

    var whereClause=util.format("mchId = %s AND theDate >= '%s' AND theDate <= '%s' ",mchId,param.startTime,param.endTime);

    if (param.productid != 0) {
        whereClause += util.format("and productId='%s'  ", param.productid);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and batchId='%s' ", param.batchid);
    }

    var sql=`select theDate,
    sum(scanCount) scanNum,
    round(sum(rpAmount)/100,2) red_amount,
    round(sum(transAmount)/100,2) trans_amount,
    sum(cardCount) card_num,sum(pointAmount) point_amount,
    sum(pointUsed) point_num from %s where ${whereClause} group by theDate;`;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        if(typeof param.month!=='undefined' && param.month==0){
            sql=util.format(sql, 'rpt_waiter_monthly');
        }else if(typeof param.week!=='undefined' && typeof param.month!=='undefined' && param.week==0 && param.month!==0){
            if(typeof param.level!=='undefined' && param.level==='week'){
                sql=util.format(sql, 'rpt_waiter_weekly');
            }else{
                sql=util.format(sql, 'rpt_waiter_daily');
            }
        }else{
            sql=util.format(sql, 'rpt_waiter_daily');
        }
    }else{
        if(typeof param.month!=='undefined' && param.month==0){
            sql=util.format(sql, 'rpt_user_monthly');
        }else if(typeof param.week!=='undefined' && typeof param.month!=='undefined' && param.week==0 && param.month!==0){
            if(typeof param.level!=='undefined' && param.level==='week'){
                sql=util.format(sql, 'rpt_user_weekly');
            }else{
                sql=util.format(sql, 'rpt_user_daily');
            }
        }else{
            sql=util.format(sql, 'rpt_user_daily');
        }
    }

    consoleSqlLog('业务分析报表',sql);
    cooker.serve(sql).then(function(data) {
        res.send(data);
    })



    
});
router.post('/down_business_date',function(req,res){
    //这个必须写JSON.parse因为前台发送的是两个字段的字符串，不转换成对象就变成字符串了
    var param=JSON.parse(req.body.param);
    var data=JSON.parse(req.body.data);

    var title=get_down_title(param);
    var iconv = new Iconv('UTF-8', 'GBK');
    var filename=encodeURI(util.format('业务统计量_%s',title['time']));;
    res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
    res.write(iconv.convert(title['title']+',扫码次数,红包金额（元）,提现金额（元）,乐券（张）,积分,积分使用\n'));
    for(var i=0;i<data.length;i++){
        res.write("\t"+data[i]['theDate']+','+data[i]['scanNum']+','+data[i]['red_amount']+','+data[i]['trans_amount']+','+data[i]['card_num']+','+data[i]['point_amount']+','+data[i]['point_num']+"\r\n");
    }
    res.end();
});
// 业务分析-end
// ------------------------------------------------
// 获取新老用户分析数据 update by cw 2017-05-25
router.post('/get_useranalysis_data', function (req, res) {
    //var params = JSON.parse(req.body.param),
    var param=req.body.param;
    var mchId = req.info.mchId;

    var whereClause=util.format("rud.mchId=%s AND theDate >= '%s' AND theDate <= '%s' ",mchId,param.startTime,param.endTime);

    if (param.productid != 0) {
        whereClause += util.format("AND productId = '%s' ", param.productid);
    }
    if(param.batchid != 0) {
        whereClause += util.format("AND batchId = '%s' ", param.batchid);
    }

    var sql=`
        select old.theDate,ifnull(osc,'0') oldScan,ifnull(nsc,'0') newScan from (
select theDate,sum(scanCount) osc from %s rud
  inner join users on rud.userId=users.id and from_unixtime(users.createTime)<theDate where ${whereClause} 
group by theDate) old left join (select theDate,sum(scanCount) nsc from %s rud
  inner join users on rud.userId=users.id and from_unixtime(users.createTime)>=theDate where ${whereClause} 
group by theDate) new on old.theDate=new.theDate;
    `;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql = util.format(sql, 'rpt_waiter_daily','rpt_waiter_daily');
    }else{
        sql = util.format(sql, 'rpt_user_daily','rpt_user_daily');
    }
    cooker.serve(sql).then(function(data) {
        consoleSqlLog('新老用户报表',sql);
        res.send(data);
    });
});

router.post('/down_useranalysis_date', function(req, res) {
    var param= JSON.parse(req.body.param);
    var mchId = req.info.mchId;

    var whereClause=util.format("rud.mchId=%s AND theDate >= '%s' AND theDate <= '%s' ",mchId,param.startTime,param.endTime);

    if (param.productid != 0) {
        whereClause += util.format("AND productId = '%s' ", param.productid);
    }
    if(param.batchid != 0) {
        whereClause += util.format("AND batchId = '%s' ", param.batchid);
    }

    var sql=`
        select old.theDate,ifnull(osc,'0') oldScan,ifnull(nsc,'0') newScan from (
select theDate,sum(scanCount) osc from %s rud
  inner join users on rud.userId=users.id and from_unixtime(users.createTime)<theDate where ${whereClause} 
group by theDate) old left join (select theDate,sum(scanCount) nsc from %s rud
  inner join users on rud.userId=users.id and from_unixtime(users.createTime)>=theDate where ${whereClause} 
group by theDate) new on old.theDate=new.theDate order by old.theDate desc;
    `;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql = util.format(sql, 'rpt_waiter_daily','rpt_waiter_daily');
    }else{
        sql = util.format(sql, 'rpt_user_daily','rpt_user_daily');
    }
    cooker.serve(sql).then(function(data) {
        var title=get_down_title(JSON.parse(req.body.param));
        var filename=encodeURI(util.format('新老用户报表_%s',title.time))
        res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':'attachment;filename='+filename+'.csv'});
        res.write(iconvLite.encode('日期,新用户扫码次数,老用户扫码次数\n','GBK'));
        for(var i=0;i<data.length;i++){
            var d = data[i];
            res.write(iconvLite.encode(`${d.theDate},${d.newScan},${d.oldScan}\n`,'GBK'));
        }
        res.end();
    });
});

router.post('/get_table_data', function (req, res) {

});

router.post('/get_policy_data', function (req, res) {

});

//router.post('/get_trend_data', function (req, res) {
//
//});

if(process.env.NODE_ENV=='localhost'){
    router.use(bodyParser.text());
}
// 用户排行开始
router.post('/get_ranking_list',function(req,res){
    var params=req.body.param;
    params.start=req.body.start;
    params.length=req.body.length;
    params.order=req.body.order;
    get_ranking_data(req.info.mchId,params).then(function(ret){
        var jsonData = {draw: req.body.draw, recordsTotal: ret.cnt, recordsFiltered: ret.cnt, data: ret.data};
        res.json(jsonData);
    });
});

router.post('/down_ranking_list',function(req,res){
    var params=req.body;
    get_ranking_data(req.info.mchId,params).then(function(ret){
        var filename=encodeURI(util.format('用户排行_%s_%s.csv',params.year,params.month));
        res.writeHead(200, {'Content-type' : 'application/octet-stream','Content-Disposition':`attachment;filename=${filename}`});
        res.write(iconvLite.encode('用户排名,用户id,微信昵称,扫码次数,提现金额（元）,积分,积分使用\n','GBK'));
        for(var i=0;i<ret.data.length;i++){
            var d=ret.data[i];
            res.write(iconvLite.encode(`${d.rank_id},${d.userId},${d.nickname},${d.scanNum},${d.transAmount},${d.pointAmount},${d.pointUsed}\n`,'GBK'));
        }
        res.end();
    });
});
function get_ranking_data(mchId,params){
    var week=(params.week.indexOf('-')==-1)?params.week:params.week.split('-')[1];
    var span=new Date().getDateSpan(params.year,params.month,week,params.day);
    var time=get_time_screening(params);
    var whereClause=util.format("mchId=%s and theDate>='%s' and theDate<='%s'",
        mchId,time['start'],time['end']);
    var orderClause='';
    if (params.pro != 0) {
        whereClause += util.format("AND proCode = '%s' ", params.pro);
    }
    if (params.city != 0) {
        whereClause += util.format("AND cityCode = '%s' ", params.city);
    }
    if (params.area!=undefined&&params.area !=0) {
        whereClause += util.format("AND areaCode = '%s' ", params.area);
    }
    if (params.productid != 0) {
        whereClause += util.format("AND productId = '%s' ", params.productid);
    }
    if(params.batchid != 0) {
        whereClause += util.format("AND batchId = '%s' ", params.batchid);
    }
    var column='';
    var order=params.order;
    if(order!=null){
       var column=order[0]['column'];
       if(column==3){
           column='scanNum';
       }else if(column==4){
           column='transAmount';
       }else if(column==5){
           column='pointAmount';
       }else if(column==6){
           column='pointUsed';
       }else{
           column='scanNum';
       }
    }
    column=(column=='')?'scanNum':column;
    orderClause+=' '+column+' desc';
    //var start=(params.start==null)?0:params.start;
    //var length=(params.length==null)?10:params.length;
    var limitClause='';
    if(params.start!=undefined&&params.length!=undefined){
        limitClause=`limit ${params.start},${params.length}`;
    }
    var sql=`select m.userId,ifnull(u.nickName,'欢乐扫用户') nickname,m.scanNum,m.transAmount,m.pointAmount,m.pointUsed from
        (select a.userId,sum(a.scanNum) scanNum,round(sum(a.transAmount)/100,2) transAmount,sum(a.pointAmount) pointAmount,sum(a.pointUsed) pointUsed  from %s a
         where ${whereClause} group by a.userId order by ${orderClause} ${limitClause}) m
         inner join users u on m.userId=u.id
    `;
    var sql_cnt=`select count(a.id) cnt from (select id from %s where ${whereClause} group by userId) a`;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof params.tab!=='undefined' && params.tab==1){
        sql = util.format(sql, 'rpt_waiter_rank');
        sql_cnt = util.format(sql_cnt, 'rpt_waiter_rank');
    }else{
        sql = util.format(sql, 'rpt_user_rank');
        sql_cnt = util.format(sql_cnt, 'rpt_user_rank');
    }

    return co(function*(){
        var start=(params.start==undefined)?0:parseInt(params.start);
        consoleSqlLog('用户排行数据',sql);
        consoleSqlLog('用户排行总记录',sql_cnt);
        var data=(yield cooker.serve(sql));
        var cnt=(yield cooker.serve(sql_cnt));
        for(var i=1;i<=data.length;i++){
            data[i-1]['rank_id']=parseInt(start)+i;
        }
        cnt=cnt[0].cnt;
        return {data:data,cnt:cnt};
    });
}
// 用户排行-end
// 对比分析-开始 add by cw update at 2017-06-15
router.post('/get_trend_data',function(req,res){
    var mchId=req.info.mchId;
    var params=req.body.param;

    var sum = 0;
    var theXaxis=[];
    var aver=0;
    var scanNum=[];
    var aver_scanNum=[];
    var arrdata={data:[]};

    var whereClause=util.format("mchId=%s and date>='%s' and date<='%s' ",
        mchId,params['startTime'],params['endTime']);
    if (params.pro != 0) {
        whereClause += util.format("AND proCode = '%s' ", params.pro);
    }
    if (params.city != 0) {
        whereClause += util.format("AND cityCode = '%s' ", params.city);
    }
    if (params.area!=undefined&&params.area !=0) {
        whereClause += util.format("AND areaCode = '%s' ", params.area);
    }
    if (params.productid != 0) {
        whereClause += util.format("AND productId = '%s' ", params.productid);
    }
    if(params.batchid != 0) {
        whereClause += util.format("AND batchId = '%s' ", params.batchid);
    }
    var sql=`select date theDate,sum(scanNum) scanNum from %s where ${whereClause} group by theDate;`;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof params.tab!=='undefined' && params.tab==1){
        sql = util.format(sql, 'rpt_waiter_area_daily');
    }else{
        sql = util.format(sql, 'rpt_area_daily');
    }

    co(function*(){
        consoleSqlLog('对比分析',sql);
        var data=(yield cooker.cook(sql));

        //处理下数据补全
        var dates=getDays(params['startTime'],params['endTime']);

        
        for(var i=0;i<dates.length;i++){
            arrdata.data.push({theDate:dates[i],scanNum:0});
        }
        var ssdata=arrdata.data;

        //试着合并数据
        for(var d in ssdata){
            for(var dd in data){
                if(data[dd].theDate==ssdata[d].theDate){
                    ssdata[d].scanNum=data[dd].scanNum
                }
            }
        }

        for(var item in ssdata) {
            theXaxis.push(ssdata[item].theDate);
            scanNum.push(ssdata[item].scanNum);
            sum=parseInt(sum) + parseInt(ssdata[item].scanNum);
        }

        //基准值
        aver=sum/ssdata.length;
        for(var i in scanNum){
            if(aver==0){
                aver_scanNum.push(scanNum[i]);
            }else{
                aver_scanNum.push((scanNum[i]/aver).toFixed(2));
            }
        }

        //组装数组
        var arr={
            theDate:theXaxis,
            cityName:params.mycity,
            scanNum:{
                name:'产品：'+params.productName+'  乐码批次：'+params.batchName+'  区域：'+params.mycity,
                type:'line',
                data:aver_scanNum,
                truedata:scanNum
            }
        };
        res.send(arr);
    });
});
// 对比分析-end
// 消费者画像-开始 add by cw
router.post('/get_portrait_data', function (req, res) {
    var mchId=req.info.mchId;
    var params=req.body;

    var whereClause=util.format("mchId=%s",req.info.mchId);
    if(params.proCode!=0){
        whereClause+=util.format(" and proCode='%s' ",params.proCode);
    }
    if(params.cityCode!=0){
        whereClause+=util.format(" and cityCode='%s' ",params.cityCode);
    }
    if(params.areaCode!=0){
        whereClause+=util.format(" and areaCode='%s' ",params.areaCode);
    }
    if(params.age!=0){
        whereClause+=util.format(" and age='%s' ",params.age);
    }
    if(params.sex!=0){
        whereClause+=util.format(" and sex='%s' ",params.sex);
    }
    if(params.constellation!=0){
        whereClause+=util.format(" and constellation='%s' ",params.constellation);
    }
    if(params.time!=0){
        whereClause+=util.format(" and time='%s' ",params.time);
    }
    sql=`select * from %s where ${whereClause} order by num desc,areaCode desc,total desc limit 3;`;
    sql_cnt=`select sum(num) cnt from %s where ${whereClause};`;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof params.tab!=='undefined' && params.tab==1){
        sql = util.format(sql, 'rpt_waiter_portrait');
        sql_cnt = util.format(sql_cnt, 'rpt_waiter_portrait');
    }else{
        sql = util.format(sql, 'rpt_user_portrait');
        sql_cnt = util.format(sql_cnt, 'rpt_user_portrait');
    }

    co(function*(){
        var data=(yield cooker.serve(sql));
        var count=(yield cooker.serve(sql_cnt));
        var result=[];
        //循环查询出地区
        for(i=0;i<data.length;i++){
            if(data[i].areaCode==0){
                data[i].city='未知城市';
                data[i].count=count[0].cnt;
                data[i].per=parseFloat(parseFloat((data[i].num/count[0].cnt)*100).toFixed(2));
            }else{
                var tscity=yield cooker.cook(util.format('select * from areas where parentCode=(select code from areas where code=%s)',data[i]['areaCode']));
                if(tscity.length>0){
                    var areainfo=yield cooker.cook(util.format('select concat(b.name,a.name) as name,a.name areaName from areas a left join areas b on a.parentCode=b.code where a.code=%s)',data[i]['areaCode']));
                }else{
                    var areainfo=yield cooker.cook(util.format('select concat(c.name,b.name) as name,a.name areaName from areas a left join areas b on a.parentCode=b.code left join areas c on b.parentCode=c.code where a.code=%s',data[i]['areaCode']));
                }
                if(areainfo[0].name!==''){
                    if(params.proCode==0){
                        data[i].city=areainfo[0].name;
                    }else{
                        data[i].city=areainfo[0].name+areainfo[0].areaName;
                    }
                }else{
                    data[i].city='未知城市';
                }
                data[i].count=count[0].cnt;
                data[i].per=parseFloat(parseFloat((data[i].num/count[0].cnt)*100).toFixed(2));
            }
        }
        res.send(data);
    });
});

// 消费者画像-end

module.exports = chartsClass;

/**
 * 根据请求参数，返回查询的日期区间
 *
 * @param  {object} params http请求参数
 * @return {object}
 */
var getQueryDate = function(params) {
    var theDateFrom, theDateTo;
    if (params.month == 0) {
        theDateFrom = params.year + '-01-01';
        theDateTo = params.year + '-12-31';
    }
    if (params.week == 0 && params.month != 0) {
        theDateFrom = params.year + '-' + params.month + '-01';
        theDateTo = params.year + '-' + params.month + '-31';
    }
    if (params.week != 0) {
        theDateFrom = params.weektime.split('_')[0];
        theDateTo = params.weektime.split('_')[1];
    }
    if (typeof params.day !== 'undefined' && params.day != 0) {
        theDateFrom = params.day;
        theDateTo = params.day;
    }
    return {
        "from": theDateFrom,
        "to": theDateTo,
    };
};

/**
 * getQueryDate的别名
 *
 * @param  {object} params http请求参数
 * @return {object}
 */
var get_time_screening = function(params) {
    var queryDate = getQueryDate(params);
    return {"start": queryDate.from, "end": queryDate.to};
};

/**
 * 根据查询条件，返回增加查询条件后的sql语句，针对batchid和productid
 *
 * @param  {object} params 请求参数
 * @param  {string} sql sql语句
 * @return {string}
 */
var generateSql = function(params, sql) {
    if (params.productid != 0) {
        sql += util.format("AND productId = '%s' ", params.productid);
    }
    if(params.batchid != 0) {
        sql += util.format("AND batchId = '%s' ", params.batchid);
    }
    return sql;
}

/**
 * 根据查询条件，返回增加查询条件后的sql语句，针对地区筛选
 *
 * @param  {object} params 请求参数
 * @param  {string} sql sql语句
 * @return {string}
 */
var generateSqlForLocation = function(params, sql) {
    if (params.pro != 0) {
        sql += util.format("AND proCode = '%s' ", params.pro);
    }
    if (params.city != 0) {
        sql += util.format("AND cityCode = '%s' ", params.city);
    }
    if (params.area !=0) {
        sql += util.format("AND areaCode = '%s' ", params.area);
    }
    return sql;
}

//控制台打印日志
var consoleSqlLog=function(title,sql){
    // console.log('========================'+title+'开始==========');
    // console.log(sql);
    // console.log('========================'+title+'结束==========');
}

//根据起止日期 列举出每天

var getDays=function(start,end){
    var start_arrs=start.split('-');
    var date=new Date(parseInt(start_arrs[0]),parseInt(start_arrs[1])-1,parseInt(start_arrs[2]));
    var end_arrs=end.split('-');
    var endDate=new Date(parseInt(end_arrs[0]),parseInt(end_arrs[1])-1,parseInt(end_arrs[2]));
    var result=[];
    while(date.getTime()<=endDate.getTime()){
        result.push(date.toDateString());
        date.setDate(date.getDate()+1);
    };
    return result;
};
