<?php
class Index extends MerchantController {

    public function __construct() {
        parent::__construct();
        if (isset($_SESSION['role']) && $_SESSION['role'] === -1) {
           redirect('/charts/index');
           exit();
       }
    }

    public function index() {
        $this->load->model('batch_model');
        $currentMerchant = $this->getCurrentMerchant();
        $batchNumberTotal = $this->batch_model->get_batch_num_total($currentMerchant->id);
        $batnum = $this->merchant_model->get_remind($currentMerchant->id);
        if (empty($batnum) || $batnum->codeLimited == 0) {
            $tip = 0;
        } elseif ($batnum->codeLimited - $batchNumberTotal < 10000) {
            $tip = 1;
        } else {
            $tip = 0;
        }
        $this->load->view('index', ['tip' => $tip]);
    }
    
}
