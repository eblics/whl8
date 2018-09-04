<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/accumstrategy_bonus_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">累计策略大奖设置</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$accum_strtegy_id?>" />
                <table class="table-form">
                    <tr style="background:#eee;">
                        <td class="name">子策略类型</td>
                        <td class="value center">
                            <b style="color:#333">子策略名称</b>
                        </td>
                        <td class="value center" style="font-weight: bold;">可中次数</td>
                        <td class="tip center"><b style="color:#333">扫码次数范围匹配（扫码次数：针对具体活动进行累计）</b></td>
                        <td></td>
                    </tr>
                    <?php foreach ($bonus as $index => $bonusItem) {
                        $content = '
                        <tr class="trlist" style="background:#f5f5f5;">
                            <td class="name">
                                <select id="strategyType_'.$index.'" class="select" name="strategyType" edit-value="'.$bonusItem->strategyType.'">
                                    <option value="0">红包</option>
                                    <option value="2">乐券</option>
                                    <option value="3">积分</option>
                                </select>
                            </td>
                            <td class="value">
                                <select id="strategyId_'.$index.'" class="select" name="strategyId" edit-value="'.$bonusItem->strategyId.'"></select>
                            </td>
                            <td class="chance">
                                <input class="input center" style="width:30px;" id="chance_'.$index.'" name="chance" value="'.$bonusItem->chance.'" maxlength="2" valType="NUMBER" msg="<font color=red>*</font>请填写不小于0的整数" title="请填写大于0的整数"/>
                            </td>
                            <td class="tip">
                                <input class="input center ckval" style="width:50px;" id="start_'.$index.'" name="start" value="'.$bonusItem->start.'" maxlength="8" valType="POSINT" msg="<font color=red>*</font>请填写大于0的整数" title="请填写大于0的整数"/> ~ 
                                <input class="input center ckval" style="width:50px;" id="end_'.$index.'" name="end" value="'.$bonusItem->end.'" maxlength="8" valType="POSINT" msg="<font color=red>*</font>请填写大于0的整数" title="请填写大于0的整数"/> 次
                            </td>
                            <td class="op"><span class="btn btn-gray noselect del"> 删除 </span></td>
                        </tr>';
                        print $content;
                    } ?>
                    <tr class="addnew">
                        <td></td>
                        <td><span id="btnAdd" class="btn btn-gray noselect"> + 增加一条设置</span></td>
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

