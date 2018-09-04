<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OMerchant_model extends CI_Model {

    function get_mch($mch_id) {
        return $this->db->where('id', $mch_id)->get('merchants')->row();
    }
    //查询企业id对应的登录帐号的手机号码
    function get_account_num($mch_id) {
        $sql = "select a.phoneNum from merchants m LEFT JOIN mch_accounts a on a.mchId = m.id where a.role=0 and a.mchId = $mch_id";
        $res = $this->db->query($sql)->row();
        return $res;
    }
    function get_mch_data($status){
        $sql = "select m.id mid,m.name mname,a.phoneNum aphone,m.contact mcontact,m.payAccountType,m.phoneNum mphone,FROM_UNIXTIME(m.createTime,'%Y-%m-%d %H:%i:%s') mdate,date(FROM_UNIXTIME(m.checkTime)) mcdate,m.status mstatus ,t.amount mamount
            from merchants m
            LEFT JOIN mch_accounts a ON a.mchId = m.id
            left join mch_balances t on t.mchId = m.id
            where m.status =? and a.role=0 ORDER BY m.createTime desc";
        $merchants = $this->db->query($sql,[$status])->result();
        foreach ($merchants as &$merchant) {
            $merchant->url = config_item('mch_url');
        }
        return $merchants;
    }

    function get_allmch_data(){
        $sql = "select m.id mid,m.name mname,a.phoneNum aphone,m.contact mcontact,m.payAccountType,m.phoneNum mphone,FROM_UNIXTIME(m.createTime,'%Y-%m-%d %H:%i:%s') mdate,date(FROM_UNIXTIME(m.checkTime)) mcdate,m.status mstatus,t.amount mamount
            from merchants m
            LEFT JOIN mch_accounts a ON a.mchId = m.id
            left join mch_balances t on t.mchId = m.id
            where a.role=0 and m.name is not null ORDER BY m.createTime desc";
        $merchants = $this->db->query($sql)->result();
        foreach ($merchants as &$merchant) {
            if (! isset($merchant->mname)) {
                unset($merchant);
            }
            $merchant->url = config_item('mch_url');
        }
        return $merchants;
    }


    function passwd($mch_id){
        info("==================== Reset password start ======================");
        debug("mch_id is $mch_id");
        $salt = mt_rand(100000, 999999);
        $pass = '123456';
        $password = md5(md5($pass . $salt) . $salt);
        $update = [
            'salt'=>$salt,
            'password'=>$password
        ];
        $result = $this->db->where('mchId', $mch_id)
            ->update('mch_accounts', $update);
        if (! $result) {
            error("Review faild: password reset error");
            throw new Exception("重置密码失败", 52005);
        }
        info("==================== Reset password end ========================");
    }

    function add($mch_account,$merchant) {
        info("==================== Add a new mch_account start ====================");
        $mobile_exists = $this->db->where('phoneNum', $mch_account['phoneNum'])
            ->get('mch_accounts')->row();
        if ($mobile_exists) {
            error("Add faild: mobile has already exists");
            throw new Exception("该手机号已存在", 51005);
        }
        if(!$merchant) {
            error("$merchant-name faild: name is null");
            throw new Exception("企业名称为空", 51005);
        }
        $mch = ['createTime' => time(), 'status' => MerchantStatusEnum::Create, 'name' => $merchant, 'is_formal' => 0];
        $result = $this->db->insert('merchants', $mch);
        if (! $result) {
            error("Add faild: insert into merchants error");
            throw new Exception("企业添加失败", 51005);
        }
        $mch_id = $this->db->insert_id();
        $salt = mt_rand(100000, 999999);
        $password = md5(md5($mch_account['phoneNum'] . $salt) . $salt);
        $mch_account['password'] = $password;
        $mch_account['salt'] = $salt;
        $mch_account['mchId'] = $mch_id;
        $mch_account['role'] = MerchantAccountEnum::Admin;
        $add_result = $this->db->insert('mch_accounts', $mch_account);
        if (! $add_result) {
            error("Add faild: insert into mch_account error");
            throw new Exception("账户添加失败", 51010);
        }
        debug("Merchent info is: " . json_encode($mch_account));
        info("==================== Add a new mch_account end ======================");
        return $this->db->insert_id();
    }

    function review($mch_id, $preview) {
        $str = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $array = array();
        $count = sizeof($str);
        for($i=0;$i<$count;$i++){
            $f = $str[$i];
            for($k=0;$k<$count;$k++){
                $s = $str[$k];
                $string = $f.$s;
                array_push($array,$string);
            }
        }
        //这里读取数据库信息
        $res_version = $this->merchant->get_cversion();
        $res_array = array();
        if($res_version){
            foreach ($res_version as $value) {
                array_push($res_array, $value->code);
            }
        }
        $array_diff = array_diff($array, $res_array);
        $code = reset($array_diff);
        $update = ['status' => MerchantStatusEnum::PreReview,'code'=>$code,'codeVersion'=>$preview];
        info("==================== Review a merchant start ======================");
        debug("mch_id is $mch_id");
        $merchant = $this->db->where('id', $mch_id)->get('merchants')->row();
        if (! $merchant) {
            error("Review faild: merchant not found");
            throw new Exception("企业不存在", 52005);
        }
        if ($merchant->status == MerchantStatusEnum::Reviewed) {
            error("Review faild: already reviewed");
            throw new Exception("企业已审核，无需执行此操作", 52005);
        }
        if ($merchant->status == MerchantStatusEnum::Freezed) {
            error("Review faild: merchant freezed");
            throw new Exception("企业已冻结，请先激活企业账户", 52005);
        }
        $result = $this->db->where('id', $mch_id)->update('merchants', $update);
        if (! $result) {
            error("Review faild: review merchant error");
            throw new Exception("审核失败", 52005);
        }
        info("==================== Review a merchant end ========================");
        return $result;
    }

    function freeze($mch_id) {
        info("==================== Freeze a merchant start ======================");
        debug("mch_id is $mch_id");
        $merchant = $this->db->where('id', $mch_id)->get('merchants')->row();
        if ($merchant->status != MerchantStatusEnum::Reviewed) {
            error("Review faild: merchant not reviewed");
            throw new Exception("企业未审核通过，不能执行冻结操作", 52005);
        }
        $update = ['status' => MerchantStatusEnum::Freezed];
        $result = $this->db->where('id', $mch_id)->update('merchants', $update);
        if (! $result) {
            error("Review faild: freeze merchant error");
            throw new Exception("冻结企业账户失败", 52005);
        }
        info("==================== Freeze a merchant end ========================");
    }

    function active($mch_id) {
        info("==================== Active a merchant start ======================");
        debug("mch_id is $mch_id");
        $merchant = $this->db->where('id', $mch_id)->get('merchants')->row();
        $update = ['status' => MerchantStatusEnum::Reviewed];
        $result = $this->db->where('id', $mch_id)->update('merchants', $update);
        if (! $result) {
            error("Review faild: active merchant error");
            throw new Exception("激活失败", 52005);
        }
        info("==================== Active a merchant end ========================");
    }
    function reset($mch_id,$ishop) {
        if($ishop == 1){
            $data = ['wxAuthStatus'=> 0];
        }
        if($ishop == 0){
            $data = ['wxAuthStatus_shop'=> 0];
        }
        return $result = $this->db->where('id',$mch_id)->update('merchants',$data);
    }

    /**
     * 审核企业
     * reviewed by shizq at 2016-10-13
     *
     * @param   $mch_id 企业编号
     * @param   $data
     * {
     *     'checkReason': '审核说明',
     *     'checkTime': '审核时间',
     *     'status': '状态',
     *     'codeVersion': '码版本(可选)',
     *     'code': '码(可选)',
     * }
     * @return  json
     */
    function check_account($mch_id, $data) {
        if (! is_array($data)) {
            return false;
        }
        info("====================  check_account start  ======================");
        debug("mch_id is $mch_id");
        $merchant = $this->db->where('id', $mch_id)->get('merchants')->row();
        if (! $merchant) {
            error("Reviewed or Refused faild: merchant not found");
            throw new Exception("企业不存在", 52005);
        }
        if ($data['status'] == $merchant->status && $merchant->status == MerchantStatusEnum::Reviewed && $merchant->codeVersion == $data['codeVersion']) {
            error("Reviewed faild: merchant already Reviewed");
            throw new Exception("企业已审核，无需执行此操作", 52005);
        }
        if ($data['status'] == $merchant->status && $merchant->status == MerchantStatusEnum::Refused) {
            error("Refused faild: merchant already Refused");
            throw new Exception("企业已拒绝，无需执行此操作", 52005);

        }
        if ($merchant->status == MerchantStatusEnum::Freezed) {
            error("Review faild: merchant freezed");
            throw new Exception("企业已冻结，请先激活企业账户", 52005);
        }
        $result = $this->db->where('id', $mch_id)->update('merchants', $data);
        if (! $result) {
            error("Review faild: review merchant error");
            throw new Exception("审核失败", 52005);
        }
        info("====================  check_account end  ========================");
        return $result;
    }
    //按要求查找对应的企业
    function get_nums($type) {
        info("====================  get_nums start  ======================");
        if(!$type){
            error("$type .Parameter is empty");
            throw new Exception("参数为空", 52005);
        }
        if($type == 'all'){
            $sql = "select a.phoneNum aphone from merchants m LEFT JOIN mch_accounts a ON a.mchId = m.id where a.role=0";
        }else{
            $sql = "select a.phoneNum aphone from merchants m LEFT JOIN mch_accounts a ON a.mchId = m.id where a.role=0 and m.status = '$type'";
        }

        $merchants = $this->db->query($sql)->result();
        if(!$merchants){
            error("$type .Parameter is empty");
            throw new Exception("参数为空", 52005);
        }
        $arr = array();
        foreach ($merchants as $k => $val) {
            array_push($arr,$val->aphone);
            $a = join(",",$arr);
        }
        return $a;
        info("====================   get_nums end   ======================");

    }
    //查询码信息
    function get_codes() {
        info("====================  get_codes start  ======================");
        $sql = "select versionNum from code_version";
        $result = $this->db->query($sql)->result();
        if(!$result){
            error("result is empty");
            throw new Exception("查询结果为空", 52005);
        }
        return $result;
        info("====================   get_codes end   ======================");

    }
    //查询codeVersion
    function get_cversion() {
        info("====================  get_cversion start  ======================");
        $sql = "select code from merchants where code is not NULL";
        $res_version = $this->db->query($sql)->result();
        if(!$res_version){
            error("res_version is empty");
            throw new Exception("查询结果为空", 52005);
        }
        return $res_version;
        info("====================   get_cversion end   ======================");
    }
    //查询所有被禁用户
    function get_allusers_data(){
        $sql = "select u.id uid,u.openid uopenid,u.subscribe usub,u.nickName uname,u.commonStatus ustatus from users_common u LEFT JOIN users_common_log l ON u.id = l.userId where u.commonStatus=1 and l.logType=1";
        $users = $this->db->query($sql)->result();
        return $users;
    }
    //申请解禁列表
    function get_unlock_list($search,&$count=0,$start=null,$length=null){
        $start = intval($start);
        $length = intval($length);
        $count_sql = "select u.id uid 
        from service_appeal s
        left JOIN users_common u ON u.openid=s.openId
        left JOIN users_common_log g ON g.userId=u.id and g.logType=1 and g.id=(select max(id) from users_common_log where userId=g.userId and logType=1)
        where s.status=1 and u.commonStatus=1 ";
        if(isset($search)&&$search!==''&&$search!==NULL){
            $count_sql .= " and concat(s.openId,u.nickName) like '%".$search."%'";
        }
        $count_sql .= ' order by s.id desc';
        $count=$this->db->query($count_sql)->num_rows();
        $sql = "select u.id uid,s.id sid,s.QRimg simg,s.openId sopenid,g.logDesc logDesc,FROM_UNIXTIME(g.createTime,'%Y-%m-%d %H:%i:%s') logTime,FROM_UNIXTIME(s.createTime,'%Y-%m-%d %H:%i:%s') stime,s.reason sreason,u.commonStatus ustatus,u.nickName uname 
        from service_appeal s 
        left JOIN users_common u ON u.openid=s.openId 
        left JOIN users_common_log g ON g.userId=u.id and g.logType=1 and g.id=(select max(id) from users_common_log where userId=g.userId and logType=1) 
        where s.status=1 and u.commonStatus=1 ";
        if(isset($search)&&$search!==''&&$search!==NULL){
            $sql .= " and concat(s.openId,u.nickName) like '%".$search."%'";
        }
        $sql .= ' order by s.id desc';
        if(isset($start)&& isset($length)){
            $sql.=" limit $start,$length";
        }
        $users = $this->db->query($sql)->result();
        return $users;
    }
    function get_unlock_list_count(){
        $sql = "select count(1) count from service_appeal s
        left JOIN users_common u ON u.openid=s.openId
        left JOIN users_common_log g ON g.userId=u.id and g.logType=1 and g.id=(select max(id) from users_common_log where userId=g.userId)
        where s.status=1 and u.commonStatus=1";
        // $count=$this->db->query($sql_count,$data)->row()->count;
        return $this->db->query($sql)->row();
    }
    public function get_unlock_list_page($start,$length) {
        return $this->db->query("select * from groups where mchId=$mchId and rowStatus=0 order by id desc limit $start,$length")->result();
    }
    //解禁操作
    function operation($id){
        //该id为users_common的id
        $this->db->trans_start();
        $res = $this->db->where('id', $id)->update('users_common',['commonStatus'=>0]);
        if($this->db->affected_rows()==0){
            return;
        }
        $result = $this->db->where('id',$id)->get('users_common')->row();
        if(!isset($result)){
            return;
        }
        $openid = $result->openid;
        $this->db->where('openId', $openid)->where('status',1)->update('service_appeal',['status'=>2]);
        $r = $this->db->affected_rows();
        $this->db->trans_complete();
        if($r>0){
            return true;
        }else{
            return false;
        }

    }
    //封禁操作
    function operation_refuse($id,$val){
        // $res = $this->db->where('id', $id)->update('users_common',['commonStatus'=>0]);
        $result = $this->db->where('id',$id)->get('users_common')->row();
        $openid = $result->openid;
        if($result){
            $r = $this->db->where('openId', $openid)->where('status',1)->update('service_appeal',['status'=>3,'refuseTime'=>time(),'refuse'=>$val]);
            if($r){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //拉黑操作
    function pull_into_blacklist($id,$val,$openid,$mark){
        $data = array(
            'openid' => $openid,
            'userId' => $id,
            'createTime' => time(),
            'remark' =>$mark
        );
        $this->db->trans_start();
        $this->db->where('openId', $openid)->where('status',1)->update('service_appeal',['status'=>3,'refuseTime'=>time(),'refuse'=>$val]);
        $res = $this->db->where('openId',$openid)->get('users_common_blacklist')->row();
        $r = $this->db->insert('users_common_blacklist',$data);
        if(!isset($r)){
            return $r;
        }
        $this->db->trans_complete();
        if(isset($res)){
            return 1;
        }else{
            return $r;
        }
    }
    //备注
    function mark($id,$val){
        $r = $this->db->where('id', $id)->where('status',1)->update('service_appeal',['mark'=>$val]);
        if($r){
            return true;
        }else{
            return false;
        }
    }
    //获取备注
    function get_mark($id){
        $r = $this->db->where('id',$id)->get('service_appeal')->row();
        return $r;
    }
    //根据参数查找用户
    function get_result($type,$vv){
        if($type == 1){
            $sql2 = "u.openid = '$vv'";
        }
        if($type == 2){
            $sql2 = "u.id = $vv";
        }
        if($type == 3){
            $sql2 = "u.nickName like '%$vv%'";
        }
        $sql = "select u.id uid,u.nickName unickname,sa.phoneNum,u.openid uopenid,u.headimgurl uimg,u.subscribe ustatus,uc.commonStatus ucstatus from users u left join users_common_sub us on us.userId=u.id left join users_common uc on us.parentId=uc.id LEFT JOIN service_appeal sa on sa.openId=uc.openid and sa.`status`=1 where uc.commonStatus=1 ".$sql2;
        return $this->db->query($sql)->result_array();
        // $this->db->query($sql)->result();
        // return $this->db->last_query();

    }
    //用户解禁/封禁
    function operation_users($id,$lock){
        $sql = "select uc.commonStatus ustatus from users u LEFT JOIN users_common_sub us on us.userId=u.id left JOIN users_common uc on us.parentId=uc.id where u.id=$id";
        $res = $this->db->query($sql)->row();
        $sql1 = "select parentId from users_common_sub where userId=$id";
        if($lock == 'lock'){
            if(isset($res)&&$res->ustatus == 1){
                return 'already locked';
            }
            if(isset($res)&&$res->ustatus == 0){
                // $this->db->trans_start();
                $r1 = $this->db->query($sql1)->row();
                $parentId = $r1->parentId;
                $r = $this->db->where('id',$parentId)->update('users_common',['commonStatus'=>1]);
                // $this->db->trans_complete();\
                if($r){
                    return 'lock success';
                }else{
                    return 'lock fail';
                }
            }
        }
        if($lock == 'unlock'){
            if(isset($res)&&$res->ustatus == 0){
                return 'exists1';
            }
            if(!isset($res)){
                return 'exists2';
            }
            if(isset($res)&&$res->ustatus == 1){
                $r1 = $this->db->query($sql1)->row();
                $parentId = $r1->parentId;
                $r = $this->db->where('id',$parentId)->update('users_common',['commonStatus'=>0]);
                if($r){
                    return 'unlock success';
                }else{
                    return 'unlock fail';
                }
            }

        }
    }
    //用户管理查找用户
    public function search_user($vk,$vv){
        // if($vk == 1 || $vk == 2){
        //     if($vk == 1){
        //         $sqls = "u.openid like '%".$vv."%'"; 
        //     }
        //     if($vk == 2){
        //         $sqls = "u.nickName like '%".$vv."%'";
        //     }
        //     $sql = "select * from users u join users_common_sub us on u.openid=us.openid where ";
        //     $result = $this->db->query($sql)->result();
        //     if(count($result)){
        //         $sql = "select u.id id,u.openid openid,u.nickName nickname,u.headimgurl headimgurl,us.status status from users u left join users_common_sub us on u.openid=us.openid where "
        //         $this->db->query();
        //     }else{

        //     }
        // }
        if($vk == 1 || $vk == 2){
            if($vk == 1){
                $sqls = "u.openid like '%".$vv."%'"; 
            }
            if($vk == 2){
                $sqls = "u.nickName like '%".$vv."%'";
            }
            $sql = "select u.id id,u.openid openid,u.nickName nickname,u.headimgurl headimgurl,us.status status,m.name name from users u left join users_common_sub us on u.openid=us.openid left join merchants m on u.mchId=m.id where ";
        }
        if($vk == 3 || $vk == 4){
            if($vk == 3){
                $sqls = "openid like '%".$vv."%'";
            }
            if($vk == 4){
                $sqls = "nickName like '%".$vv."%'";
            }
            $sql = "select id,openid,nickName nickname,headimgurl,commonStatus status,'huanlesaopf' as name from users_common where ";
        }
        $sql = $sql.$sqls;
        return $this->db->query($sql)->result_array();
        // $sql = "select id,openid,nickName,headimgurl from ".$table."where";

    }
    //用户恢复正常
    public function move_out_blacklists($id,$status){
        // if($status == 0){
        //     $result = $this->db->where('userId',$id)->get('users_common_sub')->row();
        //     if(isset($result)){
        //         $res = $this->db->where('userId',$id)->update('users_common_sub',['status'=>1]);
        //         if($res){
        //             return true;
        //         }else{
        //             return false;
        //         }
        //     }else{
        //         $r = $this->db->where('id',$id)->get('users')->row();
        //         if(isset($r)){
        //             $this->db->insert('users_common_sub',['']);
        //         }else{
        //             return false;
        //         }
        //     }
        // }else

        if($status == 1){
            $res = $this->db->where('userId',$id)->update('users_common_sub',['status'=>0]);
            if($res){
                return true;
            }else{
                return false;
            }
        }
        
    }
    //黑名单用户生成列表
    public function get_blacklist_data(){
        $sql = "select u.id id,u.userId uid,u.openid uopenid,uc.headimgurl uimg,FROM_UNIXTIME(u.createTime,'%Y-%m-%d %H:%i:%s') utime,u.remark umark from users_common_blacklist u left join users_common uc on uc.openid=u.openid order by u.createTime desc";
        return $this->db->query($sql)->result_array();
    }
    //移除黑名单
    public function move_out_black($id,$openid){
        $this->db->trans_begin();
        $this->db->where('id',$id)->delete('users_common_blacklist');
        $this->db->where('openid',$openid)->update('users_common',['commonStatus'=>0]);
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
        
    }
    /********************解封查询 add by cw 2017-01-13***************************/
    public function get_userscan_info($userId){
        $sql="select a.mchId,a.id userId,a.nickName,a.headimgurl,a.openid,a.province,a.city,a.country,a.commonStatus,c.* from users_common a
left join users_common_sub b on a.id=b.parentId
left join users_common_log c on c.userId=a.id
where a.id=? and c.logType=1 order by c.createTime desc";

        $result=$this->db->query($sql,[$userId])->row_array();
        // 查询出用户userId
        $userId_arr=$this->db->query("select userId from users_common_sub where parentId=?;",[$userId])->result_array();
        if(!empty($userId_arr)){
            $userId_string=array();
            for($i=0;$i<count($userId_arr);$i++){
                array_push($userId_string,$userId_arr[$i]['userId']);
            }
            $result['scanList']=$this->db->query("select a.id scanId,a.mchId,a.userId,a.openid,FROM_UNIXTIME(a.scanTime) scanTime,a.code,b.batchNo from scan_log a
    left join batchs b on a.batchId=b.id where a.userId in ? order by a.scanTime desc",[$userId_string])->result_array();
        }else{
            $result['scanList']=[];
        }

        return $result;
    }
    /********************解封查询 add by cw 2017-01-13***************************/

    //修改支付帐号
    function edit_payaccounttype($mchId,$payType){
        return $this->db->query("update merchants set payAccountType=$payType where id=$mchId");
    }
}
