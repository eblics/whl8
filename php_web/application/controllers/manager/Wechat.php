<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wechat extends MerchantController {
    
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->mchId=$this->session->userdata('mchId');
        $this->load->library('log_record');
    }

    /**
     *微信自定义菜单界面
     */
    public function wxmenu(){
        $this->load->view('wechat/wxmenu',['mchId'=>$this->mchId]);
    }
    
    /**
     * 获取消费者公众号原先菜单
     */
    public function get_menu_c(){
        $getres = $this->weixin_rest_api->get_menu($this->mchId,1);
        echo $getres;
    }
    /**
     * 更新消费者公众号微信菜单
     */
    public function update_menu_c(){
        $menu_data = $this->input->post('data');
        $getres = $this->weixin_rest_api->create_menu($this->mchId,$menu_data,1);
        /*-------记录日志---------add by ccz*/
        if($getres){//-------ccz,日志
            try{
                $logInfo = (array)$menu_data;
                $logInfo ['id'] = $this->mchId;
                $logInfo ['info'] = "【消费者】公众号微信菜单";
                $logInfo ['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Wechat);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo $getres;
    }
    /**
     * 获取供应链公众号原先菜单
     */
    public function get_menu_w(){
        $getres = $this->weixin_rest_api->get_menu($this->mchId,2);
        echo $getres;
    }
    /**
     * 更新供应链公众号微信菜单
     */
    public function update_menu_w(){
        $menu_data = $this->input->post('data');
        $getres = $this->weixin_rest_api->create_menu($this->mchId,$menu_data,2);
        /*-------记录日志---------add by ccz*/
        if($getres){//-------ccz,日志
            try{
                $logInfo = (array)$menu_data;
                $logInfo ['id'] =  $this->mchId;
                $logInfo ['info'] = "【供应链】公众号微信菜单";
                $logInfo ['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Wechat);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo $getres;
    }
    /**
     * 删除消费者公众号微信菜单
     */
    public function delete_menu_c(){
        $getres = $this->weixin_rest_api->delete_menu($this->mchId,1);
        /*-------记录日志---------add by ccz*/
        if($getres){//-------ccz,日志
            try{
                $logInfo = (array)$getres;
                $logInfo ['id'] = $this->mchId;
                $logInfo ['info'] = "【消费者】公众号微信菜单";
                $logInfo ['op'] = $this->log_record->Delete;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Wechat);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo $getres;
    }
    /**
     * 删除供应链公众号微信菜单
     */
    public function delete_menu_w(){
        $getres = $this->weixin_rest_api->delete_menu($this->mchId,2);
        if($getres){//-------ccz,日志
            try{
            //$logInfo = (array)$getres;
                $logInfo ['id'] = $this->mchId;
                $logInfo ['info'] = "【供应链】公众号微信菜单";
                $logInfo ['op'] = $this->log_record->Delete;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Wechat);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo $getres;
    }
    
    
}