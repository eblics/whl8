<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan_api_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_auth($info) {
        $result=$this->db->query('select appId,appSecret from auth_token a join merchants m on a.mchid=m.id where WLLCode=?',[$info['merchant_code']])->result_array();
        if(count($result)==0){
            return 'merchant_code对应的账户不存在';
        }
        else{
            return $result[0];
        }
    }

    function scan($info) {
        $this->load->library('geolocation');
        $this->load->model('scan_log_model');
        
        $result=$this->db->query('select id from merchants where WLLCode=?',[$info['merchant_code']])->result_array();
        if(count($result)==0){
            $data=[
                'name'=>$info['merchant_name'],
                'fromWLL'=>1,
                'WLLCode'=>$info['merchant_code']
            ];
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('merchants',$data);
            $mchid=$this->db->insert_id();
        }
        else{
            $mchid=$result[0]['id'];
        }
        
        $result=$this->db->query('select id,mchid from users where openid=?',[$info['openid']])->result_array();
        if(count($result)==0){
            $data=[
                'openid'=>$info['openid'],
                'mchid'=>$mchid,
                'subscribe'=>$info['subscribe'],
                'nickname'=>$info['nickname'],
                'sex'=>$info['sex'],
                'province'=>$info['province'],
                'city'=>$info['city'],
                'country'=>$info['country'],
                'headimgurl'=>$info['headimgurl'],
                'subscribe_time'=>$info['subscribe_time'],
                'groupid'=>$info['groupid'],
                'fromWLL'=>1,
            ];
            if(isset($info['remark']))
                $data['remark']=$info['remark'];
            if(isset($info['mobile']))
                $data['mobile']=$info['mobile'];
            if(isset($info['email']))
                $data['email']=$info['email'];
            if(isset($info['birthday']))
                $data['birthday']=$info['birthday'];
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('users',$data);
            $userid=$this->db->insert_id();
        }
        else if($result[0]['mchid']!=$mchid){
            return 'openid和merchant_code不对应';
        }
        else{
            $userid=$result[0]['id'];
        }
        
        $result=$this->db->query('select id from products where WLLCode=? and mchid=?',[$info['product_code'],$mchid])->result_array();
        if(count($result)==0){
            $data=[
                'mchid'=>$mchid,
                'WLLCode'=>$info['product_code'],
                'name'=>$info['product_name'],
                'unit'=>$info['product_unit'],
                'specification'=>$info['product_specification'],
                'fromWLL'=>1,
            ];
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('products',$data);
            $productid=$this->db->insert_id();
        }
        else{
            $productid=$result[0]['id'];
        }
        
        $result=$this->db->query('select id from batchs where batchNo=? and mchid=?',[$info['produce_batchno'],$mchid])->result_array();
        if(count($result)==0){
            $data=[
                'mchid'=>$mchid,
                'batchNo'=>$info['produce_batchno'],
                'activeTime'=>$info['produce_date'],
                'expireTime'=>$info['expire_date'],
                'rowStatus'=>0,
                'productId'=>$productid,
                'fromWLL'=>1,
            ];
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('batchs',$data);
            $batchid=$this->db->insert_id();
        }
        else{
            $batchid=$result[0]['id'];
        }
        
        $result=$this->db->query('select count(*) ct from scan_log where code=?',[$info['scan_code']])->result_array();
        if($result[0]['ct']!='0'){
            return '用户扫描的码不能重复';
        }
        $pos=new stdClass();
        $pos->lat=$info['scan_latitude'];
        $pos->lng=$info['scan_longitude'];
        $geo=$this->geolocation->get_gps($pos);
        $data=[
            'code'=>$info['scan_code'],
            'mchId'=>$mchid,
            'userId'=>$userid,
            'openid'=>$info['openid'],
            'ip'=>$info['scan_ip'],
            'scanTime'=>$info['scan_time'],
            'areaCode'=>$geo->areaCode,
            'geoId'=>($geo)&&isset($geo->geoId)?$geo->geoId:-1,
            'lat'=>$info['scan_latitude'],
            'lng'=>$info['scan_longitude'],
            'geoLat'=>$geo->lat,
            'geoLng'=>$geo->lng,
            'gps'=>$info['scan_longitude'].' '.$info['scan_latitude'],
            'batchId'=>$batchid,
            'fromWLL'=>1,
        ];
        $this->scan_log_model->save($data);
        
        $result=$this->db->query('select count(id) ct from auth_token where mchid=?',[$mchid])->result_array();
        if($result[0]['ct']=='0'){
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $appid = '';
            $appsecret = '';
            $timeStr = date('ymdHis',time());
            for ($i = 0; $i < strlen($timeStr); $i++) {
                $appid .= $chars[intval($timeStr[$i])*intval(strlen($chars)/10)];
            }
            for ($i = 0; $i < 4; $i++) {
                $appid .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            for ($i = 0; $i < 24; $i++) {
                $appsecret .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            $data=[
                'mchId'=>$mchid,
                'appId'=>$appid,
                'appSecret'=>$appsecret
            ];
            $this->db->insert('auth_token',$data);
        }
    }
}