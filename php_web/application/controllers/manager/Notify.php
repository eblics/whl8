<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notify extends MerchantController {
    public function __construct() {
        parent::__construct();
        $this->load->model('cashier_model');
        $this->load->helper('/common/hls');
        $this->load->model ('merchant_model');
        $this->MerId = '10012442782';
        $this->merchantKey = 'mP42238826nuW64r7yh26DGK34o2L2m81L25RG32lD7Lo1058A7iJ28at6QS';
        $this->load->library("Yeepay",array('p1_MerId'=> $this->MerId,'merchantKey'=> $this->merchantKey));
    }
    public function index(){
        $notify = & load_wechat('Pay');
        $notifyInfo = $notify->getNotify();
        if ($notifyInfo['result_code'] == 'SUCCESS') {
            $total_fee=$notifyInfo['total_fee'];
            //判断是否回调过 防止多次回调
            $nnotify=$this->cashier_model->get_notify_by_wxnum($notifyInfo['transaction_id']);
            if(empty($nnotify)){
                //====================================回调记录写入
                $ndata=array(
                    'openid'=>$notifyInfo['openid'],
                    'wxnum'=>$notifyInfo['transaction_id'],
                    'num'=>$notifyInfo['out_trade_no'],
                    'text'=>json_encode($notifyInfo),
                    'attr'=>$notifyInfo['attach'],
                    'createTime'=>time()
                );
                $this->cashier_model->add_notify($ndata);
                //====================================订单处理
                //分割附带参数  任何支付都要约定好的 attach 0->mchId 1->订单类型（1 企业会员vip购买/续费 2 后期拓展） 2->企业VIP购买等级（0基础版 1标准版 2高级版 3旗舰版）
                if(isset($notifyInfo['attach'])){
                    $attrarr=explode('|', $notifyInfo['attach']);
                    if($attrarr[1]==1){//企业VIP购买
                        $orderdata=[
                            'mchId'=>$attrarr[0],
                            'wxNum'=>$notifyInfo['transaction_id'],
                            'orderId'=>$notifyInfo['out_trade_no'],
                            'amount'=>$total_fee,
                            'status'=>1,
                            'createTime'=>time(),
                            'updateTime'=>time(),
                            'rowStatus'=>0,
                            'level'=>1
                        ];
                        $result=$this->cashier_model->add_order($orderdata);
                        $merInfo=$this->merchant_model->get_company_info($attrarr[0]);
                        if($result){
                            //====================================更新当前企业的试用期和购买等级和过期时间
                            $merInfoData=[
                                'is_formal'=>1,
                                'grade'=>$attrarr[2],
                                'expired'=>isset($merInfo->expired)&&$merInfo->expired!==NULL&&$merInfo->expired>=date('Y-m-d',time())?date('Y-m-d', strtotime($merInfo->expired.' +1 year')):date('Y-m-d', strtotime(' +1 year'))
                            ];
                            //更新企业信息
                            $this->merchant_model->update_merchant($attrarr[0],$merInfoData);
                        }
                    }
                }

                //====================================告诉腾讯已接收到
                echo 'SUCCESS';
            }
        }
    }

    //易宝支付回调结果处理
    public function yeepay(){
        $data=array();

        $data['p1_MerId']       = $this->input->get_post('p1_MerId');   
        $data['r0_Cmd']         = $this->input->get_post('r0_Cmd');
        $data['r1_Code']        = $this->input->get_post('r1_Code');
        $data['r2_TrxId']       = $this->input->get_post('r2_TrxId');
        $data['r3_Amt']         = $this->input->get_post('r3_Amt');
        $data['r4_Cur']         = $this->input->get_post('r4_Cur'); 
        $data['r5_Pid']         = $this->input->get_post('r5_Pid');
        $data['r6_Order']       = $this->input->get_post('r6_Order');
        $data['r7_Uid']         = $this->input->get_post('r7_Uid');
        $data['r8_MP']          = $this->input->get_post('r8_MP');
        $data['r9_BType']       = $this->input->get_post('r9_BType'); 
        $data['hmac']           = $this->input->get_post('hmac');
        $data['hmac_safe']      = $this->input->get_post('hmac_safe');

        // var_dump($data);
        //本地签名
        $hmacLocal = $this->yeepay->HmacLocal($data);
        // echo "</br>hmacLocal:".$hmacLocal;
        $safeLocal= $this->yeepay->gethamc_safe($data);
        // echo "</br>safeLocal:".$safeLocal;
        //验签
        if($data['hmac']     != $hmacLocal    || $data['hmac_safe'] !=$safeLocal)
        {   
            echo "验签失败";
            return;
        }else{
            if ($data['r1_Code']=="1" ){
                //判断是否回调过 防止多次回调
                $nnotify=$this->cashier_model->get_notify_by_wxnum($data['r2_TrxId']);
                if(empty($nnotify)){
                    if($data['r9_BType']=="1"){
                        echo  "
                        <html>
                        <head>
                        <title>To YeePay Page</title>
                        <meta http-equiv='refresh' content='3; url=http://".$_SERVER['HTTP_HOST']."/cashier/renew'>
                        </head>
                        <body>
                            订单支付成功，正在跳转请稍后~
                        </body>
                        </html>
                        ";
                    }elseif($data['r9_BType']=="2"){
                        //拆分参数
                        $arrs = explode('|', $data['r8_MP']);
                        //====================================回调记录写入
                        $ndata=array(
                            'openid'=>$arrs[0],
                            'wxnum'=>$data['r2_TrxId'],
                            'num'=>$data['r6_Order'],
                            'text'=>json_encode($data),
                            'attr'=>$data['r8_MP'],
                            'createTime'=>time()
                        );
                        $this->cashier_model->add_notify($ndata);
                        //====================================订单写入
                        $orderdata=[
                            'mchId'=>$arrs[0],
                            'wxNum'=>$data['r2_TrxId'],
                            'orderId'=>$data['r6_Order'],
                            'amount'=>$data['r3_Amt']*100,
                            'status'=>1,
                            'createTime'=>time(),
                            'updateTime'=>time(),
                            'rowStatus'=>0,
                            'level'=>1
                        ];
                        $result=$this->cashier_model->add_order($orderdata);
                        $merInfo=$this->merchant_model->get_company_info($arrs[0]);
                        //====================================更新当前企业的试用期和购买等级和过期时间
                        $merInfoData=[
                            'is_formal'=>1,
                            'grade'=>$arrs[1],
                            'concurrencynum'=>$arrs[2],
                            'expired'=>isset($merInfo->expired)&&$merInfo->expired!==NULL&&$merInfo->expired>=date('Y-m-d',time())?date('Y-m-d', strtotime($merInfo->expired.' +1 year')):date('Y-m-d', strtotime(' +1 year'))
                        ];
                        //更新企业信息
                        $this->merchant_model->update_merchant($arrs[0],$merInfoData);

                        #如果需要应答机制则必须回写success.
                        echo "SUCCESS";
                        return;  
                    }
                }
            }
        }
    }
}
