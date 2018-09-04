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
<script type="text/javascript" src="/static/js/winlist.js"></script>
<style type="text/css">
    .main .table-form{margin:5px 10px 25px 10px;}
    .main .table-form .condition{margin:0 15px;}
    .main .table-form .condition .input{width:130px;background-position:98% 50%;}
    .main .table-form .btns{margin-left:20px;}
    .main .table-form .btns .btn{margin-left:5px;}
    .main .table-form .btn-dob{width:30px;height:20px;line-height:20px;text-align:center;color:white;border-radius:3px;}
    .chere{cursor:pointer;}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft"><?php echo $title?>&nbsp;&nbsp;中奖记录</span>
        </div>
        <div class="h20"></div>
        <div class="content">
        <input type="hidden" id="id" value="<?php echo $id;?>">
            <table id="cardWinlist" class="display">
                <thead>
                    <tr>
                    	<th>用户ID</th>
                        <th>微信昵称</th>
                        <th>真实姓名</th>
                        <th>手机号码</th>
                        <th>收货地址</th>
                        <th>中奖时间</th>
                        <th>中奖地点</th>
                        <th>状态</th>
                        <th>处理操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>用户ID</th>
                        <th>微信昵称</th>
                        <th>真实姓名</th>
                        <th>手机号码</th>
                        <th>收货地址</th>
                        <th>中奖时间</th>
                        <th>中奖地点</th>
                        <th>状态</th>
                        <th>处理状态</th>
                    </tr>
                </tfoot>
            </table>
            <a class="btn btn-blue noselect" href="/card/down_winlist/<?php echo $id;?>/<?php echo $title?>" target="_blank">导出中奖名单</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>