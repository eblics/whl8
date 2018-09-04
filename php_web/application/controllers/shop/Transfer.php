<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends Shop_Controller {

	/**
	 * 用户点击兑换按钮进入该页面
	 * 此页面显示二维码供扫描
	 */
	public function index() {
		$obj_type = $this->input->get('obj_type');
		$obj_id = $this->input->get('obj_id');
		$role = $this->input->get('role');
		
		try {
			$this->load->model('Transfer_model', 'transfer');
			$result = $this->transfer->generate_ticket($obj_id, $obj_type);
			$this->session->ticket = $result;
			$this->session->role = $role;
			
			$data['title'] = '奖品兑换';
			$data['ticket'] = $result['ticket'];
			$this->load->view('page_transfer', $data);
		} catch (Exception $e) {
			$data['ticket'] = $e->getCode();
			$this->load->view('page_error', ['errmsg' => $e->getMessage(), 'title' => '提示']);
		}
	}
	
	/**
	 * 当用户的二维码被成功扫描之后进入该界面
	 */
	public function set() {
		$ticket = $this->session->ticket;		
		$role = $this->session->role;
		$currentUser = $this->getCurrentUserByRole($role);
		$this->load->model('Transfer_model', 'transfer');
		$card_info = $this->transfer->get_card_info($ticket['objId'], $currentUser->id, $role);
		
		$data['title'] = '设置转移数量';
		$data['prize_name'] = $card_info['title'];
		$data['total_num'] = $card_info['num'];
		$this->load->view('page_transfer_set', $data);
	}

	/**
	 * 用户在确认转移界面设置好转移数量点击确认按钮执行此Action
	 * 此处的请求需要传递num参数，也就是要转移的数量
	 */
	public function confirm() {
		$num = $this->input->post('num');
		$ticket = $this->session->ticket;
		$role = $this->session->role;
		try {
			$currentUser = $this->getCurrentUserByRole($role);
			$this->load->model('Transfer_model', 'transfer');
			$this->transfer->confirm_trans($ticket['ticket'], $num, $currentUser, $role);
			$this->output->set_content_type('application/json')->set_output(ajax_resp());
		} catch (Exception $e) {
			$this->output->set_content_type('application/json')->set_output(ajax_resp([], $e->getMessage(), 1));
		}
	}
	
	/**
	 * 扫描人扫完他人的二维码之后进入此界面等待被扫描者确认
	 */
	public function wait_confirm() {
		$role = $this->session->role;
		if (! $this->session->ticket) {
			redirect('/account?role=' . $role);
		} else {
			$data['title'] = '确认等待';
			$data['role'] = $role;
			$data['mch_id'] = $this->getCurrentMchId();
			$this->load->view('page_wait_confirm', $data);
		}
	}
	
	/**
	 * 检测扫码人是否扫描了自己的二维码
	 */
	public function check_scan() {
		$ticket = $this->session->ticket['ticket'];
		$this->load->model('Transfer_model', 'transfer');
		$scaned = $this->transfer->ticket_status($ticket);
		$this->output->set_content_type('application/json')->set_output(ajax_resp(['scaned' => $scaned]));
	}
	
	/**
	 * 检测转移人是否确认了转移
	 */
	public function check_confirm() {
		$ticket = $this->session->ticket;
		$this->load->model('Transfer_model', 'transfer');
		$confirmed = $this->transfer->ticket_confirmed($ticket);
		$this->output->set_content_type('application/json')->set_output(ajax_resp(['confirmed' => $confirmed]));
	}

	/**
	 * 服务员打开兑换(扫码)
	 */
	public function waiter() {
		// 获取企业id
		$mchId = $this->input->get('mch_id');
		if (!isset($mchId)) {
			$this->showErrPage('商户不存在');
			return;
		}

		$jssdkParams = $this->getJssdkParams($mchId);
		
		$data['title'] = '正在启动扫描...';
		$data['signPackage'] = $jssdkParams;
		$data['userinfo'] = $this->getCurrentWaiter($mchId);
		$data['role'] = ROLE_WAITER;
		$data['action'] = ACTION_TRANS;
		$data['mch_id'] = $mchId;
		$data['jsonp_url'] = $this->config->item('mobile_url');
		$this->load->view('page_scan', $data);
	}

	/**
	 * 业务员打开兑换(扫码)
	 */
	public function salesman() {
		// 获取企业id
		$mchId = $this->input->get('mch_id');
		if (!isset($mchId)) {
			$this->showErrPage('商户不存在');
			return;
		}

		$jssdkParams = $this->getJssdkParams($mchId);
		
		$data['title'] = '正在启动扫描...';
		$data['signPackage'] = $jssdkParams;
		$data['userinfo'] = $this->getCurrentSalesman($mchId);
		$data['role'] = ROLE_SALESMAN;
		$data['action'] = ACTION_TRANS;
		$data['mch_id'] = $mchId;
		$data['jsonp_url'] = $this->config->item('mobile_url');
		$this->load->view('page_scan', $data);
	}

	// --------------------------------------------
	// 获取服务员的兑换记录
	public function logs($role_str) {
		$data['title'] = '乐券明细';
		$data['role_str'] = $role_str;
		$this->showView('page_translog', $data);
	}

	// --------------------------------------------
	// 获取消费者的兑换转移记录
	public function fetch_logs($roleStr = 'consumer') {
		$queryType = $this->input->get('type');
		$page = $this->input->get('page');
		$pageSize  = $this->input->get('page_size');
		
		if (!isset($page)) {
			$page = 1;
		}
		if (!isset($pageSize)) {
			$pageSize = 10;
		} else {
			$pageSize = intval($pageSize);
		}
		$mchId = $this->getCurrentMchId();
		$this->load->model('Transfer_model', 'transfer');
		if ($roleStr == 'waiter') {
			$waiter = $this->getCurrentWaiter($mchId);
			$transfers = $this->transfer->get_trans_log($waiter->id, ROLE_WAITER, TRUE, $queryType, $page, $pageSize);
		} else if ($roleStr == 'salesman') {
			$salesman = $this->getCurrentSalesman($mchId);
			$transfers = $this->transfer->get_trans_log($salesman->id, ROLE_SALESMAN, TRUE, $queryType, $page, $pageSize);
		} else {
			$currentUser = $this->getCurrentUser($mchId);
			$transfers = $this->transfer->get_trans_log($currentUser->id, ROLE_CONSUMER, TRUE, $queryType, $page, $pageSize);
		}
		$this->output->set_content_type('application/json')->set_output(ajax_resp($transfers));
	}

	private function getCurrentUserByRole($role) {
		if ($role == ROLE_WAITER) {
			$currentUser = $this->getCurrentWaiter($this->getCurrentMchId());
		} else if ($role == ROLE_SALESMAN) {
			$currentUser = $this->getCurrentSalesman($this->getCurrentMchId());
		} else {
			$currentUser = $this->getCurrentUser($this->getCurrentMchId());
		}
		return $currentUser;
	}

}