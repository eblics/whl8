<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Wechat\Loader;

function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

if (! function_exists('info')) {
    function info($msg) {
        log_message('error', 'hls-' . $msg);
    }
}

if (! function_exists('debug')) {
    function debug($msg) {
        log_message('debug', 'hls-' . $msg);
    }
}

if (! function_exists('error')) {
    function error($msg) {
        log_message('error', 'hls-' . $msg);
    }
}


if (! function_exists('ifnull')) {
    function ifnull($var,$value) {
        return !isset($var)||empty($var)?$value:$var;
    }
}

if (! function_exists('ajax_resp')) {
    function ajax_resp($data = ['status' => 0], $msg = 'success', $errcode = 0) {
        return json_encode(['data' => $data, 'errmsg' => $msg, 'errcode' => $errcode]);
    }
}

if (! function_exists('get_real_ip')) {
    function get_real_ip(){
        $ip=false;
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ips=explode (',',$_SERVER['HTTP_X_FORWARDED_FOR']);
            if($ip){
                array_unshift($ips,$ip);
                $ip=FALSE;
            }
            for($i=0;$i<count($ips);$i++){
                if(!preg_match('/^(10│172.16│192.168)./i',$ips[$i])){
                    $ip=$ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
}

/**
 * 获取微信操作对象
 * @staticvar array $wechat
 * @param type $type
 * @return WechatReceive
 */
function & load_wechat($type = '') {
    static $wechat = array();
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        $CI = & get_instance();
        $CI->config->load('wechat', TRUE);
        $config = config_item('wechat')['wechat'];
        $config['cachepath'] = BASEPATH.'wechatcache/';
        $wechat[$index] = Loader::get($type, $config);
    }
    return $wechat[$index];
}


function getCircleAvatar($avatar, $circleAvatar, $r) {
    /**
     * @des 画一个正方形
     * @size 两个半径
     */
    $size = 2 * $r;
    $circle = new Imagick();
    $circle->newImage($size, $size, 'none');
    $circle->setimageformat('png');
    $circle->setimagematte(true);

    /**
     * @des 在矩形上画一个白色圆
     */
    $draw = new ImagickDraw();
    $draw->setfillcolor('#fff');
    $draw->circle($r, $r, $r, $size);
    $circle->drawimage($draw);

    /**
     * @des 裁剪头像成圆形
     */
    $imagick = new Imagick();
    $imagick->readImage($avatar);
    $imagick->resizeImage($size,$size,Imagick::FILTER_LANCZOS,1);
    $imagick->setImageFormat('png');
    $imagick->setimagematte(true);
    $imagick->cropimage($size, $size, 0, 0); // 修改裁剪属性
    $imagick->compositeimage($circle, Imagick::COMPOSITE_COPYOPACITY , 0, 0);
    $imagick->writeImage($circleAvatar);
    $imagick->destroy();
}


function isDev() {
    return config_item('env') === 'dev';
}

function isProd() {
	debug(var_export(config_item('env'),True));
    return config_item('env') === 'prod';
}
