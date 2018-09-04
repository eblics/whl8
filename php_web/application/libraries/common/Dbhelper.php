<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbhelper{
    var $CI;
    var $redis;
    public function __construct() {
        $this->CI=&get_instance();
        $this->CI->load->helper('common/curl_helper');
        $this->CI->load->library('common/cache');
        $this->redis=new Redis();
        $this->redis->pconnect($this->CI->config->item('redis')['host'],$this->CI->config->item('redis')['port']);
        $this->redis->auth($this->CI->config->item('redis')['password']);
    }

    public function insert_update_string($table,$set){
        $set=(array)$set;
        $insert="insert into $table(";
        $values='values(';
        $update='on duplicate key update ';
        $count=count($set);
        foreach($set as $k=>$v){
            $val=trim($v);
            $val=rtrim($val,'\\');
            $insert.=$k;
            $values.="'$val'";
            $update.="$k='$val'";
            if(--$count>0){
                $insert.=',';
                $values.=',';
                $update.=',';
            }
        }
        $insert.=') ';
        $values.=') ';
        return sprintf('%s%s%s',$insert,$values,$update);
    }
    //原触发器方法由curl到node，变为提交到Redis的消息队列，此方法暂时不删除，以兼容旧代码
    public function trigger($sql){
        return $this->push('sql_rpt',$sql);
        //return curl_post_text($this->CI->config->item('rpt_svr_url').'/trigger',$sql);
    }
    public function trigger_bulk($sql){
        return $this->push('sql_rpt',$sql);
        //return curl_post_text($this->CI->config->item('rpt_svr_url').'/trigger_bulk',$sql);
    }
    //即将作废
    public function serve($sql){
        $json=curl_post_text($this->CI->config->item('rpt_svr_url').'/serve',$sql);
        return json_decode($json);
    }

    //即将作废
    public function serve_array($sql){
        $arr=$this->serve($sql);
        if($arr==null)
            return [];
        foreach($arr as &$a){
            $a=(array)$a;
        }
        return $arr;
    }
    //即将作废
    public function serverow($sql){
        $json=curl_post_text($this->CI->config->item('rpt_svr_url').'/serverow',$sql);
        return json_decode($json);
    }
    //即将作废
    public function cook($sql){
        return curl_post_text($this->CI->config->item('rpt_svr_url').'/cook',$sql);
    }

    //将sql放入队列
    public function push($que,$sql){
        return $this->redis->rPush($que,$sql);
    }

    public function cache_get($key){
        return $this->redis->get($key);
    }

    public function cache_set($key,$val){
        return $this->redis->set($key,$val);
    }
    public function cache_del($key){
        return $this->redis->del($key);
    }

    public function cache_incrby($key,$val){
        return $this->redis->incrby($key,$val);
    }

    public function cache_decrby($key,$val){
        return $this->redis->decrby($key,$val);
    }

    public function cache_zadd($set,$mem,$score=0){
        return $this->redis->zAdd($set,$score,$mem);
    }

    public function cache_zscore($set,$mem){
        return $this->redis->zScore($set,$mem);
    }
    public function cache_zincrby($set,$mem,$score){
        return $this->redis->zIncrBy($set,$score,$mem);
    }
    //从缓存读取，如果不存在，则从主服务器读取
    public function get_cache_or_db($key, $sql) {
        $obj = $this->redis->get($key);
        if (! $obj) {
            $obj = $this->CI->db->query($sql)->row();
            $this->redis->set($key, json_encode($obj));
            //24小时过期，防止高并发情况下数据没有及时写入到数据库
            $this->redis->expire($key, 60 * 15);
            return $obj;
        }
        return json_decode($obj);
    }
    //写入缓存及主服务器
    public function set_cache_and_db($key, $val, $sql) {
        $this->push('sql_master', $sql);
        $ret = $this->redis->set($key, json_encode($val));
        if ($ret) {
            //24小时过期，防止高并发情况下数据没有及时写入到数据库
            $this->redis->expire($key, 60 * 15);
        }
        return $ret;
    }

    public function del_cache_and_db($key,$sql){
        $this->CI->db->query($sql);
        $this->redis->del($key);
    }
}
