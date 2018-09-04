var co 			= require('co');
var util 		= require('util');
var router 		= require('express').Router();
var bodyParser 	= require('body-parser');
var hlsUtil 	= require('./hls_util');

router.use(bodyParser.urlencoded({extended: true}));
router.use(bodyParser.json());
router.use(require('./cookie'));

var cooker, session, maxDays = 180;

// 模块构造函数
var mainClass = function(params) {
	cooker = params.cooker;
	session = params.session;
	return router;
};

module.exports = mainClass;


// api - begin
// ------------------------------
// path /estimate/charts_data
router.get('/charts_data', function(req, res, next) {
    var mchId = req.info.mchId;
    var activityIds = req.query.activity_ids_str;
    var startTime = req.query.start_time;
    var endTime = req.query.end_time;
    var tab = req.query.tab;

    // res.json(hlsUtil.ajaxResponseFail('报表维护中'));

    if (typeof activityIds === 'undefined' || activityIds === '') {
        res.json(hlsUtil.ajaxResponseFail('请选择要查询的活动'));
        return;
    }
    if (typeof startTime === 'undefined' || startTime === '') {
        startTime = new Date().addDays(-maxDays).toDateString();
    }
    if (typeof endTime === 'undefined' || endTime === '') {
        endTime = new Date().toDateString();
    }
    if ((new Date(endTime).getTime() - new Date(startTime).getTime()) > (maxDays * 24 * 60 * 60 * 1000)) {
        res.json(hlsUtil.ajaxResponseFail('最多只能查询' + maxDays + '天的数据'));
    }

    var params = {
        "activity_ids": "(" + activityIds + ")",
        "start_time": startTime,
        "end_time": endTime,
        "tab":tab
    };

    co(function*() {
        var activityEvaluatingArr = yield Service.getRptActivityEvaluating(mchId, params);
        var activityLogArr = yield Service.getActivityLog(mchId, params);
        // var withdrawAmountArr = yield Service.getWithdrawAmount(mchId, params);
        var scanUserArr = yield Service.getScanUserNum(mchId, params);
        var userRptArr = yield Service.getRptNum(mchId, params);
        var respData = {
            activity_logs: activityLogArr, 
            activity_evaluatings: activityEvaluatingArr,
            withdraw_amount: 0, // withdrawAmountArr[0].trans_amount,
            scan_users: scanUserArr,
            rpt_rows: userRptArr,
        };
        res.json(hlsUtil.ajaxResponseSuccess(respData));
    });
    
});

// service - begin
// ------------------------------
var Service = {

    /**
     * 获取活动策略的变更记录
     *
     * @param {Number} mchId 企业编号
     * @param {object} params {
     *   activity_id: 活动编号
     *   category_id: 产品分类编号
     *   product_id: 产品编号
     *   batch_id: 乐码批次编号
     *   start_time: 查询开始时间
     *   end_time: 查询结束时间
     * }
     * @return promise
     */
    getActivityLog: function(mchId, params) {
        let sql = `SELECT * FROM activity_log WHERE mchId = %d AND activityId IN %s AND theTime >= '%s' AND theTime <= '%s' ORDER BY theTime`;
        sql = util.format(sql, mchId, params.activity_ids, params.start_time, params.end_time);
        consoleSqlLog('获取活动策略的变更记录',sql);
        return cooker.cook(sql);
    },

    /**
     * 获取活动评估数据
     *
     * @param {Number} mchId 企业编号
     * @param {object} params {
     *   activity_id: 活动编号
     *   start_time: 查询开始时间
     *   end_time: 查询结束时间
     * }
     * @return promise
     */
    getRptActivityEvaluating: function(mchId, params) {
        let sql = `SELECT theDate, sum(rpAmount) AS redNum, sum(scanCount) AS scanNum 
            FROM %s WHERE mchId = %d AND activityId IN %s AND theDate >= '%s' AND theDate <= '%s' `;
        //兼容服务员 tab=0 消费者 tab=1 服务员
        if(typeof params.tab!=='undefined' && params.tab==1){
            sql=util.format(sql, 'rpt_waiter_activity_evaluating');
        }else{
            sql=util.format(sql, 'rpt_activity_evaluating');
        }
        sql = util.format(sql, mchId, params.activity_ids, params.start_time, params.end_time);
        sql += `GROUP BY theDate`;
        consoleSqlLog('获取活动评估数据',sql);
        return cooker.cook(sql);
    },

    /**
     * 获取用户提现金额
     * 
     * @param {Number} mchId 企业编号
     * @param {object} params {
     *   start_time: 查询开始时间
     *   end_time: 查询结束时间
     * }
     * @return promise
     */
    getWithdrawAmount: function(mchId, params) {
        let sql = `SELECT SUM(transAmount) AS trans_amount
            FROM %s WHERE mchId = %d AND theDate >= '%s' AND theDate <= '%s'`;
        //兼容服务员 tab=0 消费者 tab=1 服务员
        if(typeof params.tab!=='undefined' && params.tab==1){
            sql=util.format(sql, 'rpt_waiter_daily');
        }else{
            sql=util.format(sql, 'rpt_user_daily');
        }
        sql = util.format(sql, mchId, params.start_time, params.end_time);
        consoleSqlLog('获取用户提现金额',sql);
        return cooker.cook(sql);
    },

    /**
     * 获取扫码人数
     * 
     * @param {Number} mchId 企业编号
     * @param {object} params {
     *   activity_id: 活动编号
     *   start_time: 查询开始时间
     *   end_time: 查询结束时间
     * }
     * @return promise
     */
    getScanUserNum: function(mchId, params) {
        let sql = `SELECT distinct userId scan_user_num FROM %s t1 
            WHERE t1.mchId = %d AND t1.activityId IN %s AND t1.theDate >= '%s' AND t1.theDate <= '%s' `;
        //兼容服务员 tab=0 消费者 tab=1 服务员
        if(typeof params.tab!=='undefined' && params.tab==1){
            sql=util.format(sql, 'rpt_waiter_activity_evaluating');
        }else{
            sql=util.format(sql, 'rpt_activity_evaluating');
        }
        sql = util.format(sql, mchId, params.activity_ids, params.start_time, params.end_time);
        sql += `GROUP BY userId`;
        consoleSqlLog('获取扫码人数',sql);
        return cooker.cook(sql);
    },

    /**
     * 获取红包发放数量
     * 
     * @param {Number} mchId 企业编号
     * @param {object} params {
     *   activity_id: 活动编号
     *   start_time: 查询开始时间
     *   end_time: 查询结束时间
     * }
     * @return promise
     */
    getRptNum: function(mchId, params) {
        let sql = `SELECT ifnull(SUM(rpNum), 0) rpt_num FROM %s t1 
            WHERE t1.mchId = %d AND t1.activityId IN %s AND t1.theDate >= '%s' AND t1.theDate <= '%s' `;
        //兼容服务员 tab=0 消费者 tab=1 服务员
        if(typeof params.tab!=='undefined' && params.tab==1){
            sql=util.format(sql, 'rpt_waiter_activity_evaluating');
        }else{
            sql=util.format(sql, 'rpt_activity_evaluating');
        }
        sql = util.format(sql, mchId, params.activity_ids, params.start_time, params.end_time);
        consoleSqlLog('获取红包发放数量',sql);
        return cooker.cook(sql);
    }
};

//控制台打印日志
var consoleSqlLog=function(title,sql){
    // console.log('========================'+title+'开始==========');
    // console.log(sql);
    // console.log('========================'+title+'结束==========');
}