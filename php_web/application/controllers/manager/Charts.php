<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Charts extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('charts_model');
        $this->load->model('ranking_model');
        $this->load->model('activity_model');
        $this->load->model('product_model');
        $this->mchId=$this->session->userdata('mchId');
    }

	/**
     * CI控制器默认入口
     */
    public function index(){
        $data['data']=$this->ranking_model->areas();
        $this->load->view('charts_userscan',['data'=>$data]);
    }
    public function get_batchs($charts = NULL){
        $productid=$this->input->post('productid');
        $data=$this->charts_model->get_batchs($this->mchId,$productid);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function get_products($charts = NULL){
        if (! isset($charts)) {
            $data=$this->charts_model->get_products($this->mchId);
        } else if ($charts === 'wusu_score') {
            $data=$this->charts_model->getScoreChartsProduct($this->mchId);
        } else if ($charts === 'wusu_code') {
            $data=$this->charts_model->getCodeChartsProduct($this->mchId);
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function get_category(){
        $data=$this->activity_data();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 返回两个日期之间的日期 
     * add by George
     */
    public function get_every_day(){
        $dt = $this->input->post('dt');
        // $startTime = substr($dt,8,9);
        // $endTime = substr($dt,19,20);
        $start = substr($dt,0,10);
        $end = substr($dt,11,20);
        $dt_start = strtotime($start);
        $dt_end   = strtotime($end);
        $array = [];
        do { 
            //将 Timestamp 转成 ISO Date 输出
            array_push($array, date('Y-m-d', $dt_start));
        } while (($dt_start += 86400) <= $dt_end);
        $this->output->set_content_type('application/json')->set_output(json_encode($array));
    }
    /**
     * 请求每日数据
     * add by George
     */
    public function get_every_data(){
        $edata = $this->input->post('etata');
        $this->output->set_content_type('application/json')->set_output(json_encode($edata));
    }
    /***************************************用户扫码统计*****************************************/
    /**
     * 用户扫码统计首页
     */
    public function userscan(){
        $data['data']=$this->ranking_model->areas();
    	$this->load->view('charts_userscan',['data'=>$data]);
    }
    /**
     * 获取月份有几周
     */
    public function get_weekinfo(){
        $year=$this->input->post('year');
        $month=$this->input->post('month');
        if($month==0){
            $weekinfo=get_week($year);
        }else{
            $weekinfo=get_weekinfo($year.'-'.$month);
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($weekinfo));
    }
    /**
     * 获取用户扫码分析图数据
     */
    public function get_userscan_data(){
        $param=$this->input->post('param');
        $data=$this->charts_model->get_userscan_data($this->mchId,$param);
        //处理数据为指定格式
        if($param['month']==0){//年份数据
            $data=userscan_year_arr($param['year'],$data);
        }
        if($param['week']==0&&!$param['month']==0){//月份数据1-31天
            if(isset($param['level'])&&$param['level']=='week'){
                $data=userscan_weekly_arr($param['year'],$param['month'],$data);
            }else{
                $data=userscan_monthly_arr($param['year'],$param['month'],$data);
            } 
        }
        if(!$param['week']==0){//具体周的数据
            //处理时间
            $timearr=explode("_",$param['weektime']);
            $data=userscan_daily_arr($timearr[0],$timearr[1],$data);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //获取表格的数据
    public function get_userscan_data_table(){
        $param=$this->input->post('param');
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->charts_model->get_user_scan_daylist($this->mchId,$param,$count,$start,$length);
        // $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        // $this->output->set_content_type('application/json')->set_output(json_encode($data));
        echo $data;
    }
    //下载扫码统计表格
    public function down_userscan_date(){
        $param=array(
            'year'=>$this->input->post('year'),
            'month'=>$this->input->post('month'),
            'week'=>$this->input->post('week'),
            'day'=>$this->input->post('day'),
            'pro'=>$this->input->post('pro'),
            'city'=>$this->input->post('city'),
            'area'=>$this->input->post('area'),
            'productid'=>$this->input->post('productid'),
            'batchid'=>$this->input->post('batchid'),
        );
        $level=$this->input->post('level');
        $weektime=$this->input->post('weektime');
        $is_detail=$this->input->post('is_detail');//下载详细的用户扫码数据

        if(isset($level)){
            $param['level']=$this->input->post('level');
        }
        if(isset($weektime)){
            $param['weektime']=$this->input->post('weektime');
        }
        $title=get_down_title($param);


        // 下载详细用户扫码数据
        if(isset($is_detail)&&$is_detail==1){
            $data=$this->charts_model->get_user_scan_detaillist($this->mchId,$param);
            $output=iconv("UTF-8","GBK",'扫码记录id,用户id,微信昵称,扫码时间,卡券发放时间,乐码id,活动id,红包个数,红包金额（分项）,乐券个数,乐券名称（分项）,扫码地址');
        }else{
            $data=$this->charts_model->get_user_scan_daylist($this->mchId,$param);
            $output=iconv("UTF-8","GBK",'用户ID,微信昵称,'.$title['title'].',扫码次数,红包金额（元）,积分,提现金额（元）,乐券（张）,积分使用');
        }

        // 去除多余的参数
        for($i=0;$i<count($data);$i++){
            if(isset($data[$i]['level'])){
                unset($data[$i]['level']);
            }
        }
        $output.="\r\n";
        foreach ($data as $row) {
            if(isset($row['level'])){
                unset($row['level']);
            }
            if(isset($row['date'])){
                $row['date']="\t".$row['date'];
            }
            $str_arr = array();
            foreach ($row as $column) {
                $str_arr[] = '"' . str_replace('"', '""', iconv("UTF-8","GBK//IGNORE",$column)) . '"';
            }
            $output.=implode(',', $str_arr) . PHP_EOL;
        }
       
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename=用户扫码统计量_'.$title['time'].'.csv')
        ->set_output($output);
    }
    /***********************************时段扫码统计*************************************/
    /**
     * 时段扫码统计首页
     */
    public function period(){
        $this->load->view('charts_period');
    }
    /**
     * 时段扫码分析数据
     */
    public function period_get_data(){
        $param=$this->input->post('param');
        // var_dump($param);
        // exit();
        $data=$this->charts_model->period_get_data($this->mchId,$param);
        $datas=period_data_creat($data);
        $this->output->set_content_type('application/json')->set_output(json_encode(['data'=>$datas['data'],'string'=>$datas['linedata']]));
    }
    /**
     * 下载扫码统计量
     */
    public function down_period_date(){
        $param=json_decode($this->input->get_post('param'), true);
        $data=json_decode($this->input->get_post('data'), true);
        $title=get_down_title($param);
        $output=iconv("UTF-8","gbk",'时间,扫码次数');
        $output.="\r\n";
        for($i=0;$i<count($data);$i++){
            $output.=$data[$i]['time'].','.$data[$i]['scanNum']."\r\n";
        }
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename='.iconv("UTF-8","gbk",'扫码统计量_'.$title['time']).'.csv')
        ->set_output($output);
    }
    /***************************************区域分布*****************************************/
    /**
     * 区域分布页面
     */
    public function region(){
        $this->load->view('charts_region');
    }
    /***********************************区域分布-数据汇总*************************************/
    /**
     * 数据汇总参数筛选数据（今日、昨日、当月）
     */
    public function get_sum_of_data(){
    	$param=$this->input->post('param');
    	$data=$this->charts_model->get_sum_of_data_soure($this->mchId,$param);
    	$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /***********************************区域分布-地域分布*************************************/
    /**
     * 获取省份地图数据（左边）
     */
    public function get_pro_map_data(){
        $param=$this->input->post('param');
        $data=$this->charts_model->get_pro_map_data_soure($this->mchId,$param);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 获取城市地图数据（4个直辖市特殊处理）（右边）
     */
    public function get_city_map_data(){
        $id=$this->input->post('id');
        $param=$this->input->post('param');

        $zxs = array("北京市", "上海市", "天津市", "重庆市");
        if(in_array($id,$zxs)){
            $data=$this->charts_model->get_city_map_zxs_time_data($this->mchId,$id,$param);
        }else{
            $data=$this->charts_model->get_city_map_pro_time_data($this->mchId,$id,$param);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 获取地区地图数据（右边-二级）
     */
    public function get_area_map_data(){
    	$id=$this->input->post('id');
    	$param=$this->input->post('param');
    	$zxs = array("北京市", "上海市", "天津市", "重庆市");
    	if(in_array($id,$zxs)){
    		$data=$this->charts_model->get_area_map_pro_data($this->mchId,$id,1,$param);
    	}else{
    		$data=$this->charts_model->get_area_map_pro_data($this->mchId,$id,0,$param);
    	}
    	$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /***********************************区域分布-地域统计量*************************************/
    /**
     * 获取表格省份数据（左边）
     */
    public function get_table_pro_data(){
        $param=$this->input->post('param');
        $data=$this->charts_model->get_table_pro_data_soure($this->mchId,$param);
        $this->output->set_content_type('application/json')->set_output(json_encode(['data'=>$data]));
    }
    /**
     * 获取表格城市数据（右边）
     */
    public function get_table_city_data(){
        $city=$this->input->post('city');
        $param=$this->input->post('param');
        $zxs = array("北京市", "上海市", "天津市", "重庆市");
        if(in_array($city,$zxs)){
            $data=$this->charts_model->get_table_city_data_zxs($this->mchId,$city,$param);
        }else{
            $data=$this->charts_model->get_table_city_data_pro($this->mchId,$city,$param);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(['data'=>$data]));
    }
    /**
     * 下载地域统计量数据（精确到省-市-区）
     */
    public function down_area_date(){
        $param=array(
            'year'=>$this->input->post('year'),
            'month'=>$this->input->post('month'),
            'week'=>$this->input->post('week'),
            'day'=>$this->input->post('day'),
            'productid'=>$this->input->post('productid'),
            'batchid'=>$this->input->post('batchid'),
        );
        $level=$this->input->post('level');
        $weektime=$this->input->post('weektime');
        $daily_data=$this->input->post('is_daily');
        if(isset($level)){
            $param['level']=$this->input->post('level');
        }
        if(isset($weektime)){
            $param['weektime']=$this->input->post('weektime');
        }
        if(isset($daily_data)){
            $param['is_daily']=$this->input->post('is_daily');
        }
        $title=get_down_title($param);
        //判断是否下载月份每天数据
        if(isset($daily_data)&&$daily_data==1){
            $data=$this->charts_model->down_area_daily_data($this->mchId,$param);
            foreach($data as $key=>$val){
                unset($data[$key]['proCode']);
                unset($data[$key]['cityCode']);
                unset($data[$key]['areaCode']);
            }
            $time=get_time_screening($param);
            $date_string='';
            $begintime = strtotime($time['start']);$endtime = strtotime($time['end']);
            for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
                $date_string= $date_string.",".date("Y-m-d", $start);
            }
            //拿到x轴日期数据 $date_arr
            $data_arr=explode(",",ltrim($date_string,","));
            $subtitle="省份,城市,区域,";
            for($t=0;$t<count($data_arr);$t++){
                $subtitle.=$data_arr[$t].",";
            }
            $subtitle.="合计";
            $output=iconv("UTF-8","gbk",rtrim($subtitle,','));
        }else{
            $data=$this->charts_model->down_area_data_model($this->mchId,$param);
            foreach($data as $key=>$val){
                unset($data[$key]['proCode']);
                unset($data[$key]['cityCode']);
                unset($data[$key]['areaCode']);
            }
            $output=iconv("UTF-8","gbk",'省份,城市,区域,扫码次数,红包金额（元）,积分');//使用GBK支持的字符范围更大
        } 
        $output.="\r\n";
        foreach ($data as $row) {
            $str_arr = array();
            foreach ($row as $column) {
                $str_arr[] = '"' . str_replace('"', '""', iconv("UTF-8","gbk",$column)) . '"';
            }
            $output.=implode(',', $str_arr) . PHP_EOL;
        }
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename='.iconv("UTF-8","gbk",'地域统计量_'.$title['time']).'.csv')
        ->set_output($output);
    }
    /***************************************业务分析*****************************************/
    /**
     * 业务分析首页
     */
    public function business(){
        $this->load->view('charts_business');
    }
    /**
     * 业务分析数据）
     */
    public function get_business_data(){
    	$param=$this->input->post('param');
        $data=$this->charts_model->get_business_data($this->mchId,$param);
        //处理数据为指定格式
        if($param['month']==0){//年份数据
            $data=business_year_arr($param['year'],$data);
        }
        if($param['week']==0&&!$param['month']==0){//月份数据1-31天
            if(isset($param['level'])&&$param['level']=='week'){
                $data=business_weekly_arr($param['year'],$param['month'],$data);
            }else{
                $data=business_monthly_arr($param['year'],$param['month'],$data);
            } 
        }
        if(!$param['week']==0){//具体周的数据
            //处理时间
            $timearr=explode("_",$param['weektime']);
            $data=business_daily_arr($timearr[0],$timearr[1],$data);
            
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 业务分析下载
     */
    public function down_business_date(){
    	$param=json_decode($this->input->get_post('param'), true);
        $data=json_decode($this->input->get_post('data'), true);
        $title=get_down_title($param);
        $output=iconv("UTF-8","gbk",$title['title'].',扫码次数,红包金额（元）,提现金额（元）,乐券（张）');
        $output.="\r\n";
        for($i=0;$i<count($data);$i++){
            $output.="\t".$data[$i]['theDate'].','.$data[$i]['scanNum'].','.$data[$i]['red_amount'].','.$data[$i]['trans_amount'].','.$data[$i]['card_num']."\r\n";
        }
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename='.iconv("UTF-8","gbk",'业务统计量_'.$title['time']).'.csv')
        ->set_output($output);
    }
    /***************************************新老用户分析*****************************************/
    public function useranalysis(){
        $this->load->view('charts_useranalysis');
    }
    /**
     * 获取新老用户扫码分析数据
     */
    public function get_useranalysis_data(){
    	$param=$this->input->post('param');
        $data=$this->charts_model->get_useranalysis_data($this->mchId,$param);
        $time=get_time_screening($param);
    	$data=useranalysis_data_arr($time['start'],$time['end'],$data);
    	$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 新老用户扫码分析数据下载
     */
    public function down_useranalysis_date(){
        $param=json_decode($this->input->get_post('param'), true);
        $data=json_decode($this->input->get_post('data'), true);
        $title=get_down_title($param);
    	$output=iconv("UTF-8","gbk",'日期,新用户扫码次数,老用户扫码次数');
    	$output.="\r\n";
    	for($i=0;$i<count($data);$i++){
    		$output.=$data[$i]['theDate'].','.$data[$i]['newScan'].','.$data[$i]['oldScan']."\r\n";
    	}
    	$this->output->set_content_type('application/octet-stream')
    	->set_header('Content-Disposition:attachment;filename='.iconv("UTF-8","gbk",'新老用户报表_'.$title['time']).'.csv')
    	->set_output($output);
    }
    /***************************************热力图*****************************************/
    public function scan(){
        $data['data']=$this->ranking_model->areas();
        $this->load->view('charts_scan',["data"=>$data]);
    }
    /**
     * 取得区域数据
     */
    public function get_scan_area_data(){
        $north=intval($this->input->post('north'));
        $south=intval($this->input->post('south'));
        $west=intval($this->input->post('west'));
        $east=intval($this->input->post('east'));
        $level=intval($this->input->post('level'));
        $westLng=doubleval($this->input->post('westlng'));
        $eastLng=doubleval($this->input->post('eastlng'));
        $times=intval($this->input->post('times'));
        //$start=$this->input->post('start');
        //$end=$this->input->post('end');
        
        $batchId=intval($this->input->post('batchid'));
        $productId=intval($this->input->post('productid'));
        $year=$this->input->post('year');
        $month=$this->input->post('month');
        $week=$this->input->post('week');
        $day=$this->input->post('day');
        $pro=$this->input->post('pro');
        $city=$this->input->post('city');

        $data=$this->charts_model->get_scan_area_data($this->mchId,$north,$south,$west,$east,$level,$westLng,$eastLng,$times,
            $batchId,$productId,$year,$month,$week,$day,$pro,$city);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /***************************************用户排行*****************************************/
    public function userrank(){
    	$data['data']=$this->ranking_model->areas();
    	$this->load->view('charts_userrank',["data"=>$data]);
    }
    //获取省份对应的城市
    public function get_city_list(){
    	$proCode=$this->input->post('proCode');
    	$data=$this->ranking_model->get_city_list($proCode);
    	$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //获取市下面对应的区县
    public function get_dist_list(){
        $cityCode = $this->input->post('cityCode');
        $data = $this->ranking_model->get_dist_list($cityCode);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //搜索结果返回
    public function get_table_data(){
    	$param=$this->input->get_post('param');
    	$start=$this->input->post('start');
    	$length=$this->input->post('length');
    	$draw=$this->input->post('draw');
        // 这里定义排序的
        $order=$this->input->get_post('order');
    	$data=$this->ranking_model->get_table_data($this->mchId,$param,$count,$start,$length,$order);
    	$data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
	public function down_userrank_date(){
		$param=array(
            'year'=>$this->input->post('year'),
            'month'=>$this->input->post('month'),
            'week'=>$this->input->post('week'),
            'productid'=>$this->input->post('productid'),
            'batchid'=>$this->input->post('batchid'),
            'pro'=>$this->input->post('pro'),
            'city'=>$this->input->post('city')
        );
        $level=$this->input->post('level');
        $weektime=$this->input->post('weektime');
        if(isset($level)){
            $param['level']=$this->input->post('level');
        }
        if(isset($weektime)){
            $param['weektime']=$this->input->post('weektime');
        }
        
        $title=get_down_title($param);

    	$data=$this->ranking_model->get_table_data($this->mchId,$param);
        $data=object_array($data);
    	$output=iconv("UTF-8","GBK",'用户排名,用户id,微信昵称,扫码次数,提现金额（元）,积分');//使用GBK支持的字符范围更大

        $output.="\r\n";
        foreach ($data as $row) {
            $str_arr = array();
            foreach ($row as $column) {
                $str_arr[] = '"' . str_replace('"', '""', iconv("UTF-8","GBK//IGNORE",$column)) . '"';
            }
            $output.=implode(',', $str_arr) . PHP_EOL;
        }
    	$this->output->set_content_type('application/octet-stream')
    	->set_header('Content-Disposition:attachment;filename=用户排行榜_'.$title['time'].'.csv')
    	->set_output($output);
    }
    /***************************************区域趋势分析*****************************************/
    public function trend(){
        $data['data']=$this->ranking_model->areas();
        $data = ['data' => $data];

        // ----------------------------------------------
        // Added by shizq - begin
        /*
        $chartId = 2;
        $status = $this->charts_model->getMchChartStatus($this->mchId, $chartId);
        $data = array_merge($data, $status);*/
        // Added by shizq - end
        // ----------------------------------------------
        
        $this->load->view('charts_trend', $data);
    }
    public function get_trend_data(){
        // ----------------------------------------------
        // Added by shizq - begin
        /**
        $chartId = 2;
        $status = $this->charts_model->getMchChartStatus($this->mchId, $chartId);
        
        if ($status['need_buy'] == 1) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errcode' => $status['need_buy'], 'errmsg' => '报表试用已到期', 'data' => []]));
            return;
        } else if ($status['need_buy'] == 2) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errcode' => $status['need_buy'], 'errmsg' => '报表使用已到期', 'data' => []]));
            return;
        }
        */
        // Added by shizq - end
        // ----------------------------------------------

        $param=$this->input->post('param');
        $time=get_time_screening($param);
        $data=$this->charts_model->get_trend_data($this->mchId,$param);
        $data=trend_data_arr($time['start'],$time['end'],$data,$param['mycity'],$param['productName'],$param['batchName']);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /***************************************活动策略效果评估报表*****************************************/
    public function policy(){
    	$data['activity'] = $this->activity_data();
    	$data['category'] = $this->catedata();
        $data = ['data' => $data, 'mch_id' => $this->mchId];
        $this->load->view('charts_policy', $data);
    }
    //获取分类下的产品
    public function get_policy_product(){
    	$categoryId=$this->input->post('categoryId');
        if(isset($categoryId)){
            $data=$this->charts_model->get_product($this->mchId,$categoryId);
        }else{
            $data=$this->charts_model->get_products($this->mchId);
        }
    	$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //显示
    public function get_policy(){
        $id=$this->input->post('id');
        //查询出该策略详情
        $data=$this->charts_model->get_policy($id);
        $data['Json']=json_decode($data['Json'],true);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //查询活动评估
    public function get_policy_data(){
        // ----------------------------------------------
        // Added by shizq - begin
        /**
        $chartId = 1;
        $status = $this->charts_model->getMchChartStatus($this->mchId, $chartId);
        
        if ($status['need_buy'] == 1) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errcode' => $status['need_buy'], 'errmsg' => '报表试用已到期', 'data' => []]));
            return;
        } else if ($status['need_buy'] == 2) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errcode' => $status['need_buy'], 'errmsg' => '报表使用已到期', 'data' => []]));
            return;
        }*/
        // Added by shizq - end
        // ----------------------------------------------
        
        // error_reporting(0);
        $param=$this->input->post('param');
        $time=get_time_screening($param);
        $start=$time['start'];
        $end=$time['end'];
        //查询出策略列表
        $rule=$this->charts_model->get_policy_rule_data($this->mchId,$param);
        $data=$this->charts_model->get_policy_data($this->mchId,$param);
        $result=get_policy_data_arr($rule,$data,$start,$end);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    private function activity_data() {
    	$allData=[];
    	$fData=$this->activity_model->get_by_mchid($this->mchId);
    	$sData=$this->activity_model->get_sub_by_mchid($this->mchId);
    	foreach($fData as $k=>$v){
    		array_push($allData,$fData[$k]);
    		foreach($sData as $key=>$value){
    			if($value->parentId==$v->id){
    				array_push($allData,$sData[$key]);
    			}
    		}
    	}
    	foreach($allData as $k=>$v){
    		$allData[$k]->startTime=date('Y-m-d H:i:s',intval($v->startTime));
    		$allData[$k]->endTime=date('Y-m-d H:i:s',intval($v->endTime));
    	}
    	return $allData;
    }
    private function catedata() {
    	$categories=$this->product_model->get_category($this->mchId);
    	$categories=json_decode( json_encode($categories),true);
    	function getTree($arrCat, $parent_id = 0, $level = 0) {
    		static  $arrTree = array(); //使用static代替global
    		if(empty($arrCat)) return FALSE;
    		$level++;
    		foreach($arrCat as $key => $value)
    		{
    			if($value['parentCategoryId' ] == $parent_id)
    			{
    				$value[ 'level'] = $level;
    				$arrTree[] = $value;
    				unset($arrCat[$key]); //注销当前节点数据，减少已无用的遍历
    				getTree($arrCat,$value['id'], $level);
    			}
    		}
    		return $arrTree;
    	}
    	$catetree=getTree($categories,-1,0);
    	return $catetree;
    }

    //活动评估数据批量生成
    public function set_policy_data(){
        $theDate=$this->input->get('theDate');
        $ret = strtotime($theDate);
        if(isset($theDate)){
            if($ret !== FALSE && $ret != -1){
                if(strtotime($theDate)>strtotime(date ( 'Y-m-d' ))){
                    exit('日期不能大于今天！');
                }
                $datetime=$theDate;
            }else{
                exit('非法日期格式！');
            }
        }else{
            $datetime=date ( 'Y-m-d' );
        }
        
        error_reporting(0);
        if($this->mchId==0){
            //查询出每一个商户的mchid
            $sql="select id mchId from merchants where status=1";
            $sql_activity="select id from sub_activities where mchId=? and role=0 and state>=0";
            $mchids=$this->db->query($sql)->result_array();
            for($i=0;$i<count($mchids);$i++){
                $activity[$i]=$this->db->query($sql_activity,[$mchids[$i]['mchId']])->result_array();
                for($x=0;$x<count($activity[$i]);$x++){
                    try {
                        $activityLog ['mchId'] = $mchids[$i]['mchId'];
                        
                        $activityLog ['activityId'] = $activity[$i][$x]['id'];
                        $cate_pro = $this->activity_model->get_cate_product ($mchids[$i]['mchId'], $activity[$i][$x]['id']);

                        $activityLog ['categoryId'] = $cate_pro ['categoryId'] ? $cate_pro ['categoryId'] : 0;
                        $activityLog ['productId'] = $cate_pro ['productId'] ? $cate_pro ['productId'] : 0;
                        $activityLog ['batchId'] = $cate_pro ['batchId'] ? $cate_pro ['batchId'] : 0;
                        $policyInfo = $this->activity_model->get_activity_policy ($mchids[$i]['mchId'], $activity[$i][$x]['id']);
                        $activityLog ['Json'] = json_encode ( $policyInfo ['data'] );
                        $activityLog ['policyName'] = $policyInfo ['policyName'];
                        $activityLog ['policyLevel'] = $policyInfo ['policyLevel'];

                        $activityLog ['theTime'] = $datetime;

                        $result=$this->activity_model->get_activity_log($mchids[$i]['mchId'],$activity[$i][$x]['id'],$activityLog['theTime']);
                        if($result){
                            if($activityLog['policyName']!=''){//排除无内容的策略
                                $this->activity_model->update_activity_log($result->id,$activityLog);
                            }
                        }else{
                            if($activityLog['policyName']!=''){//排除无内容的策略
                                $this->activity_model->add_activity_log($activityLog);
                            }
                        }
                    } catch ( Exception $e ) {
                        log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
                    }
                }
            }
        }else{
            echo '木有权限';
        }
        echo 'ok';
        
    }

    /*************************临时为锐欧提供的数据下载方法 mchId=92************************/
    public function get_down_detail_scan(){
        error_reporting(0);
        if(!in_array($this->mchId, array('0','92'))){
            exit('参数错误或没有权限！');
        }
        $data=$this->charts_model->get_down_detail_scan($this->mchId);
        $data=object_array($data);
        $output=iconv("UTF-8","GBK",'用户ID,二维码Code,扫码日期,扫码时间,参与的活动,乐码批号,产品,扫码GPS坐标,扫码GPS对应的省份,扫码GPS对应的城市,微信OpenID,微信昵称,微信省份,微信城市,微信国家,手机号码,电子邮箱,QQ号码,生日,性别');//使用GBK支持的字符范围更大
        $output.="\r\n";
        foreach ($data as $row) {
            $str_arr = array();
            foreach ($row as $column) {
                $str_arr[] = '"' . str_replace('"', '""', iconv("UTF-8","GBK",$column)) . '"';
            }
            $output.=implode(',', $str_arr) . PHP_EOL;
        }
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename='.iconv("UTF-8","GBK",'活动原始数据').'.csv')
        ->set_output($output);

    }
    /*************************消费者画像************************/
    public function portrait(){
        $data['data']=$this->ranking_model->areas();
        $data = ['data' => $data];
        $this->load->view('charts_portrait',$data);
    }
    //获取消费者画像数据
    public function get_portrait_data(){
        $param=(array)$this->input->post();
        $param['mchId']=$this->mchId;
        $data=$this->charts_model->get_portrait_data($param,$count);
        //循环查询出地区
        for($i=0;$i<count($data);$i++){
            if($data[$i]['areaCode']==0){
                $data[$i]['city']='未知城市';
                $data[$i]['count']=$count;
                $data[$i]['per']=round(($data[$i]['num']/$count)*100,2);
            }else{
                $areainfo=$this->charts_model->getAreaName($data[$i]['areaCode']);
                if(!empty($areainfo)){
                    if($param['proCode']==0){
                        $data[$i]['city']=$areainfo->name;
                    }else{
                        $data[$i]['city']=$areainfo->name.$areainfo->areaName;
                    }
                }else{
                    $data[$i]['city']='未知城市';
                }
                
                
                $data[$i]['count']=$count;
                $data[$i]['per']=round(($data[$i]['num']/$count)*100,2);
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
//===============================================用户排行扫码详情

    public function show_userrank_scan(){
        $data=(array)$this->input->post();
        $this->load->view('charts_show_userrank_scan',['data'=>json_encode($data)]);
    }
    public function get_userrank_scan(){
        $param=(array)$this->input->post();
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->charts_model->get_userrank_scan($this->mchId,$param,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }


    public function get_blocked($code){
        // 先查询出相关的商户
        $url='http://m.lsa0.cn/code/decode?c='.$code;
        $ch = curl_init();  
        $timeout = 10; // set to zero for no timeout  
        curl_setopt ($ch, CURLOPT_URL,$url);  
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);   
        curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36');  
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
        $html = curl_exec($ch);
        var_dump($html);
        $html=' '.$html;
        $$html = explode(" ", $html);  
        var_dump($html[1]);
    }


    //为乌苏定制的报表 mchId=173
    public function wusu_report(){
        $this->load->view('charts_wusu_report');
    }
    //二维码瓶盖查询
    public function wusu_report_for_code(){
        //获取产品
        $data['productList']=$this->charts_model->get_products($this->mchId);
        //获得乐码
        $data['batchList']=$this->charts_model->get_products($this->mchId);
        $data['minDate'] = $this->charts_model->getWusuCodeChartsMinDate();
        $this->load->view('charts_wusu_report_for_code',$data);
    }
    //查询二维码瓶盖数据
    public function get_wusu_code_data(){
        $param=(array)$this->input->post();
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->charts_model->get_wusu_code_data($this->mchId,$param,$count,$start,$length);
        $data = (object)[
            "draw" => intval($draw),
            "recordsTotal" => $count,
            'recordsFiltered'=> $count,
            'data' => $data['data'],
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //根据活动id拉取总积分
    public function get_totalScore_by_activityId(){
        $activityId=$this->input->post('activityId');
        $data=$this->charts_model->get_totalScore_by_activityId($this->mchId,$activityId);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //获取扫码地区相关信息
    public function get_scanarea_info($charts = NULL){
        if (isset($charts)) {
            $dateTime = $this->input->get('date_time');
            $batchId = $this->input->get('batch_id');
            $dateTime = explode(',', $dateTime);
            $data = $this->charts_model->getAreaCodeList($batchId, $dateTime);
        } else {
            $param=$this->input->post();
            //拆分扫码区域 分别查询出各个扫码地区的扫码次数和积分数
            $arr=explode(',', $param['scanArea']);
            for($i=0;$i<count($arr);$i++){
                $data[$i]=$this->charts_model->get_scanarea_info($this->mchId,$param,$arr[$i]);
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    //积分核算表
    public function wusu_report_for_score(){
        //获取产品
        $data['productList']=$this->charts_model->get_products($this->mchId);
        //获得乐码
        $data['batchList']=$this->charts_model->get_products($this->mchId);
        $data['minDate'] = $this->charts_model->getWusuScoreChartsMinDate();
        $this->load->view('charts_wusu_report_for_score',$data);
    }
    //查询积分核算数据
    public function get_wusu_score_data(){
        $param=(array)$this->input->get();
        $start=$this->input->get('start');
        $length=$this->input->get('length');
        $draw=$this->input->get('draw');
        $dataList=$this->charts_model->get_wusu_score_data($this->mchId,$param,$start,$length);
        $data = (object)[
            "draw" => intval($draw),
            "recordsTotal" => $dataList['total'],
            'recordsFiltered'=> $dataList['total'],
            'data' => $dataList['data'],
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 二维码瓶盖查询下载
     * @auther fengyanjun
     * @dateTime 2017-12-21 11:04
     */
    public function get_wusu_code_data_download(){
        //获取前台传过来的jsons数据并转换为数组
        $param=(array)$this->input->get('param');
        $temp = (array)json_decode($param[0]);
        //查询数据
        $data=$this->charts_model->get_wusu_code_data($this->mchId,$temp);
        //加载PHPExcel的类
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        //创建PHPExcel实例
        $excel = new PHPExcel();
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        //为单元格赋值
        $excel->getActiveSheet()->setCellValue('A1', '产品')
            ->setCellValue('B1', '活动名称')
            ->setCellValue('C1', '活动地点')
            ->setCellValue('D1', '策略类型')
            ->setCellValue('E1', '策略内容')
            ->setCellValue('F1', '乐码批次')
            ->setCellValue('G1', '瓶盖采购数')
            ->setCellValue('H1', '瓶盖激活数')
            ->setCellValue('I1', '总积分')
            ->setCellValue('J1', '已扫积分')
            ->setCellValue('K1', '已扫瓶盖数');
//            ->setCellValue('L1', '扫码地区');
        foreach ($data['data']  as $k=>$v){
            $excel->getActiveSheet()->setCellValue('A'.($k+2), $v->productName)
                ->setCellValue('B'.($k+2), $v->activityName)
                ->setCellValue('C'.($k+2), $v->areaName)
                ->setCellValue('D'.($k+2), $v->strategyLevel)
                ->setCellValue('E'.($k+2), $v->strategyName)
                ->setCellValue('F'.($k+2), $v->batchNo)
                ->setCellValue('G'.($k+2), $v->codeCount)
                ->setCellValue('H'.($k+2), $v->capsCount2)
                ->setCellValue('I'.($k+2), $v->pointsCount2)
                ->setCellValue('J'.($k+2), $v->pointsNum)
                ->setCellValue('K'.($k+2), $v->scanNum);
//                ->setCellValue('L'.($k+2), $v->batchId);
            $excel->getActiveSheet()->getStyle('G'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('H'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('I'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('J'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('K'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        }
        //输出到浏览器
        $write = new PHPExcel_Writer_Excel2007($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="二维码瓶盖统计量_'.$temp['startTime'].'至'.$temp['endTime'].'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
    /**
     * 积分核对查询下载
     * @auther fengyanjun
     * @dateTime 2017-12-21 14:44
     */
    public function wusu_report_for_score_download(){
        //获取前台传过来的jsons数据并转换为数组
        $param=(array)$this->input->get('param');
        $temp = (array)json_decode($param[0]);
        //查询数据
        $data=$this->charts_model->get_wusu_score_data_download($this->mchId,$temp);
        //加载PHPExcel的类
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        //创建PHPExcel实例
        $excel = new PHPExcel();
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        //为单元格赋值
        $excel->getActiveSheet()->setCellValue('A1', '产品')
            ->setCellValue('B1', '策略')
            ->setCellValue('C1', '累计激活瓶数')
            ->setCellValue('D1', '累计激活积分')
            ->setCellValue('E1', '单瓶成本')
            ->setCellValue('F1', '已扫瓶数')
            ->setCellValue('G1', '已扫积分')
            ->setCellValue('H1', '扫码率')
            ->setCellValue('I1', '已扫单瓶成本');
        foreach ($data['data']  as $k=>$v){
            $excel->getActiveSheet()->setCellValue('A'.($k+2), $v->productName)
                ->setCellValue('B'.($k+2), $v->strategyName)
                ->setCellValue('C'.($k+2), $v->totalCaps2)
                ->setCellValue('D'.($k+2), $v->totalPoints2)
                ->setCellValue('E'.($k+2), sprintf("%.2f",(($v->totalPoints / $v->totalCaps)*0.01),2) .'元')
                ->setCellValue('F'.($k+2), $v->scanedCaps)
                ->setCellValue('G'.($k+2), $v->scanedPoints)
                ->setCellValue('H'.($k+2), sprintf("%.2f",(($v->scanedCaps / $v->totalCaps)*100),2).'% '."\n")
                ->setCellValue('I'.($k+2), $v->scanedCaps==='0'?'没有扫码记录':sprintf("%.2f",(($v->scanedPoints / $v->scanedCaps)*0.01),2) .'元');
            $excel->getActiveSheet()->getStyle('C'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('D'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('F'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            $excel->getActiveSheet()->getStyle('G'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        }
        //输出到浏览器
        $write = new PHPExcel_Writer_Excel2007($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="积分统计量_'.$temp['startTime'].'至'.$temp['endTime'].'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
}
