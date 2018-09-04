<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 角色权限钩子过滤器
 * 
 * @author shizq
 * 
 */
class Role_filter {

    /**
     * 管理员权限，非管理员不得访问
     * 
     * @var array
     */
    private $global_disallow_controller = []; // ['admin', 'role'];

    private $global_disallow_path = ['user/upgrade_account', 'user/authorize_account'];

    public function verify() {
        if (! defined('MCH')) {
            return;
        }
        $role_admin = isset($_SESSION['role']) ? $_SESSION['role'] : NULL;
        $uri_string = get_current_router(1) . '/' . get_current_router(2);
        $controller = get_current_router(1);

        // 0 是超级管理员，-1 是报表查看人员
        // 注：如果是报表查看人员，菜单的显示需要特殊处理
        if ($role_admin != 0 && $role_admin != -1) {
            $this->ci =& get_instance ();

            // 获取所有需要权限才能访问的uri
            $permissions = $this->ci->db->select('uris')->get('mch_permissions')->result();
            $permissionsArr = [];
            foreach ($permissions as $key => $value) {
                $permissionsArr[] = $value->uris;
            }
            $permissionsStr = implode(',', $permissionsArr);
            $permissionsArr = explode(',', $permissionsStr);

            // 获取当前角色拥有的权限uri
            $rolePermissions = $this->ci->db->select('t1.uris')
            ->from('mch_permissions as t1')
            ->join('mch_role_permissions as t2', 't2.permissionKey = t1.key')
            ->where('t2.roleId', $role_admin)
            ->get()->result();
            $rolePermissionsArr = [];
            foreach ($rolePermissions as $key => $value) {
                $rolePermissionsArr[] = $value->uris;
            }
            $rolePermissionsStr = implode(',', $rolePermissionsArr);
            $rolePermissionsArr = explode(',', $rolePermissionsStr);
            if (in_array($controller, $this->global_disallow_controller)) {
                $this->disallow();
            }
            if (in_array($uri_string, $this->global_disallow_path)) {
                $this->disallow();
            }
            if (! in_array('production', $_SESSION['permission_modules']) && $_SESSION['role'] != ROLE_ADMIN_MASTER) {
                // 如果没有产品管理权限，不做限制
                if ($controller != 'product') {
                    if (in_array($uri_string, $permissionsArr) && ! in_array($uri_string, $rolePermissionsArr)) {
                       $this->disallow();
                   } 
                }
            } else {
               if (in_array($uri_string, $permissionsArr) && ! in_array($uri_string, $rolePermissionsArr)) {
                   $this->disallow();
               } 
            }

        }
    }

    private function disallow() {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && 
            strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") { 
            header('Content-type: text/json');
            exit(ajax_resp([], '权限不足，拒绝访问！', 1));
        } else { 
            exit('<script>alert("权限不足，拒绝访问！");history.back();</script>');
        }
    }
}
