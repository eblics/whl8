<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/activity_edit.js"></script>
    <script  type="text/javascript">var sTime={
        'subStartTime':'<?=$data->subStartTime?>','subEndTime':'<?=$data->subEndTime?>'
    };</script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>活动</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="100">名称：</td>
                        <td class="value" width="350">
                            <input class="input" id="name" name="name" value="<?=$data->name?>" maxlength="32" valType="PRONAME" msg="<font color=red>*</font>名称填写不正确"/>
                        </td>
                        <td class="tip">4-32个字符</td>
                    </tr>
                    <tr>
                        <td class="name">开始时间：</td>
                        <td class="value">
                            <input class="input Wdate" id="startTime" name="startTime" value="<?=$data->startTime?>"
                            valType="NOTNULL" msg="<font color=red>*</font>『开始时间』不能为空" style="background-position:98% 50%;"
                            <?php
                            if($view->title=='修改'){
                            ?>
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:sTime.subStartTime})"
                            <?php
                            }else{
                            ?>
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'endTime\')}'})"
                            <?php
                            }
                            ?>
                            />
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">结束时间：</td>
                        <td class="value">
                            <input class="input Wdate" id="endTime" name="endTime" value="<?=$data->endTime?>"
                            valType="NOTNULL" msg="<font color=red>*</font>『开始时间』不能为空" style="background-position:98% 50%;"
                            <?php
                            if($view->title=='修改'){
                            ?>
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:sTime.subEndTime})"
                            <?php
                            }else{
                            ?>
                            onfocus="WdatePicker({isShowWeek:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'startTime\')}'})"
                            <?php
                            }
                            ?>
                            />
                        </td>
                        <td class="tip">结束时间必须大于开始时间。<font color=black>时间范围必须大于所有子活动！</font></td>
                    </tr>
                    <tr>
                        <td class="name">活动图片：</td><td class="value"><input class="js-upload" id="imgUrl" type="file" name="imgUrl" edit-value="<?=$data->imgUrl?>" /></td><td class="tip">图片大小不得超过500k</td>
                    </tr>
                    <tr>
                        <td class="name">活动简介：</td><td class="value"><textarea class="textarea" name="description" rows="8" maxlength="1000" valType="NOTNULL" msg="<font color=red>*</font>『活动简介』不能为空" ><?=$data->description?></textarea></td><td class="tip">活动的简介，控制在1000字内</td>
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
</body>

</html>

