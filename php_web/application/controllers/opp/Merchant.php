<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Merchant extends OppController {
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('OMerchant_model', 'merchant');
        $this->load->helper('sms');
    }
    public function index() {
        $status = $this->input->get('status');
        if (empty($status)) {
            if(uri_string() == ''){
                $status = 0;
            }
        }
        $title = [
            "所有企业列表", 
            "已审企业列表",
            "驳回企业列表", 
            "冻结企业列表", 
            "待审企业列表", 
            1
        ];
        if (! isset($status)) {
            $status = 0;
        }
        $data = ['title' => $title[$status], 'value' => $status];
        $this->load->view('merchant', $data);
    }

    public function add() {
        $this->load->view('merchant_edit', ['value' => 7]);
    }

    public function review() {
        $mch_id = $this->input->get('id');
        $merchant = $this->merchant->get_mch($mch_id);
        $code_version = $this->merchant->get_codes();
        if (! $merchant) {
            show_404();
        } else {
            $this->load->view('merchant_review', ['res' => $merchant,'code_version'=>$code_version,'expireTime'=>time(),'value'=>10]);
        }
    }
    /**
     * 接受企业审核操作
     */
    public function get_check(){
        /**
         *  SMS_10686349 驳回模板  
         *  SMS_10671404 通过模板
         **/
        $result = (object)array(
                'errcode'=>0,
                'errmsg'=>''
            );
        $status = $this->input->post('status');
        $id = $this->input->post('id');
        $codeVersion = $this->input->post('code');
        $codeLimited = $this->input->post('codeLimited');
        if(!empty($codeLimited) && !is_numeric($codeLimited)){
            $this->ajaxResponse([], '参数错误', 1);
            exit;
        }
        if($codeLimited == 0){
            $codeLimited = null;
        }
        $expireTime = $this->input->post('expireTime');
        $str = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $array = array();
        $count = sizeof($str);
        for($i=0;$i<$count;$i++){
            $f = $str[$i];
            for($k=0;$k<$count;$k++){
                $s = $str[$k];
                $string = $f.$s;
                array_push($array,$string);
            }
        }
        //这里读取数据库信息
        $res_version = $this->merchant->get_cversion();
        $res_array = array();
        if($res_version){
            foreach ($res_version as $value) {
                array_push($res_array, $value->code);
            }
        }
        $array_diff = array_diff($array, $res_array);
        $merchant = $this->merchant->get_mch($id);
        if($status == 2) {
            $checkReason = $this->input->post('checkReason');
            $data = array(
                'checkReason'=>$checkReason,
                'checkTime'=>time(),
                'status'=>$status
            );
            $template_id = 'SMS_10686349';
            try {
                $phoneNum = $this->merchant->get_account_num($id);
            } catch (Exception $e) {
                $this->ajaxResponse([], '查询号码失败', 52001);
            }
        }
        if($status == 1 && empty($merchant->code)) {
            $data = array(
                'checkReason'=>'审核通过',
                'checkTime'=>time(),
                'status'=>1,
                'codeVersion'=>$codeVersion,
                'code'=>reset($array_diff),
                'codeLimited'=>$codeLimited,
                'expired'=>$expireTime
            );
            $template_id = 'SMS_10671404';
            try {
                $phoneNum = $this->merchant->get_account_num($id);
            } catch (Exception $e) {
                $this->ajaxResponse([], '查询号码失败', 52001);
            }
        }
        if($status == 1 && isset($merchant->code)) {
            $data = array(
                'checkReason'=>'审核通过',
                'checkTime'=>time(),
                'status'=>$status,
                'codeVersion'=>$codeVersion
            );
            $template_id = 'SMS_10671404';
            try {
                $phoneNum = $this->merchant->get_account_num($id);
            } catch (Exception $e) {
                $this->ajaxResponse([], '查询号码失败', 52001);
            }
        }
        try {
            $this->merchant->check_account($id,$data);
            notice_sms($phoneNum, $template_id);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }
    public function test1(){
        $a = '2020-10-09';
        $b = '2020-10-01';
        $c = $a - $b;
        $d = strtotime($a);
        var_dump($d);

    }
    /**
     * 重置微信号
     */
    public function reset_wechat(){
        $result = (object)array(
                'errcode'=>0,
                'errmsg'=>''
            );
        $id = $this->input->post('id');
        $ishop = $this->input->post('e');
        if(isset($id)){
            $res = $this->merchant->reset($id,$ishop);
            if($res){
                $this->output->set_output(json_encode($result));
            }else{  
                $result->errcode = 1;
                $result->errmsg = '重置失败';
                $this->output->set_output(json_encode($result));
            }
        }
    }
    /**
     * 预审核界面
     */
    public function pre_review() {
        $mch_id = $this->input->get('id');
        $merchant = $this->merchant->get_mch($mch_id);
        $codes = $this->merchant->get_codes();
        $this->load->view('merchant_pre',['title'=>'预审核企业','merchant'=>$merchant,'codes'=>$codes,'id'=>$mch_id]);
    }
    /**
     * 发送短信
     */
    public function send_ms() {
        $this->load->view('merchant_sendms',['value' => 8]);
    }

     /**
     * 图片上传方法
     */
    public function upload() {
        $filepath= '/files/private/upload/'.$this->mchId;
        echo upload_file('gif|jpg|png',500,$filepath);
    }
    /**
     * 附件上传方法
     */
    public function attaupload() {
        $filepath= '/files/private/cert/'.$this->mchId;
        echo upload_file('pem',500,$filepath);
    }
    /**
     * 申请开能摇一摇
     */
    public function shakearound_register($mchId) {
        header("Content-type",'application/json;charset=utf-8;');
        $result = $this->wx3rd_lib->shakearound_register($mchId);
        echo json_encode($result);
    }
    /**
     * 检查摇一摇审核状态
     */
    public function shakearound_auditstatus($mchId) {
        header("Content-type",'application/json;charset=utf-8;');
        $result = $this->wx3rd_lib->shakearound_auditstatus($mchId);
        echo json_encode($result);
    }
    /**
     * 锁定用户   
     */
    public function search(){
        // 这里的value=9无实际意义，兼容上面企业管理代码
        $this->load->view('merchant_search',['title'=>'搜索用户','value' => 9]);
    }
    /**
     * 解锁用户
     */
    public function lock_list(){
        $this->load->view('merchant_lock',['title'=>'锁定用户列表','value'=>9]);
    }
    /**
     * 申请解锁列表
     */
    public function appeal_list(){
        $this->load->view('merchant_unlock_wait',['title'=>'申请解锁列表','value'=>9]);
    }
    /**
     * 查找用户
     */
    public function find_lock(){

    }
    /**
     * 白名单用户显示列表
     */
    public function white_list(){
        $this->load->view('merchant_ulist',['title'=>'白名单']);
    }
    /**
     * 黑名单用户显示列表
     */
    public function black_list(){
        $this->load->view('merchant_ulist',['title'=>'黑名单','value'=>11]);

    }
    public function test(){
        // $result = $this->db->where('id',1999912312)->update('users_common',['commonStatus'=>0]);
        // if($this->db->affected_rows()==0){
        //     echo '啥都没更新到';
        // }else{

        // }
        $r = $this->db->where('id',19500)->get('users_common')->row();
        var_dump($r);
    }

    /**
     * 切换支付帐号
     */
    public function edit_payaccounttype(){
        $result = (object)array(
                'errcode'=>0,
                'errmsg'=>''
            );
        $mchId = $this->input->post('mchId');
        $payAccountType = $this->input->post('payAccountType');
        $res = $this->merchant->edit_payaccounttype($mchId,$payAccountType);
        if($res){
            $this->output->set_output(json_encode($result));
        }else{  
            $result->errcode = 1;
            $result->errmsg = '执行失败';
            $this->output->set_output(json_encode($result));
        }
    }
}