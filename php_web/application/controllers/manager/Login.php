<?php
class Login extends MerchantController {

    public function index() {
        session_destroy();
        $this->load->view('login');
    }

}
