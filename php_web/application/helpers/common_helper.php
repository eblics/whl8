<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 返回JSON数据
 */
if (!function_exists('send_json')) {
	function json_sender($data, $message = 'success', $code = 0) {
		$dataObj = array('content'=>$data, 'msg'=>$message, 'code'=>$code);
		echo json_encode($dataObj);
	}
	function send_json($data, $message = 'success', $code = 0) {
		$dataObj = array('content'=>$data, 'msg'=>$message, 'code'=>$code);
		echo json_encode($dataObj);
	}
}

// 过滤用户输入
if (!function_exists('filter')) {
	function filter($content) {
		$content = trim($content);
  		$content = stripslashes($content);
  		$content = htmlspecialchars($content);
  		return $content;
	}
}

// 空置判断
if (!function_exists('is_not_blank')) {
	function is_not_blank($val) {
  		return $val !== '' && $val !== null;
	}
}

// 取当前uri
if (!function_exists('get_current_router')) {
    function get_current_router($index='0')
    {
        $uri=get_instance()->uri;
        if($index==='0') {
            return $uri->uri_string;
        }else{
            $segments=$uri->rsegments;
			if (empty($segments)) {
				return 'index';
			}
            return $segments[$index];
        }
    }
}

//上传单个文件方法
if(!function_exists('upload_file')){
	function upload_file($filetype,$maxsize,$filepath){
        $relpath=getcwd().$filepath;
		debug($relpath);
        if(!is_dir($relpath)){
            mkdir($relpath,0777);
        }
        $config=[
            'encrypt_name'=>TRUE,
            'overwrite'=>TRUE,
            'max_size'=>$maxsize,
            'allowed_types'=>$filetype,
            'upload_path'=>$relpath
        ];
		$CI = & get_instance();
        $clientFileExt=strtolower($CI->input->post('fileExt'));
        $filetypeArr=explode('|',$filetype);
        if(!in_array($clientFileExt,$filetypeArr)){
            return 'exterror';
        }
        $clientFileSize=$CI->input->post('fileSize');
        if($clientFileSize>$maxsize) {
            return 'toolarge';
        }
        $userfile=$CI->input->post('userfile');
        if($userfile=='if'){
            return 'ifok';
        }
		$CI->load->library('upload');
		$CI->upload->initialize($config);
        if (!$CI->upload->do_upload('userfile')){
            $error = $CI->upload->display_errors();
            return json_encode($error);
        }else{
            $file_name = $CI->upload->data('file_name');
            if($file_name){
                return $filepath.'/'.$file_name;
            }else{
                return '';
            }
        }
	}
}

// 判断公众号是否授权
if (!function_exists('is_authorizer')) {
    function is_authorizer($type)
    {
        $CI = & get_instance();
        $mchId=$CI->session->userdata ( 'mchId' );
        $CI->load->model('merchant_model');
        $merchant=$CI->merchant_model->get($mchId);
        if(!$merchant){
            return TRUE;
        }
        if($type==1){
            if($merchant->wxAuthStatus!=1){
                return FALSE;
            }
        }
        if($type==2){
            if($merchant->wxAuthStatus_shop!=1){
                return FALSE;
            }
        }
        return TRUE;
    }
}

/**
 * 获取随机session key
 *
 */
if(!function_exists('session_keys')) {
    function session_keys()
    {
        $len = mt_rand(20,23);
        $result = '';
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $c_len = strlen($charset);
        for ($i=0;$i<$len;$i++) {
            $result .= $charset[mt_rand(0,$c_len-1)];
        }
        $result = trim($result);
        return $result;
    }
}

/**
 * 获取随机appid和appsecret
 * $type值只允许为1或者2,1为获取appid,2为获取appsecret
 */
if(!function_exists('random_apps')) {
    function random_apps($type)
    {
        if($type == 1){
            $len = 10;
        }elseif($type == 2){
            $len = mt_rand(20,23);
        }else{
            return false;
        }
        $appsresult = '';
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $c_len = strlen($charset);
        for ($i=0;$i<$len;$i++) {
            $appsresult .= $charset[mt_rand(0,$c_len-1)];
        }
        $appsresult = trim($appsresult);
        return $appsresult;
    }
}

// 本周开始时间
if (!function_exists('get_week_begin')) {
	function get_week_begin() {
		$defaultDate = date("Y-m-d");
		//1 表示每周星期一为开始日期 0表示每周日为开始日期
		$first=1;
		//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
		$w=date('w',strtotime($defaultDate));
		//获取本周开始日期，如果$w是0，则表示周日，减去 6 天
		$week_start=date('Y-m-d',strtotime("$defaultDate -".($w ? $w - $first : 6).' days'));
		return $week_start;
	}
}
// 本周结束时间
if (!function_exists('get_week_end')) {
	function get_week_end() {
		$defaultDate = date("Y-m-d");
		//1 表示每周星期一为开始日期 0表示每周日为开始日期
		$first=1;
		//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
		$w=date('w',strtotime($defaultDate));
		$week_start=date('Y-m-d',strtotime("$defaultDate -".($w ? $w - $first : 6).' days'));
		$week_end=date('Y-m-d',strtotime("$week_start +6 days"));
		return $week_end;
	}
}

// 用户权限判断
if (! function_exists('has_permission')) {
    function has_permission($key) {
	    debug('has_permission');
        $cmp = (in_array($key, $_SESSION['permission_modules']) || ($_SESSION['role'] == ROLE_ADMIN_MASTER));
	debug(var_export($_SESSION,True).' in '.'file:'.__FILE__.' line:'.__LINE__);
	return $cmp;
    }
}
