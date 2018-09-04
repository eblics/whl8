<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="/static/css/activity.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/jquery.areaselect.js?v=1.1"></script>
    <script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/activity_editsub.js?v=1.2"></script>
    <script  type="text/javascript">var pTime={
        'pStartTime':'<?=$data->pStartTime?>','pEndTime':'<?=$data->pEndTime?>'
    };</script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>子活动</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="180">活动名称：</td>
                        <td class="value" width="350">
                            <?=$data->parentName?>
                            <input name="parentId" type="hidden" value="<?=$data->parentId?>" />
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">子活动名称：</td>
                        <td class="value" width="350">
                            <input class="input" id="name" name="name" value="<?=$data->name?>" valType="PRONAME" msg="<font color=red>*</font>名称填写不正确"/>
                        </td>
                        <td class="tip">将会在适当的环节推送显示给用户，请妥善填写 (4-32个字符)</td>
                    </tr>
                    <tr>
                        <td class="name">子活动简介：</td>
                        <td class="value" width="350">
                            <textarea class="textarea" id="content" name="content" rows="6" maxlength="170" valType="NOTNULL" msg="<font color=red>*</font>简介填写不正确"><?=$data->content?></textarea>
                        </td>
                        <td class="tip">将会在适当的环节推送显示给用户，请妥善填写 (170字以内)</td>
                    </tr>
                    <tr>
                        <td class="name">活动对象：</td><td class="value"><select id="role" class="select" name="role" edit-value="<?=$data->role?>">
                        <option value="0">消费者</option>
                        <option value="1">服务员</option>
                        </select></td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">策略类型：</td><td class="value"><select id="activityType" class="select" name="activityType" edit-value="<?=$data->activityType?>">
                        <option value="0">红包策略</option>
                        <?php if($_SESSION['expired']==null){?>
                        <option value="2">乐券策略</option>
                        <option value="4">积分策略</option>
                        <option value="3">组合策略</option>
                        <option value="5">叠加策略</option>
                        <option value="6">累计策略</option>
                        <?php }?>
                        </select></td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">策略内容：</td><td class="value"><select id="detailId" class="select" name="detailId" edit-value="<?=$data->detailId?>"></select></td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">H5应用：</td><td class="value"><select id="webAppId" class="select" name="webAppId" edit-value="<?=$data->webAppId?>"></select></td><td class="tip"><span style="display:none;height:30px;line-height:30px;margin:0;" id="webappConfig" class="btn btn-gray noselect">配置</span></td>
                    </tr>
                    <tr>
                        <td class="name">上报地理位置才能匹配活动：</td><td class="value">
                            <label for="geoNeeded_0"><input id="geoNeeded_0" name="geoNeeded" type="radio" value="1" <?=(int) $data->geoNeeded===1?'checked':''?>/> 是</label> &nbsp;&nbsp; 
                            <label for="geoNeeded_1"><input id="geoNeeded_1" name="geoNeeded" type="radio" value="0" <?=(int) $data->geoNeeded===0?'checked':''?>/> 否</label>
                        </td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">奖励发放：</td><td class="value">
                            <label for="subscribeNeeded_0"><input id="subscribeNeeded_0" name="subscribeNeeded" type="radio" value="1" <?=(int) $data->subscribeNeeded===1?'checked':''?>/> 需要关注公众号</label> &nbsp;&nbsp; 
                            <label for="subscribeNeeded_1"><input id="subscribeNeeded_1" name="subscribeNeeded" type="radio" value="0" <?=(int) $data->subscribeNeeded===0?'checked':''?>/> 无需关注公众号</label>
                        </td><td class="tip"></td>
                    </tr>
                    <tr class="time-tr">
                        <td class="name">活动时间：</td>
                        <td class="value">
                            <input class="input Wdate" id="startTime" name="startTime" value="<?=$data->startTime?>" style="width:40%"
                            valType="NOTNULL" msg="<font color=red>*</font>『开始时间』不能为空" style="background-position:98% 50%;"  
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:pTime.pStartTime,maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                            ~ 
                            <input class="input Wdate" id="endTime" name="endTime" value="<?=$data->endTime?>"  style="width:40%"
                            valType="NOTNULL" msg="<font color=red>*</font>『结束时间』不能为空" style="background-position:98% 50%;"  
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'startTime\')}',maxDate:pTime.pEndTime})" />
                        </td>
                        <td class="tip">结束时间必须大于开始时间。<font color=black>时间范围必须小于父活动！</font></td>
                    </tr>
                    <tr>
                        <td class="name">指定活动地区：</td><td class="value"><input id="areaCheck" name="areaCheck" type="checkbox"  <?=$view->areaCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="area-tr" style="display:<?=$view->areaShow?>">
                        <td class="name"></td><td class="value">
                            <input id="areaCode" class="input area-select" name="areaCode" value="<?=$data->areaCode?>" />
                            <!--<select id="areaCode" class="select" name="areaCode" edit-value="<?=$data->areaCode?>"></select>--></td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">关联乐码批次：</td><td class="value"><input id="batchCheck" name="batchCheck" type="checkbox"  <?=$view->batchCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="batch-tr" style="display:<?=$view->batchShow?>">
                        <td class="name"></td>
                        <td class="value">
                            <select id="batchId" class="select" name="batchId" edit-value="<?=$data->batchId?>"></select>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">关联生产入库单：</td><td class="value"><input id="prodInOrderCheck" name="prodInOrderCheck" type="checkbox"  <?=$view->prodInOrderCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="prodInOrder-tr" style="display:<?=$view->prodInShow?>">
                        <td class="name"></td><td class="value"><input type="hidden"  id="prodInOrderId"  name="prodInOrderId" value="<?=$data->prodInOrderId?>"/><input id="prodInOrderIdInput" class="input"/></td><td class="tip">请输入生产入库单号，选择搜索匹配的项</td>
                    </tr>
                    <tr>
                        <td class="name">关联出库单：</td><td class="value"><input id="outOrderCheck" name="outOrderCheck" type="checkbox"  <?=$view->outOrderCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="outOrder-tr" style="display:<?=$view->outOrderShow?>">
                        <td class="name"></td><td class="value"><input type="hidden"  id="outOrderId"  name="outOrderId" value="<?=$data->outOrderId?>"/><input id="outOrderIdInput" class="input"/></td><td class="tip">请输入出库单号，选择搜索匹配的项</td>
                    </tr>
                    <tr>
                        <td class="name">关联销售区域：</td><td class="value"><input id="saletoagcCheck" name="saletoagcCheck" type="checkbox"  <?=$view->saletoagcCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="saletoagc-tr" style="display:<?=$view->saletoagcShow?>">
                        <td class="name"></td><td class="value">
                            <input id="saletoagc" class="input area-select" name="saletoagc" value="<?=$data->saletoagc?>" />
                            <!--<select id="saletoagc" class="select" name="saletoagc" edit-value="<?=$data->saletoagc?>"></select>--></td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">关联商品过期策略：</td><td class="value"><input id="expireCheck" name="expireCheck" type="checkbox"  <?=$view->expireCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="expire-tr" style="display:<?=$view->expireShow?>">
                        <td class="name"></td><td class="value"><select style="width:40%" id="expireOprt" class="select" name="expireOprt" edit-value="<?=$data->expireOprt?>">
                        <option value="=">等于</option><option value="<=">小于等于</option><option value="&lt;">小于</option>
                        </select>
                        <input class="input Wdate" id="expireTime" name="expireTime" value="<?=$data->expireTime?>" style="width:40%"
                            style="background-position:98% 50%;"  
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
                        </td><td class="tip">制定商品质保过期策略</td>
                    </tr>
                    <tr>
                        <td class="name">关联产品：</td>
                        <td class="value"><input id="widthProduct" name="widthProduct" type="checkbox" <?=$view->is_pre==false?'checked=checked':''?> /></td>
                    </tr>
                    <tr class="product product-ca" style="display:<?=$view->category_display?>">
                        <td class="name">产品分类：</td>
                        <td class="value">
                            <select id="categoryId" class="select" name="categoryId" edit-value="<?=$data->categoryId?>"></select>
                        </td>
                        <td><label for="onlyCategory"><input id="onlyCategory" name="onlyCategory" type="checkbox" <?=$view->is_only==true?'checked=checked':''?> /> 仅关联分类</label></td>
                    </tr>
                    <tr class="product product-list" style="display:<?=$view->product_display?>">
                        <td class="name">产品列表：</td>
                        <td class="value">
                            <select id="productId" class="select" name="productId" edit-value="<?=$data->productId?>"></select>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">给参与此活动的用户打标签：</td><td class="value"><input id="tagCheck" name="tagCheck" type="checkbox"  <?=$view->tagCheck?> /></td><td class="tip"></td>
                    </tr>
                    <tr class="tag-tr" style="display:<?=$view->tagShow?>">
                        <td class="name"></td><td class="value">
                        <input type="hidden" id="tagId" name="tagId" value="<?=$data->tagId?>"/>
                        <ul id="tagList">
                        </ul>
                        </td><td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">专门针对羊毛党的活动：</td>
                        <td class="value"><input id="isForEvil" name="isForEvil" type="checkbox" <?=$view->is_forevil==false?'':'checked=checked'?> /></td>
                    </tr>
                    <tr class="forevil forevil-list" style="display:<?=$view->evilShow?>">
                        <td class="name">选择恶意用户等级：</td>
                        <td class="value">
                        	<label for="forEvil1"><input id="forEvil1" name="forEvil" type="checkbox" value='1' <?=stripos($data->forEvil,'1') !== false?'checked=checked':''?> /> 1</label>
                        	<label for="forEvil2"><input id="forEvil2" name="forEvil" type="checkbox" value='2' <?=stripos($data->forEvil,'2') !== false?'checked=checked':''?> /> 2</label>
                        	<label for="forEvil3"><input id="forEvil3" name="forEvil" type="checkbox" value='3' <?=stripos($data->forEvil,'3') !== false?'checked=checked':''?> /> 3</label>
                        	<label for="forEvil4"><input id="forEvil4" name="forEvil" type="checkbox" value='4' <?=stripos($data->forEvil,'4') !== false?'checked=checked':''?> /> 4</label>
                        </td>
                        <td class="tip">选中的恶意用户等级将匹配该活动规则，恶意等级按数值由低到高</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <span id="btnSave" class="btn btn-blue noselect">保存</span>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php include 'footer.php';?>

    <div class="h5setting">
        <iframe src=""></iframe>
        <div class="close">X</div>  
    </div>
</body>
     
</html>

