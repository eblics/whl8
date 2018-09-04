<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (! function_exists('youzan_api')) {
	function youzan_api($mch_id, $method, $params = []) {
		require '../application/third_party/youzan/app.php';
		$CI = get_instance();
		$CI->db->select('youzanAppId, youzanAppSecret');
		$result = $CI->db->where('id', $mch_id)->get('merchants')->row_array();
		if ($result['youzanAppId'] && $result['youzanAppSecret']) {
			$youzan = new Youzan($result['youzanAppId'], $result['youzanAppSecret']);
			return $youzan->exec($method, $params);
		} else {
			return ['error_response' => ['msg' => '有赞平台未正确配置']];
		}
	}
}