<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ajax 数据返回格式
 */
if (! function_exists('ajax_resp')) {
	function ajax_resp($data = ['status' => 0], $msg = NULL, $errcode = 0) {
		return json_encode(['data' => $data, 'errmsg' => $msg, 'errorMsg' => $msg, 'errorCode' => $errcode, 'errcode' => $errcode]);
	}
}
