<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/card_lists.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">乐券策略管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="cardTable" class="display">
                <thead>
                    <tr>
                        <th width="50">编号</th>
                        <th width="200">名称</th>
                        <th width="200">券组奖励</th>
                        <th width="120">中奖概率</th>
                        <th>总数量/剩余（个）</th>
                        <th>操作</th>
                    </tr>
                </thead>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/card/addgroup">添加券组</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>