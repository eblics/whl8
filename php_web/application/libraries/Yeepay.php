<?php
require_once __DIR__ . '/HttpClient.class.php';

class Yeepay{
    #时间设置
    // date_default_timezone_set('PRC');
    function __construct($config) {
        $this->p1_MerId = $config['p1_MerId'];
        $this->merchantKey = $config['merchantKey'];
    }

    #响应参数转换成数组
    function getresp($respdata)
    {   
        $result = explode("\n",$respdata);
        $output = array();
        foreach ($result as $data) 
        {
            $arr = explode('=',$data);
            if(isset($arr[1])){
                $output[$arr[0]] =  urldecode($arr[1]);
            }
        }
        return $output;
    }
    #生成本地签名hmac(不适用于回调通知)
    function HmacLocal($data)
    {
    	$text="";
    	while (list($key,$value) = each($data))
        {
            if(isset($key) && $key!="hmac" && $key!="hmac_safe") 
            {     
                $text .=    $value;
            }
        }
        return $this->HmacMd5($text,$this->merchantKey);
    }   
    //生成本地的安全签名数据
    function gethamc_safe($data)
    {
    	$text="";
    	while (list($key,$value) = each($data))
        {
            if( $key!="hmac" && $key!="hmac_safe" && $value !=null)
            {
                $text .=  $value."#" ;
            }
        }
        $text1= rtrim( trim($text), '#' ); ; 
        return $this->HmacMd5($text1,$this->merchantKey);   
    }
    //生成hmac
    function HmacMd5($data,$key)
    {
        // RFC 2104 HMAC implementation for php.
        // Creates an md5 HMAC.
        // Eliminates the need to install mhash to compute a HMAC
        // Hacked by Lance Rushing(NOTE: Hacked means written)

        //需要配置环境支持iconv，否则中文参数不能正常处理
        $key = iconv("GBK","UTF-8",$key);
        $data = iconv("GBK","UTF-8",$data);
        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*",md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad ;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack("H*",md5($k_ipad . $data)));
    }
 
}
?> 
