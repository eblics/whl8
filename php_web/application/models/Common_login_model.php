<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_login_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }
    function get_user($id){
        return $this->db->where('id',$id)->get('users_common')->row();
    }

    function get_user_by_openid($openid){
        return $this->db->where('openid',$openid)->get('users_common')->row();
    }
    
    function get_user_sub_by_userid($subUserId){
        return $this->db->where('userId',$subUserId)->get('users_common_sub')->row();
    }

    function get_user_sub_by_openid($subOpenid){
        return $this->db->where('openid',$subOpenid)->get('users_common_sub')->row();
    }

    function get_blacklist_by_openid($openid){
        return $this->db->where('openid',$openid)->get('users_common_blacklist')->row();
    }

    function get_whitelist_by_openid($openid){
        return $this->db->where('openid',$openid)->get('users_common_whitelist')->row();
    }

    function save_user($user){
        if(!property_exists($user,'tagid_list')){
            $user->tagid_list=[];
        }
        $user->tagid_list=json_encode($user->tagid_list);
        $sql=$this->dbhelper->insert_update_string('users_common',$user);
        $this->db->query($sql);
        if(isset($user->id)){
            return $user->id;
        }else{
            return $this->db->insert_id();
        }
    }

    function save_user_sub($user){
        $sql=$this->dbhelper->insert_update_string('users_common_sub',$user);
        $this->db->query($sql);
        if(isset($user->id)){
            return $user->id;
        }else{
            return $this->db->insert_id();
        }
    }

    function save_user_log($data){
        $sql=$this->dbhelper->insert_update_string('users_common_log',$data);
        $this->db->query($sql);
        return $this->db->insert_id();
    }


}
