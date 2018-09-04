<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opp extends MerchantController {

    public function auth() {
    	$admin_id = $this->input->get('admin_id');
        //此处不存在安全隐患
        //必须验证密钥才能访问.密钥写入数据库和运营端用户对应,触发一次点击生成一次密钥,并且超过5S之后的验证不具有效性.
    	$mch_id = $this->input->get('mch_id');
        $keys = $this->input->get('keys');
        //查询数据库根据角色类型给予相应的权限
        $this->load->model('OAdmin_model', 'admin');
        $mch_account = $this->admin->signin($admin_id, $mch_id, $keys);
        if(isset($mch_account->role)) {
            //运营人员
            if($mch_account->role == 2) {
                $session_data = [
                    'userId' => $mch_account->id,
                    'status' => 5,
                    'mchId' => $mch_id,
                    'username' => $mch_account->userName,
                    'role' => 0,
                    'part' => 1
                ];
                $this->session->expired = null;
                $this->session->set_userdata($session_data);
                $this->session->set_userdata('permission_modules', []);
                redirect('/');
            }
            if($mch_account->role == 1 || $mch_account->role == 0) {
                $session_data = [
                    'userId' => $mch_account->id,
                    'status' => 1,
                    'mchId' => $mch_id,
                    'username' => $mch_account->userName,
                    'role' => 0,
                    'part' =>0
                ];
                $this->session->expired = null;
                $this->session->set_userdata($session_data);
                $this->session->set_userdata('permission_modules', []);
		debug('auth:'.var_export($session_data,True));
                redirect('/');
            }
        }else{
            show_404();
        }
        // try {
        // 	$mch_account = $this->admin->signin($admin_id, $mch_id, $keys);
        //     if(isset($mch_account->role)) {
        //         if($mch_account->role == 2) {
        //             $session_data = [
        //                 'userId' => $mch_account->id,
        //                 'status' => 5,
        //                 'mchId' => $mch_id,
        //                 'username' => $mch_account->userName,
        //                 'role' => 0
        //             ];
        //             $this->session->set_userdata($session_data);
        //             redirect('/');
        //         }
        //     }else{
        //         $session_data = [
        //             'userId' => $mch_account->id,
        //             'status' => 1,
        //             'mchId' => $mch_id,
        //             'username' => $mch_account->userName,
        //             'role' => 0
        //         ];
        //         $this->session->set_userdata($session_data);
        //         redirect('/');
        //     }
        // } catch (Exception $e) {
        // 	show_404();
        // }
    }
}
