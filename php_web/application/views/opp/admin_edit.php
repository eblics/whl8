<?php include 'common/header.php'; ?>
<link type="text/css" rel="stylesheet" href="/static/css/account.css" />
</head><body><?php include 'common/menus.php';?>
    <div class="main">
        <?php include 'admin_lefter.php';?>
        <div class="rightmain">
            <div class="path"><span class="title fleft">账户编辑</span></div>
            <div class="h20"></div>
            <form type="validate">
            <table class="table-form">
                <tr><td class="name" width="150">用户名称：</td>
                    <td class="value" width="350">
                        <?php if (isset($admin)): ?>
                        <input type="text" class="input" id="userName" name="userName" 
                            value="<?=$admin->userName?>"
                            valType="NICKNAME" msg="<font color=red>*</font>帐号格式不正确">
                        <?php else:?>
                        <input type="text" class="input" id="userName" name="userName" 
                            valType="NICKNAME" msg="<font color=red>*</font>帐号格式不正确">
                        <?php endif;?>
                    </td>
                    <td class="tip">
                        运营人员的用户名，不能重复，作为登录帐号使用，建议中文全拼</td></tr>
                <tr><td class="name" width="150">手机号码：</td>
                    <td class="value" width="350">
                        <?php if (isset($admin)): ?>
                           <input type="text" class="input" id="phoneNum" name="phoneNum" 
                           value="<?=$admin->phoneNum?>"
                            valType="MOBILE" msg="<font color=red>*</font>手机格式不正确">
                        <?php else:?>
                            <input type="text" class="input" id="phoneNum" name="phoneNum" 
                            valType="MOBILE" msg="<font color=red>*</font>手机格式不正确">
                        <?php endif;?>
                    </td>
                    <td class="tip">
                        运营人员的的手机号码，请勿偷懒，该项必填</td></tr>
                <tr><td class="name">帐户类型：</td>
                    <td class="value">
                    <?php if (isset($admin)): ?>
                        <?php if ($admin->role == AdminRoleEnum::Admin):?>
                            <label for="aType">
                            <input id="aType" name="levelType" type="radio" 
                                value="1" checked="checked"/>&nbsp;管理员</label>
                            <label for="aType1">&nbsp;&nbsp;
                            <input id="aType1" name="levelType" type="radio" 
                                value="2" />&nbsp;运营人员</label>
                        <?php else:?>
                            <label for="aType">
                            <input id="aType" name="levelType" type="radio" 
                                value="1" />&nbsp;管理员</label>
                            <label for="aType1">&nbsp;&nbsp;
                            <input id="aType1" name="levelType" type="radio" 
                                value="2" checked="checked" />&nbsp;运营人员</label>
                        <?php endif;?>
                    <?php else:?>
                    <label for="aType">
                    <input id="aType" name="levelType" type="radio" 
                        value="1" />&nbsp;管理员</label>
                    <label for="aType1">&nbsp;&nbsp;
                    <input id="aType1" name="levelType" type="radio" 
                        value="2" checked="checked" />&nbsp;运营人员</label>
                    <?php endif;?>
                        
                    </td></tr>
                <tr>
                    <td class="name" width="150">帐号密码：</td>
                    <td id="pass">默认密码是123456，运营人员在首次登录后务必修改密码</td>
                    <td class="tip">请注意，修改信息将重置帐户密码</td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-blue" type="button" id="sub" value="保存"></td>
                    <td></td>
                </tr>
            </table>
            </form>
        </div>
    </div>
<?php if (isset($admin)): ?>
<input id="admin_id" type="hidden" value="<?=$admin->id?>" />
<?php endif;?>
<script type="text/javascript" src="/static/js/admin/admin_edit.js"></script>
<?php include 'common/footer.php';?>