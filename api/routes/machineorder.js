var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');
var background = require('../untilities/background');
var encoder = require('../untilities/encoder');
var envConfig = require('../untilities/config').getConfig();

router.post('/put(/sync)?/:mchid', function(req, res, next) {

    var config={
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
    var mchid=req.params.mchid;
    var ordertype=req.body.ordertype;
    var codes=req.body.codes;
    var ifpubcode=req.body.ifpubcode;
    
    var insertCode=function(orderId){
        if(req.params[0]==null){
            res.json(api.setOutput(0,{processid:orderId}));
        }
        
        res.setTimeout(0);
        
        var errMsgs = [];
        var subCodes = [];
        
        var existUnknownChar=function(str,chars){
            for(var i=0;i<str.length;i++){
                if(chars.indexOf(str[i])==-1){
                    return true;
                }
            }
            return false;
        };
        
        var rangeSql='select min(start) min,max(end) max from batchs where mchid=?';
        db.query(rangeSql,[mchid],function(rows){
            var completeAction=function(){
                var errmsg='';
                if(errMsgs.length!=0)
                    errmsg=errMsgs.join('\r\n')+'\r\n';
                var scanSql='update tts_orders_codes c join scan_log s on c.code=s.code set c.isScan=1 where c.orderId=?';
                db.query(scanSql,[orderId],function(){
                    var comSql='update tts_orders set processStatus=1,errmsg=CONCAT(IFNULL(errmsg,""),?) where mchid=? and id=?';
                    db.query(comSql,[errmsg,mchid,orderId]);
                    if(req.params[0]!=null){
                        res.json(api.setOutput(0,{codeerrmsg:errmsg,processid:orderId}));
                    }
                });
            };
            
            if(rows.length==0){
                errMsgs.push('码不在有效范围内');
                completeAction();
                return;
            }
            
            var insertAction=function(codeVersions){
                var numbers=[];
                var enCodeList={};
                var codeVer=req.appinfo;
                
                codes.forEach(function(value){
                    var code=value.trim();
                    if(code.length==0){
                        return;
                    }
                    if(ifpubcode===false){
                        codeVer=[];
                        
                        for(var i=0;i<codeVersions.length;i++){
                            if(code[0]==codeVersions[i].version){
                                codeVer=codeVersions[i];
                                break;
                            }
                        }
                        if(codeVer.length==0){
                            errMsgs.push(code+'码版本不存在');
                        }
                        else if(code.length!=codeVer.version.length+codeVer.mchcodelen+codeVer.seriallen+codeVer.validlen+codeVer.offsetlen){
                            errMsgs.push(code+'长度不符');
                        }
                        else if(!/^[0-9A-Za-z]+$/.test(code)){
                            errMsgs.push(code+'包含非法字符');
                        }
                        else{
                            if(!enCodeList.hasOwnProperty('_'+codeVer.version)){
                                enCodeList['_'+codeVer.version]={codes:[],version:{version:codeVer.version,mchcodelen:codeVer.mchcodelen,
                                        seriallen:codeVer.seriallen,validlen:codeVer.validlen}};
                            }
                            enCodeList['_'+codeVer.version].codes.push(code);
                        }
                    }
                    else{
                        if(code.length!=envConfig.code.length){
                            errMsgs.push(code+'长度不符');
                        }
                        else if(existUnknownChar(code,envConfig.code.char32set)){
                            errMsgs.push(code+'包含非法字符');
                        }
                        else{
                            subCodes.push(code);
                        }
                    }
                });
                
                var enCodes=[];
                if(ifpubcode===false){
                    for(name in enCodeList){
                        var resultCodes=encoder.decodeAndNumber(enCodeList[name].codes,enCodeList[name].version);
                        for(var i=0;i<enCodeList[name].codes.length;i++){
                            enCodes.push(enCodeList[name].codes[i]);
                            var values=resultCodes[i].split(',');
                            numbers.push(parseInt(values[1]));
                            subCodes.push(values[0]);
                        }
                    }
                }
                else{
                    var resultCodes=encoder.encodeAndNumber(subCodes,codeVer);
                    resultCodes.forEach(function(value){
                        var values=value.split(',');
                        numbers.push(parseInt(values[1]));
                        enCodes.push(values[0]);
                    });
                }
                var bufferCount = 50000;
                var count = 1;
                
                var baseSql = 'insert ignore into tts_orders_codes(code,pubcode,orderid) values';
                var sql = '';
                var min = parseInt(rows[0].min);
                var max = parseInt(rows[0].max);
                var sqlList=[];
                
                for(var i=0;i<enCodes.length;i++){
                    var codeNum=numbers[i];
                    if(codeNum<min||codeNum>max){
                        errMsgs.push((ifpubcode===false?enCodes[i]:subCodes[i])+'不在有效范围内');
                        continue;
                    }
                    
                    if(sql.length==0){
                        sql=baseSql;
                    }
                    else{
                        sql+=',';
                    }
                    sql+="("+db.escape(enCodes[i])+","+db.escape(subCodes[i])+","+orderId+")";
                    
                    if (count % bufferCount == 0) {                        
                        sqlList.push(sql);
                        
                        count=0;
                        sql='';
                    }
                    count++;
                }
                
                if(sql.length!=0){
                    sqlList.push(sql);
                }

                background.run(sqlList,function(){
                    completeAction();
                });
            };
            
            if(ifpubcode===false){
                var sql='select versionnum version,mchcodelen,seriallen,validlen,offsetlen from code_version';
                db.query(sql,function(rows){
                    insertAction(rows);
                });
            }
            else{
                insertAction();
            }
        });
    };
    
    var existOrderno=function(orderno,callback){
        getCodeVersion(function(){
            var sql = 'select id,orderType from tts_orders where orderNo=? and mchid=?';
            db.query(sql,[orderno,mchid],function(rows){
                req.body.processStatus=0;
                if(rows.length==0){
                    callback();
                }
                else if(rows[0].orderType!=ordertype){
                    res.json(api.setOutput(2,'入库类型不匹配'));
                }
                else{
                    insertCode(rows[0].id);
                    //res.json(api.setOutput(2,'订单编号不能重复'));
                }
            });
        });
    };
    
    var getCodeVersion=function(callback){
        var sql='select code,codeversion from merchants where id=?';
        db.query(sql,[mchid],function(rows){
            if(rows.length==0){
                res.json(api.setOutput(2,'商户信息缺失'));
                return;
            }
            var codeParams={};
            codeParams.code=rows[0].code;
            codeParams.version=rows[0].codeversion;
            sql='select mchcodelen,seriallen,validlen,offsetlen from code_version where versionnum=?';
            db.query(sql,[codeParams.version],function(rows){
                if(rows.length==0){
                    res.json(api.setOutput(2,'码版本信息缺失'));
                    return;
                }
                codeParams.mchcodelen=rows[0].mchcodelen;
                codeParams.seriallen=rows[0].seriallen;
                codeParams.validlen=rows[0].validlen;
                codeParams.offsetlen=rows[0].offsetlen;
                
                req.appinfo=codeParams;
                callback();
            });
        });
    };
    
    if(ordertype=='produce'){
        config={
            orderno:{
                desc:'订单编号',
                length:[1,100]
            },
            ordertype:{
                desc:'生产入库'
            },
            productcode:{
                desc:'产品编码',
                length:[1,100]
            },
            productname:{
                desc:'产品名称',
                length:[1,100]
            },
            factorycode:{
                desc:'生产工厂编码',
                length:[1,10]
            },
            factoryname:{
                desc:'生产工厂名称',
                length:[1,200]
            },
            producetime:{
                desc:'生产时间',
                length:[1,11],
                numeric:true,
                required:false
            },
            time:{
                desc:'入库时间',
                length:[1,11],
                numeric:true
            },
            shelflife:{
                desc:'保质期',
                length:[1,10],
                regexp:/^(?:\d+[dwmy])+$/,
                required:false
            },
            expiretime:{
                desc:'过期时间',
                length:[1,11],
                numeric:true,
                required:false
            }
        };
        var result=api.getInput(req.body, config, true);
        if(result!=null){
            res.json(result);
            return;
        }
        
        existOrderno(req.body.orderno,function(){
            req.body.mchid=mchid;
            req.body.ordertime=req.body.time;
            delete req.body.time;
            if(req.body.producetime==null){
                req.body.producetime=req.body.ordertime;
            }
            if(req.body.expiretime==null){
                if(req.body.shelflife==null){
                    res.json(api.setOutput(2,'缺少'+config.shelflife.desc+'或'+config.expiretime.desc+'字段'));
                    return;
                }
                var changeDate=function(date,type,num){
                    switch (type) {
                        case 'd':
                            date.setDate(date.getDate()+num);
                            break;
                        case 'w':
                            date.setDate(date.getDate()+7*num);
                            break;
                        case 'm':
                            date.setMonth(date.getMonth()+num);
                            break;
                        case 'y':
                            date.setFullYear(date.getFullYear()+num);
                            break;
                    }
                };
                var date=new Date(parseInt(req.body.ordertime)*1000);
                req.body.shelflife.match(/\d+[dwmy]/g).forEach(function(group){
                    var num=parseInt(group.slice(0,-1));
                    var type=group[group.length-1];
                    changeDate(date,type,num);
                });
                req.body.expiretime=Math.round(date.getTime()/1000);
                
                req.body.shelflifeStr=req.body.shelflife;
                delete req.body.shelflife;
            }
            else{
                delete req.body.shelflife;
            }
            
            db.insert('tts_orders',req.body,{puttime:'unix_timestamp()'},function(id){
                insertCode(id);
            });
        });
    }
    else if(ordertype=='in'){
        config={
            orderno:{
                desc:'订单编号',
                length:[1,100]
            },
            ordertype:{
                desc:'生产入库'
            },
            productcode:{
                desc:'产品编码',
                length:[1,100]
            },
            productname:{
                desc:'产品名称',
                length:[1,100]
            },
            time:{
                desc:'入库时间',
                length:[1,11],
                numeric:true
            }
        };
        var result=api.getInput(req.body, config, true);
        if(result!=null){
            res.json(result);
            return;
        }
        
        existOrderno(req.body.orderno,function(){
            req.body.mchid=mchid;
            req.body.ordertime=req.body.time;
            delete req.body.time;
            db.insert('tts_orders',req.body,{puttime:'unix_timestamp()'},function(id){
                insertCode(id);
            });
        });
    }
    else{
        config={
            orderno:{
                desc:'订单编号',
                length:[1,100]
            },
            ordertype:{
                desc:'生产入库'
            },
            productcode:{
                desc:'产品编码',
                length:[1,100]
            },
            productname:{
                desc:'产品名称',
                length:[1,100]
            },
            saletocode:{
                desc:'销往客户编码',
                length:[1,100]
            },
            saletoname:{
                desc:'客户名称',
                length:[1,200]
            },
            saletoagc:{
                desc:'销往区域的行政区域编',
                length:[1,10]
            },
            time:{
                desc:'入库时间',
                length:[1,11],
                numeric:true
            }
        };
        
        var result=api.getInput(req.body, config, true);
        if(result!=null){
            res.json(result);
            return;
        }
        
        existOrderno(req.body.orderno,function(){
            req.body.mchid=mchid;
            req.body.ordertime=req.body.time;
            delete req.body.time;
            db.insert('tts_orders',req.body,{puttime:'unix_timestamp()'},function(id){
                insertCode(id);
            });
        });
    }
});

router.post('/process/query/:processid', function(req, res, next) {
    var processid=req.params.processid;
    var sql='select processStatus,errmsg from tts_orders where id=?';
    db.query(sql,[processid],function(orders){
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
