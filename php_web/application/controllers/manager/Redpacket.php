<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Redpacket extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('red_packet_model');
        $this->load->model('card_model');
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
     * 红包列表页
     */
    public function lists() {
        $this->load->view('redpacket_lists');
    }

    /**
     * 红包列表数据
     */
    public function data() {
        $allData=[];
        $fData=$this->red_packet_model->get_by_mchid($this->mchId);
        $sData=$this->red_packet_model->get_sub_by_mchid($this->mchId);
        foreach($fData as $k=>$v){
            array_push($allData,$fData[$k]);
            foreach($sData as $key=>$value){
                if($value->parentId==$v->id){
                    $thisV=(object)[];
                    foreach($v as $a=>$b){
                        $thisV->$a='';
                        foreach($value as $c=>$d){
                            if($a==$c){
                                $thisV->$a=$d;
                            }
                            if($a=='totalNum' && $c=='num'){
                                $thisV->$a=$d;
                            }
                        }
                        if($a=='name') $thisV->$a='分级红包';
                    }
                    $thisV->parentId=$value->parentId;
                    array_push($allData,$thisV);
                }
            }
        }
        $data=["data"=>$allData];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    /**
     * 红包列表数据(不含分级红包)
     */
    public function datano() {
        $allData=$this->red_packet_model->get_by_mchid($this->mchId);
        $data=["data"=>$allData];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
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
     * 新建红包页
     */
    public function add() {
        $data=(object)[
            'id'=>'',
            'name'=>'',
            'rpType'=>0,
            'amtType'=>0,
            'amount'=>'',
            'minAmount'=>'',
            'maxAmount'=>'',
            'ruleStr' => '',
            'limitType'=>0,
            'totalAmount'=>'',
            'totalNum'=>'',
            'probability'=>'',
            'levelType'=>0,
            'priority'=>0,
            'payment'=>0,
            'isDirect'=>0,
            'withBalance'=>0
        ];
        $view=(object)['action'=>'add','title'=>'新建','levelType0ShowEdit'=>'','levelType0Show'=>'','levelType1Show'=>'','levelTypeShow'=>'none','rpType0Show'=>'','rpType1Show'=>'none','amtType0Show'=>'','amtType1Show'=>'none','amtType2Show'=>'none','limitType0Show'=>'','limitType1Show'=>'none'];
        $this->load->view('redpacket_edit',['data'=>$data,'view'=>$view]);
    }

    /**
     * 修改红包页
     */
    public function edit($id=null) {
        if(!isset($id)) exit('参数有误');
        $data=$this->red_packet_model->get($id);
        if($data){
            if($data->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('数据不存在');
        }
        $levelType0Show='';
        $levelType1Show='none';
        $levelTypeShow='none';
        $levelType0ShowEdit='none';
        $rpType0Show='';
        $rpType1Show='none';
        $amtType0Show='';
        $amtType1Show='none';
        $amtType2Show='none';
        $limitType0Show='';
        $limitType1Show='none';
        if($data->isDirect==null || $data->isDirect==0){
            $isDirect = 0;
        }else{
            $isDirect = 1;
        }
        if($data->payment==null || $data->payment==0){
            $payment = 0;
        }else{
            $payment = 1;
        }
        if($data->levelType==1){
            $levelType0Show='none';
            $levelType1Show='';
            $levelTypeShow='';
            $levelType0ShowEdit='';
        }
        if($data->rpType==1){
            $rpType0Show='none';
            $rpType1Show='';
        }
        if($data->amtType==1){
            $amtType0Show='none';
            $amtType1Show='';
        }
        if($data->amtType==2){
            $amtType0Show='none';
            $amtType1Show='none';
            $amtType2Show='';
        }
        if($data->limitType==1){
            $limitType0Show='none';
            $limitType1Show='';
        }
        if($data->amount && is_numeric($data->amount)) $data->amount/=100;
        if($data->minAmount) $data->minAmount/=100;
        if($data->maxAmount) $data->maxAmount/=100;
        if($data->totalAmount) $data->totalAmount/=100;
        if($data->probability) $data->probability*=100;
        $view=(object)['action'=>'edit','title'=>'修改','levelType0ShowEdit'=>$levelType0ShowEdit,'levelTypeShow'=>$levelTypeShow,'levelType0Show'=>$levelType0Show,'levelType1Show'=>$levelType1Show,'rpType0Show'=>$rpType0Show,'rpType1Show'=>$rpType1Show,'amtType0Show'=>$amtType0Show,'amtType1Show'=>$amtType1Show,'amtType2Show'=>$amtType2Show,'limitType0Show'=>$limitType0Show,'limitType1Show'=>$limitType1Show,'isDirect'=>$isDirect,'payment'=>$payment];
        $this->load->view('redpacket_edit',['data'=>$data,'view'=>$view]);
    }

    /**
     * 删除红包
     */
    public function del() {
        header("Content-type",'application/json;charset=utf-8;');
        $id=$this->input->post('id');
        $data=$this->red_packet_model->get($id);
        if($data->mchId!=$this->mchId){
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'无权操作'
            ];
            echo json_encode($result);
            return;
        }
        $sdata=$this->red_packet_model->get_sub_by_mchid($this->mchId);
        foreach($sdata as $k=>$v){
            if($v->parentId==$id){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'此红包存在分级红包，不能删除'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('activity_model');
        $allSubAct=$this->activity_model->get_sub_by_mchid($this->mchId);
        foreach($allSubAct as $k=>$v){
            if($v->detailId==$id && $v->activityType==0 && $v->state==1){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'已启用的活动[id:'.$v->id.']绑定了此红包，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('mixstrategy_model');
        $allMix=$this->mixstrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allMix as $k=>$v){
            if($v->strategyId==$id && $v->strategyType==0){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'组合策略[id:'.$v->parentId.']绑定了此红包，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('multistrategy_model');
        $allMulti=$this->multistrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allMulti as $k=>$v){
            if($v->strategyId==$id && $v->strategyType==0){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'叠加策略[id:'.$v->parentId.']绑定了此红包策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }
        $this->load->model('accumstrategy_model');
        $allAccum=$this->accumstrategy_model->get_sub_by_mchid($this->mchId);
        foreach($allAccum as $k=>$v){
            if($v->strategyId==$id && $v->strategyType==0){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'累计策略[id:'.$v->parentId.']绑定了此红包策略，无法删除！'
                ];
                echo json_encode($result);
                return;
            }
        }

        // 判断是否有大奖绑定到此策略，有则不允许删除
        $bonus = $this->accumstrategy_model->getBonusCountByStratetyId(0, $id);
        if (isset($bonus)) {
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'累计策略[id:'.$bonus->accumStrategyId.']的大奖设置绑定了此红包策略，无法删除！'
            ];
            echo json_encode($result);
            return;
        }

        //策略操作记录
        try{
            $strategy_log=(object)['type'=>0,'opration'=>'delete','data'=>''];
            $strategy_log->data=$this->red_packet_model->get($id);
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $isdel=$this->red_packet_model->del_redpacket($id);
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
                'errorCode'=>0,
                'errorMsg'=>''
            ];
        }else{
            $result=[
                'errorCode'=>1,
                'errorMsg'=>$isdel
            ];
        }
        if($result['errorCode'] == 0){//-------ccz,日志，
            try{
                $logInfo = (array)$data;
                $logInfo['id'] = $id;
                $logInfo['op'] = $this->log_record->Delete;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->RedPacket);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        echo json_encode($result);
        return;
    }

    /**
     * 保存红包
     */
    public function save(){
        header("Content-type",'application/json;charset=utf-8;');
        $levelType=$this->input->post('levelType');
        $pData=[
            'id'=>$this->input->post('id'),
            'mchId'=>$this->mchId,
            'name'=>$this->input->post('name'),
            'levelType'=>$this->input->post('levelType'),
            'updateTime'=>time()
        ];
        if(!empty($pData['id'])){
            $redpacket=$this->red_packet_model->get($pData['id']);
            if($pData['mchId']!=$redpacket->mchId){
                $data=['errorCode'=>1,'errorMsg'=>'没有权限修改'];
                echo json_encode($data);
                return;
            }
        }else{
            $pData['createTime']=time();
        }

        if((int)$levelType===1){
            $saveData=(object)$pData;
            $saveData->priority=$this->input->post('priority');
            $saveData->rpType=0;
            $saveData->isDirect=$this->input->post('isDirect');
            $saveData->payment=$this->input->post('payment');
            $saveData->withBalance=$this->input->post('withBalance');
        }else{
            $saveData=(object)array_merge($pData,[
                'rpType'=>$this->input->post('rpType'),
                'amtType'=>$this->input->post('amtType'),
                'amount'=>($this->input->post('amount'))*100,
                'minAmount'=>($this->input->post('minAmount'))*100,
                'maxAmount'=>($this->input->post('maxAmount'))*100,
                'ruleStr'=>($this->input->post('ruleStr')),
                'limitType'=>$this->input->post('limitType'),
                'totalNum'=>$this->input->post('totalNum'),
                'totalAmount'=>($this->input->post('totalAmount'))*100,
                'probability'=>$this->input->post('probability'),
                'isDirect'=>$this->input->post('isDirect'),
                'payment'=>$this->input->post('payment'),
                'withBalance'=>$this->input->post('withBalance')
            ]);
            $saveData->remainAmount=$saveData->totalAmount;
            $saveData->remainNum=$saveData->totalNum;
            $saveData->probability=(float)$saveData->probability/100;
            if(empty($pData['id'])) $redpacket=$saveData;
            $saveData->remainAmount=$redpacket->remainAmount+$saveData->totalAmount-$redpacket->totalAmount;
            $saveData->remainNum=$redpacket->remainNum+$saveData->totalNum-$redpacket->totalNum;

            if($saveData->limitType!=$redpacket->limitType){
                $data=['errorCode'=>1,'errorMsg'=>'上限类型不能修改'];
                echo json_encode($data);
                return;
            }
            if($saveData->limitType==0&&$saveData->totalNum<$redpacket->totalNum-$redpacket->remainNum){
                $data=['errorCode'=>1,'errorMsg'=>'总数量不能小于已发放的总数量'];
                echo json_encode($data);
                return;
            }
            if($saveData->limitType==1&&$saveData->totalAmount<$redpacket->totalAmount-$redpacket->remainAmount){
                $data=['errorCode'=>1,'errorMsg'=>'总金额不能小于已发放的总金额'];
                echo json_encode($data);
                return;
            }
        }
        if(!isset($saveData->name)){
            $data=['errorCode'=>1,'errorMsg'=>'提交数据有误'];
            echo json_encode($data);
            return;
        }
        //策略操作记录
        try{
            if(!empty($redpacket->id)){
                $strategy_log=(object)['type'=>0,'opration'=>'update','data'=>''];
                $strategy_log->data=$this->red_packet_model->get($redpacket->id);
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $data=['errorCode'=>0,'errorMsg'=>'保存成功'];
        if(!empty($redpacket->id)){
            $updateRedpacket=$this->red_packet_model->update_redpacket($redpacket->id,$saveData);
            if(!$updateRedpacket)
                $data=['errorCode'=>1,'errorMsg'=>'保存失败'];
        }else{
            $addId = $this->red_packet_model->add_redpacket($saveData);
            if(!$addId)
                $data=['errorCode'=>1,'errorMsg'=>'保存失败'];
        }
        //策略操作记录
        try{
            if(!empty($pData['id']) && $data['errorCode']==0){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        /*-------记录日志---------add by ccz*/
        if($data['errorCode']==0){//-------ccz,日志，
            try{
                $logInfo = (array)$saveData;
                if(!empty($redpacket->id)){
                    $logInfo ['id'] = $redpacket->id;
                    $logInfo ['op'] = $this->log_record->Update;
                }else{
                    $logInfo ['id'] = $addId;
                    $logInfo ['op'] = $this->log_record->New;
                }
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->RedPacket);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo json_encode($data);
    }

    /**
     * 新建分级红包页
     */
    public function addsub($id=NULL) {
        if($id===NULL) exit('参数有误');
        $curData=$this->red_packet_model->get($id);
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
        $this->load->view('redpacket_editsub',['data'=>$data,'view'=>$view]);
    }

    /**
     * 修改分级红包页
     */
    public function editsub($parentId,$id) {
        if(!isset($parentId) || !isset($id)) exit('参数有误');
        $fdata=$this->red_packet_model->get($parentId);
        if($fdata){
            if($fdata->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('参数有误');
        }
        $sdata=$this->red_packet_model->get_sub($id);
        if($sdata){
            if($sdata->mchId!=$this->mchId) exit('无权操作');
        }else{
            exit('参数有误');
        }
        if($sdata->amount) $sdata->amount/=100;
        if($sdata->probability) $sdata->probability*=100;
        $sdata->parentName=$fdata->name;
        $view=(object)['action'=>'edit','title'=>'修改'];
        $this->load->view('redpacket_editsub',['data'=>$sdata,'view'=>$view]);
    }

    /**
     * 删除分级红包
     */
    public function delsub() {
        $id=$this->input->post('id');
        $data=$this->red_packet_model->get_sub($id);
        if($data->mchId!=$this->mchId){
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'无权操作'
            ];
        }else{
            //策略操作记录
            try{
                $strategy_log=(object)['type'=>0,'opration'=>'delete_sub','data'=>''];
                $strategy_log->data=$this->red_packet_model->get_sub($id);
            }catch(Exception $e){
                log_message('error','strategy_log_error:'.$e->getMessage());
            }
            //策略操作记录 end
            $isdel=$this->red_packet_model->del_redpacket_sub($id);
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
                    'errorCode'=>0,
                    'errorMsg'=>''
                ];
                
            }else{
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>$isdel
                ];
            }
        }
        if($result['errorCode'] == 0){//-------ccz,日志，
            try{
                $logInfo = (array)$data;
                $logInfo['id'] = $id;
                $logInfo['op'] = $this->log_record->Delete;
                $logInfo['parentName'] = $this->red_packet_model->get($data->parentId)->name;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->RedPacket);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * 保存分级红包
     */
    public function savesub(){
        header("Content-type",'application/json;charset=utf-8;');
        $saveData=(object)[
            'id'=>$this->input->post('id'),
            'mchId'=>$this->mchId,
            'amount'=>$this->input->post('amount')*100,
            'num'=>$this->input->post('num'),
            'parentId'=>$this->input->post('parentId'),
            'probability'=>$this->input->post('probability')
        ];
        $saveData->remainNum=$saveData->num;
        $saveData->probability=(float)$saveData->probability/100;
        if(!empty($saveData->id)){
            $redpacketSub=$this->red_packet_model->get_sub($saveData->id);
            if($saveData->mchId!=$redpacketSub->mchId){
                $data=['errorCode'=>1,'errorMsg'=>'没有权限修改'];
                echo json_encode($data);
                return;
            }
            $saveData->remainNum=$redpacketSub->remainNum+$saveData->num-$redpacketSub->num;
        }else{
            $redpacketSub=$saveData;
        }
        if(!isset($saveData->amount) || !isset($saveData->num) || !isset($saveData->probability)){
            $data=['errorCode'=>1,'errorMsg'=>'提交数据有误'];
            echo json_encode($data);
            return;
        }
        if($saveData->num<$redpacketSub->num-$redpacketSub->remainNum){
            $data=['errorCode'=>1,'errorMsg'=>'总数量不能小于已发放的数量'];
            echo json_encode($data);
            return;
        }
        //策略操作记录
        try{
            if(!empty($redpacketSub->id)){
                $strategy_log=(object)['type'=>0,'opration'=>'update_sub','data'=>''];
                $strategy_log->data=$this->red_packet_model->get_sub($redpacketSub->id);
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        $data=['errorCode'=>0,'errorMsg'=>'保存成功'];
        if(!empty($redpacketSub->id)){
            $updateRedpacketSub=$this->red_packet_model->update_redpacket_sub($redpacketSub->id,$saveData);
            if(!$updateRedpacketSub)
                $data=['errorCode'=>1,'errorMsg'=>'保存失败'];
        }else{
            $addId = $this->red_packet_model->add_redpacket_sub($saveData);
            if(!$addId)
                $data=['errorCode'=>1,'errorMsg'=>'保存失败'];
        }
        //策略操作记录
        try{
            if(!empty($saveData->id) && $data['errorCode']==0){
                $this->load->model('strategy_log_model');
                $this->strategy_log_model->add($strategy_log->type,$strategy_log->opration,json_encode($strategy_log->data));
            }
        }catch(Exception $e){
            log_message('error','strategy_log_error:'.$e->getMessage());
        }
        //策略操作记录 end
        
        /*-------记录日志---------add by ccz*/
        if($data['errorCode']==0){//-------ccz,日志，
            try{
                $logInfo = (array)$saveData;
                if(!empty($saveData->id)){
                    $logInfo ['id'] = $saveData->id;
                    $logInfo ['op'] = $this->log_record->Update;
                }else{
                    $logInfo ['id'] = $addId;
                    $logInfo ['op'] = $this->log_record->Add;
                }
                $logInfo['parentName'] = $this->red_packet_model->get($saveData->parentId)->name;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->RedPacket);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo json_encode($data);
    }

}
