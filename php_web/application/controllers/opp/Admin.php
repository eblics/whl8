<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends OppController {

	public function index() {
		$this->load->view('admin');
	}

	public function add() { 
		$this->load->view('admin_edit');
	}

	public function edit() {
		$admin_id = $this->input->get('id');
		if (! isset($admin_id)) {
			show_404();
		} else {
			$this->load->model('OAdmin_model', 'admin');
			$admin = $this->admin->get($admin_id);
			$this->load->view('admin_edit', ['admin' => $admin]);
		}
	}

	public function profile() {
		$this->load->model('OAdmin_model', 'admin');
		$admin = $this->getCurrentUser();
		$admin_id = $admin->id;
		$admin = $this->admin->get($admin_id);
		$this->load->view('admin_profile', ['admin' => $admin]);
	}

	public function passwd() {
		$this->load->view('admin_passwd');
	}

	public function dynamic() {
    	$time = $this->input->get('time');
    	if (! isset($time)) {
    		$time = 'today';
    	}
    	$data = [];
        $this->load->view('admin_dynamic', $data);
    }

    public function signout() {
    	$admin = $this->getCurrentUser();
		$admin_id = $admin->id;
		$this->saveDynamic('退出系统', $admin_id, DynamicTypeEnum::Admin);
       session_destroy();
       redirect('/login');
    }


    // 用于测试
    public function insert_jokes() {
    	$num = trim($this->input->get('num'));
    	$unix_time = 1464762461;
    	$text = "这是测试的笑话啊";

    	for($i=0;$i<=$num;$i++){
    		$text_i = $i.$text;
    		$sql = "insert into jokes (text,theTime) values('$text_i','$unix_time')";
    		$this->db->query($sql);
    		if($i == 1000000){
    			echo "成功";
    			echo "<br>";
    		}
    	}
    	echo "目前总笑话数:".$this->db->count_all('jokes');

    }
    //删除测试数据
    public function delete_test_jokes(){
    	$unix_time = 1464762461;
    	$sql = "delete from jokes where theTime='$unix_time'";
    	$this->db->query($sql);
    	echo '清理完毕';
    	echo "<br>";
    	echo '剩余总笑话数:'.$this->db->count_all('jokes');
    }
}