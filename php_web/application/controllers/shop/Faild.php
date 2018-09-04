<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Faild extends Shop_Controller {

	public function index()	{
		$this->load->view('page_error', ['title' => '空的账户', 'errmsg' => '您的账户是空的']);
	}
}