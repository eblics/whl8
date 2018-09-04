<?php
class Myboard extends MerchantController {

	/**
	 * @deprecated 此方法已废弃，请使用/user?mch_id={$mchId}
	 */
    public function index($mchId = NULL) {
        if (isset($mchId)) {
        	redirect('/user?mch_id='. $mchId);
        } else {
            $this->showErrPage('你访问的页面不存在');
        }
    }
}