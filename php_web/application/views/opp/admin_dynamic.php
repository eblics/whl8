<?php include 'common/header.php'; ?></head>
<body><?php include 'common/menus.php';?>
<div class="main">
<?php include 'admin_lefter.php';?>
<div class="rightmain">
    <div class="path"><span class="title fleft">系统日志</span></div>
    <div class="h20"></div>
    <div class="content">
        <table id="opeTable" class="display">
		    <thead><tr><th width="50">操作ID</th>
		    <th width="200">操作者</th>
		    <th width="120">动作</th>
		    <th width="120">日期</th>
		    <th>操作对象</th></thead>
		</table>
    </div>
</div>
<script type="text/javascript" src="/static/js/admin/dynamic.js"></script>
<?php include 'common/footer.php';?>