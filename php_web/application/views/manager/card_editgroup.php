<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/card_editgroup.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$title?>券组</span>
            </div>
            <div class="h20"></div>
            <form>
                <table class="table-form">
                    <!-- 此行勿删,解决单input type=text 回车自动提交的问题 -->
                    <input type="text" style="display: none;" />
                    <tr>
                        <td class="name" width="100">券组名称：</td>
                        <td class="value" width="350">
                            <input class="input" name="title" value="<?=$data->title?>" maxlength="8" valType="LNAME" msg="<font color=red>*</font>请正确输入『券组名称』" />
                        </td>
                        <td class="tip">请控制在4-8个字符之间</td>
                    </tr>
                    <tr>
                        <td class="name">券组图片：</td>
                        <td class="value">
                            <input class="js-upload" id="imgUrl" type="file" name="imgUrl" edit-value="<?=$data->imgUrl?>" />
                        </td>
                        <td class="tip">图片大小不得超过500k</td>
                    </tr>
                    <tr>
                        <td class="name" width="100">乐券优先级：</td>
                        <td class="value">
                            <label for="priority_0"><input id="priority_0" name="priority" type="radio" value="0" <?=(int) $data->priority===0?'checked="checked"':''?>/> 随机</label> &nbsp;&nbsp;
                            <label for="priority_1"><input id="priority_1" name="priority" type="radio" value="1" <?=(int) $data->priority===1?'checked="checked"':''?>/> 按中奖概率从小到大</label> &nbsp;&nbsp;
                            <label for="priority_2"><input id="priority_2" name="priority" type="radio" value="2" <?=(int) $data->priority===2?'checked="checked"':''?>/> 按中奖概率从大到小</label></td>
                        <td class="tip"></td>
                    </tr>

                    <tr>
                        <td class="name" width="100">券组奖励：</td>
                        <td class="value">
                            <?php
                                if ($data->hasGroupBonus === 0 || $data->hasGroupBonus === '0') {
                                    print '<input name="hasGroupBonus" type="checkbox" />';
                                } else {
                                    print '<input name="hasGroupBonus" type="checkbox" checked="checked" />';
                                }
                            ?>
                        </td>
                        <td class="tip">该乐券是否有卡组奖励</td>
                    </tr>
                    <tr class="bonus-set">
                        <td class="name" width="100">奖励类型：</td>
                        <td>
                            <select name="bonusType" class="input" style="width: 100%">
                                <option value="0" selected="<?=$data->bonusType === 0 ? 'selected' : ''?>">积分</option>
                            </select>
                        </td>
                        <td class="tip">选择奖励类型</td>
                    </tr>
                    <tr class="bonus-set">
                        <td class="name" width="100">奖励数量：</td>
                        <td class="value">
                            <input name="bonusQuantity" type="text" class="input" valType="NUMBER" value="<?=$data->bonusQuantity?>"
                                msg="<font color=red>*</font>『奖励数量』只能填写整数"/>
                        </td>
                        <td class="tip">奖励数量，整数类型</td>
                    </tr>
                    <script type="text/javascript">
                        $('input[name=hasGroupBonus]').click(function() {
                            if ($(this).prop('checked')) {
                                $('.bonus-set').show();
                            } else {
                                $('.bonus-set').hide();
                            }
                        });
                        if ($('input[name=hasGroupBonus]').prop('checked')) {
                            $('.bonus-set').show();
                        } else {
                            $('.bonus-set').hide();
                        }
                    </script>

                    <tr>
                        <td class="name">券组简介：</td>
                        <td class="value"><textarea class="textarea" name="description" maxlength="600" rows="8" valType="NOTNULL" 
                            msg="<font color=red>*</font>『券组描述』不能为空" ><?=$data->description?></textarea></td>
                        <td class="tip">券组的文字简介，控制在600字内</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><span id="btnSave" class="btn btn-blue noselect">保存</span></td>
                        <td></td>
                    </tr>
                    <input type="hidden" name="id" value="<?=$data->id?>" />
                </table>
            </form>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>

</html>

