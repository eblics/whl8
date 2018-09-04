<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="/static/css/multi-select.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="/static/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/shop_tag_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">标签设置</span>
            </div>
            <div class="h20"></div>
            <form type="validate">
                <table class="table-form">
                    <tr>
                        <td class="name" width="150">标签名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="name" name="name" value="<?=$name?>" valType="NOTNULL" msg="<font color=red>*</font>『标签名称』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">关联门店：</td>
                        <td>
                            <select id="shopIds" multiple="multiple" style="display:none">
                              <?=$shopHtml?>
                            </select>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" data-id="<?=$id?>" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>