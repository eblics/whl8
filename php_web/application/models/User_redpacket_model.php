<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_redpacket_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('app_model');
        $this->load->model('user_model');
        $this->load->model('red_packet_model');
        $this->load->model('card_model');
    }
    //app抽奖专用发放接口
    function send_after_subscribe($user_redpacket){
        $red_packet=$this->red_packet_model->get($user_redpacket->rpId);
        if(!$red_packet){
            return;
        }
        $this->db->trans_start();
        try{
            if($this->db->query("select id from user_redpackets where sended=0 and id=$user_redpacket->id  for update")->num_rows()==0){
                $this->db->trans_complete();
                return (object)['errcode'=>1,'errmsg'=>'此红包已发放'];
            }
            $user_account=(object)['userId'=>$user_redpacket->userId,'mchId'=>$user_redpacket->mchId,
                'moneyType'=>$red_packet->rpType,'amount'=>$user_redpacket->amount];
            $this->db->query("INSERT INTO user_accounts(userId,mchId,moneyType,amount)
                VALUES($user_account->userId,$user_account->mchId,$user_account->moneyType,$user_account->amount)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$user_account->amount");
            $this->db->set('sended',1)->where('id',$user_redpacket->id)->update('user_redpackets');
            $this->db->trans_complete();
            //app抽奖发放通知
            $user=$this->user_model->get($user_redpacket->userId);
            if($user){
                if($user_redpacket->scanId==-1 && !empty($user_redpacket->instId)){
                    $appInst=$this->app_model->get_inst($user_redpacket->instId);
                    log_message('debug','$appInst  data: '.var_export($appInst,TRUE));
                    if($appInst){
                        $config=json_decode($appInst->config);
                        //中奖通知模板消息写入数据库
                        $formatMsg=$this->wx3rd_lib->template_format_data($user->mchId,'get_redpacket',
                            ['恭喜您中奖啦！','中得“'.($user_redpacket->amount/100).'元红包”',$config->name,'见活动说明',str_replace(PHP_EOL,'',$config->desc),'点击查看']);
                        $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
                        $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
                        $formatMsg->message=trim($formatMsg->message,'"');
                        $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($user->mchId,'$user->openid','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");
                    }
                }
            }
            //app抽奖发放通知 end
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }
        catch(ErrorException $e){
            log_message('ERROR',$e);
            log_message('ERROR',var_export($red_packet,TRUE));
        }
    }

    function send_cards_after_subscribe($user_card,$user){
        $this->db->trans_start();
        try{
            if($this->db->query("select id from user_cards where sended=0 and id=$user_card->id for update")->num_rows()==0){
                $this->db->trans_complete();
                return (object)['errcode'=>1,'errmsg'=>'此乐券已发放'];
            }
            $user_cards_account=(object)['userId'=>$user_card->userId,'role'=>$user_card->role,'mchId'=>$user->mchId,'cardId'=>$user_card->cardId];
            $sql="INSERT INTO user_cards_account(userId,role,mchId,cardId,num)
                VALUES($user_cards_account->userId,$user_cards_account->role,$user_cards_account->mchId,$user_cards_account->cardId,IFNULL(num,0)+1)
                ON DUPLICATE KEY UPDATE num=IFNULL(num,0)+1";
            log_message('debug','send_cards_after_subscribe: '.var_export($sql,TRUE));
            $this->db->query($sql);
            $this->db->set('sended',1)->where('id',$user_card->id)->update('user_cards');
            $this->db->trans_complete();
            //app抽奖发放通知
            if($user_card->scanId==-1 && !empty($user_card->instId)){
                log_message('debug','send_cards_after_subscribe 模板消息 1: '.var_export($sql,TRUE));
                $appInst=$this->app_model->get_inst($user_card->instId);
                $card=$this->card_model->get($user_card->cardId);
                log_message('debug','send_cards_after_subscribe 模板消息 appInst: '.var_export($appInst,TRUE));
                log_message('debug','send_cards_after_subscribe 模板消息 card: '.var_export($card,TRUE));
                if($appInst && $card){
                    log_message('debug','send_cards_after_subscribe 模板消息 2: '.var_export($sql,TRUE));
                    $config=json_decode($appInst->config);
                    //中奖通知模板消息写入数据库
                    if(property_exists($card,'cardType') && $card->cardType == 1){
                        $formatMsg=$this->wx3rd_lib->template_format_data($user->mchId,'get_card_youzan',
                            ['恭喜您中奖啦！','中得“'.$card->title.'”',$config->name,'见活动说明',str_replace(PHP_EOL,'',$config->desc),'点击查看']);
                    }else{
                        $formatMsg=$this->wx3rd_lib->template_format_data($user->mchId,'get_card_youzan',
                            ['恭喜您中奖啦！','中得“'.$card->title.'”',$config->name,'见活动说明',str_replace(PHP_EOL,'',$config->desc),'点击查看']);
                    }
                    $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
                    $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
                    $formatMsg->message=trim($formatMsg->message,'"');
                    $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($user->mchId,'$user->openid','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");
                }
            }
            //app抽奖发放通知 end
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }
        catch(ErrorException $e){
            log_message('ERROR',$e);
            log_message('ERROR',var_export($user_card,TRUE));
        }
    }

    function send_points_after_subscribe($user_point,$user){
        $this->db->trans_start();
        try{
            if($this->db->query("select id from user_points where sended=0 and id=$user_point->id for update")->num_rows()==0){
                $this->db->trans_complete();
                return (object)['errcode'=>1,'errmsg'=>'此积分已发放'];
            }
            $user_points_account=(object)['userId'=>$user_point->userId,'role'=>$user_point->role,'mchId'=>$user->mchId,'amount'=>$user_point->amount];
            $sql="INSERT INTO user_points_accounts(userId,role,mchId,amount)
                VALUES($user_points_account->userId,$user_points_account->role,$user_points_account->mchId,$user_points_account->amount)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$user_points_account->amount";
            log_message('debug','send_points_after_subscribe: '.var_export($sql,TRUE));
            $this->db->query($sql);
            $this->db->set('sended',1)->where('id',$user_point->id)->update('user_points');
            $this->db->trans_complete();
            //app抽奖发放通知
            if($user_point->instId!=-1){
                $appInst=$this->app_model->get_inst($user_point->instId);
                if($appInst){
                    $config=json_decode($appInst->config);
                    //中奖通知模板消息写入数据库
                    $formatMsg=$this->wx3rd_lib->template_format_data($user->mchId,'get_card_youzan',
                        ['恭喜您中奖啦！','中得“'.$user_point->amount.'积分”',$config->name,'见活动说明',str_replace(PHP_EOL,'',$config->desc),'点击查看']);
                    $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
                    $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
                    $formatMsg->message=trim($formatMsg->message,'"');
                    $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($user->mchId,'$user->openid','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");
                }
            }
            //app抽奖发放通知 end
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }
        catch(ErrorException $e){
            log_message('ERROR',$e);
            log_message('ERROR',var_export($user_point,TRUE));
        }
    }

    function get_pending_packets($user){
        return $this->db->where("userId=$user->id AND sended=0")->get('user_redpackets')->result();
    }

    function get_pending_cards($user){
        return $this->db->where("userId=$user->id AND sended=0")->get('user_cards')->result();
    }

    function get_app_pending_packets($user,$instId){
        return $this->db->where("userId=$user->id AND sended=0 AND instId=$instId")->get('user_redpackets')->result();
    }

    function get_app_pending_cards($user,$instId){
        return $this->db->where("userId=$user->id AND sended=0 AND instId=$instId")->get('user_cards')->result();
    }

    function get_app_pending_points($user,$instId){
        return $this->db->where("userId=$user->id AND sended=0 AND instId=$instId")->get('user_points')->result();
    }
    

    function get_history_by_user_id($user_id,$mch_id){
        return $this->db->query(
            'select sum(amount) as amount from user_redpackets where userId=? and mchId=? and sended=1',[$user_id,$mch_id]
            )->row();
    }

    function get_remain_rp($user_id,$mch_id,$type){
        return $this->db->query(
            'select amount from user_accounts where userId=? and mchId=? and moneyType=? limit 1',[$user_id,$mch_id,$type]
            )->row();
    }
}
