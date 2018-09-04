<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 账户管理控制器
 *
 * @author shizq
 * 
 */
class Admin extends MerchantController {

	public function __construct() {
		parent::__construct();

		/**
		 * 只有管理员才有访问权限
		 * 
		 */
		$this->load->model('Admin_model', 'admin');
		$this->load->library('log_record');
	}

	/**
	 * 展示账户管理员列表界面
	 * 
	 * @return view
	 */
	public function index() {
		$code = $this->session->mchId;
		$this->load->view('admin_list',['mchCode'=>$code]);
	}

	/**
	 * 添加管理员界面
	 * 
	 * @return view
	 */
	public function add() {
		$mch_id = $this->session->mchId;
		$roles = $this->admin->get_roles($mch_id);
		$data = [
			'roles' => $roles,
			'mch_id' => $mch_id
		];
		$this->load->view('admin_edit', $data);
		
	}

	/**
	 * 编辑管理员界面
	 * 
	 * @return view
	 */
	public function edit() {
		$admin_id = $this->input->get('id');
		$mch_id = $this->session->mchId;
		$admin = $this->admin->get_admin($admin_id, $mch_id);
		$roles = $this->admin->get_roles($mch_id);
		
		if (! $admin) {
			show_404();
		} else {
			$data = [
				'roles' => $roles,
				'edit' => true,
				'mch_id' => $mch_id,
				'admin' => $admin
			];
			$this->load->view('admin_edit', $data);
		}
	}

	/**
	 * 获取管理员列表
	 * 
	 */
	public function get() {
		$mch_id = $this->session->mchId;
		$admins = $this->admin->list_admin($mch_id);
		$this->output->set_content_type('application/json')
			->set_output(ajax_resp($admins));
	}

	/**
	 * 添加一个新的管理员
	 * 
	 */
	public function save() {
		$admin_id = $this->input->post('admin_id');
		$realname = $this->input->post('realname');
		$password_pub = '123456'; // $this->input->post('password');
		$free = $this->input->post('freedom');
		$salt = mt_rand(100000, 999999);
		if($free == "true"){
			$freedom = 1;
		}elseif($free == "false"){
			$freedom = 0;
		}else{
			$freedom = 0;
		}
		$password = md5(md5($password_pub . $salt) . $salt);
		$phone_num = $this->input->post('mobile');
		$role = $this->input->post('role');
		if (! isset($role) || $role === '') {
			$this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请选择所属角色', 1));
			return;
		}
		$mch_id = $this->session->mchId;
		$now = time();
		$admin = [
			'realName' => $realname,
			'passwordPub' => $password_pub,
			'password' => $password,
			'salt' => $salt,
			'phoneNum' => $phone_num,
			'role' => $role,
			'mchId' => $mch_id,
			'createTime' => $now,
			'updateTime' => $now,
			'status' => 0,
			'noSms' => $freedom
		];
		try {
			if ($admin_id) {
				$admin['admin_id'] = $admin_id;
				$is_edit = TRUE;
				$result = $this->admin->save_admin($admin, TRUE);
			} else {
			    $is_edit = FALSE;
				$result = $this->admin->save_admin($admin);
			}
			if ($result) {

			    /*--------update -日志信息--------by ccz*/
			    try{
			        $logInfo = (array)$admin;
			        if($is_edit){
			            $logInfo['id'] = $admin_id;
    			        $logInfo['info'] = '修改了用户"'. $logInfo['realName'].'"的用户信息';
    			        $logInfo['op'] = $this->log_record->Update;
			        }else{
			            $logInfo['info'] = '新建用户';
			             $logInfo['id'] = $result;
			            $logInfo['op'] = $this->log_record->New;
			        }
			        $this->log_record->addLog( $this->session->userdata('mchId'),$logInfo,$this->log_record->Admin);
			    }catch(Exception $e){
			        log_message('error','mch_log_error:'.$e->getMessage());
			    }
			    /*-------记录日志---------end */
				$this->output->set_content_type('application/json')
					->set_output(ajax_resp());
			} else {
				$this->output->set_content_type('application/json')
					->set_output(ajax_resp([], '发生未知错误', 1));
			}
		} catch (Exception $e) {
			$this->output->set_content_type('application/json')
				->set_output(ajax_resp([], $e->getMessage(), 1));
		}
	}

	/**
	 * 删除管理员账户
	 * 
	 */
	public function del() {
		$admin_id = $this->input->post('admin_id');
		if ($this->admin->del_admin($admin_id)) {
			$this->output->set_content_type('application/json')
					->set_output(ajax_resp());
					/*--------update -日志信息--------by ccz*/
					try{
					    $logInfo['info'] = '删除用户';
					    $logInfo['id'] = $admin_id;
					    $logInfo['op'] = $this->log_record->Delete;
					    $this->log_record->addLog( $this->session->userdata('mchId'),$logInfo,$this->log_record->Admin);
					}catch(Exception $e){
					    log_message('error','mch_log_error:'.$e->getMessage());
					}
					/*-------记录日志---------end */
		} else {
			$this->output->set_content_type('application/json')
					->set_output(ajax_resp([], '删除失败', 1));
		}
	}

	/**
	 * 锁定、解锁管理员账户
	 * 
	 */
	public function freeze() {
		$admin_id = $this->input->post('admin_id');
		$lock = $this->input->post('lock');
		try {
			if ($this->admin->freeze_admin($admin_id, $lock)) {
			    /*--------update -日志信息--------by ccz*/
			    try{
			        if($lock){
    			        $logInfo['info'] = '锁定用户';
    			        $logInfo['id'] = $admin_id;
    			        $logInfo['op'] = $this->log_record->Lock;
			        }else{
			            $logInfo['info'] = '解锁用户';
			            $logInfo['id'] = $admin_id;
			            $logInfo['op'] = $this->log_record->Unlock;
			        }
			        $this->log_record->addLog( $this->session->userdata('mchId'),$logInfo,$this->log_record->Admin);
			    }catch(Exception $e){
			        log_message('error','mch_log_error:'.$e->getMessage());
			    }
			    /*-------记录日志---------end */
				$this->output->set_content_type('application/json')
					->set_output(ajax_resp());
			} else {
				$this->output->set_content_type('application/json')
				->set_output(ajax_resp([], '操作失败', 1));
			}
		} catch (Exception $e) {
			$this->output->set_content_type('application/json')
				->set_output(ajax_resp([], $e->getMessage(), 1));
		}
	}
}