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
<script type="text/javascript" src="/static/js/product_category.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">产品分类</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="categoryTable" class="display">
                <thead>
                    <tr>
                        <th>名称</th>
                        <th>描述</th>
                        <th>操作</th>
                    </tr>
                </thead>
                
            </table>
            <div id="btnAdd" class="btn btn-blue">添加分类</div>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>