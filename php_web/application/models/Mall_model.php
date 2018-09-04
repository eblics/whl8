<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mall_model extends CI_Model {

    const VIRAL_PLATFORM_MCH_PAY = 1;

    const VIRAL_PLATFORM_RED_PACKET = 2;

	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
        /**
     * 获取所有分类
     *@mallId 商城ID
     */
    public function get_category($mallId) {
        return $this->db->where('mallId',$mallId)->get('mall_categories')->result();
    }
     /**
     * 获取单个分类
     *@id 分类ID
     */
    public function get_category_by_id($id) {
        return $this->db->where('id',$id)->get('mall_categories')->row();
    }
    /**
     * 添加分类
     *@data 数据
     */
    public function add_category($data) {
        $res = $this->db->insert('mall_categories',$data);
        if(!$res){
            throw new Exception('插入失败！');
        }else{
            return $this->db->insert_id();
        }
    }
        /**
     * 查询分类子分类
     *@id 数据id
     */
    public function sub_category_num($id) {
        return $this->db->where('parentCategoryId',$id)->count_all_results('mall_categories');
    }

    /**
     * 修改分类
     *@where 数据
     *@data 数据
     */
    public function update_category($where,$data) {
        return $this->db->where($where)->update('mall_categories',$data);
    }
    /**
     * 获取分类下产品
     *@id 分类ID
     */
    public function get_by_category($id) {
        return $this->db->where('categoryId',$id)->where('rowStatus',0)->get('mall_goods')->result();
    }
    /**
     * 删除分类
     *@id 数据id
     */
    public function del_category($id) {
        return $this->db->delete('mall_categories',['id'=>$id]);
    }
    /**
     * 新增商城
     */
    public function insert_mall($mchId,$data){
        $res = $this->db->where('mchId',$mchId)->get('malls')->result();
        if($res){
            throw new Exception('企业商城已经存在！');
        }else{
            $this->db->insert('malls',$data);
            return $this->db->insert_id();
        }
    }
    /**
     * 更新商城信息
     */
    public function update_mall($mchId,$data){
        $res = $this->db->where('mchId',$mchId)->update('malls',$data);
        if($res){
            return $res;
        }else{
            throw new Exception('更新失败！');
        }
    }
    /**
     * 获取企业所关联的商城信息
     */
    public function get_mall($mchId){
        return $this->db->where('mchId',$mchId)->get('malls')->row();
    }
    /**
     * 获取单个商品
     * @id 商品ID
     */
    public function getinfo_by_id($id){
    	return $this->db->where('id',$id)->get('mall_goods')->result();
    }
    /**
     * 初始 根据mchId查mallId
     */
    public function get_mallId_by_mchId($mchId){
    	return $this->db->where('mchId',$mchId)->get('malls')->row();
    }
    /**
     * 根据商品id查mallId
     */
    public function get_mallId_by_id($id){
    	return $this->db->where('id',$id)->get('mall_goods')->row();
    }
    /**
     * 根据订单ID查订单    订单ID查mallId
     */
    public function get_by_id($id){
        return $this->db->where('id',$id)->get('mall_orders')->row();
    }
    /**
      * 商品中根据mallId查mchId
     */
    public function get_mchId($mallId) {
    	return $this->db->where('id',$mallId)->get('malls')->row();
    }
    /**
     * 根据mchId查mallId
     */
    public function get_mallId($mchId) {
    	return $this->db->where('mchId',$mchId)->get('malls')->row();
    }
    /**
     * 查询所有的商城code
     */
    public function get_mallCode(){
        $sql = "select mallCode from malls";
        return $this->db->query($sql)->result();
    }
    /**
     * 新商品插入
     */
    public function insert_good($data) {
    	$this->db->insert('mall_goods',$data);
        return $this->db->insert_id();
    }
    /**
     * 商品更新
     */
    public function update_good($id,$data) {
    	return $this->db->where('id',$id)->update('mall_goods',$data);
    }
    /**
     *  删除图片
     */
    public function delete_img($goodsId) {
        return $this->db->where('goodsId',$goodsId)->delete('mall_goods_images');
    }
    /**
     * 插入图片
     */
    public function update_img($goodsId,$path,$default){
        $this->db->insert('mall_goods_images',['goodsId'=>$goodsId,'path'=>$path,'default'=>$default]);
    }
    /**
     * 商品更新(删除)
     */
    public function update_delete($id) {
    	$data = array('rowStatus'=>1);
    	return $this->db->where('id',$id)->update('mall_goods',$data);
    }
    /**
     * 请求商品列表
     */
    public function get_goods($mallId, $option = FALSE){
        if ($option) {
            $sql = "select FROM_UNIXTIME(m.createTime,'%Y-%m-%d %H:%i:%s') createTime,c.name name,m.oPrice,m.price,m.id,m.description,m.goodsName,m.exchangeType,m.viralAmount,m.rowStatus,i.path path from mall_goods m join mall_goods_images i on m.id=i.goodsId and m.rowStatus=0 and m.exchangeType = 1 and i.`default`=1 and m.mallId=$mallId left join mall_categories c on c.id=m.categoryId order by m.id desc";
        } else {
           $sql = "select FROM_UNIXTIME(m.createTime,'%Y-%m-%d %H:%i:%s') createTime,c.name name,m.oPrice,m.price,m.id,m.description,m.goodsName,m.exchangeType,m.viralAmount,m.rowStatus,i.path path from mall_goods m join mall_goods_images i on m.id=i.goodsId and m.rowStatus=0 and i.`default`=1 and m.mallId=$mallId left join mall_categories c on c.id=m.categoryId order by m.id desc";
        }
        // $sql = "select FROM_UNIXTIME(m.createTime,'%Y-%m-%d %H:%i:%s') createTime,m.oPrice,m.price,m.id,m.description,m.goodsName,m.rowStatus,i.path path from mall_goods m join mall_goods_images i on m.id=i.goodsId and i.`default`=1 and mallId=$mallId and m.rowStatus=0 order by m.id desc";
    	return $this->db->query($sql)->result();
    	// return $this->db->where('mallId',$mallId)->order_by('id','desc')->get('mall_goods')->result();
    }
    /**
     * 请求商品图片数据
     */
    public function get_images($goodId){
    	return $this->db->where('goodsId',$goodId)->where('rowStatus',0)->get('mall_goods_images')->result();
    }
    /**
     * 请求订单数据
     *
     */
    public function get_orders($mallId){
        // $sql = "select o.createTime ctime,g.id,g.goodsName,g.userId,g.orderId,i.path from mall_orders_goods g inner JOIN mall_orders o join mall_goods_images i on  g.orderId=o.id  and i.goodsId=g.id and g.mallId=$mallId and g.rowStatus=0";
        $sql = "select o.addressText address,o.createTime ctime,o.amount amount,o.orderNum ordernum,o.status ostatus,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and o.payStatus=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId order by ctime desc";
        return $this->db->query($sql)->result();
    }
    /**
     * 请求订单 (查询所有时间)
     */
    public function get_orders_all($mallId,$status){
        if($status == 1){
            $payStatus = 0;
            $shippingStatus = 0;
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and o.payStatus=1 order by ctime desc";
        }
        if($status == 0){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and o.payStatus=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId order by ctime desc";
        }
        if($status == 2){
            $shippingStatus = 0;
            $payStatus = 1;
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId order by ctime desc";
        }
        if($status == 3){
            $shippingStatus = 1;
            $payStatus = 1;
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and o.mallId=$mallId order by ctime desc";
        }
        if($status == 4){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 0;
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 and o.status=$s JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and o.mallId=$mallId order by ctime desc";
        }
        if($status == 5){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 1;
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 and o.status=$s JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and o.mallId=$mallId order by ctime desc";
        }
        return $this->db->query($sql)->result();
    }
    /**
     * 请求订单 (查询今天)
     */
    public function get_orders_today($mallId, $status, $today) {
        //0 全部,1 未付款,2 未发货,3 已发货,4 已收货,5 已完成
        if($status == 0){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.payStatus=1 and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')='$today' order by ctime desc";
        }
        if($status == 1){
            $shippingStatus = 0;
            $payStatus = 0;
            $s = 0;
        }
        if($status == 2){
            $shippingStatus = 0;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 3){
            $shippingStatus = 1;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 4){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 5){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 1;
        }
        if($status == 1 || $status == 2 || $status ==3 || $status == 4 || $status == 5){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 and o.status=$s JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')='$today' order by ctime desc";
        }
        return $this->db->query($sql)->result();
    }
    /**
     * 请求订单 (查询昨天)
     */
    public function get_orders_tdby($mallId, $status, $today, $tdby){
        //0 全部,1 未付款,2 未发货,3 已发货,4 已完成
        if($status == 0){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and o.payStatus=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')<'$today' and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')>'$tdby' order by ctime desc";
        }
        if($status == 1){
            $shippingStatus = 0;
            $payStatus = 0;
            $s = 0;
        }
        if($status == 2){
            $shippingStatus = 0;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 3){
            $shippingStatus = 1;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 4){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 5){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 1;
        }
        if($status == 1 || $status == 2 || $status ==3 || $status == 4 || $status == 5){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 and o.status=$s JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')<'$today' and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')>'$tdby' order by ctime desc";
        }
        return $this->db->query($sql)->result();
    }
    /**
     * 请求订单 (7天)
     */
    public function get_orders_seven($mallId, $status, $oneday) {
        if($status == 0){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.payStatus=1 and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')>'$oneday' order by ctime desc";
        }
        if($status == 1){
            $shippingStatus = 0;
            $payStatus = 0;
            $s = 0;
        }
        if($status == 2){
            $shippingStatus = 0;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 3){
            $shippingStatus = 1;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 4){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 5){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 1;
        }
        if($status == 1 || $status == 2 || $status ==3 || $status == 4 || $status == 5){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 and o.status=$s JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')>'$oneday' order by ctime desc"; 
        }
        return $this->db->query($sql)->result();
    }
    /**
     * 请求订单 (查询自定义)
     */
    public function get_orders_diy($mallId, $status, $t1, $t2){
        if($status ==0){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and o.payStatus=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')<='$t2' and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')>='$t1' order by ctime desc";
        }
        if($status == 1){
            $shippingStatus = 0;
            $payStatus = 0;
            $s = 0;
        }
        if($status == 2){
            $shippingStatus = 0;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 3){
            $shippingStatus = 1;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 4){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 0;
        }
        if($status == 5){
            $shippingStatus = 2;
            $payStatus = 1;
            $s = 1;
        }
        if($status == 1 || $status == 2 || $status ==3 || $status == 4 || $status == 5){
            $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,g.cardName gcname,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId and o.shippingStatus=$shippingStatus and o.payStatus=1 and o.status=$s JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and o.mallId=$mallId and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')<='$t2' and FROM_UNIXTIME(o.createTime,'%Y-%m-%d')>='$t1' order by ctime desc";
        }
        return $this->db->query($sql)->result();
    }
    /**
     * 订单备注,发货
     */
    public function remark($mallId, $id, $reMark = NULL){
        $res = $this->db->where('id',$id)
            ->where('rowStatus !=', 1)
            ->get('mall_orders')->row();
        if (! isset($res)) {
            throw new Exception('订单不存在');
        }
        if($res->shippingStatus == 1){
            throw new Exception('已经发货，无需再操作');
        }

        // ----------------------------------
        // Added by shizq - begin
        $orderGoods = $this->db->where('orderId', $id)->get('mall_orders_goods')->row();
        if (! isset($orderGoods)) {
            throw new Exception('订单不存在', 1);
        }
        if ($orderGoods->isViral) {
            $this->load->model('User_model', 'user');
            $user = $this->user->get($orderGoods->userId);
            $params = [
                'mchId'  => $user->mchId,
                'userId' => $user->id,
                'openid' => $user->openid,
                'amount' => $orderGoods->viralAmount * $orderGoods->goodsNumber,
                'desc'   => '现金兑换',
                'action' => 2
            ];

            if (count(config_item('sender')) <= intval($orderGoods->viralPlatform)) {
                debug("goods viralPlatform is: $orderGoods->viralPlatform");
                throw new Exception("此乐券无法兑换", 1);
            }
            $senderName = config_item('sender')[$orderGoods->viralPlatform];
            $classz = new ReflectionClass($senderName);
            $sender = $classz->newInstance();
            $insertId = $sender->requestThirdPlatform($params);
            return $this->db->where('id',$id)->where('mallId',$mallId)->update('mall_orders',['reMark' => '', 'shippingStatus' => 2, 'status' => 1]);
        } else {
            return $this->db->where('id',$id)->where('mallId',$mallId)->update('mall_orders',['reMark'=>$reMark,'shippingStatus'=>1]);
        }
        // Added by shizq - end
    }
	/**
     * 确认收货
     */
    public function confirm_get($id){
        $status = $this->db->where('id',$id)->get('mall_orders')->row();
        if($status->shippingStatus != 1){
            throw new Exception('订单状态不正确！');
        }
        if($status->shippingStatus == 1){
            $res = $this->db->where('id',$id)->update('mall_orders',['shippingStatus'=>2]);
            if($res){
                return true;
            }else{
                throw new Exception('确认收货失败！');
            }
        }
    }

     /**
     * 完成订单
     */
    public function end_order($id){
        $status = $this->db->where('id',$id)->get('mall_orders')->row();
        if($status->status == 1){
            throw new Exception('订单已完成，无需重复操作！');
        }
        if($status->status == 0){
            $res = $this->db->where('id',$id)->update('mall_orders',['status'=>1]);
            if($res){
                return true;
            }else{
                throw new Exception("完成订单失败！");
            }
        }
    }
    /**
     * 搜索订单
     */
    public function get_search_order($orderNum,$mallId){
        // $res = $this->db->like('orderNum',$orderNum)->where('mallId',$mallId)->where('rowStatus',0)->get('mall_orders')->result();
        $sql = "select o.addressText address,o.createTime ctime,o.status ostatus,o.amount amount,o.orderNum ordernum,FROM_UNIXTIME(o.createTime,'%Y-%m-%d %H:%i:%s') utime,g.goodsName gname,g.goodsNumber gnumber,o.id oid,g.goodsPrice gprice,i.path path,o.shippingStatus estatus,o.payStatus paystatus from mall_orders o join mall_orders_goods g on o.id=g.orderId JOIN mall_goods_images i on i.`default`=1 and i.goodsId=g.goodsId and o.rowStatus=0 and ordernum like '%$orderNum%' and o.mallId=$mallId order by ctime desc";
        $res = $this->db->query($sql)->result();
        // if($res){
            return $res;
        // }else{
        //     throw new Exception('未查询到符合条件的订单！');
        // }
    }

    public function request_third_platform($userId, $viralAmount, $viralPlatform) {
        $this->load->model('User_model', 'user');
        $user = $this->user->get($userId);
        $params = [
            'mchId'  => $user->mchId,
            'userId' => $user->id,
            'openid' => $user->openid,
            'amount' => $viralAmount,
            'desc'   => '现金兑换',
            'action' => 2
        ];
        if (count(config_item('sender')) <= intval($viralPlatform)) {
            debug("viralPlatform is: $viralPlatform");
            throw new Exception("此乐券无法兑换", 1);
        }
        $senderName = config_item('sender')[$viralPlatform];
        $classz = new ReflectionClass($senderName);
        $sender = $classz->newInstance();
        $respObj = $sender->requestThirdPlatform($params);
        return $respObj;
    }
}
