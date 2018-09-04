<?php
class Group extends CI_Controller {

    public function scanpk_heart_beat() {
        $this->load->model('group_scanpk_model');
        $scanpks = $this->group_scanpk_model->get_all_doing();
        foreach ($scanpks as $k => $v) {
            $scanpkUsers = $this->group_scanpk_model->get_scanpk_users($v->id);
            foreach ($scanpkUsers as $k2 => $v2) {
                $this->group_scanpk_model->update_scanpk_scan_num($v,$v2->userId);
            }
        }
        
        //更新PK状态
        $scanpksNeedPay=$this->group_scanpk_model->get_all_nopay();
        foreach ($scanpksNeedPay as $k => $v) {
            $this->group_scanpk_model->update_scanpk_status($v,1);
        }

        //结算
        $scanpkPaying=$this->group_scanpk_model->get_all_paying();
        $this->group_scanpk_model->pay_scanpk($scanpkPaying);
        $ids='';
        foreach($scanpkPaying as $k=>$v){
            $ids.=($v->id.',');
        }
        echo date('Y-m-d H:i:s').' 扫码PK结算 ids:'.$ids;
    }

}