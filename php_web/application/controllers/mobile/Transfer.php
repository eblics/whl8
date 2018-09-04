<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends Mobile_Controller {

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
			$this->load->view('transfer', $data);
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
		$this->load->view('transfer_set', $data);
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
			$this->load->view('wait_confirm', $data);
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
	 * 启动扫一扫转移乐券
	 * 
	 */
	public function start_transfer($mch_id = NULL) {
		$this->getCommonUser();
        if (! isset($mch_id)) {
            $this->load->view('error',['errmsg'=>'没有这个商户']);
            return;
        }
		
        $currentUser = $this->getCurrentUser($mch_id);
        $this->load->model('Merchant_model', 'merchant');
        $merchant = $this->merchant->get($currentUser->mchId);

        $data=['appId'=>$merchant->wxAppId,'appSecret'=>$merchant->wxAppSecret];
        $this->load->library('weixin_jssdk',$data);
        $signPackage = $this->weixin_jssdk->GetSignPackage();
        $signPackage['appId']=$merchant->wxAppId;
        // Copy from Code.php scan_by_jssdk end
        $signPackage['deal_with_js'] = true;
        $signPackage['mch_id'] = $mch_id;
		$this->load->view('scan_by_jssdk', $signPackage);
	}

	/**
	 * 标记转移票据已被扫描
	 * 
	 */
	public function mark_scan() {
		$this->getCommonUser();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			show_404();
		} else {
			$ticket = $this->input->post('ticket');
            $currentUser = $this->getCurrentUser($this->getCurrentMchId());
			$user_id = $this->session->userdata('userid');
			try {
				$this->load->model('Transfer_model', 'transfer');
				$result = $this->transfer->tag_scaned($ticket, ROLE_CONSUMER, $currentUser->id);
				$this->output->set_content_type('application/json')->set_output(ajax_resp($result));
			} catch (Exception $e) {
				$this->output->set_content_type('application/json')->set_output(ajax_resp([], $e->getMessage(), 1));
			}
		}
	}

	// --------------------------------------------
	// 消费者兑换转移记录界面
	public function logs() {
		$this->getCommonUser();
		$mchId = $this->input->get('mch_id');
        $currentUser = $this->getCurrentUser($mchId);
        $this->load->model('Transfer_model', 'transfer');
		$data['transfers'] = $this->transfer->get_trans_log($currentUser->id, ROLE_CONSUMER, TRUE);
		$data['title'] = '乐券明细';
		$data['v'] = 1.1;
		$this->load->view('translog', $data);
	}

	// --------------------------------------------
	// 获取消费者的兑换转移记录
	public function fetch_logs($role_str = 'consumer') {
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
		if ($role_str == 'waiter') {
			$waiter = $this->getCurrentWaiter($mchId);
			$transfers = $this->transfer->get_trans_log($waiter->id, ROLE_WAITER, TRUE, $queryType, $page, $pageSize);
		} else if ($role_str == 'salesman') {
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
