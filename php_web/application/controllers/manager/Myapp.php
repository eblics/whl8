<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Myapp extends MerchantController {

    public function __construct(){
        parent::__construct();
    }

	public function index() {
		redirect(config_item('mch_url') . '/myapp/lists');
	}

	public function lists() {
		$this->load->view('app_my', ['mch_id' => $this->session->mchId]);
	}

	// -----------------------------------
	// 编辑APP实例界面
	// path domain/myapp/edit/:app_inst_id
	// params {
	// 	$app_inst_id: 应用实例编号
	// }
	public function edit($app_inst_id = NULL) {
		if (! isset($app_inst_id)) {
 			show_404();
            return;
		}
        $this->load->model('Hls_app_model', 'hls_app');
        $mch_id = $this->session->mchId;
        $appinst = $this->hls_app->getAppInstById($app_inst_id);
        if ($appinst->mchId != $mch_id) {
            $this->ajaxResponse([], "应用$appinst->id不属于商户$mch_id", 1);
            return;
        }
        $appinst->config = json_decode($appinst->config);
        $appinst->config->inst_id = $app_inst_id;
        $this->load->view('app_inst_edit', ['appinst' => $appinst]);
	}

	// -----------------------------------
	// 获取企业所有应用
	// path domain/myapp/get
	public function get() {
		$this->load->model('Hls_app_model', 'hls_app');
		try {
			$mch_id = $this->session->mchId;
			$apps = $this->hls_app->getApps($mch_id);
			$this->ajaxResponse($apps);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// -----------------------------------
	// 获取企业某个应用的配置信息
	// path domain/myapp/show/:inst_id
	// params {
	// 	$inst_id: 应用实例编号
	// }
	public function show($inst_id = NULL) {
		if (! isset($inst_id)) {
			$this->ajaxResponse([], '应用不存在', 1);
		} else {
			try {
				$mch_id = $this->session->mchId;
				$this->load->model('Hls_app_model', 'hls_app');
	            $appinst = $this->hls_app->getAppInstById($inst_id);
	            if ($appinst->mchId != $mch_id) {
	                $this->ajaxResponse([], "应用$appinst->id不属于商户$mch_id", 1);
	                return;
	            }
	            $appinst->config = json_decode($appinst->config);
	            $this->ajaxResponse($appinst);
	        } catch (Exception $e) {
				$this->ajaxResponse([], $e->getMessage(), $e->getCode());
			}
		}
	}

	// -----------------------------------
	// 企业保存应用实例信息
	// path domain/myapp/save/:app_inst_id
	// params {
	// 	$app_inst_id: 应用实例编号
	// }
	public function save($app_inst_id = NULL) {
		$config = $this->input->post('config');
		if (! isset($app_inst_id)) {
			$this->ajaxResponse([], "应用不存在", 1);
            return;
		}
		try {
			$this->load->model('Hls_app_model', 'hls_app');
			$mch_id = $this->session->mchId;
            $appinst = $this->hls_app->getAppInstById($app_inst_id);
            if ($appinst->mchId != $mch_id) {
                $this->ajaxResponse([], "应用$appinst->id不属于商户$mch_id", 1);
                return;
            }
			$apps = $this->hls_app->saveInstInfo($app_inst_id, $config);
			$this->load->library('log_record');
			$logInfo = [
				'id'   => $app_inst_id,
				'info' => '配置了应用' . json_decode($appinst->config)->name,
				'op'   => $this->log_record->Config
			];
			$this->log_record->addLog($mch_id, $logInfo, $this->log_record->App);
			$this->ajaxResponse();
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// -----------------------------------
	// 删除一个应用实例
	public function del() {
		$app_inst_id = $this->input->post('app_inst_id');
		$this->load->model('Hls_app_model', 'hls_app');
		try {
			$mch_id = $this->session->mchId;
            $appinst = $this->hls_app->getAppInstById($app_inst_id);
            if ($appinst->mchId != $mch_id) {
                $this->ajaxResponse([], "应用$appinst->id不属于商户$mch_id", 1);
                return;
            }
			$this->hls_app->unApplyApp($app_inst_id);
			$this->load->library('log_record');
			$logInfo = [
				'id'   => $app_inst_id,
				'info' => '删除了应用' . json_decode($appinst->config)->name,
				'op'   => $this->log_record->Delete
			];
			$this->log_record->addLog($mch_id, $logInfo, $this->log_record->App);
			$this->ajaxResponse();
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// -----------------------------------
	// 启用应用实例
	public function enable() {
		$app_inst_id = $this->input->post('app_inst_id');
		$this->load->model('Hls_app_model', 'hls_app');
		try {
			$mch_id = $this->session->mchId;
            $appinst = $this->hls_app->getAppInstById($app_inst_id);
            if ($appinst->mchId != $mch_id) {
                $this->ajaxResponse([], "应用$appinst->id不属于商户$mch_id", 1);
                return;
            }
			$this->hls_app->enableAppInst($app_inst_id, TRUE);
			$this->load->library('log_record');
			$logInfo = [
				'id'   => $app_inst_id,
				'info' => '添加了应用' . json_decode($appinst->config)->name,
				'op'   => $this->log_record->Start
			];
			$this->log_record->addLog($mch_id, $logInfo, $this->log_record->App);
			$this->ajaxResponse();
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// -----------------------------------
	// 停用应用实例
	public function disable() {
		$app_inst_id = $this->input->post('app_inst_id');
		$this->load->model('Hls_app_model', 'hls_app');
		try {
			$mch_id = $this->session->mchId;
            $appinst = $this->hls_app->getAppInstById($app_inst_id);
            if ($appinst->mchId != $mch_id) {
                $this->ajaxResponse([], "应用$appinst->id不属于商户$mch_id", 1);
                return;
            }
			$this->hls_app->enableAppInst($app_inst_id, FALSE);
			$this->load->library('log_record');
			$logInfo = [
				'id'   => $app_inst_id,
				'info' => '停用了应用' . json_decode($appinst->config)->name,
				'op'   => $this->log_record->Stop
			];
			$this->log_record->addLog($mch_id, $logInfo, $this->log_record->App);
			$this->ajaxResponse();
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

    public function disableUsers($password = NULL, $dataFile = '/var/data.txt') {
    	if (is_null($password)) {
    		show_404();
    		exit();
    	}
    	if (! file_exists($dataFile)) {
    		exit('No data file.');
    	}
        $content = file_get_contents($dataFile);
        $openIdArr = explode("\r\n", $content);

        $resultSet = $this->db
            ->select("t1.id AS users_common_id, t2.openid")
            ->from('users_common AS t1')
            ->join('users_common_sub AS t2', 't2.parentId = t1.id')
            ->where('t1.commonStatus', 0)
            ->where_in('t2.openid', $openIdArr)
            ->get()->result();

        $idArr = [];
        foreach ($resultSet as $item) {
        	$idArr[] = $item->users_common_id;
        }

        if ($password == 'test') {
        	var_dump($idArr);
        	exit();
        } elseif ($password = '1qaz2wsx') {
        	if (empty($idArr)) {
        		print 'No user to handle.';
        		exit();
        	}
        	$this->db->set('commonStatus', 1)->where_in('id', $idArr)->update('users_common');
        	print $this->db->affected_rows() . ' items updated!';
        }
    }

}
