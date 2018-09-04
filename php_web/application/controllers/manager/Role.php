<?php
class Role extends MerchantController {

	public function index() {
		$this->load->view('role_list');
	}

	// ------------------------------------------
	// 获取角色列表
	// path /role/lists
	// method get
	public function lists() {
		$mchId = $this->session->userdata('mchId');
		$this->load->model('Admin_model', 'admin');
		$roles = $this->admin->listRole($mchId);
		$this->ajaxResponseSuccess($roles);
	}

	public function create() {
		$this->load->model('Admin_model', 'admin');
		$permissions = $this->admin->getPermissions();
		$data = [
			'edit' => FALSE,
			'permissions' => $permissions
		];
		$this->load->view('role_edit', $data);
	}

	// ------------------------------------------
	// 添加新角色
	// path /role/store
	// method post
	public function store() {
		$roleName = $this->input->post('role_name');
		$ids = $this->input->post('ids');
		$ids = json_decode($ids);
		if (empty($roleName) || mb_strlen($roleName, 'utf8') < 2 || mb_strlen($roleName, 'utf8') > 8) {
			$this->ajaxResponseFail('角色名长度不符合');
			return;
		}
		if (empty($ids)) {
			$this->ajaxResponseFail('请至少选择一种权限');
		} else {
			$this->load->model('Admin_model', 'admin');
			try {
				$mchId = $this->session->userdata('mchId');
				$roleId = $this->admin->addRole($mchId, $roleName, $ids);
				$this->load->library('log_record');
				$logInfo = [
					'roleId'	=> $roleId,
					'roleName' 	=> $roleName,
					'op'   		=> $this->log_record->Add
				];
				$this->log_record->addLog($this->session->userdata('mchId'), $logInfo, $this->log_record->Role);
				$this->ajaxResponseSuccess();
			} catch (Exception $e) {
				$this->ajaxResponseFail($e->getMessage());
			}
		}
	}

	public function edit($id = NULL) {
		if (! isset($id)) {
			show_404();
		} else {
			$this->load->model('Admin_model', 'admin');
			$permissions = $this->admin->getPermissions();
			$mchId = $this->session->userdata('mchId');
			$role = $this->admin->getRole($mchId, $id);
			if (! isset($role)) {
				show_404();
			} else {
				$rolePermissions = $this->admin->getRolePermission($id);
			}
			$data = [
				'edit' => TRUE,
				'role' => $role,
				'permissions' => $permissions,
				'role_permissions' => json_encode($rolePermissions)
			];
			$this->load->view('role_edit', $data);
		}
	}

	// ------------------------------------------
	// 修改角色
	// path /role/update
	// method post
	public function update($id) {
		$roleName = $this->input->post('role_name');
		$ids = $this->input->post('ids');
		$ids = json_decode($ids);

		$role_admin = isset($_SESSION['role']) ? $_SESSION['role'] : NULL;
		if ($role_admin !== NULL && intval($id) === intval($role_admin)) {
			$this->ajaxResponseFail('不能修改当前登录用户的权限');
			return;
		}

		if (empty($roleName) || mb_strlen($roleName, 'utf8') < 2 || mb_strlen($roleName, 'utf8') > 8) {
			$this->ajaxResponseFail('角色名长度不符合');
			return;
		}
		if (empty($ids)) {
			$this->ajaxResponseFail('请至少选择一种权限');
		} else {
			$this->load->model('Admin_model', 'admin');
			try {
				$mchId = $this->session->userdata('mchId');
				$this->admin->updateRole($id, $mchId, $roleName, $ids);
				$this->load->library('log_record');
				$logInfo = [
					'roleId'	=> $id,
					'roleName' 	=> $roleName,
					'op'   		=> $this->log_record->Update
				];
				$this->log_record->addLog($this->session->userdata('mchId'), $logInfo, $this->log_record->Role);
				$this->ajaxResponseSuccess();
			} catch (Exception $e) {
				$this->ajaxResponseFail($e->getMessage());
			}
		}
	}

	// ------------------------------------------
	// 删除角色
	// path /role/destroy
	// method post
	public function destroy($id = NULL) {
		if (! isset($id)) {
			$this->ajaxResponseFail('拒绝访问');
		} else {
			$this->load->model('Admin_model', 'admin');
			try {
				$mchId = $this->session->userdata('mchId');
				$role = $this->admin->destoryRole($mchId, $id);
				$this->load->library('log_record');
				$logInfo = [
					'roleId'	=> $id,
					'roleName' 	=> $role->roleName,
					'op'   		=> $this->log_record->Delete
				];
				$this->log_record->addLog($this->session->userdata('mchId'), $logInfo, $this->log_record->Role);
				$this->ajaxResponseSuccess();
			} catch (Exception $e) {
				$this->ajaxResponseFail($e->getMessage());
			}
		}
	}
}