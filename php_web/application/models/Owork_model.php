<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owork_model extends CI_Model {

    //获取工单数据
    function get_all_works(){
        $sql = 'select w.id id,FROM_UNIXTIME(w.createTime, "%Y-%m-%d %H:%i:%S") time,w.type type,w.title title,w.mchId mchId,w.charge charge,w.status status,m.name name,r.name rname from work_orders w left join merchants m on w.mchId=m.id left join work_role r on w.charge=r.id where w.status !=2';
        $r = $this->db->query($sql)->result();
        // return $this->db->where('status!=',2)->get('work_orders')->result();
        return $r;
    }
    //获取单个工单信息
    function get_one_work($id){
        $sql = "select w.id id,FROM_UNIXTIME(w.createTime, '%Y-%m-%d %H:%i:%S') time,w.type type,w.title title,w.content content,w.mchId mchId,w.charge charge,w.status status,m.name name,r.name rname from work_orders w left join merchants m on w.mchId=m.id left join work_role r on w.charge=r.id where w.id =$id";
        return $this->db->query($sql)->row();
    }
    //获取
    function get_role_by_id($oid) {
        return $this->db->where('id', $oid)->get('work_role')->row();
    }
    //保存用户参数
    function save_role($data){
        if(!empty($data['id'])){
            $id = $data['id'];
            unset($data['id']);
            return $this->db->where('id',$id)->update('work_role',$data);
        }else{
            unset($data['id']);
            return $this->db->insert('work_role',$data);
        }
    }
    //获取所有角色信息
    function all_role(){
        return $this->db->where('status!=',2)->get('work_role')->result();
    }
    //删除角色
    function del_r($id){
        return $this->db->where('id',$id)->update('work_role',['status'=>2]);
    }
    //锁定角色
    function lock($id){
        return $this->db->where('id',$id)->update('work_role',['status'=>1]);
    }
    //解锁角色
    function unlock($id){
        return $this->db->where('id',$id)->update('work_role',['status'=>0]);
    }
    //新增模块名称
    function littbar($val){
        // 返回结果说明：1 存在相同值，2 插入成功，3 插入失败
        $data = [];
        $res = $this->db->where('name',$val)->get('work_module')->row();
        if(isset($res)){
            $data['eres'] = null;
            $data['ecode'] = 1;
            return $data;
        }
        $result = $this->db->insert('work_module',['name'=>$val]);
        $r = $this->db->insert_id();
        $data['eres'] = $r;
        if($result){
            $data['ecode'] = 2;
            return $data;
        }else{
            $data['ecode'] = 2;
            return $data;
        }
    }
    //删除模块
    function del_bar($id){
        return $this->db->where('id',$id)->update('work_module',['rowStatus'=>1]);
    }
    //获取模块信息
    function get_modules(){
        return $this->db->get('work_module')->result();
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
}
