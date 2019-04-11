<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reporting extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('reporting_model');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }
    /**
     * [daylists description]
     * @return [html] [日扫码报表视图]
     */
    public function daylists() {
        $this->load->view('reporting_daylists');
    }
    /**
     * [scan_daylist_data description]
     * @return [json] [日扫码统计数据]
     */
    public function scan_daylist_data() {
        $from=$this->input->post('from');
        $to=$this->input->post('to');
        $from=empty($from)?date('Y-m-d',strtotime("-1 month")):$from;
        $to=empty($to)?date('Y-m-d'):$to;
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->reporting_model->get_scan_dailylist($this->session->mchId,$from,$to,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data,'from'=>$from,'to'=>$to];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function download_scan_daylist(){
        $from=$this->input->get('from');
        $to=$this->input->get('to');
        $from=empty($from)?date('Y-m-d',strtotime("-1 month")):$from;
        $to=empty($to)?date('Y-m-d'):$to;
        $data=$this->reporting_model->get_scan_dailylist($this->session->mchId,$from,$to);
        $output=iconv("UTF-8","GBK",'日期,扫码次数,红包金额（元）,提现金额（元）');//使用GBK支持的字符范围更大
        $output.="\r\n";
        foreach($data as $value){
            $output.=$value['date'].','.$value['scanCount'].','.$value['rpAmount'].','.$value['transAmount']."\r\n";
        }
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename=reporting'.$from.'to'.$to.'.csv')
        ->set_header('Content-length:'.(strlen($output)-2))
        ->set_output($output);
    }

    public function get_mch_rp_used(){
        $mch_id=$this->session->mchId;
        $rps=$this->reporting_model->get_mch_rp_used($mch_id);
        $res=[];
        for($i=0;$i<count($rps);$i++){
            $res[]=$this->reporting_model->get_rp_used($rps[$i]->id);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($res));
    }
    public function get_mch_card_used(){
        $mch_id=$this->session->mchId;
        $res=$this->reporting_model->get_mch_card_used($mch_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($res));
    }
    public function get_mch_point_used(){
        $mch_id=$this->session->mchId;
        $points=[
                    [
                        'table' => 'mall_orders',
                        'name'  => '积分兑换'
                    ],
                    [
                        'table' => 'groups_scanpk_users',
                        'name'  => '扫码PK'
                    ],
                    [
                        'table' => 'user_trans',
                        'name'  => '红包兑换'
                    ],
                ];
        $res=[];
        for($i=0;$i<count($points);$i++){
            $res[]=$this->reporting_model->get_mch_point_used($mch_id,$points[$i]['table'],$points[$i]['name']);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($res));
    }
    // 获取首页已发红包总额、卡券种类、积分
    public function get_mch_indexdata(){
	    debug('indexdata');
        $data=$this->reporting_model->get_mch_indexdata($this->session->mchId);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    // 获取新增用户数据(近7天)
    public function get_mch_user_xinzeng(){
        $data=$this->reporting_model->get_mch_user_xinzeng($this->session->mchId);
        $date_string='';
        
        $endtime = date('Y-m-d');
        $begintime = date('Y-m-d', strtotime("$endtime -6 day"));
        for ($start = strtotime($begintime); $start <= strtotime($endtime); $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'date'=>$data_arr[$t],
                    'userNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['date']])){
                $item[$v['date']]=$v;
            }else{
                $item[$v['date']]['userNum']+=$v['userNum'];
            }
        }
        $item=array_values($item);
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['date'];
            //扫码量-扫码量
            $userNum[$i]=(string)$item[$i]['userNum'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'userNum'=>$userNum
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($arr));
    }

    public function get_rp_used($rp_id){
        $money_used=$this->reporting_model->get_rp_used($rp_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($money_used));
    }

    public function get_mch_daily_scanning(){
        $mch_id=$this->session->mchId;
        $dr=$this->reporting_model->get_mch_daily_scanning($mch_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($dr));
    }
    public function get_mch_daily_rp_amount(){
        $mch_id=$this->session->mchId;
        $dr=$this->reporting_model->get_mch_daily_rp_amount($mch_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($dr));
    }

    /**
     * 获取红包发放金额图表数据
     *
     * @param  boolean $start 是否为导出操作
     * @return json
     */
    public function load_money_chart($export = FALSE) {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $start = strtotime($start);
        $end = strtotime($end);
        if (! $start) {
            $start = time() - 3600 * 24 * 7;
        }
        if (! $end) {
            $end = time();
        }

        $mch_id = $this->session->mchId;
        try {
            $data = $this->reporting_model->money_chart($mch_id, $start, $end, $export);
            if ($export) {
                return $data;
            }
            $this->output->set_content_type('application/json')->
                set_output(json_encode(['data'=>$data, 'errcode'=>0]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->
                set_output(json_encode(['errmsg'=>$start, 'errcode'=>1]));
        }
    }

    /**
     * 导出报表数据
     *
     * @return stream
     */
    public function export_money_chart() {
        header('content-type:application/octet-stream');
        header('Content-Disposition: attachment; filename="红包发放金额数据.txt"');
        $rows = $this->load_money_chart(TRUE);
        foreach ($rows as $row) {
            print '日期：' . $row->get_date . "\t";
            print '金额：' . $row->amount / 100 . "\r\n";
        }
    }

    /**
     * 获取扫码记录图表数据
     *
     * @param  boolean $start 是否为导出操作
     * @return json
     */
    public function load_scan_chart($export = FALSE) {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $start = strtotime($start);
        $end = strtotime($end);
        if (! $start) {
            $start = time() - 3600 * 24 * 7;
        }
        if (! $end) {
            $end = time();
        }

        $mch_id = $this->session->mchId;
        try {
            $data = $this->reporting_model->scan_chart($mch_id, $start, $end, $export);
            if ($export) {
                return $data;
            }
            $this->output->set_content_type('application/json')->
                set_output(json_encode(['data'=>$data, 'errcode'=>0]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->
                set_output(json_encode(['errmsg'=>$start, 'errcode'=>1]));
        }
    }

    /**
     * 导出报表数据
     *
     * @return stream
     */
    public function export_scan_chart() {
        header('content-type:application/octet-stream');
        header('Content-Disposition: attachment; filename="扫码次数数据.txt"');
        $rows = $this->load_scan_chart(TRUE);
        foreach ($rows as $row) {
            print '日期：' . $row->scan_date . "\t";
            print '扫码次数：' . $row->nums . "\r\n";
        }
    }

    /**
     * 子活动扫码记录导出（包含扫码人微信昵称）
     *
     * @id 子活动的编号id 默认为null
     */
    public function export_hls_sub_activiey_export($id=null) {
        header('content-type:application/octet-stream');
        header('Content-Disposition: attachment; filename="子活动扫码次数及发放金额数据.txt"');
         $rows = $this->load_hls_sub_activiey_export($id,TRUE);
         foreach ($rows as $row) {

         	$name = empty($row->nickName) ? '红码用户' : $row->nickName;
         	$time = empty($row->scanTime) ? '未知时间' : date('Y-m-d H:i:s',$row->scanTime);
         	$sended = $row->sended == '1' ? '已发放' : '未发放';
         	$mobile = empty($row->mobile) ? '未填写' : $row->mobile;

            print '日期：' . $time . " -- ";
            print '扫码人：' . $name . " -- ";
            print '手机号码：' . $mobile . " -- ";
            print '扫码次数：' . $row->count_num . " -- ";
            print '发放金额：' . $row->amount / 100 . "元 -- ";
            print '是否发放：' . $sended . "\r\n";
        }
    }
    /**
     * 子活动扫码记录导出（包含扫码人微信昵称、手机号码）
     *
     * @id 子活动的编号id
     */
    public function load_hls_sub_activiey_export($id) {
        $mch_id = $this->session->mchId;
        try {
            $data = $this->reporting_model->sub_activiey_export($id);
            return $data;
            $this->output->set_content_type('application/json')->
            set_output(json_encode(['data'=>$data, 'errcode'=>0]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->
            set_output(json_encode(['errmsg'=>$start, 'errcode'=>1]));
        }
    }
    //用户扫码记录统计页面
    public function userscanlists() {
    	$this->load->view('reporting_userscanlists');
    }
    //日用户扫码记录
    public function user_scan_daylist_data() {
    	$from=$this->input->post('from');
        $to=$this->input->post('to');
        $from=empty($from)?date('Y-m-d',strtotime("-1 month")):$from;
        $to=empty($to)?date('Y-m-d'):$to;
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->reporting_model->get_user_scan_daylist($this->session->mchId,$from,$to,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data,'from'=>$from,'to'=>$to];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //日用户报表下载
    public function download_user_scan_daylist(){
    	$from=$this->input->get('from');
    	$to=$this->input->get('to');
        $from=empty($from)?date('Y-m-d',strtotime("-1 month")):$from;
        $to=empty($to)?date('Y-m-d'):$to;
    	$data=$this->reporting_model->get_user_scan_daylist($this->session->mchId,$from,$to);
        //$data=object_array($data);
    	$output=iconv("UTF-8","GBK",'用户ID,微信昵称,日期,扫码次数,红包金额（元）,提现金额（元）');//使用GBK支持的字符范围更大
    	$output.="\r\n";
    	for($i=0;$i<count($data);$i++){
    		$output.=$data[$i]['userId'].','.iconv("UTF-8","GBK",$data[$i]['nickName']).','.$data[$i]['date'].','.$data[$i]['scan_num'].','.$data[$i]['red_amount'].','.$data[$i]['trans_amount']."\r\n";
    	}
    	$this->output->set_content_type('application/octet-stream')
    	->set_header('Content-Disposition:attachment;filename=reporting'.$from.'to'.$to.'.csv')
    	->set_output($output);
    }
    /**
     * 用户信息界面
     */
    public function show_user_info($id=null){
    	//查询用户信息（users）
    	$user_info=$this->reporting_model->get_user_info($this->session->mchId,$id);
    	//获取账户信息（user_accounts）0 普通红包 1 裂变红包
    	$pt=$this->reporting_model->get_user_accounts($this->session->mchId,$id,'0');
    	$lb=$this->reporting_model->get_user_accounts($this->session->mchId,$id,'1');
    	//获取乐券信息
    	$card_info=$this->reporting_model->get_card_info($id,$this->session->mchId);
        //获取积分余额
        $points=$this->reporting_model->get_points_info($id,$this->session->mchId);

        // -----------------------------
        // Added by shizq - begin
        // 获取已兑换积分和未兑换积分以及明细
        $this->load->model('Point_model', 'point');
        $userPointTotalGet = $this->point->get_user_point($this->session->mchId, $id);
        $usedAmount = $this->point->get_user_point_used($this->session->mchId, $id);
        // Added by shizq - end
        $viewData = [
            'data'      => $user_info,
            'red_pt'    => $pt,
            'red_lb'    => $lb,
            'card_info' => $card_info,
            'points'    => $points,
            'user_point_total_get'  => $userPointTotalGet,
            'used_amount'           => $usedAmount
        ];
        $this->load->view('reporting_show_user_info', $viewData);
    }
    /**
     * 扫码信息界面（显示用户微信昵称、扫码时间（到秒）、区域、码批号、码文本）
     */
    public function show_scan_info($param){
        if(!isset($param)){
            exit('参数不正确！');
        }
        $data['data']=json_encode($this->_param($param));
    	$this->load->view('reporting_show_scan_info',$data);
    }
    /**
     * 用户红包记录（显示用户微信昵称、红包时间（到秒）、子活动名称）
     */
    public function show_redpack_info($param){
        if(!isset($param)){
            exit('参数不正确！');
        }
        $data['data']=json_encode($this->_param($param));
    	$this->load->view('reporting_show_redpack_info',$data);
    }
    /**
     * 用户积分记录（显示用户微信昵称、红包时间（到秒）、子活动名称）
     */
    public function show_point_info($param){
        if(!isset($param)){
            exit('参数不正确！');
        }
        $data['data']=json_encode($this->_param($param));
        $this->load->view('reporting_show_point_info',$data);
    }
    /**
     * 用户提现记录（显示用户微信昵称、提现时间（到秒）、提现金额）
     */
    public function show_trans_info($param){
        if(!isset($param)){
            exit('参数不正确！');
        }
        $data['data']=json_encode($this->_param($param));
        $this->load->view('reporting_show_trans_info',$data);
    }
    /**
     * 积分使用记录（显示用户微信昵称、兑换时间（到秒）、兑换积分）
     */
    public function show_point_used_info($param){
        if(!isset($param)){
            exit('参数不正确！');
        }
        $data['data']=json_encode($this->_param($param));
        $this->load->view('reporting_show_point_used_info',$data);
    }
    /**
     * 用户获取乐券记录
     */
    public function show_card_info($param){
        if(!isset($param)){
            exit('参数不正确！');
        }
        $data['data']=json_encode($this->_param($param));
        $this->load->view('reporting_show_card_info',$data);
    }
    //公众号推送客服自定义通知
    public function send_message(){
        $mchId = $this->session->mchId;
        $openid = $this->input->post('openid');
        $title = $this->input->post('title');
        $form = $this->input->post('form');
        $text1 = $this->input->post('text1');
        $text2 = $this->input->post('text2');
        $text3 = $this->input->post('text3');
        $res = $this->wx3rd_lib->template_send($mchId,$openid,$this->wx3rd_lib->template_format_data($mchId,'kf_notice',[$title,$form,$text1,$text2,$text3]));
        $this->output->set_content_type('application/json')->set_output(json_encode($res));
    }
    //处理用户扫码统计传递参数 返回指定格式
    private function _param($param){
        $arr=explode('_', $param);
        //处理时间
        if($arr[2]=='day'){
            $arr['start']=strtotime($arr[1].' 00:00:00');
            $arr['end']=strtotime($arr[1].' 23:59:59');
        }
        if($arr[2]=='week'){
            $date=explode('-', $arr[1]);
            $arr['start']=weekday($date[0],$date[1])['start'];
            $arr['end']=weekday($date[0],$date[1])['end'];
        }
        return [
            'uid'=>$arr[0],
            'date'=>$arr[1],
            'level'=>$arr[2],
            'productid'=>$arr[3],
            'batchid'=>$arr[4],
            'proCode'=>$arr[5],
            'cityCode'=>$arr[6],
            'areaCode'=>$arr[7],
            'tap'=>$arr[8],
            'start'=>$arr['start'],
            'end'=>$arr['end']
        ];
    }

}
