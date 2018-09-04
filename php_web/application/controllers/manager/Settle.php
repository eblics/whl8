<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 业务员核销控制器
 *
 * @author shizq
 */
class Settle extends MerchantController {

    // 业务员核销列表界面
    public function index() {
        $mchId = $this->session->mchId;
        $this->load->model('Salesman_model', 'salesman');
        $salesmanLists = $this->salesman->listSalesman($mchId);
        $this->load->view('settle_lists', ['salesmanLists' => $salesmanLists]); 
    }

    // 获取业务员核销申请列表
    // path /settle/lists
    // method get
    public function lists() {
        $mchId      = $this->session->mchId;
        $start       = $this->input->get('start');
        $pageSize   = $this->input->get('length');
        $salesmanId = $this->input->get('salesman');
        $state      = $this->input->get('state');
        $search     = $this->input->get('search')['value'];
        $params = [
            'smId' => $salesmanId,
            'state' => $state,
            'search' => $search
        ];
        $this->load->model('Settle_model', 'settle');
        $settles = $this->settle->listSettles($mchId, $params, $start, $pageSize);
        $recordsTotal = $this->settle->getSettlesNum($mchId, $params);

        $jsonResponse = [
            'draw' => $this->input->get('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $settles,
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($jsonResponse));
    }

    // 根据核销编号获取核销的所有乐券
    // path /settle/cards/:statements_id
    // method get
    public function cards($statementsId = NULL) {
    	if (! isset($statementsId)) {
    		$this->output->set_content_type('application/json')->set_output(ajax_resp());
    		return;
    	}
    	$mchId = $this->session->mchId;
    	$this->load->model('Settle_model', 'settle');
    	$cards = $this->settle->listCardsByStatementsId($mchId, $statementsId);
    	$this->output->set_content_type('application/json')->set_output(ajax_resp($cards));
    }
    
    // 审核核销申请
    // path /settle/review
    // method post
    public function review() {
    	$statementId = $this->input->post('statement_id');
    	$pass = $this->input->post('pass');
    	$content = $this->input->post('content');
    	$mchId = $this->session->mchId;
    	$this->load->model('Settle_model', 'settle');
    	try {
    		$this->settle->reviewStatement($mchId, $statementId, $pass, $content);
    		$this->output->set_content_type('application/json')->set_output(ajax_resp());
    	} catch (Exception $e) {
    		$this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, $e->getMessage(), $e->getCode()));
    	}
    }

}