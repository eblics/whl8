<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Card extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('card_model');
        $this->load->model('merchant_model');
        $this->mchId=$this->session->userdata('mchId');
        $this->load->library('log_record');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }

    /**
     * 乐券列表页
     */
    public function lists() {
        $this->load->view('card_lists');
    }
    /**
     * 乐券添加页
     */
    public function add(){
        // $fid = get_current_router(3);
        $fid = $this->input->get('fid');
        $data = ['mchId'=>$this->mchId,'id'=>null,'imgUrl'=>'','title'=>'','totalNum'=>'','probability'=>'','description'=>'','parentId'=>$fid,'priority'=>0];
        $title = "添加";
        // 获取券组信息
        $c_group = $this->card_model->get_group($this->mchId);
        $data['cgroup'] = $c_group;
        $data['create'] = true;
        $data1 = (object)$data;

        $mchId = $this->session->userdata('mchId');
        $this->load->model('mall_model', 'mall');
        $mall = $this->mall->get_mallId_by_mchId($mchId);
        if (! isset($mall)) {
            $data1->goods = [];
            $this->load->view('card_edit', ['title' => $title, 'fid' => $fid, 'data' => $data1]);
        } else {
            // 获取商城中所有可使用乐券兑换的礼品
            $goods = $this->mall->get_goods($mall->id, true);
            $data1->goods = $goods;
            $this->load->view('card_edit', ['title' => $title, 'fid' => $fid, 'data' => $data1]);
        }
    }
    /**
     * 乐券编辑页
     */
    public function edit(){
        $id = get_current_router(3);
        $mchId = $this->mchId;
        $title = "编辑";
        if(isset($id)){
            $data = $this->card_model->get_by_id($id,$mchId);
        }
        if($data){
            if($data->mchId!=$mchId) exit('无权操作');
        }else{
            exit('数据不存在');
        }
        // 获取券组信息
        $c_group = $this->card_model->get_group($mchId);
        $data1 = (array)$data;
        $data1['cgroup'] = $c_group;
        $data2 = (object)$data1;

        $mchId = $this->session->userdata('mchId');
        $this->load->model('mall_model', 'mall');
        $mall = $this->mall->get_mallId_by_mchId($mchId);
        if (!isset($mall)) {
             $data2->goods = [];
            $this->load->view('card_edit', ['title'=>$title,'fid'=>null,'data'=>$data2]);
        } else {
            // 获取商城中所有可使用乐券兑换的礼品
            $goods = $this->mall->get_goods($mall->id, true);
            $data2->goods = $goods;
            $this->load->view('card_edit', ['title'=>$title,'fid'=>null,'data'=>$data2]);
        }
    }
    /**
     * 获取更新信息
     */
    public function get_update(){
        $mchId = $this->mchId;
        $postData = $this->input->post();
        if(isset($postData['title'])){
            $data['title']=$postData['title'];
        }
        if(isset($postData['imgUrl'])){
            $data['imgUrl']=$postData['imgUrl'];
        }
        if(isset($postData['parentId'])){
            $data['parentId']=$postData['parentId'];
        }
        if(isset($postData['totalNum'])){
            $data['totalNum']=$postData['totalNum'];
        }
        if(isset($postData['probability'])){
            $data['probability']=$postData['probability'];
        }
        if(isset($postData['description'])){
            $data['description']=$postData['description'];
        }

        // ======================== Added by shizq start ========================
        // 是否是第三方平平台的优惠券
        $isThirdParty = $this->input->post('thirdParty');
        // 卡券种类，可能的值：1、有赞；2、积分
        $cardType = $this->input->post('cardType');
        $data['cardType'] = $cardType;
        if ($isThirdParty) {
            /**
             * @var string 优惠券码组id（有赞平台）
             */
            $couponGroupId = $this->input->post('couponGroupId');
            $data['couponGroupId'] = $couponGroupId; 
        } else {
            $data['couponGroupId'] = NULL;
        }
        if ($cardType == 2) {
            $data['pointQuantity'] = $this->input->post('pointQuantity');
        }
        // 是否可转移
        $allowTransfer = $this->input->post('allowTransfer');
        if ($allowTransfer === '1') {
            $data['allowTransfer'] = 1;
        } else {
            $data['allowTransfer'] = 0;
        }

        // ========================  Added by shizq end  ========================
        $id = $this->input->post('id');
        if($id == ''){
            $id = null;
        }
        $ndata=$this->card_model->get($id);
        if($ndata){
            if($ndata->mchId!=$mchId) exit('无权操作');
        }

        // else{
        //     exit('数据不存在');
        // }
        if(isset($id)){
            $getdata = $this->card_model->get_by_id($id,$mchId);
            $oldtotalNum = $getdata->totalNum;
            $newtotalNum = $postData['totalNum'];
            $remainNum = $getdata->remainNum;
            $scanNum = $oldtotalNum - $remainNum;
            if($newtotalNum < $scanNum){
                $idres=[
                    'errorCode'=>3,
                    'errorMsg'=>''
                ];
                echo json_encode($idres);
                return;
            }
            //策略操作记录
            try{
                if(!empty($id)){
                    $strategy_log=(object)['type'=>2,'opration'=>'update_sub','data'=>''];
                    $strategy_log->data=$this->card_model->get($id);
                }
            }catch(Exception $e){
                log_message('error','strategy_log_error:'.$e->getMessage());
            }
            //策略操作记录 end
            //总数量被修改的情况
            if($postData['totalNum'] != $getdata->totalNum){
                // $hadNum被扫   =总计-剩余 
                $hadNum = $getdata->totalNum - $getdata->remainNum;
                //临时 剩余 = 被修改的总数-被扫的.
                $data['remainNum'] = $postData['totalNum'] - $hadNum;
                $data['mchId'] = $mchId;
                debug('update card - begin');
                debug('data: '. json_encode($data));
                $status = $this->card_model->update_card($id,$data);
                if($status){
                    $result=[
                        'errorCode'=>0,
                        'errorMsg'=>''
                    ];
                }else{
                    $result=[
                        'errorCode'=>1,
                        'errorMsg'=>'添加失败'
                    ];
                }
                debug('update card - end');
            }else{
                $data['mchId'] = $mchId;
                //命中概率
                if(isset($postData['probability'])){
                    $d['probability']=$postData['probability'];
                }
                if(isset($postData['title'])){
                    $d['title'] = $postData['title'];
                }
                if(isset($postData['description'])){
                    $d['description'] = $postData['description'];
                }
                if(isset($postData['imgUrl'])){
                    $d['imgUrl'] = $postData['imgUrl'];
                }
                if(isset($postData['pointQuantity'])){
                    $d['pointQuantity'] = $postData['pointQuantity'];
                }
                $d['remainNum'] = $getdata->remainNum;
                $d['couponGroupId'] = $data['couponGroupId'];
                // $d['goodsId'] = $data['goodsId'];
                $d['allowTransfer'] = $data['allowTransfer'];
                $status = $this->card_model->update_card($id,$d);
                if($status){
                    $result=[
                        'errorCode'=>0,
                        'errorMsg'=>''
                    ];
                }else{
                    $result=[
                        'errorCode'=>1,
                        'errorMsg'=>'添加失败'
                    ];
                }
            }
            
            //策略操作记录
            try{
                if(isset($id) && $status){
                    $this->load->model('strategy_log_model');
                    $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
                }
            }catch(Exception $e){
                log_message('error','strategy_log_error:'.$e->getMessage());
            }
            //策略操作记录 end
            if($result['errorCode']==0){//-------ccz,日志，
                try{
                    if($postData['totalNum'] != $getdata->totalNum){
                         $logInfo = (array)$data;
                    }else{
                        $logInfo = (array)$d;
                    }
                    $logInfo ['id'] = $id;
                    $logInfo ['op'] = $this->log_record->Update;
                    $cardgroup = $this->card_model->getgroup_by_cid($getdata->parentId);//活动其券组的信息
                    $logInfo ['grouptitle'] = $cardgroup->title;
                    $logInfo ['groupdesc'] = $cardgroup->description;
                    $logInfo ['isGroup'] = 0;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Card);
                }catch(Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage());
                }
            }
            echo json_encode($result);
            return;
        }else{
            // $newdata = $this->card_model->get_by_id($id,$mchId);
            $data['mchId'] = $mchId;
            if(isset($postData['totalNum'])){
                $data['remainNum'] = $postData['totalNum'];
            }
            $insert_id = $this->card_model->add_card($data);
            if($insert_id){
                $result=[
                    'errorCode'=>0,
                    'errorMsg'=>''
                ];
            }else{
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'添加失败'
                ];
            }
            if($result['errorCode']==0){//-------ccz,日志，
                try{
                    $logInfo = (array)$data;
                    $logInfo ['id'] = $insert_id;
                    $logInfo ['op'] = $this->log_record->Add;
                    $cardgroup = $this->card_model->getgroup_by_cid($data['parentId']);//活动其券组的信息
                    $logInfo ['grouptitle'] = $cardgroup->title;
                    $logInfo ['groupdesc'] = $cardgroup->description;
                    $logInfo ['isGroup'] = 0;
                    $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Card);
                }catch(Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage());
                }
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }
    }
    /**
     * 券组+乐券列表数据
     */
    public function data() {
        $allData = array();
        $getdata = $this->card_model->get_group($this->mchId);
        $cardata = $this->card_model->get_by_mchid($this->mchId);
        foreach($getdata as $k=>$v){
            array_push($allData,$getdata[$k]);
            foreach ($cardata as $key => $value) {
                if($v->id == $value->parentId){
                    array_push($allData,$cardata[$key]);
                }
            }
        }
        $data = ["data"=>$allData];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 乐券删除
     */
    public function del(){
        // $mchId = $this->session->userdata('mchId');
        $id=$this->input->post('id');
        // $r = $this->card_model->exists_cid($mchId,$id);
        //校验
        $data=$this->card_model->get($id);
        // if(!$r){
        //     $result=[
        //         'errcode' =>12007,
        //         'errmsg' =>'存在'
        //     ];
        // }
        if($data->mchId != $this->mchId){
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'无权操作'
            ];
            echo json_encode($result);
            return;
        }
        //策略操作记录
        try{
            $strategy_log=(object)['type'=>2,'opration'=>'delete_sub','data'=>''];
            $strategy_log->data=$this->card_model->get($id);
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $delRes = $this->card_model->del_card($id);
        //策略操作记录
        try{
            if($delRes){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        if($delRes){
            $result=[
                'errorCode'=>0,
                'errorMsg'=>''
            ];
        }else{
            $result=[
                'errorCode'=>1,
                'errorMsg'=>$delRes
            ];
        }
        if($result['errorCode']==0){//-------ccz,日志，
            try{
                $logInfo = (array)$data;
                $logInfo ['id'] = $id;
                $logInfo ['op'] = $this->log_record->Delete;
                $cardgroup = $this->card_model->getgroup_by_cid($logInfo['parentId']);//活动其券组的信息
                $logInfo ['grouptitle'] = $cardgroup->title;
                $logInfo ['groupdesc'] = $cardgroup->description;
                $logInfo ['isGroup'] = 0;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Card);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        echo json_encode($result);
        return;
    }
    /**
     * 券组添加
     */
    public function addgroup(){
        $title = "新增";
        $data = (object)[
            'mchId'=>$this->mchId,
            'id'=>null,
            'title'=>null,
            'description'=>null,
            'imgUrl'=>null,
            'hasGroupBonus' => 0,
            'bonusType' => 0,
            'bonusQuantity' => 0,
            'priority'=>0,
            'rowStatus'=>0
        ];
        $this->load->view('card_editgroup',['title'=>$title,'data'=>$data]);
    }
    /**
     * 券组编辑
     */
    public function editgroup(){
        $cid = get_current_router(3);
        $title = "编辑";
        $res = $this->card_model->get_cardgroup($cid);
        $this->load->view('card_editgroup',['title'=>$title,'data'=>$res]);
    }
    /**
     * 券组更新/新增
     */
    public function save_cgroup(){
        $data = $this->input->post('data');
        $id = $data['id'];
        $thisid = $this->card_model->get_cardgroup($id);
        if(!empty($id) && $this->mchId != $thisid->mchId){
            exit("数据不存在");//实际上是非法请求
        }
        $ginfo = [
            'id'=>$id,
            'title'=>$data['title'],
            'priority'=>$data['priority'],
            'description'=>$data['description'],
            'imgUrl'=>$data['imgUrl'],
            'hasGroupBonus' => $data['hasGroupBonus'],
            'bonusType' => $data['bonusType'],
            'bonusQuantity' => $data['bonusQuantity'],
        ];
        if ($data['hasGroupBonus'] === '1') {
            if (intval($data['bonusQuantity']) <= 0) {
                $result = [
                    'errcode'=>1,
                    'errmsg'=>'卡组奖励数量必须大于零'
                ];
                echo json_encode($result);
                return;
            }
        }
        //策略操作记录
        try{
            if(!empty($id)){
                $strategy_log=(object)['type'=>2,'opration'=>'update','data'=>''];
                $strategy_log->data=$this->card_model->get_cardgroup($id);
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        if(empty($id)){
            $ginfo = [
                'id'=>$id,
                'title'=>$data['title'],
                'priority'=>$data['priority'],
                'description'=>$data['description'],
                'imgUrl'=>$data['imgUrl'],
                'hasGroupBonus' => $data['hasGroupBonus'],
                'bonusType' => $data['bonusType'],
                'bonusQuantity' => $data['bonusQuantity'],
                'mchId'=>$this->mchId,
                'rowStatus'=>0
            ];
            $res = $this->card_model->add_group($ginfo);
        }else{
            $ginfo = [
                'title'=>$data['title'],
                'priority'=>$data['priority'],
                'description'=>$data['description'],
                'imgUrl'=>$data['imgUrl'],
                'hasGroupBonus' => $data['hasGroupBonus'],
                'bonusType' => $data['bonusType'],
                'bonusQuantity' => $data['bonusQuantity'],
                'mchId'=>$this->mchId
            ];
            $res = $this->card_model->save_group($id,$ginfo);
        }
        if($res){
            $result = [
                'errcode'=>0,
                'errmsg'=>''
            ];
        }else{
            $result = [
                'errcode'=>1,
                'errmsg'=>'新增失败'
            ];

        }
        //策略操作记录
        try{
            if(!empty($id) && $result['errcode']==0){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        if($result['errcode']==0){//-------ccz,日志，
            try{
                $logInfo = (array)$ginfo;
                if($id!=NULL && !empty($id)){
                    $logInfo ['id'] = $id;
                    $logInfo ['op'] = $this->log_record->Update;
                }else{
                    $logInfo ['id'] = $res;
                    $logInfo ['op'] = $this->log_record->New;
                }
                $logInfo ['isGroup'] = true;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Card);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        echo json_encode($result);
        return;
    }
    /**
     * 券组删除
     */
    public function del_group(){
        $mchId = $this->session->userdata('mchId');
        $cid = $this->input->post('cid');
        $res = $this->card_model->getgroup_by_cid($cid);
        $r = $this->card_model->exists_id($mchId,$cid);
        $activity_id = array();
        $activity_array = array();
        foreach ($r as $key => $value) {
            array_unshift($activity_array, $value->id);
            $activity_id = implode(",", $activity_array);
        }
        foreach ($r as $k => $v) {
            if($v == null){
                $result = [
                    'errcode'=>0,
                    'errmsg'=>''
                ];
                echo json_encode($result);
                return;
            }else{
                $result=[
                    'errcode' =>12007,
                    'errmsg' =>'已启用的活动[id:'.$activity_id.']绑定了此乐券，无法删除！
    '
                ];
                echo json_encode($result);
                return;
            }
        }
        if($res->mchId != $this->mchId){
            $result=[
                'errcode'=>1,
                'errmsg'=>'无权操作'
            ];
            echo json_encode($result);
            return;
        }
        $exists = $this->card_model->exists_card($cid,$this->mchId);
        if(isset($exists)){
            $result = [
                'errcode'=>2,
                'errmsg'=>'该券组存在子券!'
            ];
            echo json_encode($result);
            return;
        }
        $this->load->model('mixstrategy_model');
        $allMix=$this->mixstrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allMix as $k=>$v){
            if($v->strategyId==$cid && $v->strategyType==2){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'组合策略[id:'.$v->parentId.']绑定了此乐券组，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('multistrategy_model');
        $allMulti=$this->multistrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allMulti as $k=>$v){
            if($v->strategyId==$cid && $v->strategyType==2){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'叠加策略[id:'.$v->parentId.']绑定了此乐券策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('accumstrategy_model');
        $allAccum=$this->accumstrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allAccum as $k=>$v){
            if($v->strategyId==$cid && $v->strategyType==2){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'累计策略[id:'.$v->parentId.']绑定了此乐券策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        // 判断是否有大奖绑定到此策略，有则不允许删除
        $bonus = $this->accumstrategy_model->getBonusCountByStratetyId(2, $cid);
        if (isset($bonus)) {
            $result=[
                'errcode'=>1,
                'errmsg'=>'累计策略[id:'.$bonus->accumStrategyId.']的大奖设置绑定了此乐券策略，无法删除！'
            ];
            echo json_encode($result);
            return;
        }
        //策略操作记录
        try{
            $strategy_log=(object)['type'=>2,'opration'=>'delete','data'=>''];
            $strategy_log->data=$this->card_model->get_cardgroup($cid);
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $del = $this->card_model->del_group($cid);
        //策略操作记录
        try{
            if($del){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        if(isset($del)){
            $result = [
                'errcode'=>0,
                'errmsg'=>''
            ];
        }else{
            $result = [
                'errcode'=>3,
                'errmsg'=>'删除失败'
            ];

        }
        if($result['errcode']==0){//-------ccz,日志，
            try{
                $logInfo = (array)$res;
                $logInfo ['id'] = $cid;
                $logInfo ['op'] = $this->log_record->Delete;
                $logInfo ['isGroup'] = true;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Card);
            }catch(Exception $e){
		     log_message('error','mch_log_error:'.$e->getMessage());
		}
        }
        echo json_encode($result);
        return;
    }
    /**
     * 图片上传方法
     */
    public function upload() {
        $filepath= '/files/public/'.$this->mchId;
        echo upload_file('gif|jpg|png',500,$filepath);
    }

    /**
     * 获取第三方卡券组列表
     *
     * @author shizq
     * @return json
     */
    public function coupons() {
        $cardType = $this->input->get('card_type');
        if ($cardType == 1) {
            // 获取有赞平台卡券
            $this->load->helper('youzan'); 
            $result = youzan_api($this->session->userdata('mchId'), 'kdt.ump.coupons.unfinished.all');
            if (isset($result['error_response'])) {
                debug("get coupons faild: " . $result['error_response']['msg']);
                $this->output->set_content_type('application/json')->set_output(ajax_resp([], $result['error_response']['msg'], 30001));
            } else {
                debug("get coupons success" . json_encode($result));
                $this->output->set_content_type('application/json')->set_output(ajax_resp($result['response']['coupons']));
            }
        } else {
            debug("get coupons faild: unknow cardType $cardType");
            $this->output->set_content_type('application/json')->set_output(ajax_resp([], '未知的卡券类型', 30002));
        }
    }

    //乐券中奖名单
    public function winlist($id){
        if(!isset($id)){
            echo 'no access';
        }
        // 查询乐券信息
        $data=$this->card_model->get($id);
        $this->load->view('card_winlist',$data);
    }
    //获取乐券策略中奖列表
    public function get_winlist(){
        $id=$this->input->post('id');
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->card_model->get_winlist($this->mchId,$id,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //处理中奖的用户
    public function deal_with(){
        $r = array('res'=>0);
        $id = $this->input->post('id');
        $data = $this->card_model->deal_with($id);
        $data = (object)['data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    //下载中奖名单
    public function down_winlist($id=null,$title=null){
        if(!isset($id)&&!isset($title)){
            echo 'no access';
        }
        $data=$this->card_model->get_winlist($this->mchId,$id);
        $output=iconv("UTF-8","GBK",'用户ID,微信昵称,真实姓名,手机号码,收货地址,中奖时间,中奖地点,状态,处理状态');
        $output.="\r\n";
        for($i=0;$i<count($data);$i++){
            if($data[$i]['sended']==1){
                $status='已发放';
            }else{
                $status='未发放';
            }
            if($data[$i]['aprocessing']==1){
                $processing='已处理';
            }else{
                $processing='未处理';
            }
            $nickname=addslashes(str_replace(",","",iconv("UTF-8","GBK",$data[$i]['nickName'])));
            $nickname=str_replace(array("\r\n", "\r", "\n"), "", str_replace(PHP_EOL,'',str_replace("=","",str_replace(" ","",$nickname))));
            $output.=$data[$i]['userId'].','. $nickname.','.iconv("UTF-8","GBK",$data[$i]['realName']).','.$data[$i]['mobile'].','.iconv("UTF-8","GBK",$data[$i]['address']).','.$data[$i]['date'].','.iconv("UTF-8","GBK",$data[$i]['area']).','.iconv("UTF-8","GBK",$status).','.iconv("UTF-8","GBK",$processing)."\r\n";
        }
        $this->output->set_content_type('application/octet-stream')
        ->set_header('Content-Disposition:attachment;filename='.urldecode($title).'_中奖名单.csv')
        ->set_output($output);
    }

    /**
     * -----------------------------------------
     * 乐券持有者界面
     * 
     * @param int $cardId 乐券编号
     * @return view
     */
    public function holder($cardId = NULL) {
        if (is_null($cardId)) {
            exit('<script>alert("乐券不存在！");history.back();</script>');
        } else {
            $card = $this->card_model->get($cardId);
            if (! isset($card)) {
                exit('<script>alert("乐券不存在！");history.back();</script>');
            }
            $viewData = [];
            $viewData['card_id'] = $cardId;
            $viewData['card_name'] = $card->title;
            $this->load->view('card_holder', $viewData);
        }
    }

    /**
     * -----------------------------------------
     * 乐券持有者列表数据
     * 
     * @param int $cardId 乐券编号
     * @param int $page 页码
     * @return view
     */
    public function holder_list() {
        $cardId = $this->input->get('card_id');
        $page = $this->input->get('start');
        $pageSize = $this->input->get('length');
        $draw = $this->input->get('draw');
        if (! isset($page)) {
            $page = 0;
        }
        $mchId = $this->session->userdata('mchId');
        $respData = $this->card_model->cardHolderList($cardId, $mchId, $page, $pageSize);
        $data = ['data' => $respData['data_list'], 'error' => NULL, 'draw' => $draw];
        $totalNum = 0;
        foreach ($respData['total_num_obj'] as $value) {
            $totalNum += $value->total_num;
        }
        $data['recordsTotal'] = $totalNum;
        $data['recordsFiltered'] = $totalNum;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * -----------------------------------------
     * 导出乐券持有者列表数据
     * 
     * @param int $cardId 乐券编号
     * @param int $page 页码
     * @return view
     */
    public function down_card_holder($cardId = NULL) {
        if (is_null($cardId)) {
            exit('<script>alert("乐券不存在！");history.back();</script>');
        } else {
            $card = $this->card_model->get($cardId);
            if (! isset($card)) {
                exit('<script>alert("乐券不存在！");history.back();</script>');
            }
            $mchId = $this->session->userdata('mchId');
            $respData = $this->card_model->cardHolderList($cardId, $mchId, 0, 99999);
            $dataList = $respData['data_list'];
            $output = iconv("UTF-8", "GBK", '用户ID,用户角色,微信昵称,真实姓名,手机号码,持有数量');
            $output .= "\r\n";
            foreach ($dataList as $dataItem) {
                $nickname = addslashes(str_replace(",", "", iconv("UTF-8", "GBK", $dataItem->nickname)));
                $nickname = str_replace(array("\r\n", "\r", "\n"), "", str_replace(PHP_EOL, '', str_replace("=", "", str_replace(" ", "", $nickname))));
                $output .= $dataItem->user_id .',';
                $output .= iconv("UTF-8", "GBK", $dataItem->role_str) .',';
                $output .= $nickname .',';
                $output .= iconv("UTF-8", "GBK", $dataItem->realname) .',';
                $output .= iconv("UTF-8", "GBK", $dataItem->mobile) .',';
                $output .= $dataItem->num ."\r\n";
            }
            $this->output->set_content_type('application/octet-stream')
            ->set_header('Content-Disposition:attachment;filename='.urldecode($card->title).'_持有者名单.csv')
            ->set_output($output);
        }
    }
}
