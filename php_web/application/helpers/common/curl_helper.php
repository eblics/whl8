<?php
/**
 * 发起HTTPS请求
 */
function curl_post($url,$data=null,$ssl=FALSE,$header=[])
{
    //初始化curl
    $ch = curl_init();
    //参数设置
    $res= curl_setopt ($ch, CURLOPT_URL,$url);
    if($ssl){
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    $result = curl_exec ($ch);
    //连接失败
    curl_close($ch);
    return $result;
}

function curl_post_json($url,$data,$ssl=FALSE){
    return curl_post($url,$data,$ssl,['Content-type:application/json']);
}

function curl_post_text($url,$data,$ssl=FALSE){
    return curl_post($url,$data,$ssl,['Content-type:text/plain']);
}

function curl_get($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    return $res;
}
