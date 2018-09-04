<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webapp_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    public function get($id){
        return $this->db->where('id',$id)->get('webApps')->row();
    }

    public function get_by_mchid($mchId){
        return $this->db->query("select * from webApps where mchId=$mchId or mchId=-1")->result();
    }
    public function get_webapp_rules($webappId,$mchId){
        return $this->db->where('webappId',$webappId)->where('mchId',$mchId)->get('webapp_rules')->row();
    }
    
    public function get_webapp_rules_log($userId,$code,$codeOwner){
        return $this->db->where('userId',$userId)->where('code',$code)->where('codeOwner',$codeOwner)->get('webapp_rules_log')->row();
    }
    
    public function add_webapp_rules_log($data){
        $this->db->insert('webapp_rules_log',$data);
        return $this->db->insert_id();
    }
    
    public function update_webapp_rules_log($data){
        $this->db->where('id',$data->id)->update('webapp_rules_log',$data);
        return $data->id;
    }
    
    public function add_webapp_rules_score($data){
        $this->db->insert('webapp_rules_score',$data);
        $insertId=$this->db->insert_id();
        if($insertId){
            $mchId=$data['mchId'];
            $webappId=$data['webappId'];
            $score=$data['score'];
            $userId=$data['userId'];
            $sql="INSERT INTO webapp_rules_score_account(mchId,webappId,score,userId) 
                VALUES($mchId,$webappId,$score,$userId) 
                ON DUPLICATE KEY UPDATE score=IFNULL(score,0)+$score";
            $this->db->query($sql);
            log_message('debug','webapp_rules_score_account sql:'.$sql);
        }
        return $insertId;
    }
    //游戏积分排行
    public function get_webapp_rules_score_top($mchId,$webappId){
        $sql="select a.userId id,b.nickName as nickName,a.score topScore from webapp_rules_score_account as a
            inner join users as b on b.id=a.userId 
            where a.mchId=$mchId and a.webappId=$webappId  
            order by a.score desc limit 10";
        log_message('debug','get_webapp_rules_score_top sql:'.$sql);
        return $this->db->query($sql)->result();
    }
    //游戏积分明细
    public function get_webapp_rules_score_list($mchId,$webappId,$userId){
        $sql="select * from webapp_rules_score where mchId=$mchId and webappId=$webappId and userId=$userId order by id desc";
        return $this->db->query($sql)->result();
    }
    //游戏积分最高分
    public function get_webapp_rules_score_minmax($activityIdArr){
        $activityIdStr=implode(',',$activityIdArr);
        $sql='select max(score) max,min(score) min from webapp_rules_score where activityId in ('.$activityIdStr.') ';
        log_message('debug','get_webapp_rules_score_minmax sql:'.$sql);
        return $this->db->query($sql)->row();
    }
    //获取某个用户当前扫码游戏得分排名
	function get_rank_by_user_id($mchId,$webappId,$userId){
        $sql="select count(id)+1 as rank from webapp_rules_score_account where mchId=$mchId and webappId=$webappId and score>ifnull((select score from webapp_rules_score_account where userId=$userId),0)";
        return $this->db->query($sql)->row();
	}
    //获取app配置
    public function get_webapp_config($webappId,$mchId){
        return $this->db->where('webappId',$webappId)->where('mchId',$mchId)->get('webapp_config')->row();
    }
    //保存app配置
    public function save_webapp_config($webappId,$mchId,$config){
        $row = $this->db->where('webappId',$webappId)->where('mchId',$mchId)->get('webapp_config')->row();
        if($row){
            return $this->db->where('webappId',$webappId)->where('mchId',$mchId)->update('webapp_config',['data'=>$config]);
        }else{
            $new = ['webappId'=>$webappId,'mchId'=>$mchId,'data'=>$config];
            $this->db->insert('webapp_config',$new);
            return $this->db->insert_id();
        }
    }

    public function getWebappPathForSubActivity($webAppId) {
        $this->checkWebappExists($webAppId);
        $result = $this->db->where('id', $webAppId)->select('appPath')->get('webApps')->row();
        return $result;
    }

    private function checkWebappExists($webAppId) {
        $row = $this->db->where('id', $webAppId)->get('webApps')->row();
        if (! isset($row)) {
            throw new Exception("H5应用不存在", 1);
        }
    }
}