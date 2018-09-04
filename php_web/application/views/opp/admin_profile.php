<?php include 'common/header.php'; ?>
<link type="text/css" rel="stylesheet" href="/static/css/account.css" />
</head>
<body> <?php include "common/menus.php";?>
    <div class="main">
        <?php include 'admin_lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">账户信息</span>
            </div>
            <div class="h20"></div>
            <form type="validate">
            <table class="table-form">
            <tr>
            <td class="name" width="150">真实名字：</td>
            <td class="value" width="350">
                <input type="text" class="input" id="realName" name="realName" valType="NAME" value="<?=$admin->realName?>" msg="<font color=red>*</font>名字格式不正确">
            </td>
            <td class="tip">请输入真实名字，便于管理</td>
            </tr>
             <tr><td class="name" width="150">手机号码：</td>
                <td class="value" width="350">
                    <input type="text" class="input" id="phoneNum" name="phoneNum" valType="MOBILE" value="<?=$admin->phoneNum?>" msg="<font color=red>*</font>手机号码格式不正确"></td>
                <td class="tip">手机号码拿过来</td></tr>
            <tr><td class="name" width="150">邮箱：</td>
            <td class="value" width="350">
                <input type="text" class="input" id="mail" name="mail" valType="MAIL" value="<?=$admin->mail?>" msg="<font color=red>*</font>邮箱格式不正确"></td>
                <td class="tip"></td></tr>
            <tr><td></td>
            <td><input class="btn btn-blue" type="button" id="sub" value="保存"></td>
            <td></td>
            </tr>
            </table>
            </form>
        </div>
    </div>
<script type="text/javascript" src="/static/js/admin/profile.js"></script>
<?php include 'common/footer.php';?>