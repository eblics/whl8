<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accumstrategy_model extends CI_Model {

	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

	function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('accum_strategies')->row();
    }
	function get_sub_by_pid($parentId){
        return $this->db->where('parentId',$parentId)->where('rowStatus',0)->get('accum_strategies_sub')->result();
    }
	function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('accum_strategies')->result();
    }
	function get_sub_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('accum_strategies_sub')->result();
    }
	function get_full_one($id){
        $row = $this->db->where('id',$id)->where('rowStatus',0)->get('accum_strategies')->row();
		if($row){
			$row->sublist=[];
			$row->sublist=$this->get_sub_by_pid($row->id);
		}
		return $row;
    }
	
    function save($saveData,$subData,&$insertId){
		// $this->db->trans_start();
		if(!empty($saveData->id)){
			$this->db->where('id',$saveData->id)->update('accum_strategies',$saveData);
			$this->db->where(['parentId'=>$saveData->id,'mchId'=>$subData->mchId])->delete('accum_strategies_sub');
			$insertId=$saveData->id;
		}else{
			$this->db->insert('accum_strategies',$saveData);
			$insertId=$this->db->insert_id();
		}
		$insertData=[];
		foreach ($subData->strategyType as $k => $v) {
			$insertData[$k]['parentId']=$insertId;
			$insertData[$k]['mchId']=$subData->mchId;
			$insertData[$k]['strategyType']=$v;
		}
		foreach ($subData->strategyId as $k => $v) {
			$insertData[$k]['strategyId']=$v;
		}
		foreach ($subData->start as $k => $v) {
			$insertData[$k]['start']=$v;
		}
		foreach ($subData->end as $k => $v) {
			$insertData[$k]['end']=$v;
		}
		$this->db->insert_batch('accum_strategies_sub',$insertData);
		return TRUE;
        // if($this->db->trans_status() === FALSE) {
        //     $this->db->trans_rollback();
        //     return FALSE;
        // }else{
        //     $this->db->trans_commit();
		// 	return TRUE;
        // }
    }
    function del($id){
		// $this->db->trans_start();
        $this->db->where('id',$id)->update('accum_strategies',['rowStatus'=>1]);
		$this->db->where('parentId',$id)->delete('accum_strategies_sub');
        // $this->db->where('accumStrategyId', $id)->update('accum_strategies', ['rowStatus' => 1]);
		return TRUE;
		// if ($this->db->trans_status() === FALSE) {
        //     $this->db->trans_rollback();
        //     return FALSE;
        // }else{
        //     $this->db->trans_commit();
		// 	return TRUE;
        // }
    }
	//获取组合策略详情（含子策略信息）
	function get_detail_by_pid($parentId,$mchId){
		$sql="
		select s.id id,s.start start,s.end end,s.strategyType stype,s.strategyId sid,r.name name 
				from accum_strategies_sub s 
				inner join red_packets r on s.mchId=r.mchId 
				where s.mchId=$mchId and s.parentId=$parentId and s.strategyType=0 && r.id=s.strategyId
		union all 
		select s.id id,s.start start,s.end end,s.strategyType stype,s.strategyId sid,c.title name 
				from accum_strategies_sub s 
				inner join cards_group c on s.mchId=c.mchId 
				where s.mchId=$mchId and s.parentId=$parentId and s.strategyType=2 && c.id=s.strategyId
		
		";
		log_message('debug','get_detail_by_pid sql: '.var_export($sql,TRUE));
        return $this->db->query($sql)->result();
    }

    function getBonusCountByStratetyId($strategyType, $strategyId) {

        $bonus = $this->db
        ->where('strategyType', $strategyType)
        ->where('strategyId', $strategyId)
        ->where('rowStatus', 0)
        ->get('accum_strategies_bonus')
        ->row();
        $accumStrategy = $this->db->where('id', $bonus->accumStrategyId)->where('rowStatus', 0)->get('accum_strategies')->row();
        if (! isset($accumStrategy)) {
            return NULL;
        }
        return $bonus;
    }

    /**
     * 获取累计策略关联的大奖设置
     *
     * @param $accumStrategyId 累计策略编号
     * @return array
     */
    function getBonusByAStrategyId($accumStrategyId) {
        return $this->db
        ->where('accumStrategyId', $accumStrategyId)
        ->where('rowStatus', 0)
        ->get('accum_strategies_bonus')
        ->result();
    }

    function getBonusById($bonusId) {
        return $this->db
        ->where('id', $bonusId)
        ->where('rowStatus', 0)
        ->get('accum_strategies_bonus')
        ->row();
    }

    /**
     * 保存累计策略的大奖
     *
     * @param $bonus 大奖数据
     * @return void
     */
    function saveBonus($bonus) {
        info('save bonus - begin');
        info('params: '. json_encode(func_get_args()));
        $this->db->trans_start();
        $idObjArr = $this->db->select('id')->where('accumStrategyId', $bonus['id'])->get('accum_strategies_bonus')->result();
        $ids = [];
        foreach ($idObjArr as $idObj) {
            $ids[] = $idObj->id;
        }
        if (! empty($ids)) {
            $success = $this->db->set('rowStatus', 1)->where_in('bonusId', $ids)->update('accum_strategies_bonus_plan');
            if (! $success) {
                error('delete from accum_strategies_bonus_plan fail.');
                $this->db->trans_rollback();
                throw new Exception("发生未知错误", 1);
            }
        }
        $success = $this->db->set('rowStatus', 1)->where('accumStrategyId', $bonus['id'])->update('accum_strategies_bonus');
        if (! $success) {
            error('delete from accum_strategies_bonus fail.');
            $this->db->trans_rollback();
            throw new Exception("发生未知错误", 1);
        }
        $bonusRows = [];
        for ($i = 0; $i < count($bonus['strategyId']); $i++) {
            $bonusRow = [];
            $bonusRow['accumStrategyId'] = $bonus['id'];
            $bonusRow['strategyType'] = $bonus['strategyType'][$i];
            $bonusRow['strategyId'] = $bonus['strategyId'][$i];
            $bonusRow['start'] = $bonus['start'][$i];
            $bonusRow['end'] = $bonus['end'][$i];
            $bonusRow['chance'] = $bonus['chance'][$i];
            if ($bonusRow['end'] - $bonusRow['start'] < $bonusRow['chance'] - 1) {
                throw new Exception("中奖次数必须小于等于扫码区间次数", 1);
            }
            if (! isset($bonusRow['strategyId']) || $bonusRow['strategyId'] == '') {
                throw new Exception("请选择子策略", 1);
            }
            $bonusRows[] = $bonusRow;
        }
        $success = $this->db->insert_batch('accum_strategies_bonus', $bonusRows);
        if (! $success) {
            error('insert into accum_strategies_bonus fail.');
            $this->db->trans_rollback();
            throw new Exception("发生未知错误", 1);
        }
        $this->db->trans_complete();
    }

    /**
     * 累计策略是否有大奖
     *
     * @param $accumStrategyId 累计策略编号
     * @return boolean
     */
    function hasBonus($accumStrategyId) {
        $bonusArr = $this->getBonusByAStrategyId($accumStrategyId);
        return ! empty($bonusArr);
    }

    /**
     * 尝试抽取大奖
     * @param $accumStrategyId 累计策略编号
     * @param $userId 用户编号
     * @param $scanNum 扫码次数
     * @return mixd
     */
    function tryBonus($accumStrategyId, $userId, $scanNum) {
        $bonus = $this->getBonusByAStrategyId($accumStrategyId);
        if (empty($bonus)) {
            throw new Exception("策略没有设置大奖", 1);
        }

        $redis = new Redis();
        $redisConfig = config_item('redis');
        $redis->pconnect($redisConfig['host'], $redisConfig['port']);
        if (isset($redisConfig['password'])) {
            $redis->auth($redisConfig['password']);
        }

        $bonusPlans = [];
        $bonusItemId = FALSE;
        // 查找该用户对应此大奖是否有记录生成
        foreach ($bonus as $bonusItem) {
            if ($bonusItem->chance == 0) {
                return FALSE;
            }
            $redisKey = 'bonus_plan_' . $bonusItem->id . '_' . $userId;
            $jsonStr = $redis->get($redisKey);
            if (! $jsonStr) {
                $sql = "SELECT bonusId, scanNum FROM accum_strategies_bonus_plan WHERE bonusId = ? AND userId = ? AND rowStatus = 0";
                $bonusPlanArr = $this->db->query($sql, [$bonusItem->id, $userId])->result();
            } else {
                $bonusPlanArr = json_decode($jsonStr)->data;
            }

            // 生成预设好的大奖对应该用户的扫码中得次数
            if (empty($bonusPlanArr)) {
                $randArr = [];
                while (TRUE) {
                    $randNum = mt_rand($bonusItem->start, $bonusItem->end);
                    if (! in_array($randNum, $randArr)) {
                        $randArr[] = $randNum;
                    }
                    if (count($randArr) == $bonusItem->chance) {
                        break;
                    }
                }
                foreach ($randArr as $rand) {
                    $plan = ['bonusId' => $bonusItem->id, 'userId' => $userId, 'scanNum' => $rand];
                    $bonusPlans[] = $plan;
                    if (intval($scanNum) === $rand) {
                        // 中得大奖
                        $bonusItemId = $bonusItem->id;
                    }
                }
            } else {
                foreach ($bonusPlanArr as $bonusPlan) {
                    if (intval($scanNum) === intval($bonusPlan->scanNum)) {
                        // 中得大奖
                        return $this->getBonusById($bonusPlan->bonusId);
                    } 
                }
                
            }
        }
        if (! empty($bonusPlans)) {
            $success = $this->db->insert_batch('accum_strategies_bonus_plan', $bonusPlans);
            if (! $success) {
                error('insert into accum_strategies_bonus fail.');
                $this->db->trans_rollback();
                throw new Exception("发生未知错误", 1);
            }
            foreach ($bonusPlans as $bonusPlanItem) {
                $redisKey = 'bonus_plan_' . $bonusPlanItem['bonusId'] . '_' . $userId;
                $redis->set($redisKey, json_encode(['data' => $bonusPlans]));
                $redis->expire($redisKey, 3600 * 24 * 7); // 有效期7天
            }
        }
        if ($bonusItemId) {
            return $this->getBonusById($bonusItemId);
        }
        return FALSE;

    }
	
}
