<?php include 'common/header.php' ?>
<link rel="stylesheet" type="text/css" href="/static/css/merchant_lock.css">
<script type="text/javascript" src="/static/js/merchant/merchant_search.js"></script>

</head>
<body><?php include 'common/menus.php';?>
<div class="main mlock">
    <?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <div class="h20"></div>
            <div class="search-type">
                <div class="ct1">选择搜索类型：</div>
                <div class="ct2">
                    <select id="s-type">
                        <option value="1">企业openid</option>
                        <!-- <option value="2">企业nickName</option> -->
                        <option value="3">平台openid</option>
                        <option value="4">平台nickName</option>
                    </select>
                </div>
                <div class="ct3">
                    <input type="text" name="svalue" id="svalue">
                </div>
                <div class="ct4"><button id="bsearch">查找</button></div>
            </div>
            <div class="hr-line"> </div>
            <table id="searchTable" class="display" >
            <thead>
                <tr>
                    <th>用户ID</th>
                    <th>用户昵称</th>
                    <th>openid</th>
                    <th>头像</th>
                    <th>所属企业</th>
                    <th>状态</th>
                    <th width="220" class="nowrap">操作</th>
                </tr>
            </thead>
            </table>
        </div>
    </div>
</div>
<?php include 'common/footer.php';?>