<?php
defined('BASEPATH') or exit('No direct script access allowed');

// 报表数据处理helper add by cw

/**
 * 处理用户扫码统计----年份数据
 * $data 需要处理的数据
 */
if (! function_exists('userscan_year_arr')) {
    //年份数据处理
    function userscan_year_arr($year,$data){
        $times=array($year.'-01',$year.'-02',$year.'-03',$year.'-04',$year.'-05',$year.'-06',$year.'-07',$year.'-08',$year.'-09',$year.'-10',$year.'-11',$year.'-12');
        for($t=0;$t<count($times);$t++){
            $time_arr[$t]=array(
                    'theDate'=>$times[$t],
                    'scanNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($time_arr,$data);
        $res = array();
        foreach($arrs as $item) {
            if(! isset($res[$item['theDate']])) $res[$item['theDate']] = $item;
            else {
                $res[$item['theDate']]['scanNum'] += $item['scanNum'];
            }
        }
        $result=array_values($res);

        for($i=0;$i<count($result);$i++){
            //时间-X轴
            $theDate[$i]=(string)$result[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$result[$i]['scanNum'];
        }
        $arr=array(
            'theDate'=>$theDate,
            'scanNum'=>$scanNum
        );
        return $arr;
    }
}

/**
 * 处理用户扫码统计----月份数据处理
 * $year 年份
 * $month 月份
 * $data 需要处理的数据
 */
if (! function_exists('userscan_monthly_arr')) {
    //月份数据处理_1-31天数据
    function userscan_monthly_arr($year,$month,$data){
        //循环出时间
        $date_string='';
        $begintime = $year.'-'.$month.'-01';
        $endtime = date('Y-m-d', strtotime("$begintime+1 month -1 day"));
        for ($start = strtotime($begintime); $start <= strtotime($endtime); $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'scanNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum
        );
        return $arr;
    }
}

/**
 * 处理用户扫码统计----月份数据处理
 * $year 年份
 * $month 月份
 * $data 需要处理的数据
 */
if (! function_exists('userscan_weekly_arr')) {
    //月份数据处理_每周数据
    function userscan_weekly_arr($year,$month,$data){
        $weekinfo=get_weekinfo($year.'-'.$month);
        for($i=0;$i<count($weekinfo);$i++){
          $weekinfo[$i]=$weekinfo[$i][2];  
        } 
        //生成结构一致的数组
        for($t=0;$t<count($weekinfo);$t++){
            $weekinfo[$t]=array(
                    'theDate'=>$weekinfo[$t],
                    'scanNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($weekinfo,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            $week= explode("-",$item[$i]['theDate']);

            //时间-X轴
            $theDate[$i]='第'.$week[1].'周';
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum
        );
        return $arr;
    }

}
/**
 * 处理用户扫码统计----月份数据处理
 * $start 开始日期
 * $end 结束日期
 * $data 需要处理的数据
 */
if (! function_exists('userscan_daily_arr')) {
    //周数据处理-每天
    function userscan_daily_arr($start,$end,$data){
        //循环出时间
        $date_string='';
        $begintime = strtotime($start);$endtime = strtotime($end);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'scanNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum
        );
        return $arr;
    }
}


//业务分析
if (! function_exists('business_year_arr')) {
    //年份数据处理
    function business_year_arr($year,$data){
        $times=array($year.'-01',$year.'-02',$year.'-03',$year.'-04',$year.'-05',$year.'-06',$year.'-07',$year.'-08',$year.'-09',$year.'-10',$year.'-11',$year.'-12');
        for($t=0;$t<count($times);$t++){
            $time_arr[$t]=array(
                    'theDate'=>$times[$t],
                    'scanNum'=>'0',
                    'red_amount'=>'0',
                    'trans_amount'=>'0',
                    'card_num'=>'0',
                    'point_amount'=>'0',
                    'point_num'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($time_arr,$data);
        $res = array();
        foreach($arrs as $item) {
            if(! isset($res[$item['theDate']])) $res[$item['theDate']] = $item;
            else {
                $res[$item['theDate']]['scanNum'] += $item['scanNum'];
                $res[$item['theDate']]['red_amount'] += $item['red_amount'];
                $res[$item['theDate']]['trans_amount'] += $item['trans_amount'];
                $res[$item['theDate']]['card_num'] += $item['card_num'];
                $res[$item['theDate']]['point_amount'] += $item['point_amount'];
                $res[$item['theDate']]['point_num'] += $item['point_num'];
            }
        }
        $result=array_values($res);

        for($i=0;$i<count($result);$i++){
            //时间-X轴
            $theDate[$i]=(string)$result[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$result[$i]['scanNum'];
            //红包-红包
            $redNum[$i]=(string)$result[$i]['red_amount'];
            //提现-提现
            $transNum[$i]=(string)$result[$i]['trans_amount'];
            //乐券-乐券
            $cardNum[$i]=(string)$result[$i]['card_num'];

            $pointAmount[$i]=(string)$result[$i]['point_amount'];

            $pointNum[$i]=(string)$result[$i]['point_num'];

        }
        $arr=array(
            'theDate'=>$theDate,
            'scanNum'=>$scanNum,
            'redNum'=>$redNum,
            'transNum'=>$transNum,
            'cardNum'=>$cardNum,
            'pointAmount'=>$pointAmount,
            'pointNum'=>$pointNum,
            'data'=>$result
        );
        return $arr;
    }
}

/**
 * 处理用户扫码统计----月份数据处理
 * $year 年份
 * $month 月份
 * $data 需要处理的数据
 */
if (! function_exists('business_monthly_arr')) {
    //月份数据处理_1-31天数据
    function business_monthly_arr($year,$month,$data){
        //循环出时间
        $date_string='';
        $begintime = $year.'-'.$month.'-01';
        $endtime = date('Y-m-d', strtotime("$begintime+1 month -1 day"));
        for ($start = strtotime($begintime); $start <= strtotime($endtime); $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'scanNum'=>'0',
                    'red_amount'=>'0',
                    'trans_amount'=>'0',
                    'card_num'=>'0',
                    'point_amount'=>'0',
                    'point_num'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
                $item[$v['theDate']]['red_amount']+=$v['red_amount'];
                $item[$v['theDate']]['trans_amount']+=$v['trans_amount'];
                $item[$v['theDate']]['card_num']+=$v['card_num'];
                $item[$v['theDate']]['point_amount']+=$v['point_amount'];
                $item[$v['theDate']]['point_num']+=$v['point_num'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
            //红包-红包
            $redNum[$i]=(string)$item[$i]['red_amount'];
            //提现-提现
            $transNum[$i]=(string)$item[$i]['trans_amount'];
            //乐券-乐券
            $cardNum[$i]=(string)$item[$i]['card_num'];

            $pointAmount[$i]=(string)$item[$i]['point_amount'];

            $pointNum[$i]=(string)$item[$i]['point_num'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum,
                'redNum'=>$redNum,
                'transNum'=>$transNum,
                'cardNum'=>$cardNum,
                'pointAmount'=>$pointAmount,
                'pointNum'=>$pointNum,
                'data'=>$item
        );
        return $arr;
    }
}

/**
 * 处理用户扫码统计----月份数据处理
 * $year 年份
 * $month 月份
 * $data 需要处理的数据
 */
if (! function_exists('business_weekly_arr')) {
    //月份数据处理_每周数据
    function business_weekly_arr($year,$month,$data){
        $weekinfo=get_weekinfo($year.'-'.$month);
        for($i=0;$i<count($weekinfo);$i++){
          $weekinfo[$i]=$weekinfo[$i][2];  
        } 
        //生成结构一致的数组
        for($t=0;$t<count($weekinfo);$t++){
            $weekinfo[$t]=array(
                    'theDate'=>$weekinfo[$t],
                    'scanNum'=>'0',
                    'red_amount'=>'0',
                    'trans_amount'=>'0',
                    'card_num'=>'0',
                    'point_amount'=>'0',
                    'point_num'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($weekinfo,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
                $item[$v['theDate']]['red_amount']+=$v['red_amount'];
                $item[$v['theDate']]['trans_amount']+=$v['trans_amount'];
                $item[$v['theDate']]['card_num']+=$v['card_num'];
                $item[$v['theDate']]['point_amount']+=$v['point_amount'];
                $item[$v['theDate']]['point_num']+=$v['point_num'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            $week= explode("-",$item[$i]['theDate']);

            //时间-X轴
            $theDate[$i]='第'.$week[1].'周';
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
            //红包-红包
            $redNum[$i]=(string)$item[$i]['red_amount'];
            //提现-提现
            $transNum[$i]=(string)$item[$i]['trans_amount'];
            //乐券-乐券
            $cardNum[$i]=(string)$item[$i]['card_num'];
            $pointAmount[$i]=(string)$item[$i]['point_amount'];
            $pointNum[$i]=(string)$item[$i]['point_num'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum,
                'redNum'=>$redNum,
                'transNum'=>$transNum,
                'cardNum'=>$cardNum,
                'pointAmount'=>$pointAmount,
                'pointNum'=>$pointNum,
                'data'=>$item
        );
        return $arr;
    }

}
/**
 * 处理用户扫码统计----月份数据处理
 * $start 开始日期
 * $end 结束日期
 * $data 需要处理的数据
 */
if (! function_exists('business_daily_arr')) {
    //周数据处理-每天
    function business_daily_arr($start,$end,$data){
        //循环出时间
        $date_string='';
        $begintime = strtotime($start);$endtime = strtotime($end);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'scanNum'=>'0',
                    'red_amount'=>'0',
                    'trans_amount'=>'0',
                    'card_num'=>'0',
                    'point_amount'=>'0',
                    'point_num'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
                $item[$v['theDate']]['red_amount']+=$v['red_amount'];
                $item[$v['theDate']]['trans_amount']+=$v['trans_amount'];
                $item[$v['theDate']]['card_num']+=$v['card_num'];
                $item[$v['theDate']]['point_amount']+=$v['point_amount'];
                $item[$v['theDate']]['point_num']+=$v['point_num'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
            //红包-红包
            $redNum[$i]=(string)$item[$i]['red_amount'];
            //提现-提现
            $transNum[$i]=(string)$item[$i]['trans_amount'];
            //乐券-乐券
            $cardNum[$i]=(string)$item[$i]['card_num'];
            $pointAmount[$i]=(string)$item[$i]['point_amount'];
            $pointNum[$i]=(string)$item[$i]['point_num'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum,
                'redNum'=>$redNum,
                'transNum'=>$transNum,
                'cardNum'=>$cardNum,
                'pointAmount'=>$pointAmount,
                'pointNum'=>$pointNum,
                'data'=>$item
        );
        return $arr;
    }
}



/**
 * 新老用户扫码分析数据处理
 * @param 时间节点-week_本周-month_本月-rili_自定义时间段 $status
 * @param 参数 $param
 * @param 需处理的数据 $data
 */
if (! function_exists('useranalysis_data_arr')) {
    function useranalysis_data_arr($start,$end,$data){
        //循环出时间
        $date_string='';
        $begintime = strtotime($start);$endtime = strtotime($end);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'newScan'=>'0',
                    'oldScan'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['newScan']+=$v['newScan'];
                $item[$v['theDate']]['oldScan']+=$v['oldScan'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $newScan[$i]=(string)$item[$i]['newScan'];
            //红包-红包
            $oldScan[$i]=(string)$item[$i]['oldScan'];
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'newScan'=>$newScan,
                'oldScan'=>$oldScan,
                'data'=>$item
        );
        return $arr;
    }
}
/**
 * 区域趋势分析数据处理
 * @param 时间节点-week_本周-month_本月-rili_自定义时间段 $status
 * @param 参数 $param
 * @param 需处理的数据 $data
 */
if (! function_exists('trend_data_arr')) {
    function trend_data_arr($start,$end,$data,$mycity,$productName,$batchName){
        //循环出时间
        $date_string='';
        $begintime = strtotime($start);$endtime = strtotime($end);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'scanNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$data);
        //数组处理
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=$v['scanNum'];
            }
        }
        $item=array_values($item);

        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
        }
        //求和计算
        $sum = 0;
        foreach($scanNum as $key => $item){
            $sum += $item;
        }
        //数据长度
        $length=count($theDate);
        //基准值
        $aver=$sum/$length;
        //计算基准的数据
        if($aver==0){
            $aver_scanNum=$scanNum;
        }else{
            for($i=0;$i<count($scanNum);$i++){
                //扫码量-扫码量
                $aver_scanNum[$i]=(string)round($scanNum[$i]/$aver,2);
            }
        }
        //组装数组
        $arr=array(
                'theDate'=>$theDate,
                'cityName'=>$mycity,
                'scanNum'=>(object)array(
                    'name'=>'产品：'.$productName.'  乐码批次：'.$batchName.'  区域：'.$mycity,
                    'type'=>'line',
                    'data'=>$aver_scanNum,
                    'truedata'=>$scanNum
                )
        );
        return $arr;
    }
}
/**
 * 扫码统计数据处理
 * @param 需要处理的数据 $data
 * @return 返回json[]
 */
if (! function_exists('period_data_creat')) {
    function period_data_creat($data){
        $times=array('00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00',
                '09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00',
                '18:00','19:00','20:00','21:00','22:00','23:00');
        for($t=0;$t<count($times);$t++){
            $time_arr[$t]=array(
                    'time'=>$times[$t],
                    'scanNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($time_arr,$data);
        $res = array();
        foreach($arrs as $item) {
            if(! isset($res[$item['time']])) $res[$item['time']] = $item;
            else {
                $res[$item['time']]['scanNum'] += $item['scanNum'];
            }
        }
        $result=array_values($res);
        //拼接字符串返回给view
        $string='';
        for($i=0;$i<count($result);$i++){
            $string=$string.$result[$i]['scanNum'].',';
        }
        $string=substr($string,0,strlen($string)-1);

        $arr=array(
            'data'=>$result,
            'linedata'=>$string
        );
        return $arr;
    }
}
//获取月份有几周
if (! function_exists('get_weekinfo')) {
    function get_weekinfo($month){
        $weekinfo = array();
        $end_date = date('d',strtotime($month.' +1 month -1 day'));
        for ($i=1; $i <$end_date ; $i=$i+7) { 
            $w = date('N',strtotime($month.'-'.$i));
            
            $weekinfo[] = array(date('m.d',strtotime($month.'-'.$i.' -'.($w-1).' days')),date('m.d',strtotime($month.'-'.$i.' +'.(7-$w).' days')),date('Y-W',strtotime($month.'-'.$i.' -'.($w-1).' days')),date('Y-m-d',strtotime($month.'-'.$i.' -'.($w-1).' days')),date('Y-m-d',strtotime($month.'-'.$i.' +'.(7-$w).' days')));
        }
        //print_r($weekinfo);
        return $weekinfo;
    }
}
//处理参数返回开始结束时间
if (! function_exists('get_time_screening')) {
    function get_time_screening($param){
        if($param['month']==0){//年份数据
          $start=$param['year'].'-01-01';
          $end=$param['year'].'-12-31';
        }
        if($param['week']==0&&!$param['month']==0){//月份数据1-31天
          $start=$param['year'].'-'.$param['month'].'-01';
          $end=date('Y-m-d', strtotime(date('Y-m-01', strtotime($start)) . ' +1 month -1 day'));
        }
        if(!$param['week']==0){//具体周的数据
          $timearr=explode("_",$param['weektime']);
          //处理时间
          $start=$timearr[0];
          $end=$timearr[1];
        }
        //出具体每日的数据
        if(isset($param['day'])&&!$param['day']==0){
            $start=$param['day'];
            $end=$param['day'];
        }
        return ['start'=>$start,'end'=>$end];
    }
}
//下载excel的title处理
if (! function_exists('get_down_title')) {
    function get_down_title($param){
        if($param['month']==0){//年份数据
            $dTitle="月份";
            $dTime=$param['year'].'年';
        }
        if($param['week']==0&&!$param['month']==0){//月份数据1-31天
            if(isset($param['level'])&&$param['level']=='week'){
                $dTitle="周";
                $dTime=$param['year'].'-'.$param['month'].'(周数据)';
            }else{
                $dTitle="日期";
                if(isset($param['is_daily'])&&$param['is_daily']==1){
                    $dTime=$param['year'].'-'.$param['month'].'(日数据)';
                }else{
                    $dTime=$param['year'].'-'.$param['month'];
                }
            } 
        }
        if(!$param['week']==0){//具体周的数据
            $dTitle="日期";
            $dTime=$param['year'].'第'.$param['week'].'周数据';
        }
        if(isset($param['day'])&&!$param['day']==0){
            $dTitle="日期";
            $dTime=$param['day'].'数据';
        }
        return ['title'=>$dTitle,'time'=>$dTime];
    }
}

if (! function_exists('get_week')) {
    function get_week($year) { 
        $year_start = $year . "-01-01"; 
        $year_end = $year . "-12-31"; 
        $weekinfo = array();
        $startday = strtotime($year_start); 
        if (intval(date('N', $startday)) != '1') { 
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期 
        } 
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期 
     
        $endday = strtotime($year_end); 
        if (intval(date('W', $endday)) == '7') { 
            $endday = strtotime("last sunday", strtotime($year_end)); 
        } 
     
        $num = intval(date('W', $endday)); 
        for ($i = 1; $i <= $num; $i++) { 
            $j = $i -1; 
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week ")); 
     
            $end_day = date("Y-m-d", strtotime("$start_date +6 day")); 
            $weekinfo[] = array(date('m.d',strtotime($start_date)),date('m.d',strtotime($end_day)),date('W',strtotime("$year_mondy $j week ")));
        } 
        return $weekinfo; 
    } 
}
//活动评估报表处理
if(!function_exists('get_policy_data_arr')){
    function get_policy_data_arr($rule,$data,$start,$end){
        //循环下数组
        $arr=$data['policy'];
        $arrss=[];
        for($i=0;$i<count($arr);$i++){
            $arrss[$i]=array(
                    'level'=>$arr[$i]['level'],
                    'start'=>$arr[$i]['theTime'],
                    'end'=>$i+1>=count($arr) ? $arr[$i]['theTime'] : $arr[$i+1]['theTime']>=$end ? $end :$arr[$i+1]['theTime']
                );
        }
        $datas=$data['data'];
        //循环出时间
        $date_string='';
        $begintime = strtotime($start);$endtime = strtotime($end);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        //生成结构一致的数组
        for($t=0;$t<count($data_arr);$t++){
            $data_arr[$t]=array(
                    'theDate'=>$data_arr[$t],
                    'scanNum'=>'0',
                    'redNum'=>'0'
            );
        }
        //合并数组
        $arrs=array_merge($data_arr,$datas);
        $item=array();
        foreach($arrs as $k=>$v){
            if(!isset($item[$v['theDate']])){
                $item[$v['theDate']]=$v;
            }else{
                $item[$v['theDate']]['scanNum']+=@$v['scanNum'];
                $item[$v['theDate']]['redNum']+=@$v['redNum'];
            }
        }
        $item=array_values($item);
        //定义各个值的数组
        for($i=0;$i<count($item);$i++){
            //时间-X轴
            $theDate[$i]=(string)$item[$i]['theDate'];
            //扫码量-扫码量
            $scanNum[$i]=(string)$item[$i]['scanNum'];
            $redNum[$i]=(string)$item[$i]['redNum'];
        }
        //组装数组
        $result=array(
                'theDate'=>$theDate,
                'scanNum'=>$scanNum,
                'redNum'=>$redNum,
                'rules'=>$rule,
                'data'=>$item,
                'policy'=>$arrss
        );
        return $result;
    }
}

if(!function_exists('weekday')){
    function weekday($year,$week=1){
        $year_start = mktime(0,0,0,1,1,$year);   
        $year_end = mktime(0,0,0,12,31,$year);   
           
        // 判断第一天是否为第一周的开始   
        if (intval(date('W',$year_start))===1){   
            $start = $year_start;//把第一天做为第一周的开始   
        }else{   
            //$week++;   
            $start = strtotime('+1 monday',$year_start);//把第一个周一作为开始   
        }   
           
        // 第几周的开始时间   
        if ($week===1){   
            $weekday['start'] = $start;   
        }else{   
            $weekday['start'] = strtotime('+'.($week-1).' week',$start);   
        }   
           
        // 第几周的结束时间   
        $weekday['end'] = strtotime('+1 week',$weekday['start'])-1;   
        // if (date('Y',$weekday['end'])!=$year){   
        //     $weekday['end'] = $year_end;   
        // }   
        return $weekday;   
    }
}



   

    
