<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 卡券账户界面控制器
 * 
 * @author shizq
 */
class Account extends Shop_Controller {
	
	/**
	 * ---------------------------------
	 * 业务员修改用户账户信息
	 *
	 * @param string $realname 真实姓名
	 * @param string $mobile 手机号
	 * @param string $id_card_no 身份证号码
	 */
	public function update() {
		$realname = $this->input->post('realname');
		$mobile = $this->input->post('mobile');
		$id_card_no = $this->input->post('id_card_no');
		$smsCode = $this->input->post('sms_code');
		$salesman = $this->getCurrentSalesman($this->getCurrentMchId());
		if (! isset($realname) || ! isset($mobile) || ! isset($id_card_no)) {
			$this->ajaxResponseFail('参数错误');
			return;
		}
		$this->load->model('Account_model', 'account');
		try {
			$success = $this->account->save($salesman, $realname, $mobile, $id_card_no, $smsCode);
			if ($success) {
				$this->ajaxResponseSuccess();
			} else {
				$this->ajaxResponseFail('没有数据更新');
			}
		} catch (Exception $e) {
			$this->ajaxResponseFail($e->getMessage(), $e->getCode());
		}
		
	}

	/**
	 * ---------------------------------
	 * 业务员账户入口
	 */
	public function salesman() {
		$mchId = $this->input->get('mch_id');
		if (! isset($mchId)) {
			$this->showErrPage('商户不存在');
			return;
		}
		$salesman = $this->getCurrentSalesman($mchId);
		$this->load->model('Prize_model', 'prize');
		$this->load->model('Transfer_model', 'transfer');

		$cards = $this->prize->getCards($salesman->openid, ROLE_SALESMAN);
		$groupBonusCards = $this->prize->getGroupBonusCards($salesman->openid, ROLE_SALESMAN);
		
		$allowTransferCards = [];
		$allowTransferNum = 0;
		foreach ($cards as $card) {
			if ($card->allowTransfer) {
				$allowTransferNum += 1;
				$allowTransferCards[] = $card;
			}
		}
		$singleCards = [];
        $singleCardsNum = 0;
        foreach ($cards as $card) {
            if ($card->allowTransfer && $card->cardType == 0) {
                continue;
            }
            $singleCardsNum += 1;
            $singleCards[] = $card;
        }
        
        $data['cards'] = $cards;
        $data['singleCards'] = $singleCards;
        $data['singleCardsNum'] = $singleCardsNum;
		$data['allowTransferCards'] = $allowTransferCards;
		$data['groupBonusCards'] = $groupBonusCards;
		$data['allowTransferNum'] 	= $allowTransferNum;
		$data['transfers'] = $this->transfer->get_trans_log($salesman->id, ROLE_SALESMAN);
		$data['userinfo'] = $salesman;
		$data['role_str'] = 'salesman';
		$data['title'] = '我的账户-业务员';
		$data['role'] = ROLE_SALESMAN;
		$this->load->view('page_account', $data);
	}
	
	/**
	 * ---------------------------------
	 * 服务员账户入口
	 */
	public function waiter() {
		$mchId = $this->input->get('mch_id');
		if (! isset($mchId)) {
			$this->showErrPage('商户不存在');
			return;
		}
		$waiter = $this->getCurrentWaiter($mchId);
		$this->load->model('Prize_model', 'prize');
		$this->load->model('Transfer_model', 'transfer');
		
		$cards = $this->prize->getCards($waiter->openid, ROLE_WAITER);
		$groupBonusCards = $this->prize->getGroupBonusCards($waiter->openid, ROLE_WAITER);
		$allowTransferCards = [];
		$allowTransferNum = 0;
		foreach ($cards as $card) {
			if ($card->allowTransfer) {
				$allowTransferNum += 1;
				$allowTransferCards[] = $card;
			}
		}

		$singleCards = [];
        $singleCardsNum = 0;
        foreach ($cards as $card) {
            if ($card->allowTransfer && $card->cardType == 0) {
                continue;
            }
            $singleCardsNum += 1;
            $singleCards[] = $card;
        }
        
        $data['cards'] = $cards;
        $data['singleCards'] = $singleCards;
        $data['singleCardsNum'] = $singleCardsNum;
		$data['allowTransferCards'] = $allowTransferCards;
		$data['groupBonusCards'] = $groupBonusCards;
		$data['allowTransferNum'] = $allowTransferNum;
		$data['transfers'] = $this->transfer->get_trans_log($waiter->id, ROLE_WAITER);
		$data['userinfo'] = $waiter;
		$data['role_str'] = 'waiter';
		$data['title'] = '我的账户-服务员';
		$data['role'] = ROLE_WAITER;
		$this->load->view('page_account', $data);
	}
}