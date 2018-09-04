var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');
var encoder = require('../untilities/encoder');
var config = require('../untilities/config');

router.post('/getmerchants', function(req, res, next) {

    var sql='select id,codeVersion version,code,name from merchants \
        where codeVersion is not null and code is not null and id!=-1';
    db.query(sql,[],function(batchs){
        if(batchs.length==0){
            res.json(api.setOutput(2,'没有数据'));
            return;
        }
        res.json(api.setOutput(0,{data:batchs}));
    });
});

router.post('/getall', function(req, res, next) {
    
    var config={
        createTime:{
            desc:'创建时间',
            required:false
        },
        batchNo:{
            desc:'批号',
            length:[1,45],
            required:false
        },
        mchId:{
            desc:'企业号',
            length:[1,11],
            numeric:true,
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
    
    var params=[];
    var condition='';
    if(req.body.mchId!=null){
        condition+=" and b.mchid=?";
        params.push(req.body.mchId);
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
    
    var sql='select mchId,batchNo,versionNum version,state,start,end,len,productId,\
        categoryId,createTime,activeTime,expireTime,ifPubCode,isDownloaded from batchs b where rowStatus=0'+condition;
    db.query(sql,params,function(batchs){
        if(batchs.length==0){
            res.json(api.setOutput(2,'没有数据'));
            return;
        }
        res.json(api.setOutput(0,{data:batchs}));
    });
});

router.post('/get(/url)?/:mchid/:batchno/:ifpubcode?', function(req, res, next) {

    var sql='select id,versionNum version,start,end from batchs where mchid=? and batchno=? and rowStatus=0';
    db.query(sql,[req.params.mchid,req.params.batchno],function(batchs){
        if(batchs.length==0){
            res.send('');
            return;
        }
        
        sql='select code from merchants where id=?';
        db.query(sql,[req.params.mchid],function(rows){
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
                
                var values=[];
                for(var i=parseInt(batchs[0].start);i<=parseInt(batchs[0].end);i++){
                    values.push(i);
                }
                var privateCodes=encoder.privateCode(values, codeParams);
                var publicCodes=null;
                if(req.params.ifpubcode=='1'){
                    publicCodes=encoder.publicCode(values, codeParams);
                }
                var result='';
                var prefix=req.params[0]!=null?config.getConfig().code_prefix:'';
                if(codeParams.version=='4')
                    prefix=prefix.toUpperCase();
                if(publicCodes==null){
                    for(var i=0;i<values.length;i++){
                        result+=prefix+privateCodes[i]+'\n';
                    }
                }
                else{
                    for(var i=0;i<values.length;i++){
                        result+=prefix+privateCodes[i]+','+publicCodes[i]+'\n';
                    }
                }
                res.send(result.slice(0,-1));
                sql='update batchs set isDownloaded=1 where id=?';
                db.query(sql,[batchs[0].id]);
            });
        });
    });
});

module.exports = router;
