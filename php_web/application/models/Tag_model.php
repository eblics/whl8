<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tag_model extends CI_Model {
    public function __construct(){
        // Call the CI_Model constructor
        parent::__construct();
    }

    /**
     * 从数据库获取单个标签数据
     */
    public function get($id){
        return $this->db->query('select * from users_tags where id=?',[intval($id)])->row();
    }

    /**
     * 获取用户标签列表数据
     */
    public function get_list($mchId,&$count,$start=0,$length=1000){
        $this->sync_wx_to_db($mchId);
        $count=$this->db->query('select count(*) cnt from users_tags where mchId=?',[intval($mchId)])->row()->cnt;
        return $this->db->query('select * from users_tags where mchId=? limit ?,?',[intval($mchId),intval($start),intval($length)])->result();
    }

    /**
     * 用户标签列表数据同步
     */
    private function sync_wx_to_db($mchId) {
        debug('tag-model-sync-wx-to-db - begin');
        debug('tag-model-sync-wx-to-db - params: '. json_encode(func_get_args()));
        $list = $this->weixin_rest_api->get_tags($mchId);
        $tags = $list->tags;
        $dbList = $this->db->query("select * from users_tags where mchId = ?", [$mchId])->result();
        foreach ($tags as $k => $v) {
            $thisHave=false;
            foreach ($dbList as $k2 => $v2) {
                if($v->id==$v2->tagId){
                    $this->db->query("update users_tags set name='$v->name',count=$v->count where mchId=$mchId and tagId=$v->id");
                    $thisHave=true;
                }
            }
            if(! $thisHave){
                $this->db->query("insert ignore into users_tags(mchId,tagId,name,count) values($mchId,$v->id,'$v->name',$v->count)");
            }
        }
        $dbList=$this->db->query("select * from users_tags where mchId=$mchId")->result();
        foreach ($dbList as $k => $v) {
            $thisHave=false;
            foreach ($tags as $k2 => $v2) {
                if($v2->id==$v->tagId){
                    $thisHave=true;
                }
            }
            if(! $thisHave){
                $this->db->query("delete from users_tags where mchId=$mchId and tagId=$v->tagId");
            }
        }
    }

    /**
     * 添加用户标签
     */
    public function add($mchId,$name){
        $data=$this->weixin_rest_api->add_tags($mchId,$name);
        log_message('debug',var_export($data,true));
        if(isset($data->tag)&&$data->tag->id>=0){
            $tag=$data->tag;
        }else{
            return false;
        }
        return $this->db->query("insert into users_tags(mchId,tagId,name,count) values($mchId,$tag->id,'$tag->name',0)");
    }

    /**
     * 修改用户标签
     */
    public function update($mchId,$tagId,$name){
        $data=$this->weixin_rest_api->update_tags($mchId,$tagId,$name);
        log_message('debug',var_export($data,true));
        if(isset($data->errcode)&&$data->errcode==0){
            return $this->db->query("update users_tags set name='$name' where mchId=$mchId and tagId=$tagId");
        }else if(isset($data->errcode)&&$data->errcode!=0){
            return $data;
        }else{
            return false;
        }
    }

    /**
     * 删除用户标签
     */
    public function delete($mchId,$tagId){
        $data=$this->weixin_rest_api->delete_tags($mchId,$tagId);
        log_message('debug',var_export($data,true));
        if(isset($data->errcode)&&$data->errcode==0){
            return $this->db->query("delete from users_tags where mchId=$mchId and tagId=$tagId");
        }else{
            return false;
        }
        
    }

    /**
     * 给用户打标签
     */
    public function update_user_tag($mchId,$tagId,$openid){
        $time=time();
        return $this->db->query("insert into users_tags_update(mchId,tagId,openid,status,createTime,updateTime) values($mchId,$tagId,'$openid',0,$time,$time)");
    }
    

}
