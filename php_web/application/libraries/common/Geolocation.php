<?php
class Geolocation {
    var $CI;
    public function __construct() {
        $this->CI=& get_instance();
        $this->CI->load->model('geolocation_model');
    }

    //经纬度转换定位所属区块坐标点
    //lng:经度 lat:纬度
    private function convert_gps($data){
        $lng=$data->lng;
        $lat=$data->lat;
        $r=6371393;//地球半径 单位m
        $a=100;//尺度 100m
        $yScale=pi()*$r/(2*$a);
        $y=round($lat/90*$yScale);
        $ptLat=$y/$yScale*90;
        $xScale=cos($ptLat*pi()/180)*(pi()*$r/$a);
        $x=round($lng/180*$xScale);
        $ptLng=$x/$xScale*180;
        $ptLat=round($ptLat,6);
        $ptLng=round($ptLng,6);
        return (object)['lng'=>floatval($ptLng),'lat'=>floatval($ptLat)];
    }
    //gps坐标转地理位置
    private function gps_to_area($data,$coordtype='wgs84ll'){
        $latlng=$data->lat.','.$data->lng;
        $url='http://api.map.baidu.com/geocoder/v2/?ak=o78tmC1oaRiWGTVpZowpSyrP&output=json&coordtype='.$coordtype.'&pois=0';
        $url.='&location='.$latlng;
        $httpget=curl_get($url);
        if(!$httpget) $httpget=curl_get($url);
        if(!$httpget) $httpget=curl_get($url);
        if(!$httpget) {
            log_message('error','baidu map geocoder fail:'.var_export($data,TRUE));
            return (object)['errcode'=>1,'errmsg'=>'baidu map geocoder fail','data'=>''];
        }
        $output = json_decode($httpget);
        if(intval($output->status)===0){
            $adcode=$output->result->addressComponent->adcode;
            $address=$output->result->formatted_address;
            return (object)['errcode'=>0,'errmsg'=>'','data'=>(object)['adcode'=>$adcode,'address'=>$address]];
        }else{
            log_message('error','gps坐标转地理位置接口请求失败:'.var_export($data,TRUE));
            return (object)['errcode'=>1,'errmsg'=>'GPS接口请求失败','data'=>''];
        }
    }
    //IP定位
    private function ip_to_bd($ip){
        $url='http://api.map.baidu.com/location/ip?ak=o78tmC1oaRiWGTVpZowpSyrP&coor=bd09ll';
        $url.='&ip='.$ip;
        $httpget=curl_get($url);
        if(!$httpget) $httpget=curl_get($url);
        if(!$httpget) $httpget=curl_get($url);
        if(!$httpget) {
            log_message('error','baidu map ip fail:'.$ip);
            return (object)['errcode'=>1,'errmsg'=>'baidu map ip fail','data'=>''];
        }
        $output = json_decode($httpget);
        if(intval($output->status)===0){
            $lnglat=(object)['lng'=>$output->content->point->x,'lat'=>$output->content->point->y];
            return (object)['errcode'=>0,'errmsg'=>'','data'=>$lnglat];
        }else{
            log_message('error','百度IP接口请求失败:'.$ip);
            return (object)['errcode'=>1,'errmsg'=>'IP接口请求失败','data'=>''];
        }
    }
    //IP转地理位置
    private function ip_to_area($ip){
        $lnglat=$this->ip_to_bd($ip);
        if($lnglat->errcode==0){
            return $this->gps_to_area($lnglat->data,'bd09ll');
        }else{
            return (object)['errcode'=>1,'errmsg'=>'IP转地理位置请求失败','data'=>''];
        }
    }
    //坐标转换
    private function gps_to_bd($data){
        $url='http://api.map.baidu.com/geoconv/v1/?ak=o78tmC1oaRiWGTVpZowpSyrP&output=json&coords='.$data.'&from=1&to=5&output=json';
        $httpget=curl_get($url);
        if(!$httpget){
            log_message('error','坐标转换请求失败:'.$data);
            return (object)['errcode'=>1,'errmsg'=>'坐标转换请求失败','data'=>''];
        }
        $output = json_decode($httpget);
        if(intval($output->status)===0){
            $result=$output->result;
            return (object)['errcode'=>0,'errmsg'=>'','data'=>$result];
        }else{
            log_message('error',$data.' : '.$output->message);
            return (object)['errcode'=>1,'errmsg'=>$output->message,'data'=>''];
        }
    }
    public function get_geo_area($data){
        $area=$this->gps_to_area($data);
        $gps=(object)['lng'=>$data->lng,'lat'=>$data->lat,'areaCode'=>'',
            'address'=>'','expireTime'=>time()+3600*24*7];
        if($area->errcode!=0){
            $gps->areaCode='000000';
            $gps->lngBaidu=0;
            $gps->latBaidu=0;
            return $gps;
        }
        if($area->errcode==0){
            $gps=(object)['lng'=>$data->lng,'lat'=>$data->lat,'areaCode'=>$area->data->adcode,
                'address'=>$area->data->address,'expireTime'=>time()+3600*24*7];
            $coord=$this->gps_to_bd($data->lng.','.$data->lat);
            if($coord->errcode==0){
                $bdCoord=$coord->data;
                $newCoord=(object)['lng'=>$data->lng,'lat'=>$data->lat,'lngBaidu'=>$bdCoord[0]->x,'latBaidu'=>$bdCoord[0]->y];
                $gps->lngBaidu=$newCoord->lngBaidu;
                $gps->latBaidu=$newCoord->latBaidu;
            }
            return $gps;
        }
    }
    //获取GPS坐标信息
    public function get_gps($gpsData) {
        // 将经纬度点阵化
        $data = $this->convert_gps($gpsData);
        // 从geo_gps表中获取经纬度所对应的位置信息
        $gps = $this->CI->geolocation_model->get_gps($data);
        if (isset($gps)) { 
            if ($gps->expireTime < time()) { // 位置信息需要更新
                $area = $this->gps_to_area($data); // 通过百度接口将经纬度转换为位置信息
                if ($area->errcode == 0) {
                    $gps = (object)[
                        'id' => $gps->id,
                        'lng' => $data->lng,
                        'lat' => $data->lat,
                        'areaCode' => $area->data->adcode,
                        'address' => $area->data->address,
                        'expireTime' => time() + 3600 * 24 * 7 // expire time for one week
                    ];
                    $this->CI->geolocation_model->update_gps($gps);

                    // exchange position
                    $coord = $this->gps_to_bd($data->lng.','.$data->lat);
                    if ($coord->errcode == 0) {
                        $bdCoord = $coord->data;
                        $newCoord = (object)[
                            'lng' => $data->lng,
                            'lat' => $data->lat,
                            'lngBaidu' => $bdCoord[0]->x,
                            'latBaidu' => $bdCoord[0]->y
                        ];
                        $gps->lngBaidu = $newCoord->lngBaidu;
                        $gps->latBaidu = $newCoord->latBaidu;
                        $this->CI->geolocation_model->update_gps_bd($newCoord);
                    }

                }
            }
        } else {
            $area=$this->gps_to_area($data);
            if ($area->errcode == 0) {
                $gps=(object)[
                    'lng' => $data->lng,
                    'lat' => $data->lat,
                    'areaCode' => $area->data->adcode,
                    'address' => $area->data->address,'expireTime'=>time()+3600*24*7];
                $coord=$this->gps_to_bd($data->lng.','.$data->lat);
                if ($coord->errcode == 0) {
                    $bdCoord=$coord->data;
                    $newCoord=(object)[
                        'lng' => $data->lng,
                        'lat' => $data->lat,
                        'lngBaidu' => $bdCoord[0]->x,
                        'latBaidu' => $bdCoord[0]->y
                    ];
                    $gps->lngBaidu=$newCoord->lngBaidu;
                    $gps->latBaidu=$newCoord->latBaidu;
                    $gps->id=$this->CI->geolocation_model->insert_gps($gps);
                    $gps->geoId=$gps->id;
                }
            }else{
                $gps=FALSE;
            }
        }
        if ($data->lng != $gps->lng || $data->lat != $gps->lat) {
            log_message('error',var_export($data,TRUE));
            log_message('error',var_export($gps,TRUE));
        }
        if (! property_exists($gps, 'id') || $gps->id == -1) {
            $gps->id = -1;
            log_message('error',var_export(['geoid:-1',$gps],TRUE));
        }
        return $gps;
    }
    //获取IP位置信息
    public function get_ip($data){
        $ip=$this->CI->geolocation_model->get_ip($data);
        //if($ip){//IP信息存在执行更新判断
            //if($ip->expireTime<time()){
                //$lnglat=$this->ip_to_bd($data);
                //if($lnglat->errcode==0){
                    //$area = $this->gps_to_area($lnglat->data,'bd09ll');
                    //if($area->errcode==0){
                        //$ip=(object)['ip'=>$data,'areaCode'=>$area->data->adcode,
                                    //'lngBaidu'=>$lnglat->data->lng,'latBaidu'=>$lnglat->data->lat,
                                    //'address'=>$area->data->address,'expireTime'=>time()+3600*24*7];
                        //$this->CI->geolocation_model->update_ip($ip);
                    //}
                //}else{
                    //$ip=FALSE;
                //}
            //}
        //}else{//GPS信息不存在执行新增
            //$lnglat=$this->ip_to_bd($data);
            //if($lnglat->errcode==0){
                //$area = $this->gps_to_area($lnglat->data,'bd09ll');
                //if($area->errcode==0){
                    //$ip=(object)['ip'=>$data,'areaCode'=>$area->data->adcode,
                                //'lngBaidu'=>$lnglat->data->lng,'latBaidu'=>$lnglat->data->lat,
                                    //'address'=>$area->data->address,'expireTime'=>time()+3600*24*7];
                    //$this->CI->geolocation_model->insert_ip($ip);
                //}
            //}else{
                //$ip=FALSE;
            //}
        //}
        //if(!$ip){//走淘宝接口
            //$tburl='http://ip.taobao.com/service/getIpInfo.php?ip='.$data;
            //$httptb=curl_get($tburl);
            //if($httptb){
                //$output = json_decode($httptb);
                //if($output->code==0){
                    //$ip=(object)['ip'=>$data,'areaCode'=>$output->data->city_id,
                                //'lngBaidu'=>NULL,'latBaidu'=>NULL,
                                    //'address'=>$output->data->region.$output->data->city,'expireTime'=>time()+3600*24*7];
                    //$this->CI->geolocation_model->insert_ip($ip);
                //}
            //}
        //}
        return $ip;

    }

}

