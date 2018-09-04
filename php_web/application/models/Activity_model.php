<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('merchant_model');
    }

    public function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('activities')->row();
    }
    public function get_sub($id){
        return $this->db->where('id',$id)->get('sub_activities')->row();
    }
    function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('activities')->result();
    }
    public function get_sub_by_pid($pid){
        return $this->db->where('parentId',$pid)->where('rowStatus',0)->get('sub_activities')->result();
    }
    function get_sub_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('sub_activities')->result();
    }
    public function get_sub_by_h5($mchId,$h5id){
        return $this->db->where('mchId',$mchId)->where('webAppId',$h5id)->get('sub_activities')->result();
    }
    function add_activity($data){
        $this->db->insert('activities',$data);
        return $this->db->insert_id();
    }
    function update_activity($id,$data){
        return $this->db->where('id',$id)->update('activities',$data);
    }
    function del_activity($id){
        return $this->db->where('id',$id)->update('activities',['rowStatus'=>1]);
    }
    function add_sub_activity($data){
        $this->db->insert('sub_activities',$data);
        return $this->db->insert_id();
    }
    function add_sub_activityid_to_redpacket($id,$subid){
        // $this->db->trans_start();
        $this->db->where('subActivityId',$subid)->update('red_packets',['subActivityId'=>NULL]);
        $result = $this->db->where('id',$id)->update('red_packets',['subActivityId'=>$subid]);
        // $this->db->trans_complete();
        return $result;
    }
    function update_sub_activity($id,$data){
        return $this->db->where('id',$id)->update('sub_activities',$data);
    }
    function del_sub_activity($id){
        $result = $this->db->where('id',$id)->update('sub_activities',['rowStatus'=>1]);
        $this->db->where('subActivityId',$id)->update('red_packets',['subActivityId'=>NULL]);
        return $result;
    }
    function start_activity($id){
        return $this->db->where('id',$id)->update('activities',['state'=>1]);
    }
    function start_sub_activity($id){
        return $this->db->where('id',$id)->update('sub_activities',['state'=>1]);
    }
    function stop_activity($id){
        return $this->db->where('id',$id)->update('activities',['state'=>2]);
    }
    function stop_sub_activity($id){
        return $this->db->where('id',$id)->update('sub_activities',['state'=>2]);
    }
    function get_h5(){
        $mchId=$this->session->userdata('mchId');
        if($mchId==0){
            return $this->db->query("select * from webApps")->result();
        }
        return $this->db->query("select * from webApps where mchId=-1 or mchId=$mchId order by appName")->result();
    }
    public function get_area(){
        return $this->db->query('select code,name,level from areas order by code asc')->result();
    }
    
    //---------add by ccz 通过地址编码查找地域名
    public function get_area_byCode($code){
    	return $this->db->where('code',$code)->get('areas')->row();
    }
    //---------add by ccz 通过orderId查找order数据
    public function get_tts_order($orderId){
    	return $this->db->query("select id,orderNo from tts_orders where Id=$orderId ")->row();
    }
    public function get_tts_produce_order($mchId){
        return $this->db->query("select id,orderNo from tts_orders where mchId=$mchId and orderType='produce' order by id desc")->result();
    }
    public function get_tts_in_order($mchId){
        return $this->db->query("select id,orderNo from tts_orders where mchId=$mchId and orderType='in' order by id desc")->result();
    }
    public function get_tts_out_order($mchId){
        return $this->db->query("select id,orderNo from tts_orders where mchId=$mchId and  orderType='out' order by id desc")->result();
    }
    //--------------------------获取活动及其策略等详情 add by cw---------------//
	public function get_activity_policy($mchId,$id){
		$sql="select * from sub_activities where mchId=? and id=? and rowStatus=0";
		$data=$this->db->query($sql,[$mchId,$id])->row();
		// if(empty($data)){
		// 	exit('记录不存在！');
		// }
		//查询出策略
		switch ($data->activityType) {
			case 0://红包-查询红包策略
				$data->cont=$this->db->query("select * from red_packets where mchId=? and id=? and rowStatus=0",[$mchId,$data->detailId])->row();
				if($data->cont->levelType==1){
					$data->cont->subs=$this->db->query("select * from red_packets_sub where mchId=? and parentId=?",[$mchId,$data->cont->id])->result();
				}
                $policyName=$data->cont->name;
				break;
			case 1://欢乐币-查询欢乐币
				exit('不支持');
				break;
			case 2://乐券-查询乐券策略
				$data->cont=$this->db->query("select * from cards_group where mchId=? and id=? and rowStatus=0",[$mchId,$data->detailId])->row();
				$data->cont->subs=$this->db->query("select * from cards where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->id])->result();
                $policyName=$data->cont->title;
				break;
			case 3://组合策略-查询组合策略
				$data->cont=$this->db->query("select * from mix_strategies where mchId=? and id=? and rowStatus=0",[$mchId,$data->detailId])->row();
				$data->cont->subs=$this->db->query("select * from mix_strategies_sub where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->id])->result();
				for($i=0;$i<count($data->cont->subs);$i++){
					switch ($data->cont->subs[$i]->strategyType) {
						case 0://红包-查询红包策略
							$data->cont->subs[$i]->content=$this->db->query("select * from red_packets where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							if($data->cont->subs[$i]->content->levelType==1){
								$data->cont->subs[$i]->content->subs=$this->db->query("select * from red_packets_sub where mchId=? and parentId=?",[$mchId,$data->cont->subs[$i]->content->id])->result();
							}
							break;
						case 1://欢乐币-查询欢乐币
		
							break;
						case 2://乐券-查询乐券策略
							$data->cont->subs[$i]->content=$this->db->query("select * from cards_group where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							$data->cont->subs[$i]->content->subs=$this->db->query("select * from cards where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->content->id])->result();
							break;
		                case 3://积分-查询积分策略
							$data->cont->subs[$i]->content=$this->db->query("select * from points where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							$data->cont->subs[$i]->content->subs=$this->db->query("select * from points_sub where mchId=? and parentId=?",[$mchId,$data->cont->subs[$i]->content->id])->result();
							break;
						default:
							break;
					}
				}
                $policyName=$data->cont->name;
				break;
            case 4://积分-查询积分策略
				$data->cont=$this->db->query("select * from points where mchId=? and id=? and rowStatus=0",[$mchId,$data->detailId])->row();
				$data->cont->subs=$this->db->query("select * from points_sub where mchId=? and parentId=?",[$mchId,$data->cont->id])->result();
                $policyName=$data->cont->name;
				break;
            case 5://叠加策略-查询叠加策略
				$data->cont=$this->db->query("select * from multi_strategies where mchId=? and id=? and rowStatus=0",[$mchId,$data->detailId])->row();
				$data->cont->subs=$this->db->query("select * from multi_strategies_sub where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->id])->result();
				for($i=0;$i<count($data->cont->subs);$i++){
					switch ($data->cont->subs[$i]->strategyType) {
						case 0://红包-查询红包策略
							$data->cont->subs[$i]->content=$this->db->query("select * from red_packets where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							if($data->cont->subs[$i]->content->levelType==1){
								$data->cont->subs[$i]->content->subs=$this->db->query("select * from red_packets_sub where mchId=? and parentId=?",[$mchId,$data->cont->subs[$i]->content->id])->result();
							}
							break;
						case 1://欢乐币-查询欢乐币
							break;
						case 2://乐券-查询乐券策略
							$data->cont->subs[$i]->content=$this->db->query("select * from cards_group where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							$data->cont->subs[$i]->content->subs=$this->db->query("select * from cards where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->content->id])->result();
							break;
		                case 3://积分-查询积分策略
							$data->cont->subs[$i]->content=$this->db->query("select * from points where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							$data->cont->subs[$i]->content->subs=$this->db->query("select * from points_sub where mchId=? and parentId=?",[$mchId,$data->cont->subs[$i]->content->id])->result();
							break;
						default:
							break;
					}
				}
                $policyName=$data->cont->name;
				break;
            case 6://累计策略-查询累计策略
				$data->cont=$this->db->query("select * from accum_strategies where mchId=? and id=? and rowStatus=0",[$mchId,$data->detailId])->row();
				$data->cont->subs=$this->db->query("select * from accum_strategies_sub where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->id])->result();
				for($i=0;$i<count($data->cont->subs);$i++){
					switch ($data->cont->subs[$i]->strategyType) {
						case 0://红包-查询红包策略
							$data->cont->subs[$i]->content=$this->db->query("select * from red_packets where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							if($data->cont->subs[$i]->content->levelType==1){
								$data->cont->subs[$i]->content->subs=$this->db->query("select * from red_packets_sub where mchId=? and parentId=?",[$mchId,$data->cont->subs[$i]->content->id])->result();
							}
							break;
						case 1://欢乐币-查询欢乐币
		
							break;
						case 2://乐券-查询乐券策略
							$data->cont->subs[$i]->content=$this->db->query("select * from cards_group where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							$data->cont->subs[$i]->content->subs=$this->db->query("select * from cards where mchId=? and parentId=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->content->id])->result();
							break;
		                case 3://积分-查询积分策略
							$data->cont->subs[$i]->content=$this->db->query("select * from points where mchId=? and id=? and rowStatus=0",[$mchId,$data->cont->subs[$i]->strategyId])->row();
							$data->cont->subs[$i]->content->subs=$this->db->query("select * from points_sub where mchId=? and parentId=?",[$mchId,$data->cont->subs[$i]->content->id])->result();
							break;
						default:
							break;
					}
				}
                $policyName=$data->cont->name;
				break;
			default:
				;
				break;
		}

        $result=array(
                'data'=>$data,
                'policyName'=>$policyName,
                'policyLevel'=>$data->activityType
            );
		return $result;
	}
    public function get_activity_log($mchId,$activityId,$time){
        $sql="select id from activity_log where mchId=? and activityId=? and theTime=?";
        return $this->db->query($sql,[$mchId,$activityId,$time])->row();
    }

    public function update_activity_log($id,$data){
        return $this->db->where('id',$id)->update('activity_log',$data);
    }
	public function add_activity_log($data){
		$this->db->insert('activity_log', $data);
		return $this->db->insert_id();
	}

     //--------------------------活动评估结束 add by cw---------------//
    //根据活动查询关联的乐码->查询出关联的产品类别+产品
    public function get_cate_product($mchId,$id){
        $sql="select a.id,b.productId,b.categoryId,a.batchId from sub_activities a inner join batchs b on a.batchId=b.id where a.mchId=? and a.id=?;";
        return $this->db->query($sql,[$mchId,$id])->row_array();
    }

    /**
     * 获取乐码对应的组合策略（Added by shizq）
     * 
     * @param   $lecode 乐码
     * @return  array
     */
    function getMixstrategy($lecode) {
        $scan_history = $this->db->where('code', $lecode)->get('scan_log')->row();
        if (! $scan_history) {
            error("Can not find scan history where code is: $lecode");
            throw new Exception("找不到扫码记录", 1);
        }
        if (! $scan_history->activityId) {
            error("No activity matched with lecode: $lecode");
            throw new Exception("没有匹配到活动", 1);
        }
        $sub_activity = $this->db
            ->where('id', $scan_history->activityId)
            ->where('rowStatus', 0)
            ->get('sub_activities')
            ->row();
        if (! $sub_activity) {
            error("SubActivity is not exists which id is: $scan_history->activityId");
            throw new Exception("发生未知错误", 1);
        }
        if ($sub_activity->activityType != 3) {
            error("The type of subActivity is not support for truntable: $sub_activity->activityType");
            throw new Exception("此策略不支持大转盘抽奖", 1);
        }
        $mix_strategy = $this->db
            ->where('id', $sub_activity->detailId)
            ->where('rowStatus', 0)
            ->get('mix_strategies')
            ->row();
        if (! $mix_strategy) {
            error("Mix_strategy does not exists which id is: $sub_activity->detailId");
            throw new Exception("策略不存在", 1);
        }
        $mix_strategies = $this->db
            ->where('parentId', $mix_strategy->id)
            ->where('rowStatus', 0)
            ->get('mix_strategies_sub')
            ->result();
        if (! $mix_strategies) {
            error("Mix_strategy does not have any children which id is: $sub_activity->detailId");
            throw new Exception("缺少策略项目", 1);
        }
        foreach ($mix_strategies as &$mix_strategy) {
            // 获取对应的红包名称
            if ($mix_strategy->strategyType == 0) {
                $red_packet = $this->db
                    ->where('id', $mix_strategy->strategyId)
                    ->get('red_packets')
                    ->row();
                if (! $red_packet) {
                    error("Red_packet does not exists which id is: $mix_strategy->strategyId");
                    throw new Exception("转盘数据不存在", 1);
                }
                $mix_strategy->name = $red_packet->name;
            }
            // 获取对应的卡券名称
            if ($mix_strategy->strategyType == 2) {
                $card = $this->db
                    ->where('id', $mix_strategy->strategyId)
                    ->get('cards_group')
                    ->row();
                if (! $card) {
                    error("Card does not exists which id is: $mix_strategy->strategyId");
                    throw new Exception("转盘数据不存在", 1);
                }
                $mix_strategy->name = $card->title;
            }
            // 获取对应的积分名称
            if ($mix_strategy->strategyType == 3) {
                $card = $this->db
                    ->where('id', $mix_strategy->strategyId)
                    ->get('points')
                    ->row();
                if (! $card) {
                    error("Card does not exists which id is: $mix_strategy->strategyId");
                    throw new Exception("转盘数据不存在", 1);
                }
                $mix_strategy->name = $card->name;
            }
        }
        return $mix_strategies;
    }

    public function getSubActivityPreview($subActivityId) {
        if (isProd()) {
            $mchId = 173;
        } else {
            $mchId = 0;
        }
        $sql = 'SELECT t3.weight, t5.amount FROM sub_activities t1 
            JOIN mix_strategies t2 ON t2.id = t1.detailId 
            JOIN mix_strategies_sub t3 ON t3.parentId = t2.id 
            JOIN points t4 ON t4.id = t3.strategyId
            JOIN points_sub t5 ON t5.parentId = t4.id
            WHERE t1.activityType = 3 AND t3.strategyType = 3 AND t1.mchId = ? AND t1.id = ?';
        $resultSet = $this->db->query($sql, [$mchId, $subActivityId])->result();
        if (empty($resultSet)) {
            return [];
        }
        return $resultSet;
    }

}
