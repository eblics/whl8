<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tag extends MerchantController{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('tag_model');
        $this->mchId = $this->session->userdata('mchId');
    }

    public function index(){
        //如无需使用留空即可
    }

    /**
     * 用户标签列表
     */
    public function lists(){
        $this->load->view('tag_list');
    }

    /**
     * 获取用户标签列表数据
     */
    public function get_list(){
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        if(!(isset($start)&&isset($length))){
            $start=0;
            $length=1000;
        }
        $data=$this->tag_model->get_list($this->mchId,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
	 * 新建标签
	 */
	public function add() {
		$data = ( object ) [ 
				'id' => '',
				'name' => '',
				'mchId' => $this->session->mchId
		];
		$view = ( object ) [ 
				'action' => 'add',
				'title' => '新建'
		];
		$this->load->view ( 'tag_edit', ['data' => $data,'view' => $view]);
	}

    /**
	 * 修改标签
	 */
	public function edit($id = null) {
		if (! isset ( $id )) exit ( '参数有误' );
        $data = $this->tag_model->get ( $id );
        if(! $data) exit ( '参数有误' );
		$view = ( object ) [ 
				'action' => 'edit',
				'title' => '修改'
		];
		$this->load->view ( 'tag_edit', [ 
				'data' => $data,
				'view' => $view 
		] );
	}
	
	/**
	 * 删除标签
	 */
	public function delete() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$id = $this->input->post ( 'id' );
		if ($id != NULL && ! empty ( $id )) {
			$data = $this->tag_model->get ( $id );
		}else{
			$result = [ 
					'errcode' => 1,
					'errmsg' => '操作失败' 
			];
			echo json_encode ( $result );
			return;
		}
		if (! $data) {
			$result = [ 
					'errcode' => 1,
					'errmsg' => '无权操作' 
			];
			echo json_encode ( $result );
			return;
		}
		if ($data->mchId != $this->mchId) {
			$result = [ 
					'errcode' => 1,
					'errmsg' => '无权操作' 
			];
			echo json_encode ( $result );
			return;
		}
		$isdel = $this->tag_model->delete ($this->mchId,$data->tagId);
		if ($isdel) {
			$result = [ 
					'errcode' => 0,
					'errmsg' => '' 
			];
		} else {
			$result = [ 
					'errcode' => 1,
					'errmsg' => $isdel 
			];
		}
		echo json_encode ( $result );
		return;
	}

    /**
	 * 保存标签
	 */
	public function save() {
		header ( "Content-type", 'application/json;charset=utf-8;' );
		$id = $this->input->post ( 'id' );
		$name = $this->input->post ( 'name' );
        if ($id != NULL && ! empty ( $id )) {
            $data = $this->tag_model->get ( $id );
            if (! $data) {
                $result = [ 
                        'errcode' => 1,
                        'errmsg' => '无权操作' 
                ];
                echo json_encode ( $result );
                return; 
            }
            if ($data->mchId != $this->mchId) {
                $result = [ 
                        'errcode' => 1,
                        'errmsg' => '无权操作' 
                ];
                echo json_encode ( $result );
                return;
            }
			$save = $this->tag_model->update ($this->mchId,$data->tagId,$name);
			if ($save) {
				$result = [ 
						'errcode' => 0,
						'errmsg' => '' 
				];
			} else {
				$result = [ 
						'errcode' => 1,
						'errmsg' => '保存失败'
				];
			} 
			echo json_encode ( $result );
			return;
        }
		
		$save = $this->tag_model->add ( $this->mchId,$name );
		if ($save) {
			$result = [ 
					'errcode' => 0,
					'errmsg' => '' 
			];
		} else {
			$result = [
					'errcode' => 1,
					'errmsg' => $save
			];
		} 
		echo json_encode ( $result );
		return;
	}

}
