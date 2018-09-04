<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Red_packet_ajax_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('merchant_model');
        $this->load->model('trigger_model');
        $this->load->model('scan_log_model');
    }

    function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('red_packets')->row();
    }
    //分级红包情况下，按优先级获取子项
    function get_subs_by_priority($id,$priority){
        $sql="select sub.* from red_packets_sub sub inner join red_packets rp
            on sub.parentId=rp.id where rp.id=$id and sub.remainNum>0";
        if($priority==0){
            $order=' order by rand()';
        }
        else if($priority==1){
            $order=' order by amount';
        }
        else if($priority==2){
            $order=' order by amount desc';
        }
        $sql=$sql.$order;
        return $this->db->query($sql)->result();
    }

    function send($red_packet,$amount,$activity,$scaninfo,$sub=NULL){
        $openid=$this->session->userdata('openid_'.$scaninfo->mchId);
        if(!isset($openid)) return;
        $this->db->trans_start();
        $user=$this->user_model->get_by_openid($openid);
        $user_redpacket=(object)[
            'userId'=>$user->id,
            'mchId'=>$user->mchId,
            'rpId'=>$red_packet->id,
            'amount'=>$amount,
            'scanId'=>isset($scaninfo->id)?$scaninfo->id:-1,
            'instId'=>-1,
            'code'=>$scaninfo->code,
            'role'=>$activity->role,
            'getTime'=>time()
        ];
        // $hasScan=$this->db->where('scanId',$scaninfo->id)->get('user_redpackets')->row();
        // if($hasScan){
        //     $user_redpacket->scanId=-$scaninfo->id;
        // }else{
        //     $user_redpacket->scanId=$scaninfo->id;
        // }
        //活动需要订阅时，将红包设为pending，待用户订阅后到账
        $user_redpacket->sended=(!$activity->subscribeNeeded||$user->subscribe);
        //记录用户红包
        $this->db->insert('user_redpackets',$user_redpacket);
        if(!$scaninfo->over) $scaninfo->over=true;
        $scaninfo->rewardTable='user_redpackets';
        if(empty($scaninfo->rewardId)){
            $scaninfo->rewardId=$this->db->insert_id();
        }
        //修改为写入消息队列
        $this->scan_log_model->update($scaninfo);
        // $this->db->where('id',$scaninfo->id)->update('scan_log',$scaninfo);
        if($user_redpacket->sended){
            $user_account=(object)['userId'=>$user->id,'mchId'=>$scaninfo->mchId,
            'moneyType'=>$red_packet->rpType,'amount'=>$amount];
            $sql="INSERT INTO user_accounts(userId,mchId,moneyType,amount)
                VALUES($user_account->userId,$user_account->mchId,$user_account->moneyType,IFNULL(amount,0)+$user_account->amount)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$user_account->amount";
            $this->db->query($sql);

        }
        // user_packets 插入触发器 add by cw
        $this->trigger_model->trigger_user_redpacket_insert($scaninfo,$user_redpacket);
        // user_packets 插入触发器 end

        //不再直接写入数据库，而是到过期时间时更新一次，去掉，由trigger.js获取limit_zone进行更新
        //如果不是分级红包，扣除主表
        // if(!isset($sub)){
        //     //限制数量，扣除数量
        //     if((int) $red_packet->limitType===0){
        //         $red_packet->remainNum-=1;
        //     }
        //     //限制金额，扣除金额
        //     else if($red_packet->limitType==1){
        //         $red_packet->remainAmount-=$amount;
        //     }
        //     $this->db->where('id',$red_packet->id)->update('red_packets',$red_packet);
        // }
        // else{
        //     $sub->remainNum-=1;
        //     $this->db->where('id',$sub->id)->update('red_packets_sub',$sub);
        // }
        $this->db->trans_complete();
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }


    function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('red_packets')->result();
    }
    function add_redpacket($data){
        return $this->db->insert('red_packets',$data);
    }
    function update_redpacket($id,$data){
        return $this->db->where('id',$id)->update('red_packets',$data);
    }
    function del_redpacket($id){
        return $this->db->where('id',$id)->update('red_packets',['rowStatus'=>1]);
    }
    function get_sub($id){
        return $this->db->where('id',$id)->get('red_packets_sub')->row();
    }
    function add_redpacket_sub($data){
        return $this->db->insert('red_packets_sub',$data);
    }
    function update_redpacket_sub($id,$data){
        return $this->db->where('id',$id)->update('red_packets_sub',$data);
    }
    function del_redpacket_sub($id){
        return $this->db->where('id',$id)->delete('red_packets_sub');
    }
    function get_sub_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->order_by('id','desc')->get('red_packets_sub')->result();
    }
    /**
     * 尝试抽红包
     * @param  [type] $id       [description]
     * @param  [type] $activity [description]
     * @param  [type] $scaninfo [description]
     * @return [type]           [description]
     */
    function try_red_packet($id,$activity,$scaninfo){
        $result=(object)([
                'errcode'=>0,
                'errmsg'=>'',
                'data'=>NULL]);

        $red_packet=$this->get($id);
        if(!isset($red_packet)){
            $result->errcode=12;
            $result->errmsg='没有这个红包策略';
            return $result;
        }
        //如果是分级红包，走分级红包流程
        if($red_packet->levelType==1){
            return $this->try_red_packet_level($red_packet,$activity,$scaninfo,$result);
        }
        if(!$this->is_lucky($red_packet->probability)){
            $result->errcode=20;
            $result->errmsg='运气不够好哦';
            if($red_packet->failureType==0){
                $joke=$this->jokes_model->get_joke();
                $result->alt_title=$joke->title;
                $result->alt_text=$joke->text;
            }
            return $result;
        }
        //如果是限制数量，检查红包是否发完
        if(($red_packet->limitType==0)){
            $rp_cache_key="red_packets.remainNum.id.$red_packet->id";
            //此处首先将数量或金额从缓存中减去，是为了避免高并发下的问题，因为此时如果从数据库
            //加锁处理，会引起大量阻塞。假设余额S=3，A用户提取4，B用户提取2，C用户提取。如果A
            //与B先后执行到此处，首先是A扣减，此时余额为-1，则A无法领取，B用户执行后为-3，B也
            //无法提取。A与B执行结束后，S再次恢复为3，此时C执行到此处，则有S=2，C可正常提取。
            //也就是说，在策略余额不足时，可能会出现先到者未得的情况。鉴于这些情况出现在抽奖临近
            //结束时，没必要为了这个业务的严谨性牺牲并发性能，因此不予加锁处理。
            $this->dbhelper->cache_zincrby("limit_zone",$rp_cache_key,-1);
            if((int)$this->dbhelper->cache_zscore('limit_zone',$rp_cache_key)<0){
                $result->errcode=12;
                $result->errmsg='红包已发完';
                $this->dbhelper->cache_zincrby('limit_zone',$rp_cache_key,1);
                return $result;
            }
        }
        //固定金额
        if($red_packet->amtType==0){
            $amount=$red_packet->amount;
        }
        //随机金额
        else if($red_packet->amtType==1){
            //废除游戏得分决定中奖金额的机制
            // if(isset($scaninfo->score)){
            //     log_message('debug','$scaninfo->score '.var_export($scaninfo->score,true));
            //     if($scaninfo->score->current<=$scaninfo->score->min){
            //         $amount=$red_packet->minAmount;
            //     }
            //     if($scaninfo->score->current>=$scaninfo->score->max){
            //         $amount=$red_packet->maxAmount;
            //     }
            //     if($scaninfo->score->current<$scaninfo->score->max && $scaninfo->score->current>$scaninfo->score->min ){
            //         $per=($scaninfo->score->current-$scaninfo->score->min)/($scaninfo->score->max-$scaninfo->score->min);
            //         $amount=intval($red_packet->minAmount+($red_packet->maxAmount-$red_packet->minAmount)*$per);
            //     }
            //     log_message('debug','$scaninfo->score amount'.var_export($amount,true));
            // }else{
            //     $amount=rand($red_packet->minAmount,$red_packet->maxAmount);
            // }
            $amount=rand($red_packet->minAmount,$red_packet->maxAmount);
        }
        //红包金额是否足够
        if($red_packet->limitType==1){
            //见注释370
            $rp_cache_key="red_packets.remainAmount.id.$red_packet->id";
            $this->dbhelper->cache_zincrby('limit_zone',$rp_cache_key,$amount);
            if((int)$this->dbhelper->cache_zscore('limit_zone',$rp_cache_key)<0){
                $result->errcode=13;
                $result->errmsg='红包已发完';
                $this->dbhelper->cache_zincrby('limit_zone',$rp_cache_key,$amount);
                return $result;
            }
        }
        $merchant=$this->merchant_model->get($activity->mchId);
        $this->send($red_packet,$amount,$activity,$scaninfo);
        $result->errcode=0;
        $result->errmsg='领到红包'.$amount.'分';
        $result->wx_qrcode_url=$this->config->item('base_url').'files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl;
        $result->amount=$amount;
        return $result;
    }
    function try_red_packet_level($red_packet,$activity,$scaninfo,$result){
        $subs=$this->get_subs_by_priority($red_packet->id,$red_packet->priority);
        if(!isset($subs)){
            $result->errcode=1;
            $result->errmsg='没有红包了哦';
            return $result;
        }
        $sub=NULL;
        foreach($subs as $s){
            if($this->is_lucky($s->probability)){
                $result->amount=$s->amount;
                $sub=$s;
                break;
            }
        }
        if(!isset($sub)){
            $result->errcode=20;
            $result->errmsg='运气不够好哦';
            if($red_packet->failureType==0){
                $joke=$this->jokes_model->get_joke();
                $result->alt_title=$joke->title;
                $result->alt_text=$joke->text;
            }
            return $result;
        }

        $merchant=$this->merchant_model->get($activity->mchId);
        $this->send($red_packet,$sub->amount,$activity,$scaninfo,$sub);
        $result->errcode=0;
        $result->errmsg='领到红包'.$sub->amount.'分';
        $result->wx_qrcode_url=$this->config->item('base_url').'files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl;
        return $result;
    }

    function is_lucky($probability){
        $max_num=100000;
        $num=$probability*$max_num;
        $r=rand(1,$max_num);
        return $r<=$num;
    }
}
