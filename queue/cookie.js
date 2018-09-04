var co=require('co');
var bluebird=require('bluebird');
var fs = require('fs');
var config=JSON.parse(fs.readFileSync('config.js','utf-8'))[process.env.NODE_ENV];
var redis=require('redis');
bluebird.promisifyAll(redis.RedisClient.prototype);
var session=redis.createClient(config.redis);
var pu=require('php-unserialize');

var sess_dic={};
function cookie_parser(req,res,next){
    var ci_session_key=req.header('cookie');
    if(ci_session_key==null){
        res.sendStatus(403);
        return;
    }
    co(function* (){
        try{
            ci_session_key=ci_session_key.replace('=',':');
            var data=yield session.getAsync(ci_session_key);
            sess_dic=pu.unserializeSession(data);
            if(sess_dic.mchId==undefined){
                res.sendStatus(403);
                return;
            }
            req.info={};
            req.info.mchId=parseInt(sess_dic.mchId);
            next();
        }
        catch(e){
            console.log(e);
            res.sendStatus(403);
        }
    });
}

module.exports=cookie_parser;
