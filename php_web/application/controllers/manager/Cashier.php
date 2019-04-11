<?php
defined('BASEPATH') or exit('No direct script access allowed');

// #支付请求、 退款查询接口地址
// $reqURL_onLine = "https://www.yeepay.com/app-merchant-proxy/node";
// #订单查询，退款、撤销
// $OrderURL_onLine="https://cha.yeepay.com/app-merchant-proxy/command";

class Cashier extends MerchantController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('cashier_model');
        $this->load->model('merchant_model');
        $this->load->model( 'wxpay_model' );
        $this->load->helper('/common/hls');
        $this->load->helper('/common/curl');
        
        $this->mchId=$this->session->userdata('mchId');
        $this->userId = $this->session->userdata('userId');
        $this->MerId = '10012442782';
        $this->merchantKey = 'mP42238826nuW64r7yh26DGK34o2L2m81L25RG32lD7Lo1058A7iJ28at6QS';
        $this->load->library("Yeepay",array('p1_MerId'=> $this->MerId,'merchantKey'=> $this->merchantKey));
    }
    // 企业续费
    public function renew(){
        $balance=$this->wxpay_model->get_balance($this->mchId);
        $data['amount']=0;
        if($balance){
            $data['amount']=$balance->amount/100;
        }
        $data['company'] = $this->merchant_model->get_company_info($this->mchId);

        $data['userInfo'] = $this->merchant_model->get_person_info($this->userId);

        $data['orderNum'] = $p2_Order='50'.$this->mchId.date("YmdHis") . rand(10, 99);

        $this->load->view('cashier_renew',$data);
    }

    //yeepay 发起支付
    public function sendYeepayOrder(){
        $amount=$this->input->get_post('amount');
        $grade=$this->input->get_post('grade');
        $concurrencynum=$this->input->get_post('concurrencynum');
        $p2_Order=$this->input->get_post('orderNum');
        $data = array();
        $data['p0_Cmd']             = "Buy";
        $data['p1_MerId']           = $this->MerId;
        $data['p2_Order']           = $p2_Order;
        $data['p3_Amt']             = '0.01';//正式上线时请更改为上边的$amount
        $data['p4_Cur']             = "CNY";
        $data['p5_Pid']             = "lsa0.cn[vip-".$grade."]";
        $data['p6_Pcat']            = "vip";
        $data['p7_Pdesc']           = "lsa0.cn[vip-".$grade."]";
        $data['p8_Url']             = "http://".$_SERVER['HTTP_HOST']."/notify/yeepay";    
        $data['p9_SAF']             = "0";
        $data['pa_MP']              = $this->mchId.'|'.$grade.'|'.$concurrencynum;
        $data['pd_FrpId']           = "";
        $data['pm_Period']          = "7";
        $data['pn_Unit']            = "day";
        $data['pr_NeedResponse']    = "1";
        $data['pt_UserName']        = "";
        $data['pt_PostalCode']      = "";
        $data['pt_Address']         = "";
        $data['pt_TeleNo']          = "";
        $data['pt_Mobile']          = "";
        $data['pt_Email']           = "";
        $data['pt_LeaveMessage']    = "";
        $hmac                       = $this->yeepay->HmacMd5(implode($data),$this->merchantKey);

        echo "
        <html>
        <head>
        <title>红码收银台</title>
        <meta http-equiv='Content-Type' content='text/html; charset=gb2312'/>
        </head>
        <body onload='document.yeepay.submit();'>
        <form name='yeepay' action='https://www.yeepay.com/app-merchant-proxy/node' method='post'>
        <input type='hidden' name='p0_Cmd'                  value='".$data['p0_Cmd']."'>
        <input type='hidden' name='p1_MerId'                value='".$this->MerId."'>
        <input type='hidden' name='p2_Order'                value='".$data['p2_Order']."'>
        <input type='hidden' name='p3_Amt'                  value='".$data['p3_Amt']."'>
        <input type='hidden' name='p4_Cur'                  value='".$data['p4_Cur']."'>
        <input type='hidden' name='p5_Pid'                  value='".$data['p5_Pid']."'>
        <input type='hidden' name='p6_Pcat'                 value='".$data['p6_Pcat']."'>
        <input type='hidden' name='p7_Pdesc'                value='".$data['p7_Pdesc']."'>
        <input type='hidden' name='p8_Url'                  value='".$data['p8_Url']."'>
        <input type='hidden' name='p9_SAF'                  value='".$data['p9_SAF']."'>
        <input type='hidden' name='pa_MP'                       value='".$data['pa_MP']."'>
        <input type='hidden' name='pd_FrpId'                value='".$data['pd_FrpId']."'>
        <input type='hidden' name='pm_Period'               value='".$data['pm_Period']."'>
        <input type='hidden' name='pn_Unit'               value='".$data['pn_Unit']."'>
        <input type='hidden' name='pr_NeedResponse' value='".$data['pr_NeedResponse']."'>
        <input type='hidden' name='pt_UserName'         value='".$data['pt_UserName']."'>
        <input type='hidden' name='pt_PostalCode'       value='".$data['pt_PostalCode']."'>
        <input type='hidden' name='pt_Address'          value='".$data['pt_Address']."'>
        <input type='hidden' name='pt_TeleNo'               value='".$data['pt_TeleNo']."'>
        <input type='hidden' name='pt_Mobile'               value='".$data['pt_Mobile']."'>
        <input type='hidden' name='pt_Email'                value='".$data['pt_Email']."'>
        <input type='hidden' name='pt_LeaveMessage' value='".$data['pt_LeaveMessage']."'>
        <input type='hidden' name='hmac'                        value='".$hmac."'>
        </form>
        </body>
        </html>
        ";
    }
    //订单支付状态查询
    public function searchOrder(){
        $data = array();
        $data['p0_Cmd']    = "QueryOrdDetail";
        $data['p1_MerId']  = $this->MerId;
        $data['p2_Order']  = $this->input->get_post('orderNum');
        $data['pv_Ver']    = '3.0';
        $data['p3_ServiceType']   = 2;
        $data['hmac']      = $this->yeepay->HmacMd5(implode($data),$this->merchantKey);
        //发送请求
        $respdata  = HttpClient::quickPost('https://cha.yeepay.com/app-merchant-proxy/command',$data);
        //响应参数
        $arr  =  $this->yeepay->getresp($respdata);
        if($arr['r6_Order']==0){
            exit(json_encode(['errcode'=>-1,'errmsg'=>'订单不存在，请核实~']));
        }

        //本地签名
        $hmacLocal = $this->yeepay->HmacLocal($arr);
        $safeLocal= $this->yeepay->gethamc_safe($arr);

        //验签
        if($arr['hmac'] != $hmacLocal || $arr['hmac_safe'] != $safeLocal){
            exit(json_encode(['errcode'=>-1,'errmsg'=>'签名验证失败~']));
        }else{
            exit(json_encode(['errcode'=>0,'errmsg'=>$arr]));
        }

    }
    
    
    public function scanPay(){
        $param=$this->input->post('param');//$param['price'] * 100

        $pay = & load_wechat('Pay');
        $out_trade_no= '20'.$this->mchId.str_replace(' ','',str_replace('0.','',microtime())).rand(10000000,99999999).'';
        $prepay_id = $pay->getPrepayId('', '红码企业VIP'.WxCompanyVipLevel::$EnumValues[$param['grade']].'购买/续费支付',$out_trade_no,1,'http://'.$_SERVER['HTTP_HOST'].'/notify/index', 'NATIVE',$this->mchId.'|'.$param['level'].'|'.$param['grade']);
        if ($prepay_id === FALSE) {
            exit(json_encode(['code' => 'ERROR', 'info' => '创建预支付码失败，' . $pay->errMsg]));
        }
        //处理下二维码
        $code_url=str_replace("prepay_id=","",$pay->createMchPay($prepay_id)['package']);
        exit(json_encode(['errcode'=>0,'errmsg'=>$code_url,'out_trade_no'=>$out_trade_no]));
    }
    //判断订单是否支付完成
    public function is_pay(){
        $out_trade_no=$this->input->post('out_trade_no');
        $pay = & load_wechat('Pay');
        $paystatus=$pay->queryOrder($out_trade_no);
        if($paystatus['trade_state']=='SUCCESS'){
            //设置权限session
            
            exit(json_encode(['errcode' => 0, 'errmsg'=>'支付成功']));
        }else{
            exit(json_encode(['errcode' => -1, 'errmsg'=>'等待支付']));
        }
    }

    //开通vip执行扣款操作
    public function payment(){
        $amount=$this->input->post('amount');
        $grade=$this->input->post('grade');
        $concurrencynum=$this->input->post('concurrencynum');
        $out_trade_no= '20'.$this->mchId.str_replace(' ','',str_replace('0.','',microtime())).rand(10000000,99999999).'';
        //查询当前企业余额是否足够
        $balance=$this->wxpay_model->get_balance($this->mchId);
        if(($balance->amount/100)-$amount>0){
            $result=$this->cashier_model->payment($this->mchId,$amount*100);
            if($result){
                $orderdata=[
                    'mchId'=>$this->mchId,
                    'wxNum'=>'',
                    'orderId'=>$out_trade_no,
                    'amount'=>$amount*100,
                    'status'=>1,
                    'createTime'=>time(),
                    'updateTime'=>time(),
                    'rowStatus'=>0,
                    'level'=>1
                ];
                $result=$this->cashier_model->add_order($orderdata);
                $merInfo=$this->merchant_model->get_company_info($this->mchId);
                if($result){
                    //====================================更新当前企业的试用期和购买等级和过期时间
                    $merInfoData=[
                        'is_formal'=>1,
                        'grade'=>$grade,
                        'concurrencynum'=>$concurrencynum,
                        'expired'=>isset($merInfo->expired)&&$merInfo->expired!==NULL&&$merInfo->expired>=date('Y-m-d',time())?date('Y-m-d', strtotime($merInfo->expired.' +1 year')):date('Y-m-d', strtotime(' +1 year'))
                    ];
                    //更新企业信息
                    $this->merchant_model->update_merchant($this->mchId,$merInfoData);
                }
                exit(json_encode(['errcode'=>0,'errmsg'=>'开通VIP服务成功！']));
            }else{
                exit(json_encode(['errcode'=>1,'errmsg'=>'扣款失败！']));
            }
        }else{
            exit(json_encode(['errcode'=>1,'errmsg'=>'账户余额不足，扣款失败！']));
        }
    }
}
