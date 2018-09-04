<?php include 'common/header.php' ?>
<link rel="stylesheet" type="text/css" href="/static/css/merchant_lock.css">
<script type="text/javascript" src="/static/js/merchant/merchant_lists.js"></script>
</head>
<body>
<?php include 'common/menus.php';?>
<div class="main mlock">
    <?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <div class="h20"></div>
            <table id="searchTable">
                <thead>
                    <tr>
                        <th>平台ID</th>
                        <th>openid</th>
                        <th>头像</th>
                        <th>时间</th>
                        <th>拉黑原因</th>
                        <th width="220" class="nowrap">操作</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php include 'common/footer.php';?>