var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');
var encoder = require('../untilities/encoder');
var config = require('../untilities/config');

router.post('/getall', function(req, res, next) {
    
    var config={
        productName:{
            desc:'产品名称',
            length:[1,45],
            required:false
        },
        createTime:{
            desc:'创建时间',
            required:false
        },
        batchNo:{
            desc:'批号',
            length:[1,45],
            required:false
        }
    };
    var result=api.getInput(req.body, config);
    if(result!=null){
        res.json(result);
        return;
    }
    
    if(req.body.createTime!=null && typeof req.body.createTime=='object'){
        config={
            from:{
                desc:'生产时间',
                length:[1,11],
                numeric:true,
                required:false
            },
            to:{
                desc:'生产时间',
                length:[1,11],
                numeric:true,
                required:false
            }
        };
        result=api.getInput(req.body.createTime, config);
        if(result!=null){
            res.json(result);
            return;
        }
    }
    
    var params=[req.appinfo.mchid];
    var condition='';
    if(req.body.productName!=null){
        condition+=" and p.name like ?";
        params.push('%'+req.body.productName+'%');
    }
    if(req.body.createTime!=null){
        if(req.body.createTime.from!=null){
            condition+=' and b.createTime>=?';
            params.push(req.body.createTime.from);
        }
        if(req.body.createTime.to!=null){
            condition+=' and b.createTime<=?';
            params.push(req.body.createTime.to);
        }
    }
    if(req.body.batchNo!=null){
        condition+=" and b.batchNo like ?";
        params.push('%'+req.body.batchNo+'%');
    }
    
    var sql='select batchNo,versionNum version,state,start,end,len,productId,p.name productName,\
        b.categoryId,b.createTime,activeTime,expireTime,ifPubCode,isDownloaded from batchs b\
        left join products p on b.productId=p.id where b.mchid=? and rowStatus=0'+condition;
    db.query(sql,params,function(batchs){
        if(batchs.length==0){
            res.json(api.setOutput(2,'没有数据'));
            return;
        }
        res.json(api.setOutput(0,{data:batchs}));
    });
});

router.post('/get(/url)?/:batchno/:ifpubcode?', function(req, res, next) {

    var sql='select versionNum version,start,end from batchs where mchid=? and batchno=? and rowStatus=0';
    db.query(sql,[req.appinfo.mchid,req.params.batchno],function(batchs){
        if(batchs.length==0){
            res.send('');
            return;
        }
        
        sql='select code from merchants where id=?';
        db.query(sql,[req.appinfo.mchid],function(rows){
            if(rows.length==0){
                res.json(api.setOutput(2,'商户信息缺失'));
                return;
            }
            var codeParams={};
            codeParams.code=rows[0].code;
            codeParams.version=batchs[0].version;
            sql='select mchcodelen,seriallen,validlen from code_version where versionnum=?';
            db.query(sql,[codeParams.version],function(rows){
                if(rows.length==0){
                    res.json(api.setOutput(2,'码版本信息缺失'));
                    return;
                }
                codeParams.mchcodelen=rows[0].mchcodelen;
                codeParams.seriallen=rows[0].seriallen;
                codeParams.validlen=rows[0].validlen;
                
                var codeOutput=function(values){
                    var privateCodes=encoder.privateCode(values, codeParams);
                    var publicCodes=null;
                    if(req.params.ifpubcode=='1'){
                        publicCodes=encoder.publicCode(values, codeParams);
                    }
                    var prefix=req.params[0]!=null?config.getConfig().code_prefix:'';
                    if(codeParams.version=='4')
                        prefix=prefix.toUpperCase();
                    if(publicCodes==null){
                        for(var i=0;i<values.length;i++){
                            var line = (i==values.length-1)?'':'\n';
                            res.write(prefix+privateCodes[i]+line);
                        }
                    }
                    else{
                        for(var i=0;i<values.length;i++){
                            var line = (i==values.length-1)?'':'\n';
                            res.write(prefix+privateCodes[i]+','+publicCodes[i]+line);
                        }
                    }
                };
                
                var values=[];
                var count=0;
                var bufferCount=500000;
                for(var i=parseInt(batchs[0].start);i<=parseInt(batchs[0].end);i++){
                    values.push(i);
                    count++;
                    if(count==bufferCount){
                        codeOutput(values);
                        values=[];
                        count=0;
                        if(i!=parseInt(batchs[0].end)){
                            res.write('\n');
                        }
                    }
                }
                if(count!=0){
                    codeOutput(values);
                }
                res.end();
                
                sql='update batchs set isDownloaded=1 where id=?';
                db.query(sql,[batchs[0].id]);
            });
        });
    });
});

module.exports = router;
