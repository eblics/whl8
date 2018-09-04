var api = require('../untilities/api');
var db = require('../untilities/database');
var encoder = require('../untilities/encoder');
var envConfig = require('../untilities/config').getConfig();

exports.produceOrder=function *(data,mchid){
    var config={
        orderno:{
            desc:'订单编号',
            length:[1,100]
        },
        ordertype:{
            desc:'生产入库'
        },
        productcode:{
            desc:'产品编码',
            length:[1,100]
        },
        productname:{
            desc:'产品名称',
            length:[1,100]
        },
        factorycode:{
            desc:'生产工厂编码',
            length:[1,10]
        },
        factoryname:{
            desc:'生产工厂名称',
            length:[1,200]
        },
        producetime:{
            desc:'生产时间',
            length:[1,11],
            numeric:true,
            required:false
        },
        time:{
            desc:'入库时间',
            length:[1,11],
            numeric:true
        },
        shelflife:{
            desc:'保质期',
            length:[1,10],
            regexp:/^(?:\d+[dwmy])+$/,
            required:false
        },
        expiretime:{
            desc:'过期时间',
            length:[1,11],
            numeric:true,
            required:false
        }
    };
    var result=api.getInput(data, config, true);
    if(result!=null){
        return result;
    }
    
    data.processStatus=0;
    data.mchid=mchid;
    data.ordertime=data.time;
    delete data.time;
    if(data.producetime==null){
        data.producetime=data.ordertime;
    }
    if(data.expiretime==null){
        if(data.shelflife==null){
            return api.setOutput(2,'缺少'+config.shelflife.desc+'或'+config.expiretime.desc+'字段');
        }
        var changeDate=function(date,type,num){
            switch (type) {
                case 'd':
                    date.setDate(date.getDate()+num);
                    break;
                case 'w':
                    date.setDate(date.getDate()+7*num);
                    break;
                case 'm':
                    date.setMonth(date.getMonth()+num);
                    break;
                case 'y':
                    date.setFullYear(date.getFullYear()+num);
                    break;
            }
        };
        var date=new Date(parseInt(data.ordertime)*1000);
        data.shelflife.match(/\d+[dwmy]/g).forEach(function(group){
            var num=parseInt(group.slice(0,-1));
            var type=group[group.length-1];
            changeDate(date,type,num);
        });
        data.expiretime=Math.round(date.getTime()/1000);
        
        data.shelflifeStr=data.shelflife;
        delete data.shelflife;
    }
    else{
        delete data.shelflife;
    }
    
    var orderid=yield db.insertSync('tts_orders',data,{puttime:'unix_timestamp()'});
    return api.setOutput(0,{orderid:orderid});
};

exports.inOrder=function *(data,mchid){
    var config={
        orderno:{
            desc:'订单编号',
            length:[1,100]
        },
        ordertype:{
            desc:'生产入库'
        },
        productcode:{
            desc:'产品编码',
            length:[1,100]
        },
        productname:{
            desc:'产品名称',
            length:[1,100]
        },
        time:{
            desc:'入库时间',
            length:[1,11],
            numeric:true
        }
    };
    var result=api.getInput(data, config, true);
    if(result!=null){
        return result;
    }
    
    data.processStatus=0;
    data.mchid=mchid;
    data.ordertime=data.time;
    delete data.time;
    
    var orderid=yield db.insertSync('tts_orders',data,{puttime:'unix_timestamp()'});
    return api.setOutput(0,{orderid:orderid});
};

exports.outOrder=function *(data,mchid){
    var config={
        orderno:{
            desc:'订单编号',
            length:[1,100]
        },
        ordertype:{
            desc:'生产入库'
        },
        productcode:{
            desc:'产品编码',
            length:[1,100]
        },
        productname:{
            desc:'产品名称',
            length:[1,100]
        },
        saletocode:{
            desc:'销往客户编码',
            length:[1,100]
        },
        saletoname:{
            desc:'客户名称',
            length:[1,200]
        },
        saletoagc:{
            desc:'销往区域的行政区域编',
            length:[1,10]
        },
        time:{
            desc:'入库时间',
            length:[1,11],
            numeric:true
        }
    };
    
    var result=api.getInput(data, config, true);
    if(result!=null){
        return result;
    }
    
    data.processStatus=0;
    data.mchid=mchid;
    data.ordertime=data.time;
    delete data.time;
    
    var orderid=yield db.insertSync('tts_orders',data,{puttime:'unix_timestamp()'});
    return api.setOutput(0,{orderid:orderid});
};

exports.privateCodeProcess=function(codes,codeVersions){
    var errMsgs=[];
    var enCodeList={};
    
    codes.forEach(function(value){
        var code=value.trim();
        if(code.length==0){
            return;
        }
        var codeVer=[];

        for(var i=0;i<codeVersions.length;i++){
            if(code[0]==codeVersions[i].version){
                codeVer=codeVersions[i];
                break;
            }
        }
        if(codeVer.length==0){
            errMsgs.push(code+'码版本不存在');
        }
        else if(code.length!=codeVer.version.length+codeVer.mchcodelen+codeVer.seriallen+codeVer.validlen+codeVer.offsetlen){
            errMsgs.push(code+'长度不符');
        }
        else if(!/^[0-9A-Za-z]+$/.test(code)){
            errMsgs.push(code+'包含非法字符');
        }
        else{
            if(!enCodeList.hasOwnProperty('_'+codeVer.version)){
                enCodeList['_'+codeVer.version]={codes:[],version:{version:codeVer.version,mchcodelen:codeVer.mchcodelen,
                        seriallen:codeVer.seriallen,validlen:codeVer.validlen}};
            }
            enCodeList['_'+codeVer.version].codes.push(code);
        }
    });

    var enCodes=[];
    var numbers=[];
    var subCodes=[];
    for(name in enCodeList){
        var resultCodes=encoder.decodeAndNumber(enCodeList[name].codes,enCodeList[name].version);
        for(var i=0;i<enCodeList[name].codes.length;i++){
            enCodes.push(enCodeList[name].codes[i]);
            var values=resultCodes[i].split(',');
            numbers.push(values[1]);
            subCodes.push(values[0]);
        }
    }
    
    return {enCodes:enCodes,subCodes:subCodes,numbers:numbers,errMsgs:errMsgs};
};

var existUnknownChar=function(str,chars){
    for(var i=0;i<str.length;i++){
        if(chars.indexOf(str[i])==-1){
            return true;
        }
    }
    return false;
};

exports.publicCodeProcess=function(codes,codeVer){
    var errMsgs=[];
    var subCodes=[];
    
    codes.forEach(function(value){
        var code=value.trim();
        if(code.length==0){
            return;
        }

        if(code.length!=envConfig.code.length){
            errMsgs.push(code+'长度不符');
        }
        else if(existUnknownChar(code,envConfig.code.char32set)){
            errMsgs.push(code+'包含非法字符');
        }
        else{
            subCodes.push(code);
        }
    });

    var enCodes=[];
    var numbers=[];
    
    var resultCodes=encoder.encodeAndNumber(subCodes,codeVer);
    resultCodes.forEach(function(value){
        var values=value.split(',');
        numbers.push(values[1]);
        enCodes.push(values[0]);
    });
    
    return {enCodes:enCodes,subCodes:subCodes,numbers:numbers,errMsgs:errMsgs};
};

exports.getSqlList=function(resultCodes,orderId,batch,ifpubcode){
    var bufferCount = 50000;
    var count = 1;
    
    var baseSql = 'insert ignore into tts_orders_codes(code,pubcode,orderid) values';
    var sql = '';
    var min = batch.min;
    var max = batch.max;
    var minLen = min.toString().length;
    var maxLen = max.toString().length;
    var sqlList=[];
    for(var i=0;i<resultCodes.enCodes.length;i++){
        var codeNum=resultCodes.numbers[i];
        if(codeNum.length<minLen||codeNum.length>maxLen || parseInt(codeNum)<min||parseInt(codeNum)>max){
            resultCodes.errMsgs.push((ifpubcode===false?resultCodes.enCodes[i]:resultCodes.subCodes[i])+'不在有效范围内');
            continue;
        }
        
        if(sql.length==0){
            sql=baseSql;
        }
        else{
            sql+=',';
        }
        sql+="("+db.escape(resultCodes.enCodes[i])+","+db.escape(resultCodes.subCodes[i])+","+orderId+")";
        
        if (count % bufferCount == 0) {
            sqlList.push(sql);
            
            count=0;
            sql='';
        }
        count++;
    }
    
    if(sql.length!=0){
        sqlList.push(sql);
    }
    return sqlList;
};

exports.completeAction=function *(errMsgs,orderId,mchId,isSync){
    var errmsg='';
    if(errMsgs.length!=0)
        errmsg=errMsgs.join('\r\n')+'\r\n';
    var scanSql='update tts_orders_codes c join scan_log s on c.code=s.code set c.isScan=1 where c.orderId=?';
    yield db.querySync(scanSql,[orderId]);
    var comSql='update tts_orders set processStatus=1,errmsg=CONCAT(IFNULL(errmsg,""),?) where mchid=? and id=?';
    yield db.querySync(comSql,[errmsg,mchId,orderId]);
    if(isSync){
        return api.setOutput(0,{codeerrmsg:errmsg,processid:orderId});
    }
};