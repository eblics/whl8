<?php
/**
 * Wusu助威H5控制器
 *
 * @author shizq <shizhiqiang@acctrue.com>
 */
class Wusu extends Mobile_Controller {

    const MCH_ID = 173;

    public function index() {
        $mchId = $this->getEnvMchId();
        $currentUser = $this->getCurrentUser($mchId);
        if (isset($currentUser->subscribe) && ($currentUser->subscribe === '1' || $currentUser->subscribe === 1)) {
            $this->load->model('Personalinfo_model', 'personalinfo');
            $mallId = $this->personalinfo->mchid_to_mallid($mchId);
            $viewData = ['currentUser' => $currentUser, 'mall_id' => $mallId];
            $this->load->view('wusu/index', $viewData);
        } else {
            session_destroy();
            $this->load->view('wusu/zhuwei/guanzhu');
        }

    }

    /**
     * 乌苏助威H5首页
     * @return view
     */
    public function zhuwei() {
        $mchId = $this->getEnvMchId();
        $currentUser = $this->getCurrentUser($mchId);
        $viewData = [
            'currentUser' => $currentUser,
            'title' => '乌苏助威飞虎',
            'desc' => '以兄弟之名，邀你一起助威飞虎'
        ];
        $this->load->model('Wusu_model', 'wusu');
        $config = $this->wusu->getConfig($currentUser->openid);
        if (isset($config)) {
            $viewData['title'] = $config->share_title;
            $viewData['desc'] = $config->share_desc;
        }
        $this->load->view('wusu/zhuwei/index', $viewData);
    }

    public function api($apiName = NULL) {
        if (! isset($apiName)) {
            $this->ajaxResponseOver('403 Forbbiden.', 403, 403);
        } else if ($apiName === 'ranking.get') {
            $this->getRanking();
        } else if ($apiName === 'prize.try') {
            $this->tryPrize();
        } else if ($apiName === 'user.save') {
            $this->saveUser();
        } else {
            $this->ajaxResponseOver('404 Not Found.', 404, 404);
        }
    }

    private function getRanking() {
        $mchId = $this->getEnvMchId();
        $badge = $this->input->get('badge');
        if (empty($badge) || mb_strlen($badge) > 8) {
            $this->ajaxResponseOver('请输入一个不多于8个字的助威语句');
        }
        $currentUser = $this->getCurrentUser($mchId);
        $this->load->model('Wusu_model', 'wusu');
        $ranking = $this->wusu->getRanking($currentUser->openid);
        $this->ajaxResponseSuccess(['ranking' => round(($ranking + 1) / 2), 'badge' => $badge]);
    }

    private function tryPrize() {
        $mchId = $this->getEnvMchId();
        $currentUser = $this->getCurrentUser($mchId);
        try {
            $this->load->model('Wusu_model', 'wusu');
            $prizeType = $this->wusu->tryPrize($currentUser->openid);
            if ($prizeType) {
              $this->ajaxResponseSuccess(['prize' => $prizeType]);
            } else {
              $this->ajaxResponseSuccess(['prize' => 0]);
            }
        } catch (Exception $e) {
            $this->ajaxResponseOver($e->getMessage());
        }
    }

    private function saveUser() {
        $realname = $this->input->post('realname');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');
        if (mb_strlen($realname) < 2 || mb_strlen($realname) > 8) {
            $this->ajaxResponseOver('请输入有效的姓名');
        }
        if (mb_strlen($mobile) !== 11) {
            $this->ajaxResponseOver('请输入有效的手机号');
        }
        if (empty($address)) {
            $this->ajaxResponseOver('请输入有效的收货地址');
        }
        $mchId = $this->getEnvMchId();
        $currentUser = $this->getCurrentUser($mchId);
        $requestParams['realname'] = $realname;
        $requestParams['mobile'] = $mobile;
        $requestParams['address'] = $address;
        try {
            $this->load->model('Wusu_model', 'wusu');
            $this->wusu->saveUser($currentUser->openid, $requestParams);
            $this->ajaxResponseSuccess();
        } catch (Exception $e) {
            $this->ajaxResponseOver($e->getMessage());
        }

    }

    private function getEnvMchId() {
        if (! isProd()) {
            $mchId = 0;
        } else {
            $mchId = self::MCH_ID;
        }
        return $mchId;
    }

}
