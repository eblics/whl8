var fs = require('fs');

var config;

var init=function(){
    var environment='development';
    if(process.env.NODE_ENV!=null)
        environment=process.env.NODE_ENV.trim();
    
    var allConfigs=JSON.parse(fs.readFileSync('./config.json','utf-8'));
    config=allConfigs['ALL'];
    var envConfig=allConfigs[environment];
    for(var index in envConfig){
        config[index]=envConfig[index];
    }
};

exports.getConfig=function(){
    if(config==null)
        init();
    return config;
};