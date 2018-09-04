<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Settle extends Shop_Controller {

	/**
	 * 核销首页，显示个人信息以及要核销的内容
	 */
	public function index()	{
		$mchId = $this->input->get('mch_id');
		if (! isset($mchId)) {
			$this->showErrPage('商户不存在');
			return;
		}
		$this->load->model('Account_model', 'account');
		$data['salesman'] = $this->account->getSalesman($this->getCurrentSalesman($mchId)->id);
		$data['title'] = '乐券核销';
		$this->showView('page_settle', $data);
	}
	
	/**
	 * 显示核销记录界面
	 */
	public function notes() {
		$this->showView('page_settle_notes', ['title' => '核销记录']);
	}

	/**
	 * 申请核销提交Action
	 */
	public function settles() {
		$this->load->model('Settle_model', 'settle');
		$this->load->model('Account_model', 'account');
		$salesman = $this->getCurrentSalesman($this->getCurrentMchId());
		try {
			$this->settle->settle($salesman);
			$data['title'] = '核销成功';
			$this->showView('page_settle_result', $data);
		} catch (Exception $e) {
			$this->showErrPage($e->getMessage());
		}
	}
	
	/**
	 * 获取核销的所有记录
	 */
	public function settle_notes() {
		$this->load->model('Settle_model', 'settle');
		$this->load->model('Account_model', 'account');
		$salesman = $this->getCurrentSalesman($this->getCurrentMchId());
		$page = $this->input->get('page') - 1;
		if ($page < 0) $page = 0;
		try {
			$notes = $this->settle->settleNotes($salesman->id, $page);
			$this->ajaxResponseSuccess($notes);
		} catch (Exception $e) {
			$this->ajaxResponseFail($e->getMessage(), $e->getCode());
		}
		
	}
	
	/**
	 * 获取可核销的所有乐券
	 */
	public function cards() {
		$this->load->model('Settle_model', 'settle');
		$this->load->model('Account_model', 'account');
		$salesman = $this->getCurrentSalesman($this->getCurrentMchId());
		$cards = $this->settle->listSalesmanCards($salesman->id);
		$this->ajaxResponseSuccess($cards);
	}
}

