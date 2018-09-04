<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Batch extends MerchantController {
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct ();
        $this->load->model ( 'batch_model' );
        $this->load->model ( 'code_version_model' );
        $this->load->model ( 'merchant_model' );
        $this->mchId = $this->session->userdata ( 'mchId' );
        $this->load->model( 'sub_activity_model' );
        $this->load->model('activity_model');
        $this->load->library('log_record');
    }

    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }

	/**
	 * 乐码列表页
	 */
	public function lists() {
		$this->load->view ( 'batch_lists' );
	}
	
	/**
	 * 乐码列表数据
	 */
	public function data() {
		$alldata = $this->batch_model->get_mch_batchs ( $this->session->mchId, $this->input->get('state') );
		$reldata = [ ];
		foreach ( $alldata as $k => $v ) {
			$alldata [$k]->name = $v->batchNo;
			if ($v->rowStatus == 1) {
				unset ( $alldata [$k] );
			} else {
				/*--- 通过乐券的ID获取活动信息--- */
				$actInfo = ''; //活动信息字符串
				$idData ='';//活动信息的ID值字符串
				$mainId = [ ]; //父类活动的ID集合
				$actData = $this->sub_activity_model->get_by_batch($v->id);
				foreach($actData as $key => $value){								
					/*防活动重名，所以用活动的ID来判断是否已经获取了*/					
					if(!in_array($value->mainId, $mainId)){
						$mainId[] = $value->mainId;
						/*设置活动的样式，使Main活动在每行起始位置,如果一个乐码有多个主活动，则用'|'分隔
						 *    例如   主活动1/子活动1,子活动2|主活动2/子活动3,子活动4
						 *    idData用相同格式存储活动ID值*/
						$actInfo .= ($actInfo?'|':'').$value->mainName.'/'.$value->name;
						$idData .=($idData?'|':'').$value->mainId.'/'.$value->id;
					}else{
						$actInfo .= ','.$value->name;//同级子活动用逗号分隔
						$idData .= ','.$value->id;
					}
				}/*---活动信息END---*/
				$alldata [$k]->expireTime = date ( 'Y-m-d H:i:s', $v->expireTime );//过期时间
				if($v->state==1){
					$alldata [$k]->activeTime = $v->activeTime ? date ( 'Y-m-d H:i:s', $v->activeTime ):'';//激活时间
				}elseif($v->state==2){
					$alldata [$k]->stopTime = $v->stopTime ? date ( 'Y-m-d H:i:s', $v->stopTime ):'';//停用时间								
				}
				$alldata [$k]->createTime = $v->createTime ? date ( 'Y-m-d H:i:s', $v->createTime ):'';//申请时间
				$alldata [$k]->actInfo = $actInfo;//活动子活动信息  : 活动/子活动
				$alldata [$k]->idData = $idData;
				array_push ( $reldata, $alldata [$k] );
			}
		}
		$data = [ 
				"data" => $reldata 
		];
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	
	/**
	 * 乐码列表数据（仅已激活）
	 */
	public function dataenable() {
		$alldata = $this->batch_model->get_mch_batchs_enable ( $this->session->mchId );
		$reldata = [ ];
		foreach ( $alldata as $k => $v ) {
			$alldata [$k]->name = $v->batchNo;
			if ($v->rowStatus == 1) {
				unset ( $alldata [$k] );
			} else {
				$alldata [$k]->expireTime = date ( 'Y-m-d', $v->expireTime );
				array_push ( $reldata, $alldata [$k] );
			}
		}
		$data = [ 
				"data" => $reldata 
		];
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	/**
	 * 乐码批次已扫次数查询
	 */
	public function data_batch_scannum($batchId) {
		header("Content-type",'application/json;charset=utf-8;');
		$result =(object)[ 
			'errcode' => 1,
			'errmsg' => '查询失败',
			'data'=>NULL
		];
		if(!isset($batchId)){
			echo json_encode($result);
            return;
		}
		$batch=$this->batch_model->get($batchId);
		if(!$batch){
			echo json_encode($result);
            return;
		}
		if($batch->state==0){
			$result->errmsg='无法查询未激活的批次';
			echo json_encode($result);
            return;
		}
		if(intval($batch->mchId)!==intval($this->mchId)){
			$result->errmsg='没有权限';
			echo json_encode($result);
            return;
		}
		$record = $this->batch_model->get_mch_batchs_scannum($batchId);
		if(!$record){
			echo json_encode($result);
            return;
		}
		$result->errcode=0;
		$result->errmsg='查询成功';
		$result->data=$record;
		echo json_encode($result);
        return;
	}

	/**
	 * 乐码申请页
	 * is_pre 判断用户是否预审核,预审核状态下申请码关联产品不可用
	 */
	public function add() {
		$data = ( object ) [ 
				'id' => '',
				'batchNo' => '',
				'categoryId' => '',
				'productId' => '',
				'mchId' => $this->session->mchId,
				'len' => '',
				'ifPubCode' => 0,
				'expireTime' => time () + 3600 * 24 * 365 
		];
		$data->batchNo = sprintf ( "%s%04d", date ( 'Ymd', time () ), $this->batch_model->get_sq_no ( $this->session->mchId ) );
		$view = ( object ) [ 
				'action' => 'add',
				'title' => '申请',
				'len_disabled' => '',
				'category_display' => 'none',
				'product_display' => 'none',
				'category_check' => '',
				'product_check' => '' ,
				'is_pre'=>$this->session->status
		];
		$this->load->view ( 'batch_edit', [ 
				'data' => $data,
				'view' => $view
		] );
	}
	
	/**
	 * 乐码修改页
	 * is_pre 判断用户是否预审核,预审核状态下申请码关联产品不可用
	 */
	public function edit($id = null) {
		if (! isset ( $id ))
			exit ( '参数有误' );
		$data = $this->batch_model->get_by_id ( $id );
		if ($data) {
			if ($data->mchId != $this->mchId)
				exit ( '无权操作' );
			if ($data->rowStatus == 1)
				exit ( '数据已删除' );
		} else {
			exit ( '输入有误' );
		}
		$category_display = '';
		$product_display = '';
		$category_check = '';
		$product_check = '';
		if (empty ( $data->categoryId ) || $data->categoryId == '') {
			$category_display = 'none';
			$product_display = 'none';
		} else {
			$category_check = 'checked="checked"';
		}
		if (empty ( $data->productId ) || $data->productId == '') {
			$product_display = 'none';
			$product_check = 'checked="checked"';
		}
		$view = ( object ) [ 
				'action' => 'edit',
				'title' => '修改',
				'len_disabled' => 'disabled',
				'category_display' => $category_display,
				'category_check' => $category_check,
				'product_display' => $product_display,
				'product_check' => $product_check,
				'is_pre' => $this->session->status
		];
		$this->load->view ( 'batch_edit', [ 
				'data' => $data,
				'view' => $view 
		] );
	}
	
	/**
	 * 删除乐码
	 */
	public function del_batch() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$id = $this->input->post ( 'id' );
		$data = $this->batch_model->get_by_id ( $id );
		if (! $data) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '无权操作' 
			];
			echo json_encode ( $result );
			return;
		}
		if ($data->mchId != $this->mchId) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '无权操作' 
			];
			echo json_encode ( $result );
			return;
		}
		if ($data->state == 1) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '不能删除激活状态的乐码' 
			];
			echo json_encode ( $result );
			return;
		}
		$allSubAct = $this->activity_model->get_sub_by_mchid ( $this->mchId );
		/*-----添加一次性提示所有关联活动--------by ccz*/
		$relateActId = [];
		foreach ( $allSubAct as $k => $v ) {
			if ($v->batchId == $id && $v->state == 1) {
				$relateActId[] = $v->id;
			}
		}
		if(count($relateActId)>0){
			$result = [
			'errorCode' => 1,
			'errorMsg' => '已启用的活动[id:' . implode(',',$relateActId) . ']绑定了这批乐码，无法删除！'
					];
			echo json_encode ( $result );
			return;
		}
		/*---------一次性提示所有关联活动end-------2016-05-03*/
		$isdel = $this->batch_model->del_batch ( $id );
		if ($isdel) {
			$result = [ 
					'errorCode' => 0,
					'errorMsg' => '' 
			];
		} else {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => $isdel 
			];
		}
		if($result['errorCode'] == 0){//-------ccz,日志，batch修改
		    try{
			$logInfo = (array)$data;
			$logInfo['id'] = $id;
			$logInfo['op'] = $this->log_record->Delete;
			$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
		    }catch(Exception $e){
		        log_message('error','mch_log_error:'.$e->getMessage());
		    }
		}
		echo json_encode ( $result );
		return;
	}
	
	/**
	 * 保存乐码
	 */
	public function save() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$saveData = [ 
				'batchNo' => $this->input->post ( 'batchNo' ),
				'productId' => $this->input->post ( 'productId' ),
				'categoryId' => $this->input->post ( 'categoryId' ),
				'ifPubCode' => $this->input->post ( 'ifPubCode' ),
				'expireTime' => strtotime ( $this->input->post ( 'expireTime' ) ) + 3600 * 24 - 1,
				'updateTime' => time () 
		];
		if (empty ( $saveData ['expireTime'] )) {
			$data = [ 
					'errorCode' => 1,
					'errorMsg' => '提交数据有误' 
			];
			echo json_encode ( $data );
			return;
		}
		$id = $this->input->post ( 'id' );
		if ($id != NULL && ! empty ( $id )) {
			$qdata = $this->batch_model->get_by_id ( $id );
			if ($qdata) {
				if ($qdata->mchId != $this->mchId) {
					$data = [ 
							'errorCode' => 1,
							'errorMsg' => '无权操作' 
					];
					echo json_encode ( $data );
					return;
				}
				$batch = $this->batch_model->get_by_no ( $saveData ['batchNo'] );
				if ($batch && $batch->id != $qdata->id) {
					$data = [ 
							'errorCode' => 1,
							'errorMsg' => '保存失败，批号已存在！' 
					];
					echo json_encode ( $data );
					return;
				}
				$save = $this->batch_model->update_batch ( $id, $saveData );
				if ($save) {
					$data = [ 
							'errorCode' => 0,
							'errorMsg' => '' 
					];
				} else {
					$data = [ 
							'errorCode' => 1,
							'errorMsg' => '保存失败' 
					];
				}
				/*--------update batch-日志信息--------by ccz*/
				if($data['errorCode']==0){ //-------ccz,日志，batch修改
				    try{
    					$logInfo = (array)$saveData;
    					$logInfo['id'] = $id;
    					$logInfo['op'] = $this->log_record->Update;
    					$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
				    }catch(Exception $e){
				        log_message('error','mch_log_error:'.$e->getMessage());
				    }
				}
				/*-------记录日志---------end */
				echo json_encode ( $data );
				return;
			} else {
				$data = [ 
						'errorCode' => 1,
						'errorMsg' => '无权操作' 
				];
				echo json_encode ( $data );
				return;
			}
		} else {
			$saveData = array_merge ( $saveData, [ 
					'mchId' => $this->mchId,
					'len' => $this->input->post ( 'len' ),
					'state' => 0,
					'rowStatus' => 0,
					'createTime' => time () 
			] );
			
			//========================新注册用户申请码数限制开始
			//查询当前企业的信息
			$merchantInfo=$this->merchant_model->get_company_info($this->mchId);
			//如果存在过期时间则是新用户expired不为空
			// if($merchantInfo->expired!==NULL){
			// 	$batchNumberTotal=intval($this->batch_model->get_batch_num_total($this->mchId));
			// 	//如果是新用户处于试用期且未到期 则限制其总共申请码数不能超过500
			// 	if($merchantInfo->is_formal==0 && $merchantInfo->expired >= date('Y-m-d',time())){
			// 		if(($batchNumberTotal+intval($this->input->post ('len')))>500){
			// 			exit(json_encode([
			// 				'errorCode'=>1,
			// 				'errorMsg' =>'账户目前处于试用期，申请码总数不能超过500，剩余可申请数量'.(500-$batchNumberTotal).'！']
			// 			));
			// 		}
			// 	}
			// 	//如果是新用户处于试用期，但是试用到期 则限制其申请新的码
			// 	if($merchantInfo->is_formal==0 && $merchantInfo->expired < date('Y-m-d',time())){
			// 		exit(json_encode([
			// 			'errorCode'=>1,
			// 			'errorMsg' =>'账户试用已到期，申请码功能已被限制，请及时购买企业VIP服务！']
			// 		));
			// 	}

			// 	//如果是新用户已经转正，判断是否到期
			// 	if($merchantInfo->is_formal==1 && $merchantInfo->expired < date('Y-m-d',time())){
			// 		exit(json_encode([
			// 			'errorCode'=>1,
			// 			'errorMsg' =>'企业VIP服务已到期，申请码功能已被限制，请及时续费企业VIP服务！']
			// 		));
			// 	}

			// 	//如果是新用户已经转正，当前级别是基础版 限制其总码量上限10万
			// 	if($merchantInfo->is_formal==1 && $merchantInfo->grade==0 && $batchNumberTotal<=100000 && ($batchNumberTotal+intval($this->input->post ('len')))>100000){
			// 		exit(json_encode([
			// 			'errorCode'=>2,
			// 			'errorMsg' =>'码总数超基础版内额度10万，请升级企业VIP服务套餐'
			// 		]));
			// 	}

			// 	//如果是新用户已经转正，当前级别是标准版 限制其总码量上限100万
			// 	if($merchantInfo->is_formal==1 && $merchantInfo->grade==1 && $batchNumberTotal<=1000000 && ($batchNumberTotal+intval($this->input->post ('len')))>1000000){
			// 		exit(json_encode([
			// 			'errorCode'=>2,
			// 			'errorMsg' =>'码总数超标准版内额度100万，请升级企业VIP服务套餐'
			// 		]));
			// 	}

			// 	//如果是新用户已经转正，当前级别是高级版 限制其总码量上限1000万
			// 	if($merchantInfo->is_formal==1 && $merchantInfo->grade==2 && $batchNumberTotal<=10000000 && ($batchNumberTotal+intval($this->input->post ('len')))>10000000){
			// 		exit(json_encode([
			// 			'errorCode'=>2,
			// 			'errorMsg' =>'码总数超高级版内额度1000万，请升级企业VIP服务套餐'
			// 		]));
			// 	}
			// }
			//========================新注册用户申请码数限制结束
			//=====申请码限制=====
			if ($saveData['len']>10000000 ) {
			    $data = [
			        'errorCode' => 1,
			        'errorMsg' => '申请码数量不能超过1000万'
			    ];
			    echo json_encode ( $data );
			    return;
			}
			//=================
			$batch = $this->batch_model->get_by_no ( $saveData ['batchNo'] );
			if ($batch) {
				$data = [ 
						'errorCode' => 1,
						'errorMsg' => '保存失败，批号已存在！' 
				];
				echo json_encode ( $data );
				return;
			}
			$merchant = $this->merchant_model->get ( $this->mchId );
			$versionNum = $merchant->codeVersion;
			$max_num = $this->batch_model->get_mch_max_num ( $this->mchId );
			$saveData ['versionNum'] = $versionNum;
			$saveData ['len'] = $saveData ['len'];
			$saveData ['start'] = $max_num + 1;
			$saveData ['end'] = $max_num + $saveData ['len'];
			$save = $this->batch_model->add_batch ( $saveData );
		}
		if (isset ( $save ) && $save) {
			$data = [ 
					'errorCode' => 0,
					'errorMsg' => '' 
			];
		} else {
			$data = [ 
					'errorCode' => 1,
					'errorMsg' => '保存失败' 
			];
		}
		/*-------记录日志---------add by ccz*/
		if($data['errorCode']==0){//-------ccz,日志，batch修改
		    try{
    			$logInfo = $saveData;
    			$logInfo ['id'] = $save;
    			$logInfo ['op'] = $this->log_record->Add;
    			$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
		    }catch (Exception $e){
		         log_message('error','mch_log_error:'.$e->getMessage()); 
		    }
		}
		/*-------记录日志---------end */
		echo json_encode ( $data );
		return;
	}
	/**
	 * 启用
	 */
	public function start() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$id = $this->input->post ( 'id' );
		if (! isset ( $id )) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '参数有误' 
			];
			echo json_encode ( $result );
			return;
		}
		$data = $this->batch_model->get ( $id );
		if ($data->mchId != $this->mchId) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '无权操作' 
			];
			echo json_encode ( $result );
			return;
		}
		$activeTime = time(); //激活时间，当前时间戳
		$isstart = $this->batch_model->start_batch ( $id , $activeTime);
		if ($isstart) {
			$result = [ 
					'errorCode' => 0,
					'errorMsg' => '' ,
					'activeTime' => date('y-m-d',$activeTime),
			        'fullTime' => date('y-m-d H:i:s',$activeTime) 
			];
		} else {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => $isstart 
			];
		}
		if($result['errorCode'] == 0){//-------ccz,日志，batch修改
		    try{
			$logInfo = (array)$data;
			$logInfo['id'] = $id;
			$logInfo['op'] = $this->log_record->Start;
			$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
		    }catch(Exception $e){
		         log_message('error','mch_log_error:'.$e->getMessage());
		    }
		}
		echo json_encode ( $result );
		return;
	}
	/**
	 * 停用
	 */
	public function stop() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$id = $this->input->post ( 'id' );
		if (! isset ( $id )) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '参数有误' 
			];
			echo json_encode ( $result );
			return;
		}
		$data = $this->batch_model->get ( $id );
		if ($data->mchId != $this->mchId) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '无权操作' 
			];
			echo json_encode ( $result );
			return;
		}
		$stopTime = time(); //停用时间，当前时间戳，方便ajax返回的json数据使用--ccz  16-03-31
		$isstop = $this->batch_model->stop_batch ( $id ,$stopTime);
		if ($isstop) {
			$result = [ 
					'errorCode' => 0,
					'errorMsg' => '' ,
					'stopTime' => date('y-m-d',$stopTime),
			          'fullTime' => date('y-m-d H:i:s',$stopTime)
			];
		} else {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => $isstop 
			];
		}
		if($result['errorCode'] == 0){//----记录日志  -------ccz
		    try{
    			$logInfo = (array)$data;
    			$logInfo ['id'] = $id;
    			$logInfo ['op'] = $this->log_record->Stop;
    			$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
		    }catch (Exception $e){
		        log_message('error','mch_log_error:'.$e->getMessage());
		    }
		}
		echo json_encode ( $result );
		return;
	}

	/**
	 * 下载
	 */
	public function download($mch_id,$id) {
		if ($mch_id != $this->session->mchId) {
			echo '你无权下载他人的码';
			return;
		}
		$merchant = $this->merchant_model->get ( $mch_id );
		$batch = $this->batch_model->get ($id);
		$version = $this->code_version_model->get_by_version ( $batch->versionNum );
		header ( 'Content-Type: application/octet-stream;' );
		header ( 'Content-Disposition:attachment;filename="'.$batch->batchNo.'.txt"' );
		$prefix=$this->config->item ( 'code_prefix' );
		if($version->versionNum=='4')
		    $prefix=strtoupper($prefix);
		$params = [
				'prefix' => $prefix,
				'version' => $version->versionNum,
				'mch_code' => $merchant->code,
				'serial_len' => ( int ) $version->serialLen,
				'valid_len' => ( int ) $version->validLen,
				'start' => ( int ) $batch->start,
				'num' => ( int ) $batch->len,
				'if_pub_code' => ( int ) $batch->ifPubCode
		];
		//if (function_exists('hls_batch')) {
			hls_batch ( $params );
		//} 
		//else {
		//	$server_url = 'http://dev.tools.lsa0.cn/download.php?params=';
		//	echo file_get_contents($server_url . base64_encode(json_encode($params)));
		//}
		
		//
		//设置已下载标签
		$this->batch_model->batch_set_downloaded($id);
		/*-------记录日志---------*/
		try{
    		$logInfo = (array)$batch;
    		$logInfo['op'] = $this->log_record->Download;
    		$this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
		}catch(Exception $e){
		     log_message('error','mch_log_error:'.$e->getMessage());
		}
	}
	
    /**
     * 单据列表页
     */
    public function order_lists() {
        $result=$this->batch_model->get_app_id_secret($this->session->mchId);
        $result['apiurl']=$this->config->item ('api_url');
        $this->load->view ( 'batch_order_lists', $result);
    }
    
    public function order_out_lists() {
        $result=$this->batch_model->get_app_id_secret($this->session->mchId);
        $result['apiurl']=$this->config->item ('api_url');
        $this->load->view ( 'batch_order_out_lists', $result);
    }
    
    public function order_list_data($type) {
        $draw=intval($this->input->post('draw'));
        
        $start=intval($this->input->post('start'));
        $length=intval($this->input->post('length'));
        
        $result=$this->batch_model->get_batch_order_list($this->session->mchId,$start,$length,$type);
        $data=(object)['draw'=>$draw,'recordsTotal'=>$result['count'],'recordsFiltered'=>$result['count'],'data'=>$result['data']];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    public function order_scan_data($id) {
        $result=$this->batch_model->get_batch_order_scan($id);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    
    public function order_add() {
        $result=$this->batch_model->get_app_id_secret($this->session->mchId);
        $result['apiurl']=$this->config->item ('api_url');
        $this->load->view ( 'batch_order_add', $result);
    }
    
    // -----------------------------------
    // 添加乐码出库单编辑界面
    public function order_out_add() {
        $result=$this->batch_model->get_app_id_secret($this->session->mchId);
        $result['apiurl']=$this->config->item ('api_url');
        $this->load->view ( 'batch_order_out_add', $result);
    }
    
    public function order_detail($id) {
        $result=$this->batch_model->get_batch_order_detail($this->session->mchId,$id);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    
    public function order_code_download($id) {
        header ( 'Content-Type: application/octet-stream;' );
        header ( 'Content-Disposition:attachment;filename=code'.$id.'.txt' );
        $result=$this->batch_model->get_batch_order_code($id);
    }
    
    public function order_code_download_log($id) {
        try{
            $logInfo['info'] = "码下载";
            $logInfo['orderno']= $this->batch_model->get_batch_order_detail ($this->session->mchId,$id)->orderNo;
            $logInfo['op'] = $this->log_record->DownloadOrder;
            $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
        }catch(Exception $e){
            log_message('error','mch_log_error:'.$e->getMessage());
        }
    }
    
    public function order_errmsg_download($id) {
        header ( 'Content-Type: application/octet-stream;' );
        header ( 'Content-Disposition:attachment;filename=errmsg'.$id.'.txt' );
        $result=$this->batch_model->get_batch_order_errmsg($this->session->mchId,$id);
        try{
    
            $logInfo['info'] = "错误信息下载";
            $logInfo['orderno']= $this->batch_model->get_batch_order_detail ($this->session->mchId,$id)->orderNo;
            $logInfo['op'] = $this->log_record->DownloadErr;
            $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
        }catch(Exception $e){
            log_message('error','mch_log_error:'.$e->getMessage());
        }
    }
    
    public function order_delete($id,$type){
        $orderNo =$this->batch_model->get_batch_order_detail ($this->session->mchId,$id)->orderNo; //add by ccz 获取订单编号
        $errMsg = $this->batch_model->batch_order_delete($this->session->mchId,$id);
        if (!$errMsg) {
            $result = [
                'errorCode' => 0,
                'errorMsg' => ''
            ];
        } else {
            $result = [
                'errorCode' => 1,
                'errorMsg' => $errMsg
            ];
        }
        
        if($result['errorCode'] == 0){//-------ccz,日志，batch修改
            try{
                
                $logInfo = (array)$errMsg;
                $logInfo['id'] = $id;
                $logInfo['orderno']= $orderNo;
                if($type=='in'){
                    $logInfo['op'] = $this->log_record->DeleteInOrder;
                }
                else{
                    $logInfo['op'] = $this->log_record->DeleteOutOrder;
                }
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Batch);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    
    public function order_exists_orderno(){
        $orderno=$this->input->post('orderno');
        $result = $this->batch_model->batch_order_exists_orderno($this->session->mchId,$orderno);
        $this->output->set_content_type('application/json')->set_output(json_encode(["result"=>$result]));
    }

    /**
     * 验证角色是否拥有获取权限
     *
     * @author shizq
     * @return json
     */
    public function fetch_token() {
    	$this->output->set_content_type('application/json')->set_output(json_encode([]));
    }
}
