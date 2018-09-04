<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ipwall {
    
    private $ci;
    private $cache_key;
    private $ip_item;
    
    function __construct()
    {
        $this->ci=&get_instance();
        $this->ci->load->model('scan_log_model','scan_log');
        $this->cache_key=$this->ci->input->ip_address();
        $this->ip_item=$this->ci->cache->get($this->cache_key);
        if(empty($this->ip_item) || !property_exists($this->ip_item,'error_time')){
            $this->ip_item=(object)['time'=>time(),'count'=>0,'error_time'=>time(),'error_count'=>0];
            $this->save();
        }
    }
    
    public function is_prevent(){
        if($this->ip_item->time<time()){
            $this->ip_item->time=time();
            $this->ip_item->count=0;
        }
        //限制每个ip每秒最多只能扫10个码，无论对错
        if($this->ip_item->count>10){
            $this->ci->scan_log->add_black_list($this->cache_key);
            log_message('error',$this->cache_key.'扫码过于频繁');
            return true;
        }
        
        $this->ip_item->count+=1;
        $this->save();
        
        if($this->ip_item->error_time>time()){
            //针对当前session中的只要错误立马封10小时
            $this->ci->scan_log->add_black_list($this->cache_key);
            log_message('error',$this->cache_key.'扫错误码过于频繁');
            return true;
        }
        
        return false;
    }
    
    public function correct_process(){
        $this->ip_item->error_count=0;
        $this->ip_item->error_time=time();
        $this->save();
    }
    
    public function error_process(){
        //白名单IP不做处理
        //beiqi 120.236.142.136 182.234.63.132
        $whiteList=['120.236.142.136','182.234.63.132','112.25.208.41'];
        if(in_array($this->cache_key,$whiteList)){
            return;
        }
        //只要错误立马封10小时
        $this->ci->scan_log->add_black_list($this->cache_key);
        $this->ip_item->error_count+=1;
        if($this->ip_item->error_count>=5){
            $this->ip_item->error_time=time()+(60*60);
        }
        else if($this->ip_item->error_count>=4){
            $this->ip_item->error_time=time()+(20*60);
        }
        else if($this->ip_item->error_count>=3){
            $this->ip_item->error_time=time()+(5*60);
        }
        $this->save();
        $this->ci->load->view('error_process', [
            'errmsg' => '出错了', 
            'err_content' => '警告：此码涉嫌非法篡改，我们保留追究法律责任的权利。如有疑问，请联系我们。运营支持邮箱：acctrueyun@acctrue.com。'
        ]);
    }
    
    private function save(){
        $this->ci->cache->set($this->cache_key, $this->ip_item);
    }
}