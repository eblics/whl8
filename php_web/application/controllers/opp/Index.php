<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api
 */
class Index extends OppController {

    public function __construct() {
        parent::__construct();
    }

    function index(){
        redirect('/merchant');
    }
}