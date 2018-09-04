<?php include 'common/header.php' ?>
<link rel="stylesheet" type="text/css" href="/static/css/module.css">
<script type="text/javascript" src="/static/js/work/work_module.js"></script>
</head>
<body><?php include 'common/menus.php';?>
<div class="main mlock">
    <?php include 'work_left.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <!-- <div class="frame">
                <div class="frame-block frameChose">产品问题
                </div>
                <div class="frame-block">非产品类问题
                </div>
            </div>
            <div class="frame-main">
                <table>
                    <tr></tr>
                </table>
            </div> -->
            <div class="tbtitle">
                <div class="tbt1">添加模块↓↓</div>
                <div class="tbt2">已有模块↓↓</div>
            </div>
            <div class="tborder">

                <div class="tb1">
                    <div class="filling"></div>
                    <input type="text" name="" maxlength="10" class="input nameinput">
                    <div id="btnAdd" class="btn btn-blue">添加</div>
                </div>
                <div class="tb2">
                    <div class="tb2img"></div>
                </div>
                <div class="tb3">
                    <div class="tb3border">
                        <?php 
                            foreach ($data as $key => $value) {
                                echo '<div class="littbar" value="'.$value->id.'" title="双击删除">'.$value->name.'</div>';
                            }
                         ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php include 'common/footer.php';?>