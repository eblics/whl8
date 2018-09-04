<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sub_activity_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('activity_model','activity');
    }

    public function get($id){
        return $this->db->query("SELECT s.*,
                a.id mainId,a.mchId as mchId,a.name mainName,a.description mainDesc,a.imgUrl mainImgUrl,a.state mainState,
                ar.code areaCode,ar.name areaName,ar.fullName areaFullName FROM sub_activities s
            INNER JOIN activities a ON s.parentId=a.id
            LEFT JOIN areas ar ON ar.code=s.areaCode>0
            WHERE s.id=?", [$id])->row();
    }

    public function get_best_match($scaninfo,$isWaiter){
        $time=time();//当前时间
        $areaCode=$scaninfo->areaCode;//区
        $areaCodeCity=substr($areaCode,0,4).'00';//市
        $areaCodeProv=substr($areaCode,0,2).'0000';//省
        $role=$isWaiter?1:0;
        //上报地理位置判断
        $geoNeededSql='';
        if(empty($areaCode)){
            $geoNeededSql='and (s.geoNeeded=0 or s.geoNeeded is null)';
        }
        //码关联的产品判断
        $codeWidhProduct=false;
        $codeWidhCategory=false;
        $catearr=[];
        $batchInfo=$this->db->query("select * from batchs where id=$scaninfo->batchId")->row();
        if($batchInfo){
            if(!empty($batchInfo->productId) && $batchInfo->productId!=0){
                $codeWidhProduct=true;
            }
            if(!empty($batchInfo->categoryId) && $batchInfo->categoryId!=0){
                $codeWidhCategory=true;
                //遍历码关联的产品父分类
                $curCategory=$this->db->where('id',$batchInfo->categoryId)->get('categories')->row();
                $categories=$this->db->where('mchId',$batchInfo->mchId)->get('categories')->result();
                function getTree($arrCat, $parentId) {
                    static  $arrTree = []; //使用static代替global
                    if(empty($arrCat)) return false;
                    array_push($arrTree,$parentId);
                    foreach($arrCat as $k => $v){
                        if($v->id==$parentId){
                            $parentId=$v->parentCategoryId;
                            continue;
                        }
                    }
                    if($parentId!=-1) {
                        getTree($arrCat,$parentId);
                    }
                    return $arrTree;
                }
                if($curCategory->parentCategoryId!=-1){
                    $catearr=getTree($categories,$curCategory->parentCategoryId);
                }
            }
        }
        $withProdSQL='';
        $cateSql='';
        if(count($catearr)>0){
            foreach($catearr as $v){
                $cateSql.="or (s.productId is NULL and s.categoryId=$v) ";
            }
        }
        $evilSql = "";
        if(!empty($scaninfo->evilLevel) && $scaninfo->evilLevel > 0){
            $tmp = 0;
            switch (intval($scaninfo->evilLevel)) {
                case 1:
                    $tmp = 1;break;
                case 2:
                    $tmp = 2;break;
                case 3:
                    $tmp = 4;break;
                case 4:
                    $tmp = 8;break;
                default:
                     break;
            }
            $evilSql = " AND (s.forEvil is NULL OR s.forEvil ='' OR (s.forEvil & $tmp) > 0 ) ";
            //$evilSql = " AND (s.forEvil is NULL OR s.forEvil ='' OR FIND_IN_SET('$scaninfo->evilLevel',s.forEvil )) ";
        }else{
            $evilSql = " AND (s.forEvil is NULL  OR s.forEvil ='') ";
        }
        if(!$codeWidhProduct && !$codeWidhCategory){//码批次未关联产品分类和产品
            $withProdSQL="or (b.categoryId=s.categoryId and b.productId=s.productId) ";
        }else if($codeWidhProduct && $codeWidhCategory){//码批次已关联产品分类和产品
            $withProdSQL="or (b.categoryId=s.categoryId and b.productId=s.productId) or (s.productId is NULL and b.categoryId=s.categoryId) ".$cateSql;
        }else if(!$codeWidhProduct && $codeWidhCategory){//码批次已关联产品分类，未关联产品
            $withProdSQL="or (s.productId is NULL and b.categoryId=s.categoryId) ".$cateSql;
        }
        //获取时间、区域符合的活动，按照区域最小、开始时间最近、结束时间最近的原则
        $sql="SELECT s.*,bit_count(s.binding) bc,
             a.id mainId,a.mchId as mchId,a.name mainName,a.description mainDesc,a.imgUrl mainImgUrl,a.state mainState 
            FROM sub_activities s 
            INNER JOIN activities a ON s.parentId=a.id 
            INNER JOIN batchs b ON b.id=$scaninfo->batchId 
            where a.mchId=$scaninfo->mchId and a.startTime<=$time and a.endTime>=$time and s.role=$role $geoNeededSql $evilSql
            and a.rowStatus=0 and s.rowStatus=0 and s.state=1 and a.state=1 
            AND (!(s.binding&1) or (s.startTime<=$time and s.endTime>=$time)) 
            AND (!(s.binding&2) or (s.areaCode='$areaCode' or s.areaCode='$areaCodeCity' or s.areaCode='$areaCodeProv')) 
            AND (!(s.binding&4) or (s.batchId=$scaninfo->batchId)) 
            AND (!(s.binding&8) or  exists (select toc.id from tts_orders_codes toc inner join tts_orders tos on tos.id=toc.orderId where toc.orderId=s.prodInOrderId and tos.orderType='produce' and toc.code='$scaninfo->code')) 
            AND (!(s.binding&16) or exists (select toc.id from tts_orders_codes toc inner join tts_orders tos on tos.id=toc.orderId where toc.orderId=s.outOrderId and tos.orderType='out' and toc.code='$scaninfo->code')) 
            AND (!(s.binding&32) or exists (select toc.id from tts_orders_codes toc inner join tts_orders tos on tos.id=toc.orderId where toc.orderId=s.outOrderId and tos.orderType='out' and toc.code='$scaninfo->code' and ( s.saletoagc=tos.saletoAGC or s.saletoagc=CONCAT(left(tos.saletoAGC,2),'0000') or s.saletoagc=CONCAT(left(tos.saletoAGC,4),'00') ))) 
            AND (!(s.binding&128) $withProdSQL) 
            ORDER BY s.forEvil desc,s.areaCode desc,bc desc,binding asc,s.endTime,s.startTime desc,s.categoryId desc,s.productId desc limit 0,1";
            //按活动地区、绑定条件、结束时间、开始时间、活动关联产品分类及产品层级追溯 进行排序
        return $this->db->query($sql)->row();
    }
    
    public function get_parent_by_id($sub_id){
        $sub=$this->get($sub_id);
        return $this->activity->get($sub->parentId);
    }
    
    public function get_by_batch($batid){
    	return $this->db->query("SELECT s.*,
    			a.id mainId,a.mchId as mchId,a.name mainName,a.description mainDesc,a.imgUrl mainImgUrl,a.state mainState
    			FROM sub_activities s
    			INNER JOIN activities a ON s.parentId=a.id
    			WHERE s.batchId=$batid and s.rowStatus=0")->result();
    }
}
