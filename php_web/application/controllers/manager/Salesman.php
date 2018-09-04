<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 业务员管理控制器
 *
 * @author shizq
 */
class Salesman extends MerchantController {

    // 业务员列表界面
    public function index() {
        $this->load->view('salesman_lists', []); 
    }
    
    // 添加业务员界面
    public function create() {
        $viewData['edit'] = FALSE;
        $this->load->view('salesman_edit', $viewData);
    }

    // 编辑业务员界面
    public function edit() {
        $salesmanId = $this->input->get('id');
        $mchId = $this->session->mchId;
        $this->load->model('Salesman_model', 'salesman');
        $salesman = $this->salesman->getSalesman($salesmanId, $mchId);
        $viewData['edit'] = TRUE;
        $viewData['salesman'] = $salesman;
        $this->load->view('salesman_edit', $viewData);
    }

    // 获取业务员列表
    // path /salesman/get
    // method get
    public function get() {
        $mchId = $this->session->mchId;
        $this->load->model('Salesman_model', 'salesman');
        $salesmans = $this->salesman->listSalesman($mchId);
        $this->output->set_content_type('application/json')->set_output(ajax_resp($salesmans));
    }

    // 添加业务员
    // path /salesman/store
    // method post
    // params {
    //  realname: 业务员姓名
    //  mobile: 业务员手机号
    //  id_card_no: 业务员姓名
    // }
    public function store() {
        $mchId = $this->session->mchId;
        $realName = $this->input->post('realname');
        $mobile = $this->input->post('mobile');
        $idCardNo = $this->input->post('id_card_no');
        // 数据校验
        if (mb_strlen($realName) < 2 || mb_strlen($realName) > 8) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '姓名格式不正确', 1));
            return;
        }
        if (preg_match('/^1[34578]\d{9}$/', $mobile) === 0 || empty($mobile)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请输入有效的手机号', 1));
            return;
        }
        if (preg_match('/^[0-9]{17}[0-9Xx]$/', $idCardNo) === 0 || empty($idCardNo)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请输入有效的身份证号', 1));
            return;
        }
        $salesman = [
            'mchId' => $mchId,
            'realName' => $realName,
            'mobile' => $mobile,
            'idCardNo' => $idCardNo
        ];
        $this->load->model('Salesman_model', 'salesman');
        try {
            $this->salesman->saveSalesman($salesman);
            $this->output->set_content_type('application/json')->set_output(ajax_resp());
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, $e->getMessage(), $e->getCode()));
        }
    }

    // 修改业务员
    // path /salesman/update
    // method post
    // params {
    //  salesman_id: 业务员编号
    // }
    public function update() {
        $mchId = $this->session->mchId;
        $salesmanId = $this->input->post('salesman_id');
        $realName = $this->input->post('realname');
        $mobile = $this->input->post('mobile');
        $idCardNo = $this->input->post('id_card_no');
        // 数据校验
        if (mb_strlen($realName) < 2 || mb_strlen($realName) > 8) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '姓名格式不正确', 1));
            return;
        }
        if (preg_match('/^1[34578]\d{9}$/', $mobile) === 0 || empty($mobile)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请输入有效的手机号', 1));
            return;
        }
        if (preg_match('/^[0-9]{17}[0-9Xx]$/', $idCardNo) === 0 || empty($idCardNo)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请输入有效的身份证号', 1));
            return;
        }
        $salesman = [
            'mchId' => $mchId,
            'realName' => $realName,
            'mobile' => $mobile,
            'idCardNo' => $idCardNo
        ];
        $this->load->model('Salesman_model', 'salesman');
        try {
            $this->salesman->saveSalesman($salesman, $salesmanId);
            $this->output->set_content_type('application/json')->set_output(ajax_resp());
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, $e->getMessage(), $e->getCode()));
        }
    }

    // 删除业务员
    // path /salesman/del
    // method post
    // params {
    //  salesman_id: 业务员编号
    // }
    public function del() {
        $salesmanId = $this->input->post('salesman_id');
        $this->load->model('Salesman_model', 'salesman');
        try {
            $realName = $this->salesman->delSalesman($salesmanId);
            $this->load->library('log_record');
            $logInfo['info'] = '删除业务员';
            $logInfo['id'] = $salesmanId;
            $logInfo['realName'] = $realName;
            $logInfo['op'] = $this->log_record->Delete;
            $this->log_record->addLog($this->session->userdata('mchId'), $logInfo, $this->log_record->Salesman);
            $this->output->set_content_type('application/json')->set_output(ajax_resp());
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, $e->getMessage(), $e->getCode()));
        }
    }

    // 锁定或解锁业务员
    // path /salesman/freeze
    // method post
    // params {
    //  salesman_id: 业务员编号
    //  lock: 0 解锁，2 锁定
    // }
    public function freeze() {
        $salesmanId = $this->input->post('salesman_id');
        $lock = $this->input->post('lock');
        try {
        	$this->load->model('Salesman_model', 'salesman');
            $realName = $this->salesman->freezeSalesman($salesmanId, $lock);
            $this->load->library('log_record');
            if ($lock) {
                $logInfo['info'] = '锁定用户';
                $logInfo['id'] = $salesmanId;
                $logInfo['realName'] = $realName;
                $logInfo['op'] = $this->log_record->Lock;
            } else {
                $logInfo['info'] = '解锁用户';
                $logInfo['id'] = $salesmanId;
                $logInfo['realName'] = $realName;
                $logInfo['op'] = $this->log_record->Unlock;
            }
            $this->log_record->addLog( $this->session->userdata('mchId'), $logInfo, $this->log_record->Salesman);
            $this->output->set_content_type('application/json')->set_output(ajax_resp());
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, $e->getMessage(), $e->getCode()));
        }
    }

}