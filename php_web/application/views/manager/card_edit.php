<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/card_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">乐券<?=$title?></span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" value="<?=$data->id?>" />
                <input type="hidden" class="fid" value="<?=$fid?>" />
                <table class="table-form">
                <tr>
                    <td class="name" width="100">乐券名称：</td><td class="value" width="350"><input class="input" name="title" value="<?=$data->title?>" maxlength="15" valType="LNAME" msg="<font color=red>*</font>请正确输入『乐券名称』" /></td><td class="tip">请控制在4-15个字符之间</td>
                </tr>
                <tr>
                    <td class="name">乐券图片：</td><td class="value"><input class="js-upload" id="imgUrl" type="file" name="imgUrl" edit-value="<?=$data->imgUrl?>" /></td><td class="tip">图片大小不得超过500k</td>
                </tr>
                <tr>
                        <td class="name" width="100">乐券数量：</td>
                        <td class="value" width="350">
                            <input class="input" id="totalNum" name="totalNum" value="<?=$data->totalNum?>" valType="NUMBER" msg="<font color=red>*</font>输入不正确" <?=($data->id>0)?'readonly="readonly" style="background:#ddd"':''?> />
                        </td>
                        <td class="tip">张</td>
                </tr>
                <tr>
                    <td class="name">中奖概率：</td><td class="value" width="350"><input class="input" name="probability" value="<?=$data->probability?>" valType="PROBABILITY" msg="<font color=red>*</font>只能输入0-100之间，最多3位小数" /></td><td class="tip">%</td>
                </tr>

                <tr >
                    <td class="name">是否可转移：</td>
                    <td class="value" width="350">
                        <?php if (isset($data->allowTransfer) && $data->allowTransfer == 1): ?>
                            <input id="allowTransfer" name="allowTransfer" type="checkbox" checked="checked"  />
                        <?php else:?>
                            <input id="allowTransfer" name="allowTransfer" type="checkbox"/>
                        <?php endif;?>
                    </td>
                    <td class="tip">设置乐券是否可以转移</td>
                </tr>

                <!-- ===================== Added by shizq start ===================== -->
                <?php if (isset($data->cardType) && $data->cardType == 1):?>
                    <!-- 第三方乐券的编辑 -->
                <tr class="third-party-check">
                <?php elseif (isset($data->create)):?>
                    <!-- 添加乐券 -->
                <tr class="third-party-check">
                <?php else:?>
                    <!-- 非第三方乐券的编辑 -->
                <tr class="third-party-check" style="display:none">
                <?php endif; ?>

                    <td class="name">第三方：</td>
                    <td class="value">
                        <?php if (isset($data->cardType) && $data->cardType == 1):?>
                        <!-- 如果是第三方乐券，那么不允许修改为非第三方乐券 -->
                        <input id="thirdParty" name="thirdParty" onclick="return false" readonly="readonly" checked="true" type="checkbox" />
                        <?php else:?>
                        <input id="thirdParty" name="thirdParty" type="checkbox" />
                        <?php endif; ?>
                    </td>
                    <td class="tip">保存后不可修改</td>
                </tr>

                <?php if (isset($data->cardType) && $data->cardType == 1):?>
                <tr class="hls-thirdpary-addon">
                <?php else:?>
                <tr class="hls-thirdpary-addon" style="display:none">
                <?php endif; ?>
                    <td class="name">商户平台：</td>
                    <td class="value" width="350">
                        <select id="cardType" name="cardType" class="input" style="width:100%">
                            <option value="1">有赞</option>
                        </select>
                    </td>
                    <td class="tip"></td>
                </tr>
                <?php if (isset($data->cardType) && $data->cardType == 1):?>
                <tr class="hls-thirdpary-addon">
                <?php else:?>
                <tr class="hls-thirdpary-addon" style="display:none">
                <?php endif; ?>
                    <td class="name">优惠码券：</td>
                    <td class="value" width="350">
                        <select id="couponGroupId" class="input" style="width:100%" name="couponGroupId"></select>
                    </td>
                    <td class="tip"></td>
                </tr>
                <script type="text/javascript">
                    var thirdParty = <?=(isset($data->cardType) && $data->cardType)? $data->cardType: 0?>,
                        couponGroupId = <?=empty($data->couponGroupId)? 'null': $data->couponGroupId?>;
                    $('#thirdParty').change(function() {
                        isThirdParty = $(this)[0].checked;
                        var addon = $('.hls-thirdpary-addon');
                        var mallGoodsSelect = $('.input-link-point');
                        if (isThirdParty) {
                            $('#linkPoint').prop('checked', false);
                            thirdParty = 1;
                            loadCouponGroupIds(thirdParty);
                            addon.show();
                            mallGoodsSelect.hide();
                        } else {
                            thirdParty = 0;
                            addon.hide();
                        }
                    });

                    function loadCouponGroupIds(cardType) {
                        $.get('/card/coupons', {card_type: cardType}, function(resp) {
                            if (! resp.errcode) {
                                $('#couponGroupId').empty();
                                for (var row, i = 0; i < resp.data.length; i++) {
                                    if (couponGroupId == resp.data[i].group_id) {
                                        row = '<option selected value="' + resp.data[i].group_id + '">' + resp.data[i].title + '</option>';
                                    } else {
                                        row = '<option value="' + resp.data[i].group_id + '">' + resp.data[i].title + '</option>';
                                    }
                                    $('#couponGroupId').append($(row));
                                }
                            } else {
                                common.alert(resp.errmsg + '！');
                            }
                        }, 'json').error(function(error) {
                            common.alert('无法连接服务器！');
                        });
                    }
                    if (thirdParty === 1 || thirdParty === '1') {
                        loadCouponGroupIds(thirdParty);
                    }
                </script>
                <!-- ===================== Added by shizq end  =====================  -->

                <!-- Added by shizq - begin -->
                <?php if (isset($data->cardType) && $data->cardType == 2):?>
                    <!-- 第三方乐券的编辑 -->
                    <tr>
                <?php elseif (isset($data->create)):?>
                    <!-- 添加乐券 -->
                    <tr>
                <?php else:?>
                    <!-- 非第三方乐券的编辑 -->
                    <tr style="display:none">
                <?php endif;?>
                    <td class="name" width="100">关联积分：</td>
                    <td class="value" width="350">
                        <?php if (isset($data->cardType) && $data->cardType == 2):?>
                            <input id="linkPoint" name="linkPoint" type="checkbox"  onclick="return false" readonly="readonly" checked="true" />
                        <?php else:?>
                            <input id="linkPoint" name="linkPoint" type="checkbox" />
                        <?php endif;?>
                    </td>
                    <td class="tip">关联积分,保存后不可修改</td>
                </tr>
                <tr class="input-link-point">
                    <td class="name" width="100">积分数量：</td>
                    <td class="value" width="350">
                        <?php if (isset($data->create)):?>
                            <input class="input" id="pointQuantity" name="pointQuantity" type="text" />
                        <?php else:?>
                            <input class="input" id="pointQuantity" name="pointQuantity" type="text" value="<?=$data->pointQuantity?>" />
                        <?php endif;?>
                        
                    </td>
                    <td class="tip">请填写积分数量</td>
                </tr>
                <script type="text/javascript">
                    $('#linkPoint').change(function() {
                        if ($(this)[0].checked) {
                            $('.hls-thirdpary-addon').hide();
                            $('.input-link-point').show();
                            $('#thirdParty').prop('checked', false);
                        } else {
                            $('.input-link-point').hide();
                        }
                    });

                    if ($('#linkPoint').prop('checked')) {
                        $('.hls-thirdpary-addon').hide();
                        $('.input-link-point').show();
                    } else {
                        $('.input-link-point').hide();
                    }
                </script>
                <!-- Added by shizq - end -->

                <tr>
                    <td class="name">券组选择：</td>
                    <td class="value">
                        <select id="select" class="select" name="parentId" edit-value="">
                            <?php 
                            $c = $data->cgroup;
                            foreach ($c as $value): ?>
                                <option value="<?=$value->id?>" <?=$data->parentId == $value->id?'selected=':''?> ><?=$value->title ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                    </td>
                </tr>
                <tr>
                    <td class="name">乐券简介：</td><td class="value"><textarea class="textarea" name="description" maxlength="600" rows="8" valType="NOTNULL" msg="<font color=red>*</font>『乐券描述』不能为空" ><?=$data->description?></textarea></td><td class="tip">乐券的文字简介，控制在600字内</td>
                </tr>
                <tr>
                    <td></td><td><span id="btnSave" class="btn btn-blue noselect">保存</span></td><td></td>
                </tr>
            </table>
            </form>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>

</html>

