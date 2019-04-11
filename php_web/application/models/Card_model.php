<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Card_model extends CI_Model {

    var $redis;
	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->redis=new Redis();
        $this->redis->pconnect($this->config->item('redis')['host'],$this->config->item('redis')['port']);
        if(isset($this->config->item('redis')['password'])){
            $this->redis->auth($this->config->item('redis')['password']);
        }
    }

	/**
	 * 获取所有乐券
	 *@mchId 商户ID
	 */
    public function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('cards')->result();
    }
    /**
     * 获取券组信息 生成数组
     */
    public function get_group($mchId){
    	return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('cards_group')->result();
    }
	/**
	 * 添加卡片
	 *@data 数据
	 */
	public function add_card($data) {
		$time = time();
		$data['createTime'] = $time;
		$data['updateTime'] = $time;
		$this->db->insert('cards',$data);
		return $this->db->insert_id();
	}
	//获取单条
	function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('cards')->row();
    }
    //获取是否存在该ID的活动
    function exists_cid($mchId,$cid){
    	return $this->db->where('mchId',$mchId)->where('rowStatus',0)->where('activityType',2)->where('detailId',$cid)->get('sub_activities')->result();
    }
    //查找乐券关联的活动(不包括停用)
    function exists_id($mchId,$cid){
    	return $this->db->where('mchId',$mchId)->where('rowStatus',0)->where('state',1)->where('activityType',2)->where('detailId',$cid)->get('sub_activities')->result();
    }
    /**
	 * 删除乐券 假删除 只是改变状态
	 *@id 数据id
	 */
	public function del_card($id) {
		// return $this->db->delete('cards',['id'=>$id]);
		return $this->db->where('id',$id)->update('cards',['rowStatus'=>1]);
	}
    /**
     * 通过券组ID获取单条券组信息
     * @ $cid 为card_group id简写
     */
    public function getgroup_by_cid($cid){
    	return $this->db->where('id',$cid)->where('rowStatus',0)->get('cards_group')->row();
    }
	/**
	 * 修改卡片
	 *@where 数据
	 *@data 数据
	 */
	public function update_card($id,$data) {
		// ======================== Added by shizq start ==========================
		/* $time = time();
		$sql = "INSERT INTO cards_tactics (cardId, probability, startTime, endTime)
				SELECT id, probability, updateTime, ?
				FROM cards
				WHERE id = ?";
		$this->db->query($sql, [$time, $id]);
		$data['updateTime'] = $time; */
		// ======================== Added by shizq end   ==========================
        $card_sub_key="cards.remainNum.id.$id";
        $this->redis->zAdd('limit_zone',$data['remainNum'],$card_sub_key);
		return $this->db->where('id',$id)->where('rowStatus',0)->update('cards',$data);
	}
	/**
	 * 获取券组信息
	 *@where 数据
	 */
	public function get_cardgroup($id){
		return $this->db->where('id',$id)->where('rowStatus',0)->get('cards_group')->row();
	}
	/**
	 * 添加券组
	 *@data 数据
	 */
	public function add_group($data) {
		$this->db->insert('cards_group',$data);
		return $this->db->insert_id();
	}
	/**
	 * 保存券组信息
	 *@id 数据id
	 *@data 数据
	 */
	public function save_group($id,$data){
		return $this->db->where('id',$id)->update('cards_group',$data);
	}
	/**
	 * 删除券组 假删除,只改变状态
	 *@id 数据id
	 */
	public function del_group($id){
		// return $this->db->delete('cards_group',['id'=>$id]);
		return $this->db->where('id',$id)->update('cards_group',['rowStatus'=>1]);
	}
	/**
	 * 查询是否存在子券
	 *@id 券组id
	 */
	public function exists_card($id,$mchId){
		return $this->db->where('parentId',$id)->where('rowStatus',0)->where('mchId',$mchId)->get('cards')->row();
	}
    /**
	 * 获取单个卡片
	 *@id 卡片ID
	 */
	public function get_by_id($id,$mchId) {
		return $this->db->where('id',$id)->where('rowStatus',0)->where('mchId',$mchId)->get('cards')->row();
	}
	/**
	 * 获取乐券策略中奖列表
	 */
	public function get_winlist($mchId,$id,&$count=0,$start=null,$length=null){
		$count=$this->db->query("select count(*) cnt from user_cards a where cardId=? and a.role=0 and transId=-1 order by a.getTime desc",[$id])->row()->cnt;
		$sql="select ifnull(u.nickName,'红码用户') nickName,u.realName,u.mobile,u.address,m.* from(
select a.userId,a.id as aid,a.processing as aprocessing,a.sended,FROM_UNIXTIME(a.getTime) as date,ifnull(g.address,'终端不允许获取') as area from user_cards a
left join scan_log s on a.code=s.code
left join geo_gps g on s.geoId=g.id
where a.cardId=? and a.role=0 and a.transId=-1 order by a.getTime desc";
		$data=[$id];
    	if(isset($start)&&isset($length)){
    		$sql.=' limit ?,?';
    		$data[]=intval($start);
    		$data[]=intval($length);
    	}
    	$sql.=')m left join users u on u.id=m.userId and u.mchId=?;';
    	$data[]=$mchId;
        return $this->db->query($sql,$data)->result_array();

	}
	//处理中奖
	public function deal_with($id){
		$this->db->where('id',$id)->update('user_cards',['processing'=>1]);
		return $this->db->affected_rows();
	}


    public function cardHolderList($cardId, $mchId, $page, $pageSize = 10) {
        $query = [intval($cardId), intval($mchId), intval($cardId), intval($mchId), intval($cardId), intval($mchId)];
        
        // 查询总数量
        $sql = "
            SELECT count(*) AS total_num FROM user_cards_account t1 
            INNER JOIN users    t2 ON t2.id = t1.userId AND t1.role = 0
            INNER JOIN cards    t3 ON t3.id = t1.cardId
            WHERE t1.cardId = ? AND t1.mchId = ? AND t1.num > 0
            UNION ALL 
            SELECT count(*) AS total_num FROM user_cards_account t1 
            INNER JOIN waiters  t2 ON t2.id = t1.userId AND t1.role = 1 
            INNER JOIN cards    t3 ON t3.id = t1.cardId
            WHERE t1.cardId = ? AND t1.mchId = ? AND t1.num > 0
            UNION ALL 
            SELECT count(*) AS total_num FROM user_cards_account t1 
            INNER JOIN salesman t2 ON t2.id = t1.userId AND t1.role = 2
            INNER JOIN cards    t3 ON t3.id = t1.cardId
            WHERE t1.cardId = ? AND t1.mchId = ? AND t1.num > 0";
        $numResult = $this->db->query($sql, $query)->result();

        $query[] = intval($page);
        $query[] = intval($pageSize);

        // 查询具体数据
        $sql = "
            SELECT t1.role, t1.userId AS user_id, t2.nickName AS nickname, t2.realName AS realname, t2.mobile, 
            t3.title AS card_name, t1.num FROM user_cards_account t1 
            INNER JOIN users    t2 ON t2.id = t1.userId AND t1.role = 0
            INNER JOIN cards    t3 ON t3.id = t1.cardId
            WHERE t1.cardId = ? AND t1.mchId = ? AND t1.num > 0
            UNION ALL 
            SELECT t1.role, t1.userId AS user_id, t2.nickName AS nickname, t2.realName AS realname, t2.mobile, 
            t3.title AS card_name, t1.num FROM user_cards_account t1 
            INNER JOIN waiters  t2 ON t2.id = t1.userId AND t1.role = 1 
            INNER JOIN cards    t3 ON t3.id = t1.cardId
            WHERE t1.cardId = ? AND t1.mchId = ? AND t1.num > 0
            UNION ALL 
            SELECT t1.role, t1.userId AS user_id, t2.nickName AS nickname, t2.realName AS realname, t2.mobile, 
            t3.title AS card_name, t1.num FROM user_cards_account t1 
            INNER JOIN salesman t2 ON t2.id = t1.userId AND t1.role = 2
            INNER JOIN cards    t3 ON t3.id = t1.cardId
            WHERE t1.cardId = ? AND t1.mchId = ? AND t1.num > 0 LIMIT ?, ?";
        $dataList = $this->db->query($sql, $query)->result();
        foreach ($dataList as $dataItem) {
            if ($dataItem->role === 0 || $dataItem->role === '0') {
                $dataItem->role_str = '消费者';
            } else if ($dataItem->role == 1) {
                $dataItem->role_str = '服务员';
            } else if ($dataItem->role == 2) {
                $dataItem->role_str = '业务员';
            } else {
                $dataItem->role_str = '未知角色';
            }

            if (! $dataItem->realname) {
                $dataItem->realname = '未设置';
            }

            if (! $dataItem->nickname) {
                $dataItem->nickname = '未设置';
            }

            if (! $dataItem->mobile) {
                $dataItem->mobile = '未设置';
            }
        }

        return ['total_num_obj' => $numResult, 'data_list' => $dataList];
    }
    
    /**
     * 获取某个卡组下的所有卡券
     * @param int $groupId
     * @return array
     */
    public function getUserGroupCards($groupId, $userId) {
    	$sql = "SELECT t1.id, t1.title, t1.cardType, t1.pointQuantity, ifnull(t2.num, 0) num
    		FROM cards t1 lEFT JOIN user_cards_account t2 ON t2.cardId = t1.id AND t2.userId = ? AND t2.role = 0
    		WHERE t1.parentId = ? AND t1.rowStatus = 0";
    	$resultSet = $this->db->query($sql, [$userId, $groupId])->result();
    	return $resultSet;
    }
}
