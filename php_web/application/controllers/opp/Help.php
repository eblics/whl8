<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends OppController {

    public function index() {
    	$section = $this->input->get('section');
    	if (! $section) {
    		$section = 0;
    	}
    	$data = ['section' => $section];
        $this->load->view('help', $data);
    }
}