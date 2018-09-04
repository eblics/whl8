<?php
/**
 * 应用登录控制器
 *
 * @author shizq <shizhiqiang@acctrue.com>
 */
class Login extends Mobile_Controller {
	
	// ------------------------------
	// 登录入口
	public function index() {
		$this->load->model('Merchant_model', 'merchant_model');
        $this->load->model('Hls_app_model', 'hls_app');
		$mchId = $this->input->get('mch_id');
		$from = $this->input->get('hls_app');
        $merchant = $this->merchant_model->get($mchId);
        if (! isset($merchant)) {
            $this->load->view('error', ['errmsg' => '没有这个商户']);
        } else {
            $this->getCurrentUser($mchId);
            redirect($from);
            exit();
        }
       
	}

    public function member() {
        $wxUser = $this->getCurrentMember(-1);
        var_dump($wxUser);
    }
}