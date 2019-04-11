var fs = require('fs');
var exec=require('child_process').exec;
var path = require('path');
var config=JSON.parse(fs.readFileSync('config.js','utf-8'))[process.env.NODE_ENV];

function service_queue(){
    common_deal(10,10,'deal_withdraw',1000);
    common_deal(10,1,'deal_withdraw_hlspay',2000);
    common_deal(5,5,'deal_template_msg',1000);
    common_deal(8,5,'deal_userinfo',1000);
    common_deal(2,1,'deal_withdraw_processing',10000);
    common_deal(2,1,'deal_withdraw_processing_hlspay',10000);
    common_deal(30,10,'deal_mch_template',1800000);
    common_deal(5,5,'deal_tagging_user',1000);
    setinterval_deal('group_scanpk',60000);
}

function common_deal(cnum,cpage,ctype,interval){
    function comm_str(num,page){
        var str="su www-data -c 'CI_ENV="+config.ci_env+" php5 /var/www/whl8.cn/php_web/cli/index.php wxapi ";
        str+=ctype;
        return str+"/"+num+"/"+page+"'";
    }
    function exec_comm(num,page,result){
        exec(comm_str(num,page), function(err,stdout,stderr){
            var ok=0;
            if(err) {
                console.log('error:'+stderr+err);
                fs.appendFileSync('/var/log/queue_service_queue.log',stderr+'\n','utf-8');
            } else {
                if(stdout!='NULL'){
                    ok=1;
                    console.log(stdout);
                    fs.appendFileSync('/var/log/queue_service_queue.log',stdout+'\n','utf-8');
                }
            }
            result(ok);
        });
    }
    function main(num,page){
        var over=page;
        for(var i=1;i<=page;i++){
            exec_comm(num,i,function(d){
                over--;
                if(over==0){
                    setTimeout(function(){
                        main(num,page);
                    },interval);
                }
            });
        }
    }
    main(cnum,cpage);
}

function setinterval_deal(dtype,interval){
    var command='';
    switch(dtype){
        case 'group_scanpk':
        command="su www-data -c 'CI_ENV="+config.ci_env+" php5 /var/www/whl8.cn/php_web/cli/index.php group scanpk_heart_beat'";
        break;
    }
    exec(command, function(err,stdout,stderr){
        if(err) {
            console.log('error:'+stderr+err);
            fs.appendFileSync('/var/log/queue_service_queue.log',stderr+'\n','utf-8');
        } else {
            if(stdout!='NULL'){
                console.log(stdout);
                fs.appendFileSync('/var/log/queue_service_queue.log',stdout+'\n','utf-8');
            }
        }
        setTimeout(function(){
            setinterval_deal(dtype,interval);
        },interval);
    });
}

function service_queue_class(options){
    service_queue();
}
module.exports=service_queue_class;
