var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');

var config = require('./untilities/config').getConfig();
var security = require('./untilities/security');
var api = require('./untilities/api');

var test = require('./routes/test');
var device = require('./routes/device');
var order = require('./routes/order');
var salesman = require('./routes/salesman');
var batch = require('./routes/batch');
var code = require('./routes/code');
var machineorder = require('./routes/machineorder');
var machinebatch = require('./routes/machinebatch');
var machinedevice = require('./routes/machinedevice');

var app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');


// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json({limit: '36mb'}));
app.use(bodyParser.urlencoded({ extended: false }));
//app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use(function(req, res, next) {
  if(config.cors_domain!=null){
    res.setHeader('Access-Control-Allow-Origin', config.cors_domain);
    res.setHeader('Access-Control-Allow-Headers', 'x-requested-with,content-type');
  }
  next();
});
app.use('/test', test);
app.use('/machine/app',machinedevice);
app.use('/machine',security.machinecheck);
app.use('/machine/order', machineorder);
app.use('/machine/batch',machinebatch);
app.use('/app', device);
app.use('/', security.check);
app.use('/order', order);
app.use('/salesman/statement', salesman);
app.use('/batch', batch);
app.use('/code', code);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handlers

// development error handler
// will print stacktrace
if (config.is_print_stacktrace === true) {
  app.use(function(err, req, res, next) {
    //res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  res.json(api.setOutput(9, err.message));
});

module.exports = app;

process.on('uncaughtException', function (err) {
  console.log(err);
});
