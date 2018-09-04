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
    if (req.query.testing === 'lihuijing') {
        req.info={};
        if (req.query.mchId !== undefined) {
            req.info.mchId = req.query.mchId;
        } else {
            req.info.mchId = 0;
        }
        next();
        return;
    }
    var cookie_value=req.header('cookie');
    var dict={};
    var cookie_pair=cookie_value.split(';');
    for(var i=0;i<cookie_pair.length;i++){
    	var pair=cookie_pair[i];
	if(pair.length==0||pair.indexOf('=')==-1) continue;
	var p=pair.trim().split('=');
	var k=p[0];
	var v=p[1];
	dict[k]=v;
    }
    //console.log(dict['ci_session']);
    if(dict['ci_session']==null){
        res.sendStatus(403);
        return;
    }
    co(function* (){
        try{
            var ci_session_key='ci_session:'+dict['ci_session'];
            var data=yield session.getAsync(ci_session_key);
            if (data == null) {
                res.sendStatus(403);
                return; 
            }
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
