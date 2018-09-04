var api = require('./api');
var db = require('./database');

exports.check=function(req, res, next){
    var token=req.query.token;
    if(token==null){
        res.json(api.setOutput(2,'缺少token参数'));
        return;
    }
    
    var sql='select id,mchid from tts_apps where token=? and expireTime>unix_timestamp()';
    db.query(sql,[token],function(apps){
        if(apps.length==0){
            res.json(api.setOutput(2,'token验证失败'));
            return;
        }
        var refreshSql='update tts_apps set expireTime=UNIX_TIMESTAMP(DATE_ADD(now(),INTERVAL 30 MINUTE)) where id=?';
        db.query(refreshSql,[apps[0].id]);
        
        req.appinfo=apps[0];
        sql='select code,codeversion from merchants where id=?';
        db.query(sql,[req.appinfo.mchid],function(rows){
            if(rows.length==0){
                res.json(api.setOutput(2,'商户信息缺失'));
                return;
            }
            req.appinfo.code=rows[0].code;
            req.appinfo.version=rows[0].codeversion;
            sql='select mchcodelen,seriallen,validlen,offsetlen from code_version where versionnum=?';
            db.query(sql,[req.appinfo.version],function(rows){
                if(rows.length==0){
                    res.json(api.setOutput(2,'码版本信息缺失'));
                    return;
                }
                req.appinfo.mchcodelen=rows[0].mchcodelen;
                req.appinfo.seriallen=rows[0].seriallen;
                req.appinfo.validlen=rows[0].validlen;
                req.appinfo.offsetlen=rows[0].offsetlen;
                next();
            });
        });
    });
};

exports.machinecheck=function(req, res, next){
    var token=req.query.token;
    if(token==null){
        res.json(api.setOutput(2,'缺少token参数'));
        return;
    }
    
    var sql='select id from machine_apps where token=? and expireTime>unix_timestamp()';
    db.query(sql,[token],function(apps){
        if(apps.length==0){
            res.json(api.setOutput(2,'token验证失败'));
            return;
        }
        var refreshSql='update machine_apps set expireTime=UNIX_TIMESTAMP(DATE_ADD(now(),INTERVAL 30 MINUTE)) where id=?';
        db.query(refreshSql,[apps[0].id]);
        
        next();
    });
};