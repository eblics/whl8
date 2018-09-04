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
                <span class="title fleft">授权设置</span>
            </div>
            <div class="h20"></div>
            <form type="validate">
                <table class="table-form">
                    <tr>
                        <td class="name" width="150">企业名称：</td>
                        <td class="value" width="350">
                            <?=$name?>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">授权门店：</td>
                        <td>
                            <select id="shopIds" multiple="multiple" style="display:none">
                              <?=$shopHtml?>
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
<script type="text/javascript" src="/static/js/shop/permission_edit.js"></script>
<?php include 'common/footer.php';?>