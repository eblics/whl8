<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personalinfo_model extends CI_Model {

    public function getPersonalInfo($userId) {
        $sql = "select t1.mobile, t1.birthday, t1.address, t1.realname,
            t2.code provincecode,t2.name provincename,
            t3.code citycode,t3.name cityname
            from (select mobile,date(birthday) birthday,areacode,address,realname from users where id = ?) t1
            left join areas t3 on t1.areacode = t3.code 
            left join areas t2 on t3.parentCode = t2.code";
        return $this->db->query($sql, [$userId])->row();
    }
    
    function mchid_to_mallid($mchid) {
        $result=$this->db->query('select id from malls where mchid=?',[$mchid])->result_array();
        if(count($result)==0){
            return null;
        }
        return $result[0]['id'];
    }
    
    function updateinfo($mchid,$openid,$values){
        $this->db->where('mchid',$mchid)->where('openid',$openid)->update('users',$values);
    }
    
    function areas() {
        $areas=$this->db->query('SELECT code,name,level from areas where (level=0 or level=1) and (type=0 or type=1) order by code')->result();
        $result=[];
        foreach ($areas as $area) {
            $level=intval($area->level);
            if($level==0){
                $result[]=['code'=>$area->code,'name'=>$area->name,'children'=>[]];
            }
            else{
                $result[count($result)-1]['children'][]=['code'=>$area->code,'name'=>$area->name];
            }
        }
        return $result;
    }
}