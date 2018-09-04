<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 * @author add by cw 2016-12-29
 *
 */
class Trigger_model extends CI_Model {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('batch_model');
        $this->load->model('sub_activity_model');
    }
    // scan_log更新触发器 add by cw
    public function trigger_scan_log_update($scaninfo){
        $batchinfo=$this->batch_model->get($scaninfo->batchId);
        if($scaninfo->geoId!=-1){
            $location=$this->getLocation($scaninfo->geoId);
            $proCode=$location->proCode;
            $cityCode=$location->cityCode;
            $areaCode=$location->areaCode;
        }
        else{
            // 没有拿到位置的
            $proCode='000000';
            $cityCode='000000';
            $areaCode='000000';
        }
        $sql=<<<EOF
insert into rpt_area_daily(proCode,cityCode,areaCode,mchId,userId,date,scanNum,batchId,productId)
values('$proCode','$cityCode','$areaCode',$scaninfo->mchId,$scaninfo->userId, date(from_unixtime($scaninfo->scanTime)),1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update scanNum=scanNum+1;

insert into rpt_user_rank(proCode,cityCode,areaCode,mchId,userId,theDate,scanNum,batchId,productId)
values('$proCode','$cityCode','$areaCode',$scaninfo->mchId,$scaninfo->userId, date(from_unixtime($scaninfo->scanTime)),1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update scanNum=scanNum+1;

insert into rpt_user_daily(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,date(from_unixtime($scaninfo->scanTime)),1)
on duplicate key update scanCount=scanCount+1;

insert into rpt_user_weekly(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,(select if(DATE_FORMAT(FROM_UNIXTIME($scaninfo->scanTime),'%u')='00',DATE_FORMAT(CONCAT(year(date_sub(FROM_UNIXTIME($scaninfo->scanTime),interval 1 year)),'-12-31'),'%Y-%u'),DATE_FORMAT(FROM_UNIXTIME($scaninfo->scanTime),'%Y-%u'))),1)
on duplicate key update scanCount=scanCount+1;

insert into rpt_user_monthly(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,DATE_FORMAT(FROM_UNIXTIME($scaninfo->scanTime),'%Y-%m'),1)
on duplicate key update scanCount=scanCount+1;


insert into rpt_user_rank_all(mchId,userId,scanNum)values($scaninfo->mchId,$scaninfo->userId,1) on duplicate key update scanNum=scanNum+1;

insert into rpt_area_scanall(mchId,date,hour,scanNum,batchId,productId)
values($scaninfo->mchId,date(from_unixtime($scaninfo->scanTime)),CONCAT(LPAD(hour(from_unixtime($scaninfo->scanTime)),2,0),':00'),1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update scanNum=scanNum+1;

EOF;
        if(isset($scaninfo->activityId)&&!empty($scaninfo->activityId)){//已修改成在匹配到活动时触发
            $sql.="insert into rpt_activity_evaluating (mchId,userId,activityId,categoryId,productId,batchId,theDate,scanCount)values($scaninfo->mchId,$scaninfo->userId,$scaninfo->activityId,(select categoryId from batchs where id=$scaninfo->batchId),$batchinfo->productId,$scaninfo->batchId,date(from_unixtime($scaninfo->scanTime)),1) on duplicate key update scanCount=scanCount+1;";
        }
        $this->dbhelper->trigger($sql);
    }


    // scan_log插入触发器 add by cw
    public function trigger_scan_log_insert($scaninfo){
        $batchinfo=$this->batch_model->get($scaninfo->batchId);
        //判断是否拿到geoId
        if(isset($scaninfo->geoId)&&!empty($scaninfo->geoId)&&$scaninfo->geoId!='-1'){
            $location=$this->getLocation($scaninfo->geoId);
            $proCode=$location->proCode;
            $cityCode=$location->cityCode;
            $areaCode=$location->areaCode;
        }else{
           $proCode='000000';
           $cityCode='000000';
           $areaCode='000000';
        }
        ////////////////这里特殊处理下 如果活动状态异常 同样触发 扫码记录/////////////////////
        $isWaiter=FALSE;
        $result=(object)array(
            'errcode'=>0
        );
        if(isset($scaninfo->activityId)){
            $activity=$this->sub_activity_model->get($scaninfo->activityId);

        }else{
            $activity=$this->sub_activity_model->get_best_match($scaninfo,$isWaiter);
        }
        if(!isset($activity)){
            $result->errcode=1;
        }

        //活动还未开始
        if(isset($activity->mainState)&&$activity->mainState==0){
            $result->errcode=1;
        }
            //活动已停用
        if(isset($activity->mainState)&&$activity->mainState==2){
            $result->errcode=1;
        }
            //活动还未开始
        if(isset($activity->state)&&$activity->state==0){
            $result->errcode=1;
        }
            //活动已停用
        if(isset($activity->state)&&$activity->state==2){
             $result->errcode=1;
        }
        if($result->errcode==1){
            $sql_3=<<<EOF

insert into rpt_user_daily(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,date(from_unixtime($scaninfo->scanTime)),1)
on duplicate key update scanCount=scanCount+1;

insert into rpt_user_weekly(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,(select if(DATE_FORMAT(FROM_UNIXTIME($scaninfo->scanTime),'%u')='00',DATE_FORMAT(CONCAT(year(date_sub(FROM_UNIXTIME($scaninfo->scanTime),interval 1 year)),'-12-31'),'%Y-%u'),DATE_FORMAT(FROM_UNIXTIME($scaninfo->scanTime),'%Y-%u'))),1)
on duplicate key update scanCount=scanCount+1;

insert into rpt_user_monthly(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,DATE_FORMAT(FROM_UNIXTIME($scaninfo->scanTime),'%Y-%m'),1)
on duplicate key update scanCount=scanCount+1;

insert into rpt_user_rank(proCode,cityCode,areaCode,mchId,userId,theDate,scanNum,batchId,productId)
values('$proCode','$cityCode','$areaCode',$scaninfo->mchId,$scaninfo->userId, date(from_unixtime($scaninfo->scanTime)),1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update scanNum=scanNum+1;
EOF;
            $this->dbhelper->trigger($sql_3);
        }

    }
    //在匹配到活动后再触发
    public function trigger_scan_log_activity_insert($scaninfo){
        $location=$this->getLocation($scaninfo->geoId);
        $batchinfo=$this->batch_model->get($scaninfo->batchId);
        $proCode=$location->proCode;
        $cityCode=$location->cityCode;
        $areaCode=$location->areaCode;
        $date=date('Y-m-d');
        $month=date('Y-m');
        $week=date('Y-W');
        $hour=date('h').':00';
        $sql=<<<EOF
insert into rpt_activity_evaluating(mchId,userId,activityId,categoryId,productId,batchId,theDate,scanCount)
values($scaninfo->mchId,$scaninfo->userId,$scaninfo->activityId,$batchinfo->categoryId,$batchinfo->productId,$scaninfo->batchId,'$date',1)
on duplicate key update scanCount=scanCount+1;
EOF;
        $this->dbhelper->trigger($sql);
    }
    //待实现
    public function trigger_scan_log_waiter_insert($scaninfo){
    }

    public function trigger_scan_log_waiter_update($scaninfo){
    }
    // user_packets插入触发器 add by cw
    public function trigger_user_redpacket_insert($scaninfo,$user_redpacket){
        //判断geoId
        //查询出geoId
        //$geoId=$this->db->query("select geoId from scan_log where code=?",[$code])->row()->geoId;
        //改用缓存处理
        $code=$user_redpacket->code;
        $batchinfo=$this->batch_model->get($scaninfo->batchId);
        // Added by shizq - begin
        // 如果是大转盘抽奖，根本就拿不到batchinfo
        if (! isset($batchinfo)) {
            $batchinfo = new stdClass();
            $batchinfo->productId = -1;
            $batchinfo->categoryId = -1;
        }
        // Added by shizq - end
        $mchId=$scaninfo->mchId;
        $userId=$scaninfo->userId;
        $geoId=$scaninfo->geoId;
        $activityId=$scaninfo->activityId;
        $location=$this->getLocation($geoId);
        $amount=$user_redpacket->amount;
        $proCode=$location->proCode;
        $cityCode=$location->cityCode;
        $areaCode=$location->areaCode;
        $date=date('Y-m-d');
        $month=date('Y-m');
        $week=date('Y-W');
        //此处不用担心sql注入，用heredoc语法，可读性更好
        $sql=<<<EOF
insert into rpt_user_daily(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,rpAmount)
values($mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,'$date',$amount)
on duplicate key update rpAmount=rpAmount+$amount;

insert into rpt_user_weekly(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,rpAmount)
values($mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,'$week',$amount)
on duplicate key update rpAmount=rpAmount+$amount;

insert into rpt_user_monthly(mchId,userId,proCode,cityCode,areaCode,batchId,productId,theDate,rpAmount)
values($mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,'$month',$amount)
on duplicate key update rpAmount=rpAmount+$amount;

insert into rpt_area_daily(mchId,userId,proCode,cityCode,areaCode,batchId,productId,date,redNum)
values($mchId,$userId,'$proCode','$cityCode','$areaCode',$scaninfo->batchId,$batchinfo->productId,'$date',$amount)
on duplicate key update redNum=redNum+$amount;

insert into rpt_activity_evaluating(mchId,userId,activityId,categoryId,productId,batchId,theDate,rpAmount)
values($mchId,$userId,$activityId,$batchinfo->categoryId,$batchinfo->productId,$scaninfo->batchId,'$date',$amount)
on duplicate key update rpAmount=rpAmount+$amount;

insert into rpt_activity_evaluating(mchId,userId,activityId,categoryId,productId,batchId,theDate)
values($mchId,$userId,$activityId,$batchinfo->categoryId,$batchinfo->productId,$scaninfo->batchId,'$date')
on duplicate key update rpNum=rpNum+1;


EOF;


        $this->dbhelper->trigger($sql);
    }
    // 提现触发器 add by cw
    public function trigger_trans($scaninfo,$amount){
        $date=date('Y-m-d');
        $month=date('Y-m');
        $week=date('Y-W');
        $sql=<<<EOF
insert into rpt_user_daily(mchId,userId,proCode,cityCode,areaCode,theDate,transAmount)
values($scaninfo->mchId,$scaninfo->userId,'000000','000000','000000','$date',$amount)
on duplicate key update transAmount=transAmount + ($amount);

insert into rpt_user_weekly(mchId,userId,proCode,cityCode,areaCode,theDate,transAmount)
values($scaninfo->mchId,$scaninfo->userId,'000000','000000','000000','$week',$amount)
on duplicate key update transAmount=transAmount + ($amount);

insert into rpt_user_monthly(mchId,userId,proCode,cityCode,areaCode,theDate,transAmount)
values($scaninfo->mchId,$scaninfo->userId,'000000','000000','000000','$month',$amount)
on duplicate key update transAmount=transAmount + ($amount);

insert into rpt_user_rank(mchId,userId,theDate,transAmount)
values($scaninfo->mchId,$scaninfo->userId,'$date',$amount)
on duplicate key update transAmount=transAmount + ($amount);
EOF;

        $this->dbhelper->trigger($sql);
    }
    // 积分使用触发器 add by cw
    public function trigger_point_used($pointInfo){
        $date=date('Y-m-d');
        $month=date('Y-m');
        $week=date('Y-W');

        $sql=<<<EOF
insert into rpt_user_daily(mchId,userId,proCode,cityCode,areaCode,theDate,pointUsed)values($pointInfo->mchId,$pointInfo->userId,'000000','000000','000000','$date',$pointInfo->amount)
on duplicate key update pointUsed=pointUsed+$pointInfo->amount;

insert into rpt_user_weekly(mchId,userId,proCode,cityCode,areaCode,theDate,pointUsed)values($pointInfo->mchId,$pointInfo->userId,'000000','000000','000000','$week',$pointInfo->amount)
on duplicate key update pointUsed=pointUsed+$pointInfo->amount;

insert into rpt_user_monthly(mchId,userId,proCode,cityCode,areaCode,theDate,pointUsed)values($pointInfo->mchId,$pointInfo->userId,'000000','000000','000000',$month,$pointInfo->amount)
on duplicate key update pointUsed=pointUsed+$pointInfo->amount;

insert into rpt_user_rank(mchId,userId,theDate,pointUsed)
values($pointInfo->mchId,$pointInfo->userId,'$date',$pointInfo->amount)
on duplicate key update pointUsed=pointUsed+$pointInfo->amount;
EOF;

        $this->dbhelper->trigger($sql);
    }
    // user_cards 触发器 add by cw
    public function trigger_user_cards($scaninfo,$user_card){
        if($user_card->role==0){
            //scanId此时取不到，只能通过code获取 --lishuliang
            $batchinfo=$this->batch_model->get($scaninfo->batchId);

            // Added by shizq - begin
            // 如果是大转盘抽奖，根本就拿不到batchinfo
            if (! isset($batchinfo)) {
                $batchinfo = new stdClass();
                $batchinfo->productId = -1;
            }
            // Added by shizq - end
            $location=$this->getLocation($scaninfo->geoId);
            $proCode=$location->proCode;
            $cityCode=$location->cityCode;
            $areaCode=$location->areaCode;
            $date=date('Y-m-d');
            $month=date('Y-m');
            $week=date('Y-W');

            $sql=<<<EOF
insert into rpt_user_daily(mchId,userId,proCode,cityCode,areaCode,theDate,cardCount,batchId,productId)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$date',1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update cardCount=cardCount+1;

insert into rpt_user_weekly(mchId,userId,proCode,cityCode,areaCode,theDate,cardCount,batchId,productId)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$week',1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update cardCount=cardCount+1;

insert into rpt_user_monthly(mchId,userId,proCode,cityCode,areaCode,theDate,cardCount,batchId,productId)
values($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$month',1,$scaninfo->batchId,$batchinfo->productId)
on duplicate key update cardCount=cardCount+1;
EOF;
            $this->dbhelper->trigger($sql);
        }
    }

    /**
     * 用户获得积分后触发
     *
     * @param array $params {
     *   mch_id,
     *   user_id,
     *   event_time,
     *   point_amount,
     *   batch_id
     * }
     * @return void
     */
    public function trigger_after_get_point($scaninfo,$user_point) {
        $location = $this->getLocation($scaninfo->geoId);
        $batchinfo=$this->batch_model->get($scaninfo->batchId);
        // Added by shizq - begin
        // 如果是大转盘抽奖，根本就拿不到batchinfo
        if (! isset($batchinfo)) {
            $batchinfo = new stdClass();
            $batchinfo->productId = -1;
        }
        // Added by shizq - end
        $proCode=$location->proCode;
        $cityCode=$location->cityCode;
        $areaCode=$location->areaCode;
        $date=date('Y-m-d');
        $month=date('Y-m');
        $week=date('Y-W');
        $hour=date('h').':00';
        $sql=<<<EOF
INSERT INTO rpt_user_daily (mchId, userId, proCode, cityCode, areaCode, theDate, pointAmount, batchId, productId)
VALUES ($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$date',$user_point->amount,$scaninfo->batchId,$batchinfo->productId)
ON duplicate key UPDATE pointAmount = pointAmount + $user_point->amount;

INSERT INTO rpt_user_weekly (mchId, userId, proCode, cityCode, areaCode, theDate, pointAmount, batchId, productId)
VALUES ($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$week',$user_point->amount,$scaninfo->batchId,$batchinfo->productId)
ON duplicate key UPDATE pointAmount = pointAmount + $user_point->amount;

INSERT INTO rpt_user_monthly (mchId, userId, proCode, cityCode, areaCode, theDate, pointAmount, batchId, productId)
VALUES ($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$month',$user_point->amount,$scaninfo->batchId,$batchinfo->productId)
ON duplicate key UPDATE pointAmount = pointAmount + $user_point->amount;

INSERT INTO rpt_area_daily (mchId, userId,proCode, cityCode, areaCode, date, pointAmount, batchId, productId)
VALUES ($scaninfo->mchId,$scaninfo->userId,'$proCode','$cityCode','$areaCode','$date',$user_point->amount,$scaninfo->batchId,$batchinfo->productId)
ON duplicate key UPDATE pointAmount = pointAmount + $user_point->amount;

insert into rpt_user_rank(mchId,userId,theDate,pointAmount)
values($scaninfo->mchId,$scaninfo->userId,'$date',$user_point->amount)
on duplicate key update pointAmount=pointAmount+$user_point->amount;
EOF;
            $this->dbhelper->trigger($sql);
    }

    /*
     * 根据扫码记录获取地理位置
     */
    private function getLocation($geoId) {
        if($geoId!=-1){
            $areaCodeRow=$this->db->query("select areaCode from geo_gps where id=?",[$geoId])->row();
            if(!isset($areaCodeRow)){
                $proCode='000000';
                $cityCode='000000';
                $areaCode='000000';
            }else{
                $areaCode=$areaCodeRow->areaCode;
                if($areaCode==''||strlen($areaCode)<6){
                        $areaCode='000000';
                }
                $proCode=substr($areaCode,0,2).'0000';
                $cityCode=substr($areaCode,0,4).'00';
            }
        }
        else{
            $proCode='000000';
            $cityCode='000000';
            $areaCode='000000';
        }
        $location =(object) [
            'areaCode' => $areaCode,
            'proCode'  => $proCode,
            'cityCode' => $cityCode,
        ];
        return $location;
    }

}
