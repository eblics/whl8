<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/salesman.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">
            <?php if ($edit): ?>
            编辑业务员
            <?php else:?>
            添加业务员
            <?php endif;?>
            </span>
        </div>
        <div class="h20"></div>
        <div class="content">
           <form>
                <table class="table-form">
                <tr><td class="name" width="100">业务员姓名：</td>
                    <td class="value" width="350">
                    <?php if ($edit): ?>
                        <input class="input" name="realname" id="realname" valType="ADMINNAME"
                            value="<?=$salesman->realName?>"
                            msg="<font color=red>*</font>请正确输入『业务员姓名』" />
                    <?php else:?>
                        <input class="input" name="realname" id="realname" valType="ADMINNAME"
                            msg="<font color=red>*</font>请正确输入『业务员姓名』" />
                    <?php endif;?>
                    </td>
                    <td class="tip">请控制在2~8个字符之间</td></tr>
                <tr><td class="name">手机号：</td>
                    <td class="value">
                    <?php if ($edit): ?>
                    <input class="input" name="mobile" id="mobile" valType="MOBILE"
                        value="<?=$salesman->mobile?>" 
                        msg="<font color=red>*</font>请正确输入『手机号』" />
                    <?php else:?>
                    <input class="input" name="mobile" id="mobile" valType="MOBILE"
                        msg="<font color=red>*</font>请正确输入『手机号』" />
                    <?php endif;?>
                    </td>
                    <td class="tip"></td></tr>
                <tr><td class="name">身份证号码：</td>
                    <td class="value">
                        <?php if ($edit): ?>
                    <input class="input" name="id_card_no" id="id_card_no"
                        value="<?=$salesman->idCardNo?>" 
                        msg="<font color=red>*</font>请正确输入『身份证号』" />
                    <?php else:?>
                    <input class="input" name="id_card_no" id="id_card_no"
                        msg="<font color=red>*</font>请正确输入『身份证号』" />
                    <?php endif;?></td>
                    <td class="tip"></td></tr>                
                <tr><td></td>
                    <td><span id="btnBack" class="btn btn-blue noselect">
                        返回
                    </span><span id="btnSave" class="btn btn-blue noselect">
                        保存
                    </span></td>
                    <td></td></tr>
            </table>
            </form>
        </div>
    </div>
</div>
<?php if ($edit): ?>
<input id="salesman_id" type="hidden" value="<?=$salesman->id?>" />
<?php endif;?>
<?php include 'footer.php';?>
<script type="text/javascript">
    Page.edit();
</script>
</body>
</html>