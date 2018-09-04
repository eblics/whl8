<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/mall_configure.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">商城配置</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">商城名称：</td>
                        <td class="value" width="350">
                            <input type="hidden" id="id" value="<?=$data['mallId']?>">
                            <input class="input" id="name" maxlength="10" name="name" value="<?=$data['name']?>" valType="GOODNAME" msg="<font color=red>*</font>商城名称格式不正确"/>
                        </td>
                        <td class="tip">商城名称 4-10个字符</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">商城描述：</td>
                        <td class="value" width="350">
                            <textarea id="desc" class="textarea" msg="<font color=red>*</font>『商城描述』不能为空" valtype="NOTNULL" rows="8" maxlength="600" name="desc"><?=$data['desc']?></textarea>
                        </td>
                        <td class="tip">建议在100字以内</td>
                    </tr>
                    
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" value="确认开通商城"></td><td></td>
                    </tr>
                </table>
                
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>