var execSync = require('child_process').execSync;

var path='./bin/encoder.php';
//var path='./bin/remote.php';

var getCodes=function(codes,parmas){
    var pars=['php',path];
    parmas.forEach(function(p){
        pars.push(p);
    });
    codes.forEach(function(code){
        pars.push(code);
    });
    if(pars.length==parmas.length+2)
        return [];
    var out=execSync(pars.join(' '),{encoding: 'utf8'});
    return out.split('\n');
};

var batchGetCodes=function(codes,parmas){
    var result=[];
    var pCodes=[];
    for(var i=0;i<codes.length;i++){
        pCodes.push(codes[i]);
        if((i+1)%10000==0){
            getCodes(pCodes,parmas).forEach(function(code){
                result.push(code);
            });
            pCodes=[];
        }
    }
    if(pCodes.length!=0){
        getCodes(pCodes,parmas).forEach(function(code){
            result.push(code);
        });
    }
    return result;
};

exports.encodeAndNumber=function(codes,appinfo){
    var parmas=['1',appinfo.version,appinfo.code,appinfo.seriallen,appinfo.validlen];
    return batchGetCodes(codes,parmas);
};

exports.decode=function(codes,appinfo){
    var parmas=['2',appinfo.version,appinfo.mchcodelen,appinfo.seriallen,appinfo.validlen];
    return batchGetCodes(codes,parmas);
};

exports.privateCode=function(codes,appinfo){
    var parmas=['3',appinfo.version,appinfo.code,appinfo.seriallen,appinfo.validlen];
    return batchGetCodes(codes,parmas);
};

exports.publicCode=function(codes,appinfo){
    var parmas=['4'];
    return batchGetCodes(codes,parmas);
};

exports.decodeAndNumber=function(codes,appinfo){
    var parmas=['5',appinfo.version,appinfo.mchcodelen,appinfo.seriallen,appinfo.validlen];
    return batchGetCodes(codes,parmas);
};

exports.encode=function(codes,appinfo){
    var parmas=['6',appinfo.version,appinfo.code,appinfo.seriallen,appinfo.validlen];
    return batchGetCodes(codes,parmas);
};