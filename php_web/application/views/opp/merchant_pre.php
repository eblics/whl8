<?php include 'common/header.php'; ?></head>
<body> <?php include 'common/menus.php';?>
<div class="main">
<?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path"><span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <form type="validate">
            <table class="table-form">
                <tr>
                    <td class="name" width="150">预审企业名称：</td>
                    <td class="value" width="350">
                        <input type="text" class="input" id="userName" name="userName" value="<?=$merchant->name?>" readonly="readonly" disabled="disabled">
                    </td>
                    <td class="tip">预审核的企业名称</td>
                </tr>
                <tr>
                    <td class="name" width="150">企业注册时间：</td>
                    <td class="value" width="350">
                        <input type="text" class="input" name="createTime" id="createTime" value="<?=date('Y-m-d H:i:s',$merchant->createTime)?>" disabled="disabled">
                    </td>
                    <td class="tip"></td>
                </tr>
                <tr>
                    <td class="name" width="150">码版本选择：</td>
                    <td class="value" width="350">
                        <select id="codeVersion" class="select" name="codeVersion" edit-value="<?=$merchant->codeVersion?>">
                            <option value="">请选择......</option>
                            <?php foreach ($codes as $key => $value): ?>
                            <option <?=$merchant->codeVersion==$value->versionNum?'selected':'' ?> value="<?=$value->versionNum?>"><?=$value->versionNum?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="tip">0,不加密版本;1以上为加密版本</td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="hidden" id="mid" name="mid" value="<?=$id?>">
                        <input style="float:left;" id="cancle_sub" class="btn btn-gray" type="button" value="放弃预审核">
                        <input style="float:right;" id="sub" class="btn btn-blue" type="button" value="通过预审核">
                    </td>
                    <td>
                        
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript" src="/static/js/merchant/pre.js"></script>
<?php include 'common/footer.php';?>