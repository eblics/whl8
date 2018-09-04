<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Group
 */
class Group extends Mobile_Controller {
    public function __construct($mchId=null) {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('merchant_model');
        $this->load->model('user_model');
        $this->load->model('group_model');
        $this->load->model('group_scanpk_model');
        $this->load->library('common/common_login');
        $mchId=$this->session->userdata('mchId');
        if(isset($mchId)){
            $setting=$this->group_model->get_group_setting($mchId);
            if(! $setting){
                $this->groupProductName='好友圈';
            }else{
                $this->groupProductName=$setting->productName;
            }
        }else{
            $this->groupProductName='好友圈';
        }
    }

    /**
     * 群列表
     */
    public function lists($mchId = null) {
        if (! isset($mchId)) {
            $this->showErrPage('您访问的页面不存在');
        }
        $currentUser = $this->getCurrentUser($mchId);
        if ($currentUser->subscribe != BoolEnum::Yes) {
            $this->load->view('group_error',['errmsg'=>'您还没有关注公众号『'.$merchant->wxName.'』<BR>长按识别二维码，关注公众号<BR><img src="/group/minimg/'.urlencode($merchant->wxQrcodeUrl).'" width=200 height=200>']);
            return;
        }
        $groups=$this->group_model->get_my_group($currentUser->id);
        $data=['groups'=>$groups];
        $data['groupProductName']=$this->groupProductName;
        $recommend=$this->group_model->get_group_recommend($mchId,$currentUser->id,2);
        $data['recommend']=$recommend;
        $this->load->view('group_lists',$data);
    }

    /**
     * 群聊天
     */
    public function chat($id=null){
        $mchId = $this->getCurrentMchId();
        $currentUser = $this->getCurrentUser($mchId);
        $merchant=$this->merchant_model->get($mchId);
        if ($currentUser->subscribe != BoolEnum::Yes) {
            $this->load->view('group_error',['errmsg'=>'您还没有关注公众号『'.$merchant->wxName.'』<BR>长按识别二维码，关注公众号<BR><img src="/group/minimg/'.urlencode($merchant->wxQrcodeUrl).'" width=200 height=200>']);
            return;
        }
        $group=$this->group_model->get_group($id);
        if(! $group) return;
        $allMembers=$this->group_model->get_group_member($group->id);
        $member=$this->group_model->get_group_member_one($group->id,$user->id);
        if(! $member){
            if($group->status==0){
                redirect('/group/join');
                return;
            }
            $userData=[
                'groupId'=>$group->id,
                'nickName'=>$user->nickName,
                'headImage'=>$user->headimgurl,
                'userId'=>$user->id,
                'createTime'=>time(),
                'updateTime'=>time()
            ];
            if($group->memberNum>=$group->maxMemberNum){
                $this->load->view('group_error',['errmsg'=>'来晚了，此群已满']);
                return;
            }
            $save=$this->group_model->add_group_member($group->id,$userData);
            if(! $save){
                $this->load->view('group_error',['errmsg'=>'进群失败，请重试']);
                return;
            }
            $member=(object)$userData;
        }
        $data=$group;
        $data->userId=$user->id;
        $data->userName=$member->nickName;
        $data->userImg=$member->headImage;
        $data->allMembers=$allMembers;
        $data->groupProductName=$this->groupProductName;
        $this->load->view('group_chat',$data);
    }

    /**
     * 群设置
     */
    public function setting($id=null){

        $id=intval($id);
        $mchId=$this->getCurrentMchId();
        $merchant=$this->merchant_model->get($mchId);
        
        $currentUser = $this->getCurrentUser($mchId);
        $group=$this->group_model->get_group($id);
        if(! $group) return;
        $member=$this->group_model->get_group_member_one($group->id,$currentUser->id);
        if(! $member) return;
        $allMembers=$this->group_model->get_group_member($group->id);
        if(! $allMembers) return;
        $master=$this->group_model->get_group_master($group->id);
        if(! $master) return;
        $data=$group;
        $data->allMembers=$allMembers;
        $data->currentUser=$currentUser;
        $data->currentMember=$member;
        $data->currentMaster=$master;
        $data->groupProductName=$this->groupProductName;
        $this->load->view('group_setting',['data'=>$data]);
    }

    /**
     * 用户信息
     */
    public function userinfo($groupId=null){
        $groupId=intval($groupId);
        $mchId=$this->getCurrentMchId();
        $merchant=$this->merchant_model->get($mchId);
        $openid=$this->session->userdata('openid_'.$mchId);
        $currentUser = $this->getCurrentUser($mchId);
        $group=$this->group_model->get_group($groupId);
        if(! $group) return;
        $member=$this->group_model->get_group_member_one($group->id,$currentUser->id);
        if(! $member) return;
        $data=$member;
        $data->groupProductName=$this->groupProductName;
        $this->load->view('group_userinfo',['data'=>$data]);
    }

    /**
     * 新建群
     */
    public function add($id=null){
        $this->common_login->login();//common_login login
        if(! $this->islogin()){
            $this->load->view('group_error',['errmsg'=>'未登录，请从公众号登入']);
            return;
        }
        $data=['isEdit'=>false];
        if(! isset($id)){
            $this->load->view('group_add',['data'=>$data]);
            return;
        }
        $id=intval($id);
        $data['isEdit']=true;
        $group=$this->group_model->get_group($id);
        if(! isset($group)) return;
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) return;
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) return;
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) return;
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) return;
        $master=$this->group_model->get_group_master($id);
        if($user->id!=$master->userId) return;
        $data['groupInfo']=$group;
        $data['groupProductName']=$this->groupProductName;
        $this->load->view('group_add',['data'=>$data]);
    }

    /**
     * 创建口令
     */
    public function pwd($id=NULL){
        $this->common_login->login();//common_login login
        if(! $this->islogin()){
            $this->load->view('group_error',['errmsg'=>'未登录，请从公众号登入']);
            return;
        }
        if(! isset($id)) return;
        $id=intval($id);
        $group=$this->group_model->get_group($id);
        if(! isset($group)) return;
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) return;
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) return;
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) return;
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) return;
        $saveData['updateTime']=time();
        $master=$this->group_model->get_group_master($id);
        if($user->id!=$master->userId) return;
        $isupdate=false;
        if($group->password!=''){
            $isupdate=true;
        }
        $this->load->view('group_pwd',['groupId'=>$id,'password'=>$group->password,'isupdate'=>$isupdate,'groupProductName'=>$this->groupProductName]);
    }

    /**
     * 口令进群
     */
    public function join(){
        $this->common_login->login();//common_login login
        if(! $this->islogin()){
            $this->load->view('group_error',['errmsg'=>'未登录，请从公众号登入']);
            return;
        }
        $data=[];
        $data['groupProductName']=$this->groupProductName;
        $this->load->view('group_join',$data);
    }

    /**
     * 新建群成功
     */
    public function add_group_ok($id=NULL){
        $this->common_login->login();//common_login login
        if(! $this->islogin()){
            $this->load->view('group_error',['errmsg'=>'未登录，请从公众号登入']);
            return;
        }
        if(! isset($id)) return false;
        $id = intval($id);
        $mchId=$this->session->userdata('mchId');
        $this->load->view('group_addok',['groupId'=>$id,'mchId'=>$mchId,'groupProductName'=>$this->groupProductName]);
    }

    /**
     * 群图片上传
     */
    public function upload() {
        if(! $this->common_login->is_login()) return;//common_login login
        if(! $this->islogin()){
            return;
        }
        $mchId=$this->session->userdata('mchId');
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) return;
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) return;
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) return;
        $filepath= '/files/public/group/'.$mchId;
        // echo upload_file('gif|jpg|jpeg|png',500,$filepath);
        $file=$_POST['filestr'];
        $base64 = htmlspecialchars($file);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
            $type = $result[2];
            $mchRelDir=getcwd().$filepath;
            if(!is_dir($mchRelDir)){
                mkdir($mchRelDir,0777,true);
            }
            $new_file = $filepath .'/'. $user->id.'_'.time(). rand(1000,9999) . ".{$type}";
            $relpath=getcwd().$new_file;
            if (file_put_contents($relpath, base64_decode(str_replace($result[1], '', $base64)))) {
                //resize image
                $this->load->library('common/imagick_lib');
                $this->imagick_lib->readImage($relpath);
                $this->imagick_lib->resize(200,200);
                $this->imagick_lib->saveTo($relpath);
                //end resize image
                echo $this->config->item('cdn_m_url').$new_file;
            }
        }
    }
    /**
     * 图片获取
     */
    function minimg($url=null){
        header("Content-Type: image/jpeg;text/html; charset=utf-8");
        ini_set('user_agent','Mozilla/5.0 (Linux; Android 5.1.1; Mi-4c Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036215 Safari/537.36 MicroMessenger/6.3.16.49_r03ae324.780 NetType/WIFI Language/zh_CN');
        $width = 200;
        $height = 200;
        $image = new Imagick (urldecode($url));
        $image->resizeimage($width,$height,Imagick::FILTER_LANCZOS,1);
        echo $image->getimageblob();
    }

    /**
     * 保存群
     */
    public function save(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        if(! $this->islogin()){
            $result->errcode=1;
            $result->errmsg='未登录';
            echo json_encode($result);
            return;
        }
        $id = intval($this->input->post('id'));
        $groupName = trim($this->input->post('groupName'));
        $groupImg = trim($this->input->post('groupImg'));
        if($groupName=='' || $groupImg==''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            echo json_encode($result);
            return;
        }
        $saveData=['groupName'=>$groupName,'groupImg'=>$groupImg];
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $userData=[
            'nickName'=>$user->nickName,
            'headImage'=>$user->headimgurl,
            'userId'=>$user->id,
            'createTime'=>time(),
            'updateTime'=>time()
        ];
        if(isset($id) && $id!=''){
            $saveData['id']=$id;
            $saveData['updateTime']=time();
            $save=$this->group_model->update_group($saveData);
        }else{
            $saveData['mchId']=$mchId;
            $saveData['createTime']=time();
            $saveData['updateTime']=time();
            $userData['role']=1;
            $save=$this->group_model->add_group($saveData,$userData);
        }
        if(! $save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        $result->data=$save;
        echo json_encode($result);
        return;
    }

    /**
     * 设置口令
     */
    public function save_pwd(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        if(! $this->islogin()){
            $result->errcode=1;
            $result->errmsg='未登录';
            echo json_encode($result);
            return;
        }
        $id = intval($this->input->post('id'));
        $password = trim($this->input->post('password'));
        if($id==''){
            $result->errcode=1;
            $result->errmsg='提交的信息有误';
            echo json_encode($result);
            return;
        }
        if($password==''){
            $password=$id.rand(1000,9999);
            $group_model=$this->group_model;
            function ckpass($pass,$group_model){
                $group=$group_model->get_group_by_password($pass);
                if($group){
                    $password=$id.rand(1000,9999);
                    ckpass($password,$group_model);
                }
            }
            ckpass($password,$group_model);
        }
        $group=$this->group_model->get_group_by_password($password);
        if($group){
            $result->errcode=1;
            $result->errmsg='此口令不可用，换一个试试';
            echo json_encode($result);
            return;
        }
        $saveData=['password'=>$password,'id'=>$id];
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) return;
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) return;
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) return;
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) return;
        $saveData['updateTime']=time();
        $master=$this->group_model->get_group_master($id);
        if($user->id!=$master->userId){
            $result->errcode=1;
            $result->errmsg='无权操作此群';
            echo json_encode($result);
            return;
        }
        $save=$this->group_model->update_group($saveData);
        if(! $save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        echo json_encode($result);
        return;
    }
    

    /**
     * 验证口令
     */
    public function pwd_valid(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        if(! $this->islogin()){
            $result->errcode=1;
            $result->errmsg='未登录';
            echo json_encode($result);
            return;
        }
        $password = trim($this->input->post('password')).'';
        if($password==''){
            $result->errcode=1;
            $result->errmsg='提交的信息有误';
            echo json_encode($result);
            return;
        }
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) return;
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) return;
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) return;
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) return;
        $group=$this->group_model->get_group_by_password($password);
        if(! $group || $group->mchId!=$mchId){
            $result->errcode=1;
            $result->errmsg='口令不正确';
            echo json_encode($result);
            return;
        }
        if($group->status==1){
            $result->errcode=1;
            $result->errmsg='口令不正确';
            echo json_encode($result);
            return;
        }
        $member=$this->group_model->get_group_member_one($group->id,$user->id);
        if($member){
            $result->data=$group->id;
            echo json_encode($result);
            return;
        }
        $userData=[
            'groupId'=>$group->id,
            'nickName'=>$user->nickName,
            'headImage'=>$user->headimgurl,
            'userId'=>$user->id,
            'createTime'=>time(),
            'updateTime'=>time()
        ];
        $save=$this->group_model->add_group_member($group->id,$userData);
        if(! $save){
            $result->errcode=1;
            $result->errmsg='进群失败，请重试';
            echo json_encode($result);
            return;
        }
        $result->data=$group->id;
        echo json_encode($result);
        return;
    }

    /**
     * 保存用户信息
     */
    public function save_userinfo(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        if(! $this->islogin()){
            $result->errcode=1;
            $result->errmsg='未登录';
            echo json_encode($result);
            return;
        }
        $groupId = intval($this->input->post('groupId'));
        $nickName = trim($this->input->post('nickName'));
        $headImage = trim($this->input->post('headImage'));
        if($nickName=='' || $headImage==''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            echo json_encode($result);
            return;
        }
        $saveData=['nickName'=>$nickName,'headImage'=>$headImage,'groupId'=>$groupId];
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $group=$this->group_model->get_group($groupId);
        if(! $group) return;
        $member=$this->group_model->get_group_member_one($group->id,$user->id);
        if(! $member) return;
        $saveData['userId']=$user->id;
        $saveData['updateTime']=time();
        $save=$this->group_model->update_group_member($member->id,$saveData);
        if(! $save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        $result->data=$save;
        echo json_encode($result);
        return;
    }

    /**
     * 搜索群
     */
    public function search(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        if(! $this->islogin()){
            $result->errcode=1;
            $result->errmsg='未登录';
            echo json_encode($result);
            return;
        }
        $txt = $this->input->post('keyword');
        $txt = addslashes(htmlspecialchars($txt));
        $txt = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/','&\\1',str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $txt));
        $txt=trim($txt);
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $search=$this->group_model->search_group($mchId,$txt);
        if(! $search){
            $search=[];
        }
        $result->data=$search;
        echo json_encode($result);
        return;
    }


    /**
     * 退出群
     */
    public function quit(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        if(! $this->islogin()){
            $result->errcode=1;
            $result->errmsg='未登录';
            echo json_encode($result);
            return;
        }
        $groupId = intval($this->input->post('groupId'));
        if($groupId==''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            echo json_encode($result);
            return;
        }
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $group=$this->group_model->get_group($groupId);
        if(! $group) return;
        $member=$this->group_model->get_group_member_one($group->id,$user->id);
        if(! $member) return;
        if($member->role==1){
            $del=$this->group_model->delete_group($groupId);
        }else{
            $del=$this->group_model->delete_group_member($member->id,$group->id);
        }
        if(! $del){
            $result->errcode=1;
            $result->errmsg='退出失败';
            echo json_encode($result);
            return;
        }
        echo json_encode($result);
        return;
    }
    

}
