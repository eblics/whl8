<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Geolocation_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_gps($data){
        $sql="select *,id geoId from geo_gps where lng=$data->lng and lat=$data->lat";
        $obj= $this->db->query($sql)->row();
        return $obj;
    }

    public function get_ip($data){
        $sql="select geo_ip.id,geo_gps.id geoId,ip,lng,lat,lngBaidu,latBaidu,areaCode,address from geo_ip,geo_gps where geo_ip.geoId=geo_gps.id and ip='$data'";
        return $this->db->query($sql)->row();
    }

    public function insert_gps($data){
        $sql='INSERT INTO geo_gps(lng,lat,lngBaidu,latBaidu,areaCode,address,expireTime)
              VALUES('. $data->lng .','. $data->lat .','. $data->lngBaidu .','. $data->latBaidu .','. $data->areaCode .',\''. $data->address .'\','. $data->expireTime .')
              on duplicate key update expireTime=expireTime';
        $this->db->query($sql);
        return $this->db->insert_id();
    }

    public function update_gps($data){
        $sql='UPDATE geo_gps SET areaCode='. $data->areaCode .',address=\''. $data->address .'\',expireTime='. $data->expireTime .'
              WHERE lng='. $data->lng .' and lat='. $data->lat;
        return $this->db->query($sql);
    }
    public function update_geo_gps($data){ 
        if(!property_exists($data,'lngBaidu') || !property_exists($data,'latBaidu')){
            log_message('error','Geolocation_model update_geo_gps fail:'.var_export($data,true));
            return false;
        }
        $sql="UPDATE geo_gps SET areaCode='$data->areaCode',address='$data->address',lngBaidu=$data->lngBaidu,latBaidu=$data->latBaidu,expireTime=$data->expireTime
            where lng=$data->lng and lat=$data->lat";
        return $this->db->query($sql);
    }

    public function update_gps_bd($data){
        $sql='UPDATE geo_gps SET lngBaidu='. $data->lngBaidu .',latBaidu='. $data->latBaidu .' WHERE lng='. $data->lng .' and lat='. $data->lat;
        return $this->db->query($sql);
    }

    public function insert_ip($data){
        $time=time()+7*24*3600;
        $sql="INSERT INTO geo_ip(ip,geoId,expireTime)
              VALUES('$data->ip',$data->geoId,$time)
              on duplicate key update ip='$data->ip',expireTime=$time";
        $this->db->query($sql);
        return $this->db->insert_id();
    }

    public function update_ip($data){
        $sql='UPDATE geo_ip SET areaCode='. $data->areaCode .',lngBaidu='. $data->lngBaidu .',latBaidu='. $data->latBaidu .',address=\''. $data->address .'\',expireTime='. $data->expireTime .'
              WHERE ip=\''. $data->ip .'\'';
        return $this->db->query($sql);
    }

    function get_ip_geo($ip){
        $expireTime=time();
        $sql="select * from geo_ip where ip='$ip' and expireTime<$expireTime";
        return $this->db->query($sql)->row();
    }
}
