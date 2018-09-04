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
<script type="text/javascript" src="/static/js/mch_balance.js"></script>
<style type="text/css">
    .main .btn-order{margin:0px 35px}
    .main .balance{overflow:hidden;padding:40px 20px;font-size:15px;}
    .main .balance strong{font-weight:700;}
    .main .balance .amount{font-size:19px;color:#333;font-weight:700;}
    .transDialog .table-form{width:100%;border:1px solid #eee;}
    .transDialog .table-form th{font-size:14px;font-weight:700;padding:10px;}
    .transDialog .table-form td{padding:10px;}
    .transDialog .table-form td.bg{background:#eee;}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php if($_SESSION['expired']==null){?>
    <?php include 'lefter.php';?>
    <?php }else{?>
    <?php include 'lefter_user.php';?>
    <?php }?>
    
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">帐户余额</span>
        </div>
        <div class="balance">
            <strong>帐户余额：</strong><span class="amount"><?=$balance?></span> <span class="btn btn-blue noselect btn-order">充值</span> 　　　　　　　　
            <span style="color:#666;">欢乐扫平台代发红包：<b style="color:#000"><?=$hlspay?'已开启':'未开启'?></b></span> 
            <span style="color:#666;font-size:12px;">（若需开启欢乐扫平台代发红包功能，请联系客服人员）</span>
        </div>
        <div class="path">
            <span class="title fleft">收支明细</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="orderLog" class="display">
                <thead>
                    <tr>
                    	<th>订单号</th>
                        <th>金额</th>
                        <th>生成时间</th>
                        <th>类型</th>
                        <th>备注</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>订单号</th>
                        <th>金额</th>
                        <th>生成时间</th>
                        <th>类型</th>
                        <th>备注</th>
                        <th>状态</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>