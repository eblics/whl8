<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends MerchantController {

    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('product_model');
        $this->mchId=$this->session->userdata('mchId');
        $this->load->library('log_record');
        $this->load->model('Product_model','product');
        if (isset($_SESSION['role']) && $_SESSION['role'] === -1) {
           redirect('/charts/index');
           exit();
       }
    }

    /**
     * 产品分类页
     */
    public function category() {
        if (! in_array('production', $_SESSION['permission_modules']) && $_SESSION['role'] != ROLE_ADMIN_MASTER) {
            if (in_array('batch', $_SESSION['permission_modules'])) {
                redirect('batch/lists');
                exit();
            }
            if (in_array('redpacket', $_SESSION['permission_modules'])) {
                redirect('redpacket/lists');
                exit();
            }
            if (in_array('card', $_SESSION['permission_modules'])) {
                redirect('card/lists');
                exit();
            }
            if (in_array('point', $_SESSION['permission_modules'])) {
                redirect('point/lists');
                exit();
            }
            if (in_array('mixstrategy', $_SESSION['permission_modules'])) {
                redirect('mixstrategy/lists');
                exit();
            }
            if (in_array('multistrategy', $_SESSION['permission_modules'])) {
                redirect('multistrategy/lists');
                exit();
            }
            if (in_array('accumstrategy', $_SESSION['permission_modules'])) {
                redirect('accumstrategy/lists');
                exit();
            }
            if (in_array('activity', $_SESSION['permission_modules'])) {
                redirect('activity/lists');
                exit();
            }
            if (in_array('group', $_SESSION['permission_modules'])) {
                redirect('group/setting');
                exit();
            }
            if (in_array('mall', $_SESSION['permission_modules'])) {
                redirect('mall/configure');
                exit();
            }
            if (in_array('wechat', $_SESSION['permission_modules'])) {
                redirect('wechat/wxmenu');
                exit();
            }
            if (in_array('setting', $_SESSION['permission_modules'])) {
                redirect('setting/guard');
                exit();
            }
            if (in_array('myapp', $_SESSION['permission_modules'])) {
                redirect('app/index');
                exit();
            }
            if (in_array('charts', $_SESSION['permission_modules'])) {
                redirect('charts/index');
                exit();
            }
            if (in_array('salesman', $_SESSION['permission_modules'])) {
            	redirect('salesman/index');
            	exit();
            }
            if (in_array('admin', $_SESSION['permission_modules'])) {
            	redirect('admin/index');
            	exit();
            }
            if (in_array('userdeal', $_SESSION['permission_modules'])) {
                redirect('userdeal/mch_forbidden_users');
                exit();
            }
            if (in_array('tag', $_SESSION['permission_modules'])) {
                redirect('tag/lists');
                exit();
            }
            redirect('service/help_read');
            exit();
        }
        $this->load->view('product_category');
    }

    /**
     * 获取产品分类
     * @deprecated please use /product/list_categories instead.
     */
    public function catedata() {
        $this->list_categories();
    }

    // 获取产品分类
    // path /product/catedata
    public function list_categories() {
        $mchId = $this->mchId;
        $categories = $this->product_model->getCategories($mchId);
        $this->ajaxResponseSuccess($categories);
    }

    /**
     * 添加分类
     */
    public function add_category() {
        $postData=$this->input->post();
        $data=[
            'mchId'=>$this->mchId,
            'parentCategoryId'=>$postData['parentCategoryId'],
            'name'=>$postData['name'],
            'desc'=>$postData['desc'],
            'createTime'=>time(),
            'updateTime'=>time()
        ];
        $insertId=$this->product_model->add_category($data);
        if((int)$insertId===0){
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'添加失败'
            ];
        }else{
            $result=[
                'errorCode'=>0,
                'result'=>$insertId
            ];
        }
        if($result['errorCode'] == 0){//----记录日志  -------ccz
            try{
            	$logInfo = $data;
            	if($logInfo['parentCategoryId']!=-1){//有父级分类
            	   $logInfo['parentCategoryName'] = $this->product->get_category_by_id($logInfo['parentCategoryId'])->name;
            	}
            	$logInfo ['id'] = $insertId;
            	$logInfo ['op'] = $this->log_record->Add;
            	$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Category);
        	}catch(Exception $e){
        	    log_message('error','mch_log_error:'.$e->getMessage());
        	}
        }
        // $result=json_decode( json_encode($result),true);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * 修改分类
     */
    public function update_category() {
        $postData=$this->input->post();
        $qdata=$this->product_model->get_category_by_id($postData['id']);
        if($qdata->mchId!=$this->mchId) exit('无权操作');
        $where=[
            'id'=>$postData['id'],
            'mchId'=>$this->mchId
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
        $status=$this->product_model->update_category($where,$data);
        if($status){
            $result=[
                'errorCode'=>0,
                'errorMsg'=>''
            ];
        }else{
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'保存失败'
            ];
        }
        if($result['errorCode'] == 0){//----记录日志  -------ccz
            try{
            	$logInfo = (array)$data;
            	if($qdata->parentCategoryId!=-1){//有父级分类
            	    $logInfo['parentCategoryName'] = $this->product->get_category_by_id($qdata->parentCategoryId)->name;
            	    $logInfo['parentCategoryId'] = $qdata->parentCategoryId;
            	}else{
            	    $logInfo['parentCategoryId'] = -1;
            	}
            	if(!isset($logInfo['name'])){
            	    $logInfo['name'] = $this->product->get_category_by_id($postData['id'])->name;
            	}
            	$logInfo ['id'] = $postData['id'];
            	$logInfo ['op'] = $this->log_record->Update;
            	$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Category);
        	}catch(Exception $e){
        	    log_message('error','mch_log_error:'.$e->getMessage());
        	}
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * 删除分类
     */
    public function del_category() {
        $id=$this->input->post('id');
        $qdata=$this->product_model->get_category_by_id($id);
        if($qdata->mchId!=$this->mchId) exit('无权操作');
        $subNum=$this->product_model->sub_category_num($id);
        if($subNum>0){
            $result=[
                'errorCode'=>1,
                'errorMsg'=>'此分类下存在子类，无法删除'
            ];
        }else{
            $products=$this->product_model->get_by_category($id);
            if($products){
                $result=[
                    'errorCode'=>1,
                    'errorMsg'=>'此分类下存在产品，无法删除'
                ];
            }else{
                $isdel=$this->product_model->del_category($id);
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
            
        }
        
        if($result['errorCode'] == 0){//----记录日志  -------ccz
            try{
            	$logInfo = (array)$qdata;
            	if($qdata->parentCategoryId!=-1){//有父级分类
            	    $logInfo['parentCategoryName'] = $this->product->get_category_by_id($qdata->parentCategoryId)->name;
            	    $logInfo['parentCategoryId'] = $qdata->parentCategoryId;
            	}
            	$logInfo ['id'] = $id;
            	$logInfo ['op'] = $this->log_record->Delete;
            	$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Category);
        	}catch(Exception $e){
        	    log_message('error','mch_log_error:'.$e->getMessage());
        	}
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * 产品列表页
     */
    public function lists() {
        $this->load->view('product_lists');
    }

    /**
     * 产品数据
     */
    public function prodata() {
        $products=$this->product_model->get_product($this->mchId);
        $categories=$this->product_model->get_category($this->mchId);
        foreach($products as $key=>$value){
            foreach($categories as $k=>$v){
                if($value->categoryId==$v->id){
                    $value->category=$v->name;
                }
            }
        }
        $products=json_decode( json_encode($products),true);
        $data=["data"=>$products];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * 删除产品
     */
    public function del_product() {
        $id=$this->input->post('id');
        $qdata=$this->product_model->get_by_id($id);
        if($qdata->mchId!=$this->mchId) exit('无权操作');
        $isdel=$this->product_model->del_product($id);
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
        
        if($result['errorCode'] == 0){//----记录日志  -------ccz
            try{
            	$logInfo = (array)$qdata;
            	$logInfo ['id'] = $id;
            	$logInfo ['op'] = $this->log_record->Delete;
            	$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Product);
        	}catch(Exception $e){
        	    log_message('error','mch_log_error:'.$e->getMessage());
        	}
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * 产品添加保存方法
     */
    public function save() {
        $saveData=[
            'mchId'=>$this->mchId,
            'name'=>$this->input->post('name'),
            'categoryId'=>$this->input->post('categoryId'),
            'imgUrl'=>$this->input->post('imgUrl'),
            'unit'=>$this->input->post('unit'),
            'specification'=>$this->input->post('specification'),
            'description'=>$this->input->post('description'),
            'createTime'=>time(),
            'updateTime'=>time()
        ];
        $id=$this->input->post('id');
        if (empty($saveData['name']) || empty($saveData['categoryId'])){
            $data=['errorCode'=>1,'errorMsg'=>'提交数据有误'];
        }else{
            if($id!=NULL){
                $qdata=$this->product_model->get_by_id($id);
                if($qdata->mchId!=$this->mchId) exit('无权操作');
                //更新产品
                $save=$this->product_model->update_product($id,$saveData);
            }else{
            	//添加产品
                $save=$this->product_model->add_product($saveData);
            }
            if($save){
                $data=['errorCode'=>0,'errorMsg'=>''];
            }else{
                $data=['errorCode'=>1,'errorMsg'=>'保存失败'];
            }
        }
        if($data['errorCode'] == 0){//----记录日志  -------ccz
            try{
            	$logInfo = (array)$saveData;
            	$logInfo ['id'] = $id?$id:$save;
            	$logInfo ['op'] = $id?$this->log_record->Update:$this->log_record->Add;
            	$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Product);
        	}catch(Exception $e){
        	    log_message('error','mch_log_error:'.$e->getMessage());
        	}
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * 图片上传方法
     */
    public function upload() {
        $filepath= '/files/public/'.$this->mchId;
        echo upload_file('gif|jpg|png',500,$filepath);
    }

    /**
     * 产品添加页
     */
    public function add() {
        $this->load->view('product_edit');
    }

    /**
     * 产品修改页
     */
    public function edit($id=null) {
        if($id==null) exit('参数有误');
        $data=$this->product_model->get_by_id($id);
        if($data){
            if($data->mchId!=$this->mchId) exit('无权操作');
            $this->load->view('product_edit',['data'=>$data]);
        }
    }

    public function get_mch_products($mch_id){
        $products=$this->product_model->get_mch_products($mch_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($products));
    }


}
