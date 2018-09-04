var bodyParser = require('body-parser');
var co=require('co');
var util=require('util');
var bluebird=require('bluebird');
var exec=require('child_process').exec;

require('./hls_util');
var fs = require('fs');
var config=JSON.parse(fs.readFileSync('config.js','utf-8'))[process.env.NODE_ENV];
var redis=require('redis');
bluebird.promisifyAll(redis.RedisClient.prototype);
var master=require('mysql2/promise').createPool(config.master);
var mysql=require('mysql2/promise').createPool(config.mysql);
var redis=redis.createClient(config.redis);

var trigger_queue=require('./trigger')({config:config,master:master,redis:redis,trg_servers:config.trg_servers});

var service_queue=require('./service_queue')();
