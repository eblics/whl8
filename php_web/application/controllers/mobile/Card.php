<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 * 
 */
class Card extends Mobile_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Merchant_model', 'merchant_model');
        $this->load->model('User_model', 'user_model');
        $this->load->library('common/common_lib');
        $this->load->library('common/common_login');
    }

    /**
     * @deprecated please use /user/cards
     */
    public function account($mchId = NULL) {
        if (! isset($mchId)) {
            redirect('/user/cards');
        } else {
            redirect('/user/cards?mch_id=' . $mchId);
        }
    }

    // ----------------------------------------
    // 用户线上兑换乐券
    // public function settle($cardId, $addressId = NULL) {
    //     $this->getCommonUser();
    //     $currentUser = $this->getCurrentUser($this->getCurrentMchId());
    //     $this->load->model('Prize_model', 'prize');
    //     try {
    //         $resp = $this->prize->settleCards($currentUser->id, $cardId, $addressId);
    //         $this->session->settle_result = [
    //             'platform'   => $resp['settleInfo']['platform'],
    //             'amount'     => $resp['settleInfo']['amount'],
    //             'title'      => '兑换结果',
    //             'event_time' => $resp['settleInfo']['event_time'],
    //             'card_title' => $resp['settleInfo']['card_title'],
    //             'type'       => $resp['settleInfo']['type'],
    //             'online'     => $resp['settleInfo']['online'],
    //             'mall_id'    => $resp['settleInfo']['mall_id'],
    //         ];
    //         $this->ajaxResponseSuccess($resp['settleResult']);
    //     } catch (Exception $e) {
    //         $this->ajaxResponseFail($e->getMessage(), $e->getCode());
    //     }
    // }

    // ----------------------------------------
    // 用户线上兑换乐券成功详细界面
    // public function settle_result() {
    //     $settle_result = $this->session->settle_result;
    //     if (! isset($settle_result)) {
    //         show_404();
    //     } else {
    //         $this->load->view('settle_result', $settle_result);
    //     }
    // }

    // ----------------------------------------
    // 用户兑换乐券收货地址选择界面
    // public function choice_address() {
    //     $this->load->view('settle_address', []);
    // }

    // ----------------------------------------
    // 用户兑换乐券收货地址添加界面
    // public function edit_address() {
    //     $this->load->view('edit_address', []);
    // }

    // ----------------------------------------
    // 用户线上兑换乐券，之前的兑换已作废
    public function exchange_cards() {
        $this->getCommonUser();
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $targetId = $this->input->post('target_id');
        $ifGroupCard = $this->input->post('if_group_card');
        $this->load->model('Prize_model', 'prize');
        try {
            if ($ifGroupCard) {
                // 卡组兑换
                $resp = $this->prize->settleGroupCards($currentUser->id, $targetId);
            } else {
                // 单卡兑换
                $resp = $this->prize->settleSingleCards($currentUser->id, $targetId);
            }
            $this->ajaxResponseSuccess($resp);
        } catch (Exception $e) {
            $this->ajaxResponseFail($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取某个券组中所有的乐券
     * @return json
     */
    public function group_cards() {
        $groupId = $this->input->get('group_id');
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $this->load->model('Card_model', 'card');
        $cards = $this->card->getUserGroupCards($groupId, $currentUser->id);
        $this->ajaxResponseSuccess($cards);
    }
}
