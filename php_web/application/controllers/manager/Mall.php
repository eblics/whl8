<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mall extends MerchantController{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('mall_model');
        $this->mchId = $this->session->userdata('mchId');
        $mall_id = $this->mall_model->get_mallId_by_mchId($this->mchId);
        if($mall_id){
            $this->session->userdata['mallId'] = $mall_id->id;
        }else{
            $this->session->userdata['mallId'] = null;
        }
        $this->mallId = $this->session->userdata['mallId'];
        $this->load->library('log_record');
    }

    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }
    /**
     * 配置页面
     */
    public function configure() {
        $res = $this->mall_model->get_mall($this->mchId);
        if($res){
            $data = array(
                'name'=>$res->name,
                'desc'=>$res->desc,
                'mallId'=>$res->id
                );
        }else{
            $data = array(
                'name'=>null,
                'desc'=>null,
                'mallId'=>null
                );
        }
        $this->load->view('mall_configure',['data'=>$data]);
    }
    /**
     * 开通积分商城
     */
    public function update_mall(){
        $mchId = $this->mchId;
        $name = $this->input->post('name');
        $desc = $this->input->post('desc');
        $data = array(
                'name'=>$name,
                'desc'=>$desc,
                'mchId'=>$mchId
            );
        if($this->mallId){
            
            try {
                $res = $this->mall_model->update_mall($mchId,$data);
                if ($res) {
                    $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
                    try{
                        $logInfo = [];
                        $logInfo ['info'] = '更新商城配置';
                        $logInfo ['op'] = $this->log_record->Update;
                        $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                    }catch (Exception $e){
                         log_message('error','mch_log_error:'.$e->getMessage()); 
                    }
                }
            } catch (Exception $e) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
            }
            
        }else{
            try {
                # 日志开始

                # 日志结束
                $res = $this->mall_model->insert_mall($mchId,$data);
                if ($res) {
                    $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
                    try{
                        $logInfo = [];
                        $logInfo ['info'] = '开通商城';
                        $logInfo ['op'] = $this->log_record->Add;
                        $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                    }catch (Exception $e){
                         log_message('error','mch_log_error:'.$e->getMessage()); 
                    }
                }
                
            } catch (Exception $e) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
            }
        }
    }
    /**
     * 商品分类
     */
    public function category(){
        if($this->mallId == null){
            $data = [
                "isopen"=>false
            ];
        }else{
            $data = [
                "isopen"=>true
            ];
        }
        $this->load->view('mall_category',['data'=>$data]);
    }
    /**
     * 拉取分类
     */
    public function catedata(){
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $categories=$this->mall_model->get_category($this->mallId);
        $categories=json_decode( json_encode($categories),true);
        function getTree($arrCat, $parent_id = 0, $level = 0) {
            static  $arrTree = array(); //使用static代替global
            if(empty($arrCat)) return FALSE;
            $level++;
            foreach($arrCat as $key => $value)
            {
                if($value['parentCategoryId' ] == $parent_id)
                {
                    $value[ 'level'] = $level;
                    $arrTree[] = $value;
                    unset($arrCat[$key]); //注销当前节点数据，减少已无用的遍历
                    getTree($arrCat,$value['id'], $level);
                }
            }
            return $arrTree;
        }
        $catetree=getTree($categories,-1,0);
        $data=["data"=>$catetree];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 添加商品分类
     */
    public function add_category(){
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $postData=$this->input->post();
        $data=[
            'mallId'=>$this->mallId,
            'parentCategoryId'=>-1,
            'name'=>$postData['name'],
            'desc'=>$postData['desc'],
            'createTime'=>time(),
            'updateTime'=>time()
        ];
        $insertId=$this->mall_model->add_category($data);
        try {
            if ((int)$insertId!=0) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'', 'errcode'=>0,'result'=>$insertId]));
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '分类名称：'.$postData['name'].'，ID：'.$insertId;
                    $logInfo ['op'] = $this->log_record->Add;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                     log_message('error','mch_log_error:'.$e->getMessage()); 
                }
            }
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
        }
    }
    /**
     * 更新分类信息
     */
    public function update_category(){
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $postData=$this->input->post();
        $qdata=$this->mall_model->get_category_by_id($postData['id']);
        $thisdata = (object)$qdata;
        $thismallid = $this->mallId;
        $thatmallid = $thisdata->mallId;
        if($thismallid!=$thatmallid){
            exit('无权操作');
        } 
        $where=[
            'id'=>$postData['id'],
            'mallId'=>$this->mallId
        ];
        $data=[
            'updateTime'=>time()
        ];
        if(isset($postData['name'])){
            $data['name']=$postData['name'];
        }
        if(isset($postData['desc'])){
            $data['desc']=$postData['desc'];
        }
        $status=$this->mall_model->update_category($where,$data);
        try {
            if ($status) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'', 'errcode'=>0]));
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '分类名称：'.$postData['cname'].'，ID：'.$postData['id'];
                    $logInfo ['op'] = $this->log_record->Update;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                     log_message('error','mch_log_error:'.$e->getMessage()); 
                }
            }
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
        }
    }
    /**
     * 删除商品分类
     */
    public function del_category(){
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $id=$this->input->post('id');
        $name = $this->input->post('name');
        $qdata=$this->mall_model->get_category_by_id($id);
        if(isset($qdata)){
            $thisdata = (object)$qdata;
            $thisid = $thisdata->mallId;
            $thatid = $this->mallId;
            if($thisid !=$thatid) exit('无权操作');
            $subNum=$this->mall_model->sub_category_num($id);
            if($subNum>0){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'此分类下存在子类，无法删除'
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($result));
            }else{
                $products=$this->mall_model->get_by_category($id);
                if($products){
                    $result=[
                        'errcode'=>1,
                        'errmsg'=>'此分类下存在商品，无法删除'
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($result));
                }else{
                    $isdel=$this->mall_model->del_category($id);
                    try{
                        if($isdel){//有父级分类
                            $logInfo = [];
                            $logInfo ['info'] = '分类名称：'.$name.'，ID：'.$id;
                            $logInfo ['op'] = $this->log_record->Delete;
                            $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'', 'errcode'=>0]));
                        }
                    }catch(Exception $e){
                        log_message('error','mch_log_error:'.$e->getMessage());
                    }
                }
            }
        }
        
    }
    /**
     * 商品页面
     */
    public function goods() {
        if($this->mallId == null){
            $data = [
                "isopen"=>false
            ];
            $this->load->view('mall_goods',['sdata'=>$data]);
        }else{
            $data = [
                "isopen"=>true
            ];
            $this->load->view('mall_goods',['sdata'=>$data]);
        }
    }
    /**
     * 商品新增/编辑页面
     */
    public function edit() {
        // $id = $this->input->get('id');
        // $id = get_current_router(4);
        $id = $this->uri->segment(4);
        if($id){
            $tmp = $this->mall_model->get_mallId_by_id($id);
            if(isset($tmp)){
                $this_mchId = $tmp->mallId;
            }else{
                exit('您无权访问此商品！');
            }
            if($this_mchId != $this->mallId){
                exit('您无权访问此商品！');
            }
            $res = $this->mall_model->getinfo_by_id($id);
            if($res){
                $view['data'] = $res[0];
            }   
        }else{
            $data = (object)array(
                    'id'=>null,
                    'goodsName'=>null,
                    'category'=>null,
                    'oPrice'=>null,
                    'price'=>null,
                    'description'=>null
                );
            $view['data'] = $data;
        }
        if($id){
            $view['title'] = '编辑';
            $view['isEdit'] = 'true';
        }else{
            $view['title'] = '新增';
            $view['isEdit'] = 'false';
        }
        $httpval = $this->config->item('base_url');
        $this->load->view('mall_goods_edit',['view'=>$view,'httpval'=>$httpval]);
    }
    /**
     * 接收编辑/新增
     */
    public function get_update() {
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }

        $data = $this->input->post(array('id','oPrice','price','description','goodsName','categoryId','exchangeType','viralGoods','viralPlatform', 'viralAmount', 'createOrder'), FALSE);
        //$data['description'] = htmlspecialchars($data['description']);
        // var_dump($data['description']);
        // exit;
        $data['isViral'] = $data['viralGoods'];
        unset($data['viralGoods']);
        if (!$data['isViral']) {
            $data['viralPlatform'] = NULL;
        }
        $id = $this->uri->segment(3);
        if($id != null){
            $this_mchId = $this->mall_model->get_mallId_by_id($id)->mallId;
            $thatmallid = $this->mallId;
            if($this_mchId != $thatmallid){
                exit('您无权访问此商品！');
            }
        }
        if($id != $data['id']){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'商品参数异常',17800));
            return;
        }
        $newid = $data['id'];
        $array = $this->input->post('arraydata');
        
        if($id == null || $id == ''){
            unset($data['id']);
            $data['mallId'] = $this->mallId;
            $data['createTime'] = time();
            $data['viralAmount'] = $data['viralAmount'] * 100;
            $res_id = $this->mall_model->insert_good($data);
            if($res_id){
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '商品名称：'.$data['goodsName'].'，ID：'.$res_id;
                    $logInfo ['op'] = $this->log_record->New;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                     log_message('error','mch_log_error:'.$e->getMessage()); 
                }
            }else{
                $this->output->set_content_type('application/json')->set_output(ajax_resp([],'商品新建失败'),17801);
            }
        }else{
            unset($data['id']);
            $data['updateTime'] = time();
            $data['mallId'] = $this->mallId;
            $data['createTime'] = time();
            unset($data['exchangeType']);
            unset($data['viralGoods']);
            unset($data['viralPlatform']);
            unset($data['viralAmount']);
            $res = $this->mall_model->update_good($id,$data);
            if($res){
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '商品名称：'.$data['goodsName'].'，ID：'.$id;
                    $logInfo ['op'] = $this->log_record->Update;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                     log_message('error','mch_log_error:'.$e->getMessage()); 
                }
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
            }else{
                $this->output->set_content_type('application/json')->set_output(ajax_resp([],'商品更新失败'),17802);
            }
        }
        if($newid == ''|| $newid == null){
            foreach ($array as $key => $value) {
                if($key == 0){
                    $default = 1;
                }else{
                    $default = 0;
                }
                $this->mall_model->update_img($res_id,$value,$default);
            }
        }else{
            $res_d_i = $this->mall_model->delete_img($newid);
            if($res_d_i){
                foreach ($array as $key => $value) {
                    if($key == 0){
                        $default = 1;
                        
                    }else{
                        $default = 0;
                    }
                    $this->mall_model->update_img($newid,$value,$default);
                }
            }
        }
    }
    /**
     * 请求商品图片
     */
    public function get_good_images() {
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $goodId = $this->input->post('id');
        $res = $this->mall_model->get_images($goodId);
        $array = array();
        if($res){
            foreach ($res as $key => $value) {
                array_push($array, $value->path);
            }
            $this->output->set_content_type('application/json')->set_output(ajax_resp([$array]));
        }
    }
    /**
     * 请求商品信息
     */
    public function get_goods() {
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
            // $alldata=["data"=>null];
        }
        $mallId = $this->mallId;
        $res = $this->mall_model->get_goods($mallId);
        $data = array();
        $array = array();
        foreach ($res as $k => $v){
            array_push($data, $res[$k]);
        }
        $alldata=["data"=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($alldata));
    }
    /**
     * 获取订单备注
     */
    public function get_remark(){
        $id = $this->input->post('id');
        try {
            $res = $this->mall_model->get_by_id($id);
            if (isset($res)) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([$res]));
            }
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'获取订单信息失败！', 'errcode'=>1]));
        }
    }
    /**
     * 订单备注界面
     */
    public function remark(){
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $mallId = $this->mallId;
        $id = $this->input->post('id');
        $reMark = $this->input->post('textarea');
        $ordernum = $this->input->post('ordernum');
        try {
            $res = $this->mall_model->remark($mallId, $id, $reMark);
            if ($res) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '对订单号为'.$ordernum.'的订单执行【去发货】操作';
                    $logInfo ['op'] = $this->log_record->Send;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage()); 
                }
            }
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
        }
    }
    /**
     * 删除商品
     */
    public function delete(){
        if($this->mallId == null){
            exit('该企业的积分商城未开启!');
        }
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $res = $this->mall_model->update_delete($id);
        if($res){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
            try{
                $logInfo = [];
                $logInfo ['info'] = '商品名称：'.$name.'，ID：'.$id;
                $logInfo ['op'] = $this->log_record->Delete;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
            }catch (Exception $e){
                 log_message('error','mch_log_error:'.$e->getMessage()); 
            }
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'商品删除失败'),17802);
        }
    }
    /**
     * 订单请求页面 
     */
    public function orders(){
        if($this->mallId == null){
            $data = [
                "isopen"=>false
            ];
            $this->load->view('mall_orders',['sdata'=>$data]);
            // exit();
            return;
        }else{
            $ssdata = ["isopen"=>true];
        }
        $mallId = $this->mallId;
        $res = $this->mall_model->get_orders($mallId);
        $status = $this->uri->segment(4);
        $timedate = $this->uri->segment(6);
        // $status = $this->input->post('status');
        // $timedate = $this->input->post('timedate');
        if($this->uri->segment(3) == 'ordernum'){
            $orderNum = $this->uri->segment(4);
            if($orderNum){
                // try {
                    $res = $this->mall_model->get_search_order($orderNum,$mallId);
                    if (!empty($res)) {
                        $btn_data = array(
                        'status'=>'',
                        'timedate'=>''
                        );
                        $array = [];
                        $array_data = [];
                        $data = [];
                        foreach ($res as $key => $value) {
                            array_push($array, $res[$key]->oid);
                        }
                        $get_data = array_unique($array);
                        $count = count($get_data);
                        $get = array_values($get_data);
                        for($i=0;$i<$count;$i++){
                            foreach ($res as $key => $value) {
                                if($get[$i] == $value->oid){
                                        $id = $get[$i];
                                        $array_data[$id][] = (array)$value;
                                }
                            }
                        }
                        // $this->output->set_content_type('application/json')->set_output(ajax_resp([$alldata]));
                        $this->load->view('mall_orders',['sdata'=>$ssdata,'data'=>$array_data,'btn_data'=>$btn_data,'search'=>true]);
                    }else{
                        $btn_data = array(
                        'status'=>'',
                        'timedate'=>''
                        );
                        $this->load->view('mall_orders',['sdata'=>$ssdata,'data'=>[],'btn_data'=>$btn_data,'search'=>false]);
                    }
            }
            return;
        }
        if(!is_numeric($timedate)){
            $td = true;
        }else{
            $td = false;
        }
        if(is_numeric($status)){
            $btn_data = array(
                'status'=>$status,
                'timedate'=>$timedate
                );
            // 时间
            // 0 全部;1 今天;2昨天;3 自定义
            $time = time();
            $today = date('Y-m-d',$time);
            $temp_time = time() - 60*60*24;
            $temp_time1 = time() + 60*60*24;
            $temp_time2 = time() - 60*60*24*2;
            $temp_time3 = time() - 60*60*24*8;
            $yestoday = date('Y-m-d',$temp_time);
            $tomorrow = date('Y-m-d',$temp_time1);
            $seven_days = date('Y-m-d',$temp_time3);
            //the day before yestoday
            $tdby = date('Y-m-d',$temp_time2);
            //所有
            if($timedate == 0){
                $res = $this->mall_model->get_orders_all($mallId,$status);
                $array = [];
                $array_data = [];
                $data = [];
                foreach ($res as $key => $value) {
                    // echo $res[$key]->oid;
                    // echo "==";
                    array_push($array, $res[$key]->oid);
                }
                $get_data = array_unique($array);
                $count = count($get_data);
                $get = array_values($get_data);
                for($i=0;$i<$count;$i++){
                    foreach ($res as $key => $value) {
                        if($get[$i] == $value->oid){
                                $id = $get[$i];
                                $array_data[$id][] = (array)$value;
                        }
                    }
                }
                // $this->output->set_content_type('application/json')->set_output(ajax_resp([$array_data]));
            }
            //今天
            if($timedate == 1){
                $res = $this->mall_model->get_orders_today($mallId, $status, $today);
                $array = [];
                $array_data = [];
                $data = [];
                foreach ($res as $key => $value) {
                    array_push($array, $res[$key]->oid);
                }
                $get_data = array_unique($array);
                $count = count($get_data);
                $get = array_values($get_data);
                for($i=0;$i<$count;$i++){
                    foreach ($res as $key => $value) {
                        if($get[$i] == $value->oid){
                                $id = $get[$i];
                                $array_data[$id][] = (array)$value;
                        }
                    }
                }
            }
            //昨天
            if($timedate == 2){
                $res = $this->mall_model->get_orders_tdby($mallId, $status, $today, $tdby);
                $array = [];
                $array_data = [];
                $data = [];
                foreach ($res as $key => $value) {
                    array_push($array, $res[$key]->oid);
                }
                $get_data = array_unique($array);
                $count = count($get_data);
                $get = array_values($get_data);
                for($i=0;$i<$count;$i++){
                    foreach ($res as $key => $value) {
                        if($get[$i] == $value->oid){
                                $id = $get[$i];
                                $array_data[$id][] = (array)$value;
                        }
                    }
                }
            }
            if($timedate == 3){
                $res = $this->mall_model->get_orders_seven($mallId, $status, $seven_days);
                $array = [];
                $array_data = [];
                $data = [];
                foreach ($res as $key => $value) {
                    array_push($array, $res[$key]->oid);
                }
                $get_data = array_unique($array);
                $count = count($get_data);
                $get = array_values($get_data);
                for($i=0;$i<$count;$i++){
                    foreach ($res as $key => $value) {
                        if($get[$i] == $value->oid){
                                $id = $get[$i];
                                $array_data[$id][] = (array)$value;
                        }
                    }
                }
            }
            if($td){
                // var_dump($timedate);
                $str = explode('.',$timedate);
                $t1 = $str[0];
                $t2 = $str[1];
                if($t1>$t2){
                    die('非法参数！');
                }
                $res = $this->mall_model->get_orders_diy($mallId, $status, $t1, $t2);
                $array = [];
                $array_data = [];
                $data = [];
                foreach ($res as $key => $value) {
                    array_push($array, $res[$key]->oid);
                }
                $get_data = array_unique($array);
                $count = count($get_data);
                $get = array_values($get_data);
                for($i=0;$i<$count;$i++){
                    foreach ($res as $key => $value) {
                        if($get[$i] == $value->oid){
                                $id = $get[$i];
                                $array_data[$id][] = (array)$value;
                        }
                    }
                }
            }
        }
        if($timedate == null){
            $btn_data = array(
                'status'=>'',
                'timedate'=>''
                );
            $array = [];
            $array_data = [];
            $data = [];
            foreach ($res as $key => $value) {
                array_push($array, $res[$key]->oid);
            }
            // $d = [];
            $get_data = array_unique($array);
            $count = count($get_data);
            $get = array_values($get_data);
            for($i=0;$i<$count;$i++){
                foreach ($res as $key => $value) {
                    if($get[$i] == $value->oid){
                            $id = $get[$i];
                            $array_data[$id][] = (array)$value;
                    }
                }
            }
        }
        $sdata = [
            "isopen"=>true
        ];
        $this->load->view('mall_orders',['data'=>$array_data,'sdata'=>$sdata,'btn_data'=>$btn_data]);
    }
    /**
     * 确认收货
     */
    public function confirm_get(){
        $id = $this->input->post('id');
        $ordernum = $this->input->post('ordernum');
        if($id == null || $id == ''){
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['errmsg'=>'操作失败','errcode'=>1]));
        }
        try {
            $res = $this->mall_model->confirm_get($id);
            if ($res) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '对订单号为'.$ordernum.'的订单执行【确认收货】操作';
                    $logInfo ['op'] = $this->log_record->Confirm;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage()); 
                }
            }
            
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
        }
    }
    /**
     * 完成订单
     */
    public function end_order(){
        $id = $this->input->post('id');
        $ordernum = $this->input->post('ordernum');
        if($id == null || $id == ''){
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['errmsg'=>'操作失败','errcode'=>1]));
            return;
        }
        $get_mallId = $this->mall_model->get_by_id($id);
        if($this->mallId != $get_mallId->mallId){
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['errmsg'=>'请求非法！','errcode'=>1]));
            return;
        }
        try {
            $res = $this->mall_model->end_order($id);
            if ($res) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
                try{
                    $logInfo = [];
                    $logInfo ['info'] = '对订单号为'.$ordernum.'的订单执行【完成订单】操作';
                    $logInfo ['op'] = $this->log_record->End;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Mall);
                }catch (Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage()); 
                }
            }
            
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
        }
    }
    /**
     * 搜索订单
     */
    public function get_search_order(){
        $mallId = $this->mallId;
        $orderNum = $this->input->post('orderNum');
        if($orderNum){
            try {
                $res = $this->mall_model->get_search_order($orderNum,$mallId);
                if ($res) {
                    $btn_data = array(
                    'status'=>'',
                    'timedate'=>''
                    );
                $array = [];
                $array_data = [];
                $data = [];
                foreach ($res as $key => $value) {
                    array_push($array, $res[$key]->oid);
                }
                // $d = [];
                $get_data = array_unique($array);
                $count = count($get_data);
                $get = array_values($get_data);
                for($i=0;$i<$count;$i++){
                    foreach ($res as $key => $value) {
                        if($get[$i] == $value->oid){
                                $id = $get[$i];
                                $array_data[$id][] = (array)$value;
                        }
                    }
                }
                    // $this->output->set_content_type('application/json')->set_output(ajax_resp([$alldata]));
                $this->load->view('mall_orders',['data'=>$array_data,'btn_data'=>$btn_data]);
                }
            } catch (Exception $e) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>$e->getMessage(), 'errcode'=>1]));
            }
        }

    }
    /**
     * 上传处理
     */
    public function file_upload() {
        // $path = "uploads/";
        $path= '/files/public/'.$this->mchId;
        $extArr = array("jpg","jpeg", "png", "gif");
        $realpath = getcwd().$path;
        if (!file_exists($realpath)) {
            mkdir($realpath,0777);
        }
        if (@is_writable($realpath) === false) {
            exit("返回错误: 上传目录没有写权限。($path)");
        }
        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
            $name = $_FILES['filepath']['name'];
            $size = $_FILES['filepath']['size'];
            // print_r($_FILES['filepath']);
            // var_dump($_FILES['filepath']);
            if(empty($name)){
                echo '请选择要上传的图片';
                exit;
            }
            $ext = extend($name);
            if(!in_array($ext,$extArr)){
                echo '图片格式错误！';
                exit;
            }
            if($size>(100*1024)){
                echo '图片大小不能超过100KB';
                exit;
            }
            $image_name = time().rand(100,999).".".$ext;
            $tmp = $_FILES['filepath']['tmp_name'];
            if(move_uploaded_file($tmp, $$realpath.$image_name)){
                // echo '<img src="'.$path.$image_name.'"  class="preview">';
                echo $$realpath.$image_name;
            }else{
                echo '上传出错了！';
            }
            exit;
        }
        exit;
    }
    /**
     * ajax更新积分
     */
    public function ajax_update_price() {
        $id = $this->input->post('id');
        $integralname = $this->input->post('integralname');
        $price = $this->input->post('price');
        if(!preg_match("/^[0-9]{1,10}$/",$price)){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'积分更新失败'),17802);
        }
        if($integralname == 'oPrice'){
            $data = array(
                    'oPrice'=>$price
                );
        }
        if($integralname == 'price'){
            $data = array(
                    'price'=>$price
                );
        }
        $res = $this->mall_model->update_good($id,$data);
        if($res){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'积分更新失败'),17802);
        }
    }
     /**
     * 图片上传方法
     */
    public function upload() {
        $filepath= '/files/public/'.$this->mchId;
        echo upload_file('gif|jpg|jpeg|png',500,$filepath);
    }
    /**
     * 名称过滤
     */
    function extend($file_name){
        $extend = pathinfo($file_name);
        $extend = strtolower($extend["extension"]);
        return $extend;
    }
}