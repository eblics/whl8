<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Group extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('merchant_model');
        $this->load->model('group_model');
        $this->load->model('group_scanpk_model');
        $this->mchId=$this->session->userdata('mchId');
        $this->load->library('log_record');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }
    
    //好友圈配置
    public function setting(){
        $data=$this->group_model->get_group_setting($this->mchId);
        $productUrl=$this->config->item('mobile_url').'group/lists/'.$this->mchId;
        if(!$data){
            $data=(object)[
                'id'=>'',
                'productName'=>'好友圈'
            ];
        }
        $data->productUrl=$productUrl;
        $this->load->view('group_setting',['data'=>$data]);
    }

    //好友圈群组管理
    public function lists(){
        $groups=$this->group_model->get_all_group($this->mchId);
        $this->load->view('group_lists',['data'=>$groups]);
    }

    //好友圈群组数据
    public function data(){
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $start=intval($this->input->post('start'));
        $length=intval($this->input->post('length'));
        $draw=intval($this->input->post('draw'));
        $groupsCount=$this->group_model->get_all_group_count($this->mchId);
        $groups=$this->group_model->get_group_page($this->mchId,$start,$length);
        foreach($groups as $k=>$v){
            $groups[$k]->createTime=date('Y-m-d H:i:s',$v->createTime);
            if(stripos($v->groupImg,'http://')===false){
                $groups[$k]->groupImg=$this->config->item('mobile_url').$v->groupImg;
            }
        }
        $result=(object)['data'=>$groups];
        $result->draw=intval($draw);
        $result->recordsTotal=$groupsCount->count;
        $result->recordsFiltered=$groupsCount->count;
        echo json_encode($result);
        return;
    }

    //好友圈群组成员数据
    public function data_member(){
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        $id=intval($this->input->post('id'));
        if(!isset($id)){
            $result->errcode=1;
            $result->errmsg='缺少参数';
            echo json_encode($result);
            return;
        }
        $group=$this->group_model->get_group($id);
        if(!$group){
            $result->errcode=1;
            $result->errmsg='群组不存在';
            echo json_encode($result);
            return;
        }
        if($group->mchId!=$this->mchId){
            $result->errcode=1;
            $result->errmsg='没有权限';
            echo json_encode($result);
            return;
        }
        $members=$this->group_model->get_group_member($id);
        foreach($members as $k=>$v){
            $members[$k]->createTime=date('Y-m-d H:i:s',$v->createTime);
            if(stripos($v->headImage,'http://')===false){
                $members[$k]->headImage=$this->config->item('mobile_url').$v->headImage;
            }
        }
        $result->data=$members;
        echo json_encode($result);
        return;
    }

    /**
     * 保存 好友圈配置
     */
    public function save(){
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];
        $saveData = [ 
				'productName' => trim($this->input->post('productName')),
				'mchId' => $this->mchId,
				'updateTime' => time()
		];
        if($saveData['productName']==''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            echo json_encode($result);
            return;
        }
        $setting=$this->group_model->get_group_setting($this->mchId);
        if(!$setting) {
            $saveData['createTime']=time();
            $save=$this->group_model->add_group_setting($saveData);
        }else{
            $save=$this->group_model->update_group_setting($saveData);
        }
        if(! $save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        $result->data=$save;

        if ($result->errcode == 0) { // -------ccz,操作日志
        	try {
        		$logInfo = ( array ) $saveData;
        		$logInfo ['op'] = $this->log_record->Update;
        		$logInfo ['info'] = '酒友圈';
        		$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Group );
        	} catch ( Exception $e ) {
        		log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
        	}
        }
        echo json_encode($result);
        return;
    }

    
}
