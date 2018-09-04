<?php
// 定义虚拟商品发放接口
interface Settler {

    /**
     * 虚拟平台发放虚拟商品
     * 
     * @param $params {
     *  int $mchId,
     *  string $openid,
     *  int $amount,
     *  string desc
     * }
     * @return object
     */
    function requestThirdPlatform($params);
}