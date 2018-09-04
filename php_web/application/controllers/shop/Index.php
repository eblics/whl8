<?php
class Index extends CI_Controller {

	public function jssdk() {
		$merchants = $this->db->get('merchants')->result();
		$this->load->model('Weixin_model', 'weixin');
		foreach ($merchants as $merchant) {
			if (! isset($merchant->wxAppId) 		|| 
				! isset($merchant->wxAppSecret) 	||
				empty($merchant->wxAppId) 			||
				empty($merchant->wxAppSecret) 		||
				$merchant->wxAuthStatus == BoolEnum::Yes) {
				continue;
			}
			print date("y-m-d H:i:s") . ' -> 正在刷新（消费者）：' . $merchant->name . PHP_EOL;
			$this->weixin->getJssdkTicket($merchant, TRUE, TRUE);
		}
		foreach ($merchants as $merchant) {
			if (! isset($merchant->wxAppId_shop) 		|| 
				! isset($merchant->wxAppSecret_shop) 	||
				empty($merchant->wxAppId_shop) 			||
				empty($merchant->wxAppSecret_shop) 		||
				$merchant->wxAuthStatus_shop == BoolEnum::Yes) {
				continue;
			}
			print date("y-m-d H:i:s") . ' -> 正在刷新（供应链）：' . $merchant->name . PHP_EOL;
			$this->weixin->getJssdkTicket($merchant, TRUE);
		}
		print date("y-m-d H:i:s") . ' -> 操作完成'. PHP_EOL;
	}
}