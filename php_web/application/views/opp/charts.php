<?php include 'common/header.php' ?>
<style type="text/css">
    #div1{
        width: 35px;
        height: 23px;
        border-radius: 12px;
        position: relative;
    }
    #div2{
        width: 20px;
        height: 20px;
        border-radius: 10px;
        position: absolute;
        background: white;
        box-shadow: 0px 2px 2px rgba(0,0,0,0.4);
    }
    .open1{
        background: rgba(0,184,0,0.8);
    }
    .open2{
        top: 2px;
        right: 1px;
    }
    .close1{
        background: rgba(255,255,255,0.4);
        border:1px solid rgba(0,0,0,0.15);
        border-left: transparent;
    }
    .close2{
        left: 0px;
        top: 0px;
        border:1px solid rgba(0,0,0,0.1);
    }
</style>
<script type="text/javascript" src="/static/js/charts/charts.js"></script>
</head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <?php include 'charts_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft">开通管理</span></div>
        <div class="h20"></div>
        <div class="content">
            <table class="table-form">
                <tr><td class="name" width="150">选择企业：</td>
                    <td class="value" width="350">
                        <select id="chose" class="select" name="strategyId" edit-value="">
                            <option>请选择......</option>
                        </select>
                    </td>
                    <td class="tip">
                    下拉选择需要操作的企业
                    </td>
                </tr>
                <tr><td class="name" width="150">手机号码：</td>
                    <td class="value" width="350">
                    </td>
                    <td class="tip">
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php include 'common/footer.php';?>