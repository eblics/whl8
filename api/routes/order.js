var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');
var background = require('../untilities/background');
var encoder = require('../untilities/encoder');
var corouter = require('../untilities/corouter')(router);
var envConfig = require('../untilities/config').getConfig();
var _module = require('../modules/order.module');

corouter.post('/put(/sync)?', function *(req, res, next) {
    var config={
        orderno:{
            desc:'订单编号',
            length:[1,100]
        },
        ordertype:{
            desc:'生产入库',
            options:['produce','in','out']
        },
        codes:{
            desc:'相关码'
        },
        ifpubcode:{
            desc:'是否明码',
            boolean:true,
            required:false
        }
    };
    var result=api.getInput(req.body, config);
    if(result!=null){
        res.json(result);
        return;
    }
    var mchid=req.appinfo.mchid;
    var orderno=req.body.orderno;
    var ordertype=req.body.ordertype;
    var codes=req.body.codes;
    var ifpubcode=req.body.ifpubcode;
    var isSync=req.params[0]!=null?true:false;
    var orderId;
    
    var sql = 'select id,orderType from tts_orders where orderNo=? and mchid=?';
    var rows=yield db.querySync(sql,[orderno,mchid]);

    if(rows.length==0){
        var result;
        
        if(ordertype=='produce'){
            result=yield _module.produceOrder(req.body,mchid);
        }
        else if(ordertype=='in'){
            result=yield _module.inOrder(req.body,mchid);
        }
        else{
            result=yield _module.outOrder(req.body,mchid);
        }
        if(result.errcode!=0){
            res.json(result);
            return;
        }
        orderId=result.orderid;
    }
    else if(rows[0].orderType!=ordertype){
        res.json(api.setOutput(2,'入库类型不匹配'));
        return;
    }
    else{
        orderId=rows[0].id;
        //res.json(api.setOutput(2,'订单编号不能重复'));return;
    }
    
    if(!isSync){
        res.json(api.setOutput(0,{processid:orderId}));
    }
    
    //连接不会超时
    res.setTimeout(0);
    
    var rangeSql='select min(start) min,max(end) max from batchs where mchid=?';
    var batchs=yield db.querySync(rangeSql,[mchid]);
    
    if(batchs.length==0){
        var result=yield _module.completeAction(['码不在有效范围内'],orderId,mchid,isSync);
        if(result!=null)
            res.json(result);
        return;
    }

    var sql='select versionnum version,mchcodelen,seriallen,validlen,offsetlen from code_version';
    var codeVersions=yield db.querySync(sql);
    var codeVer=req.appinfo;
    
    var resultCodes;
    if(ifpubcode===false){
        resultCodes=_module.privateCodeProcess(codes, codeVersions);
    }
    else{
        resultCodes=_module.publicCodeProcess(codes, codeVer);
    }
    
    var sqlList=_module.getSqlList(resultCodes,orderId,batchs[0],ifpubcode);
    yield background.runSync(sqlList);
    var result=yield _module.completeAction(resultCodes.errMsgs,orderId,mchid,isSync);
    if(result!=null)
        res.json(result);
});

router.post('/process/query/:processid', function(req, res, next) {
    var processid=req.params.processid;
    var mchid=req.appinfo.mchid;
    var sql='select processStatus,errmsg from tts_orders where mchid=? and id=?';
    db.query(sql,[mchid,processid],function(orders){
        if(orders.length==0){
            res.json(api.setOutput(2,'没有数据'));
            return;
        }
        var result=orders[0];
        res.json(api.setOutput(0,{processstatus:result.processStatus,codeerrmsg:result.errmsg}));
    });
});

router.get('/get/:processid', function(req, res, next) {
    var processid=req.params.processid;
    //tts_orders_codes表没有mchid字段
    var sql='select pubCode from tts_orders_codes where orderId=?';
    db.query(sql,[processid],function(rows){
        res.setHeader('Content-Type','application/octet-stream');
        res.setHeader('Content-Disposition','attachment;filename=code'+processid+'.txt');
        for(var i=0;i<rows.length;i++){
            res.write(rows[i].pubCode+'\r\n');
        }
        res.end();
    });
});

module.exports = router;
