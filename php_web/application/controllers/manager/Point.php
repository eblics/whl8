<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Point extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->mchId=$this->session->userdata('mchId');
        $this->load->model('point_model');
        $this->load->library('log_record');
    }
    
    /**
     * 积分策略列表页
     */
    public function lists() {
        $this->load->view('point_lists');
    }

    /**
     * 积分策略列表数据
     */
    public function data() {
        header("Content-type",'application/json;charset=utf-8;');
        $allData=[];
        $fData=$this->point_model->get_by_mchid($this->mchId);
        $sData=$this->point_model->get_sub_by_mchid($this->mchId); 
        foreach($fData as $k=>$v){
            array_push($allData,$fData[$k]);
            foreach($sData as $key=>$value){
                if($value->parentId==$v->id){
                    $thisV=$value;
                    $thisV->name='分级积分';
                    $thisV->totalNum=$value->num;
                    array_push($allData,$thisV);
                }
            }
        }
        $data=["data"=>$allData];
        echo json_encode($data);
    }
    /**
     * 积分策略列表数据(不含子积分策略)
     */
    public function datano() {
        header("Content-type",'application/json;charset=utf-8;');
        $allData=$this->point_model->get_by_mchid($this->mchId);
        $data=["data"=>$allData];
        echo json_encode($data);
    }
    
    /**
     * 新建积分策略页
     */
    public function add() {
        $data=(object)[
            'id'=>'',
            'name'=>'',
            'priority'=>0
        ];
        $view=(object)['action'=>'add','title'=>'新建'];
        $this->load->view('point_edit',['data'=>$data,'view'=>$view]);
    }

    /**
     * 修改积分策略页
     */
    public function edit($id=null) {
        if(!isset($id)) exit('参数有误');
        $data=$this->point_model->get($id);
        if($data){
            if($data->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('数据不存在');
        }
        $view=(object)['action'=>'edit','title'=>'修改'];
        $this->load->view('point_edit',['data'=>$data,'view'=>$view]);
    }

    /**
     * 删除策略
     */
    public function del() {
        header("Content-type",'application/json;charset=utf-8;');
        $id=$this->input->post('id');
        $data=$this->point_model->get($id);
        if($data->mchId!=$this->mchId){
            $result=[
                'errcode'=>1,
                'errmsg'=>'无权操作'
            ];
            echo json_encode($result);
            return;
        }
        $sdata=$this->point_model->get_sub_by_mchid($this->mchId);
        foreach($sdata as $k=>$v){
            if($v->parentId==$id){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'存在分级积分，不能删除'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('activity_model');
        $allSubAct=$this->activity_model->get_sub_by_mchid($this->mchId);
        foreach($allSubAct as $k=>$v){
            if($v->detailId==$id && $v->activityType==4 && $v->state==1){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'已启用的活动[id:'.$v->id.']绑定了此积分策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('mixstrategy_model');
        $allMix=$this->mixstrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allMix as $k=>$v){
            if($v->strategyId==$id && $v->strategyType==3){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'组合策略[id:'.$v->parentId.']绑定了此积分策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('multistrategy_model');
        $allMulti=$this->multistrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allMulti as $k=>$v){
            if($v->strategyId==$id && $v->strategyType==3){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'叠加策略[id:'.$v->parentId.']绑定了此积分策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('accumstrategy_model');
        $allAccum=$this->accumstrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allAccum as $k=>$v){
            if($v->strategyId==$id && $v->strategyType==3){
                $result=[
                    'errcode'=>1,
                    'errmsg'=>'累计策略[id:'.$v->parentId.']绑定了此积分策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }

        // 判断是否有大奖绑定到此策略，有则不允许删除
        $bonus = $this->accumstrategy_model->getBonusCountByStratetyId(3, $id);
        if (isset($bonus)) {
            $result=[
                'errcode'=>1,
                'errmsg'=>'累计策略[id:'.$bonus->accumStrategyId.']的大奖设置绑定了此红包策略，无法删除！'
            ];
            echo json_encode($result);
            return;
        }

        //策略操作记录
        try{
            $strategy_log=(object)['type'=>5,'opration'=>'delete','data'=>''];
            $strategy_log->data=$this->point_model->get($id);
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $isdel=$this->point_model->del($id);
        //策略操作记录
        try{
            if($isdel){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        if($isdel){
            $result=[
                'errcode'=>0,
                'errmsg'=>''
            ];
        }else{
            $result=[
                'errcode'=>1,
                'errmsg'=>$isdel
            ];
        }
        

        if ($result ['errcode'] == 0) { // -------ccz,操作日志
        	try {
        		$logInfo = ( array ) $data;
        		$logInfo ['op'] = $this->log_record->Delete;
        		$logInfo ['info'] = '';
        		$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Point );
        	} catch ( Exception $e ) {
        		log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
        	}
        }
        echo json_encode($result);
        return;
    }

    /**
     * 保存积分策略
     */
    public function save(){
        header("Content-type",'application/json;charset=utf-8;');
        $saveData=[
            'id'=>$this->input->post('id'),
            'mchId'=>$this->mchId,
            'name'=>$this->input->post('name'),
            'priority'=>$this->input->post('priority'),
            'updateTime'=>time()
        ];
        if(!empty($saveData['id'])){
            $point=$this->point_model->get($saveData['id']);
            if($saveData['mchId']!=$point->mchId){
                $data=['errcode'=>1,'errmsg'=>'没有权限修改'];
                echo json_encode($data);
                return;
            }
        }else{
            $saveData['createTime']=time();
        }
        if(!isset($saveData['name'])){
            $data=['errcode'=>1,'errmsg'=>'提交数据有误'];
            echo json_encode($data);
            return;
        }
        //策略操作记录
        try{
            if(!empty($saveData['id'])){
                $strategy_log=(object)['type'=>5,'opration'=>'update','data'=>''];
                $strategy_log->data=$this->point_model->get($saveData['id']);
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $data=['errcode'=>0,'errmsg'=>'保存成功'];
        if(!empty($saveData['id'])){
            $update=$this->point_model->update($saveData['id'],$saveData);
            if(!$update)
                $data=['errcode'=>1,'errmsg'=>'保存失败'];
        }else{
            $addId = $this->point_model->add($saveData);
            if(!$addId)
                $data=['errcode'=>1,'errmsg'=>'保存失败'];
        }
        //策略操作记录
        try{
            if(!empty($saveData['id']) && $data['errcode']==0){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        

        if ($data ['errcode'] == 0) { // -------ccz,操作日志
        	try {
        		$logInfo = ( array ) $saveData;
        		$logInfo ['op'] = $logInfo['id']?$this->log_record->Update:$this->log_record->New;
        		$logInfo ['info'] = '';
        		$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Point );
        	} catch ( Exception $e ) {
        		log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
        	}
        }
        
        echo json_encode($data); 
    }

    /**
     * 新建分级红包页
     */
    public function addsub($id=NULL) {
        if($id===NULL) exit('参数有误');
        $curData=$this->point_model->get($id);
        if(!$curData)  exit('数据不存在');
        if($curData->mchId!=$this->mchId) exit('无权操作');
        $data=(object)[
            'id'=>'',
            'amount'=>'',
            'num'=>'',
            'probability'=>'',
            'parentId'=>$curData->id,
            'parentName'=>$curData->name
        ];
        $view=(object)['action'=>'add','title'=>'新建'];
        $this->load->view('point_editsub',['data'=>$data,'view'=>$view]);
    }

    /**
     * 修改分级红包页
     */
    public function editsub($parentId,$id) {
        if(!isset($parentId) || !isset($id)) exit('参数有误');
        $fdata=$this->point_model->get($parentId);
        if($fdata){
            if($fdata->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('参数有误');
        }
        $sdata=$this->point_model->get_sub($id);
        if($sdata){
            if($sdata->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('参数有误');
        }
        if($sdata->probability) $sdata->probability*=100;
        $sdata->parentName=$fdata->name;
        $view=(object)['action'=>'edit','title'=>'修改'];
        $this->load->view('point_editsub',['data'=>$sdata,'view'=>$view]);
    }

    /**
     * 删除分级红包
     */
    public function delsub() {
        header("Content-type",'application/json;charset=utf-8;');
        $id=$this->input->post('id');
        $data=$this->point_model->get_sub($id);
        if($data->mchId!=$this->mchId){
            $result=[
                'errcode'=>1,
                'errmsg'=>'无权操作'
            ];
        }else{
            //策略操作记录
            try{
                $strategy_log=(object)['type'=>5,'opration'=>'delete_sub','data'=>''];
                $strategy_log->data=$this->point_model->get_sub($id);
            }catch(Exception $e){
                log_message('error','strategy_log_error:'.$e->getMessage());
            }
            //策略操作记录 end
            $isdel=$this->point_model->del_sub($id);
            //策略操作记录
            try{
                if($isdel){
                    $this->load->model('strategy_log_model');
                    $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
                }
            }catch(Exception $e){
                log_message('error','strategy_log_error:'.$e->getMessage());
            }
            //策略操作记录 end
            if($isdel){
                $result=[
                    'errcode'=>0,
                    'errmsg'=>''
                ];
                
            }else{
                $result=[
                    'errcode'=>1,
                    'errmsg'=>$isdel
                ];
            }
        }
        
        if ($result ['errcode'] == 0) { // -------ccz,操作日志
        	try {
        		$logInfo = ( array ) $data;
        		$logInfo ['op'] = $this->log_record->Delete;
        		//shit!shit!shit! 这里获取分级策略名称代码成本太高，直接多读取数据库好了，有空再去修改他的js代码，把父级名传过来
        		$pointData = ( array )$this->point_model->get($logInfo['parentId']);
        		$logInfo ['name'] = $pointData['name'];
        		$logInfo ['info'] = ' 的分级积分ID: '.$id;
        		$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Point );
        	} catch ( Exception $e ) {
        		log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
        	}
        }
        echo json_encode($result);
    }

    /**
     * 保存分级积分
     */
    public function savesub(){
        header("Content-type",'application/json;charset=utf-8;');
        $saveData=(object)[
            'id'=>$this->input->post('id'),
            'mchId'=>$this->mchId,
            'amount'=>$this->input->post('amount'),
            'num'=>$this->input->post('num'),
            'parentId'=>$this->input->post('parentId'),
            'probability'=>$this->input->post('probability'),
            'third_number'=>$this->input->post('third_number')
        ];
        $parentName = $this->input->post('parentName');
        $saveData->remainNum=$saveData->num;
        $saveData->probability=(float)$saveData->probability/100;
        // -------------------------------------
        // Added by shizq - begin
        $this->load->model('merchant_model', 'merchant');
        $merchant = $this->merchant->get($saveData->mchId);
        if ($saveData->third_number === '1') {
            if (! isset($merchant->rrdAppId) || ! isset($merchant->rrdSecret)) {
                $data = ['errcode' => 1, 'errmsg' => '人人店接口未配置'];
                echo json_encode($data);
                return;
            } 
        }
        // Added by shizq -end
        // -------------------------------------
        if(!empty($saveData->id)){
            $pointSub=$this->point_model->get_sub($saveData->id);
            if($saveData->mchId!=$pointSub->mchId){
                $data=['errcode'=>1,'errmsg'=>'没有权限修改'];
                echo json_encode($data);
                return;
            }
            $saveData->remainNum=$pointSub->remainNum+$saveData->num-$pointSub->num;
        }else{
            $pointSub=$saveData;
        }
        if(!isset($saveData->amount) || !isset($saveData->num) || !isset($saveData->probability)){
            $data=['errcode'=>1,'errmsg'=>'提交数据有误'];
            echo json_encode($data);
            return;
        }
        if($saveData->num<$pointSub->num-$pointSub->remainNum){
            $data=['errcode'=>1,'errmsg'=>'总数量不能小于已发放的数量'];
            echo json_encode($data);
            return;
        }
        //策略操作记录
        try{
            if(!empty($pointSub->id)){
                $strategy_log=(object)['type'=>5,'opration'=>'update_sub','data'=>''];
                $strategy_log->data=$this->point_model->get_sub($pointSub->id);
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $data=['errcode'=>0,'errmsg'=>'保存成功'];
        if(!empty($pointSub->id)){
            $updatePointSub=$this->point_model->update_sub($pointSub->id,$saveData);
            if(!$updatePointSub)
                $data=['errcode'=>1,'errmsg'=>'保存失败'];
        }else{
            $addId = $this->point_model->add_sub($saveData);
            if(!$addId)
                $data=['errcode'=>1,'errmsg'=>'保存失败'];
        }
        //策略操作记录
        try{
            if(!empty($saveData->id) && $data['errcode']==0){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        
        if ($data ['errcode'] == 0) { // -------ccz,操作日志
        	try {
        		$logInfo = ( array ) $saveData;
        		$logInfo ['op'] = $logInfo['id']?$this->log_record->Update:$this->log_record->Add;
        		$logInfo ['name'] = $parentName;
        		$logInfo ['info'] = $logInfo['id']?' 的分级积分ID: '.$logInfo['id']:'的分级积分';
        		$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Point );
        	} catch ( Exception $e ) {
        		log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
        	}
        }
        
        echo json_encode($data);
    }

    // -----------------------------------------
    // Added by shizq
    // 获取用户积分使用记录
    public function fetch_point_used_logs() {
        $user_id = $this->input->get('user_id');
        $this->load->model('User_model', 'user_model');
        $logs = $this->user_model->getPointUsedLogs($user_id, 0, $this->mchId, 0, 100);
        $this->output->set_content_type('application/json')->set_output(ajax_resp($logs));
    }

    // -----------------------------------------
    // Added by shizq
    // 获取用户积分获取记录
    public function fetch_point_get_logs() {
        $user_id = $this->input->get('user_id');
        $this->load->model('User_model', 'user_model');
        $logs = $this->user_model->getPointGetLogs($user_id, 0, $this->mchId, 0, 100);
        $this->output->set_content_type('application/json')->set_output(ajax_resp($logs));
    }

    // -----------------------------------------
    // Added by shizq
    // 查看用户积分数据正确性
    public function test($key = NULL, $userId = 0) {
        if (is_null($key) || $key !== '1acctrue1') {
            show_404();
            exit();
        }
        // 使用的积分
        $usedPointArr = $this->db
            ->query("select userId, sum(amount) amount from user_points_used where mchId = 173 and userId > $userId group by userId")
            ->result();

        // 获得的积分1
        $getPointArr1 = $this->db
            ->query("select userId, sum(amount) amount from user_points where sended = 1 and userId > $userId and mchId = 173 group by userId")
            ->result();

        // 获得的积分2
        // $getPointArr2 = $this->db
        //     ->query("select userId, sum(amount) amount from user_points_get where mchId = 173 group by userId")
        //     ->result();

        // 用户的账户积分余额
        $userPointAmount = $this->db
            ->query("select userId, amount from user_points_accounts where mchId = 173 and userId > $userId group by userId")
            ->result();

        // foreach ($getPointArr1 as $point1) {
        //     foreach ($getPointArr2 as $point2) {
        //         if ($point1->userId == $point2->userId) {
        //             $point1->amount += $point2->amount;
        //             // continue;
        //         }
        //     }
        // }
        $getPointArr = $getPointArr1;

        foreach ($getPointArr as $point) {
            $userId = 0; // 记录外层循环当前的用户编号
            $userPointAmountTemp = 0; // 用户总获取积分
            $tempAmount = 0; // 用户数据库中记录的积分余额
            foreach ($userPointAmount as $userPoint) {
                if ($point->userId == $userPoint->userId) {
                    $userId = $point->userId;
                    $userPointAmountTemp = $point->amount;
                    $tempAmount = $userPoint->amount;
                }
            }
            foreach ($usedPointArr as $usedPoint) {
                if ($point->userId == $usedPoint->userId) {
                    // 用户总积分 - 用户已使用的积分
                    if (($point->amount - $usedPoint->amount) != $tempAmount) {
                        print '异常数据 <span style="color:red">用户编号：'. $point->userId . '，总积分:' . $point->amount .'，计算剩余积分值:'. ($point->amount - $usedPoint->amount) .'，数据库记录的积分剩余值:'. $tempAmount .'</span>';
                        print '<hr>';
                    }
                }
            }
            
        }
    }

}
