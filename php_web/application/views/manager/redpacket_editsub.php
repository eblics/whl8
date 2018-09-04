<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/redpacket_editsub.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>分级红包</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="130">红包名称：</td>
                        <td class="value" width="350"><?=$data->parentName?>
                        <input name="parentId" type="hidden" value="<?=$data->parentId?>" /></td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">分级红包额度：</td>
                        <td class="value" width="350">
                            <input class="input" id="amount" name="amount" maxlength="7" value="<?=$data->amount?>" valType="AMOUNT" msg="<font color=red>*</font>输入不正确"/>
                        </td>
                        <td class="tip">元</td>
                    </tr>
                    <tr>
                        <td class="name">分级红包数量：</td>
                        <td class="value" width="350">
                            <input class="input" id="num" name="num" maxlength="9" value="<?=$data->num?>" valType="NUMBER" msg="<font color=red>*</font>输入不正确" <?=($data->id>0)?'readonly="readonly" style="background:#ddd"':''?>/>
                        </td>
                        <td class="tip">个</td>
                    </tr>
                    <tr>
                        <td class="name">分级红包中奖概率：</td>
                        <td class="value" width="350">
                            <input class="input" id="probability" name="probability" value="<?=$data->probability?>" valType="PROBABILITY" msg="<font color=red>*</font>只能输入0-100之间，最多3位小数"/>
                        </td>
                        <td class="tip">%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><span id="btnSave" class="btn btn-blue noselect">保存</span>
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

