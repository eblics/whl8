var db = require('./database');

var sqlQueues=[];
var stateQueues={};
var pools={};

const poolNumber=2;
const delay=200;

var init=function(){
    for(var i=0;i<poolNumber;i++){
        pools['_'+i]={isWorking:false};
    }
};

init();

var startWork=function(){
    if(sqlQueues.length==0)
        return;
    for(var name in pools){
        if(pools[name].isWorking==false){
            if(sqlQueues.length==0)
                return;
            pools[name].isWorking=true;
            //console.log(name+'startWork');
            working(name);
        }
    }
};

var working=function(poolName){
    var obj=sqlQueues.shift();
    db.query(obj.sql,function(){
        finishWork(poolName,obj.name);
    });
};

var finishWork=function(poolName,stateName){
    stateQueues[stateName].cur++;
    if(stateQueues[stateName].sum==stateQueues[stateName].cur){
        stateQueues[stateName].callback();
        delete stateQueues[stateName];
    }
    pools[poolName].isWorking=false;
    startWork();
};

var createRandomName=function(){
    var str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var name='';
    for(var i=0;i<18;i++){
        name+=str[Math.floor(Math.random()*str.length)];
    }
    return name;
};

exports.run=function(sqlarray,callback){
    if(sqlarray.length==0){
        callback();
        return;
    }
    var name=createRandomName();
    for(var i=0;i<sqlarray.length;i++){
        sqlQueues.push({name:name,sql:sqlarray[i]});
    }
    stateQueues[name]={sum:sqlarray.length,cur:0,callback:callback};
    startWork();
};

exports.runSync=function(sqlarray){
    return new Promise(function (resolve, reject){
        exports.run(sqlarray,function() {
            resolve();
        });
    });
};