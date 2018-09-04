<?php include 'common/header.php' ?>
<link type="text/css" rel="stylesheet" href="/static/css/account.css" />
<link type="text/css" rel="stylesheet" href="/static/css/multi-select.css" />
</head>
<body>
<?php include 'common/menus.php';?>
    <div class="main">
        <?php include 'merchant_lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$title?></span>
            </div>
            <div class="h20"></div>
            <form type="validate">
                <table class="table-form">
                    <tr>
                        <td class="name" width="150">代理商名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="name" name="name" value="<?=$dealer['name']?>" valType="NOTNULL" msg="<font color=red>*</font>『代理商名称』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">代理商地址：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="address" name="address" value="<?=$dealer['address']?>" valType="NOTNULL" msg="<font color=red>*</font>『代理商地址』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系人：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="ownerName" name="ownerName" value="<?=$dealer['ownerName']?>" valType="NOTNULL" msg="<font color=red>*</font>『联系人名称』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">手机号码：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="phone" name="phone" maxlength="11" value="<?=$dealer['phone']?>" valType="MOBILE" msg="<font color=red>*</font>手机格式不正确"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">邮箱：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="mail" name="mail" value="<?=$dealer['mail']?>" valType="MAIL" msg="<font color=red>*</font>邮箱格式不正确"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">代理商代码：</td>
                        <td>
                            <input type="text" class="input" id="code" name="code" readonly="readonly" value="<?=$dealer['code']?>" valType="NOTNULL" msg="<font color=red>*</font>代码不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" data-id="<?=$dealer['id']?>" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<script type="text/javascript" src="/static/js/common/jquery.multi-select.js"></script>
<script type="text/javascript" src="/static/js/dealer/dealer_edit.js"></script>
<?php include 'common/footer.php';?>