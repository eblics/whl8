var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');
var encoder = require('../untilities/encoder');
var config = require('../untilities/config');

router.post('/get', function(req, res, next) {
    var mchid=req.appinfo.mchid;
    
    var sql='select ss.statementNo,ss.id,s.idCardNo,s.mobile,s.openid from salesman s join\
        salesman_statements ss on s.id=ss.smId where ss.state<>2 and ss.mchid=?';
    db.query(sql,[mchid],function(statements){
        if(statements.length==0){
            res.json(api.setOutput(2,'没有数据'));
            return;
        }
        
        var sids=[];
        statements.forEach(function(item){
            sids.push(item.id);
        });
        
        sql='select b.statementsId,a.code from scan_log a join\
            salesman_statements_objs b on b.scanId=a.id and b.statementsId in ('+sids.join(',')+')';
        db.query(sql,[mchid],function(codes){
            var result={};
            result.data=[];
            
            statements.forEach(function(item){
                var d={};
                d.orderno=item.statementNo;
                d.icno=item.idCardNo;
                d.phoneNum=item.mobile;
                d.openid=item.openid;
                var nodes=[];
                
                codes.forEach(function(code){
                    if(code.statementsId==item.id){
                        nodes.push(code.code);
                    }
                });
                
                d.nodes=encoder.decode(nodes, req.appinfo);
                result.data.push(d);
            });
            
            sql='update salesman_statements set state=1 where state=0 and id in ('+sids.join(',')+')';
            db.query(sql,[mchid],function(codes){
                res.json(api.setOutput(0,result));
            });
        });
    });
});

router.post('/writeback/:orderno', function(req, res, next) {
    var config={
        errcode:{
            desc:'错误码',
            options:['0','1','2']
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
    
    var orderno=req.params.orderno;
    var ifpubcode=req.body.ifpubcode;
    
    var sql='select count(*) ct from salesman_statements where statementNo=? and mchId=? and state=1';
    db.query(sql,[orderno,req.appinfo.mchid],function(rows){
        if(parseInt(rows[0].ct)==0){
            res.json(api.setOutput(2,'订单号不存在或已被操作'));
            return;
        }
        
        if(req.body.errcode=='1'){
            config={
                errmsg:{
                    desc:'错误原因',
                    length:[1,200]
                },
                data:{
                    desc:'码数据'
                }
            };
            
            var result=api.getInput(req.body, config);
            if(result!=null){
                res.json(result);
                return;
            }
            
            var codes=req.body.data;
            
            config={
                code:{
                    desc:'码名称',
                    length:[1,20]
                },
                errcode:{
                    desc:'错误码',
                    numeric:true
                },
                errmsg:{
                    desc:'错误原因',
                    length:[1,200],
                    required:false
                }
            };
            
            var hCodes=[];
            for(var index in codes){
                result=api.getInput(codes[index], config);
                if(result!=null){
                    res.json(result);
                    return;
                }
                hCodes.push(codes[index].code);
            }
            if(ifpubcode!==false){
                hCodes=encoder.encode(hCodes,req.appinfo);
            }
            var codeArr=[];
            hCodes.forEach(function(code){
                codeArr.push(db.escape(code));
            });
            
            sql='select b.id,a.code from scan_log a join salesman_statements_objs b on a.id=b.scanId where a.code in ('+codeArr.join(',')+')';
            db.query(sql,function(idcodeArr){
                if(idcodeArr.length==0){
                    res.json(api.setOutput(2,'未找到码记录'));
                    return;
                }
                for(var index in hCodes){
                    var exist=false;
                    idcodeArr.forEach(function(codeObj){
                        if(codeObj.code==hCodes[index]){
                            exist=true;
                            return false;
                        }
                    });
                    if(!exist){
                        res.json(api.setOutput(2,codes[index].code+'码不存在'));
                        return;
                    }
                }
                
                var str='';
                idcodeArr.forEach(function(codeObj){
                    str+='('+codeObj.id+',';
                    for(var index in hCodes){
                        if(codeObj.code==hCodes[index]){
                            var code=codes[index];
                            str+=db.escape(code.errcode)+','
                                +(code.errmsg==null?"''":db.escape(code.errmsg));
                            break;
                        }
                    }
                    str+='),';
                });
                
                sql='insert into salesman_statements_objs(id,errcode,errmsg) values '+str.slice(0,-1)+
                    'on duplicate key update errcode=values(errcode),errmsg=values(errmsg)';
                
                db.query(sql,function(){
                    sql='update salesman_statements_objs o join salesman_statements s on o.statementsId=s.id\
                        set errcode=0 where s.statementNo=? and s.mchId=? and o.errcode is null';
                    db.query(sql,[orderno,req.appinfo.mchid],function(){
                        updateState();
                    });
                });
            });
        }
        else{
            if(req.body.errcode=='2'){
                config={
                    errmsg:{
                        desc:'错误原因',
                        length:[1,200]
                    }
            };
                
                var result=api.getInput(req.body, config);
                if(result!=null){
                    res.json(result);
                    return;
                }
            }
            
            sql='update salesman_statements_objs o join salesman_statements s on o.statementsId=s.id\
                set errcode=?,errmsg=? where s.statementNo=? and s.mchId=?';
            db.query(sql,[req.body.errcode=='2'?1:0,req.body.errmsg==null?'':req.body.errmsg,orderno,req.appinfo.mchid],function(){
                updateState();
            });
        }
        
        var updateState=function(){
            sql='update salesman_statements set state=2,settleCode=?,settleResult=?,settletime=unix_timestamp() where\
                statementNo=? and mchId=?';
            db.query(sql,[req.body.errcode,req.body.errmsg==null?'':req.body.errmsg,orderno,req.appinfo.mchid],function(){
                res.json(api.setOutput(0));
            });
        };
    });
});

if(config.getConfig().test_api === true){
    router.post('/test_changestate/:orderno', function(req, res, next) {
        var sql='update salesman_statements set state=1,settleCode=null,settleResult=null where statementNo=? and mchId=? and state=2';
        db.query(sql,[req.params.orderno,req.appinfo.mchid],function(){
            res.json(api.setOutput(0));
        });
    });
    
    router.post('/test_getresult/:orderno', function(req, res, next) {
        var sql='select b.code,a.errcode,a.errmsg from salesman_statements_objs a join scan_log b \
            on a.scanId=b.id join salesman_statements c on a.statementsId=c.id where c.statementNo=?';
        db.query(sql,[req.params.orderno],function(rows){
            var arr=[];
            rows.forEach(function(row){
                arr.push(row);
            });
            res.json(api.setOutput(0,{result:arr}));
        });
    });
}

module.exports = router;
