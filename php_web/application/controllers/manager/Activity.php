<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Activity extends MerchantController {
	public function __construct() {
		// 初始化，加载必要的组件
		parent::__construct ();
		$this->load->model ( 'activity_model' );
		$this->load->model ( 'red_packet_model' );
		$this->load->model ( 'batch_model' );
		$this->load->model ( 'webapp_model' );
		$this->load->model ( 'card_model' );
		$this->mchId = $this->session->userdata ( 'mchId' );
		$this->load->library ( 'log_record' );
		$this->load->model('multistrategy_model');
		$this->load->model('point_model');
		$this->load->model('accumstrategy_model');
		$this->load->model('tag_model');
	}
	
	/**
	 * CI控制器默认入口
	 */
	public function index() {
		// 如无需使用留空即可
	}
	
	/**
	 * 活动列表页
	 */
	public function lists() {
		$this->load->view ( 'activity_lists' );
	}
	
	/**
	 * 活动列表数据
	 */
	public function data() {
		$allData = [ ];
		$fData = $this->activity_model->get_by_mchid ( $this->mchId );
		$sData = $this->activity_model->get_sub_by_mchid ( $this->mchId );
		foreach ( $fData as $k => $v ) {
			array_push ( $allData, $fData [$k] );
			foreach ( $sData as $key => $value ) {
				if ($value->parentId == $v->id) {
					array_push ( $allData, $sData [$key] );
				}
			}
		}
		foreach ( $allData as $k => $v ) {
			$allData [$k]->startTime = date ( 'Y-m-d H:i:s', intval ( $v->startTime ) );
			$allData [$k]->endTime = date ( 'Y-m-d H:i:s', intval ( $v->endTime ) );
		}
		$data = [ 
				"data" => $allData 
		];
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	
	/**
	 */
	public function relateData() {
		/*
		 * --------add by ccz ---20160412
		 * 需要的数据：0000010指定的活动区域，
		 * 0000010,指定活动区域
		 * 0000100,关联的乐码批次
		 * 0001000,关联生产入库单
		 * 0010000,关联出库单
		 * 0100000,关联销售区域
		 * 1000000,关联商品过期策略
		 * --------end by ccz
		 */
		// 关联条件
		$_BIND_TIME = 0b0000001; // 绑定时间
		$_BIND_AREA = 0b0000010; // 促销区域 sub表中
		$_BIND_BATCHNO = 0b0000100; // 乐码批次batchs表中
		$_BIND_ORDERNO_IN = 0b0001000; // 绑定入库单 TTS
		$_BIND_ORDERNO_OUT = 0b0010000; // 绑定出库单TTS
		$_BIND_SALETOAGC = 0b0100000; // 销售区域 sub表中
		$_BIND_EXPIRE_TIME = 0b1000000; // 过期时间tts表中
		$_BIND_ALL = 0b1111110;
		
		$sub_active_id = $this->input->post ( 'id' );
		$areaCode = $this->input->post ( 'areaCode' );
		$saletoagc = $this->input->post ( 'saletoagc' );
		$binding = ( int ) $this->input->post ( 'binding' );
		
		$allData = [ ];
		// 如果有绑定关系，则执行
		if ($_BIND_ALL & $binding) {
			$subActData = $this->activity_model->get_sub ( $sub_active_id ); // 取子活动表数据
			if ($_BIND_BATCHNO & $binding) {
				// 乐码批次
				$batchData = $this->batch_model->get_by_id ( $subActData->batchId ); // 取得乐码信息
				$allData ['batchNo'] = ($batchData->rowStatus == 0) ? $batchData->batchNo : '';
			}
			if ($_BIND_AREA & $binding) {
				// 促销区域
				$area = $this->activity_model->get_area_byCode ( $areaCode ); // 取活动地区名字
				$allData ['area'] = $area->name;
			}
			if ($_BIND_ORDERNO_IN & $binding) {
				// 绑定入库单
				$inOrderData = $this->activity_model->get_tts_order ( $subActData->prodInOrderId ); // 关联入库单
				$allData ['order_in'] = $inOrderData->orderNo;
			}
			if ($_BIND_ORDERNO_OUT & $binding) {
				// 绑定出库单
				$outOrderData = $this->activity_model->get_tts_order ( $subActData->outOrderId ); // 关联出库单
				$allData ['order_out'] = $outOrderData->orderNo;
			}
			if ($_BIND_SALETOAGC & $binding) {
				// 销售区域
				$saletoagc = $this->activity_model->get_area_byCode ( $saletoagc ); // 取销售地区名字
				$allData ['saletoagc'] = $saletoagc->name;
			}
			if ($_BIND_EXPIRE_TIME & $binding) {
				// 过期时间
				$allData ['expire_time'] = date ( 'Y-m-d', intval ( $subActData->expireTime ) );
			}
		}
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $allData ) );
	}
	
	/**
	 * H5数据
	 */
	public function h5data() {
		$data = $this->activity_model->get_h5 ();
		foreach ( $data as $k => $v ) {
			$data [$k]->name = $v->appName;
			$data [$k]->level = 0;
		}
		$data = [ 
				"data" => $data 
		];
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	
	/**
	 * 地区数据
	 */
	public function areadata() {
		$data = $this->activity_model->get_area ();
		foreach ( $data as $k => $v ) {
			$data [$k]->id = $v->code;
			$data [$k]->level = $v->level + 1;
		}
		$data = [ 
				"data" => $data 
		];
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	/**
	 * TTS ORDER 生产入库数据
	 */
	public function tts_in_produce_data() {
		$data = $this->activity_model->get_tts_produce_order ( $this->mchId );
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	/**
	 * TTS ORDER 普通入库数据
	 */
	public function tts_in_order_data() {
		$data = $this->activity_model->get_tts_in_order ( $this->mchId );
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	
	/**
	 * TTS ORDER 出库数据
	 */
	public function tts_out_order_data() {
		$data = $this->activity_model->get_tts_out_order ( $this->mchId );
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}

	/**
	 * tag数据
	 */
	public function tag_data() {
		$data = $this->tag_model->get_list($this->mchId,$count);
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	
	/**
	 * 活动新建页
	 */
	public function add() {
		$data = ( object ) [ 
				'id' => '',
				'name' => '',
				'startTime' => date ( 'Y-m-d H:i:s', strtotime ( date ( 'Y-m-d' ) ) ),
				'endTime' => date ( 'Y-m-d H:i:s', strtotime ( date ( 'Y-m-d' ) ) + 3600 * 24 * 30 - 1 ),
				'imgUrl' => '',
				'description' => '',
				'state' => 0 
		];
		$data->subStartTime = date ( 'Y-m-d H:i:s' );
		$data->subEndTime = date ( 'Y-m-d H:i:s' );
		$view = ( object ) [ 
				'action' => 'add',
				'title' => '新建' 
		];
		$this->load->view ( 'activity_edit', [ 
				'data' => $data,
				'view' => $view 
		] );
	}
	
	/**
	 * 活动修改页
	 */
	public function edit($id = null) {
		if (! isset ( $id ))
			exit ( '参数有误' );
		$data = $this->activity_model->get ( $id );
		if ($data) {
			if ($data->mchId != $this->mchId)
				exit ( '无权操作' );
		} else {
			exit ( '数据不存在' );
		}
		$view = ( object ) [ 
				'action' => 'edit',
				'title' => '修改' 
		];
		$data->startTime = date ( 'Y-m-d H:i:s', $data->startTime );
		$data->endTime = date ( 'Y-m-d H:i:s', $data->endTime );
		$sdata = $this->activity_model->get_sub_by_pid ( $id );
		$allStartTime = [ ];
		$allEndTime = [ ];
		foreach ( $sdata as $k => $v ) {
			array_push ( $allStartTime, $v->startTime );
			array_push ( $allEndTime, $v->endTime );
		}
		if (empty ( $allStartTime ))
			$allStartTime [] = time ();
		if (empty ( $allEndTime ))
			$allEndTime [] = time ();
		$min = min ( $allStartTime );
		$max = max ( $allEndTime );
		$data->subStartTime = date ( 'Y-m-d H:i:s', $min );
		$data->subEndTime = date ( 'Y-m-d H:i:s', $max );
		$this->load->view ( 'activity_edit', [ 
				'data' => $data,
				'view' => $view 
		] );
	}
	
	/**
	 * 子活动新建页
	 */
	public function addsub($id = NULL) {
		if ($id === NULL)
			exit ( '参数有误' );
		$curData = $this->activity_model->get ( $id );
		if ($curData) {
			if ($curData->mchId != $this->mchId)
				exit ( '无权操作' );
		} else {
			exit ( '数据有误' );
		}
		$data = ( object ) [ 
				'id' => '',
				'name' => '',
				'content' => '',
				'startTime' => date ( 'Y-m-d H:i:s', $curData->startTime ),
				'endTime' => date ( 'Y-m-d H:i:s', $curData->endTime ),
				'webAppId' => '',
				'batchId' => '',
				'detailId' => '',
				'geoNeeded' => 0,
				'subscribeNeeded' => 1,
				'areaCode' => '',
				'parentId' => $curData->id,
				'parentName' => $curData->name,
				'prodInOrderId' => '',
				'outOrderId' => '',
				'saletoagc' => '',
				'expireOprt' => '',
				'expireTime' => '',
				'state' => 0,
				'activityType' => 0,
				'role' => 0,
				'is_pre'=>5,
				'productId' =>'',
				'categoryId'=>'',
		        'tagId'=>'',
		        'is_forevil'=>false,
				'forEvil'=>''
		];
		$data->pStartTime = date ( 'Y-m-d H:i:s', $curData->startTime );
		$data->pEndTime = date ( 'Y-m-d H:i:s', $curData->endTime );
		$view = ( object ) [ 
				'action' => 'add',
				'title' => '新建',
				'batchShow' => 'none',
				'timeShow' => 'none',
				'areaShow' => 'none',
				'batchCheck' => '',
				'timeCheck' => '',
				'areaCheck' => '',
				'prodInShow' => 'none',
				'prodInOrderCheck' => '',
				'outOrderShow' => 'none',
				'outOrderCheck' => '',
				'saletoagcShow' => 'none',
				'saletoagcCheck' => '',
				'expireShow' => 'none',
				'expireCheck' => '' ,
				'is_pre' => true,
				'category_display'=>'none',
				'product_display'=>'none',
				'tagCheck' => '',
				'tagShow' => 'none',
		        'evilShow' => 'none',
		];
		$this->load->view ( 'activity_editsub', [ 
				'data' => $data,
				'view' => $view 
		] );
	}
	
	/**
	 * 子活动修改页
	 */
	public function editsub($id = NULL, $subid = null) {
		if (! isset ( $id ))
			exit ( '参数有误' );
		$curData = $this->activity_model->get ( $id );
		if ($curData) {
			$data = $this->activity_model->get_sub ( $subid );
			if (! $data)
				exit ();
			if ($data->parentId != $curData->id) {
				exit ( '非法操作' );
			}
		} else {
			exit ();
		}
		if ($data->mchId != $this->mchId)
			exit ( '无权操作' );
		$data->parentName = $curData->name;
		$batchShow = $timeShow = $areaShow = $prodInShow = $outOrderShow = $saletoagcShow = $expireShow = $tagShow = '';
		$batchCheck = $timeCheck = $areaCheck = $prodInOrderCheck = $outOrderCheck = $saletoagcCheck = $expireCheck = $tagCheck = 'checked';
		if (empty ( $data->batchId )) {
			$batchShow = 'none';
			$batchCheck = '';
		}
		if (empty ( $data->startTime ) || empty ( $data->endTime )) {
			$timeShow = 'none';
			$timeCheck = '';
		}
		if (empty ( $data->areaCode )) {
			$areaShow = 'none';
			$areaCheck = '';
		}
		if (empty ( $data->prodInOrderId )) {
			$prodInShow = 'none';
			$prodInOrderCheck = '';
		}
		if (empty ( $data->outOrderId )) {
			$outOrderShow = 'none';
			$outOrderCheck = '';
		}
		if (empty ( $data->saletoagc )) {
			$saletoagcShow = 'none';
			$saletoagcCheck = '';
		}
		if (empty ( $data->expireTime )) {
			$expireShow = 'none';
			$expireCheck = '';
		}
		if (! empty ( $data->startTime ))
			$data->startTime = date ( 'Y-m-d H:i:s', $data->startTime );
		if (! empty ( $data->endTime ))
			$data->endTime = date ( 'Y-m-d H:i:s', $data->endTime );
		if (! empty ( $data->expireTime ))
			$data->expireTime = date ( 'Y-m-d H:i:s', $data->expireTime );
		if($data->productId == null){
			$is_only = true;
			$product_display = 'none';
		}else{
			$is_only = false;
			$product_display = '';
		}
		if($data->categoryId === null || $data->categoryId === ''){
			$is_pre = true;
			$category_display = 'none';
		}else{
			$is_pre = false;
			$category_display = '';
		}
		$data->pStartTime = date ( 'Y-m-d H:i:s', $curData->startTime );
		$data->pEndTime = date ( 'Y-m-d H:i:s', $curData->endTime );
		if (empty ( $data->tagId )) {
			$tagShow = 'none';
			$tagCheck = '';
		}
		if (empty ( $data->forEvil )) {
		    $evilShow = 'none';
		    $is_forevil = false;
		}else{
		    $evilShow = '';
		    $is_forevil = true;
		}
		$view = ( object ) [ 
				'action' => 'edit',
				'title' => '修改',
				'batchShow' => $batchShow,
				'timeShow' => $timeShow,
				'areaShow' => $areaShow,
				'batchCheck' => $batchCheck,
				'timeCheck' => $timeCheck,
				'areaCheck' => $areaCheck,
				'prodInShow' => $prodInShow,
				'prodInOrderCheck' => $prodInOrderCheck,
				'outOrderShow' => $outOrderShow,
				'outOrderCheck' => $outOrderCheck,
				'saletoagcShow' => $saletoagcShow,
				'saletoagcCheck' => $saletoagcCheck,
				'expireShow' => $expireShow,
				'expireCheck' => $expireCheck,
				'category_display'=>$category_display,
				'product_display'=>$product_display,
				'is_pre' =>$is_pre,
				'is_only' =>$is_only,
				'tagShow' => $tagShow,
		        'is_forevil' => $is_forevil,
		        'evilShow' => $evilShow,
				'tagCheck' => $tagCheck
		];
		$this->load->view ( 'activity_editsub', [ 
				'data' => $data,
				'view' => $view
		] );
	}
	
	/*
	 * 显示活动详情
	 */
	public function getsubInfo() {
		$parentid = $this->input->post ( 'parentid' );
		$id = $this->input->post ( 'id' );
		if (! isset ( $parentid ))
			exit ( '参数有误' );
		$curData = $this->activity_model->get ( $parentid );
		if ($curData) {
			$data = $this->activity_model->get_sub ( $id );
			if (! $data)
				exit ( 'emptydata' );
			if ($data->parentId != $curData->id) {
				exit ( '非法操作' );
			}
		} else {
			exit ( 'else' );
		}
		if ($data->mchId != $this->mchId)
			exit ( '无权操作' );
		$data->parentName = $curData->name;
		$data->webAppName = $data->webAppId ? $this->webapp_model->get ( $data->webAppId )->appName : '';
		$data->area = $data->areaCode ? $this->activity_model->get_area_byCode ( $data->areaCode )->name : '';
		$data->prodInOrder = $data->prodInOrderId ? $this->activity_model->get_tts_order ( $data->prodInOrderId )->orderNo : ''; // 关联入库单
		$data->prodOutOrder = $data->outOrderId ? $this->activity_model->get_tts_order ( $data->outOrderId )->orderNo : ''; // 关联出库单
		$data->saletoagc = $data->saletoagc ? $this->activity_model->get_area_byCode ( $data->saletoagc )->name : '';
		$batchInfo = $this->batch_model->get_by_id ( $data->batchId );
		$data->batchNo = ($data->batchId && $batchInfo->rowStatus == 0) ? $batchInfo->batchNo : ''; // 需要判断乐码状态
		
		switch ($data->activityType) {
			case '0' : // 红包策略
				$row = $this->red_packet_model->get ( $data->detailId );
				if ($row)
					$data->details = $row->name;
				else
					$data->details = "无";
				break;
			case '1' : // 欢乐币策略
				$data->details = '乐币活动暂未开启';
				break;
			case '2' : // 乐券策略
				$row = $this->card_model->get_cardgroup ( $data->detailId );
				if ($row)
					$data->details = $row->title;
				else
					$data->details = "无";
				break;
			case '3' : // 组合策略
				$row = $this->mixstrategy_model->get ( $data->detailId );
				if ($row)
					$data->details = $row->name;
				else
					$data->details = "无";
				break;
			case '4' : // 积分策略
				$row = $this->point_model->get ( $data->detailId );
				if ($row)
					$data->details = $row->name;
				else
					$data->details = "无";
				break;
			case '5' : // 叠加策略
				$row = $this->multistrategy_model->get ( $data->detailId );
				if ($row)
					$data->details = $row->name;
				else
					$data->details = "无";
				break;
			case '6' : // 累计策略
				$row = $this->accumstrategy_model->get ( $data->detailId );
				if ($row)
					$data->details = $row->name;
				else
					$data->details = "无";
				break;
			default :
				$data->details = '未定义活动类型';
		}
		if (! empty ( $data->startTime ))
			$data->startTime = date ( 'Y-m-d H:i:s', $data->startTime );
		if (! empty ( $data->endTime ))
			$data->endTime = date ( 'Y-m-d H:i:s', $data->endTime );
		if (! empty ( $data->expireTime ))
			$data->expireTime = date ( 'Y-m-d', $data->expireTime );
		$data->pStartTime = date ( 'Y-m-d H:i:s', $curData->startTime );
		$data->pEndTime = date ( 'Y-m-d H:i:s', $curData->endTime );
		echo json_encode ( $data );
		// $this->load->view('activity_showsub.php',['data'=>$data]);
	}
	/**
	 * 删除活动
	 */
	public function del() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$id = $this->input->post ( 'id' );
		if (! isset ( $id ))
			exit ( '参数有误' );
		$data = $this->activity_model->get ( $id );
		if ($data->mchId != $this->mchId)
			exit ( '无权操作' );
		$sdata = $this->activity_model->get_sub_by_mchid ( $this->mchId );
		foreach ( $sdata as $k => $v ) {
			if ($v->parentId == $id) {
				$result = [ 
						'errorCode' => 1,
						'errorMsg' => '此活动存在子活动，不能删除' 
				];
				echo json_encode ( $result );
				return;
			}
		}
		if ($data->state == 1) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '启用中的活动不能删除' 
			];
			echo json_encode ( $result );
			return;
		}
		// 策略操作记录
		try {
			$strategy_log = ( object ) [ 
					'type' => 4,
					'opration' => 'delete',
					'data' => '' 
			];
			$strategy_log->data = $this->activity_model->get ( $id );
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		$isdel = $this->activity_model->del_activity ( $id );
		// 策略操作记录
		try {
			if ($isdel) {
				$this->load->model ( 'strategy_log_model' );
				$this->strategy_log_model->add ( $strategy_log->type, $strategy_log->opration, json_encode ( $strategy_log->data ) );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
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
		if ($result ['errorCode'] == 0) { // -------ccz,日志，
			try {
				$logInfo = ( array ) $data;
				$logInfo ['id'] = $id;
				$logInfo ['op'] = $this->log_record->Delete;
				$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Activity );
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		echo json_encode ( $result );
		return;
	}
	
	/**
	 * 删除子活动
	 */
	public function delsub() {
		$id = $this->input->post ( 'id' );
		if (! isset ( $id ))
			exit ( '参数有误' );
		$data = $this->activity_model->get_sub ( $id );
		if ($data->mchId != $this->mchId)
			exit ( '无权操作' );
		if ($data->state == 1) {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => '启用中的活动不能删除' 
			];
		} else {
			// 策略操作记录
			try {
				$strategy_log = ( object ) [ 
						'type' => 4,
						'opration' => 'delete_sub',
						'data' => '' 
				];
				$strategy_log->data = $this->activity_model->get_sub ( $id );
			} catch ( Exception $e ) {
				log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
			}
			// 策略操作记录 end
			$isdel = $this->activity_model->del_sub_activity ( $id );
			// 策略操作记录
			try {
				if ($isdel) {
					$this->load->model ( 'strategy_log_model' );
					$this->strategy_log_model->add ( $strategy_log->type, $strategy_log->opration, json_encode ( $strategy_log->data ) );
				}
			} catch ( Exception $e ) {
				log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
			}
			// 策略操作记录 end
			
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
		}
		if ($result ['errorCode'] == 0) { // -------ccz,日志，batch修改
			try {
				$logInfo = ( array ) $data;
				$logInfo ['id'] = $id;
				$logInfo ['op'] = $this->log_record->Delete;
				$logInfo ['parentName'] = $this->activity_model->get ( $data->parentId )->name;
				$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Activity );
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
	}

	public function preview_subactivity_info() {
		if ($this->mchId != 173 && $this->mchId != 0) {
			exit('403 Forbidden.');
		}
		$subActivityId = $this->input->get('id');
		$info = $this->activity_model->getSubActivityPreview($subActivityId);
		$data = [
			'data' => $info,
			'errmsg' => NULL,
			'errcode' => 0,
		];
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
	
	/**
	 * 启用
	 */
	public function start($type, $confirm = NULL) {
		$id = $this->input->post ( 'id' );
		if (! isset ( $id ))
			exit ( '参数有误' );
			// 策略操作记录
		try {
			if (( int ) $type === 1) {
				$strategy_log = ( object ) [ 
						'type' => 4,
						'opration' => 'start_sub',
						'data' => '' 
				];
				$strategy_log->data = $this->activity_model->get_sub ( $id );
			}
			if (( int ) $type === 0) {
				$strategy_log = ( object ) [ 
						'type' => 4,
						'opration' => 'start',
						'data' => '' 
				];
				$strategy_log->data = $this->activity_model->get ( $id );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		if (( int ) $type === 1) {
			$data = $this->activity_model->get_sub ( $id );
			if ($data->mchId != $this->mchId)
				exit ( '无权操作' );
			if (($this->mchId == 173 || $this->mchId == 0) && ! isset($confirm)) {
				$subActivity = $this->activity_model->get_sub($id);
				if ($subActivity->activityType === '3') {
					$info = $this->activity_model->getSubActivityPreview($id);
					if (! empty($info)) {
						$data = [
							'data' => $info,
							'errorMsg' => '活动启用信息：',
							'errorCode' => 173,
						];
						$this->output->set_content_type('application/json')->set_output(json_encode($data));
						return;
					}
				}
			}
			$isstart = $this->activity_model->start_sub_activity ( $id );
		} else if (( int ) $type === 0) {
			$data = $this->activity_model->get ( $id );
			if ($data->mchId != $this->mchId)
				exit ( '无权操作' );
			$isstart = $this->activity_model->start_activity ( $id );
		}
		if ($isstart) {
			$result = [ 
					'errorCode' => 0,
					'errorMsg' => '' 
			];
		} else {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => $isstart 
			];
		}
		// 策略操作记录
		try {
			if ($isstart) {
				$this->load->model ( 'strategy_log_model' );
				$this->strategy_log_model->add ( $strategy_log->type, $strategy_log->opration, json_encode ( $strategy_log->data ) );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		
		/* ---------------操作日志记录start-------------- */
		if ($result ['errorCode'] == 0) { // -------ccz,日志，batch修改
			try {
				$logInfo = ( array ) $data;
				$logInfo ['id'] = $id;
				$logInfo ['op'] = $this->log_record->Start;
				if (( int ) $type == 1) {
					$logInfo ['parentName'] = $this->activity_model->get ( $data->parentId )->name;
				}
				$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Activity );
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		/* ----------------操作日志记录end-------------------- */
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
	}
	/**
	 * 停用
	 */
	public function stop($type) {
		$id = $this->input->post ( 'id' );
		if (! isset ( $id ))
			exit ( '参数有误' );
			// 策略操作记录
		try {
			if (( int ) $type === 1) {
				$strategy_log = ( object ) [ 
						'type' => 4,
						'opration' => 'stop_sub',
						'data' => '' 
				];
				$strategy_log->data = $this->activity_model->get_sub ( $id );
			}
			if (( int ) $type === 0) {
				$strategy_log = ( object ) [ 
						'type' => 4,
						'opration' => 'stop',
						'data' => '' 
				];
				$strategy_log->data = $this->activity_model->get ( $id );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		if (( int ) $type === 1) {
			$data = $this->activity_model->get_sub ( $id );
			if ($data->mchId != $this->mchId)
				exit ( '无权操作' );
			$isstart = $this->activity_model->stop_sub_activity ( $id );
		} else if (( int ) $type === 0) {
			$data = $this->activity_model->get ( $id );
			if ($data->mchId != $this->mchId)
				exit ( '无权操作' );
			$isstart = $this->activity_model->stop_activity ( $id );
		}
		if ($isstart) {
			$result = [ 
					'errorCode' => 0,
					'errorMsg' => '' 
			];
		} else {
			$result = [ 
					'errorCode' => 1,
					'errorMsg' => $isstart 
			];
		}
		// 策略操作记录
		try {
			if ($isstart) {
				$this->load->model ( 'strategy_log_model' );
				$this->strategy_log_model->add ( $strategy_log->type, $strategy_log->opration, json_encode ( $strategy_log->data ) );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		if ($result ['errorCode'] == 0) { // -------ccz,日志，batch修改
			try {
				$logInfo = ( array ) $data;
				$logInfo ['id'] = $id;
				$logInfo ['op'] = $this->log_record->Stop;
				if (( int ) $type == 1) {
					$logInfo ['parentName'] = $this->activity_model->get ( $data->parentId )->name;
				}
				$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Activity );
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
	}
	
	/**
	 * 保存活动
	 */
	public function save() {
		$saveData = [ 
				'name' => $this->input->post ( 'name' ),
				'startTime' => strtotime ( $this->input->post ( 'startTime' ) ),
				'endTime' => strtotime ( $this->input->post ( 'endTime' ) ),
				'description' => $this->input->post ( 'description' ),
				'imgUrl' => $this->input->post ( 'imgUrl' ),
				'updateTime' => time () 
		];
		if (empty ( $saveData ['startTime'] ) || empty ( $saveData ['endTime'] )) {
			$data = [ 
					'errorCode' => 1,
					'errorMsg' => '提交数据有误' 
			];
		} else {
			$id = $this->input->post ( 'id' );
			// 策略操作记录
			try {
				if (! empty ( $id )) {
					$strategy_log = ( object ) [ 
							'type' => 4,
							'opration' => 'update',
							'data' => '' 
					];
					$strategy_log->data = $this->activity_model->get ( $id );
				}
			} catch ( Exception $e ) {
				log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
			}
			// 策略操作记录 end
			if ($id != NULL && ! empty ( $id )) {
				$save = $this->activity_model->update_activity ( $id, $saveData );
			} else {
				$saveData = array_merge ( $saveData, [ 
						'mchId' => $this->mchId,
						'state' => 0,
						'createTime' => time () 
				] );
				$save = $this->activity_model->add_activity ( $saveData );
			}
			// 策略操作记录
			try {
				if (! empty ( $id ) && $save) {
					$this->load->model ( 'strategy_log_model' );
					$this->strategy_log_model->add ( $strategy_log->type, $strategy_log->opration, json_encode ( $strategy_log->data ) );
				}
			} catch ( Exception $e ) {
				log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
			}
			// 策略操作记录 end
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
		}
		/* -------记录日志---------add by ccz */
		if ($data ['errorCode'] == 0) { // -------ccz,日志，batch修改
			try {
				$logInfo = ( array ) $saveData;
				if ($id != NULL && ! empty ( $id )) {
					$logInfo ['id'] = $id;
					$logInfo ['op'] = $this->log_record->Update;
				} else {
					$logInfo ['id'] = $save;
					$logInfo ['op'] = $this->log_record->New;
				}
				$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Activity );
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		/* -------记录日志---------end */
		$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
	}
	
	/**
	 * 保存子活动
	 */
	public function savesub() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$saveData = [ 
				'parentId' => $this->input->post ( 'parentId' ),
				'name' => $this->input->post ( 'name' ),
				'content' => $this->input->post ( 'content' ),
				'role' => $this->input->post ( 'role' ),
				'activityType' => $this->input->post ( 'activityType' ),
				'detailId' => $this->input->post ( 'detailId' ),
				'webAppId' => $this->input->post ( 'webAppId' ),
				'geoNeeded' => $this->input->post ( 'geoNeeded' ),
				'subscribeNeeded' => $this->input->post ( 'subscribeNeeded' ),
				'startTime' => $this->input->post ( 'startTime' ),
				'endTime' => $this->input->post ( 'endTime' ),
				'areaCode' => $this->input->post ( 'areaCode' ),
				'batchId' => $this->input->post ( 'batchId' ),
				'prodInOrderId' => $this->input->post ( 'prodInOrderId' ),
				'outOrderId' => $this->input->post ( 'outOrderId' ),
				'saletoagc' => $this->input->post ( 'saletoagc' ),
				'expireOprt' => $this->input->post ( 'expireOprt' ),
				'expireTime' => $this->input->post ( 'expireTime' ),
				'binding' => $this->input->post ( 'binding' ),
				'updateTime' => time (),
				'productId' => $this->input->post('productId'),
				'categoryId' => $this->input->post('categoryId'),
		        'tagId' => $this->input->post('tagId'),
		        'forEvil' => $this->input->post('forEvil')
		];
		if (empty ( $saveData ['expireTime'] )) {
			$saveData ['expireTime'] = NULL;
		} else {
			$saveData ['expireTime'] = strtotime ( $saveData ['expireTime'] );
		}
		if (! empty ( $saveData ['startTime'] ))
			$saveData ['startTime'] = strtotime ( $saveData ['startTime'] );
		if (! empty ( $saveData ['endTime'] ))
			$saveData ['endTime'] = strtotime ( $saveData ['endTime'] );
		if (empty ( $saveData ['parentId'] ) || empty ( $saveData ['name'] )) {
			$data = [ 
					'errorCode' => 1,
					'errorMsg' => '提交数据有误' 
			];
			echo json_encode ( $data );
			return;
		}
		if(empty($saveData['productId'])){
			$saveData['productId'] = null;
		}
		if(empty($saveData['categoryId'])){
			$saveData['categoryId'] = null;
		}
		$id = $this->input->post ( 'id' );
		$activity = $this->activity_model->get ( $saveData ['parentId'] );
		if (! $activity) {
			$data = [ 
					'errorCode' => 1,
					'errorMsg' => '数据有误' 
			];
			echo json_encode ( $data );
			return;
		}
		if (! ($saveData ['startTime'] >= $activity->startTime && $saveData ['endTime'] <= $activity->endTime)) {
			$data = [ 
					'errorCode' => 1,
					'errorMsg' => '子活动时间范围不能超出父活动的时间范围' 
			];
			echo json_encode ( $data );
			return;
		}
		// 策略操作记录
		try {
			if (! empty ( $id )) {
				$strategy_log = ( object ) [ 
						'type' => 4,
						'opration' => 'update_sub',
						'data' => '' 
				];
				$strategy_log->data = $this->activity_model->get_sub ( $id );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		if ($id != NULL && ! empty ( $id )) {
			$subActivity = $this->activity_model->get_sub ( $id );
			if (! $subActivity) {
				$data = [ 
						'errorCode' => 1,
						'errorMsg' => '数据不存在' 
				];
				echo json_encode ( $data );
				return;
			}
			if ($subActivity->mchId != $this->mchId) {
				$data = [ 
						'errorCode' => 1,
						'errorMsg' => '无权操作' 
				];
				echo json_encode ( $data );
				return;
			}
			$save = $this->activity_model->update_sub_activity ( $id, $saveData );
		} else {
			$saveData = array_merge ( $saveData, [ 
					'mchId' => $this->mchId,
					'state' => 0,
					'rowStatus' => 0,
					'createTime' => time () 
			] );
			$detail = $this->red_packet_model->get ( $saveData ['detailId'] );
			$save = $this->activity_model->add_sub_activity ( $saveData );
		}
		// 策略操作记录
		try {
			if (! empty ( $id ) && $save) {
				$this->load->model ( 'strategy_log_model' );
				$this->strategy_log_model->add ( $strategy_log->type, $strategy_log->opration, json_encode ( $strategy_log->data ) );
			}
		} catch ( Exception $e ) {
			log_message ( 'error', 'strategy_log_error:' . $e->getMessage () );
		}
		// 策略操作记录 end
		if (! isset ( $data )) {
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
		}
		/* ----子活动新增/修改日志 add by cw---- */
		if ($data ['errorCode'] == 0) {
			try {
				$activityLog ['mchId'] = $this->mchId;
				if ($id != NULL && ! empty ( $id )) {
					$activityLog ['activityId'] = $id;
					$cate_pro = $this->activity_model->get_cate_product ( $this->mchId, $id );
					$activityLog ['categoryId'] = $cate_pro ['categoryId'] ? $cate_pro ['categoryId'] : 0;
					$activityLog ['productId'] = $cate_pro ['productId'] ? $cate_pro ['productId'] : 0;
					$activityLog ['batchId'] = $cate_pro ['batchId'] ? $cate_pro ['batchId'] : 0;
					$policyInfo = $this->activity_model->get_activity_policy ( $this->mchId, $id );
					$activityLog ['Json'] = json_encode ( $policyInfo ['data'] );
					$activityLog ['policyName'] = $policyInfo ['policyName'];
					$activityLog ['policyLevel'] = $policyInfo ['policyLevel'];
				} else {
					$activityLog ['activityId'] = $save;
					$cate_pro = $this->activity_model->get_cate_product ( $this->mchId, $save );
					$activityLog ['categoryId'] = $cate_pro ['categoryId'] ? $cate_pro ['categoryId'] : 0;
					$activityLog ['productId'] = $cate_pro ['productId'] ? $cate_pro ['productId'] : 0;
					$activityLog ['batchId'] = $cate_pro ['batchId'] ? $cate_pro ['batchId'] : 0;
					$policyInfo = $this->activity_model->get_activity_policy ( $this->mchId, $save );
					$activityLog ['Json'] = json_encode ( $policyInfo ['data'] );
					$activityLog ['policyName'] = $policyInfo ['policyName'];
					$activityLog ['policyLevel'] = $policyInfo ['policyLevel'];
				}
				$activityLog ['theTime'] = date ( 'Y-m-d' );
				$result=$this->activity_model->get_activity_log($this->mchId,$activityLog['activityId'],$activityLog['theTime']);
				if($result){
					if($activityLog['policyName']!=''){//排除无内容的策略
						$this->activity_model->update_activity_log($result->id,$activityLog);
					}
				}else{
					if($activityLog['policyName']!=''){//排除无内容的策略
						$this->activity_model->add_activity_log($activityLog);
					}
				}

				
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		/* ----子活动新增/修改日志 add by cw---- */
		/* -------记录日志---------add by ccz */
		if ($data ['errorCode'] == 0) { // -------ccz,日志，
			try {
				$logInfo = ( array ) $saveData;
				if ($id != NULL && ! empty ( $id )) {
					$logInfo ['id'] = $id;
					$logInfo ['op'] = $this->log_record->Update;
				} else {
					$logInfo ['id'] = $save;
					$logInfo ['op'] = $this->log_record->Add;
				}
				$logInfo ['parentName'] = $this->activity_model->get ( $saveData ['parentId'] )->name;
				$this->log_record->addLog ( $this->mchId, $logInfo, $this->log_record->Activity );
			} catch ( Exception $e ) {
				log_message ( 'error', 'mch_log_error:' . $e->getMessage () );
			}
		}
		echo json_encode ( $data );
		return;
	}
	
	/**
	 * 全策略H5配置页面
	 */
	public function h5setting($name = 'allstrategy') {
		$mchId = $this->session->userdata ( 'mchId' );
		if (! isset ( $mchId )) {
			echo '未登录';
			return;
		}
		$config = NULL;
		$webappId = NULL;
		$apps = $this->webapp_model->get_by_mchid ( $mchId );
		foreach ( $apps as $k => $v ) {
			if (stripos ( $v->appPath, '/' . $name . '/' ) != FALSE) {
				$webappId = $v->id;
				break;
			}
		}
		if ($webappId === NULL) {
			echo 'H5不存在';
			return;
		}
		$appConfig = $this->webapp_model->get_webapp_config ( $webappId, $mchId );
		if ($appConfig) {
			$config = $appConfig->data;
		}
		$this->load->view ( 'h5setting/' . $name, [ 
				'h5name' => $name,
				'config' => $config 
		] );
	}
	/**
	 * 全策略H5配置页面-获取qrcode
	 */
	public function h5setting_getqrcode() {
		header ( "Content-Type: image/jpeg;text/html; charset=utf-8" );
		ini_set ( 'user_agent', 'Mozilla/5.0 (Linux; Android 5.1.1; Mi-4c Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036215 Safari/537.36 MicroMessenger/6.3.16.49_r03ae324.780 NetType/WIFI Language/zh_CN' );
		$mchId = $this->session->userdata ( 'mchId' );
		if (! isset ( $mchId )) {
			return;
		}
		$merchant = $this->merchant_model->get ( $mchId );
		$url = $merchant->wxQrcodeUrl;
		$width = 300;
		$height = 300;
		$image = new Imagick ( urldecode ( $url ) );
		$image->resizeimage ( $width, $height, Imagick::FILTER_LANCZOS, 1 );
		echo $image->getimageblob ();
	}
	/**
	 * 全策略H5配置页面-保存配置
	 */
	public function h5setting_save($name = 'allstrategy') {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$mchId = $this->session->userdata ( 'mchId' );
		if (! isset ( $mchId )) {
			echo '未登录';
			return;
		}
		$config = $this->input->post ( 'config' );
		$webappId = NULL;
		$apps = $this->webapp_model->get_by_mchid ( $mchId );
		foreach ( $apps as $k => $v ) {
			if (stripos ( $v->appPath, '/' . $name . '/' ) != FALSE) {
				$webappId = $v->id;
				break;
			}
		}
		if ($webappId === NULL) {
			echo 'H5不存在';
			return;
		}
		$save = $this->webapp_model->save_webapp_config ( $webappId, $mchId, $config );
		$result = ( object ) [ 
				'errcode' => 0,
				'errmsg' => '' 
		];
		if (! $save) {
			$result->errcode = 1;
			$result->errmsg = '保存失败';
		}
		echo json_encode ( $result );
		return;
	}
}
