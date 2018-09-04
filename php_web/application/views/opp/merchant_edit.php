<?php include 'common/header.php' ?>
<link type="text/css" rel="stylesheet" href="/static/css/account.css" />
</head>
<body>
<?php include 'common/menus.php';?>
    <div class="main">
        <?php include 'merchant_lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">帐号添加</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">帐号昵称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="userName" name="userName" valType="NAME" msg="<font color=red>*</font>帐号格式不正确">
                        </td>
                        <td class="tip">下次登录将显示在右上角，比如：张三丰</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">企业名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="mname" name="mname" valType="NAME" msg="<font color=red>*</font>企业名称格式不正确">
                        </td>
                        <td class="tip">企业的全称，比如：北京爱创科技股份有限公司</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">手机号码：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="phoneNum" name="phoneNum" valType="MOBILE" msg="<font color=red>*</font>手机格式不正确">
                        </td>
                        <td class="tip">必填，将作为企业登录的基本凭证</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">邮箱：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="mail" name="mail" valType="MAIL" msg="<font color=red>*</font>邮箱格式不正确">
                        </td>
                        <td class="tip">企业的联系邮箱</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">帐号密码：</td>
                        <td>默认密码就是手机号码</td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<script type="text/javascript" src="/static/js/merchant/merchant_edit.js"></script>
<?php include 'common/footer.php';?>