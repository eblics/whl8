var app         = require('express')();
app.post('/test',function(req, res, next) {
	console.log('work');
	res.send('work');
});

var server = app.listen(3002, function () {
    var host = '127.0.0.1';
    var port = 3002;
    console.log('rpt server started at http://%s:%s', host, port);
});

process.on('uncaughtException', function (err) {
  console.log(err);
});
