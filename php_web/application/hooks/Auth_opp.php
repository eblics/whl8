<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {

    /**
     * 所有权限列表
     * 
     * @var array
     */
    private $privileges = [
		'api/merchant/add',
        'api/merchant/get',
        'api/merchant/send_ms',
        'api/merchant/review',
        'api/merchant/active',
        'api/merchant/passwd',
        'api/merchant/freeze',
        'api/admin/get',
        'api/admin/add',
        'api/admin/dynamic',
		'api/admin/active',
		'api/admin/freeze',
		'api/admin/update',
		'api/admin/active',
		'api/admin/reset_passwd'
    ];

    /**
     * 普通管理员权限列表
     * 
     * @var array
     */
    private $admin_priviletes = [
       	'api/merchant/add',
        'api/admin/get',  
        'api/merchant/get',
        'api/admin/dynamic',
		'api/merchant/review',
		'api/merchant/active',
		'api/merchant/passwd',
		'api/merchant/freeze',
		'api/merchant/passwd'
    ];

    /**
     * 运营人员管理权限
     * 
     * @var array
     */
    private $normal_privileges = [
        'api/merchant/add',
        'api/merchant/get',
        'api/merchant/send_ms',
		'api/merchant/review',
		'api/merchant/active',
		'api/merchant/passwd'
    ];
    //根据控制器划分权限
    /**
     * 管理人员
     */
    private $admin_permissions = ['merchant','login','admin'];
    /**
     * 运营人员
     */
    private $normal_permissions = ['merchant','login'];
    public function check() {
        if (! defined('OPP')) {
            return;
        }
        $role = isset($_SESSION['admin'])? $_SESSION['admin']->role: NULL;
        if (! $role) {
            return;
        }
        $url = uri_string();
        if ($role == AdminRoleEnum::Admin) {
            if (in_array($url, $this->privileges) && 
                ! in_array($url, $this->admin_priviletes)) {
                $this->disallow();
            }
        }

        if ($role == AdminRoleEnum::Normal) {
            if (in_array($url, $this->privileges) && 
                ! in_array($url, $this->normal_privileges)) {
                $this->disallow();
            }
        }
    }

    private function disallow() {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && 
            strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") { 
            exit($this->ajax_resp([], '权限不足，拒绝访问', 1));
        } else { 
            exit('<script>alert("权限不足，拒绝访问！");history.back();</script>');
        }
    }

    function ajax_resp($data = ['status' => 0], $msg = 'success', $errcode = 0) {
        header('content-type:application/json');
        return json_encode(['data' => $data, 'errmsg' => $msg, 'errcode' => $errcode]);
    }
}
