<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Multistrategy_model extends CI_Model {

	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

	function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('multi_strategies')->row();
    }
	function get_sub_by_pid($parentId){
        return $this->db->where('parentId',$parentId)->where('rowStatus',0)->get('multi_strategies_sub')->result();
    }
	function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('multi_strategies')->result();
    }
	function get_sub_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('multi_strategies_sub')->result();
    }
	function get_full_one($id){
        $row = $this->db->where('id',$id)->where('rowStatus',0)->get('multi_strategies')->row();
		if($row){
			$row->sublist=[];
			$row->sublist=$this->get_sub_by_pid($row->id);
		}
		return $row;
    }
	
    function save($saveData,$subData,&$insertId){
		// $this->db->trans_start();
		if(!empty($saveData->id)){
			$this->db->where('id',$saveData->id)->update('multi_strategies',$saveData);
			$this->db->where(['parentId'=>$saveData->id,'mchId'=>$subData->mchId])->delete('multi_strategies_sub');
			$insertId=$saveData->id;
		}else{
			$this->db->insert('multi_strategies',$saveData);
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
		foreach ($subData->weight as $k => $v) {
			$insertData[$k]['weight']=$v;
		}
		$this->db->insert_batch('multi_strategies_sub',$insertData);
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
        $this->db->where('id',$id)->update('multi_strategies',['rowStatus'=>1]);
		$this->db->where('parentId',$id)->delete('multi_strategies_sub');
		return TRUE;
		// if ($this->db->trans_status() === FALSE) {
        //     $this->db->trans_rollback();
        //     return FALSE;
        // }else{
        //     $this->db->trans_commit();
		// 	return TRUE;
        // }
    }
	//获取叠加策略详情（含子策略信息）
	function get_detail_by_pid($parentId,$mchId){
		$sql="
		select s.id id,s.weight weight,s.strategyType stype,s.strategyId sid,r.name name 
				from multi_strategies_sub s 
				inner join red_packets r on s.mchId=r.mchId 
				where s.mchId=$mchId and s.parentId=$parentId and s.strategyType=0 && r.id=s.strategyId
		union all 
		select s.id id,s.weight weight,s.strategyType stype,s.strategyId sid,c.title name 
				from multi_strategies_sub s 
				inner join cards_group c on s.mchId=c.mchId 
				where s.mchId=$mchId and s.parentId=$parentId and s.strategyType=2 && c.id=s.strategyId
		
		";
		log_message('debug','get_detail_by_pid sql: '.var_export($sql,TRUE));
        return $this->db->query($sql)->result();
    }
	
	
}
