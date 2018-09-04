<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<style type="text/css">
    .main .table-form{margin:5px 10px 25px 10px;}
    .main .table-form .condition{margin:0 15px;}
    .main .table-form .condition .input{width:130px;background-position:98% 50%;}
    .main .table-form .btns{margin-left:20px;}
    .main .table-form .btns .btn{margin-left:5px;}
    .main .table-form .btn-dob{width:30px;height:20px;line-height:20px;text-align:center;color:white;border-radius:3px;}
    .chere{cursor:pointer;}
</style>
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">乐券持有者 - <?=$card_name?></span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <input type="hidden" id="card_id" value="<?=$card_id?>">
            <table id="card_holder_table" class="display">
                <thead>
                    <tr>
                        <th>用户ID</th>
                        <th>用户角色</th>
                        <th>微信昵称</th>
                        <th>真实姓名</th>
                        <th>手机号码</th>
                        <th>持有数量</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>用户ID</th>
                        <th>用户角色</th>
                        <th>微信昵称</th>
                        <th>真实姓名</th>
                        <th>手机号码</th>
                        <th>持有数量</th>
                    </tr>
                </tfoot>
            </table>
            <a class="btn btn-blue noselect" href="/card/down_card_holder/<?=$card_id?>" target="_blank">导出乐券持有者名单</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/card_holder.js"></script>
</body>
</html>