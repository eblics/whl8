<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/group_setting.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">基础设置</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <form type="validate">
                <table class="table-form">
                    <tr>
                        <td class="name" width="150">好友圈别名：</td>
                        <td class="value" width="350">
                            <input class="input" id="productName" maxlength="10" name="productName" value="<?=$data->productName?>" valType="GROUPNAME" msg="<font color=red>*</font>限制在1-10个字之间"/>
                        </td>
                        <td class="tip">自定义好友圈的名称，不超过10个字</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">访问链接：</td>
                        <td class="value" width="350">
                            <input class="input" style="background:#eee;" value="<?=$data->productUrl?>" readonly="readonly"/>
                        </td>
                        <td class="tip">将此链接加到微信菜单使用</td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="save" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>