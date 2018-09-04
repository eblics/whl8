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
<script type="text/javascript" src="/static/js/group_lists.js"></script>
<style>
#groupTable .groupImg img{ border-radius:40px;}
.trans_dialog_group_name{height:50px; line-height:50px; text-align:center; font-size:17px;color:#333; background:#ddd;border-radius:4px;}
.trans_dialog_member_list{padding:10px 0;overflow:hidden;}
.trans_dialog_member_list li{width:420px; height:40px;line-height:40px; float:left;overflow:hidden;border:1px solid #ddd; margin:5px 10px 5px 0;border-radius:4px;}
.trans_dialog_member_list li>*{float:left;display:inline-block;height:40px;margin-right:10px;overflow:hidden;}
.trans_dialog_member_list li.master{border-color:#71C671;margin-right:420px;}
.trans_dialog_member_list li span{padding:0 5px;background:#ddd;color:#999;min-width:60px;text-align:center;}
.trans_dialog_member_list li.master span{background:#71C671;color:#000;}
.trans_dialog_member_list li img{width:38px;height:38px;margin-top:1px;border-radius:38px;}
.trans_dialog_member_list li strong{padding:0 5px;color:#666;}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">群组管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="groupTable" class="display">
                <thead>
                    <tr>
                        <th width="80">群ID</th>
                        <th width="80">群图标</th>
                        <th>群名称</th>
                        <th>成员数</th>
                        <th>创建时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>群ID</th>
                        <th>群图标</th>
                        <th>群名称</th>
                        <th>成员数</th>
                        <th>创建时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>