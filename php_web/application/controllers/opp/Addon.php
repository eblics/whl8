<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Addon extends OppController {

    public function index() {
        $this->load->view('addon_index');
    }
}