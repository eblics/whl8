var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');

router.post('/token', function(req, res, next) {
    var config={
        appid:{
            desc:'appid',
            length:[1,100]
        },
        appsecret:{
            desc:'appsecret',
            length:[1,100]
        }
    };
    var result=api.getInput(req.body, config);
    if(result!=null){
        res.json(result);
        return;
    }
    var sql='select * from tts_apps where appid=? and appsecret=?';
    db.query(sql, [req.body.appid,req.body.appsecret], function(rows) {
        if(rows.length==0){
            res.json(api.setOutput(1, 'appid或appsecret不正确'));
            return;
        }
        sql='select unix_timestamp() timestamp';
        db.query(sql,function(rs){
            if(parseInt(rs[0].timestamp)<parseInt(rows[0].expireTime)){
                sql='update tts_apps set expireTime=UNIX_TIMESTAMP(DATE_ADD(now(),INTERVAL 30 MINUTE)) where id=?';
                db.query(sql, [rows[0].id], function() {
                    res.json(api.setOutput(0,{token:rows[0].token}));
                });
            }
            else{
                var str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var token='';
                for(var i=0;i<64;i++){
                    token+=str[Math.floor(Math.random()*str.length)];
                }
                sql='update tts_apps set token=?,expireTime=UNIX_TIMESTAMP(DATE_ADD(now(),INTERVAL 30 MINUTE)) where id=?';
                db.query(sql, [token,rows[0].id], function(rows,fields) {
                    res.json(api.setOutput(0,{token:token}));
                });
            }
        });
    });
});

module.exports = router;
