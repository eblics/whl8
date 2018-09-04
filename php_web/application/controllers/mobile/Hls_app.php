<?php
/**
 * 应用界面入口控制器
 *
 * @author shizq <shizhiqiang@acctrue.com>
 */
class Hls_app extends Mobile_Controller {

	// -----------------------------------------
	// 获取当前用户
    public function get_member() {
    	$mchId = $this->input->get('mch_id');
    	$appPath = $this->input->get('app_path');
    	$this->load->model('Hls_app_model', 'hls_app');
        $currentUser = $this->getCurrentUser($mchId);
        $appInst = $this->hls_app->getAppInst($mchId, $appPath);
        $member = $this->hls_app->getMember($currentUser->openid);
        $member->app_config = $appInst->config;
        $this->ajaxResponseSuccess($member);
    }

    /**
     * 捞红包应用
     * @param string $pageName 页面名称 比如：/hls_app/fishing/logs -> views/fishing/logs.php
     * @return view
     */
    public function fishing($pageName = NULL) {
        $commonUser = $this->getCommonUser();
    	$mchId = $this->input->get('mch_id');
        if (! isset($mchId)) {
            $mchId = $this->getCurrentMchId();
        }
    	if (! isset($mchId)) {
    		$this->showErrorPage('商户编号不存在');
    	}
    	$user = $this->getCurrentUser($mchId);
    	if (! isset($pageName)) {
    		$this->load->view('fishing/index');
    	} else {
    		if (file_exists(VIEWPATH . 'fishing/'. $pageName . '.php')) {
    			$this->load->view('fishing/'. $pageName);
    		} else {
    			show_404();
    		}
    	}
    }

    /**
     * 欢乐扫应用API
     * @param string $apiName 接口名称
     * @return json
     */
    public function api($apiName = NULL) {
        $commonUser = $this->getCommonUser();
    	$user = $this->getCurrentUser($this->getCurrentMchId());
    	if (! isset($apiName)) {
    		$this->ajaxResponseFail('403 Forbidden', 403, 403);
    	} else if ($apiName == 'fishing.throw_bomb') {
    		// 扔炸弹
    		$this->throw_bomb($user);
    	} else if ($apiName == 'fishing.extract_redpacket') {
    		// 捞红包
    		$this->extract_redpacket($user);
    	} else if ($apiName == 'fishing.count_box') {
    		// 统计池子中的红包数量
    		$this->count_box($user);
    	} else if ($apiName == 'fishing.open_box') {
    		// 打开箱子看看是红包还是炸弹
            $this->open_box($user);
    	} else if ($apiName == 'fishing.logs') {
    		// 记录
            $this->logs($user);
    	}  else {
    		// 未知的api接口
    		$this->ajaxResponseFail('404 Not Found', 404, 404);
    	}
    }

    /*
	 * 扔炸弹API    
     */
    private function throw_bomb($user) {
    	$amount = $this->input->post('amount');
    	if (empty($amount) || $amount <= 0) {
    		$this->ajaxResponseOver('额度必须大于零');
    	}
    	$this->load->model('Group_fishing_model', 'fishing');
        $this->fishing->throwBomb($user, $amount);
        $this->ajaxResponseSuccess();
    }

    /*
	 * 捞红包API    
     */
    private function extract_redpacket($user) {
    	$rand = rand(0, 2);
    	if ($rand == 0) {
    		$this->ajaxResponseOver('什么也没捞到');
    	}
		$this->load->model('Group_fishing_model', 'fishing');
		$boxId = $this->fishing->extractRedpacket($user);
		if (isset($boxId)) {
			$this->ajaxResponseSuccess($boxId);
		} else {
			$this->ajaxResponseFail('什么也没捞到');
		}
    }

    /**
     * 打开箱子Api
     */
    private function open_box($user) {
        $boxId = $this->input->post('box_id');
        if (! isset($boxId)) {
            $this->ajaxResponseOver('找不到该箱子');
        }
        $this->load->model('Group_fishing_model', 'fishing');
        $result = $this->fishing->openBox($user, $boxId);
        $this->ajaxResponseSuccess($result);
    }

    /*
	 * 统计池子中的API数量
     */
    private function count_box($user) {
		$this->load->model('Group_fishing_model', 'fishing');
		$result = $this->fishing->countBox($user);
		$this->ajaxResponseSuccess($result);
    }

    /*
     * 获取记录
     */
    private function logs($user) {
        $page = $this->input->get('page');
        if (! isset($page) || ! is_numeric($page) || $page < 1) {
            $page = 1;
        }
        $this->load->model('Group_fishing_model', 'fishing');
        $logs = $this->fishing->logs($user, $page);
        $this->ajaxResponseSuccess($logs);
    }

}