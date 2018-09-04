<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/mixstrategy_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>组合策略</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="100">名称：</td>
                        <td class="value" width="350">
                            <input class="input" id="name" name="name" value="<?=$data->name?>" valType="PRONAME" msg="<font color=red>*</font>请正确输入『名称』"/>
                        </td>
                        <td class="tip">4-32个字符</td>
                    </tr>
                    <tr>
                        <td class="name" width="100">启用失效过滤：</td>
                        <td class="value" width="350">
                            <input id="hasEnabled" name="hasEnabled" type="checkbox" <?=$data->hasEnabled==1?'checked="checked"':''?> />
                        </td>
                        <td class="tip">如果选中，则奖品已发完的子策略不进行匹配</td>
                    </tr>
                    <tr style="background:#eee;">
                        <td class="name">子策略类型</td>
                        <td class="value center">
                            <b style="color:#333">子策略名称</b>
                        </td>
                        <td class="tip center"><b style="color:#333">子策略权重</b></td>
                        <td></td>
                    </tr>
                    <?php
                    foreach ($data->sonlist as $k => $v) {
                        echo '
                        <tr class="trlist" style="background:#f5f5f5;">
                            <td class="name">
                                <select id="strategyType_'.$k.'" class="select" name="strategyType" edit-value="'.$v->strategyType.'">
                                    <option value="0">红包</option>
                                    <option value="2">乐券</option>
                                    <option value="3">积分</option>
                                </select>
                            </td>
                            <td class="value">
                                <select id="strategyId_'.$k.'" class="select" name="strategyId" edit-value="'.$v->strategyId.'"></select>
                            </td>
                            <td class="tip"><input class="input center" style="width:100px;" id="weight_'.$k.'" name="weight" value="'.$v->weight.'" valType="NUMBER" msg="<font color=red>*</font>请填写大于0的整数，若为0将不匹配这条子策略" title="请填写大于0的整数，若为0将不匹配这条子策略"/> 填写整数</td>
                            <td class="op"><span class="btn btn-gray noselect del"> 删除 </span></td>
                        </tr>
                        ';
                    } 
                    ?>
                    <tr class="addnew">
                        <td></td>
                        <td><span id="btnAdd" class="btn btn-gray noselect"> + 增加一条策略</span></td>
                        <td></td>
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

