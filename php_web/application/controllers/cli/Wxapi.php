<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 微信功能入口
 */
class Wxapi extends CI_Controller {
    /**
     * 初始化
     */
    public function __construct() {
        if(! is_cli()) {
            exit('403 Forbidden');
        }
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->library('common/common_lib');
        $this->load->model('merchant_model');
        $this->load->model('wxapi_model');
    }
    
    //处理用户提现
    public function deal_withdraw($num=1,$page=1){
        $list=$this->wxapi_model->get_some_withdraw($num,$page);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            if($v->payType==0){
                $this->wxapi_model->send_redpacket($v);
            }else if($v->payType==1){
                $this->wxapi_model->send_mchpay($v);
            }
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s')." deal with user_trans ids:".$ids;
    }

    //处理用户提现(红码代发数据)
    public function deal_withdraw_hlspay($num=1,$page=1){
        $list=$this->wxapi_model->get_some_withdraw($num,$page,1);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            if($v->payType==0){
                $this->wxapi_model->send_redpacket($v);
            }else if($v->payType==1){
                $this->wxapi_model->send_mchpay($v);
            }
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s')." deal with hls_pay user_trans ids:".$ids;
    }

    //处理用户提现(处理中状态)
    public function deal_withdraw_processing($num=1,$page=1){
        $list=$this->wxapi_model->get_some_withdraw_processing($num,$page);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            $this->wxapi_model->check_redpacket_processing($v);
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' deal with user_trans processing ids:'.$ids;
    }

    //处理用户提现(处理中状态)(红码代发数据)
    public function deal_withdraw_processing_hlspay($num=1,$page=1){
        $list=$this->wxapi_model->get_some_withdraw_processing($num,$page,1);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            $this->wxapi_model->check_redpacket_processing($v);
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' deal with hls_pay user_trans processing ids:'.$ids;
    }

    //处理用户模板消息
    public function deal_template_msg($num=1,$page=1){
        $list=$this->wxapi_model->get_some_templateMsg($num,$page);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            $this->wxapi_model->send_template_msg($v);
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' deal with user_template_msg ids:'.$ids;
    }

    //处理用户信息
    public function deal_userinfo($num=1,$page=1){
        $list=$this->wxapi_model->get_some_userinfo($num,$page);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            $this->wxapi_model->update_userinfo($v);
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' deal with user_update ids:'.$ids;
    }

    //定时检测更新企业模板消息
    public function deal_mch_template($num=1,$page=1){
        $mchList=$this->wxapi_model->get_some_merchants($num,$page);
        if(! $mchList){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($mchList as $k=>$v){
            if($v->wxAuthStatus!=1) continue;
            $this->wx3rd_lib->template_check($v->id);
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' deal with mch_template ids:'.$ids;
    }

    //公众号的所有api调用（包括第三方帮其调用）次数进行清零
    public function clear_quota($mchId){
        $result = $this->wx3rd_lib->clear_quota($mchId);
        if(! $result){
            echo 'NULL';
            return;
        }
        if($result->errcode==0){
            echo date('Y-m-d H:i:s').' clear_quota '.$mchId.' ok';
        }else{
            echo date('Y-m-d H:i:s').' clear_quota '.$mchId.' fail. errmsg:'.$result->errmsg;
        }
    }

    //给用户打标签
    public function deal_tagging_user($num=1,$page=1){
        $list=$this->wxapi_model->get_some_usertag($num,$page);
        if(! $list){
            echo 'NULL';
            return;
        }
        $ids='';
        foreach($list as $k=>$v){
            $this->wxapi_model->tagging_user($v);
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' tagging_user ids:'.$ids;
    }

    


}
