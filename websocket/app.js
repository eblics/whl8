var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
var url = require('url');
var xss = require('xss');
var fs = require('fs-ext');
var moment = require('moment');
var mysql=require('mysql');


var configFile = fs.readFileSync('config.js','utf-8');
var config = JSON.parse(configFile)[process.env.NODE_ENV];
var pool  = mysql.createPool(config.mysql);
pool.on('error',function(err){
    console.log(err);
});

server.listen(config.port, function () {
    console.log('Server listening at port %d', config.port);
});

// 定义房间内用户id
var rooms = {};

io.of('/chat').on('connection', function (socket) {
    var clientUrlFormat = url.parse(socket.handshake.headers.referer, true);
    var groupId = clientUrlFormat.path.replace('/group/chat/', '');
    if (groupId.indexOf('?') != -1) {
        groupId=groupId.split('?')[0];
    }
    var roomId = groupId, user;
    //检查创建聊天日志路径
    var logPath = './data/chat/' + groupId;
    var logFile = moment().format('YYYYMM') + '.txt';
    fs.exists(logPath, function(exists){
        if(! exists){
            fs.mkdir(logPath, function(err){
                if (err) {
                    console.log(logPath + ' create fail');
                } else {
                    console.log(logPath + ' create success');
                }
            });
        }
    });

    // 用户进房间（进群上线）
    socket.on('join', function(currentUser, currentGroup) {
        console.log('user in: ' + JSON.stringify(currentUser));
        user = currentUser;
        // 将用户昵称加入房间名单中
        if (! rooms[roomId]) {
            rooms[roomId] = [];
        }

        // 将用户id加入此房间
        rooms[roomId].push(user.id);
        socket.join(roomId);
        // 记录系统内容
        msgLog({id:-1},user.id + '进入了本群', logPath, logFile);
        // 通知房间内人员
        io.of('/chat').to(roomId).emit('sys',rooms[roomId],'<font style="color:#ccc">( '+moment().format('HH:mm')+' )</font> '+user.name+' 进入了本群',currentGroup);

        fs.exists(logPath+'/'+logFile, function(exists){
            if(! exists){
                fs.open(logPath+'/'+logFile,'w+',function (err, fd) {
                    if (err) {
                        console.log('文件创建失败。');
                        return;
                    }
                    console.log(logPath+'/'+logFile+' 文件创建成功');
                    fs.close(fd, function(err){

                    });
                })
            } else {
                //读取历史聊天记录
                fs.seek(fs.openSync(logPath+'/'+logFile, 'r'), -1024*10, 2, function(err, currFilePos){
                    if(err){
                        if(JSON.stringify(err).indexOf('EINVAL')==-1){
                            return;
                        }
                        currFilePos=0;
                    }
                    fs.open(logPath+'/'+logFile,'r', function(err,fd){
                        if(! err){
                            var buffer = new Buffer(1024*10);
                            fs.read(fd,buffer,0,buffer.length,currFilePos,function(err, bytes){
                                if(! err){
                                    var outStr=buffer.slice(0,bytes).toString();
                                    historyLog(outStr,roomId);
                                    fs.close(fd);
                                }
                            });
                        }
                    });
                });
            }
        });
    });

    // 接收消息
    socket.on('message', function (msg,syschat) {
        console.log(msg);
        if(typeof syschat=='undefined') syschat=0;
        var options = {
            whiteList: {}
        };
        // 验证如果用户不在房间内则不给发送
        if (typeof rooms['roomId'] !== 'undefined') {
            if (rooms[roomId].indexOf(user.id) === -1) {  
                return false;
            }
        }
        
        //记录聊天内容
        msgLog(user,msg,logPath,logFile,syschat);
        user.time=moment().format('HH:mm');
        io.of('/chat').to(roomId).emit('msg', user, msg,syschat);
    });

    //用户离开房间
    socket.on('leave', function () {
        socket.emit('disconnect');
    });

    // 从房间名单中移除
    socket.on('disconnect', function () {
        if (typeof user === 'undefined') {
            return;
        }
        var index = rooms[roomId].indexOf(user.id);
        if (index !== -1) {
            rooms[roomId].splice(index,1);
        }
        socket.leave(roomId);    // 退出房间
        //记录系统内容
        msgLog({id:-1},user.id+'离开了本群',logPath,logFile);
        io.of('/chat').to(roomId).emit('sys',rooms[roomId],'<font style="color:#ccc">( '+moment().format('HH:mm')+' )</font> '+user.name+' 离开了本群');
        console.log(user.name + '退出了' + roomId);
    });
  
});


function msgLog(user,msg,logPath,logFile,syschat){
    if(typeof syschat=='undefined') syschat=0;
    msg=msg.replace(/"|&|'|<|>|[\x00-\x20]|[\x7F-\xFF]|[\u0100-\u2700]/g,function($0){
        var c = $0.charCodeAt(0), r = ["&#"];
        c = (c == 0x20) ? 0xA0 : c;
        r.push(c); r.push(";");
        return r.join("");
    });
    var options = {whiteList:{}};
    msg=xss(msg,options);
    // 记录聊天内容保存文件
    if (typeof user !== 'undefined') {
        var chatLog=moment().format('YYYY-MM-DD HH:mm:ss')+' #%# '+user.id+' #%# '+msg.replace(/ #%# /g," \#\%\# ").replace(/\n/g,"\/n")+' #%# '+syschat+'\n';
    }
    fs.appendFile(logPath+'/'+logFile,chatLog,function(err){  
        if(err){
            console.log(err);  
        }  
    });
}

function historyLog(dataStr,roomId){
    var html='';
    var formatData=[];
    var arr=dataStr.split('\n');
    var newArr=[];
    pool.getConnection(function(err, connection) {
        if(err){        
            console.log('[pool getConnection] - :'+err);
            return;
        }
        connection.query('SELECT * from groups_members where groupId='+roomId, function(err, rows) {
            if (err) {
                console.log(err);
                return;
            };
            arr.forEach(function(el){
                if(typeof el!='undefined' && el.indexOf(' #%# ')!=-1){
                    var thisMsgArr=el.split(' #%# ');
                    if(thisMsgArr[1]!=-1){
                        thisMsgArr.push('');
                        thisMsgArr.push('');
                        newArr.push(thisMsgArr);
                    }
                }
            },this);
            if(newArr.length>40){
                for(var i=40;i>0;i--){
                    formatData.push(newArr[newArr.length-i]);
                }
            }else{
                formatData=newArr;
            }
            formatData.forEach(function(el){
                rows.forEach(function(element) {
                    // console.log(element.userId);
                    if(element.userId==el[1]){
                        if(el.length==6){
                            el[4]=element.headImage;
                            el[5]=element.nickName;
                        }else{
                            el[3]=element.headImage;
                            el[4]=element.nickName;
                        }
                    }
                }, this);
            },this);
            io.of('/chat').to(roomId).emit('historyMsg', formatData);
            connection.release();
        });
    });
    
}