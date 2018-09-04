<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="/static/css/batch_order_add.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/batch_order_out_add.js"></script>
    <script type="text/javascript">
    var appid="<?=$appid?>";
    var appsecret="<?=$appsecret?>";
    var apiurl="<?=$apiurl?>";
    </script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">出库单增加</span>
            </div>
            <div class="h20"></div>
            <form>
                <table class="table-form">
                    <tr>
                        <td class="name" width="100">订单编号：</td>
                        <td class="value" width="350">
                            <input class="input" type="text" name="orderno" valType="NOTNULL" msg="<font color=red>*</font>『订单编号』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">产品编码：</td>
                        <td class="value">
                            <input class="input" type="text" name="productcode" valType="NOTNULL" msg="<font color=red>*</font>『产品编码』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">产品名称：</td>
                        <td class="value">
                            <input class="input" type="text" name="productname" valType="NOTNULL" msg="<font color=red>*</font>『产品名称』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">码类型：</td>
                        <td class="value">
                            <label for="codetype_0"><input id="codetype_0" name="codetype" type="radio" value="public" checked="checked"/> 明码</label>&nbsp;&nbsp;
                            <label for="codetype_1"><input id="codetype_1" name="codetype" type="radio" value="private" /> 暗码</label>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">销往客户编码：</td>
                        <td class="value">
                            <input class="input" type="text" name="saletocode" valType="NOTNULL" msg="<font color=red>*</font>『销往客户编码』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">客户名称：</td>
                        <td class="value">
                            <input class="input" type="text" name="saletoname" valType="NOTNULL" msg="<font color=red>*</font>『客户名称』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">销往区域的编码：</td>
                        <td class="value">
                            <input class="input" type="text" name="saletoagc" valType="NOTNULL" msg="<font color=red>*</font>『销往区域的编码』不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">出库时间：</td>
                        <td class="value">
                            <input class="input Wdate" type="text" name="time" valType="NOTNULL" msg="<font color=red>*</font>『出库时间』不能为空" style="background-position:98% 50%;"  onfocus="WdatePicker({isShowWeek:true})" />
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">相关码：</td>
                        <td class="value">
                            <input type="file" class="file-upload" name="codes" valType="NOTNULL" accept="text/plain" msg="<font color=red>*</font>请选择『相关码』文件"/>
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