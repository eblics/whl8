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
<script type="text/javascript" src="/static/js/admin.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">
            <?php if (isset($edit)): ?>
            编辑角色账户
            <?php else:?>
            添加角色账户
            <?php endif;?>
            </span>
        </div>
        <div class="h20"></div>
        <div class="content">
           <form>
                <table class="table-form">
                <tr><td class="name" width="100">角色姓名：</td>
                    <td class="value" width="350">
                    <?php if (isset($edit)): ?>
                        <input class="input" name="realname" id="realname" valType="ADMINNAME"
                            value="<?=$admin->realName?>"
                            msg="<font color=red>*</font>请正确输入『角色姓名』" />
                    <?php else:?>
                    <input class="input" name="realname" id="realname" valType="ADMINNAME"
                        msg="<font color=red>*</font>请正确输入『角色姓名』" />
                    <?php endif;?>
                    </td>
                    <td class="tip">请控制在2~8个字符之间</td></tr>
                <tr><td class="name">手机号：</td>
                    <td class="value">
                    <?php if (isset($edit)): ?>
                    <input class="input" name="mobile" id="mobile" valType="MOBILE"
                        value="<?=$admin->phoneNum?>" readonly="true" disabled
                        msg="<font color=red>*</font>请正确输入『手机号』" />
                    <?php else:?>
                    <input class="input" name="mobile" id="mobile" valType="MOBILE"
                        msg="<font color=red>*</font>请正确输入『手机号』" />
                    <?php endif;?>
                    </td>
                    <td class="tip">角色手机号码（初始密码：123456）</td>
                </tr>
                <tr><td class="name">角色：</td>
                    <td class="value">
                        <select class="select" name="role" id="role">
                        <?php if (isset($edit)): ?>
                            <?php foreach ($roles as $role) {
                                if ($admin->role == $role->id) {
                                    print '<option value="'. $role->id .'" selected="selected">'. $role->roleName .'</option>';
                                } else {
                                    print '<option value="'. $role->id .'">'. $role->roleName .'</option>';
                                }
                            } ?>
                        <?php else:?>
                            <?php foreach ($roles as $role) {
                                print '<option value="'. $role->id .'">'. $role->roleName .'</option>';
                            } ?>
                        <?php endif;?>
                        </select></td>
                    <td class="tip">请选择角色所属组</td></tr>

                <?php if($mch_id == 0 || $mch_id == 325): ?>
                <tr>
                    <td class="name">免短信验证：</td>
                    <td class="value">
                        <?php if(isset($admin)): ?>
                            <input type="checkbox" name="freedom" <?=$admin->noSms==1?'checked':'' ?> id="freedom" />
                        <?php else:?>
                            <input type="checkbox" name="freedom" id="freedom" />
                        <?php endif; ?>
                    </td>
                    <td class="tip">该帐号是否免短信验证登录</td>
                </tr>
                <?php endif; ?>

                <tr><td></td>
                    <td><span id="btnSave" class="btn btn-blue noselect">
                        保存
                    </span><span id="btnAddRole" class="btn btn-blue noselect">
                        新建角色
                    </span></td>
                    <td></td></tr>
            </table>
            </form>
        </div>
    </div>
</div>
<?php if (isset($edit)): ?>
<input id="admin_id_edit" type="hidden" value="<?=$admin->id?>" />
<?php endif;?>
<?php include 'footer.php';?>
<script type="text/javascript">
    adminList.edit();
</script>
</body>
</html>
