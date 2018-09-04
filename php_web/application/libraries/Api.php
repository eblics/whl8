<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api
{
    public $appinfo;
    
    public function __construct() {
        $this->ci=&get_instance();
    }
    
    public function get_input($config,$isClear=false,$array=null){
        if($array==null){
            $result=json_decode(file_get_contents("php://input"),true);
        }
        else{
            $result=$array;
        }
        $output=[];
        foreach ($config as $key=>$item){
            if(array_key_exists('required',$item) && $item['required']===false && !isset($result[$key])){
                continue;
            }
            
            if(!isset($result[$key])){
                $this->set_output(1,null,'缺少'.$item['desc'].'字段');
            }
            $value=$result[$key];
            if(is_array($value)){
                if(count($value)==0){
                    $this->set_output(1,null,$item['desc'].'字段不能为空');
                }
            }
            else{
                if($value==''){
                    $this->set_output(1,null,$item['desc'].'字段不能为空');
                }
                else if(!$this->validate_length($item,mb_strlen($value))){
                    $this->set_output(1,null,$item['desc'].'字段长度不符');
                }
                else if(!$this->validate_numeric($item,$value)){
                    $this->set_output(1,null,$item['desc'].'非数字类型');
                }
                else if(!$this->validate_options($item,$value)){
                    $this->set_output(1,null,$item['desc'].'类型不匹配');
                }
                else if(!$this->validate_regexp($item,$value)){
                    $this->set_output(1,null,$item['desc'].'格式不正确');
                }
            }
            if($isClear){
                $output[$key]=$value;
            }
        }
        if($isClear){
            return $output;
        }
        return $result;
    }
    
    private function validate_length($config,$length){
        if(isset($config['length'])){
            $maxLength=0;
            $minLength=0;
            $lenConfig=$config['length'];
            if(count($lenConfig)==2){
                $maxLength=$lenConfig[1];
                $minLength=$lenConfig[0];
            }
            else{
                $maxLength=$lenConfig[0];
                $minLength=$lenConfig[0];
            }
            if($length>$maxLength || $length<$minLength)
                return false;
        }
        return true;
    }
    
    private function validate_numeric($config,$value){
        if(isset($config['numeric']) && $config['numeric']===true){
            return is_numeric($value);
        }
        return true;
    }
    
    private function validate_options($config,$value){
        if(isset($config['options'])){
            return in_array($value, $config['options']);
        }
        return true;
    }
    
    private function validate_regexp($config,$value){
        if(isset($config['regexp'])){
            return preg_match($config['regexp'], $value);
        }
        return true;
    }
    
    public function set_output($errcode,$data=[],$errmsg=null){
        $result=[];
        $result['errcode']=$errcode;
        if($errcode!=0){
            $result['errmsg']=$errmsg;
        }
        if(count($data)!=0){
            foreach ($data as $key=>$value){
                $result[$key]=$value;
            }
        }
        header('content-type:application/json');
        echo json_encode($result);
        exit;
    }
}