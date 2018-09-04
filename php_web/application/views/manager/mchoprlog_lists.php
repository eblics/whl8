<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/libs/DateRange/dateRange.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/log_lists.js"></script>
<script type="text/javascript" src="/static/libs/DateRange/dateRange.js"></script>
</head>
<body >
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">操作日志</span>
        </div>
        <div class="h20"></div>
        <div class="content">
        <ul id="filter" class="filter"></ul>
            <table id="logTable" class="display">
                <thead>
                    <tr>
                        <th width="35px">序号</th>
                        <th width="35px">操作主体</th>
                        <th>操作</th>
                        <th width="300px" class="center">概述</th>
                        <th>操作时间</th>
                        <th>操作用户</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>序号</th>
                        <th>操作主体</th>
                        <th>操作</th>
                        <th class="center">概述</th>
                        <th>操作时间</th>
                        <th>操作用户</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>
<?php include 'footer.php';?>
</body>
</html>
