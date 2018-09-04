<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/batch_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">乐码<?=$view->title?></span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="100">批次编号：</td>
                        <td class="value" width="350">
                            <input class="input" id="batchNo" name="batchNo" maxlength="30" value="<?=$data->batchNo?>" valType="PRONAME" msg="<font color=red>*</font>请正确输入『批号』"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">数量：</td>
                        <td class="value">
                            <input class="input" id="len" name="len" <?=$view->len_disabled?> value="<?=$data->len?>" maxlength ="8" valType="LECODE" msg="<font color=red>*</font>数量 只能为1-10000000的整数"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">过期时间：</td>
                        <td class="value">
                            <input class="input Wdate" id="expireTime" name="expireTime" value="<?=date('Y-m-d',$data->expireTime)?>" valType="NOTNULL" msg="<font color=red>*</font>『过期时间』不能为空" style="background-position:98% 50%;"  onfocus="WdatePicker({isShowWeek:true})" />
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">包含明码：</td>
                        <td class="value"><input id="ifPubCode" name="ifPubCode" type="checkbox" value="<?=$data->ifPubCode?>"/></td>
                    </tr>
                    <tr>
                        <td class="name">关联产品：</td>
                        <td class="value"><input id="widthProduct"  name="widthProduct" type="checkbox"  <?=$view->category_check?> /></td>
                    </tr>
                    <tr class="product" style="display:<?=$view->category_display?>">
                        <td class="name">产品分类：</td>
                        <td class="value">
                            <select id="categoryId" class="select" name="categoryId" edit-value="<?=$data->categoryId?>"></select>
                        </td>
                        <td><label for="onlyCategory"><input id="onlyCategory" name="onlyCategory" type="checkbox" <?=$view->product_check?> /> 仅关联分类</label></td>
                    </tr>
                    <tr class="product product-tr" style="display:<?=$view->product_display?>">
                        <td class="name">产品列表：</td>
                        <td class="value">
                            <select id="productId" class="select" name="productId" edit-value="<?=$data->productId?>"></select>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <span id="btnSave" class="btn btn-blue noselect">保存</span>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
     
</html>

