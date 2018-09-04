<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Code_encoder {

    var $CI;
    public function __construct() {
        $this->CI=&get_instance();
        $this->CI->load->model('merchant_model','merchant');
        $this->CI->load->model('code_version_model','code_version');
    }
    /**
     * 根据码规则，将整数值加密、混淆为一个字符串
     * @param  [type] $version [description]
     * @param  [type] $i      [description]
     * @return [type]         [description]
     */
    function encode($version,$mch_id,$i,$if_pub_code=FALSE){
        $merchant=$this->CI->merchant->get($mch_id);
        if(!isset($merchant)){
            return (object)[
                'errcode'=>1,
                'errmsg'=>'没有这个商户',
                'result'=>NULL
            ];
        }
        $verDef=$this->CI->code_version->get_by_version($merchant->codeVersion);
        if(!isset($verDef)){
            return (object)[
                'errcode'=>2,
                'errmsg'=>'没有这个码版本',
                'result'=>NULL
            ];
        }

        $params = [
                'prefix'=>$this->CI->config->item('code_prefix'),
                'version'=>$version,
                'value'=>$i,
                'if_pub_code'=>$if_pub_code,
                'mch_code'=>$merchant->code,
                'serial_len'=>(int)$verDef->serialLen,
                'valid_len'=>(int)$verDef->validLen,
                'value'=>(int)$i,//不包含校验码的码文本
            ];
        if (function_exists('hls_encode')) {
            $code_obj=(object)hls_encode($params);
        } else {
            $code_obj=json_decode(file_get_contents('http://dev.tools.lsa0.cn/batch.php?type=encode&params=' . base64_encode(json_encode($params))));
        }
        
        return (object)[
            'errcode'=>0,
            'errmsg'=>'ok',
            'result'=>$code_obj
        ];
    }

    function decode($code){
        $code_prefix=$this->CI->config->item('code_prefix');
        $version=substr($code,0,1);
        $verDef=$this->CI->code_version->get_by_version($version);
        if(!isset($verDef)){
            log_message('error',"code_decoder/decode:$code  没有这个码版本");
            return (object)[
            'errcode'=>1,
            'errmsg'=>'没有这个码版本',
            ];
        }
        $relLen=strlen($verDef->versionNum)+(int)$verDef->mchCodeLen+(int)$verDef->serialLen+(int)$verDef->validLen+(int)$verDef->offsetLen;
        if(strlen($code)!=$relLen){
            log_message('error',"code_decoder/decode:$code  码长度无效");
            return (object)[
            'errcode'=>2,
            'errmsg'=>'码长度无效',
            ];
        }
        $params = [
            'code'=>$code,
            'version'=>$verDef->versionNum,
            'mch_code_len'=>(int)$verDef->mchCodeLen,
            'serial_len'=>(int)$verDef->serialLen,
            'valid_len'=>(int)$verDef->validLen
        ];
        
        if (function_exists('hls_decode')) {
            $code_obj=(object)hls_decode($params);
        } else {
            $result = file_get_contents('http://dev.tools.lsa0.cn/batch.php?type=decode&params=' . base64_encode(json_encode($params)));
            $code_obj=json_decode($result);
        }
        $code_obj->code_prefix=$code_prefix;
        //因为鸟人用华润帐号申请了泰山的码，所以才有之下的恶心代码
        if($code_obj->mch_code==='XX'){
            if($code_obj->value>=19151405 && $code_obj->value<=20351404){
                $code_obj->mch_code='04';
                $code_obj->value-=19151404;
            }
            if($code_obj->value>=32651405&&$code_obj->value<=32701404){
                $code_obj->mch_code='04';
                $code_obj->value-=31451404;
            }
        }
        return (object)[
            'errcode'=>0,
            'errmsg'=>'ok',
            'result'=>$code_obj
        ];
    }
    function decode_pub($pub_code){
        $ret=(object)['errcode'=>0,'errmsg'=>''];
        $ret->result=(object)hls_decode_pub($pub_code);
        return $ret;
    }
}
