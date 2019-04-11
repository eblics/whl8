<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Charts_model extends CI_Model {

	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
    //获取商户的乐码批次
    function get_batchs($mchId,$productid){
      $sql="select * from batchs where mchId=? and rowStatus=0 and state>0 ";
      $data=[$mchId];
      if(!$productid==0){
        $sql.='and productId=? ';
        $data[]=$productid;
      }
      $sql.="order by createTime desc;";
      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve_array($sql);
   } 
   //获取产品
   function get_products($mchId){
        return $this->db->where('mchId',$mchId)->get('products')->result();
   }
    /**************************************用户扫码开始(业务分析同源)*************************************************/
    public function get_userscan_data($mchId,$param){
        //选择年-查1-12个月的 对应monthly
        if($param['month']==0){
            $sql="select theDate,sum(scanCount) scanNum from rpt_user_monthly where mchId=? and theDate>=? and theDate<=? ";
            $data=[$mchId,$param['year'].'-01',$param['year'].'-12'];

            if(!$param['pro']==0){
              $sql.='and proCode=? ';
              $data[]=$param['pro'];
            }
            if(!$param['city']==0){
              $sql.='and cityCode=? ';
              $data[]=$param['city'];
            }
            if(!$param['area']==0){
              $sql.='and areaCode=? ';
              $data[]=$param['area'];
            }

            if(!$param['productid']==0){
              $sql.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql.="group by theDate;";
            $sql=$this->db->compile_binds($sql,$data);
            return $this->dbhelper->serve_array($sql);
        }
        //选择年、月-查1-31天的/当月的周 对应daily/weekly
        if($param['week']==0&&!$param['month']==0){
            if(isset($param['level'])&&$param['level']=='week'){
                $weekinfo=get_weekinfo($param['year'].'-'.$param['month']);
                for($i=0;$i<count($weekinfo);$i++){
                  $weekinfo[$i]=$weekinfo[$i][2];  
                } 
                //查询每周的数据
                $sql="select theDate,sum(scanCount) scanNum from rpt_user_weekly where mchId=? and theDate>=? and theDate<=? ";
                $data=[$mchId,reset($weekinfo),end($weekinfo)];
                if(!$param['pro']==0){
                  $sql.='and proCode=? ';
                  $data[]=$param['pro'];
                }
                if(!$param['city']==0){
                  $sql.='and cityCode=? ';
                  $data[]=$param['city'];
                }
                if(!$param['area']==0){
                  $sql.='and areaCode=? ';
                  $data[]=$param['area'];
                }
                if(!$param['productid']==0){
                  $sql.='and productId=? ';
                  $data[]=$param['productid'];
                }
                if(!$param['batchid']==0){
                  $sql.='and batchId=? ';
                  $data[]=$param['batchid'];
                }
                $sql.="group by theDate;";
                $sql=$this->db->compile_binds($sql,$data);
                return $this->dbhelper->serve_array($sql);

            }else{
                  $start=$param['year'].'-'.$param['month'].'-01';
                  $end=date('Y-m-d', strtotime("$start +1 month -1 day"));
                  $sql="select theDate,sum(scanCount) scanNum from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
                  $data=[$mchId,$start,$end];
                  if(!$param['pro']==0){
                    $sql.='and proCode=? ';
                    $data[]=$param['pro'];
                  }
                  if(!$param['city']==0){
                    $sql.='and cityCode=? ';
                    $data[]=$param['city'];
                  }
                  if(!$param['area']==0){
                    $sql.='and areaCode=? ';
                    $data[]=$param['area'];
                  }
                  if(!$param['productid']==0){
                    $sql.='and productId=? ';
                    $data[]=$param['productid'];
                  }
                  if(!$param['batchid']==0){
                    $sql.='and batchId=? ';
                    $data[]=$param['batchid'];
                  }
                  $sql.="group by theDate;";
                  $sql=$this->db->compile_binds($sql,$data);
                  return $this->dbhelper->serve_array($sql);
            } 
        }
        //选择年-月-周 显示该周的天数
        if(!$param['week']==0){
            //处理时间
            $timearr=explode("_",$param['weektime']);
            $start=$timearr[0];
            $end=$timearr[1];

            $sql="select theDate,sum(scanCount) scanNum from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
            $data=[$mchId,$start,$end];
            if(!$param['pro']==0){
              $sql.='and proCode=? ';
              $data[]=$param['pro'];
            }
            if(!$param['city']==0){
              $sql.='and cityCode=? ';
              $data[]=$param['city'];
            }
            if(!$param['area']==0){
              $sql.='and areaCode=? ';
              $data[]=$param['area'];
            }
            if(!$param['productid']==0){
              $sql.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql.="group by theDate;";
            $sql=$this->db->compile_binds($sql,$data);
            return $this->dbhelper->serve_array($sql);
        }
        
    }
    //用户扫码统计表格数据
    public function get_user_scan_daylist($mchId,$param,&$count=0,$start=null,$length=null) {
         //选择年-查1-12个月的 对应monthly
        if($param['month']==0){
            $sql_cnt="select count(id) cnt from (select id from rpt_user_monthly where mchId=? and theDate>=? and theDate<=? ";
            $sql="select m.userId,ifnull(nickName,'红码用户') nickName,m.date,m.scanNum,m.red_amount,m.point_amount,m.trans_amount,m.card_num,m.pointUsed,m.level from ((select theDate date,userId,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(pointAmount),0) point_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointUsed) pointUsed,'month' level from rpt_user_monthly where mchId=? and theDate>=? and theDate<=? ";

            $data=[$mchId,$param['year'].'-01',$param['year'].'-12'];

            if(!$param['pro']==0){
              $sql_cnt.='and proCode=? ';
              $sql.='and proCode=? ';
              $data[]=$param['pro'];
            }
            if(!$param['city']==0){
              $sql_cnt.='and cityCode=? ';
              $sql.='and cityCode=? ';
              $data[]=$param['city'];
            }
            if(!$param['area']==0){
              $sql_cnt.='and areaCode=? ';
              $sql.='and areaCode=? ';
              $data[]=$param['area'];
            }

            if(!$param['productid']==0){
              $sql_cnt.='and productId=? ';
              $sql.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql_cnt.='and batchId=? ';
              $sql.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql_cnt.="group by theDate,userId)m";
            $count=$this->db->compile_binds($sql_cnt,$data);
            $count=$this->dbhelper->serverow($count)->cnt;

            $sql.="group by theDate,userId order by theDate desc,userId desc";
            if(isset($start)&&isset($length)){
              $sql.=' limit ?,?';
              $data[]=intval($start);
              $data[]=intval($length);
            }
            $sql.=')) m left join users on m.userId=users.id;';

            $sql=$this->db->compile_binds($sql,$data);
            // return $this->dbhelper->serve_array($sql);
            // $this->dbhelper->serve_array($sql);
            // return $this->db->last_query();
            return $sql;
        }
        //选择年、月-查1-31天的/当月的周 对应daily/weekly
        if($param['week']==0&&!$param['month']==0){
            //周数据
            if(isset($param['level'])&&$param['level']=='week'){
                $weekinfo=get_weekinfo($param['year'].'-'.$param['month']);
                for($i=0;$i<count($weekinfo);$i++){
                  $weekinfo[$i]=$weekinfo[$i][2];  
                } 

                $sql_cnt="select count(*) cnt from(select id from rpt_user_weekly where mchId=? and theDate>=? and theDate<=? ";
                //查询每周的数据
                $sql="select m.userId,ifnull(nickName,'红码用户') nickName,m.date,m.scanNum,m.red_amount,m.point_amount,m.trans_amount,m.card_num,m.pointUsed,m.level from (select theDate date,userId,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(pointAmount),0) point_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointUsed) pointUsed,'week' level from rpt_user_weekly where mchId=? and theDate>=? and theDate<=? ";
                $data=[$mchId,reset($weekinfo),end($weekinfo)];

                if(!$param['pro']==0){
                  $sql_cnt.='and proCode=? ';
                  $sql.='and proCode=? ';
                  $data[]=$param['pro'];
                }
                if(!$param['city']==0){
                  $sql_cnt.='and cityCode=? ';
                  $sql.='and cityCode=? ';
                  $data[]=$param['city'];
                }
                if(!$param['area']==0){
                  $sql_cnt.='and areaCode=? ';
                  $sql.='and areaCode=? ';
                  $data[]=$param['area'];
                }

                if(!$param['productid']==0){
                  $sql_cnt.='and productId=? ';
                  $sql.='and productId=? ';
                  $data[]=$param['productid'];
                }
                if(!$param['batchid']==0){
                  $sql_cnt.='and batchId=? ';
                  $sql.='and batchId=? ';
                  $data[]=$param['batchid'];
                }
                $sql_cnt.="group by theDate,userId) m ;";
                $count=$this->db->compile_binds($sql_cnt,$data);
                $count=$this->dbhelper->serverow($count)->cnt;

                $sql.="group by theDate,userId order by theDate desc,userId desc";
                if(isset($start)&&isset($length)){
                  $sql.=' limit ?,?';
                  $data[]=intval($start);
                  $data[]=intval($length);
                }
                $sql.=') m left join users on m.userId=users.id;';

                $sql=$this->db->compile_binds($sql,$data);
                // return $this->dbhelper->serve_array($sql);
                // $this->dbhelper->serve_array($sql);
                // return $this->db->last_query();
                return $sql;

            }else{
                  //1-31天数据
                  $time=get_time_screening($param);
                  $sql_cnt="select count(*) cnt from(select id from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
                  $sql="select m.userId,ifnull(nickName,'红码用户') nickName,m.date,m.scanNum,m.red_amount,m.point_amount,m.trans_amount,m.card_num,m.pointUsed,m.level from (select theDate date,userId,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(pointAmount),0) point_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointUsed) pointUsed,'day' level from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
                  
                  $data=[$mchId,$time['start'],$time['end']];


                  if(!$param['pro']==0){
                    $sql_cnt.='and proCode=? ';
                    $sql.='and proCode=? ';
                    $data[]=$param['pro'];
                  }
                  if(!$param['city']==0){
                    $sql_cnt.='and cityCode=? ';
                    $sql.='and cityCode=? ';
                    $data[]=$param['city'];
                  }
                  if(!$param['area']==0){
                    $sql_cnt.='and areaCode=? ';
                    $sql.='and areaCode=? ';
                    $data[]=$param['area'];
                  }

                  if(!$param['productid']==0){
                    $sql_cnt.='and productId=? ';
                    $sql.='and productId=? ';
                    $data[]=$param['productid'];
                  }
                  if(!$param['batchid']==0){
                    $sql_cnt.='and batchId=? ';
                    $sql.='and batchId=? ';
                    $data[]=$param['batchid'];
                  }
                  $sql_cnt.="group by theDate,userId) m ;";
                  $count=$this->db->compile_binds($sql_cnt,$data);
                  $count=$this->dbhelper->serverow($count)->cnt;

                  $sql.="group by theDate,userId order by theDate desc,userId desc";
                  if(isset($start)&&isset($length)){
                    $sql.=' limit ?,?';
                    $data[]=intval($start);
                    $data[]=intval($length);
                  }
                  $sql.=') m left join users on m.userId=users.id;';
                  $sql=$this->db->compile_binds($sql,$data);
                  // return $this->dbhelper->serve_array($sql);
                  // $this->dbhelper->serve_array($sql);
                  // return $this->db->last_query();
                  return $sql;
            } 
        }
        //选择年-月-周 显示该周的天数
        if(!$param['week']==0){
            //处理时间
            $timearr=explode("_",$param['weektime']);

            $sql_cnt="select count(*) cnt from(select id from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
            $sql="select m.userId,ifnull(nickName,'红码用户') nickName,m.date,m.scanNum,m.red_amount,m.point_amount,m.trans_amount,m.card_num,m.pointUsed,m.level from (select theDate date,userId,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(pointAmount),0) point_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointUsed) pointUsed,'day' level from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
            $data=[$mchId,$timearr[0],$timearr[1]];

            if(!$param['pro']==0){
              $sql_cnt.='and proCode=? ';
              $sql.='and proCode=? ';
              $data[]=$param['pro'];
            }
            if(!$param['city']==0){
              $sql_cnt.='and cityCode=? ';
              $sql.='and cityCode=? ';
              $data[]=$param['city'];
            }
            if(!$param['area']==0){
              $sql_cnt.='and areaCode=? ';
              $sql.='and areaCode=? ';
              $data[]=$param['area'];
            }

            if(!$param['productid']==0){
              $sql_cnt.='and productId=? ';
              $sql.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql_cnt.='and batchId=? ';
              $sql.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql_cnt.="group by theDate,userId) m;";
            $count=$this->db->compile_binds($sql_cnt,$data);
            $count=$this->dbhelper->serverow($count)->cnt;

            $sql.="group by theDate,userId order by theDate desc,userId desc";

            if(isset($start)&&isset($length)){
              $sql.=' limit ?,?';
              $data[]=intval($start);
              $data[]=intval($length);
            }

            $sql.=') m left join users on m.userId=users.id;';
            $sql=$this->db->compile_binds($sql,$data);
            // return $this->dbhelper->serve_array($sql);
            // $this->dbhelper->serve_array($sql);
            // return $this->db->last_query();
            return $sql;
        }
    }
    // 下载用户详细扫码数据

    public function get_user_scan_detaillist($mchId,$param){
        //选择年、月-查1-31天的/当月的周 对应daily/weekly
        if($param['week']==0&&!$param['month']==0){
            //周数据
            if(isset($param['level'])&&$param['level']=='day'){
                //1-31天数据
                  $time=get_time_screening($param);
                  $start=$time['start'].' 00:00:00';
                  $end=$time['end'].' 23:59:59';
            }
        }
        //选择年-月-周 显示该周的天数
        if(!$param['week']==0){
            //处理时间
            $time=explode("_",$param['weektime']);
            $start=$time[0].' 00:00:00';
            $end=$time[1].' 23:59:59';
        }
       $sql="select a.id scanId,a.userId,ifnull(d.nickName,'红码用户') nickName,FROM_UNIXTIME(a.scanTime) scanTime,ifnull(FROM_UNIXTIME(c.getTime),'') getTime,a.batchId,a.activityId,count(b.id) redCount,ifnull(GROUP_CONCAT(round(b.amount/100,2) SEPARATOR ' 、'),0) redList,IFNULL(count(c.id),0) cardCount,ifnull(GROUP_CONCAT(f.title SEPARATOR ' 、'),'') cardTitle,e.address from scan_log a 
                left join user_redpackets b on b.mchId=? and b.scanId=a.id and b.role=0
                left join user_cards c on c.scanId=a.id and c.scanId>0 and c.sended=1 and c.transId=-1 and c.role=0
                left join users d on d.id=a.userId
                left join geo_gps e on a.geoId=e.id
                left join cards f on f.id=c.cardId
                left join batchs g on a.batchId=g.id
                where a.mchId=? and a.scanTime>=UNIX_TIMESTAMP(?) and a.scanTime<=UNIX_TIMESTAMP(?) ";
                $data=[$mchId,$mchId,$start,$end];
                if(!$param['pro']==0){
                  $sql.="and concat(substring(e.areaCode,1,2),'0000')=? ";
                  $data[]=$param['pro'];
                }
                if(!$param['city']==0){
                  $sql.="and concat(substring(e.areaCode,1,4),'00')=? ";
                  $data[]=$param['city'];
                }
                if(!$param['area']==0){
                  $sql.="and e.areaCode=? ";
                  $data[]=$param['area'];
                }

                if(!$param['productid']==0){
                  $sql.='and g.productId=? ';
                  $data[]=$param['productid'];
                }
                if(!$param['batchid']==0){
                  $sql.='and a.batchId=? ';
                  $data[]=$param['batchid'];
                }

                $sql.="group by a.id;";

                $sql=$this->db->compile_binds($sql,$data);
                return $this->dbhelper->serve_array($sql);
    }

    /**************************************业务分析*************************************************/
    public function get_business_data($mchId,$param){        
        //选择年-查1-12个月的 对应monthly
        if($param['month']==0){
            $sql="select theDate,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointAmount) point_amount,sum(pointUsed) point_num from rpt_user_monthly where mchId=? and theDate>=? and theDate<=? ";
            $data=[$mchId,$param['year'].'-01',$param['year'].'-12'];
            if(!$param['productid']==0){
              $sql.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql.="group by theDate;";
            $sql=$this->db->compile_binds($sql,$data);
            return $this->dbhelper->serve_array($sql);
        }
        //选择年、月-查1-31天的/当月的周 对应daily/weekly
        if($param['week']==0&&!$param['month']==0){
            if(isset($param['level'])&&$param['level']=='week'){
                $weekinfo=get_weekinfo($param['year'].'-'.$param['month']);
                for($i=0;$i<count($weekinfo);$i++){
                  $weekinfo[$i]=$weekinfo[$i][2];  
                } 
                //查询每周的数据
                $sql="select theDate,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointAmount) point_amount,sum(pointUsed) point_num from rpt_user_weekly where mchId=? and theDate>=? and theDate<=? ";
                $data=[$mchId,reset($weekinfo),end($weekinfo)];
                if(!$param['productid']==0){
                  $sql.='and productId=? ';
                  $data[]=$param['productid'];
                }
                if(!$param['batchid']==0){
                  $sql.='and batchId=? ';
                  $data[]=$param['batchid'];
                }
                $sql.="group by theDate;";
                $sql=$this->db->compile_binds($sql,$data);
                return $this->dbhelper->serve_array($sql);

            }else{
                  $start=$param['year'].'-'.$param['month'].'-01';
                  $end=date('Y-m-d', strtotime("$start +1 month -1 day"));
                  $sql="select theDate,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointAmount) point_amount,sum(pointUsed) point_num from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
                  $data=[$mchId,$start,$end];
                  if(!$param['productid']==0){
                    $sql.='and productId=? ';
                    $data[]=$param['productid'];
                  }
                  if(!$param['batchid']==0){
                    $sql.='and batchId=? ';
                    $data[]=$param['batchid'];
                  }
                  $sql.="group by theDate;";
                  $sql=$this->db->compile_binds($sql,$data);
                  return $this->dbhelper->serve_array($sql);
            } 
        }
        //选择年-月-周 显示该周的天数
        if(!$param['week']==0){
            //处理时间
            $timearr=explode("_",$param['weektime']);
            $start=$timearr[0];
            $end=$timearr[1];

            $sql="select theDate,sum(scanCount) scanNum,round(sum(rpAmount)/100,2) red_amount,round(sum(transAmount)/100,2) trans_amount,sum(cardCount) card_num,sum(pointAmount) point_amount,sum(pointUsed) point_num from rpt_user_daily where mchId=? and theDate>=? and theDate<=? ";
            $data=[$mchId,$start,$end];
            if(!$param['productid']==0){
              $sql.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql.="group by theDate;";
            $sql=$this->db->compile_binds($sql,$data);
            return $this->dbhelper->serve_array($sql);
            // return $this->db->query($sql,$data)->result_array();
        }
        
    }
    /**************************************时段分布开始*************************************************/
    /**
     * 时段分析数据
     * @param 商户 $mchId
     * @param 参数  $param
     */
    public function period_get_data($mchId,$param){
        //根据传递过来的级别来分配起始时间
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
        if(!$param['day']==0){
          $start=$param['day'];
          $end=$param['day'];
        }
        $sql="select hour time, sum(scanNum) scanNum from rpt_area_scanall where mchId=? and date >=? and date<=? ";
        $data=[$mchId,$start,$end];
        if(!$param['productid']==0){
          $sql.='and productId=? ';
          $data[]=$param['productid'];
        }
        if(!$param['batchid']==0){
          $sql.='and batchId=? ';
          $data[]=$param['batchid'];
        }
        $sql.=" group by hour;";
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve_array($sql);
    }
    /**************************************区域分布开始*************************************************/
    /**
     * 获取数据汇总数据
     * @param 商户 $mchId
     * @param 条件 $param
     */
    public function get_sum_of_data_soure($mchId,$param){
      $time=get_time_screening($param);
      $sql="select m.*,CASE WHEN (m.scan_num/m.scan_all)>1 THEN '100.00' ELSE ifnull(round(((m.scan_num/m.scan_all))*100,2),'0.00') END per from(select ";
      //扫码城市
      $sql.="ifnull((select count(a.city) from (select count(*) city from rpt_area_daily where mchId =? and cityCode!=''
and scanNum!='0' and date>=? and date<=? ";
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      $sql.="GROUP BY cityCode) a),'0') as city,";
      //红包城市
      $sql.="ifnull((select count(b.red_city) red from (select count(*) red_city from rpt_area_daily where mchId =? and cityCode!='' and redNum!='0'
and date>=? and date<=? ";
      array_push($data,$mchId,$time['start'],$time['end']);
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      $sql.="GROUP BY cityCode) b),'0') as red_city,";
      //扫码量
      $sql.="ifnull((select sum(c.scan_num) scan_num from (select cityCode,sum(scanNum) scan_num from rpt_area_daily where mchId =? and cityCode!=''
and date>=? and date<=? ";
      array_push($data,$mchId,$time['start'],$time['end']);
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      $sql.="GROUP BY cityCode) c),'0') as scan_num,";
      //扫码总量
      $sql.="ifnull((select sum(scanNum) scanNum from rpt_area_scanall where mchId=? and date>=? and date<=? ";

      array_push($data,$mchId,$time['start'],$time['end']);
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }

      $sql.="),'0') as scan_all)m;";
      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serverow($sql);
    }
    /**
     * 获取省份Map数据
     * @param 起始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function get_pro_map_data_soure($mchId,$param){
        $time=get_time_screening($param);
        $sql="select a.name,ifnull(sum(b.scan_nums),0) value from areas a
            left join (select proCode,sum(scanNum) scan_nums from rpt_area_daily where mchId=? and date>=? and date<=? ";
        $data=[$mchId,$time['start'],$time['end']];
        if(!$param['productid']==0){
          $sql.='and productId=? ';
          $data[]=$param['productid'];
        }
        if(!$param['batchid']==0){
          $sql.='and batchId=? ';
          $data[]=$param['batchid'];
        }

        $sql.="group by proCode) b on a.code=b.proCode where a.level='0' group by a.name order by value desc;";

        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve($sql);
    }
    /**
     * 获取区级数据
     * @param 商户 $mchId
     * @param 城市名称 $cityName
     * @param 是否是直辖市 $is_zxs
     */
    public function get_area_map_pro_data($mchId,$cityName,$is_zxs,$param){
      $time=get_time_screening($param);
      $sql="select a.name,ifnull(sum(b.scan_nums),0) value from areas a
left join (select areaCode,sum(scanNum) scan_nums from rpt_area_daily where mchId=? and date>=? and date<=? ";
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      if($is_zxs=='1'){
        $parentCode="(select code from areas where name=? and level='1')";
        $data[]=$cityName;
      }else{
        $parentCode="(select code from areas where name=?)";
        $data[]=$cityName;
      }
      $sql.="group by areaCode) b on b.areaCode=a.code where ";
      // 东莞等特殊城市特殊处理
      // 查询出特殊地区的 特征
      $tscity=$this->db->query("select * from areas where parentCode=(select code from areas where name=?)",[$cityName])->row();
      if(empty($tscity)){
        $sql.="a.code=$parentCode group by a.name;";
      }else{
        $sql.="a.parentCode=$parentCode group by a.name;";
      }
      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve_array($sql);
    }

    /**
     * 获取直辖市时间段数据
     * @param 商户 $mchId
     * @param 直辖市 $id
     * @param 开始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function get_city_map_zxs_time_data($mchId,$id,$param){
        $time=get_time_screening($param);
        $sql="select a.name,ifnull(sum(b.scan_nums),0) value from areas a
    		left join (select areaCode,sum(scanNum) scan_nums from rpt_area_daily where mchId=? and date>=? and date<=? ";
        $data=[$mchId,$time['start'],$time['end']];
        if(!$param['productid']==0){
          $sql.='and productId=? ';
          $data[]=$param['productid'];
        }
        if(!$param['batchid']==0){
          $sql.='and batchId=? ';
          $data[]=$param['batchid'];
        }
        $sql.="group by areaCode) b on b.areaCode=a.code where a.parentCode=(select code from areas where name=? and level='1') group by a.name;";
        $data[]=$id;
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve($sql);
    }

    /**
     * 获取市区时间段数据
     * @param 商户 $mchId
     * @param 省份 $id
     * @param 开始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function get_city_map_pro_time_data($mchId,$id,$param){
        $time=get_time_screening($param);
        $sql="select a.name,ifnull(sum(c.scan_nums),0) value from areas a
    		left join areas b on b.parentCode=a.code
    		left join (select areaCode,sum(scanNum) scan_nums from rpt_area_daily where mchId=? and date>=? and date<=? ";
        $data=[$mchId,$time['start'],$time['end']];
        if(!$param['productid']==0){
          $sql.='and productId=? ';
          $data[]=$param['productid'];
        }
        if(!$param['batchid']==0){
          $sql.='and batchId=? ';
          $data[]=$param['batchid'];
        }
        $sql.="group by areaCode) c on c.areaCode=b.code where a.parentCode=(select code from areas where name=?) group by a.name;";
        $data[]=$id;
        $sql=$this->db->compile_binds($sql,$data);
        $arr=$this->dbhelper->serve_array($sql);

        // 东莞等特殊地区处理
        $sql_1="select a.name,ifnull(sum(b.scan_nums),0) value from areas a
        left join (select areaCode,sum(scanNum) scan_nums from rpt_area_daily where mchId=? and date>=? and date<=? ";
        $data_1=[$mchId,$time['start'],$time['end']];
        if(!$param['productid']==0){
          $sql_1.='and productId=? ';
          $data_1[]=$param['productid'];
        }
        if(!$param['batchid']==0){
          $sql_1.='and batchId=? ';
          $data_1[]=$param['batchid'];
        }
        $sql_1.="group by areaCode) b on b.areaCode=a.code where a.parentCode=(select code from areas where name=?) group by a.name;";
        $data_1[]=$id;
        $sql_1=$this->db->compile_binds($sql_1,$data_1);
        $arr_1=$this->dbhelper->serve_array($sql_1);
        $arrs=array_merge($arr,$arr_1);
        foreach($arrs as $item) {
            if(! isset($res[$item['name']])) $res[$item['name']] = $item;
            else {
                $res[$item['name']]['value'] += $item['value'];
            }
        }
        $result=array_values($res);
        return $result;
    }
    /**
     * 获取表格省份数据
     * @param 商户 $mchId
     * @param 开始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function get_table_pro_data_soure($mchId,$param){
      $time=get_time_screening($param);
      $sql="select a.*,b.name from (select proCode,ifnull(sum(scanNum),0) scanNum,ifnull(round(sum(redNum)/100,2),0) redNum,ifnull(sum(pointAmount),0) pointAmount from rpt_area_daily where mchId=? and date>=? and date<=? and areaCode is not null and areaCode!='000000' ";
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      $sql.="group by proCode order by scanNum desc,redNum desc)a inner join areas b on b.code=a.proCode;";
      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve($sql);
    }
    /**
     * 获取表格市区-省份数据
     * @param 商户 $mchId
     * @param 省份 $city
     * @param 开始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function get_table_city_data_pro($mchId,$city,$param){
      $time=get_time_screening($param);
      $sql="select m.name,m.scanNum,m.redNum,m.pointAmount from (select a.name,ifnull(sum(c.scanNum),0) scanNum,ifnull(round(sum(c.redNum)/100,2),0) redNum,ifnull(sum(c.pointAmount),0) pointAmount from areas a
left join areas b on b.parentCode=a.code
left join (select areaCode,sum(scanNum) scanNum,sum(redNum) redNum,sum(pointAmount) pointAmount from rpt_area_daily where mchId=? and date>=? and date<=? ";
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      $sql.="group by areaCode) c on c.areaCode=b.code
where a.parentCode=(select code from areas where name=? ";
      $data[]=$city;

      $sql.=") group by a.name order by scanNum desc) m where m.scanNum!='0' or m.redNum!='0.00';";
      $sql=$this->db->compile_binds($sql,$data);
      $arr=$this->dbhelper->serve_array($sql);
      // 东莞等城市特殊处理
      $sql_1="select m.name,m.scanNum,m.redNum,m.pointAmount from (select a.name,ifnull(sum(b.scanNum),0) scanNum,ifnull(round(sum(b.redNum)/100,2),0) redNum,ifnull(sum(b.pointAmount),0) pointAmount from areas a
left join (select areaCode,sum(scanNum) scanNum,sum(redNum) redNum,sum(pointAmount) pointAmount from rpt_area_daily where mchId=? and date>=? and date<=? ";
      $data_1=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql_1.='and productId=? ';
        $data_1[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql_1.='and batchId=? ';
        $data_1[]=$param['batchid'];
      }
      $sql_1.="group by areaCode) b on b.areaCode=a.code
where a.parentCode=(select code from areas where name=? ";
      $data_1[]=$city;

      $sql_1.=") group by a.name order by scanNum desc) m where m.scanNum!='0' or m.redNum!='0.00';";
      $sql_1=$this->db->compile_binds($sql_1,$data_1);
      $arr_1=$this->dbhelper->serve_array($sql_1);
      $result=array_merge($arr,$arr_1);
      $scanNum = array();
      $redNum = array();
      foreach ($result as $vod) {
          $scanNum[] = $vod['scanNum'];
          $redNum[] = $vod['redNum'];
      }
      array_multisort($scanNum, SORT_DESC, $redNum, SORT_DESC, $result);
      return $result;
    }
    /**
     * 获取表格市区-直辖市数据
     * @param 商户 $mchId
     * @param 直辖市 $city
     * @param 开始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function get_table_city_data_zxs($mchId,$city,$param){
      $time=get_time_screening($param);
      $sql="select m.name,m.scanNum,m.redNum,m.pointAmount from (select a.name,ifnull(sum(b.scanNum),0) scanNum,ifnull(round(sum(b.redNum)/100,2),0) redNum,ifnull(sum(b.pointAmount),0) pointAmount from areas a
left join (select areaCode,sum(scanNum) scanNum,sum(redNum) redNum,ifnull(sum(pointAmount),0) pointAmount from rpt_area_daily where mchId=? and date>=? and date<=? ";
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }

      $sql.="group by areaCode) b on b.areaCode=a.code
where a.parentCode=(select code from areas where name=? and level='1'";
      $data[]=$city;

      $sql.=") group by a.name order by scanNum desc) m where m.scanNum!='0' or m.redNum!='0.00';";
      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve($sql);
    }
    /**
     * 地域统计量数据下载
     * @param 商户 $mchId
     * @param 开始时间 $startDate
     * @param 结束时间 $endDate
     */
    public function down_area_data_model($mchId,$param){
      $time=get_time_screening($param);
      $sql="select b.name proName,ifnull(c.name,b.name) cityName,ifnull(d.name,c.name) areaName,a.scanNum,a.redNum,a.pointAmount from (
select concat(substring(areaCode,1,2),'0000') proCode,concat(substring(areaCode,1,4),'00') cityCode,areaCode,ifnull(sum(scanNum),0) scanNum,ifnull(round(sum(redNum)/100,2),0) redNum,ifnull(sum(pointAmount),0) pointAmount from rpt_area_daily 
where mchId=? and date>=? and date<=? and areaCode is not null and areaCode!='000000' ";
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      $sql.="group by areaCode) a left join areas b on a.proCode=b.code left join areas c on a.cityCode=c.code left join areas d on a.areaCode=d.code";
      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve_array($sql);	
    }
    //下载每个月的日数据
    public function down_area_daily_data($mchId,$param){
        $time=get_time_screening($param);
        $date_string='';
        $begintime = strtotime($time['start']);$endtime = strtotime($time['end']);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $date_string= $date_string.",".date("Y-m-d", $start);
        }
        //拿到x轴日期数据 $date_arr
        $data_arr=explode(",",ltrim($date_string,","));
        $sql="select b.name Province,ifnull(c.name,b.name) City,ifnull(d.name,c.name) Area,a.* FROM(select concat(substring(areaCode,1,2),'0000') proCode,concat(substring(areaCode,1,4),'00') cityCode,areaCode,";
        for($i=0;$i<count($data_arr);$i++){
          $sql.="ifnull(sum(case when date='".$data_arr[$i]."' then scanNum end),0) '".$data_arr[$i]."',";
        }
        $sql.="ifnull(sum(scanNum),0) scanNum from rpt_area_daily where mchId=? and date>=? and date<=? and areaCode>0 ";
        $data=[$mchId,$time['start'],$time['end']];
        if(!$param['productid']==0){
          $sql.='and productId=? ';
          $data[]=$param['productid'];
        }
        if(!$param['batchid']==0){
          $sql.='and batchId=? ';
          $data[]=$param['batchid'];
        }
        $sql.="group by areaCode) a left join areas b on a.proCode=b.code left join areas c on a.cityCode=c.code left join areas d on a.areaCode=d.code";
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve_array($sql);
    }
    /**************************************扫码分布(热力图)开始*************************************************/
    /*public function get_scan_area_data($mchId,$north,$south,$west,$east,$level,$westLng,$eastLng,$times,$start,$end){
      $dig='0x00000000FFFFFFFF';
      $sql='select scale>>32 lngScale,scale&'.$dig.' latScale,sum(scanCount) count from rpt_geo_daily
            where mchId=? and level=? and scanDate>=? and scanDate<=? and latScale is not null and
            latScale<? and latScale>? and lngScale>? and lngScale<? and
            f_geo_get_lng_by_scale(lngScale*?,latScale*?)<? and f_geo_get_lng_by_scale((lngScale+1)*?,(latScale+1)*?)>? group by scale;';
        $sql=$this->db->compile_binds($sql,[$mchId,$level,$start,$end,$north,$south,$west,$east,$times,$times,$eastLng,$times,$times,$westLng]);
        $data=$this->dbhelper->serve_array($sql);
      //$data=$this->db->query($sql,[$mchId,$level,$start,$end,$north,$south,$west,$east,$times,$times,$eastLng,$times,$times,$westLng])->result_array();
      //print_r($this->db->last_query());

      $sql='select max(count) maxCount from (select scale,sum(scanCount) count
            from rpt_geo_daily where mchId=? and level=? and scanDate>=? and scanDate<=? and
            latScale is not null group by scale) t;';
        $sql=$this->db->compile_binds($sql,[$mchId,$level,$start,$end]);
        $max=$this->dbhelper->serverow($sql)->maxCount;
        return ['data'=>$data==null?[]:$data,'max'=>$max*0.618];
        //$max=$this->db->query($sql,[$mchId,$level,$start,$end])->row()->maxCount;
        //return ['data'=>$data,'max'=>$max*0.618];
    }*/
    
    public function get_scan_area_data($mchId,$north,$south,$west,$east,$level,$westLng,$eastLng,$times,$batchId,$productId,$year,$month,$week,$day,$pro,$city){
        
        $tableName='';
        $scanDate='';
        if($day!='0'){
            $tableName='rpt_geo_daily';
            $scanDate=$day;
        }
        else if($week!='0'){
            $tableName='rpt_geo_weekly';
            $scanDate=$week;
        }
        else{
            $tableName='rpt_geo_monthly';
            $scanDate=$year.'-'.$month;
        }
        
        $conSql='mchId=? and level=? and scanDate=?';
        $conParams=[$mchId,$level,$scanDate];
        if($batchId!=0){
            $conSql.=' and batchId=?';
            $conParams[]=$batchId;
        }
        if($productId!=0){
            $conSql.=' and productId=?';
            $conParams[]=$productId;
        }
        if($city!='0'){
            $conSql.=' and cityCode=?';
            $conParams[]=$city;
        }
        else if($pro!='0'){
            $conSql.=' and proCode=?';
            $conParams[]=$pro;
        }
        
        $dig='0x00000000FFFFFFFF';
        $sql='select scale>>32 lngScale,scale&'.$dig.' latScale,sum(scanCount) count from '.$tableName.'
            where '.$conSql.' and latScale is not null and
            latScale<? and latScale>? and lngScale>? and lngScale<? and
            f_geo_get_lng_by_scale(lngScale*?,latScale*?)<? and f_geo_get_lng_by_scale((lngScale+1)*?,(latScale+1)*?)>? group by scale;';
        $parmas=array_merge($conParams,[$north,$south,$west,$east,$times,$times,$eastLng,$times,$times,$westLng]);
        $sql=$this->db->compile_binds($sql,$parmas);
        $data=$this->dbhelper->serve_array($sql);        
        //$data=$this->db->query($sql,$parmas)->result_array();
        
        $sql='select max(count) maxCount from (select scale,sum(scanCount) count
            from '.$tableName.' where '.$conSql.' and
            latScale is not null group by scale) t;';
        $sql=$this->db->compile_binds($sql,$conParams);
        $max=$this->dbhelper->serverow($sql)->maxCount;
        //$max=$this->db->query($sql,$conParams)->row()->maxCount;
        return ['data'=>$data==null?[]:$data,'max'=>$max*0.618];
    }
    /**************************************新老用户扫码分析开始*************************************************/
    public function get_useranalysis_data($mchId,$param){
      //根据传递过来的级别来分配起始时间
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
      $sql="select old.theDate,ifnull(osc,'0') oldScan,ifnull(nsc,'0') newScan from (";
      $sql.="select theDate,sum(scanCount) osc from rpt_user_daily rud
  inner join users on rud.userId=users.id and from_unixtime(users.createTime)<theDate where rud.mchId=? and theDate>=? and theDate<=? ";

      $data=[$mchId,$start,$end];

      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }

      $sql.="group by theDate) old left join (select theDate,sum(scanCount) nsc from rpt_user_daily rud
  inner join users on rud.userId=users.id and from_unixtime(users.createTime)>=theDate where rud.mchId=? and theDate>=? and theDate<=? ";
      $data[]=$mchId;
      $data[]=$start;
      $data[]=$end;
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }

      $sql.="group by theDate) new on old.theDate=new.theDate order by old.theDate desc;";

      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve_array($sql);
    }
    /**************************************区域趋势开始*************************************************/
    public function get_trend_data($mchId,$param){
      $sql="select date theDate,sum(scanNum) scanNum from rpt_area_daily where mchId=? and date>=? and date<=? ";
      $time=get_time_screening($param);
      $data=[$mchId,$time['start'],$time['end']];
      if(!$param['productid']==0){
        $sql.='and productId=? ';
        $data[]=$param['productid'];
      }
      if(!$param['batchid']==0){
        $sql.='and batchId=? ';
        $data[]=$param['batchid'];
      }
      if(!$param['pro']==0){
        $sql.='and proCode=? ';
        $data[]=$param['pro'];
      }
      if(!$param['city']==0){
        $sql.='and cityCode=? ';
        $data[]=$param['city'];
      }
      if(!$param['area']==0){
        $sql.='and areaCode=? ';
        $data[]=$param['area'];
      }
      $sql.="group by date";

      $sql=$this->db->compile_binds($sql,$data);
      return $this->dbhelper->serve_array($sql);
    }
    
    /**************************************活动评估开始*************************************************/
    public function get_product($mchId,$cate){
    	$sql="select id,name from products where mchId=? and categoryId=?;";
      $sql=$this->db->compile_binds($sql,[$mchId,$cate]);
      return $this->dbhelper->serve_array($sql);
    }
    public function get_policy($id){
      return $this->db->where('id',$id)->get('activity_log')->row_array();
    }
    public function get_policy_data($mchId,$param){
        $time=get_time_screening($param);

        $sql="select theDate,sum(rpAmount)/100 redNum,sum(scanCount) scanNum from rpt_activity_evaluating where mchId=? and theDate>=? and theDate<=? ";
        $data=[$mchId,$time['start'],$time['end']];
        if($param['activityid']!=0){
            $sql.=" and activityId=? ";
            $data[]=intval($param['activityid']);
        }
        if($param['categoryid']!=0){
            $sql.=" and categoryId=? ";
            $data[]=intval($param['categoryid']);
        }
        if($param['productid']!=0){
            $sql.=" and productId=? ";
            $data[]=intval($param['productid']);
        }
        if($param['batchid']!=0){
            $sql.=" and batchId=? ";
            $data[]=intval($param['batchid']);
        }
        $sql.=" group by theDate;";

        $sql=$this->db->compile_binds($sql,$data);
        $result['data']=$this->dbhelper->serve_array($sql);

        //查询策略
        //拉去以前的策略
        $sql2="select policyLevel as level,theTime from activity_log where mchId=? ";
        $data2=[$mchId];
       if($param['activityid']!=0){
            $sql2.=" and activityId=? ";
            $data2[]=intval($param['activityid']);
        }
        if($param['categoryid']!=0){
            $sql2.=" and categoryId=? ";
            $data2[]=intval($param['categoryid']);
        }
        if($param['productid']!=0){
            $sql2.=" and productId=? ";
            $data2[]=intval($param['productid']);
        }
        if($param['batchid']!=0){
            $sql2.=" and batchId=? ";
            $data2[]=intval($param['batchid']);
        }
        $sql2.=" and theTime>=? and theTime<=?;";
        $data2[]=$time['start'];
        $data2[]=$time['end'];

        $sql2=$this->db->compile_binds($sql2,$data2);
        $result['policy']=$this->dbhelper->serve_array($sql2);

        //如果历史策略拉取不到 则拉取该活动最后一个历史策略
        if(!$result['policy']&&$time['start']>=date('Y-m-d')&&date("m", strtotime($time['start']))!==date('m')){
          $sql3="select policyLevel as level,theTime from activity_log where mchId=? ";
          $data3=[$mchId];
         if($param['activityid']!=0){
              $sql3.=" and activityId=? ";
              $data3[]=intval($param['activityid']);
          }
          if($param['categoryid']!=0){
              $sql3.=" and categoryId=? ";
              $data3[]=intval($param['categoryid']);
          }
          if($param['productid']!=0){
              $sql3.=" and productId=? ";
              $data3[]=intval($param['productid']);
          }
          if($param['batchid']!=0){
              $sql3.=" and batchId=? ";
              $data3[]=intval($param['batchid']);
          }
          $sql3.=" order by theTime desc limit 1;";

          $sql3=$this->db->compile_binds($sql3,$data3);
          $result['policy']=$this->dbhelper->serve_array($sql3);
        }

        //如果当前月份为空
        if(!$result['policy']&&$time['start']<=date('Y-m-d')&&date("m", strtotime($time['start']))==date('m')){
          $sql="select policyLevel as level,theTime from activity_log where mchId=? ";
          $data=[$mchId];
         if($param['activityid']!=0){
              $sql.=" and activityId=? ";
              $data[]=intval($param['activityid']);
          }
          if($param['categoryid']!=0){
              $sql.=" and categoryId=? ";
              $data[]=intval($param['categoryid']);
          }
          if($param['productid']!=0){
              $sql.=" and productId=? ";
              $data[]=intval($param['productid']);
          }
          if($param['batchid']!=0){
              $sql.=" and batchId=? ";
              $data[]=intval($param['batchid']);
          }
          $sql.=" order by theTime desc limit 1;";

          $sql=$this->db->compile_binds($sql,$data);
          $result['policy']=$this->dbhelper->serve_array($sql);
        }

        //如果选中的开始时间不等于结果中的策略第一个子项的start时间，则需要查询出上月最后一个策略来补齐
        //月补齐
        if($result['policy']&&$time['start']!==$result['policy'][0]->start&&date('d',strtotime($time['start']))=='01'){
          //查询上月的是否有策略，有的话就补齐
          $sql4="select policyLevel as level,theTime from activity_log where mchId=? ";
          $data4=[$mchId];
          if($param['activityid']!=0){
              $sql4.=" and activityId=? ";
              $data4[]=intval($param['activityid']);
          }
          if($param['categoryid']!=0){
              $sql4.=" and categoryId=? ";
              $data4[]=intval($param['categoryid']);
          }
          if($param['productid']!=0){
              $sql4.=" and productId=? ";
              $data4[]=intval($param['productid']);
          }
          if($param['batchid']!=0){
              $sql4.=" and batchId=? ";
              $data4[]=intval($param['batchid']);
          }
          $sql4.=" and theTime>=? and theTime<=? order by theTime desc;";
          $data4[]=date('Y-m-01',strtotime('-1 month',strtotime($time['start'])));
          $data4[]=date('Y-m-t',strtotime('-1 month',strtotime($time['start'])));
          $istop=$this->db->query($sql4,$data4)->row();
          if(!empty($istop)){
            $top=array(
              'level'=>$istop->level,
              'start'=>$time['start'],
              'end'=>$result['policy'][0]->start
            );
            array_unshift($result['policy'],$top);
          }
        }

        return $result;
    }
    public function get_policy_rule_data($mchId,$param){
        $time=get_time_screening($param);
        $sql="select * from activity_log where mchId=? ";
        $data=[$mchId];
        if($param['activityid']!=0){
            $sql.=" and activityId=? ";
            $data[]=intval($param['activityid']);
        }
        if($param['categoryid']!=0){
            $sql.=" and categoryId=? ";
            $data[]=intval($param['categoryid']);
        }
        if($param['productid']!=0){
            $sql.=" and productId=? ";
            $data[]=intval($param['productid']);
        }
        if($param['batchid']!=0){
            $sql.=" and batchId=? ";
            $data[]=intval($param['batchid']);
        }
        $sql.=" and theTime>=? and theTime<=?;";
        $data[]=$time['start'];
        $data[]=$time['end'];
        $sql=$this->db->compile_binds($sql,$data);
        $result=$this->dbhelper->serve_array($sql);

        if(!$result&&$time['start']>=date('Y-m-d')&&date("m", strtotime($time['start']))!==date('m')){
          $sql="select * from activity_log where mchId=? ";
          $data=[$mchId];
          if($param['activityid']!=0){
              $sql.=" and activityId=? ";
              $data[]=intval($param['activityid']);
          }
          if($param['categoryid']!=0){
              $sql.=" and categoryId=? ";
              $data[]=intval($param['categoryid']);
          }
          if($param['productid']!=0){
              $sql.=" and productId=? ";
              $data[]=intval($param['productid']);
          }
          if($param['batchid']!=0){
              $sql.=" and batchId=? ";
              $data[]=intval($param['batchid']);
          }
          $sql.=" order by theTime desc limit 1;";

          $sql=$this->db->compile_binds($sql,$data);
          $result=$this->dbhelper->serve_array($sql);
        }elseif(!$result&&$time['start']<=date('Y-m-d')&&date("m", strtotime($time['start']))==date('m')){
            $sql="select * from activity_log where mchId=? ";
            $data=[$mchId];
            if($param['activityid']!=0){
                $sql.=" and activityId=? ";
                $data[]=intval($param['activityid']);
            }
            if($param['categoryid']!=0){
                $sql.=" and categoryId=? ";
                $data[]=intval($param['categoryid']);
            }
            if($param['productid']!=0){
                $sql.=" and productId=? ";
                $data[]=intval($param['productid']);
            }
            if($param['batchid']!=0){
                $sql.=" and batchId=? ";
                $data[]=intval($param['batchid']);
            }
            $sql.=" order by theTime desc limit 1;";

            $sql=$this->db->compile_binds($sql,$data);
            $result=$this->dbhelper->serve_array($sql);
        }else{
            //如果选中的开始时间不等于结果中的规则第一个子项的start时间，则需要查询出上月最后一个策略来补齐
            //月补齐
            if($result&&date('d',strtotime($time['start']))=='01'){
              //查询上月的是否有策略，有的话就补齐
              $sql="select * from activity_log where mchId=? ";
              $data=[$mchId];
              if($param['activityid']!=0){
                  $sql.=" and activityId=? ";
                  $data[]=intval($param['activityid']);
              }
              if($param['categoryid']!=0){
                  $sql.=" and categoryId=? ";
                  $data[]=intval($param['categoryid']);
              }
              if($param['productid']!=0){
                  $sql.=" and productId=? ";
                  $data[]=intval($param['productid']);
              }
              if($param['batchid']!=0){
                  $sql.=" and batchId=? ";
                  $data[]=intval($param['batchid']);
              }
              $sql.=" and theTime>=? and theTime<=? order by theTime desc limit 1;";
              $data[]=date('Y-m-01',strtotime('-1 month',strtotime($time['start'])));
              $data[]=date('Y-m-t',strtotime('-1 month',strtotime($time['start'])));
              $istop=$this->db->query($sql,$data)->row();

              if(!empty($istop)){
                array_unshift($result,$istop);
              }
            }
        }
        return $result;
    }
    /*************************临时为锐欧提供的数据下载方法 mchId=62************************/
    public function get_down_detail_scan($mchId){
        $sql="select a.userId,a.`code`,date(FROM_UNIXTIME(a.scanTime )) date,FROM_UNIXTIME(a.scanTime,'%H:%i:%s') time,b.`name`,c.batchNo,d.`name` productName,CONCAT(a.geoLat,',',a.geoLng) gps,h.name proName,g.name cityName,u.openid,replace(replace(replace(u.nickName,CHAR(13),''),CHAR(10),''),'\"','') nickname,u.province,u.city,u.country,u.mobile,u.email,u.qq, DATE_FORMAT(u.birthday,'%Y-%m-%d') birthday,if(u.sex=1,'男','女') as sex from scan_log a
          left join sub_activities b on a.activityId=b.id
          left join batchs c on a.batchId=c.id
          left join products d on c.productId=d.id
          left join geo_gps e on a.geoId=e.id
          left join areas f on e.areaCode=f.code
          left join areas g on f.parentCode=g.code
          left join areas h on g.parentCode=h.code
          left join users u on a.userId=u.id
          where a.mchId=?";
        $sql=$this->db->compile_binds($sql,[$mchId]);
        return $this->dbhelper->serve_array($sql);
    }
    /*************************消费者画像数据************************/
    //根据区域code获取名称
    //省市区
    public function getAreaName($code){
      // 东莞等特殊地区处理
      $tscity=$this->db->query("select * from areas where parentCode=(select code from areas where code=?)",[$code])->row();
      if(!empty($tscity)){
        return $this->db->query("select concat(b.name,a.name) as name,a.name areaName from areas a left join areas b on a.parentCode=b.code where a.code=?",[$code])->row();
      }else{
        return $this->db->query("select concat(c.name,b.name) as name,a.name areaName from areas a left join areas b on a.parentCode=b.code left join areas c on b.parentCode=c.code where a.code=?",[$code])->row();
      }
    }
    public function get_portrait_data($param,&$count=0){
        $sql_cnt="select sum(num) cnt from rpt_user_portrait where mchId=? ";
        $sql="select * from rpt_user_portrait where mchId=? ";
        $data=[$param['mchId']];

        if($param['proCode']!=0){
            $sql.=" and proCode=? ";
            $sql_cnt.=" and proCode=? ";
            $data[]=$param['proCode'];
        }
        if($param['cityCode']!=0){
            $sql.=" and cityCode=? ";
            $sql_cnt.=" and cityCode=? ";
            $data[]=$param['cityCode'];
        }
        if($param['areaCode']!=0){
            $sql.=" and areaCode=? ";
            $sql_cnt.=" and areaCode=? ";
            $data[]=$param['areaCode'];
        }
        if($param['age']!=0||!empty($param['age'])){
            $sql.=" and age=? ";
            $sql_cnt.=" and age=? ";
            $data[]=$param['age'];
        }
        if($param['sex']!=0){
            $sql.=" and sex=? ";
            $sql_cnt.=" and sex=? ";
            $data[]=$param['sex'];
        }
        if($param['constellation']!=0||!empty($param['constellation'])){
            $sql.=" and constellation=? ";
            $sql_cnt.=" and constellation=? ";
            $data[]=(string)$param['constellation'];
        }
        if($param['time']!=0||!empty($param['time'])){
            $sql.=" and time=? ";
            $sql_cnt.=" and time=? ";
            $data[]=$param['time'];
        }

        //计算总数
        $count=$this->db->compile_binds($sql_cnt,$data);
        $count=$this->dbhelper->serverow($count)->cnt;

        $sql.=" order by num desc,areaCode desc,total desc limit 3;";
        
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve_array($sql);
    }
/**************************************扫码排行-扫码明细*************************************************/
    public function get_userrank_scan($mchId,$param,&$count=0,$start=null,$length=null){
        $time=get_time_screening($param);
        $sql_count="select count(*) cnt from scan_log a
            left join users b on b.id=a.userId
            left join geo_gps c on c.id=a.geoId
            left join areas d on d.code=c.areaCode
            left join areas e on d.parentCode=e.code
            left join areas f on e.parentCode=f.code
            left join batchs g on g.id=a.batchId and g.rowStatus=0 where a.mchId=? and a.userId=? and a.scanTime>=? and a.scanTime<=? ";
        $sql="select ifnull(b.nickName,'红码用户') nickName,FROM_UNIXTIME(a.scanTime, '%Y-%m-%d %H:%i:%s') as date,ifnull(g.batchNo,'<span style=color:#ccc>批次已删除</span>') batchNo,a.code,a.userId,ifnull(d.fullName,'<span style=color:#ccc>终端不允许获取</span>') name from scan_log a
            left join users b on b.id=a.userId
            left join geo_gps c on c.id=a.geoId
            left join areas d on d.code=c.areaCode
            left join areas e on d.parentCode=e.code
            left join areas f on e.parentCode=f.code
            left join batchs g on g.id=a.batchId and g.rowStatus=0 where a.mchId=? and a.userId=? and a.scanTime>=? and a.scanTime<=? ";
            $data=[$mchId,$param['userId'],strtotime($time['start'].' 00:00:00'),strtotime($time['end'].' 23:59:59')];
          if(!$param['batchid']==0){
            $sql_count.='and a.batchId=? ';
            $sql.='and a.batchId=? ';
            $data[]=$param['batchid'];
          }
          if(!$param['pro']==0){
            $sql_count.='and f.code=? ';
            $sql.='and f.code=? ';
            $data[]=$param['pro'];
          }
          if(!$param['city']==0){
            $sql_count.='and e.code=? ';
            $sql.='and e.code=? ';
            $data[]=$param['city'];
          }
          
          if(!$param['productid']==0){
            $sql_count.='and g.productId=? ';
            $sql.='and g.productId=? ';
            $data[]=$param['productid'];
          }
          $sql.=" order by date desc";
          $count=$this->db->query($sql_count,$data)->row()->cnt;
          if(isset($start)&&isset($length)){
              $sql.=' limit ?,?';
              $data[]=intval($start);
              $data[]=intval($length);
          }


          $sql=$this->db->compile_binds($sql,$data);
          return $this->dbhelper->serve_array($sql);
    }

    public function getScoreChartsProduct($mchId) {
        $sql = "SELECT DISTINCT productId AS id, productName name FROM rpt_wusu_score_report WHERE mchId = ?";
        $data = $this->db->query($sql, [$mchId])->result();
        return $data;
    }

    public function getCodeChartsProduct($mchId) {
        $sql = "SELECT DISTINCT productId AS id, productName name FROM rpt_wusu_code_report WHERE mchId = ?";
        $data = $this->db->query($sql, [$mchId])->result();
        return $data;
    }

    public function getWusuScoreChartsMinDate() {
        $minDateRow = $this->db->query("SELECT min(theDate) minDate FROM rpt_wusu_score_report")->row();
        if (isset($minDateRow) && isset($minDateRow->minDate)) {
          $minDate = $minDateRow->minDate;
        } else {
          $minDate = date('Y-m-d', strtotime('-1 day'));
        }
        return $minDate;
    }

    public function getWusuCodeChartsMinDate() {
        $minDateRow = $this->db->query("SELECT min(theDate) minDate FROM rpt_wusu_code_report")->row();
        if (isset($minDateRow) && isset($minDateRow->minDate)) {
          $minDate = $minDateRow->minDate;
        } else {
          $minDate = date('Y-m-d', strtotime('-1 day'));
        }
        return $minDate;
    }

    /**
     * 乌苏二维码瓶盖查询
     * @return array
     */
    public function get_wusu_code_data($mchId,$param,&$count=0,$start=null,$length=null){
        if (isProd() && $mchId !== '173' && $mchId !== '0') {
            return ['data' => [], 'total' => 0];
        }
        $where = $this->getWhere($param);
        $this->db->where('mchId', $mchId);
        $this->db->where('theDate >=', $param['startTime']);
        $this->db->where('theDate <=', $param['endTime']);
        $this->db->where($where);
        $this->db->select('*, sum(pointsNum) pointsNum, sum(scanNum) scanNum');
        $this->db->group_by('mchId, activityId, batchId');
        $this->db->order_by('activityId', 'desc');
        $data = $this->db->limit($length, $start)->get('rpt_wusu_code_report')->result();

        $sql = "SELECT COUNT(distinct mchId, activityId, batchId) value 
          FROM rpt_wusu_code_report WHERE mchId = ? AND theDate >= ? AND theDate <= ?";
        foreach ($where as $key => $value) {
            $sql .= '  AND '. $key .'='. $value;
        }
        $count = $this->db->query($sql, [$mchId, $param['startTime'], $param['endTime']])->row();
        $count = $count->value;
        return ['data' => $data, 'total' => $count];

        // $sql_count="select count(id) cnt from rpt_wusu_code_report where mchId=? and theDate>=? and theDate<=? ";
        // $sql="select * from rpt_wusu_code_report where mchId=? and theDate>=? and theDate<=? ";
        // $data=[$mchId,$param['startTime'],$param['endTime']];
        // if(!$param['productId']==0){
        //     $sql_count.='and productId=? ';
        //     $sql.='and productId=? ';
        //     $data[]=$param['productId'];
        // }
        // if(!$param['batchId']==0){
        //     $sql_count.='and batchId=? ';
        //     $sql.='and batchId=? ';
        //     $data[]=$param['batchId'];
        // }

        // $count=$this->db->query($sql_count,$data)->row()->cnt;
        // if(isset($start)&&isset($length)){
        //     $sql.=' limit ?,?';
        //     $data[]=intval($start);
        //     $data[]=intval($length);
        // }

        // return $this->db->query($sql,$data)->result();
    }

    private function getWhere($params) {
        $where = [1 => 1];
        if ($params['productId'] !== '0') {
            $where['productId'] = $params['productId'];
        }
        if (isset($params['batchId']) && $params['batchId'] !== '0') {
            $where['batchId'] = $params['batchId'];
        }
        return $where;
    }

    //查询总积分
    public function get_totalScore_by_activityId($mchId,$activityId){
        $sql="SELECT ifnull(SUM(t.result),0) AS TotalScore FROM (SELECT p.amount * m.weight AS result FROM points_sub p INNER JOIN (SELECT strategyId, weight FROM mix_strategies_sub WHERE parentId IN (SELECT detailId FROM sub_activities WHERE mchId = ? AND id = ? AND activityType = 3 GROUP BY detailId) AND strategyType = 3 GROUP BY strategyId ) m ON p.parentId = m.strategyId GROUP BY m.strategyId ) t";
        return $this->db->query($sql,[$mchId,$activityId])->row()->TotalScore;
    }
    //查询积分
    public function get_scanarea_info($mchId,$param,$areaCode){
        $sql="SELECT b.name, b.fullName, SUM(a.scanNum) AS scanNum, SUM(a.pointsNum) AS pointsNum FROM rpt_wusu_code_report a LEFT JOIN areas b ON b.code = $areaCode WHERE a.mchId = ? AND a.theDate >= ? AND a.theDate <= ? AND a.scanAreaCode LIKE '%$areaCode%' ";
        $data=[$mchId,$param['startTime'],$param['endTime']];
        if(!$param['productId']==0){
            $sql_count.='and a.productId=? ';
            $sql.='and a.productId=? ';
            $data[]=$param['productId'];
        }
        if(!$param['batchId']==0){
            $sql_count.='and a.batchId=? ';
            $sql.='and a.batchId=? ';
            $data[]=$param['batchId'];
        }
        return $this->db->query($sql,$data)->row();
    }

    public function get_wusu_score_data($mchId, $param, $start = 0, $length = 10) {
        if (isProd() && $mchId !== '173' && $mchId !== '0') {
            return ['data' => [], 'total' => 0];
        }
        $where = $this->getWhere($param);
        $this->db->where('mchId', $mchId);
        $this->db->where('theDate >=', $param['startTime']);
        $this->db->where('theDate <=', $param['endTime']);
        $this->db->where($where);
        $this->db->select('*, sum(scanedPoints) scanedPoints, sum(scanedCaps) scanedCaps');
        $this->db->group_by('mchId, activityId');
        $this->db->order_by('activityId', 'desc');
        $data = $this->db->limit($length, $start)->get('rpt_wusu_score_report')->result();

        $sql = "SELECT COUNT(distinct mchId, activityId) value 
          FROM rpt_wusu_score_report WHERE mchId = ? AND theDate >= ? AND theDate <= ?";
        foreach ($where as $key => $value) {
            $sql .= '  AND '. $key .'='. $value;
        }
        $count = $this->db->query($sql, [$mchId, $param['startTime'], $param['endTime']])->row();
        return ['data' => $data, 'total' => $count->value];
    }

    public function getAreaCodeList($batchId, $theDate) {
      $sql = "SELECT t1.areaCode, sum(t1.scanNum) scanNum, t2.name, t2.fullName, sum(t1.pointsNum) pointsNum
        FROM rpt_wusu_scan_area t1 JOIN areas t2 ON t2.code = t1.areaCode
        WHERE batchId = ? AND theDate >= ? AND theDate <= ? GROUP BY areaCode";
      $areas = $this->db->query($sql, [$batchId, $theDate[0], $theDate[1]])->result();
      return $areas;
    }

    /**
     * 积分核对查询下载
     * @auther fengyanjun
     * @dateTime 2017-12-21 15:10
     * @param int $mchId
     * @param array $param 查询条件
     * @return array
     */
    public function get_wusu_score_data_download($mchId, $param) {
        if (isProd() && $mchId !== '173' && $mchId !== '0') {
            return ['data' => [], 'total' => 0];
        }
        $where = $this->getWhere($param);
        $this->db->where('mchId', $mchId);
        $this->db->where('theDate >=', $param['startTime']);
        $this->db->where('theDate <=', $param['endTime']);
        $this->db->where($where);
        $this->db->select('*, sum(scanedPoints) scanedPoints, sum(scanedCaps) scanedCaps');
        $this->db->group_by('mchId, activityId');
        $this->db->order_by('activityId', 'desc');
        $data = $this->db->get('rpt_wusu_score_report')->result();

        $sql = "SELECT COUNT(distinct mchId, activityId) value 
          FROM rpt_wusu_score_report WHERE mchId = ? AND theDate >= ? AND theDate <= ?";
        foreach ($where as $key => $value) {
            $sql .= '  AND '. $key .'='. $value;
        }
        $count = $this->db->query($sql, [$mchId, $param['startTime'], $param['endTime']])->row();
        return ['data' => $data, 'total' => $count->value];
    }
}
