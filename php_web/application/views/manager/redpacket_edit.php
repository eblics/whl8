<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/redpacket_edit.js"></script>
    <script type="text/javascript">var levelType=<?=$data->levelType?>;</script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view->title?>红包策略</span>
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
                        <td class="name">分级红包：</td>
                        <td class="value">
                            <span style="display:<?=$view->levelType0ShowEdit?>"><label for="levelType_0" style="display:<?=$view->levelType0Show?>"><input id="levelType_0" name="levelType" type="radio" value="0" <?=(int) $data->levelType===0?'checked':''?>/>否 &nbsp;&nbsp;</label></span>
                            <label for="levelType_1" style="display:<?=$view->levelType1Show?>"><input id="levelType_1" name="levelType" type="radio" value="1" <?=(int) $data->levelType===1?'checked':''?>/> 是</label>
                            <?=(int)$data->levelType===0&&$view->action=='edit'?'否':''?>
                            
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr class="levelType" style="display:<?=$view->levelTypeShow?>">
                        <td class="name">分级红包优先级：</td>
                        <td class="value">
                            <label for="priority_0"><input id="priority_0" name="priority" type="radio" value="0" <?=(int) $data->priority===0?'checked':''?>/> 随机</label> &nbsp;&nbsp;
                            <label for="priority_1"><input id="priority_1" name="priority" type="radio" value="1" <?=(int) $data->priority===1?'checked':''?>/> 金额从小到大</label> &nbsp;&nbsp;
                            <label for="priority_2"><input id="priority_2" name="priority" type="radio" value="2" <?=(int) $data->priority===2?'checked':''?>/> 金额从大到小</label>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr class="levelType_1" style="display:<?=$view->levelType0Show?>">
                        <td class="name">红包类型：</td>
                        <td class="value">
                            <label for="rpType_0"><input id="rpType_0" name="rpType" type="radio" value="0" <?=(int) $data->rpType===0?'checked':''?>/> 普通</label> &nbsp;&nbsp;
                            <label for="rpType_1"><input id="rpType_1" name="rpType" type="radio" value="1" <?=(int) $data->rpType===1?'checked':''?>/> 裂变</label>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr class="levelType_1" style="display:<?=$view->levelType0Show?>">
                        <td class="name">额度类型：</td>
                        <td class="value">
                            <label for="amtType_0"><input id="amtType_0" name="amtType" type="radio" value="0" <?=(int) $data->amtType===0?'checked':''?>/> 固定</label> &nbsp;&nbsp;
                            <label for="amtType_1"><input id="amtType_1" name="amtType" type="radio" value="1" <?=(int) $data->amtType===1?'checked':''?>/> 随机</label> &nbsp;&nbsp;
                            <?php if (isDev()): // 此功能暂停 - begin?>
                            <label for="amtType_2"><input id="amtType_2" name="amtType" type="radio" value="2" <?=(int) $data->amtType===2?'checked':''?>/> 指定</label>
                            <?php endif; // 此功能暂停 - end?>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr class="amtType_0" style="display:<?=$view->levelType0Show?><?=$view->amtType0Show?>">
                        <td class="name" width="100">红包额度：</td>
                        <td class="value" width="350">
                            <input class="input" id="amount" name="amount" maxlength="7" value="<?=$data->amount?>" valType="AMOUNT" msg="<font color=red>*</font>输入不正确"/>
                        </td>
                        <td class="tip">元</td>
                    </tr>
                    <tr class="amtType_1" style="display:<?=$view->amtType1Show?>">
                        <td class="name" width="100">红包最小额度：</td>
                        <td class="value" width="350">
                            <input class="input" id="minAmount" name="minAmount" maxlength="7" value="<?=$data->minAmount?>" valType="AMOUNT" msg="<font color=red>*</font>输入不正确"/>
                        </td>
                        <td class="tip">元</td>
                    </tr>
                    <tr class="amtType_1" style="display:<?=$view->amtType1Show?>">
                        <td class="name" width="100">红包最大额度：</td>
                        <td class="value" width="350">
                            <input class="input" id="maxAmount" name="maxAmount" maxlength="7" value="<?=$data->maxAmount?>" valType="AMOUNT" msg="<font color=red>*</font>输入不正确"/>
                        </td>
                        <td class="tip">元</td>
                    </tr>
                    <tr class="amtType_2" style="display:<?=$view->amtType2Show?>">
                        <td class="name" width="100">自定义红包：</td>
                        <td class="value" width="350">
                            <input class="input" id="ruleStr" name="ruleStr" maxlength="64" value="<?=$data->ruleStr?>" valType="RED_PACKET_CUSTOMIZE" msg="<font color=red>*</font>输入不正确"/>
                        </td>
                        <td class="tip">元</td>
                    </tr>
                    <tr class="levelType_1" style="display:<?=$view->levelType0Show?>">
                        <td class="name">上限类型：</td>
                        <td class="value">
                            <label for="limitType_0"><input id="limitType_0" name="limitType" type="radio" value="0" <?=(int) $data->limitType===0?'checked':''?>/> 数量</label> &nbsp;&nbsp;
                            <label for="limitType_1"><input id="limitType_1" name="limitType" type="radio" value="1" <?=(int) $data->limitType===1?'checked':''?>/> 金额</label>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr class="limitType_0" style="display:<?=$view->limitType0Show?>">
                        <td class="name" width="100">总数量：</td>
                        <td class="value" width="350">
                            <input class="input" id="totalNum" name="totalNum" value="<?=$data->totalNum?>" valType="NUMBER" msg="<font color=red>*</font>输入不正确" <?=($data->id>0)?'readonly="readonly" style="background:#ddd"':''?>/>
                        </td>
                        <td class="tip">个</td>
                    </tr>
                    <tr class="limitType_1" style="display:<?=$view->limitType1Show?>">
                        <td class="name" width="100">总金额：</td>
                        <td class="value" width="350">
                            <input class="input" id="totalAmount" name="totalAmount" value="<?=$data->totalAmount?>" valType="AMOUNT" msg="<font color=red>*</font>输入不正确" <?=($data->id>0)?'readonly="readonly" style="background:#ddd"':''?>/>
                        </td>
                        <td class="tip">元</td>
                    </tr>
                    <tr class="levelType_0" style="display:<?=$view->levelType0Show?>">
                        <td class="name" width="100">中奖概率：</td>
                        <td class="value" width="350">
                            <input class="input" id="probability" name="probability" value="<?=$data->probability?>" valType="PROBABILITY" msg="<font color=red>*</font>只能输入0-100之间，最多3位小数"/>
                        </td>
                        <td class="tip">%</td>
                    </tr>
                    <tr>
                        <td class="name">直接发放：</td>
                        <td class="value">
                            <!-- <input id="isDirect" name="isDirect" <?=$data->isDirect==1?'checked':''?> type="checkbox" value="<?=$data->isDirect?>"/> -->
                            <label for="isDirect_0"><input id="isDirect_0" name="isDirect" type="radio" value="0" <?=(int) $data->isDirect===0?'checked':''?>/> 否</label> &nbsp;&nbsp;
                            <label for="isDirect_1"><input id="isDirect_1" name="isDirect" type="radio" value="1" <?=(int) $data->isDirect===1?'checked':''?>/> 是</label>
                        </td>
                        <td class="tip">是否选择将1元以上的红包直接发放给用户</td>
                    </tr>
                    <tr style="display:<?=(int) $data->isDirect===0?'none':''?>">
                        <td class="name">合并余额发放：</td>
                        <td class="value">
                            <label for="withBalance_0"><input id="withBalance_0" name="withBalance" type="radio" value="0" <?=(int) $data->withBalance===0?'checked':''?>/> 否</label> &nbsp;&nbsp;
                            <label for="withBalance_1"><input id="withBalance_1" name="withBalance" type="radio" value="1" <?=(int) $data->withBalance===1?'checked':''?>/> 是</label>
                        </td>
                        <td class="tip">是否将每次扫码中得红包和用户帐户余额一并发放给用户</td>
                    </tr>
                    <tr style="display:<?=(int) $data->isDirect===0?'none':''?>">
                        <td class="name">发放方式：</td>
                        <td class="value">
                            <label for="payment_0"><input id="payment_0" name="payment" type="radio" value="0" <?=(int) $data->payment===0?'checked':''?>/> 微信红包</label> &nbsp;&nbsp;
                            <label for="payment_1"><input id="payment_1" name="payment" type="radio" value="1" <?=(int) $data->payment===1?'checked':''?>/> 企业付款</label>
                        </td>
                        <td class="tip">请确认微信支付平台已开通“微信红包”或“企业付款”产品<br /><font style="color:red">提醒：微信支付接口对单个用户每日接收红包或者企业付款，有默认10次的上限（最大上限100次，可至微信支付商户平台进行修改）。<br/>　　　若用户接收红包或企业付款次数超限，将存入个人中心-我的红包余额。</font></td>
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

