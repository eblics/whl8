var express = require('express');
var router = express.Router();

var api = require('../untilities/api');

/* GET home page. */
router.get('/get', function(req, res, next) {
    throw new Error('someting wrong');
    res.send('get');
});

router.post('/post', function(req, res, next) {
    throw new Error('someting wrong');
    res.send('post');
});

module.exports = router;
