module.exports = {

	test: function(params) {
		let sql = "select nickname, city, province, country from users limit 3";
		return global.mysqlPool.getConnection().then((conn) => {
            let params = {
                "sql": sql,
                "charset": "utf8mb4",
                "multipleStatements": true,
                "stringifyObjects": true,
                "supportBigNumbers": true,
            };
            var result = conn.query(params);
            conn.release();
            return result;
        }).then((result) => {
            return result[0];
        }).catch((err) => {
            if (typeof conn != 'undefined') {
                conn.release();
            }
            console.log(sql);
            console.log(err);
            throw new Error('DATABASE_EXEPTION');
        });
	},

    charts: function(params) {
        // 无参数，默认昨日数据
        var start = `unix_timestamp('`+ params.start +` 00:00:00')`;
        var end = `unix_timestamp('`+ params.end +` 23:59:59')`;
        if (params.chartsType == 'prices') {
            // 中奖数据
            var sql = `select 
                t1.getTime time, 
                (t1.amount / 100) amount, 
                t3.latitude, 
                t3.longitude, 
                t4.productName, 
                t5.fullName address,
                ifnull(t6.nickName, '未知昵称') nickname,
                t6.id userId,
                t7.name dealerName
                from hr_user_redpackets t1 
                left join scan_log      t2 on t2.rewardId   = t1.id and t2.rewardTable = 'hr_user_redpackets'
                left join hr_code       t3 on t3.code       = t1.code
                left join tts_orders    t4 on t4.id         = t3.orderId
                left join areas         t5 on t5.code       = t2.areaCode
                left join users         t6 on t6.id         = t1.userId
                left join hr_dealers    t7 on t7.id         = t1.dealerId
                where t1.mchId = `+ params.mchId +`
                and t1.getTime >= '`+ params.start +` 00:00:00' and t1.getTime <= '`+ params.end +` 23:59:59'`;
        } else if (params.chartsType == 'jlhrx') {
            var sql = `select * from (
                select 
                t1.code,
                t1.getTime time, 
                (t1.amount / 100) amount, 
                t3.latitude, 
                t3.longitude, 
                t4.productName, 
                t5.fullName address,
                ifnull(t6.nickName, '未知昵称') nickname,
                t6.id userId,
                t7.name dealerName
                from hr_user_redpackets t1 
                left join scan_log      t2 on t2.rewardId   = t1.id and t2.rewardTable = 'hr_user_redpackets'
                left join hr_code       t3 on t3.code       = t1.code
                left join tts_orders    t4 on t4.id         = t3.orderId
                left join areas         t5 on t5.code       = t2.areaCode
                left join users         t6 on t6.id         = t1.userId
                left join hr_dealers    t7 on t7.id         = t1.dealerId
                where t1.mchId = 5
                and t1.getTime >= '`+ params.start +` 00:00:00' and t1.getTime <= '`+ params.end +` 23:59:59'
                ) tmp1 where length(code) > 12`;
        } else if (params.chartsType == 'users') {
            // 扫码用户
            var sql = `select t2.openid, t1.code, t2.nickName, t2.sex, t2.country, t2.province, 
                t2.city, t2.language, t2.realName, t2.birthday, t2.address 
                from scan_log   t1 
                join users      t2 on t2.id = t1.userId where t1.mchId = 1 
                and t1.batchId = 0 and t1.activityId = 0 
                and t1.scanTime >= `+ start +` and t1.scanTime <= `+ end + ` limit 10000`;
        } else if (params.chartsType == 'count') {
            // 红包统计
            var sql = `select t1.date, t2.name, t2.areas, t1.redpacketCount, 
                t1.redpacketAmount / 100 'redpacketAmount', t1.withdrawAmount / 100 'withdrawAmount', 
                t1.withdrawAmount / 100 'withdrawAmount', t1.serviceAmount / 100 'serviceAmount',
                t2.balance/100 'balance'
                from hr_charts  t1 
                join hr_dealers t2 on t2.id = t1.dealerId 
                where dealerId > 1003 and date >= '`+ params.start +` 00:00:00' and date <= '`+ params.end +` 23:59:59' 
                and (t1.redpacketCount > 0 or t1.withdrawAmount > 0) order by date`;
        } else if (params.chartsType == 'balance') {
            // 经销商账户余额
            var sql = `select t1.code, t1.name, t1.areas, t1.balance / 100 AS balance
                from hr_dealers t1 where mchId = 5`;
        } else if (params.chartsType == 'jlhr') {
            // 吉林雪花对于的欢乐扫乐码中奖数据
            var sql = `select * from (
                select t4.code, t1.amount/100 as 'amount', t1.getTime as 'time', t2.name as 'name', 
                    t3.nickName as 'nickname', t5.fullName as 'address'
                from hr_user_redpackets t1 
                left join hr_dealers    t2 on t2.id = t1.dealerId
                left join users         t3 on t3.id = t1.userId
                left join scan_log      t4 on t4.code = t1.code and t4.mchId = 5 and t4.activityId = 0
                left join areas         t5 on t5.code = t4.areaCode
                where t1.mchId = 5 and t1.getTime >= `+ params.start +` and t1.getTime <= `+ params.end +` 
                and t1.code like '5%' limit 10000) tmp1 where length(code) = 12`;
        } else if (params.chartsType == 'jlhr_real') {
            var sql = `select t1.prizeName, t1.getTime as time, t1.code, 
                ifnull(t2.realName, '未填写') as realName, ifnull(t2.mobile, '未填写') as mobile, 
                ifnull(t2.address, '未填写') as address, 
                t2.nickname, t3.name as dealerName
                from hr_user_prize  t1 
                join users          t2 on t2.id = t1.userId
                join hr_dealers     t3 on t3.id = t1.dealerId`;
        } else {
            return new Promise(function(success, fail) {
                throw new Error('UNKNOW_CHARTS');
            });
        }
        
        return global.mysqlPool.getConnection().then((conn) => {
            let params = {
                "sql": sql,
                "charset": "utf8mb4",
                "multipleStatements": true,
                "stringifyObjects": true,
                "supportBigNumbers": true,
            };
            var result = conn.query(params);
            conn.release();
            return result;
        }).then((result) => {
            return result[0];
        }).catch((err) => {
            if (typeof conn != 'undefined') {
                conn.release();
            }
            console.log(sql);
            console.log(err);
            throw new Error('DATABASE_EXEPTION');
        });
    }
};