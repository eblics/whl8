<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 微信支付入口
 */
class Wxpay extends MerchantController {

    public function __construct() {
        parent::__construct();
        $this->load->library('common/wxpay/WxPayNativePay');
        $this->load->model('wxpay_model');
        $this->load->model('merchant_model');
        $this->mchId = $this->session->userdata('mchId');
    }

    /**
     * 订单列表
     * path /wxpay/data_list
     * @return json
     */
    public function data_lists() {
        $orderList = $this->wxpay_model->get_mch_list();
        $this->ajaxResponseSuccess($orderList);
    }

    //余额充值界面
    function balance(){
        $balance=$this->wxpay_model->get_balance($this->mchId);
        $amount=0;
        if($balance){
            $amount=$balance->amount/100;
        }
        $merchant=$this->merchant_model->get($this->mchId);
        $viewData = ['balance'=>sprintf('%.2f',$amount),'hlspay'=>$merchant->payAccountType];
        $this->load->view('mch_balance',$viewData);
    }

    /**
     * 微信支付二维码
     * path /wxpay/order
     * @return images
     */
    function order(){
        $productId=$this->input->get_post('orderid', TRUE);
        if(! isset($productId)){
            exit();
        }
        $order=$this->wxpay_model->get_mch_order($productId);
        if(! $order) return;
        $input = new WxPayUnifiedOrder();
        $input->SetBody("欢乐扫平台充值");
        $input->SetAttach("欢乐扫平台充值");
        $outTradeNo=WxPayConfig::MCHID.date("YmdHis");
        $input->SetOut_trade_no($outTradeNo);
        $input->SetTotal_fee($order->amount);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("欢乐扫平台充值");
        $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/wxpay/notify/$productId/$outTradeNo");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($productId);
        $result = $this->wxpaynativepay->GetPayUrl($input);
        $payQrcodeUrl=$result["code_url"];
        $this->load->library('common/qrcode');
        QRcode::png($payQrcodeUrl);
    }

    //订单支付回调界面
    function notify($orderId = NULL, $outTradeNo = NULL) {
        if (! isset($orderId) || ! isset($outTradeNo)) {
            exit();
        }
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xmlSuccess = '<xml><return_code><![CDATA[SUCCESS]]></return_code>';
        $xmlSuccess .= '<return_msg><![CDATA[OK]]></return_msg></xml>';
        if (! $postObj) {
            if (! isProd()) {
                try {
                    $this->wxpay_model->deal_order($orderId, $outTradeNo);
                    echo $xmlSuccess;
                } catch (Exception $e) {
                    if ($e->getCode() === 100404) {
                        echo $xmlSuccess;
                    } else {
                        exit('FAIL:'. $e->getMessage());
                    }
                }
            }
            exit();
        }
        if ($postObj->result_code != 'SUCCESS' || $postObj->return_code != 'SUCCESS') {
            error('result_code: '. $postObj->result_code);
            error('return_code: '. $postObj->return_code);
            $params  = "";
            $params .= "<xml>";
            $params .=   "<return_code>FAIL</return_code>";
            $params .=   "<return_msg>". $postObj->return_msg ."</return_msg>";
            $params .= "</xml>";
            exit($params);
        }
        if ($outTradeNo != $postObj->out_trade_no) {
            error('请求数据单号错误 -> '. $outTradeNo .':'. $postObj->out_trade_no);
            $params  = "";
            $params .= "<xml>";
            $params .=   "<return_code>FAIL</return_code>";
            $params .=   "<return_msg>数据单号不匹配</return_msg>";
            $params .= "</xml>";
            exit($params);
        }
        try {
            $this->wxpay_model->deal_order($orderId, $outTradeNo);
            echo $xmlSuccess;
        } catch (Exception $e) {
            if ($e->getCode() === 100404) {
                echo $xmlSuccess;
            } else {
                exit('FAIL:'. $e->getMessage());
            }
        }
    }

    //检查订单支付情况
    function check_mch_order(){
        header("Content-type",'application/json;charset=utf-8;');
        $result=(object)[
            'errcode'=>0,
            'errmsg'=>''
        ];
        $orderId=$this->input->get_post('orderId', TRUE);
        if(! isset($orderId)){
            $result->errcode=1;
            $result->errmsg='参数有误';
            echo json_encode($result); 
            return;
        }
        $order=$this->wxpay_model->get_mch_order($orderId);
        if(! $order){
            $result->errcode=1;
            $result->errmsg='订单不存在';
            echo json_encode($result); 
            return;
        }
        if($order->status!=1){
            $result->errcode=1;
            $result->errmsg='未支付';
            echo json_encode($result); 
            return;
        }
        echo json_encode($result); 
    }



    //获取未支付订单
    function get_mch_order($orderId){
        header("Content-type",'application/json;charset=utf-8;');
        $order=$this->wxpay_model->get_mch_order($orderId);
        echo json_encode($order);
    }

    //获取未支付订单
    function get_mch_order_doing(){
        header("Content-type",'application/json;charset=utf-8;');
        $order=$this->wxpay_model->get_mch_order_doing();
        echo json_encode($order);
    }

    //订单生成
    function order_add(){
        header("Content-type",'application/json;charset=utf-8;');
        $result=(object)[
            'errcode'=>0,
            'errmsg'=>'',
            'data'=>(object)[]
        ];
        $amount=$this->input->get_post('amount', TRUE);
        if(intval($amount)<=0){
            $result->errcode=1;
            $result->errmsg='金额输入有误';
            echo json_encode($result);
            return;
        }
        $orderId = '10'.$this->mchId.str_replace(' ','',str_replace('0.','',microtime()));
        $orderId .= rand(10000000,99999999).'';
        $data=[
            'mchId'=>$this->mchId,
            'amount'=>intval($amount)*100,
            'orderId'=>$orderId,
            'createTime'=>time(),
            'updateTime'=>time(),
            'status'=>0,
            'level'=>0
        ];
        if (! isProd()) {
            $data['amount'] = mt_rand(1, 3);
        }
        $save=$this->wxpay_model->order_save($data);
        if(! $save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        $result->data=(object)$data;
        $result->data->amount=intval($amount);
        $result->data->createTime=date('Y-m-d h:i:s',$result->data->createTime);
        echo json_encode($result);
    }

    //订单取消
    function order_cancel(){
        header("Content-type",'application/json;charset=utf-8;');
        $result=(object)[
            'errcode'=>0,
            'errmsg'=>'',
            'data'=>(object)[]
        ];
        $orderId=$this->input->get_post('orderId', TRUE);
        $order=$this->wxpay_model->get_mch_order($orderId);
        if(! $order){
            $result->errcode=1;
            $result->errmsg='订单号不存在';
            echo json_encode($result);
            return;
        }
        if($order->status==1){
            $result->errcode=1;
            $result->errmsg='此订单无法取消';
            echo json_encode($result);
            return;
        }
        $order->rowStatus=1;
        $update=$this->wxpay_model->order_save($order);
        if(! $update){
            $result->errcode=1;
            $result->errmsg='操作失败';
            echo json_encode($result);
            return;
        }
        echo json_encode($result);
    }

    

}