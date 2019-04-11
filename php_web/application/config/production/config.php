<?php
/**
 * dev环境配置
 */
$config['env']                  = 'dev';

// mobile
$config['mobile_url']           = 'http://m.whl8.cn/';
$config['cdn_m_url']            = $config['mobile_url'];
$config['webapp_url']           = $config['mobile_url'] . 'h5/';

// merchant
$config['mch_url']              = 'http://www.whl8.cn/';

// api
$config['api_url']              = 'http://api.whl8.cn/';

// shop
$config['shop_url']             = 'http://m.whl8.cn/';

$config['code_prefix']          = 'http://whl8.cn/c/';
$config['rpt_svr_url']          = 'http://whl8.cn:3002';

// wechat api
$config['wx3rd_appid']          = 'wxbaff6eca705707fc';
$config['wx3rd_appsecret']      = '7132bddefe3b032602e6358274eff8ed';
$config['wx3rd_token']          = '09e7a98b9457e3446a79f5d96e64c6e4';
$config['wx3rd_aeskey']         = '1WFp5xh7f0vwUWEEeKPfeqmA7hTrtsnlOkW7QFXOcEb';

// session
$config['sess_driver']          = 'redis';
$config['sess_save_path']       = 'tcp://localhost:6379?auth=lsl2001';
$config['sess_expiration']      = 7200;

// Redis配置
$config['redis']                = ['host' => 'localhost', 'port' => 6379,'password'=>'lsl2001'];
$config['ssl_file_path']        = '/var/www/whl8.cn/php_web/manager';

$config['log_threshold']       = 1;

// 队列服务器IP配置
$config['api3rd_server_ip']     = 'localhost';
$config['log_path']             = '/var/log/whl8.cn/';

$config['gm_mch_id']            = 255;
//$config['use_alidayu']          =1;
