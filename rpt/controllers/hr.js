var router = require('express').Router();
var hrService = require('../services/hrService');
var iconv = require('iconv-lite');
var util = require('util');
var hlsUtil = require('../hls_util');

router.get('/test', function(req, res, next) {
    var params = req.query;

    hrService.test(params).then((resp) => {
    	res.json(resp);
    }).catch((err) => {
    	res.json({data: null, errmsg: err.message, errcode: 0});
    });
});

// router.use(require('../cookie'));

router.get('/charts/:chartsType', function(req, res) {
    var params = req.query;
    if (! params.mch_id) {
        return res.json({data: null, errmsg: '缺少下载参数', errcode: 1});
    }
    params.mchId = params.mch_id;
    params.chartsType = req.params.chartsType;
    if (! params.start) {
        params.start = new Date().addDays(-1).toDateString();
    }
    if (! params.end) {
        params.end = new Date().addDays(-1).toDateString();
    }
    hrService.charts(params).then((resp) => {
        try {
            var header, filename;
            if (      params.chartsType == 'prices') {
                filename = params.start.toString()+'-'+params.end.toString()+'(辽宁华润)中奖数据.csv';
                header = '用户,经销商,时间,红包金额,纬度,经度,产品名称,地区\n';
            } else if (params.chartsType == 'jlhrx') {
                filename = params.start.toString()+'-'+params.end.toString()+'(吉林华润)中奖数据.csv';
                header = '用户,经销商,时间,红包金额,纬度,经度,产品名称,地区\n';
            } else if (params.chartsType == 'users') {
                filename = params.start.toString()+'-'+params.end.toString()+'扫码数据.csv';
                header = '微信openid,码,用户昵称,性别,国家,省份,城市,语言,真实姓名,生日,地址\n';
            } else if (params.chartsType == 'count') {
                filename = params.start.toString()+'-'+params.end.toString()+'红包统计数据.csv';
                header = '日期,经销商,区域,红包发放个数,红包发放金额,用户提现金额,服务费金额,经销商余额\n';
            } else if (params.chartsType == 'balance') {
                filename = '经销商余额(查询时间：'+ new Date().format('yyyy年MM月dd日 hh时mm分') +').csv';
                header = '编码,经销商,区域,余额\n';
            } else if (params.chartsType == 'jlhr') {
                filename = '吉林欢乐扫乐码中奖(查询时间：'+ new Date().format('yyyy年MM月dd日 hh时mm分') +').csv';
                header = '乐码,中奖金额,中奖时间,经销商,用户昵称,区域\n';
            } else if (params.chartsType == 'jlhr_real') {
                filename = '吉林实物中奖(查询时间：'+ new Date().format('yyyy年MM月dd日 hh时mm分') +').csv';
                header = '暗码,奖品名称,中奖人,中奖时间,联系电话,收获地址,经销商,微信昵称\n';
            }
            filename = iconv.decode(iconv.encode(filename, 'utf-8'), 'ISO-8859-1');
            res.writeHead(200, {
                'Content-type' : 'application/octet-stream',
                'Content-Disposition':'attachment;filename=' + filename});
            res.write(iconv.encode(header, 'gbk'));
            let item;
            if (params.chartsType == 'prices') {
                for (let i = 0; i < resp.length; i++) {
                    item = resp[i];
                    res.write(iconv.encode('\t' + 
                        item.nickname.replace(/,/g, '_')     + ',' + 
                        item.dealerName + ',' + 
                        new Date(item.time).toLocaleString().replace(',', ' ') + ',' + 
                        item.amount     + ',' + 
                        item.latitude   + ',' + 
                        item.longitude  + ',' + 
                        item.productName+ ',' + 
                        item.address + '\n', 'gbk'));
                } 
            } else if (params.chartsType == 'jlhr' || params.chartsType == 'jlhrx') {
                for (let i = 0; i < resp.length; i++) {
                    item = resp[i];
                    res.write(iconv.encode('\t' + 
                        item.nickname.replace(/,/g, '_')     + ',' + 
                        item.dealerName + ',' + 
                        new Date(item.time).toLocaleString().replace(',', ' ') + ',' + 
                        item.amount     + ',' + 
                        item.latitude   + ',' + 
                        item.longitude  + ',' + 
                        item.productName+ ',' + 
                        item.address + '\n', 'gbk'));
                } 
            } else if (params.chartsType == 'users') {
                for (let i = 0; i < resp.length; i++) {
                    item = resp[i];
                    res.write(iconv.encode('\t' + 
                        item.openid     + ',' + 
                        item.code       + ',' + 
                        item.nickName.replace(/,/g, '_')     + ',' + 
                        item.sex        + ',' + 
                        item.country    + ',' + 
                        item.province   + ',' + 
                        item.city       + ',' + 
                        item.language   + ',' + 
                        item.realName   + ',' + 
                        item.birthday   + ',' + 
                        item.address    + '\n', 'gbk'));
                } 
            } else if (params.chartsType == 'count') {
                for (let i = 0; i < resp.length; i++) {
                    item = resp[i];
                    res.write(iconv.encode('\t' + 
                        item.date.format('yyyy-MM-dd') +  ',' + 
                        item.name               + ',' + 
                        item.areas              + ',' + 
                        item.redpacketCount     + ',' + 
                        item.redpacketAmount    + ',' + 
                        item.withdrawAmount     + ',' + 
                        item.serviceAmount      + ',' + 
                        item.balance            + 
                        '\n', 'gbk'));
                } 
            } else if (params.chartsType == 'balance') {
                for (let i = 0; i < resp.length; i++) {
                    item = resp[i];
                    res.write(iconv.encode('\t' + 
                        item.code       + ',' + 
                        item.name       + ',' + 
                        item.areas      + ',' + 
                        item.balance    + ',' + 
                        '\n', 'gbk'));
                } 
            } else if (params.chartsType == 'jlhr_real') {
                for (let i = 0; i < resp.length; i++) {
                    item = resp[i];
                    res.write(iconv.encode('\t' + 
                        item.code       + ',' + 
                        item.prizeName  + ',' + 
                        item.realName   + ',' + 
                        item.time.format('yyyy-MM-dd hh:mm:ss') + ',' + 
                        item.mobile     + ',' + 
                        item.address    + ',' + 
                        item.dealerName + ',' + 
                        item.nickname.replace(/,/g, '_')     + ',' + 
                        '\n', 'gbk'));
                } 
            }
            res.end();
        } catch (e) {
            console.error(e);
        }
    }).catch((err) => {
        res.json({data: null, errmsg: err.message, errcode: 1});
    });
});

module.exports = router;