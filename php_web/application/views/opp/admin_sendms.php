<?php include 'common/header.php'; ?></head>
<body> <?php include 'common/menus.php';?>
<div class="main">
<?php include 'admin_lefter.php';;?>
<div class="rightmain">
    <div class="path"><span class="title fleft">帐号安全</span></div>
    <div class="h20"></div>
    <form type="validate">
    <table class="table-form">
    <tr><td class="name" width="150">原始密码：</td>
    <td class="value" width="350">
        <input class="input" type="password" id="old_pass" name="oldpass" 
        valType="required" msg="<font color=red>*</font>不能为空"/></td>
    <td class="tip">您的账户原始密码</td></tr>
    <tr><td class="name" width="150">新密码：</td>
    <td class="value" width="350">
        <input class="input" type="password" id="new_pass" name="newpass" 
        valType="PASS" msg="<font color=red>*</font>密码为英文或者数字且在6-18位之间"/></td>
    <td class="tip">密码为英文或者数字且在6-18位之间</td></tr>
    <tr><td class="name" width="150">确认密码：</td>
    <td class="value" width="350">
        <input class="input" type="password" id="new_pass2" name="renewpass" 
        valType="PASS" msg="<font color=red>*</font>密码为英文或者数字且在6-18位之间"/></td>
    <td class="tip">确认新密码</td></tr>
    <tr><td></td><td><input class="btn btn-blue" type="button" id="sub" 
        value="保存"></td><td></td></tr>
    </table>   
    </form>
 </div>
</div>
<script type="text/javascript" src="/static/js/admin/passwd.js"></script>
<?php include 'common/footer.php';?>