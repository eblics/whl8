<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/point_editsub.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>分级积分</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="130">策略名称：</td>
                        <td class="value" width="350"><?=$data->parentName?>
                        <input type="hidden" name="parentName" value="<?=$data->parentName?>"/> 
                        <input name="parentId" type="hidden" value="<?=$data->parentId?>" /></td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">分级积分额度：</td>
                        <td class="value" width="350">
                            <input class="input" id="amount" name="amount" maxlength="7" value="<?=$data->amount?>" valType="NUMBER" msg="<font color=red>*</font>输入不正确"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name">数量：</td>
                        <td class="value" width="350">
                            <input class="input" id="num" name="num" maxlength="9" value="<?=$data->num?>" valType="NUMBER" msg="<font color=red>*</font>输入不正确" <?=($data->id>0)?'readonly="readonly" style="background:#ddd"':''?>/>
                        </td>
                        <td class="tip">个</td>
                    </tr>
                    <tr>
                        <td class="name">中奖概率：</td>
                        <td class="value" width="350">
                            <input class="input" id="probability" name="probability" value="<?=$data->probability?>" valType="PROBABILITY" msg="<font color=red>*</font>只能输入0-100之间，最多3位小数"/>
                        </td>
                        <td class="tip">%</td>
                    </tr>
                    <tr>
                        <td class="name">积分种类：</td>
                        <td class="value" width="350">
                            <select id="third_number" name="third_number" class="input" style="width: 100%">
                                <?php if (isset($data->third_number)):?>
                                    <?php if ($data->third_number == 0):?>
                                        <option value="0" selected="selected">积分</option>
                                    <?php else:?>
                                        <option value="0">积分</option>
                                    <?php endif;?>
                                    <?php if ($data->third_number == 1):?>
                                        <option value="1" selected="selected">人人店积分</option>
                                    <?php else:?>
                                        <option value="1">人人店积分</option>
                                    <?php endif;?>
                                <?php else:?>
                                    <option value="0" selected="selected">红码积分</option>
                                    <option value="1">人人店积分</option>
                                <?php endif;?>
                            </select>
                        </td>
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

