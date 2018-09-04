<?php
/**
 * test环境配置
 */
$config['env']                  = 'test';

// mobile
$config['mobile_url']           = 'http://test.m.lsa0.cn/';
$config['cdn_m_url']            = $config['mobile_url'];
$config['webapp_url']           = $config['mobile_url'] . 'h5/';

// merchant
$config['mch_url']              = 'http://test.www.lsa0.cn/';

// api
$config['api_url']              = 'http://test.api.lsa0.cn/';

// shop
$config['shop_url']             = 'http://test.shop.lsa0.cn/';

$config['code_prefix']          = 'http://test.lsa0.cn/c/';
$config['rpt_svr_url']          = 'http://localhost:3003';

// wechat api
$config['wx3rd_appid']          = 'wxa6c7708793938b7e';
$config['wx3rd_appsecret']      = '1f735ecc4a4e48d14a24061c970f6660';
$config['wx3rd_token']          = '09e7a98b9457e3446a79f5d96e64c6e4';
$config['wx3rd_aeskey']         = '1WFp5xh7f0vwUWEEeKPfeqmA7hTrtsnlOkW7QFXOcEb';

// session
$config['sess_driver']          = 'redis';
$config['sess_save_path']       = 'tcp://10.30.146.201:6379?auth=Acctrue886';
$config['sess_expiration']      = 7200;

// Redis配置
$config['redis']                = ['host' => '10.30.146.201', 'port' => 6379, 'password' => 'Acctrue886'];
$config['ssl_file_path']        = '/var/nfs4';

$config['log_threshold'] 		= 2;

// 队列服务器IP配置
$config['api3rd_server_ip']     = '118.190.99.6';
$config['log_path'] 			= '/var/log/lsa0.cn/';

$config['gm_mch_id']			= 103;