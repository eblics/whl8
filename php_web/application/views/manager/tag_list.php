<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/tag_list.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">标签列表</span> <p style="color:#ccc">（注：标签数量上限为100，请妥善规划管理标签设置）
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="tagTable" class="display">
                <thead>
                    <tr>
                    	<th>标签ID</th>
                        <th>名称</th>
                        <th>粉丝数</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>标签ID</th>
                        <th>名称</th>
                        <th>粉丝数</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/tag/add">新建标签</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>