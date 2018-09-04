var co = require('co');

var corouter=function(router){
    this.get=function(url,callback){
        router.get(url, function(req, res, next) {
            co(callback(req, res, next));
        });
    };
    this.post=function(url,callback){
        router.post(url, function(req, res, next) {
            co(callback(req, res, next));
        });
    };
};

function corouterObject(router){
    return new corouter(router);
}

module.exports=corouterObject;