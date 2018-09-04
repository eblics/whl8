var mysql = require('mysql');
var config = require('./config');

var readPool = mysql.createPool(config.getConfig().database.read);
var writePool = mysql.createPool(config.getConfig().database.write);

var getPool=function(head){
    var text=head.toLowerCase();
    if(text=='select'){
        return readPool;
    }
    else if(text=='insert' || text=='update' || text=='delete'){
        return writePool;
    }
    else{
        throw new Error('unknown sql');
    }
};

exports.escape=function(value){
    return readPool.escape(value);
};

exports.query=function(sql,values,callback){
    if(typeof values=='function'){
        callback=values;
        values=null;
    }
    getPool(sql.substring(0,6)).query(sql, values, function(err, rows, fields) {
        if (err)
            console.log('[errorquery]:' + err);
            console.log('             ' + sql);
        if(callback)
            callback(rows);
    });
};

exports.querySync=function(sql,values){
    return new Promise(function (resolve, reject){
        exports.query(sql, values, function(rows) {
            resolve(rows);
        });
    });
};

exports.insert=function(tableName,data,unescapeData,callback){
    if(typeof unescapeData=='function'){
        callback=unescapeData;
        unescapeData=null;
    }
    var fields=[];
    var signs=[];
    var values=[];
    for(var name in data){
        fields.push(name);
        signs.push('?');
        values.push(data[name]);
    }
    for(var name in unescapeData){
        fields.push(name);
        signs.push(unescapeData[name]);
    }
    var sql='insert into '+tableName+'('+fields.join(',')+')values('+signs.join(',')+')';
    exports.query(sql, values, function(fields) {
        if(callback)
            callback(fields.insertId);
    });
};

exports.insertSync=function(tableName,data,unescapeData){
    return new Promise(function (resolve, reject){
        exports.insert(tableName,data,unescapeData,function(rows) {
            resolve(rows);
        });
    });
};