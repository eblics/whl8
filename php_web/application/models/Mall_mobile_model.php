<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mall_mobile_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mall_model','mallm');
        $this->load->model('trigger_model');
    }

    // public function mallid_to_mchid($mallid) {
    //     $result=$this->db->query('select mchid from malls where id=?',[$mallid])->row();
    //     if (! isset($result)) {
    //         return NULL;
    //     }
    //     return $result->mchid;
    // }
    public function getMallByMchId($mchId) {
        return $this->db->where('mchId', $mchId)->get('malls')->row();
    }

    public function get_mall($mallid) {
        $result=$this->db->query('select mchid,name from malls where id=?',[$mallid])->result_array();
        if(count($result)==0){
            return null;
        }
        return $result[0];
    }

    public function openid_to_userid($openid) {
        $result=$this->db->query('select id from users where openid=?',[$openid])->result_array();
        if(count($result)==0){
            return null;
        }
        return $result[0]['id'];
    }

    public function get_point($mallid,$userid) {
        $result=$this->db->query('select amount from user_points_accounts a join malls m on a.mchId=m.mchId where m.id=? and userId=?',[$mallid,$userid])->result_array();
        if(count($result)==0){
            return 0;
        }
        return intval($result[0]['amount']);
    }

    //public function get_goods_list($mallid,$ids,$pageindex,$pagesize,$order,$condition) {
    //
    //}

    public function get_recommand_goods($mallid) {
        $sql='select g.id,goodsName,price,path from mall_goods g left join mall_goods_images i on g.id=i.goodsid and i.rowStatus=0 and i.default=1 where mallId=? and g.exchangeType=0 and g.rowStatus=0 order by createtime desc limit 4';
        return $this->db->query($sql,[$mallid])->result_array();
    }

    public function get_goods_list($mallid,$ids=null,$categoryId=null) {
        $sql='select g.id,goodsName,price,path,isViral from mall_goods g left join mall_goods_images i on g.id=i.goodsid and i.rowStatus=0 and i.default=1 where mallId=? and g.exchangeType=0 and g.rowStatus=0';
        if($categoryId!=null){
            $goodsInfo=$this->db->query($sql.' and g.categoryId=?',[$mallid,$categoryId])->result_array();
        }else if($ids==null){
            $goodsInfo=$this->db->query($sql,[$mallid])->result_array();
        }
        else{
            $goodsInfo=$this->db->query($sql.' and g.id in ?',[$mallid,$ids])->result_array();
        }
        return $goodsInfo;
    }

    public function get_categories_list($mallid) {
        $sql='select id,name from mall_categories c where mallId=? and exists(select 1 from mall_goods g where g.rowStatus=0 and g.categoryId=c.id)';
        $categoriesInfo=$this->db->query($sql,[$mallid])->result_array();
        return $categoriesInfo;
    }

    public function get_orders_list($mallid,$userid,$status) {
        $sql='select o.id,orderNum,amount,shippingStatus,payStatus,from_unixtime(createTime) createTime,g.goodsName,goodsPrice,goodsNumber,cardName,path from mall_orders o join mall_orders_goods g on o.id=g.orderId
            left join mall_goods_images i on g.goodsId=i.goodsId and i.default=1
            where o.mallId=? and o.userId=? and status=? and o.rowStatus=0 order by createTime desc';
        return $this->db->query($sql,[$mallid,$userid,$status])->result_array();
    }

    public function get_addresses_list($mallid,$userid) {
        $sql='select d.id,receiver,phoneNum,a.fullName area,address,isDefault from mall_addresses d left join areas a on d.areaCode=a.code where d.rowStatus=0 and mallId=? and userId=?';
        $result=$this->db->query($sql,[$mallid,$userid])->result_array();
        return $result;
    }

    public function get_address($mallid,$userid,$id=null) {
        if($id!=null){
            $sql='select d.id,receiver,phoneNum,a.fullName area,address,areaCode,level from mall_addresses d left join areas a on d.areaCode=a.code where d.rowStatus=0 and mallId=? and userId=? and d.id=?';
            $result=$this->db->query($sql,[$mallid,$userid,$id])->result_array();
            if(count($result)==0){
                return null;
            }
            return $result[0];
        }else{
            $sql='select d.id,receiver,phoneNum,a.fullName area,address,areaCode,level from mall_addresses d left join areas a on d.areaCode=a.code where d.rowStatus=0 and mallId=? and userId=? and isDefault=1';
            $result=$this->db->query($sql,[$mallid,$userid])->result_array();
            if(count($result)==0){
                return null;
            }
            return $result[0];
        }
    }
    public function get_good($mallid,$id) {
        $sql='select goodsName,price,description,isViral from mall_goods where rowStatus=0 and mallId=? and id=?';
        $data=$this->db->query($sql,[$mallid,$id])->result_array();
        if(count($data)==0){
            return null;
        }
        $result=$data[0];
        $sql='select path from mall_goods_images where goodsId=? and rowStatus=0 order by id,`default` desc';
        $data=$this->db->query($sql,[$id])->result_array();
        $result['images']=[];
        for($i=0;$i<count($data);$i++){
            $result['images'][]=$data[$i]['path'];
        }
        return $result;
    }

    public function create_order_for_card($mallid,$userid,$goodsObject,$addressid) {
        if($addressid==null){
            throw new Exception('未填写收货地址');
        }
        $sql='select d.id,receiver,phoneNum,a.fullName area,address from mall_addresses d left join areas a on d.areaCode=a.code where d.rowStatus=0 and mallId=? and userId=? and d.id=?';
        $result=$this->db->query($sql,[$mallid,$userid,$addressid])->result_array();
        if(count($result)==0){
            throw new Exception('收货地址不存在');
        }
        $address=$result[0];
        $addressId=$address['id'];
        $addressText=$address['receiver'].'|'.$address['phoneNum'].'|'.$address['area'].'|'.$address['address'];

        $changetime = date('ymdHis',time());
        $array = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for($i=0;$i<4;$i++) {
            $code.=$array[mt_rand(0,strlen($array)-1)];
        }
        $orderNum = $changetime.$code;

        $this->db->query('insert into mall_orders(mallId,userId,orderNum,createTime,addressId,addressText,payStatus)values(?,?,?,unix_timestamp(),?,?,1)',[$mallid,$userid,$orderNum,$addressId,$addressText]);
        $orderid=$this->db->insert_id();

        $value=[];
        $value[]=$mallid;
        $value[]=$userid;
        $value[]=$orderid;
        $value[]=$goodsObject['id'];
        $value[]=$goodsObject['goodsName'];
        $value[]=$goodsObject['amount'];
        $value[]=$goodsObject['cardId'];
        $value[]=$goodsObject['cardName'];
        $value[]=$goodsObject['isViral'];
        $value[]=$goodsObject['viralPlatform'];
        $value[]=$goodsObject['viralAmount'];

        $this->db->query('insert into mall_orders_goods(mallId,userId,orderId,goodsId,goodsName,goodsNumber,cardId,cardName,isViral,viralPlatform,viralAmount) values (?,?,?,?,?,?,?,?,?,?,?)',$value);
    }

    public function create_order($mallid,$userid,$goodsAmount,$addressid) {
    //连事务都没用，虽然积分商城用的不多，但这是要疯啊   --lishuliang
        $userPoint=0;
        $point=$this->db->query('select amount,p.mchid,role from user_points_accounts p join malls m on p.mchid=m.mchid and m.id=? where userid=?',[$mallid,$userid])->result_array();
        if(count($point)!=0)
            $userPoint=intval($point[0]['amount']);

        $goodIds=[];
        foreach($goodsAmount as $good){
            $goodIds[]=$good['id'];
        }
        $goodsInfo=$this->db->query('select id,goodsName,oPrice,price,isViral,viralPlatform,viralAmount,createOrder from mall_goods where mallId=? and id in ?',[$mallid,$goodIds])->result_array();
        $amount=0;
        $createOrder=false;
        $hasRealGoods=false;
        foreach($goodsInfo as $good){
            $id=$good['id'];
            foreach($goodsAmount as $goodAmount){
                if($goodAmount['id']==$id){
                    $amount+=$good['price']*$goodAmount['amount'];
                    if($good['createOrder']==1)
                        $createOrder=true;
                    if($good['isViral']==0){
                        $hasRealGoods=true;
                        $createOrder=true;
                    }
                    break;
                }
            }
        }
        if($userPoint<$amount){
            return '账户积分不够';
        }
        if($hasRealGoods && $addressid==null){
            return '未填写收货地址';
        }

        $changetime = date('ymdHis',time());
        $array = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for($i=0;$i<4;$i++) {
            $code.=$array[mt_rand(0,strlen($array)-1)];
        }
        $orderNum = $changetime.$code;

        $data=[
            'mallId'=>$mallid,
            'userId'=>$userid,
            'orderNum'=>$orderNum,
            'amount'=>$amount,
            'payStatus'=>1
        ];

        if($hasRealGoods){
            $sql='select d.id,receiver,phoneNum,a.fullName area,address from mall_addresses d left join areas a on d.areaCode=a.code where d.rowStatus=0 and mallId=? and userId=? and d.id=?';
            $result=$this->db->query($sql,[$mallid,$userid,$addressid])->result_array();
            if(count($result)==0){
                return '收货地址不存在';
            }
            $address=$result[0];
            $addressId=$address['id'];
            $addressText=$address['receiver'].'|'.$address['phoneNum'].'|'.$address['area'].'|'.$address['address'];

            $data['addressId']=$addressId;
            $data['addressText']=$addressText;
        }
        $this->db->set('createTime', 'unix_timestamp()', false);
        $this->db->insert('mall_orders',$data);
        $orderid=$this->db->insert_id();

        $values=[];
        $sql='';
        foreach($goodsInfo as $good){
            $value=[];
            $value[]=$mallid;
            $value[]=$userid;
            $value[]=$orderid;
            $value[]=$good['id'];
            $value[]=$good['goodsName'];
            $value[]=$good['oPrice'];
            $value[]=$good['price'];
            foreach($goodsAmount as $goodAmount){
                if($goodAmount['id']==$good['id']){
                    $value[]=$goodAmount['amount'];
                    break;
                }
            }
            $value[]=$good['isViral'];
            $value[]=$good['viralPlatform'];
            $value[]=$good['viralAmount'];
            $values[]=$value;
            $sql.='?,';
        }
        $this->db->query('insert into mall_orders_goods(mallId,userId,orderId,goodsId,goodsName,goodsOprice,goodsPrice,goodsNumber,isViral,viralPlatform,viralAmount) values '.substr($sql,0,-1),$values);

        if(!$createOrder){
            try{
                $this->mallm->request_third_platform($userid,$goodsInfo[0]['viralAmount'],$goodsInfo[0]['viralPlatform']);
                $this->reduce_points($amount,$userid,$point,$orderid);
                $this->db->where('id',$orderid)->update('mall_orders',['shippingStatus'=>2,'status'=>1]);
                return 1;
            }
            catch(Exception $ex){
                $this->db->where('id',$orderid)->update('mall_orders',['rowStatus'=>1]);
                return $ex->getMessage();
            }
        }
        else{
            $this->reduce_points($amount,$userid,$point,$orderid);
        }
    }

    /*public function delete_order($mallid,$userid,$orderid) {
        $this->db->where('mallid',$mallid)->where('userid',$userid)->where('id',$orderid)->update('mall_orders',['rowStatus'=>'1']);
    }*/

    private function reduce_points($amount,$userid,$point,$orderid){
        if($amount!=0){
            $this->db->query('update user_points_accounts set amount=amount-? where userid=? and mchid=?',[$amount,$userid,$point[0]['mchid']]);
            $data=[
                'userId'=>$userid,
                'mchId'=>$point[0]['mchid'],
                'doTable'=>'mall_orders',
                'doId'=>$orderid,
                'amount'=>$amount,
                'role'=>$point[0]['role']
            ];
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('user_points_used',$data);
        }
    }

    function get_areacode_info() {
        $areas=$this->db->query('SELECT code,name,level from areas where type=0 or type=1 order by code')->result();
        $result=[];
        foreach ($areas as $area) {
            $level=intval($area->level);
            if($level==0){
                $result[]=['code'=>$area->code,'name'=>$area->name,'children'=>[]];
            }
            else if($level==1){
                $result[count($result)-1]['children'][]=['code'=>$area->code,'name'=>$area->name,'children'=>[]];
            }
            else{
                $index=count($result[count($result)-1]['children'])-1;
                $result[count($result)-1]['children'][$index]['children'][]=['code'=>$area->code,'name'=>$area->name];
            }
        }
        return $result;
    }

    public function get_point_history() {

    }

    public function update_address($mallid,$userid,$data,$id=null) {
        if($id!=null){
            $this->db->where('mallid',$mallid)->where('userid',$userid)->where('id',$id)->update('mall_addresses',$data);
        }
        else{
            $data['mallId']=$mallid;
            $data['userId']=$userid;
            $this->db->insert('mall_addresses',$data);
        }
    }

    public function default_address($mallid,$userid,$id=null){
        $this->db->where('mallid',$mallid)->where('userid',$userid)->update('mall_addresses',['isDefault'=>0]);
        if($id!=null){
            $this->db->where('mallid',$mallid)->where('userid',$userid)->where('id',$id)->update('mall_addresses',['isDefault'=>1]);
        }
    }
}
