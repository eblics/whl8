<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OShop_model extends CI_Model {

    function get_mch($mch_id) {
        return $this->db->where('id', $mch_id)->get('merchants')->row();
    }
    
    function get_shop_data(){
        $sql = "select s.id,s.name,a.fullName area,address,ownerName,ownerPhoneNum,date(FROM_UNIXTIME(createTime)) createTime,state from shops s left join areas a on s.areaCode=a.code where s.type=2 ORDER BY createTime desc";
        $shops = $this->db->query($sql)->result();
        return $shops;
    }
    
    function get_examine_data(){
        $sql = "select id,name,'区域' area,address,ownerName,ownerPhoneNum,date(FROM_UNIXTIME(createTime)) createTime,state from shops where type=1 and (state=1 or state=2) ORDER BY createTime desc";
        $shops = $this->db->query($sql)->result();
        return $shops;
    }
    
    function get_examine_detail($id){
        $sql = "select name,address,ownerName,ownerPhoneNum,date(FROM_UNIXTIME(createTime)) createTime,state from shops where id=? and type=1";
        $examines = $this->db->query($sql,[$id])->result_array();
        if(count($examines)!=0){
            $examines=$examines[0];
        }
        return $examines;
    }
    
    function agree_examine($id){
        $this->db->update('shops',['state'=>2],['id'=>$id,'type'=>1,'state'=>1]);
    }
    
    function reject_examine($id){
        $this->db->update('shops',['state'=>3],['id'=>$id,'type'=>1,'state'=>1]);
    }
    
    function get_shop_detail($id){
        $sql = "select name,address,ownerName,ownerPhoneNum from shops where id=?";
        $shops = $this->db->query($sql,[$id])->result_array();
        if(count($shops)!=0){
            $shops=$shops[0];
        }
        $sql = "select id,deviceId,comment,shopId from shop_devices where shopId is null or shopId=?";
        $devices = $this->db->query($sql,[$id])->result_array();
        return ['shop'=>$shops,'device'=>$devices];
    }
    
    function delete_shop_data($id){
        $this->db->delete('shops',['id'=>$id,'type'=>2]);
        $this->db->query('update shop_devices set shopId=null where shopId='.$id);
    }
    
    function post_shop_data($id,$data,$deviceIds){
        if($id==0){
            $data['type']=2;
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('shops',$data);
            $id=$this->db->insert_id();
            if(count($deviceIds)!=0){
                $this->db->query('update shop_devices set shopId='.$id.' where id in ('.implode(',',$deviceIds).')');
            }
        }
        else{
            $this->db->update('shops',$data,['id'=>$id,'type'=>2]);
            $this->db->query('update shop_devices set shopId=null where shopId='.$id);
            if(count($deviceIds)!=0){
                $this->db->query('update shop_devices set shopId='.$id.' where id in ('.implode(',',$deviceIds).')');
            }
        }
    }
    
    function get_device_data(){
        $sql = "select id,deviceId,comment,major,minor,state from shop_devices ORDER BY id desc";
        $shops = $this->db->query($sql)->result();
        return $shops;
    }
    
    function get_permission_data(){
        $sql = "select id,name,address,mail,phoneNum from merchants where codeVersion!='' and code!=''";
        $shops = $this->db->query($sql)->result();
        return $shops;
    }
    
    function get_permission_detail($id){
        $sql = "select name from merchants where id=?";
        $name = $this->db->query($sql,[$id])->row()->name;
        $sql = "select s.id,s.name,m.shopId from shops s left join shop_mch m on s.id=m.shopId and m.mchId=?";
        $shops = $this->db->query($sql,[$id])->result_array();
        return ['shop'=>$shops,'name'=>$name];
    }
    
    function post_permission_data($id,$shopIds){
        $this->db->delete('shop_mch',['mchId'=>$id]);
        $data=[];
        for($i=0;$i<count($shopIds);$i++){
            $arr=['shopId'=>$shopIds[$i],'mchId'=>$id];
            $data[]=$arr;
        }
        $this->db->insert_batch('shop_mch',$data);
    }
}
