<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop_model extends CI_Model {

    function get_tag_data($mchId){
        $sql = "select id,name from shop_mch_tags where mchId=? order by id desc";
        $shops = $this->db->query($sql,[$mchId])->result_array();
        return $shops;
    }
    
    function get_tag_detail($id,$mchId){
        $sql = "select name from shop_mch_tags where id=?";
        $name = $this->db->query($sql,[$id])->result_array();
        if(count($name)!=0){
            $name=$name[0]['name'];
        }
        else{
            $name='';
        }
        $sql = "select s.name,m.shopId,t.tagId from shops s join shop_mch m on s.id=m.shopId and m.mchId=?
left join shop_tag t on t.shopId=m.shopId and t.tagId=?";
        $shops = $this->db->query($sql,[$mchId,$id])->result_array();
        return ['name'=>$name,'shop'=>$shops];
    }
    
    function delete_tag_data($id){
        $this->db->delete('shop_mch_tags',['id'=>$id]);
        $this->db->delete('shop_tag',['tagId'=>$id]);
    }
    
    function post_tag_data($id,$data,$mchId,$shopIds){
        if($id==0){
            if($this->db->query('select count(id) ct from shop_mch_tags where name=? and mchId=?',[$data['name'],$mchId])->row()->ct!=0)
                return ['errcode'=>1,'errmsg'=>'标签名称已经存在'];
            $this->db->insert('shop_mch_tags',$data);
            $id=$this->db->insert_id();
        }
        else{
            if($this->db->query('select count(id) ct from shop_mch_tags where name=? and mchId=? and id!=?',[$data['name'],$mchId,$id])->row()->ct!=0)
                return ['errcode'=>1,'errmsg'=>'标签名称已经存在'];
            $this->db->update('shop_mch_tags',$data,['id'=>$id]);
        }
        
        $this->db->delete('shop_tag',['tagId'=>$id]);
        if(count($shopIds)!=0){
            $data=[];
            for($i=0;$i<count($shopIds);$i++){
                $arr=['shopId'=>$shopIds[$i],'tagId'=>$id];
                $data[]=$arr;
            }
            $this->db->insert_batch('shop_tag',$data);
        }
        return ['errcode'=>0];
    }
    
    function get_shop_list($openid,$type=1){
        $sql = "select id,name,'区域' area,state,address,ownerName,ownerPhoneNum,date(FROM_UNIXTIME(createTime)) createTime from shops where openid=? and type=? ORDER BY createTime desc";
        $shops = $this->db->query($sql,[$openid,$type])->result_array();
        return $shops;
    }
    
    function get_shop_detail($id){
        $sql = "select s.id,s.name,s.areaCode,s.lat,s.lng,s.address,s.areaLen,ownerName,ownerPhoneNum,fullName city from shops s left join areas a on s.areaCode=a.code where s.id=?";
        $shops = $this->db->query($sql,[$id])->result_array();
        if(count($shops)!=0)
            $shops=$shops[0];
        return $shops;
    }
    
    function validate_owner($data){
        $sql = 'select count(*) ct from shops where type=2 and state=1 and ownerName=? and ownerPhoneNum=? and id=?';
        return $this->db->query($sql,[$data['ownerName'],$data['ownerPhoneNum'],$data['id']])->row()->ct;
    }
    
    function validate_shop($id){
        $sql = 'select count(*) ct from shops where type=2 and state=1 and id=?';
        return $this->db->query($sql,[$id])->row()->ct;
    }
    
    function post_gps_shop_data($id,$data,$openid,$state=0){
        if($id==0){
            $data['openid']=$openid;
            $data['state']=$state;
            $data['type']=1;
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('shops',$data);
            $id=$this->db->insert_id();
        }
        else{
            $data['state']=$state;
            $this->db->update('shops',$data,"id=$id and openid='$openid' and type=1 and (state=0 or state=3)");
        }
    }
    
    function activate_bluetooth_shop($id,$data){
        $data['state']=2;
        $this->db->update('shops',$data,"id=$id and type=2 and state=1");
    }
    
    function revoke_shop($id){
        $this->db->update('shops',['state'=>0],"id=$id and type=1 and state=1");
    }
    
    function delete_shop($id){
        $this->db->delete('shops',['id'=>$id,'state=0']);
    }
    
    function get_address_from_areacode($areaCode){
        $sql = "select fullName from areas where code=?";
        $address = $this->db->query($sql,[$areaCode])->row();
        if($address!=null){
            $address=$address->fullName;
        }
        return $address;
    }
}