var util=require('util');
require('./hls_util');

var cooker={
    //忙闲状况
    busy:{},
    //菜单
    //menu:{},
    //新增品类
    n:0,
    db:null,
    cache:null,
    //提供数据。如果缓存中有数据，则直接提供
    serve:function(sql){
        return this.cache.getAsync(sql).then(function(value){
            var ret=undefined;
            //如果没有数据，则开始获取
            if(value==null){
                return cooker.cook(sql).then((rows)=>{return rows;});
            }
            //如果有缓存，在cooker不忙的时候去查询最新数据
            if(cooker.busy[sql]==undefined||cooker.busy[sql]==0){
                cooker.cook(sql);
            }
            return JSON.parse(value);
        });
    },
    typeCast:function (field, next) {
        if (field.type == 'VAR_STRING') {
            return field.string();
        }
        else if(field.type=='DATE'){
            return new Date(field.string()).format('yyyy-MM-dd');
        }
        else if(field.type=='DATETIME'){
            return new Date(field.string()).format('yyyy-MM-dd hh:mm:ss');
        }
        return next();
    },
    //从数据库查询数据
    cook:function(sql){
        this.busy[sql]=1;
        return this.db.getConnection()
        .then((conn)=>{
		console.log(conn);
            var res=conn.query({sql:sql,typeCast:this.typeCast,"charset":"utf8mb4","multipleStatements":true,"stringifyObjects":true,"supportBigNumbers":true});
            conn.release();
            return res;
        })
        .then((result)=>{
            var rows=result[0];
            cooker.cache.set(sql,JSON.stringify(rows));
            cooker.busy[sql]=0;
            return rows;
        })
        .catch((err)=>{
            //conn.release();
            console.log(sql);
            console.log(err);
        });
    }
}

//cooker.redis.on("error", function (err) {
//    console.log("Error " + err);
//});

function cookerClass(options){
    cooker.cache=options.cache;
    cooker.db=options.db;
    return cooker;
}
module.exports=cookerClass;
