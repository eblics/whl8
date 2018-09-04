<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * 登录API控制器
 *
 * @author shizq Revised By George
 *
 */
class Api extends Api_Controller {

    public function login() {
        $username = $this->input->post('account');
        $password = $this->input->post('password');
        $verify_code = $this->input->post('verify_code');
        $ip_address = $this->input->ip_address();
        try {
            $this->load->model('OAdmin_model', 'admin');
	    debug('vcode:'.$this->session->userdata('verify_code'));
            $this->admin->valid_verify_code($verify_code, $this->session->userdata('verify_code'));
            $admin = $this->admin->find($username, $password);
            $this->session->set_userdata('admin', $admin);
            $this->saveDynamic('登录系统', $admin->id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    public function admin($apiName = NULL) {
        $this->load->model('OAdmin_model', 'admin');
        $this->{$apiName}();
    }

    public function merchant($apiName = NULL) {
        $this->load->model('OMerchant_model', 'merchant');
        $this->load->helper('sms');
        $this->{$apiName}();
    }

    public function tools($apiName = NULL) {
        $this->{$apiName}();
    }

    private function get_admin() {
        $admins = $this->admin->get();
        $this->ajaxResponse($admins);
    }

    private function dynamic() {
        $dynamics = $this->admin->dynamic();
        $this->ajaxResponse($dynamics);
    }

    private function add_admin() {
        $username = $this->input->post('username');
        $mobile = $this->input->post('mobile');
        $role = $this->input->post('role');
        $salt = mt_rand(100000, 999999);
        $password = md5(md5('123456' . $salt) . $salt);
        $admin = [
            'userName' => $username,
            'phoneNum' => $mobile,
            'role' => $role,
            'createTime' =>time(),
            'salt' => $salt,
            'password' => $password,
            'status' => 0
        ];
        try {
            $admin_id = $this->admin->save($admin);
            $this->saveDynamic('新建了管理员账户', $admin_id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    /**
     * 删除管理帐户
     *
     */
    private function del_admin(){
        $id = $this->input->post('id');
        try{
            $this->admin->deladmin($id);
            $this->saveDynamic('删除了管理员账户', $id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        }catch(Exception $e){
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    /**
     * 更新管理员信息
     *
     * @param {
     *     realname: 姓名
     *     mobile: 手机号
     *     mail: 邮箱地址
     * }
     * @return json
     */
    private function update_profile() {
        $realName = $this->input->post('realname');
        $mobile = $this->input->post('mobile');
        $mail = $this->input->post('mail');
        $admin = $this->getCurrentUser();
        $admin_id = $admin->id;
        $params = [
            'realName' => $realName,
            'phoneNum' => $mobile,
            'mail' => $mail
        ];
        try {
            $this->admin->update($admin_id, $params);
            $this->saveDynamic('修改了个人信息', $admin_id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 修改管理员账户信息
     *
     * @param {
     *     admin_id: 管理员ID
     *     mobile: 新的手机号
     *     role: 新的角色类型
     * }
     * @return json
     */
    private function update() {
        $admin_id = $this->input->post('admin_id');
        $mobile = $this->input->post('mobile');
        $role = $this->input->post('role');
        $admin = [
            'id' => $admin_id,
            'phoneNum' => $mobile,
            'role' => $role
        ];
        try {
            $this->admin->save($admin, TRUE);
            $this->saveDynamic('修改了账户信息', $admin['id'], DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 修改管理员账户密码
     *
     * @param {
     *     $old_pass: 旧密码
     *     $new_pass: 新密码
     * }
     * @return json
     */
    private function passwd() {
        $old_pass = $this->input->post('old_pass');
        $new_pass = $this->input->post('new_pass');
        $admin = $this->getCurrentUser();
        try {
            $this->admin->passwd($old_pass, $new_pass, $admin);
            $this->saveDynamic('修改了个人密码', $admin->id, DynamicTypeEnum::Admin);
            session_destroy();
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 冻结一个管理员账户
     *
     * @param   $admin_id
     * @return  json
     */
    private function freeze_admin() {
        $admin_id = $this->input->post('admin_id');
        try {
            $this->admin->freeze($admin_id);
            $this->saveDynamic('冻结了管理员账户', $admin_id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 冻结一个管理员账户
     *
     * @param   $admin_id
     * @return  json
     */
    private function active_admin() {
        $admin_id = $this->input->post('admin_id');
        try {
            $this->admin->active($admin_id);
            $this->saveDynamic('激活了管理员账户', $admin_id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 重置管理员账户密码
     *
     * @param   $admin_id
     * @return  json
     */
    private function reset_passwd() {
        $admin_id = $this->input->post('admin_id');
        try {
            $this->admin->reset_passwd($admin_id);
            $this->saveDynamic('重置了管理员密码', $admin_id, DynamicTypeEnum::Admin);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 生成运营人员登录到企业后台的token & 生成密钥
     *
     */
    private function generate_token() {
        $admin = $this->getCurrentUser();
        $id = $admin->id;
        $mch_id = $this->input->post('mch_id');
        try {
            $session_keys = $this->admin->generate_token($id);
            $this->saveDynamic('进入了企业账户', $mch_id, DynamicTypeEnum::Merchant);
            $this->ajaxResponse(['admin_id' => $this->getCurrentUser()->id,'keys' => $session_keys]);
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取企业列表数据
     *
     * @param  integer $id 查询类别
     * @return json
     */
    private function get() {
        $status = $this->input->get('status');
        try {
            if (! $status) {
                $res = $this->merchant->get_allmch_data();
            } else {
                $res = $this->merchant->get_mch_data($status);
            }
            $this->ajaxResponse($res);
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 添加一个企业账户
     *
     * @param {
     *     username: 企业名称
     *     mobile: 手机号
     *     mail: 邮箱账户
     * }
     * @return json
     */
    private function add() {
        $username = $this->input->post('username');
        $mobile = $this->input->post('mobile');
        $mail = $this->input->post('mail');
        $name = $this->input->post('name');
        $mch_account = [
            'userName' => $username,
            'phoneNum' => $mobile,
            'mail' => $mail,
            'createTime' => time(),
            'updateTime' => time(),
            // 'is_formal' => 0,
            'status' => 0,
        ];
        try {
            $mch_account_id = $this->merchant->add($mch_account,$name);
            $this->saveDynamic('新建了企业账户', $mch_account_id, DynamicTypeEnum::MchAccount);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 执行审核操作
     *
     * @param   $mch_id  企业ID
     * @param   $preview 是否是预审核 0：否 1：是
     * @return  ajax
     */
    private function review() {
        $mch_id = $this->input->post('mch_id');
        $preview = $this->input->post('preview');
        try {
            $this->merchant->review($mch_id, $preview);
            if ($preview) {
                $this->saveDynamic('预审核了企业账户', $mch_id, DynamicTypeEnum::Merchant);
            } else {
                $this->saveDynamic('审核了企业账户', $mch_id, DynamicTypeEnum::Merchant);
            }
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    /**
     * 新的预审核执行操作
     *
     */
    private function pre() {
        $mch_id = $this->input->post('id');
        $codeVersion = $this->input->post('value');
        // $result = $this->merchant->review($mch_id,$codeVersion);
        try {
            $result = $this->merchant->review($mch_id,$codeVersion);
            if($result){
                $this->saveDynamic('预审核了企业账户', $mch_id, DynamicTypeEnum::Merchant);
                $this->ajaxResponse();
            }
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 冻结一个企业
     *
     * @param   $mch_id  企业ID
     * @return  ajax
     */
    private function freeze() {
        $mch_id = $this->input->post('mch_id');
        try {
            $this->merchant->freeze($mch_id);
            $this->saveDynamic('冻结了企业账户', $mch_id, DynamicTypeEnum::Merchant);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 激活企业账户
     *
     * @param   $mch_id  企业ID
     * @return  ajax
     */
    private function active() {
        $mch_id = $this->input->post('mch_id');
        try {
            $this->merchant->active($mch_id);
            $this->saveDynamic('激活了企业账户', $mch_id, DynamicTypeEnum::Merchant);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    /**
     * 给企业发送短信
     *
     * @param   $mch_id  企业ID
     * @return  ajax
     */
    private function send_ms() {
        if($this->input->post('content1')){
            $content1 = $this->input->post('content1');
        }
        if($this->input->post('content2')){
            $content2 = $this->input->post('content2');
        }
        if($this->input->post('content3')){
            $content3 = $this->input->post('content3');
        }
        if($this->input->post('objectives')){
            $objectives = $this->input->post('objectives');
        }
        if(isset($content3) && isset($content2) && isset($content1)){
            $type = 3;
        }elseif(isset($content2) && isset($content1)){
            $type = 2;
        }else{
            $type = 1;
        }
        //在这里修改---start
        if($objectives == 'diy'){
            $mobile = $this->input->post('phones');
        }
        if($objectives == 1){
            $mobile = $this->merchant->get_nums(1);
        }
        if($objectives == 2){
            $mobile = $this->merchant->get_nums(2);
        }
        if($objectives == 3){
            $mobile = $this->merchant->get_nums(3);
        }
        if($objectives == 4){
            $mobile = $this->merchant->get_nums(4);
        }
        if($objectives == 'all'){
            $mobile = $this->merchant->get_nums('all');
        }
        //在这里修改---end
        if($type == 1){
            //单变量模板
            $template_id = 'SMS_8966308';
            try {
                $res = send_sms($mobile, $template_id, $content1, $content2 = null, $con3 = null, $type);
                $this->ajaxResponse();
            } catch (Exception $e) {
                $this->ajaxResponse([], $e->getMessage(), $e->getCode());
            }
        }
        if($type == 2){
            //双变量模板
            $template_id = 'SMS_9180035';
            try {
                $res = send_sms($mobile, $template_id, $content1, $content2, $con3 = null, $type);
                $this->ajaxResponse();
            } catch (Exception $e) {
                $this->ajaxResponse([], $e->getMessage(), $e->getCode());
            }
        }
        if($type == 3){
            //三变量模板
            $template_id = 'SMS_9315001';
            try {
                $res = send_sms($mobile, $template_id, $content1, $content2, $content3, $type);
                $this->ajaxResponse();
            } catch (Exception $e) {
                $this->ajaxResponse([], $e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * 密码初始化
     *
     * @return ajax
     */
    private function init_passwd() {
        $mch_id = $this->input->post('mch_id');
        try {
            $res = $this->merchant->passwd($mch_id);
            $this->saveDynamic('初始化了企业账户密码', $mch_id, DynamicTypeEnum::Merchant);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    /**
     * 锁定列表
     */
    private function lock_list(){
        // $status = $this->input->get('status');
        $res = $this->merchant->get_allusers_data();
        try {
            if ($res) {
                $res = $this->merchant->get_allusers_data();
            }
            $this->ajaxResponse($res);
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    /**
     * 申请解锁列表
     */
    private function unlock_wait_list(){
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $search = $this->input->post('search')['value'];
        // $param=(array)$this->input->post();
        $start=intval($this->input->post('start'));
        $length=intval($this->input->post('length'));
        $draw=intval($this->input->post('draw'));
        $data=$this->merchant->get_unlock_list($search,$count,$start,$length);
        $datas=(object)["draw"=> intval($draw),"recordsTotal"=>intval($count),'recordsFiltered'=>$count,'data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($datas));
        // $listsCount=$this->merchant->get_unlock_list_count();
        // $lists=$this->merchant->get_unlock_list_page($start,$length);

        // $result=(object)['data'=>$lists];
        // $result->draw=intval($draw);
        // $result->recordsTotal=$listsCount->count;
        // $result->recordsFiltered=$groupsCount->count;
        // echo json_encode($result);
        // $data=$this->merchant->get_unlock_list($param,$count,$start,$length);
        // $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        // $this->output->set_content_type('application/json')->set_output(json_encode($data));
        // try {
        //     $res = $this->merchant->get_unlock_list();
            // $this->ajaxResponse($result);

        // } catch (Exception $e) {
        //     $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        // }
    }

    /**
     * 操作 ajax
     */
    private function operation(){
        $id = $this->input->post('id');
        $res = ['res'=>true];
        try {
            $res = $this->merchant->operation($id);
            if($res){
                $this->ajaxResponse($res);
            }

        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    private function operation_refuse(){
        $id = $this->input->post('id');
        $val = $this->input->post('val');
        try {
            $res = $this->merchant->operation_refuse($id,$val);
            if($res){
                $this->ajaxResponse($res);
            }
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    //拉黑（拒绝并加入黑名单）
    private function pull_into_blacklist(){
        $id = $this->input->post('id');
        $val = $this->input->post('val');
        $openid = $this->input->post('openid');
        $text = $this->input->post('text');
        $mark = $this->input->post('mark');
        try {
            $res = $this->merchant->pull_into_blacklist($id,$val,$openid,$mark);
            //如果黑名单表里存在，不提示。
            if($res){
                $this->ajaxResponse($res);
            }
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    //备注
    private function mark(){
        $id = $this->input->post('id');
        $val = $this->input->post('val');
        try {
            $res = $this->merchant->mark($id,$val);
            if($res){
                $this->ajaxResponse($res);
            }
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    //获取备注
    private function get_mark(){
        $id = $this->input->post('id');
        try {
            $res = $this->merchant->get_mark($id);
            if(isset($res)){
                $this->ajaxResponse($res);
            }else{
                $this->ajaxResponse([],0,1);
            }
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    //查找封禁用户
    private function get_user(){
        //1.企业openid；2.企业昵称；3.平台openid；4.平台nickname；
        $vk = $this->input->get_post('vk');
        $vv = $this->input->get_post('vv');
        $res = $this->merchant->search_user($vk,$vv);
        $result=(object)['data'=>$res];
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    //用户拉黑或恢复正常
    private function move_out_blacklists(){
        $id = $this->input->get_post('id');
        $status = $this->input->get_post('status');
        $res = $this->merchant->move_out_blacklists($id,$status);
        if($res){
            $result=(object)['res'=>'操作成功！','errcode'=>0];
            $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }else{
            $result=(object)['res'=>'操作失败！','errcode'=>1];
            $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }
    }
    //单独 解封/封禁用户
    private function operation_user(){
        $id = $this->input->get_post('id');
        $lock = $this->input->get_post('lock');
        $res = $this->merchant->operation_users($id,$lock);
        switch ($res) {
            case 'already locked':
                $result=(object)['res'=>'失败，用户已经是锁定状态！'];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            case 'lock fail':
                $result=(object)['res'=>'锁定操作失败！','errcode'=>1];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            case 'exists1':
                $result=(object)['res'=>'解锁失败，用户状态正常无需重复操作！','errcode'=>1];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            case 'exists2':
                $result=(object)['res'=>'未被禁，用户状态正常无需重复操作！','errcode'=>1];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            case 'lock success':
                $result=(object)['res'=>'操作成功！','errcode'=>0];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            case 'unlock success':
                $result=(object)['res'=>'操作成功！','errcode'=>0];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            case 'unlock fail':
                $result=(object)['res'=>'操作失败！','errcode'=>1];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
                break;
            // default:
            //     $result=(object)['res'=>'该用户暂时不可操作！','errcode'=>1];
            //     $this->output->set_content_type('application/json')->set_output(json_encode($result));
            //     break;
        }
    }
    //获取黑名单用户数据
    private function get_blacklist_data(){
        $res = $this->merchant->get_blacklist_data();
        $data = ['data'=>$res];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //用户移出黑名单
    private function move_out_blacklist(){
        $id = $this->input->post('id');
        $openid = $this->input->post('openid');
        $res = $this->merchant->move_out_black($id,$openid);
        if($res){
            $result = array('result'=>true);
        }else{
            $result = array('result'=>false);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    /*************查询用户封禁信息 add by cw 2017-01-13*******************/
    // 查询用户的相关的信息
    private function get_userscan_info(){
        $userId=$this->input->post('userId');
        $data=$this->merchant->get_userscan_info($userId);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /*************查询用户封禁信息 add by cw 2017-01-13*******************/

    private function handle_upload_file() {
        // 判断是否有文件上传
        if (! isset($_FILES['upfile']) || empty($_FILES['upfile'])) {
            $this->ajaxResponse([], '请选择数据文件', 1);
            return;
        }

        // 判断是否有错误
        if ($_FILES['upfile']['error'] > 0) {
            $this->ajaxResponse([], '上传数据文件失败', 1);
            return;
        }

        // 判断文件大小
        if ($_FILES["upfile"]["size"] > 100000) {
            $this->ajaxResponse([], '上传数据文件不能大于100KB', 1);
            return;
        }

        $uploadType = $_FILES["upfile"]["type"];

        $uploadDir = '/var/upload/'. $_FILES["upfile"]["name"];

        // 判断上传目录是否存在
        if (! (file_exists('/var/upload') && is_dir('/var/upload'))) {
            if (! mkdir('/var/upload', 0775, true)) {
                $this->ajaxResponse([], '无法创建上传目录', 1);
                return;
            }
        }

        // 判断文件类型
        if (($uploadType == "text/plain")) {
            if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {
                if (move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadDir)) {
                    $content = file_get_contents($uploadDir);
                    $openIdArr = explode("\r\n", $content);
                    $this->ajaxResponse(['fileName' => $_FILES["upfile"]["name"], 'openidNums' => count($openIdArr) - 1]);
                } else {
                    $this->ajaxResponse([], '上传数据文件失败', 2);
                    return;
                }
            } else {
                $this->ajaxResponse([], '上传数据文件失败', 3);
                return;
            }
        } else {
            $this->ajaxResponse([], '格式不正确，只能上传txt文件', 1);
            return;
        }
    }

    /**
     * 封杀微信用户
     *
     * @param $dataFile 数据文件名称
     * @return json
     */
    private function seal() {
        $dataFile = $this->input->get('file_name');
        if (! file_exists('/var/upload/'. $dataFile) || is_null($dataFile)) {
            $this->ajaxResponse([], '请先上传数据文件', 1);
            return;
        }
        $content = file_get_contents('/var/upload/'. $dataFile);
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

        if (empty($idArr)) {
            $this->ajaxResponse([], '无可用的openid可以封杀', 1);
            return;
        }
        $this->db->set('commonStatus', 1)->where_in('id', $idArr)->update('users_common');
        $this->ajaxResponse(['seal_nums' => $this->db->affected_rows()]);
    }
}
