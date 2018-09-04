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
<script type="text/javascript" src="/static/js/mall_category.js"></script>
<style type="text/css">
    .content .main-tip{
        width:90%;
        height:300px;
        background:#F9F9F9;
        border-radius:5px;
        border:1px solid #CECBCB;
        margin:50px auto 0;
    }
    .content .tip-title{
        font-size:20px;
        font-weight:bold;
        text-align:center;
        line-height:30px;
        width:100%;
        height:30px;
        margin-top:100px;
    }
    .content .tip-btn{
        width:120px;
        height:30px;
        border-radius:3px;
        background:#5A8EDD;
        text-align:center;
        line-height:30px;
        font-size:14px;
        color:white;
        margin:0 auto;
        margin-top:50px;
    }
    .content .tip-btn:hover{
        background:#5583CB;
        cursor:pointer;
    }
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">商品分类</span>
        </div>
        <div class="h20"></div>
        <?php if($data['isopen'] == false): ?>
            <div class="content">
                <div class="main-tip">
                    <div class="tip-title">贵企业还未开通商城，该块功能暂时不能使用，请在开通商城之后再使用！</div>
                    <div class="tip-btn">点击去开通商城</div>
                </div>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>