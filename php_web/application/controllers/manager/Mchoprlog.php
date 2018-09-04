<?php defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Mchoprlog extends MerchantController {

	public function __construct() {
		parent::__construct ();
		$this->load->model('Admin_model','admin');
		$this->load->library('log_record');
	}
	
    public function lists() {
    	$this->load->view('mchoprlog_lists');
    }
    
    public function data() {
        $start =  $this->input->post('start');
        $draw = $this->input->post('draw');
        $len = $this->input->post('length');
        $obj = $this->input->post('obj');
        $obj = $obj?$this->log_record->$obj:null;
        $opr = $this->input->post('opr');
        $opr = $opr?$this->log_record->$opr:null;
        $datestart = $this->input->post('datestart');
        $datestart = $datestart?strtotime($datestart):null;
        $dateend = $this->input->post('dateend');
        $dateend = $dateend?strtotime($dateend ."+23 hour +59 minute+59 second"):null;
    	$alldata = $this->log_record->getLog($this->getUserIds(), $start, $len, $count, $obj, $opr, $datestart, $dateend);
    	$reldata = [];

        // 此处的$v对应的就是数据库表mch_opr_log中一条记录
    	foreach ($alldata as $k => $v) {
    		/*----------操作的实体-------------*/
    	    $alldata[$k]->id = $v->pid;
    	    $alldata[$k]->username = $v->realName;

            // oprdetail对象，即操作类型
    		$detail = json_decode($v->oprdetail);
    		switch($detail->op){
    		    case $this->log_record->New:
    		        $alldata[$k]->opration = "新建";
    		        break;
    		    case $this->log_record->Add:
    		        if( $v->oprobject == $this->log_record->Batch){ //乐码叫申请
    		            $alldata[$k]->opration = "申请";
    		        }else{
    		            $alldata[$k]->opration = "添加";
    		        }
    		        break;
    		    case $this->log_record->Delete:
    		        $alldata[$k]->opration = "删除";
    		        break;
    		    case $this->log_record->Update:
    		        $alldata[$k]->opration = "修改";
    		        break;
    		    case $this->log_record->Start:
    		        if($v->oprobject==$this->log_record->Activity || $v->oprobject == $this->log_record->App){//活动是，启用，乐码是 ，激活
    		            $alldata[$k]->opration = "启用";
    		        }
    		        else{
    		            $alldata[$k]->opration = "激活";
    		        }
    		        break;
    		    case $this->log_record->Stop:
    		        $alldata[$k]->opration = "停用";
    		        break;
    		    case $this->log_record->Download:
    		        $alldata[$k]->opration = "下载";
    		        break;
    		    case $this->log_record->Lock:
    		        $alldata[$k]->opration = "锁定";
    		        break;
    		    case $this->log_record->Unlock:
    		        $alldata[$k]->opration = "解锁";
    		        break;
    		    case $this->log_record->Login:
    		        $alldata[$k]->opration = "登陆";
    		        break;
    		    case $this->log_record->Repassword:
    		        $alldata[$k]->opration = "找回密码";
    		        break;
    		    case $this->log_record->AddInOrder:
    		        $alldata[$k]->opration = "增加入库单";
    		        break;
    		    case $this->log_record->DeleteInOrder:
    		        $alldata[$k]->opration = "删除入库单";
    		        break;
    		    case $this->log_record->AddOutOrder:
    		        $alldata[$k]->opration = "增加出库单";
    		        break;
    		    case $this->log_record->DeleteOutOrder:
    		        $alldata[$k]->opration = "删除出库单";
    		        break;
    		    case $this->log_record->DownloadOrder:
    		        $alldata[$k]->opration = "码下载";
    		        break;
		        case $this->log_record->DownloadErr:
		            $alldata[$k]->opration = "错误信息下载";
		            break;
		        case $this->log_record->Confirm:
		        	$alldata[$k]->opration = "确认收货";
		        	break;
	        	case $this->log_record->Send:
	        		$alldata[$k]->opration = "发货";
	        		break;
        		case $this->log_record->End:
        			$alldata[$k]->opration = "完成订单";
                    break;
                case $this->log_record->Install:
                    $alldata[$k]->opration = "安装";
        			break;
                case $this->log_record->Config:
                    $alldata[$k]->opration = "配置";
                    break;
                case $this->log_record->Buy:
                    $alldata[$k]->opration = "购买";
                    break;
                default:
                    $alldata[$k]->opration = "未知操作";
                    break;
    		}
    		
            // 分析操作主体和概述
    		switch($v->oprobject){
    			case $this->log_record->Batch:
        			$alldata[$k]->oprobject = "乐码";
        			if($detail->op==$this->log_record->DownloadOrder||$detail->op==$this->log_record->DownloadErr||
        			    $detail->op==$this->log_record->AddInOrder||$detail->op==$this->log_record->DeleteInOrder||
        			    $detail->op==$this->log_record->AddOutOrder||$detail->op==$this->log_record->DeleteOutOrder){ //乐码入库单
        			    $alldata[$k]->detail = "订单单号:".$detail->orderno;
        			}else{
        			    $alldata[$k]->detail = "乐码ID:".$detail->id.", 批号:".$detail->batchNo;
        			}
        			break;
    			case $this->log_record->Activity:
        			$alldata[$k]->oprobject = "活动";
        			$alldata[$k]->detail = "活动: ";
        			if(isset($detail->parentName))
        			{
        			    $alldata[$k]->detail .= $detail->parentName." 的子活动: ";
        			}
        			$alldata[$k]->detail .= $detail->name." 活动ID:".$detail->id;
        			break;
    			case $this->log_record->Card:
        			$alldata[$k]->oprobject = "乐券";
        			if(isset($detail->isGroup) && $detail->isGroup){//券组
        			    $alldata[$k]->detail = "券组ID:".$detail->id.", 券组:".$detail->title;
        			}else{ //乐券
        			    $alldata[$k]->detail = "券组".$detail->grouptitle."的 乐券ID:".$detail->id.", 乐券:".$detail->title;
        			}
        			break;
        		case $this->log_record->Point:
        			$alldata[$k]->oprobject = "积分";
        			$alldata[$k]->detail ="名称:".$detail->name. '' . $detail->info;
        			break;
    			case $this->log_record->Product:
        			$alldata[$k]->oprobject = "产品";
        			$alldata[$k]->detail = "产品ID:".$detail->id.", 品名:".$detail->name;
        			break;
    			case $this->log_record->Category:
        			$alldata[$k]->oprobject = "分类";
        			if($detail->op==$this->log_record->Add){//添加分类
            			if($detail->parentCategoryId==-1){//顶级分类
            			    $alldata[$k]->detail = "类名:".$detail->name;
            			}else{
            			    $alldata[$k]->detail = "分类名称:".$detail->name.", 父类:".$detail->parentCategoryName;
            			}
        			}elseif($detail->op==$this->log_record->Update){//修改分类----此处记录数据，每次只能更改一种(名称或描述)所以数据库内只有一个字段是存在的。
        			    
         			    if($detail->parentCategoryId==-1){//顶级分类
        			        $alldata[$k]->detail ="类名:".$detail->name.(isset($detail->desc)?"的描述":"");
        			    }else{
            			    $alldata[$k]->detail ="分类名称:".$detail->name.(isset($detail->desc)?"的描述":""). ", 父类:".$detail->parentCategoryName;
            			}
        			}elseif($detail->op==$this->log_record->Delete){//删除分类
        			    if($detail->parentCategoryId==-1){//顶级分类
        			        $alldata[$k]->detail = "类名:".$detail->name;
        			    }else{
        			        $alldata[$k]->detail = "分类名称:".$detail->name.", 父类:".$detail->parentCategoryName;
         			    }
        			}else{
        			     $alldata[$k]->detail = "ID:".$v->id.",的日志记录出现问题，请联系爱创开发人员";
        			}
        			break;
    			case $this->log_record->RedPacket:
        			$alldata[$k]->oprobject = "红包";
        			$alldata[$k]->detail = '';
        			if(isset($detail->parentId)){
        			     $alldata[$k]->detail .= "红包: ".$detail->parentName." 的分级";
        			}			
        			    $alldata[$k]->detail .= "红包ID: ".$detail->id.(isset($detail->name)?", 红包名称: ".($detail->name):"");//.",类型: ".($detail->rpType?"裂变红包":"普通红包"):"分级红包");
        			break;
    			case $this->log_record->Setting:
    			    $alldata[$k]->oprobject = "安全";
    			    if($detail->type == 'wechat'){
    			        $alldata[$k]->detail = " 微信菜单: ".$detail->info;
    			    }else if($detail->type == 'scanrate'){
            			    switch ($detail->unit){
            			        case 'i':$t= '分钟';break;
            			        case 'h':$t= '小时';break;
            			        case 'm':$t= '月';break;
            			        case 'y':$t= '年';break;
            			    }
            			$alldata[$k]->detail = " 扫码频率为: 每".$t."最多扫码:". $detail->times."次";
    			    }
    		      	break;
    			case $this->log_record->User:
        			$alldata[$k]->oprobject = "用户";
                    if($detail->op == $this->log_record->Update){  //更新操作分很多种，objInfo ,1,2,3,分别是：消费者微信信息，供应链信息，企业信息
    			        $alldata[$k]->detail = "用户:".$v->realName.','.$detail->info;
    			    }
    			    else{//其他操作
    			        $alldata[$k]->detail ="用户:".((isset($detail->realName)&&$detail->realName != null)?$detail->realName:$detail->phoneNum);//.','.$detail->info;
    			    }
    			    break;
    			case $this->log_record->Mixstrategy:
    			    $alldata[$k]->oprobject = "组合";
    			    $alldata[$k]->detail = "组合策略ID:".$detail->id.", 策略名称:".$detail->name;
    			    break;
    			case $this->log_record->Multistrategy:
    			    $alldata[$k]->oprobject = "叠加";
    			    $alldata[$k]->detail = (isset($detail->id)?("叠加策略ID:".$detail->id.","):"")." 策略名称:". (isset($detail->name) ? $detail->name : '未设置');
    			    break;
                case $this->log_record->Accumstrategy:
                    $alldata[$k]->oprobject = "累计";
                    $str = "累计策略ID：%s, 累计策略名称：%s";
                    $alldata[$k]->detail = sprintf($str, $detail->id, $detail->name);
                    break;
    			case $this->log_record->Admin:
    			    $alldata[$k]->oprobject = "账户";
    			    $alldata[$k]->detail = "用户:".$this->admin->get_admin($detail->id, $this->session->userdata['mchId'])->realName;
    			    break;
    		    case $this->log_record->Wechat:
    		        $alldata[$k]->oprobject = "微信菜单";
    		        $alldata[$k]->detail = $detail->info; //. ",  用户:".(isset($detail->realName)?$detail->realName:($this->admin->get_admin($detail->id, $v->mchid)->realName));
    		        break;
    		    case $this->log_record->Mall:
    		        $alldata[$k]->oprobject = "商城";
    		        $alldata[$k]->detail = isset($detail->info)?$detail->info:"";//$detail->info; 这里需要记录其他信息，在调用AddLog方法的时候，把 $logInfo ['info']赋值即可 
    		        break;
    		    case $this->log_record->Group:
    		         $alldata[$k]->oprobject = "基础设置";
    		         $alldata[$k]->detail = isset($detail->info)?$detail->info:"";//$detail->info; 这里需要记录其他信息，在调用AddLog方法的时候，把 $logInfo ['info']赋值即可
    		         break;
                case $this->log_record->App:
                    $this->load->model('Hls_app_model', 'hls_app');
                    $alldata[$k]->oprobject = "应用";
                    try {
                        $alldata[$k]->detail = "应用:" . json_decode($this->hls_app->getAppInstById($detail->id)->config)->name;
                    } catch (Exception $e) {
                        debug($e);
                        $app = $this->db->where('id', $detail->id)->get('apps')->row();
                        if (! isset($app)) {
                            $app = new stdClass();
                            $app->name = '未知';
                        }
                        $alldata[$k]->detail = "应用:" . $app->name;
                    }
                    break;
                case $this->log_record->Role:
                    $alldata[$k]->oprobject = "角色";
                    $alldata[$k]->detail = "角色名称：". $detail->roleName;
                    break;
                case $this->log_record->Salesman:
                    $alldata[$k]->oprobject = "业务员";
                    $alldata[$k]->detail = "业务员：". (isset($detail->realName) ? $detail->realName: '未知业务员');
                    break;
    		}		
    		$alldata[$k]->oprtime =  date ( 'Y-m-d H:i:s', $v->oprtime );
    		array_push ( $reldata, $alldata[$k] );
    	}
    	$data = [
    	    "data" => $reldata,
    	    "recordsTotal" => $count,
    	    "recordsFiltered"=>$count,
    	    "draw"=> intval($draw)
    	];
    	$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    /**
     * 获取所有的管理员
     *
     * @return str 管理员
     */
    public function getUserIds(){
        $idstr=$this->session->userdata['userId'];
        $mchId = $this->session->userdata['mchId'];
        $role = $this->session->userdata['role'];
        $allmchs = [];
        if($role=='0'){//如果是超级管理员，则取出所有的用户ID
        	$allmchs = $this->admin->list_admin($mchId,true);
		}       
        foreach ( $allmchs as $k => $v ) {
            $idstr .=  ",".$v->id;
        }

        return $idstr;
    }
    
    /**
     * 获得所以可操作实体
     */
    public function getobject(){
        $oprobj=[
            "Category" 	 	=> "分类",
            "Product"  	 	=> "产品",
            "Batch" 	 	=> "乐码",
            "RedPacket"  	=> "红包",
            "Card" 	 	 	=> "乐券",
            "Point" 	 	=> "积分",
            "Mixstrategy"	=> "组合",
            "Accumstrategy" => "累计",
        	"Multistrategy" => "叠加",
            "Activity" 	 	=> "活动",
            "Admin" 	 	=> "账户",
            "Wechat" 	 	=> "微信菜单",
            "Setting" 	 	=> "安全",
            "User" 		 	=> "用户",
            "Mall" 		 	=> "商城",
            "App" 		 	=> "应用",
        	"Group"         => "基础设置",
            "Role"          => "角色",
        ];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $oprobj ) );
    }

    /**
     * 获得所有操作类型
     */
    public function getOpration(){
    	$oprobj = $this->input->post('obj');
    	switch($oprobj){
    		case "Batch":
    			$data=[	"Add" =>"申请",	"Update" =>"修改","Delete" =>"删除","Download" =>"下载","Start" =>"激活","Stop" =>"停用","AddInOrder"=>"增入库单","DeleteInOrder"=>"删入库单","AddOutOrder"=>"增出库单","DeleteOutOrder"=>"删出库单","DownloadOrder"=>"码下载","DownloadErr"=>"错误信息下载"];
    			break;
    		case "Activity":
                $data=["New" =>"新建","Add" =>"添加","Update" =>"修改","Delete" =>"删除","Start" =>"启用","Stop" =>"停用"];
                break;
    		case "Card":
                $data=["New"=> "新建","Add" =>"添加","Update" =>"修改","Delete" =>"删除"];
                break;
            case "Point":
               	$data=["New"=> "新建","Add" =>"添加","Update" =>"修改","Delete" =>"删除"]; 
               	break;
    		case "Product":
                $data=["Add" =>"添加","Update" =>"修改","Delete" =>"删除"];
                break;
    		case "Category":
    	        $data=["Add" =>"添加","Update" =>"修改","Delete" =>"删除"];
                break;
    		case "RedPacket":
                $data=["New" =>"新建", "Add" =>"添加","Update" =>"修改","Delete" =>"删除"];
    		    break;
    	    case "Setting":
        		$data=[	"Update" =>"修改"];
        		break;
    		case "User":
        		$data=["Update" =>"修改","Repassword" =>"找回密码","Login" =>"登陆"];
        		break;
    		case "Mixstrategy":
    		    $data=["Add" =>"添加","Update" =>"修改","Delete" =>"删除"];
    		    break;
    		case "Multistrategy":
    		    $data=["New" =>"新建","Update" =>"修改","Delete" =>"删除"];
    		    break;
    		case "Admin":
    		    $data=["New" =>"新建","Update" =>"修改","Delete" =>"删除","Lock"=>"锁定","Unlock"=>"解锁"];
    		    break;
    	    case "Wechat":
    	        $data=["Update" =>"修改"];
    	        break;
            case "Mall":
                $data=["New" =>"新建","Update"=>"修改","Delete" =>"删除","Send"=>"发货", "Confirm"=>"确认收货","End"=>"完成订单" ];
                break;
            case "App":
                $data=['Start' => '启用', 'Stop' => '停用', 'Config' => '配置', 'Delete' => '删除', "Install" => '安装', 'Buy' => '购买'];
                break;
            case "Group":
                $data=["Update"=>"修改"];
                break;
            case "Role":
                $data = ["Add" => '新建', "Update" => "修改", 'Delete' => '删除'];
                break;
            default:
        		$data = [];
        		break;
    	}
    	$this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    /**
     * 生产出/入库单
     */
    public function batchorderlog($type){
        try{
            $orderno = $this->input->post('orderno');
            $logInfo['orderno'] = $orderno;
            if($type=='in'){
                $logInfo['op'] = $this->log_record->AddInOrder;
            }
            else{
                $logInfo['op'] = $this->log_record->AddOutOrder;
            }
            $this->log_record->addLog($this->session->userdata['mchId'],$logInfo,$this->log_record->Batch);
        }catch(Exception $e){
            log_message('error','mch_log_error:'.$e->getMessage());
        }
    }
}