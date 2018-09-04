<?php
/**
 * 应用登出控制器
 *
 * @author shizq <shizhiqiang@acctrue.com>
 */
class Signout extends Mobile_Controller {

    public function index() {
        $from = $this->input->get('from');
        session_destroy();
        if (isset($from)) {
            redirect($from);
        } else {
            redirect('/welcome');
        }
    }

}