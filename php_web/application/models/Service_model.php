<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
    //是否存在待审核数据
    public function is_exists($openId){
        $res = $this->db->where('openId',$openId)->where('status',1)->get('service_appeal')->result();
        return $res;
    }
    //查询对应的企业公众号是否被封
    public function get_by_mch($openId){
        $sql = "select * from users_common where openid='$openId'";
        $sasql = "select * from service_appeal where openId='$openId' and status=3";
        $res = $this->db->query($sql)->row();
        $sares = $this->db->query($sasql)->row();
        if(isset($res)){
            $back = (object)[
                    'result'=>null,
                    'res'=>null,
                    'status'=>null
                ];
            if($sares){
                $back->result = 3;
                $back->res = $sares;
                return $back;
            }
            if($res->commonStatus == 1){
                $back->result = 1;
                $back->res = $res;
                return $back;
            }else{        
                $id = $res->id;
                $sqlone = "select * from users_common_sub where parentId='$id' and status=1";
                $r = $this->db->query($sqlone)->result();
                // 1平台封，企业封 
                //遍历
                foreach ($r as $key => $value) {
                    if($value->status == 1){
                        $back->status = 1;
                        return $back;//双封
                    }
                }
                return false;
            }
        }else{
            return false;
        } 
    }
    //查询某openid对应的所有数据
    public function get_all_data($openId){
        return $this->db->where('openId',$openId)->get('service_appeal')->result();
    }
    public function get_all_not2($openId){
        $sql = "select * from service_appeal where openId = $openId and status !=2 and status !=4";
        return $this->db->query($sql)->result();
        // return $this->db->where('openId',$openId)->where('status',2)->get('service_appeal')->result();
    }
    //排除1和3的状态
    public function out_two($openId){
        $sql = "select * from service_appeal where openId='$openId' and status !=2 and status !=4";
        return $this->db->query($sql)->result();
    }
    //插入申请解封数据
    public function insert_lifted($data,$n){
        //新增
        if($n == 1){
            $res = $this->db->insert('service_appeal',$data);
            if($res){
                return $this->db->insert_id();
            }
        }
        //更新
        if($n == 2){
            $res = $this->db->where('openId',$data['openId'])->where('status',3)->update('service_appeal',$data);
            return $res;
        }
        //插入之前首先查询是否是该openid是否对应唯一status=1的状态
        // $res = $this->db->where('openId',$data['openId'])->where('status',1)->get('service_appeal')->row();
        // $status = $res->status;
        // if(isset($res)){
        //     return false;
        // }else{
        //     $r = $this->db->where('openId',$data['openId'])->where('status',3)->get('service_appeal')->row();
        //     if(isset($r) && $r->status == 3){
        //         return $this->db->where('openId',$data['openId'])->update(['status'=>1,'name'=>$data['lname'],'img'=>$data['img'],'phoneNum'=>$data['lphonenum'],'reason'=>$data['lreason']]);
        //     }else{
        //         return false;
        //     }
        // }
        // if($status == 1){
        //     $this->db->insert('service_appeal',$data);
        //     return $this->db->insert_id();
        // }
        // if($status == 3){
        //     $this->db->where('openId',$data['openId'])->update(['status'=>1,'name'=>$data['lname'],'img'=>$data['img'],'phoneNum'=>$data['lphonenum'],'reason'=>$data['lreason']]);
        // }
        // if(isset($res)){
        //     return false;
        // }else{
        //     $this->db->insert('service_appeal',$data);
        //     return $this->db->insert_id();
        // }
    }
    //查询申请状态
    public function get_status($openid,$status){
        if($status == 1){
            return $this->db->where('openId',$openid)->where('status',$status)->get('service_appeal')->row();  
        }
        if($status == 3){
            return $this->db->where('openId',$openid)->where('status',$status)->get('service_appeal')->row(); 
        }
         
    }

    //查询当前用户的信息
    public function get_common_user($openid){
        return $this->db->where('openid',$openid)->where('commonStatus',1)->get('users_common')->row();
    }
    //查询封禁
    public function get_blacklist($openid){
        return $this->db->where('openid',$openid)->get('users_common_blacklist')->row();
    }
    //
    public function appeal($openid){
        return $this->db->where('openid',$openid)->where('status',3)->update('service_appeal',['status'=>2]);
    }
    
}