
exports.getInput=function(object,config,isClear){
    var outputName=[];
    for(var name in config){
        var value=object[name];
        
        if(config[name].required===false && value==null){
            continue;
        }
        if(object[name]==null){
            return exports.setOutput(1,'缺少'+config[name].desc+'字段');
        }
        if(value.length==0){
            return exports.setOutput(1,config[name].desc+'字段不能为空');
        }
        if(typeof value=='string'){
            if(!validateLength(config[name],value.length)){
                return exports.setOutput(1,config[name].desc+'字段长度不符');
            }
            else if(!validateNumeric(config[name],value)){
                return exports.setOutput(1,config[name].desc+'非数字类型');
            }
            else if(!validateBoolean(config[name],value)){
                return exports.setOutput(1,config[name].desc+'非布尔类型');
            }
            else if(!validateOptions(config[name],value)){
                return exports.setOutput(1,config[name].desc+'类型不匹配');
            }
            else if(!validateRegexp(config[name],value)){
                return exports.setOutput(1,config[name].desc+'格式不正确');
            }
        }
        if(isClear===true){
            outputName.push(name);
        }
    }
    if(isClear===true){
        for(var name in object){
            if(outputName.indexOf(name)==-1){
                delete object[name];
            }
        }
    }
};

var validateLength=function(config,length){
    if(config.length!=null){
        var maxLength=0;
        var minLength=0;
        var lenConfig=config.length;
        if(lenConfig.length==2){
            maxLength=lenConfig[1];
            minLength=lenConfig[0];
        }
        else{
            maxLength=lenConfig[0];
            minLength=lenConfig[0];
        }
        if(length>maxLength || length<minLength)
            return false;
    }
    return true;
};

var validateNumeric=function(config,value){
    if(config.numeric===true){
        return /^\d+$/.test(value);
    }
    return true;
}

var validateBoolean=function(config,value){
    if(config.boolean===true){
        return typeof value=='boolean';
    }
    return true;
}

var validateOptions=function(config,value){
    if(config.options!=null){
        return config.options.indexOf(value) != -1;
    }
    return true;
}

var validateRegexp=function(config,value){
    if(config.regexp!=null){
        return config.regexp.test(value);
    }
    return true;
}

exports.setOutput=function(errcode,data,errmsg){
    if(typeof data=='string'){
        errmsg=data;
        data=null;
    }
    var result={};
    result.errcode=errcode;
    if(errcode!=0){
        result.errmsg=errmsg;
    }
    if(data!=null && data.length!=0){
        for (var name in data){
            result[name]=data[name];
        }
    }
    return result;
};