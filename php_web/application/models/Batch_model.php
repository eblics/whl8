<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('merchant_model','merchant');
        $this->load->model('code_version_model');
    }
    function get_sq_no($mch_id){
        return $this->db->query("SELECT count(*) num FROM batchs WHERE mchId=$mch_id")->row()->num;
    }
    function get_by_de_code($codeinfo){
        $merchant=$this->merchant->get_by_code($codeinfo->mch_code);
        return $this->db
            ->where('mchId',$merchant->id)
            ->where('versionNum',$codeinfo->version)
            ->where('start<=',$codeinfo->value)
            ->where('end>=',$codeinfo->value)
            ->get('batchs')->row();
    }

    function get_by_en_code($code){
        $codeinfo=hls_decode($code);
        return $this->get_by_de_code($codeinfo->result);
    }
    function get_by_value($mch_id,$v){
        if ($mch_id == '' || $mch_id === NULL || ! isset($mch_id)) {
            return NULL;
        }
        $sql="SELECT * FROM batchs WHERE mchId=$mch_id AND start<=$v AND end>=$v";
        return $this->db->query($sql)->row();
    }

    function get_mch_batchs_bak($mch_id){
        return $this->db->where('mchId',$mch_id)->order_by('id','desc')->get('batchs')->result();
    }
    //查询出批次乐码，并显示已扫次数
    function get_mch_batchs($mch_id, $state = NULL){
        $where = [$mch_id];
        $sql = "SELECT t1.*, t2.name AS category_name, t3.name AS product_name, t3.id AS product_id FROM batchs t1 
                LEFT JOIN categories t2 ON t2.id = t1.categoryId 
                LEFT JOIN products   t3 ON t3.id = t1.productId
                WHERE t1.mchId = ? ";
        if (isset($state) && in_array(intval($state), [0, 1, 2])) {
            $where[] = $state;
            $sql .= "AND t1.state = ? ";
        }
        $sql .= "ORDER BY id DESC";
    	return $this->db->query($sql, $where)->result();
    }
    //查询某个批次的码被扫的次数
    function get_mch_batchs_scannum($batchId){
    	return $this->db->query("SELECT count(id) as scanNum,id batchId FROM scan_log WHERE batchId=$batchId")->row();
    }
    
    function get_mch_batchs_enable($mch_id){
        return $this->db->where('mchId',$mch_id)->where('state',1)->order_by('id','desc')->get('batchs')->result();
    }
    function get($id){
        return $this->get_by_id($id);
    }
    function get_by_id($id){
        return $this->db->where('id',$id)->get('batchs')->row();
    }
    function get_by_no($batchNo){
        return $this->db->where('batchNo',$batchNo)->where('mchId',$this->session->userdata('mchId'))->get('batchs')->row();
    }

    function get_mch_max_num($mch_id){
        return $this->db->query("select max(end) max_num from batchs where mchId=$mch_id")->row()->max_num;
    }

    function add_batch($data){
        $this->db->insert('batchs',$data);
        return $this->db->insert_id();
    }
    function update_batch($id,$data){
        return $this->db->where('id',$id)->update('batchs',$data);
    }
    function del_batch($id){
        return $this->db->where('id',$id)->update('batchs',['rowStatus'=>1]);
    }
    function start_batch($id,$activeTime = null){
        return $this->db->where('id',$id)->update('batchs',['state'=>1,'activeTime'=>$activeTime,'stopTime'=>null]);//激活状态和激活时间，增加时间参数--ccz 16-03-31
    }
    function stop_batch($id,$stopTime = null){
        return $this->db->where('id',$id)->update('batchs',['state'=>2,'stopTime'=>$stopTime,'activeTime'=>null]); //停用状态和停用时间,增加时间参数--ccz 16-03-31
    }
    
    function get_batch_order_list($mchId,$start,$length,$type) {
        $typeSql='';
        $typeParams=null;
        if($type=='out'){
            $typeSql=' and orderType=?';
            $typeParams='out';
        }
        else{
            $typeSql=' and orderType in ?';
            $typeParams=['in','produce'];
        }
        $sql='select count(*) count from tts_orders where mchId=?'.$typeSql;
        $count=$this->db->query($sql,[$mchId,$typeParams])->row()->count;
        $sql='select id,orderNo,productCode,productName,orderType,date(FROM_UNIXTIME(orderTime)) orderTime,FROM_UNIXTIME(putTime) putTime,processStatus from tts_orders where mchId=?'.$typeSql.' order by id desc limit ?,?';
        $data=$this->db->query($sql,[$mchId,$typeParams,$start,$length])->result_array();
        return ['data'=>$data,'count'=>$count];
    }
    
    function get_batch_order_scan($orderId) {
        $sql='select count(*) count from tts_orders_codes where orderId=?';
        $sum=$this->db->query($sql,[$orderId])->row()->count;
        $sql='select count(*) count from tts_orders_codes where orderId=? and isScan=1';
        $scaned=$this->db->query($sql,[$orderId])->row()->count;
        return ['sum'=>$sum,'scaned'=>$scaned];
    }
    
    function get_app_id_secret($mchId) {
        $sql='select appid,appsecret from tts_apps where mchId=?';
        $data=$this->db->query($sql,[$mchId])->result_array();
        if(count($data)!=0)
            return $data[0];
        return ['appid'=>'','appsecret'=>''];
    }
    
    function get_batch_order_code($id) {
        $sql='select pubCode from tts_orders_codes where orderId=?';
        $data=$this->db->query($sql,[$id])->result();
        foreach ($data as $row)
        {
            echo $row->pubCode."\r\n";
        }
    }
    
    function get_batch_order_detail($mchId,$id) {
        $sql='select orderNo,orderType,factoryCode,factoryName,date(FROM_UNIXTIME(produceTime)) produceTime,shelfLifeStr,date(FROM_UNIXTIME(expireTime)) expireTime,productCode,productName,saleToCode,saleToName,saleToAgc,date(FROM_UNIXTIME(orderTime)) orderTime,FROM_UNIXTIME(putTime) putTime,processStatus from tts_orders where mchId=? and id=?';
        $data=$this->db->query($sql,[$mchId,$id])->result();
        if(count($data)!=0)
            return $data[0];
        return [];
    }
    
    function get_batch_order_errmsg($mchId,$id) {
        $sql='select errmsg from tts_orders where mchId=? and id=?';
        $data=$this->db->query($sql,[$mchId,$id])->row();
        echo $data->errmsg;
    }
    
    function batch_order_delete($mchId,$id) {
        $data=$this->db->query('SELECT count(*) ct FROM sub_activities where rowStatus=0 and mchId=? and (prodInOrderId=? or outOrderId=?)',[$mchId,$id,$id])->row()->ct;
        if(intval($data)!=0)
            return '此订单已被活动关联，不能被删除';
        $this->db->delete('tts_orders', ['mchId' => $mchId,'id' => $id]);
        $this->db->delete('tts_orders_codes', ['orderId' => $id]);
    }
    
    function batch_order_exists_orderno($mchId,$orderno) {
        $sql='select count(*) ct from tts_orders where mchId=? and orderno=?';
        $data=$this->db->query($sql,[$mchId,$orderno])->row()->ct;
        return intval($data);
    }
    
    function batch_set_downloaded($id){
        $this->db->where('id',$id)->update('batchs',['isDownloaded'=>1]);
    }

    //查询出该商户已申请总码数 add by cw @2017-07-26
    public function get_batch_num_total($mchId){
        return $this->db->select_sum('len')->get_where('batchs',array('mchId'=>$mchId))->row()->len;
    }

    /**
     * 获取乐码批次
     * 
     * @param $mchId 商户编号
     * @param $value value
     * @return object
     */
    public function getBatchByValue($currentUser, $value, $code) {
        $mchId = $currentUser->mchId;
        $batch = $this->get_by_value($mchId, $value);
        if (! isset($batch)) {
            throw new Exception("此乐码没有申请", 110102);
        }
        if ($batch->rowStatus == 1) {
            throw new Exception("此码已停用", 100106);
        }
        if ($batch->expireTime < time()) {
            throw new Exception("此码已过期", 100103);
        }
        if ($batch->state == 0) {
            throw new Exception("此码未激活", 110104);
        }
        if ($batch->state == 2) {
            throw new Exception("此码已停用", 100105);
        }
        return $batch;
    }

    public function getBatchByValueWithoutException($params, $value) {
        $mchId = $params->mchId;
        $batch = $this->get_by_value($mchId, $value);
        if (! isset($batch)) {
            throw new Exception("此乐码没有申请", 110102);
        }
        if ($batch->state == 2) {
            throw new Exception("此码已停用", 100105);
        }
        return $batch;
    }
}
