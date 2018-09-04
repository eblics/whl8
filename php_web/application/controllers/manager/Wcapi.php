<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('TOKEN','09e7a98b9457e3446a79f5d96e64c6e4');
//weixin callback api
class Wcapi extends MerchantController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('merchant_model','merchant');
        $this->load->model('batch_model','batch');
        $this->load->model('sub_activity_model');
        $this->load->model('scan_log_model');
        $this->load->model('red_packet_model','red_packet');
        $this->load->model('user_redpacket_model');
        $this->load->model('user_model','user');
        $this->load->model('batch_model');
        $this->load->model('code_version_model','code_version');
        $this->load->model('webapp_model','webapp');
        $this->load->helper('common/curl_helper');
        $this->load->library('common/common_lib');
        $this->load->library('common/code_encoder');
        //$this->load->model('Api_model', 'api_model');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }
    
    public function phpinfo(){
        phpinfo();
    }
    public function api(){
        header('content-type:text');
        if (!isset($_GET['echostr'])) {
					if($this->checkSignature()){
							$this->responseMsg();
					}
        }else{
            echo $_GET['echostr'];
            //$this->valid();
        }
    }
//验证签名
    public function valid()
    {
        if($this->checkSignature())
            echo $_GET['echostr'];
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function get_time(){
        echo strtotime($_GET['t']);
    }


    public function test_wcapi(){
       $obj=(object)[
                'toUser'=>'gh_d143628978e7',
                'fromUser'=>"ox2eiuLewqvMoOE_TDKEB6xXm9X4",
                'createTime'=>time(),
                'x'=>'21.3',
                'y'=>'112.4',
                'scale'=>'20',
                'label'=>'地理',
                'msgId'=>1
            ];
$xml_location='<xml><ToUserName><![CDATA[gh_61e7e9c70caf]]></ToUserName>
<FromUserName><![CDATA[ox2eiuLewqvMoOE_TDKEB6xXm9X4]]></FromUserName>
<CreateTime>1452670664</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[LOCATION]]></Event>
<Latitude>31.495852</Latitude>
<Longitude>120.365891</Longitude>
<Precision>65.000000</Precision>
</xml>';
$tpl_text = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
$xml_subscribe='<xml>
<ToUserName><![CDATA[gh_d143628978e7]]></ToUserName>
<FromUserName><![CDATA[ox2eiuDq9FknCxJbS_vTkCmeIzIY]]></FromUserName>
<CreateTime>AA45DAAZF</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
</xml>';
           $xml=[
            'xml_location'=>$xml_location,
            'xml_subscribe'=>$xml_subscribe,
            'xml_text'=>sprintf($tpl_text,'gh_d143628978e7','ox2eiuLewqvMoOE_TDKEB6xXm9X4',time(),$this->input->get_post('p'))];
            foreach($xml as $key=>&$value)
            {
                   $value=str_replace(["\r","\n","\r\n"],['','',''],$value);
            }
           $this->load->view('test_wcapi',$xml);
    }



    /**************************消息处理函数*****************************/
    var $msg_handlers=[
        'event'=>'receiveEvent',
        'text'=>'receiveText',
        'image'=>'receiveImage',
        'location'=>'receiveLocation',
        'voice'=>'receiveVoice',
        'video'=>'receiveVideo',
        'link'=>'receiveLink'];
     //事件处理器
    var $event_handlers=[
        'subscribe'=>'receive_evt_subscribe',
        'unsubscribe'=>'receive_evt_unsubscribe',
        'SCAN'=>'receive_evt_scan',
        'CLICK'=>'receive_evt_click',
        'LOCATION'=>'receive_evt_location',
        'VIEW'=>'receive_evt_view',
        'MASSSENDJOBFINISH'=>'receive_evt_mjf'];

    //响应消息
    public function responseMsg()
    {
        $postStr =file_get_contents("php://input");
        // if(!isset($postStr)) exit;
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            $result=$this->{$this->msg_handlers[$RX_TYPE]}($postObj);
            $this->logger("T ".$result);
            echo $result;
        }else {
            $this->logger("X ".$postStr);
            echo "";
            exit;
        }
    }


    //接收事件消息
    private function receiveEvent($object)
    {
        $evt=trim($object->Event);
        return $this->{$this->event_handlers[$evt]}($object);
    }

    //接收文本消息
    private function receiveText($obj)
    {
				//因安全原因，暂时取消输明码
				return;
        $user=$this->user_model->get_by_openid($obj->FromUserName);
        $merchant=$this->merchant_model->get_by_wxid($obj->ToUserName);
        if(isset($user)){
            $info=$this->weixin_rest_api->get_userinfo($merchant->wxAppId,$obj->FromUserName);
            if(isset($info)&&isset($info->errcode)){
                if($info->errcode==0){
                    foreach($info as $key=>$value){
                        $user->{"$key"}=$value;
                    }
                    $user->nickName=addslashes(htmlspecialchars($info->nickname));
                    unset($user->nickname);
                    $user->updateTime=time();
                    $this->user_model->save($user);
                }
            }
        }
        $content=$obj->Content->__toString();
        if(!ctype_alnum($content)){
            return $this->transmitText($obj,"欢迎来到$merchant->wxName");
        }
        $pub_code_de=$this->code_encoder->decode_pub($content);
        if($pub_code_de->errcode!=0){
            return $this->transmitText($obj,$pub_code_de->errmsg);
        }
        $code_en=$this->code_encoder->encode(
            $merchant->codeVersion,
            $merchant->id,
            $pub_code_de->result->value);
        if($code_en->errcode!=0){
            return $this->transmitText($obj,$code_en->errmsg);
        }
        $code=$code_en->result->code;
        $scaninfo=$this->scan_log_model->get_by_code($code);
        if(!empty($scaninfo)){
            if($scaninfo->openId!=$obj->FromUserName){
                return $this->transmitText($obj,'此码已被他人扫过');
            }
        }
        $batch=$this->batch_model->get_by_value($merchant->id,$pub_code_de->result->value);
        if(!isset($batch)){
            return $this->transmitText($obj,'码没有申请');
        }
        if($batch->expireTime<time()){
            return $this->transmitText($obj,'此码已过期');
        }
        if(empty($scaninfo)){
            $scaninfo=(object)[
                'code'=>$code,
                'userId'=>$user->id,
                'openid'=>$obj->FromUserName,
                'mchId'=>$merchant->id,
                'ip'=>$_SERVER['REMOTE_ADDR'],
                'scanTime'=>time(),
                'batchId'=>$batch->id,
                'isFirst'=>true,
                'over'=>0
            ];
            $scaninfo->id=$this->scan_log_model->save($scaninfo);
        }
        $activity=$this->sub_activity_model->get_best_match($scaninfo,time(),(object)['lng'=>0,'lat'=>0]);
        if(empty($activity)){
            return $this->transmitText($obj,'没有适合你的活动');
        }
        //活动还未开始
        if($activity->mainState==0){
            return $this->transmitText($obj,'没有适合你的活动');
        }
            //活动已停用
        if($activity->mainState==2){
            return $this->transmitText($obj,'这个活动已经停止');
        }
            //活动还未开始
        if($activity->state==0){
            return $this->transmitText($obj,'这个活动还未启动，敬请期待');
        }
            //活动已停用
        if($activity->state==2){
            return $this->transmitText($obj,'这个活动已经停止');
        }
        $scaninfo->activityId=$activity->id;
        $scaninfo->over=1;
        //活动是红包类型
        if($activity->activityType==0){
            $result=$this->red_packet_model->try_red_packet($activity->detailId,$activity,$scaninfo);
            if($result->errcode==0){
                $msg[] = [
                    "Title"=>'你有新到红包，请注意查收',
                    "Description"=>"您新到 $result->amount 分红包，多扫多得心情好",
                    "PicUrl"=>$this->config->item('mobile_url')."static/images/hongbao.png",
                    "Url" =>$this->config->item('mobile_url').'/user/account/'.$merchant->id];
                return $this->transmitNews($obj,$msg);
            }
            else
            {
                if(!empty($result->alt_text)){
                    return $this->transmitText($obj,$result->alt_text);
                }
                else{
                    return $this->transmitText($obj,$result->errmsg);
                }
            }
        }
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }



    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //日志记录
    private function logger($log_content)
    {
        // if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
        //     sae_set_display_errors(false);
        //     sae_debug($log_content);
        //     sae_set_display_errors(true);
        // }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
        //     $max_size = 10000;
        //     $log_filename = "log.xml";
        //     if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        //     file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        // }
        $max_size = 10000;
        $log_filename = "log.txt";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
    }
    /****************************事件处理函数*******************************************/

    function receive_evt_view($obj){
        $user=$this->user_model->get_by_openid($obj->FromUserName);
        if(!isset($user)){
            $user=(object)[];
            $user->createTime=time();
        }
        $merchant=$this->merchant_model->get_by_wxid($obj->ToUserName);
        $info=$this->weixin_rest_api->get_userinfo($merchant->wxAppId,$obj->FromUserName);
        if(!isset($info)||isset($info->errcode)){
            return;
        }
        foreach($info as $key=>$value){
            $user->{"$key"}=$value;
        }
        $user->nickName=addslashes(htmlspecialchars($info->nickname));
        unset($user->nickname);
        $user->mchId=$merchant->id;
        $user->updateTime=time();
        $this->user_model->save($user);
    }

    function receive_evt_subscribe($obj){
        $user=$this->user_model->get_by_openid($obj->FromUserName);
        $merchant=$this->merchant_model->get_by_wxid($obj->ToUserName);
        if(isset($user)){
            $info=$this->weixin_rest_api->get_userinfo($merchant->wxAppId,$obj->FromUserName);
            if(isset($info)&&isset($info->errcode)){
                if($info->errcode==0){
                    foreach($info as $key=>$value){
                        $user->{"$key"}=$value;
                    }
                    $user->nickName=addslashes(htmlspecialchars($info->nickname));
                    unset($user->nickname);
                    $user->updateTime=time();
                    $this->user_model->save($user);
                }
            }
            $amount=$this->common_lib->send_pending_packtets($user);
            //发放pending状态的乐券
            //$cardsNum=$this->common_lib->send_pending_cards($user);
            $content[] = [
                    "Title"=>$merchant->subscribeMsg,
                    "PicUrl"=>$this->config->item('mobile_url').$merchant->subscribeImgUrl
            ];
            $content[] = [
                    "Title"=>'你有新到红包，请注意查收',
                    "Description"=>"您新到 $amount 分红包，多扫多得心情好",
                    "PicUrl"=>$this->config->item('mobile_url')."static/images/hongbao.png",
                    "Url" =>$this->config->item('mobile_url').'/user/account/'.$merchant->id
            ];
            return $this->transmitNews($obj,$content);
        }
        else{
            $info=$this->weixin_rest_api->get_userinfo($merchant->wxAppId,$obj->FromUserName);
            if(isset($info)){
                if(!isset($info->errcode)){
                    $user=(object)[];
                    foreach($info as $key=>$value){
                        $user->{"$key"}=$value;
                    }
                    $user->nickName=addslashes(htmlspecialchars($info->nickname));
                    unset($user->nickname);
                    $user->mchId=$merchant->id;
                    $user->createTime=time();
                    $this->user_model->save($user);
                }
            }
            return $this->transmitText($obj,"欢迎订阅$merchant->wxName");
        }

    }

    function receive_evt_unsubscribe($obj){
        $user=$this->user_model->get_by_openid($obj->FromUserName);
        $user->subscribe=0;
        $this->user_model->save($user);
    }

    function receive_evt_location($obj){
        $merchant=$this->merchant_model->get_by_wxid($obj->ToUserName);
        $openid=(string)$obj->FromUserName;
        $mch_wxid=(string)$obj->ToUserName;
        //用户是否存在，如果不存在，取用户信息并存储
        $userinfo=$this->user->get_by_openid($openid);
        if($userinfo==NULL){
            $wx_userinfo=$this->weixin_rest_api->get_userinfo($merchant->wxAppId,$openid);
            log_message('debug',var_export($wx_userinfo,TRUE));
            if(! (isset($wx_userinfo->errcode) && $wx_userinfo->errcode!=0)){
                $wx_userinfo->nickName=addslashes(htmlspecialchars($wx_userinfo->nickname));
                unset($wx_userinfo->nickname);
                $this->user->save($wx_userinfo);
                $userinfo=$this->user->get_by_openId($openid);
            }
        }
        //寻找最早的用户扫描记录，即where isFirst=true order by scanTime desc
        $scaninfo=$this->scan_log_model->get_user_first_scaninfo($openid,$merchant->id);
        if(!isset($scaninfo)){
            return;
            // return 'no code scanned';
            // $content[] =[
            //         "Title"=>"欢迎来到".$merchant->name,
            //         "Description"=>$merchant->subscribeMsg,
            //         "PicUrl"=>$merchant->subscribeImgUrl,
            //         "Url" =>$this->config->item('mobile_url').'/merchant/subscribe/'.$merchant->id];

            // return $this->transmitNews($obj,$content);
        }

        $batch=$this->batch->get_by_en_code($scaninfo->code);
        if(!isset($batch)){
            $content = '批次未申请或已停用';
            //这条扫码记录标记为结束
            $scaninfo->over=true;
            $this->scan_log_model->save($scaninfo);
            return $this->transmitText($obj,$content);
        }
        if($batch->expireTime<time()){
            //这条扫码记录标记为结束
            $scaninfo->over=true;
            $this->scan_log_model->save($scaninfo);
            $content = 'sorry，您扫描的码过期了哦';
            return $this->transmitText($obj,$content);
        }
        //如果这个扫描记录有活动id，但over标记为false，说明用户扫码，但还未进行抽奖等
        if(isset($scaninfo->activityId)){
            //这个活动id是子活动id
            $activity=$this->sub_activity_model->get($scaninfo->activityId);
            return $this->take_activity($activity,$merchant,$scaninfo);
        }
        //没有参与过任何活动，则参加目前商家定义的活动
        $activity=$this->sub_activity_model_model->get_best_match(
            $merchant->id,
            $scaninfo->scanTime,
            (object)['lat'=>$obj->Latitude,'lng'=>$obj->Longitude]);
        return $this->take_activity($activity,$merchant,$scaninfo,$obj);
    }
    /**
     * 参与活动
     * @param  [type] $activity [description]
     * @param  [type] $merchant [description]
     * @param  [type] $scaninfo [description]
     * @return [type]           [description]
     */
    function take_activity($activity,$merchant,$scaninfo,$obj){
        //print_r($activity);
        if(!isset($activity)){
                $content = 'sorry，不存在此活动';
                $this->transmitText($obj,$content);
                $scaninfo->over=true;
                $this->scan_log_model->save($scaninfo);
                return;
            }

            //活动还未开始
            if((int) $activity->mainState===0){
                $scaninfo->over=true;
                $this->scan_log_model->save($scaninfo);
                $content = '这个活动还未启动，敬请期待';
                return $this->transmitText($obj,$content);
            }
            //活动已停用
            if((int) $activity->mainState===2){
                $scaninfo->over=true;
                $this->scan_log_model->save($scaninfo);
                $content = '这个活动已经停止';
                return $this->transmitText($obj,$content);
            }
            //活动还未开始
            if((int) $activity->state===0){
                $scaninfo->over=true;
                $this->scan_log_model->save($scaninfo);
                $content = '这个活动还未启动，敬请期待';
                return $this->transmitText($obj,$content);
            }
            //活动已停用
            if((int) $activity->state===2){
                $scaninfo->over=true;
                $this->scan_log_model->save($scaninfo);
                $content = '这个活动已经停止';
                return $this->transmitText($obj,$content);
            }
            $scaninfo->activityId=$activity->id;
            $this->scan_log_model->save($scaninfo);
            //$parent_actvity=$this->sub_activity_model->get_parent_by_id($activity->id);
            $webapp=$this->webapp->get($activity->webAppId);
            //发送活动相关的消息给用户，让用户参与抽奖等
            $content[] = [
                    "Title"=>$activity->mainName,
                    "Description"=>$activity->mainDesc,
                    "PicUrl"=>$activity->mainImgUrl,
                    "Url" =>$this->config->item('mobile_url').$webapp->path.'&actId='.$activity->id];
            return $this->transmitNews($obj,$content);
    }
}
