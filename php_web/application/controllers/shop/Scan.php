<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan extends Shop_Controller {

	/**
	 * 服务员进入扫一扫
	 * 
	 * @return view
	 */
	public function waiter() {
		// 获取企业id
		$mchId = $this->input->get('mch_id');

		// $commonUser = $this->getCommonUser();
		$waiter = $this->getCurrentWaiter($mchId);
		$jssdkParams = $this->getJssdkParams($mchId);
		
		$data['title'] = '正在启动扫描...';
		$data['signPackage'] = $jssdkParams;
		$data['userinfo'] = $waiter;
		$data['role'] = ROLE_WAITER;
		$data['action'] = ACTION_SCAN;
		$data['mch_id'] = $mchId;
		$data['jsonp_url'] = $this->config->item('mobile_url');
		$this->load->view('page_scan', $data);
	}
	
	/**
	 * 对扫码结果进行分析
	 * 
	 * @param string $lecode 乐码
	 */
	public function analyse($lecode) {
		// $commonUser = $this->getCommonUser();
		$role = $this->input->post('role');
		$action = $this->input->post('action');	

		$mchId = $this->getCurrentMchId();
		$this->load->model('Lecode_model', 'lecode');
		if ($role == RoleEnum::Waiter) {
			$currentWaiter = $this->getCurrentWaiter($mchId);
			if ($action == ScanActionEnum::Scan) {
				$this->session->unset_userdata('last_scan_code');
				$this->lecode->scan($lecode, $currentWaiter);
				$this->ajaxResponseSuccess(['type' => ScanActionEnum::Scan]);
				$this->session->set_userdata('last_scan_code', $lecode);
			} else {
				$this->lecode->scanTicket($lecode, $role, $currentWaiter->id);
				$this->ajaxResponseSuccess(['type' => ScanActionEnum::Transfer]);
			}
		} elseif ($role == RoleEnum::Salesman) {
			$currentSalesman = $this->getCurrentSalesman($mchId);
			$this->lecode->scanTicket($lecode, $role, $currentSalesman->id);
			$this->ajaxResponseSuccess(['type' => ScanActionEnum::Transfer]);
		} else {
			$this->ajaxResponseFail('PARAMS ERROR: SCAN_ANALYSE_UNKNOW_ROLE');
		}
	}

	public function test_scan($key = NULL, $wxOpenid = NULL, $commonOpenid = NULL) {
		$currentWaiter = $this->getCurrentWaiter(0);
		$this->load->view('test_scan');
	}
}
