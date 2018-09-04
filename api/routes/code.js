var express = require('express');
var router = express.Router();

var api = require('../untilities/api');
var db = require('../untilities/database');
var config = require('../untilities/config');

router.post('/version/:versionno', function(req, res, next) {
    
    var sql='select versionNum,mchCodeLen,serialLen,validLen from code_version where versionNum=?';
    db.query(sql,[req.params.versionno],function(rows){
        if(rows.length==0){
            res.json(api.setOutput(2,'没有数据'));
            return;
        }
        var version=rows[0];
        var versionInfo={
            version:version.versionNum,
            mch_code_len:version.mchCodeLen,
            serial_len:version.serialLen,
            valid_len:version.validLen,
            prefix:config.getConfig().code_prefix
        };
        res.json(api.setOutput(0,versionInfo));
    });
});

module.exports = router;
