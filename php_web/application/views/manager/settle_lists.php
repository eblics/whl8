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
<script type="text/javascript" src="/static/js/settle.js"></script>
<style type="text/css">
.list-item {
	margin-top: 5px;
	border-bottom: 1px solid silver;
	padding: 10px;
}
.list-title {
	font-size: 18px;
	font-weight: bold;
}
.list-item div {
	display: inline-block;
	width: 45%;
}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">业务员核销</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="settleTable" class="display">
                <thead>
                    <tr>
                        <th width="30">编号</th> 
                        <th>业务员姓名</th>
                        <th>申请时间</th>
                        <th>乐券数量</th>
                        <th>申请状态</th>
                        <th>状态</th>
                        <th width="160">操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>编号</th> 
                        <th>业务员姓名</th>
                        <th>申请时间</th>
                        <th>乐券数量</th>
                        <th>申请状态</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<script type="text/javascript">
    var salesmanLists = <?=json_encode($salesmanLists) ?>;
    Page.init();
</script>
</body>
</html>
