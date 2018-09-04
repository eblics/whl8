<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Charts extends OppController {
    public function __construct() {
        parent::__construct();
        $this->load->model('Charts_setting_model', 'charts');
    }
	public function index() {
        $title = "保镖";
		$this->load->view('charts');
	}
    /**
     * 报表管理
     */
    public function manage() {
        $res = $this->charts->merchants();
        if(!empty($res)){
            $this->ajaxResponse([$res],'success',0);
        }else{
            $this->ajaxResponse([],'',1);
        }
    }
	
}