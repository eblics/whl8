<?php
/**
 * 累计策略控制器
 */
class Accumstrategy extends MerchantController {

    public function __construct() {
        parent::__construct();
        $this->load->model('accumstrategy_model');
        $this->load->model('red_packet_model');
        $this->load->model('card_model');
        $this->load->model('point_model');
        $this->mchId=$this->session->userdata('mchId');
        $this->load->library('log_record');
    }
    
    /**
     * 累计策略列表页
     */
    public function lists() {
        $this->load->view('accumstrategy_lists');
    }

    /**
     * 累计策略列表数据
     */
    public function data($type='all') {
        header("Content-type",'application/json;charset=utf-8;');
        $allData=[];
        $fData=$this->accumstrategy_model->get_by_mchid($this->mchId);
        if($type!='all'){
            $data=["data"=>$fData];
            echo json_encode($data);
            return;
        }
        $sData=$this->accumstrategy_model->get_sub_by_mchid($this->mchId);
        $rpData=$this->red_packet_model->get_by_mchid($this->mchId);
        $cardData=$this->card_model->get_group($this->mchId);
        $rpSubData=$this->red_packet_model->get_sub_by_mchid($this->mchId);
        $cardSubData=$this->card_model->get_by_mchid($this->mchId);
        $pointData=$this->point_model->get_by_mchid($this->mchId);
        $pointSubData=$this->point_model->get_sub_by_mchid($this->mchId);
        foreach($rpData as $k=>$v){
            if($v->levelType==1){
                $thisProR=1;
                foreach($rpSubData as $k2=>$v2){
                    if($v->id==$v2->parentId){
                        $thisProR*=1-($v2->probability);
                    }
                }
                $rpData[$k]->avProbability=bcsub(1,$thisProR,6);
            }else{
                $rpData[$k]->avProbability=$v->probability;
            }
        }
        foreach($cardData as $k=>$v){
            $thisProC=1;
            foreach($cardSubData as $k2=>$v2){
                if($v->id==$v2->parentId){
                    $thisProC*=1-($v2->probability/100);
                }
            }
            $cardData[$k]->avProbability=bcsub(1,$thisProC,6);
        }
        foreach($pointData as $k=>$v){
            $thisProR=1;
            foreach($pointSubData as $k2=>$v2){
                if($v->id==$v2->parentId){
                    $thisProR*=1-($v2->probability);
                }
            }
            $pointData[$k]->avProbability=bcsub(1,$thisProR,6);
        }
        foreach($fData as $k=>$v){
            array_push($allData,$fData[$k]);
            foreach($sData as $key=>$value){
                if($value->parentId==$v->id){
                    $thisV=$value;
                    if($value->strategyType==0){
                        foreach($rpData as $rpk=>$rpv){
                            if($rpv->id==$value->strategyId){
                                $thisV->strategyName=$rpv->name;
                                $thisV->avProbability=$rpv->avProbability;
                            }
                        }
                    }
                    if($value->strategyType==2){
                        foreach($cardData as $cak=>$cav){
                            if($cav->id==$value->strategyId){
                                $thisV->strategyName=$cav->title;
                                $thisV->avProbability=$cav->avProbability;
                            }
                        }
                    }
                    if($value->strategyType==3){
                        foreach($pointData as $pok=>$pov){
                            if($pov->id==$value->strategyId){
                                $thisV->strategyName=$pov->name;
                                $thisV->avProbability=$pov->avProbability;
                            }
                        }
                    }
                    array_push($allData,$thisV);
                }
                
            }
        }
        $data=["data"=>$allData];
        echo json_encode($data);
    }
    /**
     * 红包列表数据(不含分级红包)
     */
    public function data_rp() {
        header("Content-type",'application/json;charset=utf-8;');
        $allData=$this->red_packet_model->get_by_mchid($this->mchId);
        $data=["data"=>$allData];
        echo json_encode($data);
    }
    /**
     * 乐券列表数据
     */
    public function data_cards() {
        header("Content-type",'application/json;charset=utf-8;');
        $group=$this->card_model->get_group($this->mchId);
        foreach($group  as $key => $value){
            $group[$key]->name=$group[$key]->title;
        }
        $data=["data"=>$group];
        echo json_encode($data);
    }
    /**
     * 积分列表数据(不含分级积分)
     */
    public function data_point() {
        header("Content-type",'application/json;charset=utf-8;');
        $allData=$this->point_model->get_by_mchid($this->mchId);
        $data=["data"=>$allData];
        echo json_encode($data);
    }
    /**
     * 新建累计策略页
     */
    public function add() {
        $data=(object)[
            'id'=>'',
            'name'=>'',
            'sonlist'=>(object)[
                (object)['strategyType'=>0,'strategyId'=>'','start'=>'','end'=>''],
                (object)['strategyType'=>2,'strategyId'=>'','start'=>'','end'=>''],
            ]
        ];
        $view=(object)['action'=>'add','title'=>'新建'];
        $this->load->view('accumstrategy_edit',['data'=>$data,'view'=>$view]);
    }

    /**
     * 修改累计策略页
     */
    public function edit($id=null) {
        if(!isset($id)) exit('参数有误');
        $data=$this->accumstrategy_model->get($id);
        if($data){
            if($data->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('数据不存在');
        }
        $subData=$this->accumstrategy_model->get_sub_by_pid($id);
        $data->sonlist=$subData;
        $view=(object)['action'=>'edit','title'=>'修改'];
        $this->load->view('accumstrategy_edit',['data'=>$data,'view'=>$view]);
    }

    /**
     * 编辑累计策略大奖设置
     * 
     * @param $id 策略编号
     * @return view
     */
    public function bonus($id = NULL) {
        if (! isset($id)) {
            print '<script>alert("策略不存在！");history.back();</script>';
            exit();
        }
        $accumStrategy = $this->accumstrategy_model->get($id);
        if (! isset($accumStrategy)) {
            print '<script>alert("策略不存在！");history.back();</script>';
            exit();
        }
        if ($accumStrategy->mchId != $this->mchId) {
            print '<script>alert("策略不存在！");history.back();</script>';
            exit();
        }
        // 获取已设置的大奖策略信息
        $accumStrategyBonus = $this->accumstrategy_model->getBonusByAStrategyId($id);
        if (empty($accumStrategyBonus)) {
            $accumStrategyBonus = [];
            $bonus = new stdClass();
            $bonus->id = NULL;
            $bonus->strategyType = 0;
            $bonus->strategyId = NULL;
            $bonus->start = '';
            $bonus->end = '';
            $bonus->chance = '';
            $accumStrategyBonus[] = $bonus;
        }
        $viewData = ['bonus' => $accumStrategyBonus, 'accum_strtegy_id' => $id];
        $this->load->view('accumstrategy_bonus_edit', $viewData);

    }

    /**
     * 保存累计策略大奖设置
     * 
     * @param $id 策略编号
     * @return json
     */
    public function save_bonus() {
        $accumStrategyId = $this->input->post('id');
        $strategyTypeArr = $this->input->post('strategyType');
        $strategyIdArr = $this->input->post('strategyId');
        $startArr = $this->input->post('start');
        $endArr = $this->input->post('end');
        $chanceArr = $this->input->post('chance');
                
        if (! empty($accumStrategyId)) {
            $accumStrategy = $this->accumstrategy_model->get($accumStrategyId);
            if ($this->mchId != $accumStrategy->mchId) {
                $data = ['errorCode' => 1, 'errorMsg' => '没有权限修改'];
                echo json_encode($data);
                return;
            }
        }

        if (! is_numeric($accumStrategyId) || empty($strategyTypeArr) || empty($strategyIdArr)) {
            $this->ajaxResponse(NULL, '参数错误', 1);
        }

        if (empty($startArr) || empty($endArr) || empty($chanceArr)) {
            $this->ajaxResponse(NULL, '参数错误', 1);
        }
        
        try {
            $this->accumstrategy_model->saveBonus($this->input->post());
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse(NULL, $e->getMessage(), $e->getCode());
        }
    }

    public function test_bonus($accumStrategyId = 1, $scanNum = 1) {
        $result = $this->accumstrategy_model->tryBonus($accumStrategyId, 528, $scanNum);
        var_dump($result);
    }

    /**
     * 删除累计策略
     */
    public function del() {
        header("Content-type",'application/json;charset=utf-8;');
        $id=$this->input->post('id');
        $data=$this->accumstrategy_model->get($id);
        if($data->mchId!=$this->mchId){
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'无权操作'
            ];
            echo json_encode($result);
            return;
        }
        $this->load->model('activity_model');
        $allSubAct=$this->activity_model->get_sub_by_mchid($this->mchId);
        foreach($allSubAct as $k=>$v){
            if($v->detailId==$id && $v->activityType==6 && $v->state==1){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'已启用的活动[id:'.$v->id.']绑定了此累计策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        //策略操作记录
        try{
            $strategy_log=(object)['type'=>7,'opration'=>'delete','data'=>''];
            $strategy_log->data=$this->accumstrategy_model->get_full_one($id);
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $isdel=$this->accumstrategy_model->del($id);
        //策略操作记录
        try{
            if($isdel){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        
        if($isdel){
            $result=[
                'errorCode'=>0,
                'errorMsg'=>''
            ];
        }else{
            $result=[
                'errorCode'=>1,
                'errorMsg'=>$isdel
            ];
        }
        /*-----记录日志--------by ccz*/
        if($result['errorCode'] == 0){
            try{
                $logInfo = (array)$data;
                $logInfo['id'] = $id;
                $logInfo['op'] = "delete";
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Accumstrategy);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        echo json_encode($result);
        return;
    }

    /**
     * 保存累计策略
     */
    public function save(){
        header("Content-type",'application/json;charset=utf-8;');
        $pData=[
            'id'=>$this->input->post('id'),
            'mchId'=>$this->mchId,
            'name'=>$this->input->post('name'),
            'updateTime'=>time()
        ];
        $subData=(object)[
            'mchId'=>$this->mchId,
            'strategyType'=>$this->input->post('strategyType'),
            'strategyId'=>$this->input->post('strategyId'),
            'start'=>$this->input->post('start'),
            'end'=>$this->input->post('end')
        ];
                
        if(!empty($pData['id'])){
            $accumstrategy=$this->accumstrategy_model->get($pData['id']);
            if($pData['mchId']!=$accumstrategy->mchId){
                $data=['errorCode'=>1,'errorMsg'=>'没有权限修改'];
                echo json_encode($data);
                return;
            }
        }else{
            $pData['createTime']=time();
        }
        
        $saveData=(object)$pData;
        if(!isset($saveData->name)){
            $data=['errorCode'=>1,'errorMsg'=>'提交数据有误'];
            echo json_encode($data);
            return;
        }
        //策略操作记录
        try{
            if(!empty($pData['id'])){
                $strategy_log=(object)['type'=>7,'opration'=>'update','data'=>''];
                $strategy_log->data=$this->accumstrategy_model->get_full_one($pData['id']);
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $result=$this->accumstrategy_model->save($saveData,$subData,$insertId);
        if(!$result){
            $data=['errorCode'=>1,'errorMsg'=>'保存失败'];
            echo json_encode($data);
            return;
        }
        //策略操作记录
        try{
            if(!empty($pData['id']) && $result){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $data=['errorCode'=>0,'errorMsg'=>'保存成功'];
        echo json_encode($data);
        
        /*-------记录日志---------add by ccz*/
        if($data['errorCode']==0){//-------ccz,日志，batch修改
            try{
                $logInfo = (array)$saveData;
                if(!empty($pData['id'])){
                    $logInfo ['op'] = $this->log_record->Update;
                }else{
                    $logInfo ['id'] = $insertId;
                    $logInfo ['op'] = $this->log_record->Add;
                }
                $this->log_record->addLog($this->mchId, $logInfo, $this->log_record->Accumstrategy);
            }catch (Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
    }

}
