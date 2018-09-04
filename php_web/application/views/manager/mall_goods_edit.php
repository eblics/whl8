<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="/static/css/goods.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="/static/libs/UEditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/static/libs/UEditor/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="/static/libs/UEditor/lang/zh-cn/zh-cn.js"></script>
    <script type="text/javascript">
        var httpval = "<?=$httpval?>";
    </script>
    <script type="text/javascript" src="/static/js/mall_goods_edit.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft"><?=$view['title']?>商品</span>
            </div>
            <div class="h20"></div>
            <form>
                <input type="hidden" name="id" id="id" value="<?=$view['data']->id?>" />
                <table class="table-form">
                    <tr>
                        <td class="name" width="150">商品名称：</td>
                        <td class="value" width="450">
                            <input class="input" id="goodsName" name="goodsName" minlength="4" maxlength="18" valType="GOODNAME" value="<?=$view['data']->goodsName?>" msg="<font color=red>*</font>名称填写不正确 4-10个字符长度"/>
                        </td>
                        <td class="tip">4-10个字符</td> </tr>
                    <tr>
                        <td class="name" width="150">所属分类：</td>
                        <td class="value" width="450">
                            <select id="categoryId" class="select" name="categoryId" edit-value="<?=isset($view['data']->id)?$view['data']->categoryId:''?>"></select>
                        </td>
                        <td class="tip">选择商品所属分类</td></tr>

                    
                    <!-- Added by shizq - begin -->
                    <!--  乐券兑换已停用 <tr>
                        <td class="name" width="150">兑换类型：</td>
                        <td class="value" width="450">
                            <?php if (isset($view['data']->exchangeType) && $view['data']->exchangeType == 1) {
                                print '<label><input id="goodsTypePoint" type="radio" value="0" name="exchangeType"> 积分兑换</label>　';
                                print '<label><input id="goodsTypeCard" type="radio" value="1" checked="checked" name="exchangeType"> 乐券兑换</label>';
                            } else {
                                print '<label><input id="goodsTypePoint" type="radio" value="0" checked="checked" name="exchangeType"> 积分兑换</label>　';
                                print '<label><input id="goodsTypeCard" type="radio" value="1" name="exchangeType"> 乐券兑换</label>　';
                            } ?>
                        </td>
                        <td class="tip">选择兑换类型，保存后不可修改</td>
                    </tr> -->
                    <tr class="viral-option">
                        <td class="name" width="150">虚拟商品：</td>
                        <td class="value" width="450">
                            <?php
                                if (isset($view['data']->isViral) && $view['data']->isViral == 1) {
                                    print '<input id="viralGoods" type="checkbox" checked="checked" name="viralGoods">';
                                } else {
                                    print '<input id="viralGoods" type="checkbox" name="viralGoods">';
                                }
                            ?>
                        </td>
                        <td class="tip">选择是否是虚拟商品，保存后不可修改</td>
                    </tr>

                    <tr class="viral-platform-option" style="display: none;">
                        <td class="name" width="150">需要确认订单：</td>
                        <td class="value" width="450">
                            <?php
                                if (isset($view['data']->createOrder) && $view['data']->createOrder == 1) {
                                    print '<input id="createOrder" type="checkbox" checked="checked" name="createOrder">';
                                } else {
                                    print '<input id="createOrder" type="checkbox" name="createOrder">';
                                }
                            ?>
                        </td>
                        <td class="tip">选择是否需要生成订单</td>
                    </tr>

                    <tr class="viral-platform-option" style="display:none">
                        <td class="name">虚拟商品平台：</td>
                        <td class="value" width="350">
                            <select id="viralPlatform" class="input select" name="viralPlatform">
                                <option value="-1">--请选择虚拟商品发放平台--</option>
                                <?php if (isset($view['data']->viralPlatform)): ?>
                                    <?php if ($view['data']->viralPlatform == 0):?>
                                        <option selected="selected" value="0">微信红包</option>
                                        <option value="1">微信企业付款</option>
                                    <?php elseif ($view['data']->viralPlatform == 1):?>
                                        <option value="0">微信红包</option>
                                        <option selected="selected" value="1">微信企业付款</option>
                                    <?php endif;?>
                                <?php else:?>
                                    <option value="0">微信红包</option>
                                    <option value="1">微信企业付款</option>
                                <?php endif;?>
                                
                            </select>
                        </td>
                        <td class="tip">选择虚拟商品所属平台，保存后不可修改</td>
                    </tr>

                    <tr class="viral-platform-option" style="display:none">
                    <td class="name" width="150">数量：</td>
                        <td class="value" width="450">
                            <input class="input" id="viralAmount" name="viralAmount" value="<?=isset($view['data']->viralAmount) ? $view['data']->viralAmount * 0.01 : 0 ?>" />
                        </td>
                        <td class="tip">数量，如果是金额，则单位为元，保存后不可修改</td>
                    </tr>
                    <!-- Added by shizq - end -->

                    <tr class="hls-price">
                        <td class="name" width="150">原价：</td>
                        <td class="value" width="450">
                            <input class="input" id="oPrice" value="<?=$view['data']->oPrice?>" name="oPrice" valType="NUMBER" msg="<font color=red>*</font>允许的积分格式为整数"/>
                        </td>
                        <td class="tip">积分</td>
                    </tr>
                    <tr class="hls-price">
                        <td class="name" width="150">现价：</td>
                        <td class="value" width="450">
                            <input class="input" id="price" value="<?=$view['data']->price?>" name="price" value="" maxlength="32" valType="NUMBER" msg="<font color=red>*</font>允许的积分格式为整数"/>
                        </td>
                        <td class="tip">积分</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">商品图片：</td>
                        <td class="value imgclass" width="450">
                            <!-- <div class="img" title="单击删除"><img src="http://imgqn.koudaitong.com/upload_files/2015/07/02/FtY5NjbT4JeUMfrE27EUAVjget04.jpg%21580x580.jpg"></div>
                            <div class="img" title="单击删除"><img src="http://imgqn.koudaitong.com/upload_files/2015/07/02/FtY5NjbT4JeUMfrE27EUAVjget04.jpg%21580x580.jpg"></div>
                            <div class="img" title="单击删除"><img src="http://imgqn.koudaitong.com/upload_files/2015/07/02/FtY5NjbT4JeUMfrE27EUAVjget04.jpg%21580x580.jpg"></div>
                            <div class="img" title="单击删除"><img src="http://imgqn.koudaitong.com/upload_files/2015/07/02/FtY5NjbT4JeUMfrE27EUAVjget04.jpg%21580x580.jpg"></div> -->
                            <div class="addImg" val="1" id="uploadImg1View"></div>
                            <div class="addImgUp" style="display:none;">
                                <input class="js-upload" type="file" edit-value="" name="uploadImg1" id="uploadImg1">
                            </div>
                            <div class="addImg" val="2" id="uploadImg2View"></div>
                            <div class="addImgUp" style="display:none;">
                                <input class="js-upload" type="file" edit-value="" name="uploadImg2" id="uploadImg2">
                            </div>
                            <div class="addImg" val="3" id="uploadImg3View"></div>
                            <div class="addImgUp" style="display:none;">
                                <input class="js-upload" type="file" edit-value="" name="uploadImg3" id="uploadImg3">
                            </div>
                            <div class="addImg" val="4" id="uploadImg4View"></div>
                            <div class="addImgUp" style="display:none;">
                                <input class="js-upload" type="file" edit-value="" name="uploadImg4" id="uploadImg4">
                            </div>
                            <div class="addImg" val="5" id="uploadImg5View"></div>
                            <div class="addImgUp" style="display:none;">
                                <input class="js-upload" type="file" edit-value="" name="uploadImg5" id="uploadImg5">
                            </div>
                            
                        </td>
                        <td class="tip">最多允许上传五张商品图</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">描述：</td>
                        <td class="value" width="450">
                            <!-- <textarea class="textarea" id="description" msg="<font color=red>*</font>『描述』不能为空" maxlength="2000" valtype="NOTNULL" maxlength="1000" rows="8" name="description"><?=$view['data']->description?></textarea> -->

                            <script type="text/plain" id="description" name="description"><?=$view['data']->description?></script>
                        </td>
                        <td class="tip">建议控制在200字内 太多字符手机端展示会不友好</td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td>
                            <span style="float:right;" id="btnSave" class="btn btn-blue noselect">保存信息</span>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </form>

            <!-- Added by shizq - begin-->
            <script type="text/javascript">
                // if (!$('#goodsTypePoint').prop('checked')) {
                //     $('.hls-price').hide();
                // }
                // if ($('#viralGoods').prop('checked')) {
                //     $('.viral-platform-option').show();
                // }
                // $('#goodsTypePoint, #goodsTypeCard').change(function() {
                //     $('#viralGoods')[0].checked = false;
                //     $('.viral-platform-option').hide();
                //     if (!$('#goodsTypePoint').prop('checked')) {
                //         $('.hls-price').hide();
                //         $('#oPrice,#price').poshytip('hide');
                //     } else {
                //         $('.hls-price').show();
                //     }
                // });
                $('#viralGoods').change(function() {
                    if ($('#viralGoods')[0].checked) {
                        $('.viral-platform-option').show();
                    } else {
                        $('.viral-platform-option').hide();
                    }
                });

                if (<?=$view['isEdit']?>) {
                    $('#goodsTypePoint, #goodsTypeCard, #viralGoods').click(function() {
                        return false;
                    });
                    $('#goodsTypePoint, #goodsTypeCard, #viralGoods').prop('disabled', true);
                    $('#viralPlatform').prop('disabled', true);
                    $('#viralAmount').prop('disabled', true);
                }
            </script>
            <!-- Added by shizq - end -->
        </div>
    </div>
    <ul class="hover-panel" style="display:none;">
        <li title="修改" id="thisedit"><i class="iconfont">&#xe602;</i></li>
        <li title="删除" id="thisdel" class="last"><i class="iconfont">&#xe603;</i></li>
    </ul>
    <?php include 'footer.php';?>
</body>
</html>