<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ODealer_model extends CI_Model {

    function add($dealer) {
        info("==================== Add dealer start ======================");
        debug("add dealer is: " . json_encode($dealer['name']));
        $result = $this->db->insert('dealer_info',$dealer);
        if (! $result) {
            error('Faild: add dealer_info faild');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Add dealer end ========================");
    }
    function get_dealer($id){
        info("==================== Get dealer start ======================");
        debug("get dealer info,id is: " . json_encode($id));
        $result = $this->db->where('id',$id)->get('dealer_info')->row();
        if (!isset($result)) {
            error('Faild: get dealer_info faild');
            throw new Exception("发生未知错误", 50022);
        }else{
            return $result;
        }
        info("==================== Get dealer end ========================");
    }
    function get_code(){
        $result = $this->db->get('dealer_info')->result();
        return $result;
    }
    function get_data() {
        $sql = "select FROM_UNIXTIME(createTime,'%Y-%m-%d %H:%i:%s') createTime,id,status,name,code,contact,address,mail,phoneNum from dealer_info order by id desc";
        $result = $this->db->query($sql)->result();
        return $result;
    }

    function update_dealer($id,$dealer) {
        info("==================== Update dealer start ======================");
        debug("Update dealer info,id is: " . json_encode($id));
        $update_success = $this->db->where('id', $id)
            ->update('dealer_info', $dealer);
        if (! $update_success) {
            error('Faild: update dealer faild');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Update dealer end ========================");
    }

    function lock_dealer($id){
        info("==================== Lock dealer start ======================");
        debug("Lock dealer,id is: " . json_encode($id));
        $update_success = $this->db->set('status',1)->where('id', $id)->update('dealer_info');
        if (! $update_success) {
            error('Faild: lock dealer faild');
            throw new Exception("发生未知错误", 50022);
        }
        info("==================== Lock dealer end ========================");
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



}
