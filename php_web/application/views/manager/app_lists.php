<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/app_lists.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/app_lists.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
	<?php include 'lefter_app.php';?>
    <div class="rightmain">
    	<div class="path">
            <span class="title fleft">应用市场</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <ul id="app_container" class="fix"></ul>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
