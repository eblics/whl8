<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends MerchantController {

	// ------------------------------------
	// APP应用列表界面
	public function index() {
		$this->load->view('app_lists');
	}

	// ------------------------------------
	// APP详细界面
	public function desc($app_id = NULL) {
		try {
			if (! isset($app_id)) {
				throw new Exception("你要找的应用不存在", 1);
			}
			$this->load->model('Hls_app_model', 'hls_app');
			$mch_id = $this->session->mchId;
			$app = $this->hls_app->getAppById($mch_id, $app_id);
			$recommend_apps = $this->hls_app->getRecommed($mch_id);
			$data = ['app' => $app, 'recommend_apps' => $recommend_apps];
			$this->load->view('app_desc', $data);
		} catch (Exception $e) {
			show_404();
		}
		
	}

	// ================================ Api ================================

	// ------------------------------------
	// 获取所有应用
	public function get() {
		$current_page = $this->input->get('current_page');
		$this->load->model('Hls_app_model', 'hls_app');
		try {
			$mch_id = $this->session->mchId;
			$apps = $this->hls_app->getAllApps($mch_id, $current_page);
			$this->ajaxResponse($apps);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// ------------------------------------
	// 生成支付订单
	public function create_order() {
		$app_id = $this->input->post('app_id');
		try {
			$mch_id = $this->session->mchId;
			$this->load->model('Hls_app_model', 'hls_app');
			// 判断是否需要支付
			$resp = $this->hls_app->generateWxPayOrder($app_id, $mch_id);
			$this->ajaxResponse($resp);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// ------------------------------------
	// App购买付款微信二维码
	public function get_pay_order($order_id) {
		try {
			$this->load->model('Hls_app_model', 'hls_app');
			$mch_id = $this->session->mchId;
			$data = $this->hls_app->getOrder($order_id, $mch_id);
			$data['order_id'] = $order_id;
			$this->ajaxResponse($data);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// ------------------------------------
	// 查询订单状态
	public function orderquery($order_id) {
		try {
			$this->load->model('Hls_app_model', 'hls_app');
			// 判断是否需要支付
			$resp = $this->hls_app->wxOrderquery($order_id);
			$appinst = $this->hls_app->getAppinstByOrderId($order_id);
			$this->load->library('log_record');
			$logInfo = [
				'id'   => $appinst->id,
				'info' => '购买了应用' . json_decode($appinst->config)->name,
				'op'   => $this->log_record->Buy
			];
			$this->log_record->addLog($appinst->mchId, $logInfo, $this->log_record->App);
			$this->ajaxResponse($resp);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
		
	}

	// ------------------------------------
	// 企业添加新的应用
	public function apply() {
		$app_id = $this->input->get('app_id');
		try {
			$this->load->model('Hls_app_model', 'hls_app');
			$mch_id = $this->session->mchId;
			$this->hls_app->applyApp($app_id, $mch_id);
			$app = $this->hls_app->getAppById($mch_id, $app_id);
			$this->ajaxResponse();
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// ------------------------------------
	// 如果企业添加的应用自己删除了，那么可以再次启用
	public function re_apply() {
		$app_id = $this->input->post('app_id');
		try {
			$this->load->model('Hls_app_model', 'hls_app');
			$mch_id = $this->session->mchId;
			$this->hls_app->reApplyApp($app_id, $mch_id);
			$app = $this->hls_app->getAppById($mch_id, $app_id);
			$this->load->library('log_record');
			$logInfo = [
				'id'   => $app_id,
				'info' => '安装了应用' . $app->name,
				'op'   => $this->log_record->Install
			];
			$this->log_record->addLog($mch_id, $logInfo, $this->log_record->App);
			$this->ajaxResponse();
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}
	
}