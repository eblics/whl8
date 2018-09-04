<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OAdmin_model extends CI_Model {

    const TABLE_OPP_ACCOUNTS = 'opp_accounts';
    const TABLE_OPP_DYNAMIC = 'opp_dynamic';

    function save($admin, $edit = FALSE) {
        info("==================== Save admin start ======================");
        if ($edit) {
            debug("Update admin id is: " . json_encode($admin['id']));
            debug("Update info: " . json_encode($admin));
            $result = $this->db->where('id', $admin['id'])
                ->set('role', $admin['role'])
                ->set('phoneNum', $admin['phoneNum'])
                ->update(self::TABLE_OPP_ACCOUNTS);
            if (! $result) {
                error('Faild: update oop_accounts faild');
                throw new Exception("发生未知错误", 50022);
            }
        } else {
            debug("Add new admin: " . json_encode($admin));
            $exists = $this->db->where('userName', $admin['userName'])->get(self::TABLE_OPP_ACCOUNTS)->row();
            if ($exists) {
                error('username already exists');
                throw new Exception("该用户名已存在", 1);
            }
            $result = $this->db->insert(self::TABLE_OPP_ACCOUNTS, $admin);
            if (! $result) {
                error('Faild: insert oop_accounts faild');
                throw new Exception("发生未知错误", 50022);
            }
            return $this->db->insert_id();
        }
        info("==================== Save admin end ========================");
    }

    function update($admin_id, $params) {
        info("==================== Update profile start ======================");
        debug("Update info is: " . json_encode($params));
        $update_success = $this->db->where('id', $admin_id)
            ->update(self::TABLE_OPP_ACCOUNTS, $params);
        if (! $update_success) {
            error('Faild: update opp_accounts faild');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Update profile end ========================");
    }
    // 删除账号
    function deladmin($id){
        info("==================== Del profile start ======================");
        debug("Del profile is $id" );
        $update_success = $this->db->where('id', $id)->update(self::TABLE_OPP_ACCOUNTS, ['status'=>3]);
        if (! $update_success) {
            error('Faild: del(false) opp_accounts faild');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Del profile end ========================");
    }
    
    function passwd($old_pass, $new_pass, $admin) {
        info("==================== Update password start ======================");
        debug("old_pass is $old_pass, new_pass is $new_pass");
        debug("admin info: " . json_encode($admin));
        $old_pass = md5(md5($old_pass . $admin->salt) . $admin->salt);
        if ($old_pass != $admin->password) {
            error('Faild: old_pass is wrong');
            throw new Exception("原密码不正确", 50020);
        }
        if ($old_pass == md5(md5($new_pass . $admin->salt) . $admin->salt)) {
            error('Faild: new_pass can not be same as old_pass');
            throw new Exception("新密码不能和原密码相同", 50022);
        }
        $update = ['password' => md5(md5($new_pass . $admin->salt) . $admin->salt)];
        $result = $this->db->where('id', $admin->id)
            ->update(self::TABLE_OPP_ACCOUNTS, $update);
        if (! $result) {
            error('Faild: update password error');
            throw new Exception("发生未知错误", 50010);
        }
        info("==================== Update password end ========================");
    }

    function freeze($admin_id) {
        info("==================== Freeze admin start ======================");
        debug("Admin is is: $admin_id");
        $update_success = $this->db->where('id', $admin_id)
            ->set('status', AdminStatusEnum::Locked)
            ->update('opp_accounts');
        if (! $update_success) {
            error('Faild: freeze admin error');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Freeze admin end ========================");
    }

    function active($admin_id) {
        info("==================== Active admin start ======================");
        debug("Admin is is: $admin_id");
        $update_success = $this->db->where('id', $admin_id)
            ->set('status', AdminStatusEnum::Enable)
            ->update('opp_accounts');
        if (! $update_success) {
            error('Faild: active admin error');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Active admin end ========================");
    }

    function reset_passwd($admin_id) {
        info("==================== Reset password start ======================");
        debug("Admin is is: $admin_id");
        $admin = $this->get($admin_id);
        if (! $admin) {
            error('Faild: admin not found');
            throw new Exception("账户不存在", 50022);
        }
        $update_success = $this->db->where('id', $admin_id)
            ->set('password', md5(md5('123456' . $admin->salt) . $admin->salt))
            ->update('opp_accounts');
        if (! $update_success) {
            error('Faild: active admin error');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Reset password end =========================");
    }

    function signin($admin_id, $mch_id ,$keys) {
        if (! isset($admin_id) || ! isset($mch_id) || ! isset($keys)) {
            error("Errow: admin_id and mch_id can not be null");
            throw new Exception("管理员ID,商户ID,密钥不能为空", 1);
        }
        $admin = $this->db->where('id', $admin_id)
            ->get(self::TABLE_OPP_ACCOUNTS)->row();
        if (time() > $admin->auth_token) {
            error("Errow: auth_token has timeout");
            throw new Exception("登录验证已过期", 1);
        }

        if(strtolower($keys) != strtolower($admin->sessionKeys)) {
            error("Error:sessionKeys is wrong");
            throw new Exception("授权已过期");
            //不抛出 真实的错误:sessionKeys错误
        }
        $mch_account = $this->db
            ->where('mchId', $mch_id)
            ->where('role', 0)
            ->get('mch_accounts')->row();
        if (! $mch_account) {
            error("Errow: mch_account not found where mchid is $mch_id");
            throw new Exception("找不到管理员帐户", 1);
        }
        $mch_account->userName = $admin->userName;
        //运营人员判断
        if($admin->role == 2) {
            $mch_account->role = 2;
            return $mch_account;
        }
        if($admin->role == 1) {
            $mch_account->role = 1;
            return $mch_account;
        }
        if($admin->role == 0) {
            $mch_account->role = 0;
            return $mch_account;
        }

    }

    function valid_verify_code($verify_code, $created) {
        if ($created != $verify_code) {
            throw new Exception("验证码错误", 1);
        }
    }

    function find($username, $password) {
        $admin = $this->db
            ->where('userName', $username)
            ->get(self::TABLE_OPP_ACCOUNTS)
            ->row();
        if (! $admin) {
            throw new Exception("帐户不存在", 1);
        }
        $salt = $admin->salt;
        if ($admin->password !== md5(md5($password . $salt) . $salt)) {
            throw new Exception("用户名或密码错误", 1);
        }
        if ($admin->status == 2) {
            throw new Exception("该帐户已被禁用 如有疑问请联系系统管理员", 1);
        }
        return $admin;
    }

    function get($admin_id = NULL) {
        if (isset($admin_id)) {
            $admins = $this->db->where('id', $admin_id)
                ->get(self::TABLE_OPP_ACCOUNTS)->row();
        } else {
            $admins = $this->db
                ->where_not_in('id',1)
                ->where_not_in('status',3)
                ->order_by('createTime', 'DESC')
                ->get(self::TABLE_OPP_ACCOUNTS)
                ->result();
            foreach ($admins as &$admin) {
                $admin->createTime = date('Y-m-d H:i:s',  $admin->createTime);
            }
        }
       
        return $admins;
    }

    function dynamic() {
        $dynamics = $this->db->select('*')
            ->from(self::TABLE_OPP_ACCOUNTS)
            ->join(self::TABLE_OPP_DYNAMIC, 'opp_accounts.id = opp_dynamic.adminId')
            ->order_by('occTime', 'DESC')
            ->limit(30)
            ->get()->result();
        foreach ($dynamics as &$dynamic) {
            $dynamic->occTime = date('Y-m-d H:i:s', $dynamic->occTime);
        }
        return $dynamics;
    }


    /**
     * 生成企业登录验证
     */
    function generate_token($id) {
        $session_keys = session_keys();
        $data = array(
            'auth_token' => time() + 5,
            'sessionKeys' => $session_keys
        );
        $result = $this->db->where('id',$id)->update('opp_accounts',$data);
        if (! $result) {
            error("Generate token faild");
            throw new Exception("发生未知错误");
        }
        return $session_keys;
    }
}
