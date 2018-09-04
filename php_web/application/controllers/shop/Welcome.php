<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Shop_Controller {
	
	public function index()	{
		session_destroy();
		$data['title'] = '关于';
		$this->load->view('page_welcome', $data);
	}
}
