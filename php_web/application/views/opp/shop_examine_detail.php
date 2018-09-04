<?php include 'common/header.php' ?>
<link type="text/css" rel="stylesheet" href="/static/css/shop_examine_detail.css" />
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
                        <td class="name" width="150">门店名称：</td>
                        <td class="value" width="350">
                            <span class="text"><?=$name?></span>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">门店地址：</td>
                        <td class="value" width="350">
                            <span class="text"><?=$address?></span>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">店主姓名：</td>
                        <td class="value" width="350">
                            <span class="text"><?=$ownerName?></span>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">店主手机号：</td>
                        <td class="value" width="350">
                            <span class="text"><?=$ownerPhoneNum?></span>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td><td>
                        <?php if($state==1):?>
                        <input class="btn btn-blue" type="button" id="agree" data-id="<?=$id?>" value="同意">
                        <input class="btn btn-blue" type="button" id="reject" data-id="<?=$id?>" value="拒绝">
                        <?php endif;?>
                        </td><td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<script type="text/javascript" src="/static/js/common/common.js"></script>
<script type="text/javascript" src="/static/js/common/jquery.multi-select.js"></script>
<script type="text/javascript" src="/static/js/shop/shop_examine_detail.js"></script>
<?php include 'common/footer.php';?>