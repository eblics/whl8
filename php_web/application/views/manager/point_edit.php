<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/point_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>积分策略</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="100">名称：</td>
                        <td class="value" width="350">
                            <input class="input" id="name" name="name" value="<?=$data->name?>" valType="PRONAME" msg="<font color=red>*</font>请正确输入『名称』"/>
                        </td>
                        <td class="tip">4-32个字符</td>
                    </tr>
                    <tr>
                        <td class="name">子策略优先级：</td>
                        <td class="value">
                            <label for="priority_0"><input id="priority_0" name="priority" type="radio" value="0" <?=(int) $data->priority===0?'checked':''?>/> 随机</label> &nbsp;&nbsp;
                            <label for="priority_1"><input id="priority_1" name="priority" type="radio" value="1" <?=(int) $data->priority===1?'checked':''?>/> 额度从小到大</label> &nbsp;&nbsp;
                            <label for="priority_2"><input id="priority_2" name="priority" type="radio" value="2" <?=(int) $data->priority===2?'checked':''?>/> 额度从大到小</label>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><span id="btnSave" class="btn btn-blue noselect">保存</span>
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

