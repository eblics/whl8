var bodyParser  = require('body-parser');
var fs          = require('fs');
var co          = require('co');
var app         = require('express')();
var redis       = require('redis');
var util        = require('util');
var bluebird    = require('bluebird');
var exec        = require('child_process').exec;
var cookieParser = require('cookie-parser');

require('./hls_util');
console.log('启动环境 -> ' + process.env.NODE_ENV);
if (process.env.NODE_ENV == undefined) {
    process.env.NODE_ENV = 'development';
}

var config = JSON.parse(fs.readFileSync('config.js','utf-8'))[process.env.NODE_ENV];
var mysql  = require('mysql2/promise').createPool(config.mysql);
global.mysqlPool = mysql;

bluebird.promisifyAll(redis.RedisClient.prototype);
redis = redis.createClient(config.redis);

var cooker      = require('./cooker')({db:mysql, cache:redis});
var charts      = require('./charts')({cooker: cooker, session: redis});
var reporting   = require('./reporting')({cooker:cooker, session:redis})
var estimate    = require('./estimate')({cooker: cooker, session: redis});
var hr          = require('./controllers/hr');
var shop        = require('./shop')({cooker: cooker, session: redis});

//app.post('/rpt/reporting/test',function(req, res, next) {
//	console.log('work');
//	res.send('work');
//});

console.log('cookie');
app.use(function(req, res, next) {
    var now = new Date().toLocaleString();
    console.log(now + ' -> ' + req.url);
    next();
});
//app.use(cookieParser);
app.use('/rpt/reporting', reporting);
app.use('/rpt/charts', charts);
app.use('/rpt/estimate', estimate);
app.use('/rpt/hr', hr);
app.use('/rpt/shop', shop);

app.use(bodyParser.text());

app.post('/serve',function(req,res) {
    var sql = req.body;
    cooker.serve(sql).then((data) => {
        res.send(data).end();
    });
});

app.post('/serverow',function(req,res){
    if (config.white_list.indexOf(req.ip) > -1) {
        var sql=req.body;
        cooker.serve(sql).then((data)=>{
            if(data.length>0){
                res.send(data[0]).end();
            }
            else{
                res.send({}).end();
            }
        });
    } else {
        res.sendStatus(403);
    }
});

//将IP加入防火墙屏蔽
app.post('/add_blacklist',function(req,res){
    var commStr="iptables -I INPUT -s "+req.body+" -j DROP";
    fs.appendFileSync('blacklist.log',commStr+'\n','utf-8');
    exec(commStr, function(err,stdout,stderr){
        if(err) {
            console.log('error:'+stderr);
            fs.appendFileSync('blacklist.log',stderr+'\n','utf-8');
        } else {
            console.log(stdout);
            fs.appendFileSync('blacklist.log',stdout+'\n','utf-8');
        }
        res.send(stdout).end();
    });
});
//将IP移出防火墙屏蔽
app.post('/remove_blacklist',function(req,res){
    var commStr="iptables -D INPUT -s "+req.body+" -j DROP";
    fs.appendFileSync('blacklist.log',commStr+'\n','utf-8');
    exec(commStr, function(err,stdout,stderr){
        if(err) {
            console.log('error:'+stderr);
            fs.appendFileSync('blacklist.log',stderr+'\n','utf-8');
        } else {
            console.log(stdout);
            fs.appendFileSync('blacklist.log',stdout+'\n','utf-8');
        }
        res.send(stdout).end();
    });
});

var server = app.listen(config.port, function () {
    var host = '127.0.0.1';
    var port = server.address().port;
    console.log('rpt server started at http://%s:%s', host, port);
});

//process.on('uncaughtException', function (err) {
//  console.log(err);
//});
