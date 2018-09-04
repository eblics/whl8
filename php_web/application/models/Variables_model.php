<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Variables_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    public function set($name,$value,$theTime=NULL){
        if(is_null($theTime)){
            $theTime=time();
        }
        return $this->db->query('insert into variables(name,val,theTime)values(?,?,?)
            on DUPLICATE key update val=?,theTime=?',[$name,$value,$theTime,$value,$theTime]);
    }

    public function get($name){
       return $this->db->query('select * from variables where name=?',[$name])->row();
    }

}
