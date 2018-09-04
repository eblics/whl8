<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/company.css?232" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<style type="text/css">
    .onebox{
        width:100%;
        height:30px;
        margin-top:100px;
        text-align:center;
        font-size:20px;
        color:#666;
    }
    .twobox{
        margin-top:20px;
        width:100%;
        font-size:40px;
        color:#333;
        text-align:center;
        letter-spacing:5px;
    }
    .buttonbox{
        margin-top:30px;
        width:130px;
        height:35px;
        letter-spacing:3px;
        background:#5A8FDD;
        line-height:35px;
        color:white;
        letter-spacing:5px;
        font-size:15px;
        text-align:center;
        margin-left: auto;
        margin-right:auto;
        -moz-user-select: none; 
        -webkit-user-select: none; 
        -ms-user-select: none; 
        -khtml-user-select: none; 
        user-select: none; 
    }
    .buttonbox:hover{
        cursor:pointer;
    }
</style>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <?php if($_SESSION['status'] == 4): ?>
        <div class="rightmain">
            <div class="onebox">感谢您的填写，我们会尽快审核，谢谢~</div>
            <div class="twobox">企业信息提交成功！</div>
            <!-- <div class="buttonbox">刷新</div> -->
        </div>
        <?php elseif($_SESSION['part'] == 1): ?>
        <div class="rightmain">
            <div class="onebox">您仅可以操作码模块！</div>
            <!-- <div class="twobox">请补全完企业信息，提交审核，使用完整的系统功能！</div> -->
            <!-- <div class="buttonbox">刷新</div> -->
        </div>
        <?php elseif($_SESSION['status'] == 5): ?>
        <div class="rightmain">
            <div class="onebox">企业预审核状态，仅可使用乐码功能，谢谢~</div>
            <div class="twobox">请补全完企业信息，提交审核，使用完整的系统功能！</div>
            <!-- <div class="buttonbox">刷新</div> -->
        </div>
    <?php elseif($_SESSION['status'] == 2): ?>
        <div class="rightmain">
            <div class="onebox">企业驳回审核状态</div>
            <div class="twobox">请重新修改企业信息，提交审核，使用完整的系统功能！</div>
            <!-- <div class="buttonbox">刷新</div> -->
        </div>
        <?php elseif($_SESSION['status'] == 0): ?>
            <div class="onebox">请补充完整企业信息和微信信息，谢谢~</div>
            <div class="twobox">菜单功能将在补充完信息并审核通过后可用！</div>
            <!-- <div class="buttonbox">刷新</div> -->
        <?php endif; ?>
    </div>
    <?php include 'footer.php';?>
</body>
</html>