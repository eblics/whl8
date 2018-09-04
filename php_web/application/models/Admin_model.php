<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Admin_model extends CI_Model {
	
	const TABLE_MERCHANTS_ACCOUNTS = 'mch_accounts';
	const TABLE_MCH_ROLES = 'mch_roles';

	/**
	 * 获得一个管理员信息
	 *
	 * @param  int   $mch_id 商户ID
	 * @param  int   $mch_id 商户ID
	 * @return object 管理员对象
	 */
	function get_admin($admin_id, $mch_id) {
		$result = $this->db->where('id', $admin_id)
			->where('mchId', $mch_id)
			->get(self::TABLE_MERCHANTS_ACCOUNTS)->result();
		if (count($result) == 1) {
			return $result[0];
		} else {
			return FALSE;
		}
	}

	/**
	 * 获取所有的管理员
	 * 
	 * @param  int   $mch_id 商户ID
	 * @return array 管理员对象数组
	 */
	function list_admin($mch_id, $all = FALSE) {
		$role_admin = 0;
		if ($all) {
			$role_admin = NULL;
		}
		$admins = $this->db->select('t1.id, t1.userName, t1.realName, t1.phoneNum, t1.noSms, t1.status, t2.roleName')
		->from(self::TABLE_MERCHANTS_ACCOUNTS . ' as t1')
		->join(self::TABLE_MCH_ROLES . ' as t2', 't2.mchId = t1.mchId and t2.id = t1.role', 'left')
		->where('t1.mchId', $mch_id)
		->where('t1.role !=', $role_admin)
		->where('t1.status !=', 3) // 3代表已删除
		->get()->result();
		return $admins;
	}

	/**
	 * 保存一个管理员的信息到数据库
	 * 
	 * @param  array   $admin   管理员信息关联数组
	 * @param  boolean $is_edit 是否是编辑操作
	 * @return boolean TRUE:SUCCESS FALSE:FAILD
	 */
	function save_admin($admin, $is_edit = FALSE) {
		info('================== Save admin start ==================');
		
		if (strlen($admin['phoneNum']) != 11) {
			error('Faild: ' . 'invalid phoneNum ' . $admin['phoneNum']);
			throw new Exception('手机号不正确');
		}
		if (! $admin['passwordPub'] && strlen($admin['passwordPub']) < 6) {
			error('Faild: ' . 'invalid password ' . $admin['passwordPub']);
			throw new Exception("密码不能为空且至少需要6位字符");
		}
		if (! $admin['realName']) {
			error('Faild: ' . 'invalid userName ' . $admin['userName']);
			throw new Exception("用户姓名不能为空");
		}
		unset($admin['passwordPub']);
		if ($is_edit) {
			info('================== Update admin start ====================');
			info('Admin ID is: ' . $admin['admin_id']);
			$realName = $this->db
				->where('realName',$admin['realName'])
				->where('mchId',$admin['mchId'])
				->where('id !=', $admin['admin_id'])
				->get('mch_accounts')->row();
			if(isset($realName)){
				error('Faild: ' . 'invalid realName ' . $admin['realName']);
				throw new Exception('已存在相同的名字');
			}
			$result = $this->db->set('role', $admin['role'])
				->set('realName', $admin['realName'])
				->set('noSms',$admin['noSms'])
				->where('id', $admin['admin_id'])
				->update(self::TABLE_MERCHANTS_ACCOUNTS);
			if ($result) {
				info('Update new admin success');
			} else {
				throw new Exception("修改管理员信息失败");
				error('Faild: Update new admin faild');
			}
		} else {
			info('================== Add new admin start ====================');
			info('Admin info: ' . json_encode($admin));
			$realName = $this->db->where('realName',$admin['realName'])->where('mchId',$admin['mchId'])->get('mch_accounts')->row();
			if(isset($realName) && $realName->status != 3){
				error('Faild: ' . 'invalid realName ' . $admin['realName']);
				throw new Exception('已存在相同的名字');
			}
			$mobile_exists = $this->db->where('phoneNum', $admin['phoneNum'])
				->get(self::TABLE_MERCHANTS_ACCOUNTS)->row();
			if ($mobile_exists) {
				debug('exists admin info: ' .json_encode($mobile_exists));
				if ($mobile_exists->status != 3) {
					error('Faild: ' . 'phoneNum ' . $admin['phoneNum'] . ' alreay exists');
					throw new Exception("该手机号已存在");
				} else {
					$admin['userName'] = '';
					$admin['mail'] = '';
					$admin['idCardNum'] = '';
					$admin['idCardImgUrl'] = '';
					$add_result = $this->db
						->where('id', $mobile_exists->id)
						->update(self::TABLE_MERCHANTS_ACCOUNTS, $admin);
					if ($add_result) {
						info('Add admin success');
						return $mobile_exists->id;
					} else {
						throw new Exception("添加管理员失败");
						error('Faild: Add admin faild');
					}
				}
				
			} else {
				$result = $this->db->insert(self::TABLE_MERCHANTS_ACCOUNTS, $admin);
				$result = $this->db->insert_id();
				if ($result) {
					info('Add admin success');
				} else {
					throw new Exception("添加管理员失败");
					error('Faild: Add admin faild');
				}
			}
		}
		info('================== Save admin end ====================');
		return $result;
	}

	/**
	 * 删除一个管理员账户
	 * 
	 * @param  int $admin_id 管理员ID
	 * @return boolean TRUE:SUCCESS FALSE:FAILD
	 */
	function del_admin($admin_id) {
		info('================== Delete admin start ==================');
		info('Admin ID is: ' . $admin_id);
		$result = $this->db->set('status', 3)
			->where('id', $admin_id)->update(self::TABLE_MERCHANTS_ACCOUNTS);
		if ($result) {
			info('Delete admin success');
		} else {
			error('Delete admin faild');
		}
		info('================== Delete admin end ====================');
		return $result;
	}

	/**
	 * 锁定或解锁一个管理员账户
	 * 
	 * @param  int $admin_id 管理员ID
	 * @param  int $lock 锁定2，解锁0
	 * @return boolean TRUE:SUCCESS FALSE:FAILD
	 */
	function freeze_admin($admin_id, $lock) {
		info('================== Freeze admin start ==================');
		info('Admin ID is: ' . $admin_id . ' lock is ' . $lock);

		/**
		 * 只有两种操作合法
		 */
		if ($lock != 2 && $lock != 0) {
			error('Faild: ' . 'lock is ' . $lock);
			throw new Exception("数据非法");
		}
		$result = $this->db->set('status', $lock)
			->where('id', $admin_id)->update(self::TABLE_MERCHANTS_ACCOUNTS);
		if ($result) {
			info('Freeze admin success');
		} else {
			throw new Exception("发生未知错误");
			error('Freeze admin faild');
		}
		info('================== Freeze admin end ====================');
		return $result;
	}

	/**
	 * 获取企业角色列表
	 * 
	 * @param  $mch_id
	 * @return array
	 */
	function get_roles($mch_id) {
		$roles = $this->db->where('mchId', $mch_id)->get('mch_roles')->result();
		return $roles;
	}

	/**
	 * 获取权限列表
	 * 
	 * @return array
	 */
	function getPermissions() {
		$permissions = $this->db->get('mch_permissions')->result();
		return $permissions;
	}

	/**
	 * 添加角色
	 * 
	 * @param  $roleName 
	 * @param  $ids 权限编号
	 */
	function addRole($mchId, $roleName, $ids) {
		debug("add role - begin");
		debug("params: ". json_encode(func_get_args()));
		$role = $this->db->where('roleName', $roleName)->where('mchId', $mchId)->get('mch_roles')->row();
		if (isset($role)) {
			throw new Exception("该角色名称已存在", 1);
		}
		$success = $this->db->insert('mch_roles', ['mchId' => $mchId, 'roleName' => $roleName]);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}
		$roleId = $this->db->insert_id();
		$keys = $this->db->where_in('id', $ids)->select('key')->get('mch_permissions')->result();

		$data = [];
		foreach ($keys as $item) {
			$data[] = ['roleId' => $roleId, 'permissionKey' => $item->key];
		}
		$success = $this->db->insert_batch('mch_role_permissions', $data);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}
		debug("add role - end");
		return $roleId;
	}

	/**
	 * 更新角色
	 * 
	 * @param  $roleName 
	 * @param  $ids 权限编号
	 */
	function updateRole($roleId, $mchId, $roleName, $ids) {
		debug("update role - begin");
		debug("params: ". json_encode( func_get_args()));
		$role = $this->getRole($mchId, $roleId);
		if (! isset($role)) {
			throw new Exception("角色不存在", 1);
		}
		$roleExists = $this->db
		->where('roleName', $roleName)
		->where('roleName !=', $role->roleName)
		->where('mchId', $mchId)
		->get('mch_roles')->row();
		if (isset($roleExists)) {
			throw new Exception("该角色名称已存在", 1);
		}

		$this->db->trans_start();
		$success = $this->db
		->where('id', $roleId)
		->where('mchId', $mchId)
		->update('mch_roles', ['roleName' => $roleName]);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}

		$success = $this->db->where_in('roleId', $roleId)->delete('mch_role_permissions');
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}

		$keys = $this->db->where_in('id', $ids)->select('key')->get('mch_permissions')->result();

		$data = [];
		foreach ($keys as $item) {
			$data[] = ['roleId' => $roleId, 'permissionKey' => $item->key];
		}
		$success = $this->db->insert_batch('mch_role_permissions', $data);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}
		$this->db->trans_complete();
		debug("update role - end");
	}

	/**
	 * 获取商户所有的角色
	 *
	 * @param  $mchId
	 * @return array
	 */
	function listRole($mchId) {
		$roles = $this->db->where('mchId', $mchId)->get('mch_roles')->result();
		$roleIds = [];
		foreach ($roles as $key => $role) {
			$roleIds[] = $role->id;
		}
		if (empty($roleIds)) {
			return [];
		}
		$names = $this->db->select('t1.name, t2.roleId')
		->from('mch_permissions as t1')
		->join('mch_role_permissions as t2', 't2.permissionKey = t1.key')
		->where_in('t2.roleId', $roleIds)
		->get()->result();
		foreach ($roles as $role) {
			$role->permissions = [];
			foreach ($names as $name) {
				if ($name->roleId == $role->id) {
					$role->permissions[] = $name->name;
				}
			}
		}
		return $roles;
	}

	/**
	 * 获取一个角色信息
	 * 
	 * @param $mchId  
	 * @param $roleId
	 * @return object
	 */
	function getRole($mchId, $roleId) {
		return $this->db->where('mchId', $mchId)->where('id', $roleId)->get('mch_roles')->row();
	}

	/**
	 * 获取一个角色的权限
	 * 
	 * @param $roleId
	 * @return array
	 */
	function getRolePermission($roleId) {
		$permissions = $this->db->select('t1.id')
		->from('mch_permissions as t1')
		->join('mch_role_permissions as t2', 't2.permissionKey = t1.key')
		->where('t2.roleId', $roleId)
		->get()->result();
		$permissionsArr = [];
		foreach ($permissions as $permission) {
			$permissionsArr[] = $permission->id;
		}
		return $permissionsArr;
	}

	/**
	 * 删除角色
	 * 
	 * @param $roleId
	 * @return array
	 */
	function destoryRole($mchId, $roleId) {
		debug("delete role - begin");
		debug("params: ". json_encode( func_get_args()));

		$role = $this->getRole($mchId, $roleId);
		$this->db->trans_start();
		$success = $this->db
		->where('mchId', $mchId)
		->where('id', $roleId)
		->delete('mch_roles');
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}

		$success = $this->db
		->where('mchId', $mchId)
		->where('role', $roleId)
		->update('mch_accounts', ['status' => 3]);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}
		$this->db->trans_complete();
		debug("delete role - end");
		return $role;
	}

}