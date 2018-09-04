var fs = require('fs');
var co=require('co');
var mysql=require('mysql2/promise');
//var mysql=require('promise-mysql');
var util=require('util');
var exec=require('child_process').exec;

var queue=null;
var master_db=null;
var trg_servers=null;
var master_queue_size=1500;
var rpt_queue_size=1500;
var master_error_log="/var/log/queue/master_error.sql";
var rpt_error_log="/var/log/queue/rpt_error.sql";
var master_sql=util.format('/**************** %s ***********************/\n',new Date().format('yyyy-MM-dd hh:mm:ss'));
var rpt_sql=util.format('/**************** %s ***********************/\n',new Date().format('yyyy-MM-dd hh:mm:ss'));

function monitor_queue_sql_master(pageNum,pageSize,timeout){
    co(function*(){
        try{
            yield co(function*(){
                master_sql=util.format('/****************master sql %s ***********************/\n',new Date().format('yyyy-MM-dd hh:mm:ss'));
                var totalNum=pageNum*pageSize;
                for(var i=1;i<=totalNum;i++){
                    try{
                        var item=yield queue.lpopAsync('sql_master');
                        if(i==1 && item==null){
                            break;
                        }
                        if(item!=null){
                            master_sql+=item+';\n';
                        }
                        if(i % pageSize == 0){
                            master_db_query(master_sql);
                            master_sql='';
                        }
                        if(item==null){
                            break;
                        }
                    }catch(e){
                        console.log(e);
                    }
                }
                if(master_sql!='' && master_sql!=null &&  master_sql!='null'){
                    master_db_query(master_sql);
                }
                function master_db_query(sqlStr){
                    co(function*(){
                        var stime=new Date().getTime();
                        var conn=null;
                        try{
                            conn=yield master_db.getConnection();
                            yield conn.query(sqlStr);
                            conn.release();
                        }catch(e){
                            conn&&conn.release();
                            console.log(util.format('############# master error %s####################',new Date().format('yyyy-MM-dd hh:mm:ss')));
                            console.log(e);
                            fs.appendFileSync(master_error_log,sqlStr,'utf-8');
                        }
                        var etime=new Date().getTime();
                        var time=(etime-stime)/1000;
                        console.log(new Date().format('yyyy-MM-dd hh:mm:ss')+' monitor_queue_sql_master executing - '+time+'s');
                    });
                }
            });
        }
        finally{
            setTimeout(function(){monitor_queue_sql_master(pageNum,pageSize,timeout)},timeout);
        }
    });
}

function monitor_queue_sql_rpt(pageNum,pageSize,timeout){
    co(function*(){
        try{
            yield co(function*(){
                rpt_sql=util.format('/****************rpt sql %s ***********************/\n',new Date().format('yyyy-MM-dd hh:mm:ss'));
                var totalNum=pageNum*pageSize;
                for(var i=1;i<=totalNum;i++){
                    try{
                        var item=yield queue.lpopAsync('sql_rpt');
                        if(i==1 && item==null){
                            break;
                        }
                        if(item!=null){
                            rpt_sql+=item;
                        }
                        if(i % pageSize == 0){
                            rpt_db_query(rpt_sql);
                            rpt_sql='';
                        }
                        if(item==null){
                            break;
                        }
                    }
                    catch(e){
                        console.log(e);
                    }
                }
                if(rpt_sql!='' && rpt_sql!=null &&  rpt_sql!='null'){
                    rpt_db_query(rpt_sql);
                }
                function rpt_db_query(sqlStr){
                    co(function*(){
                        var stime=new Date().getTime();
                        for(var i=0;i<trg_servers.length;i++){
                            var db=trg_servers[i].db;
                            var conn=null;
                            try{
                                conn=yield db.getConnection();
                                yield conn.query(sqlStr);
                                conn.release();
                            }
                            catch(e){
                                conn&&conn.release();
                                console.log(util.format('############# rpt error %s####################',new Date().format('yyyy-MM-dd hh:mm:ss')));
                                console.log(e);
                                fs.appendFileSync(i+'_'+rpt_error_log,sqlStr,'utf-8');
                            }
                        }
                        var etime=new Date().getTime();
                        var time=(etime-stime)/1000;
                        console.log(new Date().format('yyyy-MM-dd hh:mm:ss')+' monitor_queue_sql_rpt executing - '+time+'s');
                    });
                }
                
            });
        }
        finally{
            setTimeout(function(){monitor_queue_sql_rpt(pageNum,pageSize,timeout)},timeout);
        }
    });
}

function monitor_queue_scan_wait(timeout){
    co(function*(){
        try{
            yield co(function*(){
                //检查排队队列，将已经过期的码从队列中删除
                var stime=new Date().getTime();
                var codes=yield queue.zrangeAsync('ngx_codes_set',0,100);
                for(var i=0;i<codes.length;i++){
                    var t=yield queue.zscoreAsync('ngx_codes_expire',codes[i]);
                    //80秒过期
                    if(t==null||((new Date()).getTime()/1000)-t>80){
                        yield queue.zremAsync('ngx_codes_set',codes[i]);
                        yield queue.zremAsync('ngx_codes_expire',codes[i]);
                    }
                }
                yield queue.setAsync('ngx_current_in',0);
                var etime=new Date().getTime();
                var time=(etime-stime)/1000;
                console.log(new Date().format('yyyy-MM-dd hh:mm:ss')+' monitor_queue_scan_wait executing - '+time+'s');
            });
        }
        finally{
            setTimeout(function(){monitor_queue_scan_wait(timeout)},timeout);
        }
    });
}

function monitor_queue_limit_zone(timeout){
    co(function*(){
        try{
            yield co(function*(){
                var stime=new Date().getTime();
                var eles=yield queue.zrangeAsync('limit_zone',0,-1,'withscores');
                var conn=null;
                conn=yield master_db.getConnection();
                for(var i=0;i<eles.length;i+=2){
                    try{
                        //[table.field.key.val]
                        var arr=eles[i].split('.');
                        var val=eles[i+1];
                        if (arr[0] != '' && arr[1] !== undefined && arr[2] !== undefined && arr[2] !== undefined) {
                            var sql=`update ${arr[0]} set ${arr[1]}=${val} where ${arr[2]}=${arr[3]};`;
                            yield conn.query(sql);
                        }
                    }
                    catch(e){
                        conn&&conn.release();
                        console.log(sql);
                        console.log('################limit_zone_error#####################')
                        console.log(e);
                    }
                }
                conn.release();
                var etime=new Date().getTime();
                var time=(etime-stime)/1000;
                console.log(new Date().format('yyyy-MM-dd hh:mm:ss')+' monitor_queue_limit_zone executing - '+time+'s');
            });
        }
        finally{
            setTimeout(function(){monitor_queue_limit_zone(timeout)},timeout);
        }
    });
}


function triggerClass(options){
    queue=options.redis;
    master_db=options.master;
    //master_db=mysql.createPool(options.config.mysql);
    trg_servers=options.trg_servers;
    for(var i=0;i<trg_servers.length;i++){
        trg_servers[i].db=require('mysql2/promise').createPool(trg_servers[i]);
    }
    monitor_queue_sql_master(20,100,5000);
    monitor_queue_sql_rpt(20,100,5000);
    monitor_queue_scan_wait(2000);
    monitor_queue_limit_zone(10000);
}
module.exports=triggerClass;