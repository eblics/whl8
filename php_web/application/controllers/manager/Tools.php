<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
** 小工具控制器 （方便快捷获取所需要的数据）
** 任何人都可以添加（请注意 不要同步到测试版，仅供开发版使用 连接的是168数据库）
** 关于多数据库和数据库动态切换请参考 http://codeigniter.org.cn/user_guide/database/connecting.html
**/
class Tools extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('tools_model');
        $this->load->library('common/code_encoder');
        $this->mchId=$this->session->userdata('mchId');
    }
    // 一键查询码相关信息
    public function search_codes(){
        $this->load->view('tools/search_codes');
    }
    // 获取相关信息
    public function get_code_info(){
        // header('content-type:application/octet-stream');
        // header('Content-Disposition: attachment; filename="码相关信息.txt"');
        header('Content-Type: application/json;charset=utf-8;');
        $codes=$this->input->post('codes');

        $codes=rtrim($codes, ',');
        $arr=explode(',', $codes);
        for($i=0;$i<count($arr);$i++){
            if(! $this->tools_model->is_mch_code(trim($arr[$i]))){
                $result[$i]=(object)['code'=>trim($arr[$i]),'errmsg'=>'此码不属于当前帐号'];
                continue;
            }
            $result[$i]=$this->tools_model->get_code_info($arr[$i]);
            if(empty($result[$i])){
                unset($result[$i]);
            }else{
                $result[$i]->code=$arr[$i];
            }
        }
        echo json_encode($result);
        // var_dump($codes);
        // $this->output->set_content_type('application/json')->set_output(json_encode($result));
        // exit;
        // foreach ($result as $row) {
        //     print '码文本：' . $row->code . "\r\n";
        //     print '所属企业：' . $row->mname.'(ID：'.$row->mid.')' . "\r\n";
        //     print '所属活动：' . $row->aname.'(ID：'.$row->aid.')' . "\r\n";
        //     print '结束日期：' . $row->aendTime. "\r\n";
        //     print '微信昵称：' . $row->nickName. "\r\n";
        //     print 'OPENID：' . $row->openid. "\r\n";
        //     print '扫码时间：' . $row->scanTime. "\r\n";
        //     print '扫码地址：' . $row->scanAddress. "\r\n";
        //     print '中奖金额：' . $row->amount. "\r\n";
        //     print '=========================================='."\r\n";
        // }
    }
    // 获取未扫码的信息
    public function get_code_noscan(){
        // header('content-type:application/octet-stream');
        // header('Content-Disposition: attachment; filename="码批次相关信息.txt"');
        header('Content-Type: application/json;charset=utf-8;');
        $codes=$this->input->post('codes');
        $codes=rtrim($codes, ',');
        $arr=explode(',', $codes);
        for($i=0;$i<count($arr);$i++){
            if(! $this->tools_model->is_mch_code(trim($arr[$i]))){
                $result[$i]=(object)['code'=>trim($arr[$i]),'errmsg'=>'此码不属于当前帐号'];
                continue;
            }
            $code_ret=$this->code_encoder->decode(trim($arr[$i]));
            if($code_ret->errcode!=0){
                $result[$i]=(object)['errmsg'=>$code_ret->errmsg];
            }else{
                $result[$i]=$this->tools_model->get_code_noscan($code_ret);
            }
            if(empty($result[$i])){
                unset($result[$i]);
            }else{
                $result[$i]->code=trim($arr[$i]);
            }
        }
        echo json_encode($result);
        // foreach ($result as $row) {
        //     print '码文本：' . $row->code . "\r\n";
        //     if(property_exists($row,'errmsg')){
        //         print '' . $row->errmsg. "\r\n";
        //     }else{
        //         print '所属企业：' . $row->mchName.'(ID：'.$row->mchId.')' . "\r\n";
        //         print '所属批次：' . $row->batchNo.'(ID：'.$row->batchId.')' . "\r\n";
        //         print '批次状态：' . ($row->batchState==0?'申请':'').($row->batchState==1?'激活':'').($row->batchState==2?'停用':''). "\r\n";
        //     }
        //     print '=========================================='."\r\n";
            
        // }
    }
    // 一键查用户的封禁状态
    public function search_users_status(){
        $this->load->view('/tools/search_users_status');
    }
    // 查询用户的相关的信息
    public function get_userscan_info(){
        $userId=$this->input->post('userId');
        $data=$this->tools_model->get_userscan_info($userId);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    // 批量查询openid相关信息
    public function getInfo_from_openid(){
        if (! file_exists('/var/upload/from_openid.txt')) {
            echo '请先上传数据文件';
            return;
        }
        $content = file_get_contents('/var/upload/from_openid.txt');
        $openIdArr = explode("\r\n", $content);
        for($i=0;$i<count($openIdArr);$i++){
            $result[$i]=$this->tools_model->getInfo_from_openid($openIdArr[$i]);
        }
        $output=iconv("UTF-8","GBK",'用户id,openid,企业ID,用户状态（0-正常 1-封禁）,扫码次数,红包个数,红包金额,账户余额,提现金额,余额（红包-提现）');
        $output.="\r\n";
        foreach ($result as $row) {
            $str_arr = array();
            foreach ($row as $column) {
                $str_arr[] = '"' . str_replace('"', '""', iconv("UTF-8","GBK//IGNORE",$column)) . '"';
            }
            $output.=implode(',', $str_arr) . PHP_EOL;
        }
       
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename=账户余额大于余额'.date('Y-m-d H:i:s').'.csv')
        ->set_output($output);
    }

    //码轨迹追踪与用户扫码轨迹
    public function codetrace(){
        $this->load->view('/tools/codetrace');
    }
    //追踪
    public function get_trace(){
        $term=$this->input->post('term');
        $type=$this->input->post('type');
        //判断码或者 用户是否属于当前商户
        if($this->mchId>0){
            if($type==1){
                if(! $this->tools_model->is_mch_code(trim($term))){
                    exit(json_encode(['errcode'=>-1,'errmsg'=>'该码不属于当前企业！']));
                }
            }

            if($type==2||$type==3){
                $muser=$this->tools_model->get_user($type,$term);
                if(empty($muser)||$muser->mchId!==$this->mchId){
                    exit(json_encode(['errcode'=>-1,'errmsg'=>'该用户不属于当前企业！']));
                }
            }
        }

        $data=$this->tools_model->get_trace($type,$term);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * 测试图片裁切为圆
     * @return void
     */
    public function test_images($id = 0) {
        getCircleAvatar('/var/www/dev/lsa0.cn/merchant/www/static/images/huizhang.png', '/var/www/hls/'.$id.'.png', 50);
    }
}
