<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cache{
    var $CI;
    var $host;
    var $port;
    public function __construct() {
        $this->CI=&get_instance();
        $this->host=$this->CI->config->item('redis')['host'];
        $this->port=$this->CI->config->item('redis')['port'];
    }

    public function set($key,$value){
        $redis=new redis();
        $redis->connect($this->host, $this->port);
        $redis->auth($this->CI->config->item('redis')['password']);
        $value=json_encode($value);
        $redis->set($key,$value);
    }

    public function get($key){
        $redis=new redis();
        $redis->connect($this->host, $this->port);
        $redis->auth($this->CI->config->item('redis')['password']);
        return json_decode($redis->get($key));
    }
}

