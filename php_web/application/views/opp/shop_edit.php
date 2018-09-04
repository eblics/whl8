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
                <span class="title fleft"><?=$id==0?'门店添加':'门店编辑'?></span>
            </div>
            <div class="h20"></div>
            <form type="validate">
                <table class="table-form">
                    <tr>
                        <td class="name" width="150">门店名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="name" name="name" value="<?=$shop['name']?>" valType="NOTNULL" msg="<font color=red>*</font>『门店名称』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">门店地址：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="address" name="address" value="<?=$shop['address']?>" valType="NOTNULL" msg="<font color=red>*</font>『门店地址』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">店主姓名：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="ownerName" name="ownerName" value="<?=$shop['ownerName']?>" valType="NOTNULL" msg="<font color=red>*</font>『店主姓名』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">店主手机号：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="ownerPhoneNum" name="ownerPhoneNum" value="<?=$shop['ownerPhoneNum']?>" valType="MOBILE" msg="<font color=red>*</font>手机格式不正确"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">部署设备：</td>
                        <td>
                            <select id="deviceIds" multiple="multiple" style="display:none">
                              <?=$deviceHtml?>
                            </select>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" data-id="<?=$id?>" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<script type="text/javascript" src="/static/js/common/jquery.multi-select.js"></script>
<script type="text/javascript" src="/static/js/shop/shop_edit.js"></script>
<?php include 'common/footer.php';?>