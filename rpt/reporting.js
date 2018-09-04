var co=require('co');
var router=require('express').Router();
var bodyParser = require('body-parser');
var hlsUtil = require('./hls_util');
var util=require('util');
router.use(bodyParser.urlencoded({ extended: true }));
router.use(bodyParser.json());

var ALL_SESS_PREFIX='all_sess_prefix_'
var cache=null;
var session=null;
function reportingClass(options){
    cooker=options.cooker;
    session=options.session;
    return router;
}
router.use(require('./cookie'));
router.post('/test2',function(req,res){
	console.log('test2');
	res.send('test2');
});

//向谌威演示写法
// var mch_id=req.query.mch_id;
// var mch_id=req.body.mch_id;
router.get('/show_to_cw',function(req,res){
    var mch_id=req.info.mchId;
    //长sql可以用如下语法糖，叫Template string
    var format=`
        select * from scan_log limit %d,%d;
        `;
    var sql=util.format(format,0,10);
    //then是异步回调
    cooker.cook(sql).then(function(data){
        //在co里，可以用yield保证顺序执行
        co(function *(){
            var arr=[];
            for(var i=0;i<data.length;i++){
                //yield后的语句，即then方法会执行，并保证语句顺序执行
                var d = yield cooker.cook(util.format('select * from users where id=%d',data[i].userId));
                //取yiled返回的子项
                var s = (yield cooker.cook(util.format('select * from users where id=%d',data[i].userId)))[0];
                arr[arr.length]=d;
            }
            res.send(arr);
        });
    });
});
/************************企业端首页开始**********************************/
// 首页-红包使用数量
router.post('/get_mch_rp_used',function(req,res,next){
    var mch_id=req.info.mchId;
    var sql=util.format("SELECT * FROM red_packets where mchId=%s and rowStatus=0 order by id desc",mch_id);
    var format=`
        SELECT name,ifnull(totalNum-remainNum,0) used,ifnull(remainNum,0) remain,ifnull(totalNum,0) total,limitType from red_packets
        where id=%d and levelType=0 and limitType=0 and rowStatus=0
        union select name,ifnull(totalAmount-remainAmount,0) used,ifnull(remainAmount,0) remain,ifnull(totalAmount,0) total,limitType from red_packets where id=%d and levelType=0 and limitType=1 and rowStatus=0
        union SELECT rp.name,ifnull(sum(sub.num)-sum(sub.remainNum),0) used,ifnull(sum(sub.remainNum),0) remain,ifnull(sum(num),0) total,0 limitType FROM red_packets_sub sub
        inner join red_packets rp on rp.id=sub.parentId where rp.id=%d and levelType=1 and rowStatus=0;
        `;
    cooker.serve(sql).then(function(data){
        //在co里，可以用yield保证顺序执行
        co(function *(){
            var arr=[];
            for(var i=0;i<data.length;i++){
                //yield后的语句，即then方法会执行，并保证语句顺序执行
                var result = yield cooker.serve(util.format(format,data[i].id,data[i].id,data[i].id));
                arr[i]=result[0];
            }
            res.send(arr);
        });
    });
});
// 首页-卡券使用数量
router.post('/get_mch_card_used',function(req,res,next){
    var mch_id=req.info.mchId;
    var format=`
            select a.title name,sum(b.totalNum) total,sum(b.remainNum) remain,(sum(b.totalNum)-sum(b.remainNum)) used from cards_group a
            left join cards b on b.parentId=a.id
            where a.mchId=%s and a.rowStatus=0 and b.rowStatus=0 group by a.id order by a.id desc,b.id desc;
            `;
    var sql=util.format(format,mch_id);
    cooker.serve(sql).then(function(data){
        res.send(data).end();
        return;
    });
});
// 首页-积分使用情况
router.post('/get_mch_point_used',function(req,res,next){
    var mch_id=req.info.mchId;
    var point_type=[
        {"table":"mall_orders","name":"积分商城"},
        {"table":"groups_scanpk_users","name":"扫码PK"},
        {"table":"user_trans","name":"红包兑换"}
    ];
    var format=`
        select *,ifnull((total-used),0) remain,"%s" name,"%s" table_model from
        (select ifnull(sum(a.amount),0) used,(select ifnull(sum(amount),0) total
        from user_points where mchId=%s and role=0) total
        from user_points_used a inner join user_trans b on a.doId=b.id where a.mchId=%s and a.doTable='%s' and a.role=0 and b.action=1 and b.wxStatus=1)m;
        `;
    co(function *(){
        var arr=[];
        for(var i=0;i<point_type.length;i++){
            //yield后的语句，即then方法会执行，并保证语句顺序执行
            var result = yield cooker.serve(util.format(format,point_type[i].name,point_type[i].table,mch_id,mch_id,point_type[i].table));
            arr[i]=result[0];
        }
        res.send(arr);
    });
});
// 首页-综合数据
router.post('/get_mch_indexdata',function(req,res,next){
    var mch_id=req.info.mchId;
    var sql=[];
    sql.push({"sql":'select round(sum(amount)/100,2) redNum from user_redpackets where mchId=%s and role=0;'});// 红包总额
    sql.push({"sql":'select count(*) cardNum from(select id from cards where mchId=%s and rowStatus=0 and totalNum!=remainNum group by parentId)m;'});// 卡券总额
    sql.push({"sql":'select sum(amount) pointNum from user_points where mchId=%s and role=0;'});// 积分总额
    sql.push({"sql":'select count(*) scanNum from scan_log where mchId=%s;'});// 扫码总量
    sql.push({"sql":'select sum(len) batchNum from batchs where mchId=%s and rowStatus=0 and state>0;'});// 扫码率
    /*******************/
    co(function *(){
        var arr=[];
        for(var i=0;i<sql.length;i++){
            //yield后的语句，即then方法会执行，并保证语句顺序执行
            var result = yield cooker.serve(util.format(sql[i].sql,mch_id));
            arr[i]=result[0];
        }
        res.send(arr);
    });
});
// 首页-新增人数（近30天）
router.post('/get_mch_user_xinzeng',function(req,res,next){
    var mch_id=req.info.mchId;
    var btime = new Date().getTime()/1000;
    var etime = new Date(Date.now() - (30 * 24 * 60 * 60 * 1000))/1000;
    var sql=util.format("select count(*) userNum,date(FROM_UNIXTIME(createTime)) date from users where mchId=%s and createTime>='%s' and createTime<='%s' group by date;",mch_id,etime,btime);
    cooker.serve(sql).then(function(data){
        var theDate=[];
        var userNum=[];
        for(i=0;i<data.length;i++){
            theDate.push(data[i].date);
            userNum.push(data[i].userNum);
        }
        res.send({"theDate":theDate,"userNum":userNum}).end();
        return;
    });
});

// 用户扫码统计-扫码详情
router.post('/scan_info_data',function(req,res){
    var mch_id=req.info.mchId;
    var param=JSON.parse(req.body.param);
    
    var whereClause=util.format("a.mchId=%s and a.scanTime>='%s' and a.scanTime<='%s' and a.userId=%s ",mch_id,param.start,param.end,param.uid);

    if(param.proCode!=0){
        whereClause += util.format("and concat(substring(d.areaCode,1,2),'0000')='%s' ", param.proCode);
    }
    if(param.cityCode!=0){
        whereClause += util.format("and concat(substring(d.areaCode,1,4),'00')='%s' ", param.cityCode);
    }
    if(param.areaCode!=0){
        whereClause += util.format("and d.areaCode='%s'  ", param.areaCode);
    }
    if (param.productid != 0) {
        whereClause += util.format("and b.productId='%s'  ", param.productid);
    }
    if (param.batchid != 0) {
        whereClause += util.format("and a.batchId='%s' ", param.batchid);
    }

    var sql=`select a.userId,c.nickName,FROM_UNIXTIME(a.scanTime) date,ifnull(e.fullName,'<span style="color:#ccc">终端不允许获取</span>') name,b.batchNo,a.code from %s a
        left join batchs b on a.batchId=b.id
        left join users c on a.userId=c.id
        left join geo_gps d on a.geoId=d.id
        left join areas e on d.areaCode=e.code
        where ${whereClause} order by date desc`;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql=util.format(sql, 'scan_log_waiters');
    }else{
        sql=util.format(sql, 'scan_log');
    }

    consoleSqlLog('用户扫码详情',sql);
    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});
//获取该次扫码所获得的奖励
router.post('/getRewards',function(req,res){
    var mch_id=req.info.mchId;
    var code=req.body.code;

    var whereClause=util.format("code='%s' and role=0 ",code);
    var sql=`select userId,amount,FROM_UNIXTIME(getTime) date,'red' level from user_redpackets where ${whereClause} 
UNION
select m.userId,cards.title amount,m.date,m.level from(select userId,cardId amount,FROM_UNIXTIME(getTime) date,'card' level from user_cards where ${whereClause})m inner join cards on m.amount=cards.id 
UNION
select userId,amount,FROM_UNIXTIME(getTime) date,'point' level from user_points where ${whereClause}`;

    consoleSqlLog('获取code获得的奖励',sql);
    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});
// 用户扫码统计-红包详情（这里的子活动名称可能需要不全）
router.post('/redpack_info_data',function(req,res){
    var mch_id=req.info.mchId;
    var param=JSON.parse(req.body.param);
    var whereClause=util.format("a.mchId=%s and a.userId=%s and a.getTime>='%s' and a.getTime<='%s' ",
        mch_id,param.uid,param.start,param.end);
    if (param.proCode != 0) {
        whereClause += util.format("and i.code = '%s' ", param.proCode);
    }
    if (param.cityCode != 0) {
        whereClause += util.format("and h.code = '%s' ", param.cityCode);
    }
    if (param.areaCode != 0) {
        whereClause += util.format("and g.code = '%s' ", param.areaCode);
    }
    if (param.productid != 0) {
        whereClause += util.format("and e.productId = '%s' ", param.productid);
    }
    if(param.batchid != 0) {
        whereClause += util.format("and c.batchId = '%s' ", param.batchid);
    }
    console.log(param);
    var sql=`select * from (
select ifnull(b.nickName,'欢乐扫用户') nickName,FROM_UNIXTIME(a.getTime) as date,round(ifnull(a.amount,0)/100,2) amount,d.name,a.userId,a.scanId,a.code,a.instId from user_redpackets a
        left join users b on b.id=a.userId
        left join %s c on a.code=c.code
        left join sub_activities d on d.id=c.activityId
        left join batchs e on c.batchId=e.id
        left join geo_gps f on f.id=c.geoId
        left join areas g on g.code=f.areaCode
        left join areas h on h.code=g.parentCode
        left join areas i on i.code=h.parentCode
        where ${whereClause} and a.code is not null
) m order by date desc `;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql=util.format(sql, 'scan_log_waiters');
    }else{
        sql=util.format(sql, 'scan_log');
    }
    consoleSqlLog('用户扫码统计-红包详情',sql);
    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});
// 用户扫码统计-提现详情
router.post('/trans_info_data',function(req,res){
    var mch_id=req.info.mchId;
    var params=JSON.parse(req.body.param);
    var whereClause=util.format("a.mchId=%s and a.userId=%s and a.theTime>='%s' and a.theTime<='%s' ",
        mch_id,params.uid,params.start,params.end);
    var sql=`select ifnull(b.nickName,'欢乐扫用户') nickName,FROM_UNIXTIME(a.theTime, '%Y-%m-%d %H:%i:%s') as date,round(ifnull(a.amount,0)/100,2) trans_amount,a.userId from user_trans a
                left join users b on b.id=a.userId where ${whereClause} and a.action=0 and a.wxStatus=1 order by a.theTime desc `;
    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});
// 用户扫码统计-卡券详情
router.post('/card_info_data',function(req,res){
    var mch_id=req.info.mchId;
    var param=JSON.parse(req.body.param);
    var whereClause=util.format("c.mchId=%s and a.userId=%s and a.getTime>='%s' and a.getTime<='%s' and a.sended=1 and a.transId=-1 and a.role=0 ",
        mch_id,param.uid,param.start,param.end);
    if (param.proCode != 0) {
        whereClause += util.format("and i.code = '%s' ", param.proCode);
    }
    if (param.cityCode != 0) {
        whereClause += util.format("and h.code = '%s' ", param.cityCode);
    }
    if (param.areaCode != 0) {
        whereClause += util.format("and g.code = '%s' ", param.areaCode);
    }
    if (param.productid != 0) {
        whereClause += util.format("and e.productId = '%s' ", param.productid);
    }
    if(param.batchid != 0) {
        whereClause += util.format("and c.batchId = '%s' ", param.batchid);
    }
    var sql=`select * from (
select m.userId,m.nickName,cards.title,name,FROM_UNIXTIME(m.getTime) as date,scanId,instId from(
        select a.userId userId,b.nickName,a.getTime,a.cardId,d.name,a.scanId,a.instId from user_cards a
        left join users b on b.id=a.userId
        left join %s c on a.code=c.code
        left join sub_activities d on d.id=c.activityId
        left join batchs e on c.batchId=e.id
        left join geo_gps f on c.geoId=f.id
        left join areas g on g.code=f.areaCode
        left join areas h on h.code=g.parentCode
        left join areas i on i.code=h.parentCode
        where ${whereClause} and a.code is not null)m inner join cards on m.cardId=cards.id
) m order by date desc `;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql=util.format(sql, 'scan_log_waiters');
    }else{
        sql=util.format(sql, 'scan_log');
    }

    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});
// 用户扫码统计-积分使用详情
router.post('/point_used_info_data',function(req,res){
    var mch_id=req.info.mchId;
    var params=JSON.parse(req.body.param);
    var whereClause=util.format("a.mchId=%s and a.userId=%s and a.createTime>='%s' and a.createTime<='%s' ",
        mch_id,params.uid,params.start,params.end);
    var sql=`select ifnull(b.nickName,'欢乐扫用户') nickName,FROM_UNIXTIME(a.createTime, '%Y-%m-%d %H:%i:%s') as date,ifnull(a.amount,0) pointUsed,a.userId from user_points_used a
                left join users b on b.id=a.userId 
                left join user_trans c on a.doId=c.id
                where ${whereClause} and c.wxStatus=1 and c.action=1 order by a.createTime desc `;
    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});
// 用户扫码统计-积分详情
router.post('/point_info_data',function(req,res){
    var mch_id=req.info.mchId;
    var param=JSON.parse(req.body.param);
    var whereClause=util.format("c.mchId=%s and a.userId=%s and a.getTime>='%s' and a.getTime<='%s' ",
        mch_id,param.uid,param.start,param.end);
    if (param.proCode != 0) {
        whereClause += util.format("and i.code = '%s' ", param.proCode);
    }
    if (param.cityCode != 0) {
        whereClause += util.format("and h.code = '%s' ", param.cityCode);
    }
    if (param.areaCode != 0) {
        whereClause += util.format("and g.code = '%s' ", param.areaCode);
    }
    if (param.productid != 0) {
        whereClause += util.format("and e.productId = '%s' ", param.productid);
    }
    if(param.batchid != 0) {
        whereClause += util.format("and c.batchId = '%s' ", param.batchid);
    }
    var sql=`select * from (
select ifnull(b.nickName,'欢乐扫用户') nickName,FROM_UNIXTIME(a.getTime) as date,ifnull(a.amount,0) amount,d.name,a.userId,a.scanId,a.code,a.instId from user_points a
        left join users b on b.id=a.userId
        left join %s c on a.code=c.code
        left join sub_activities d on d.id=c.activityId
        left join batchs e on c.batchId=e.id
        left join geo_gps f on f.id=c.geoId
        left join areas g on g.code=f.areaCode
        left join areas h on h.code=g.parentCode
        left join areas i on i.code=h.parentCode
        where ${whereClause} and a.code is not null
) m order by date desc `;

    //兼容服务员 tab=0 消费者 tab=1 服务员
    if(typeof param.tab!=='undefined' && param.tab==1){
        sql=util.format(sql, 'scan_log_waiters');
    }else{
        sql=util.format(sql, 'scan_log');
    }

    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});

// 乐券策略-中奖名单-中奖记录
router.post('/card_get_winlist',function(req,res){
    var cid=req.body.id;
    var mchId = req.info.mchId;
    var whereClause=util.format("a.cardId=%d and a.role=0 and a.transId=-1 ",cid);
    var sql=`select ifnull(u.nickName,'欢乐扫用户') nickName,u.realName,u.mobile,u.address,m.* from(
select a.userId,a.id as aid,a.processing as aprocessing,a.sended,FROM_UNIXTIME(a.getTime) as date,ifnull(g.address,'终端不允许获取') as area from user_cards a
left join scan_log s on a.code=s.code
left join geo_gps g on s.geoId=g.id
where ${whereClause}
)m left join users u on u.id=m.userId and u.mchId=%d order by date desc;`;
    sql=util.format(sql,mchId);
    cooker.serve(sql).then(function(data){
        res.json({data:data}).end();
        return;
    });
});

module.exports=reportingClass;

// 详情列表统一时间处理

var get_list_detailTime=function(req){
    var params=JSON.parse(req.body.param);
    var mchId = req.info.mchId;
    params.start=req.body.start;
    params.end=req.body.length;
    // 处理时间 （day->天 week->周）
    if(params.level=='day'){
        var btime=hlsUtil.get_unix_time(params.date+' 00:00:00');
        var etime=hlsUtil.get_unix_time(params.date+' 23:59:59');
    }
    if(params.level=='week'){
        var week= new Array();
        week=params.date.split("-");
        var span=new Date().getDateSpan(week[0],0,parseInt(week[1])+1,0);
        var btime=hlsUtil.get_unix_time(span.start.toDateString()+' 00:00:00');
        var etime=hlsUtil.get_unix_time(span.end.toDateString()+' 23:59:59');
    }

    return {'mchId':mchId,'userId':params.uid,'start':btime,'end':etime};
}

var get_down_title=function(param){
    var dTitle='';
    var dTime='';
    if(param['month']==0){//年份数据
        dTitle="月份";
        dTime=param['year']+'年';
    }
    if(param['week']==0&&!param['month']==0){//月份数据1-31天
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
    if(!param['week']==0){//具体周的数据
        dTitle="日期";
        dTime=param['year']+'第'+param['week']+'周数据';
    }
    if(param['day']!=null&&!param['day']==0){
        dTitle="日期";
        dTime=param['day']+'数据';
    }
    return {'title':dTitle,'time':dTime};
}

//控制台打印日志
var consoleSqlLog=function(title,sql){
    console.log('========================'+title+'开始==========');
    console.log(sql);
    console.log('========================'+title+'结束==========');
}
