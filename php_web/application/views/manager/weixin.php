<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/company.css?232" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/weixin.js"></script>
</head>
<body>
<?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">供应链微信信息 （<?=$res->wxAuthStatus_shop == 1?'<font color=green>已授权：'.$res->wxName_shop.'</font>':'<font color=red>未授权</font>';?>）</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <!-- <tr>
                        <td class="name" width="150">公众号名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxName_shop" name="wxName_shop" valType="required" value="<?=$res->wxName_shop?>" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 名称</td>
                    </tr>
                     <tr>
                        <td class="name" width="150">原始ID：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxYsId_shop" name="wxYsId_shop" valType="required" value="<?=$res->wxYsId_shop?>" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 原始ID （例如：gh_c3343450a8）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">AppId：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxAppId_shop" name="wxAppId_shop" valType="required" value="<?=$res->wxAppId_shop?>" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppId（应用ID）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">AppSecret：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxAppSecret_shop" name="wxAppSecret_shop" value="<?=$res->wxAppSecret_shop?>" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppSecret（应用密钥）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">二维码：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->wxQrcodeUrl_shop?>" name="wxQrcodeUrl_shop" id="wxQrcodeUrl_shop"  msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 二维码 （不大于500K）</td>
                    </tr> -->
                    <tr>
                        <td class="name" width="150">微信支付商户号：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxMchId_shop" name="wxMchId_shop" valType="required" value="<?=$res->wxMchId_shop?>" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">微信支付商户平台 - 账户概览 - 微信支付商户号</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付API密钥：</td>
                        <td class="value" width="350">
                           <input type="text" class="input" id="wxPayKey_shop" name="wxPayKey_shop" valType="required" value="<?=$res->wxPayKey_shop?>"  msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API密钥</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书cert：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" name="certPath_shop" edit-value="<?=$res->certPath_shop?>" id="certPath_shop" type="file"  />
                        </td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API证书 （apiclient_cert.pem）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书key：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" type="file" id="keyPath_shop" edit-value="<?=$res->keyPath_shop?>" name="keyPath_shop" ></td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API证书 （apiclient_key.pem）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书rootca：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" type="file" id="caPath_shop" edit-value="<?=$res->caPath_shop?>" name="caPath_shop"></td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API证书 （rootca.pem）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">订阅消息：</td>
                        <td class="value" class="input" width="350">
                            <textarea class="textarea exception" rows="4" name="subscribeMsg_shop" maxlength="50" cols="20" id="subscribeMsg_shop" valType="required" msg="<font color=red>*</font>不能为空"><?=$res->subscribeMsg_shop?></textarea>
                        </td>
                        <td class="tip">用户关注公众号时，推送给用户的文字内容 （不超过50字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">订阅图片：</td>
                        <td class="value" width="350">
                        <input id="subscribeImgUrl_shop" class="js-upload" type="file" edit-value="<?=$res->subscribeImgUrl_shop?>" name="subscribeImgUrl_shop" ></td>
                        <td class="tip">用户关注公众号时，推送给用户的图片 （不大于500K）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包发送者名称：</td>
                        <td class="value" width="350"><input id="wxSendName_shop" class="input" valType="RED_DES" maxlength="10" value="<?=$res->wxSendName_shop?>" name="wxSendName_shop" msg="<font color=red>*</font>不能为空"></td>
                        <td class="tip">用户收到的微信红包，显示红包发送方名称，只允许中英文和数字，10字符以内</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包活动名称：</td>
                        <td class="value" width="350"><input id="wxActName_shop" class="input" valType="RED_DES" maxlength="10" value="<?=$res->wxActName_shop?>" name="wxActName_shop" msg="<font color=red>*</font>空不能为"></td>
                        <td class="tip">未关注公众号的情况下，用户收到的微信红包，显示红包活动的名称，只允许中英文和数字，10字符以内</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包裂变人数：</td>
                        <td class="value" width="350"><input id="wxRpTotalNum_shop" class="input" valType="NUM" name="wxRpTotalNum_shop" value="<?=$res->wxRpTotalNum_shop?>"  msg="<font color=red>*</font>3-20的正整数"></td>
                        <td class="tip">可以领取每个裂变红包的人数，每人随机分得金额 （只能在3-20人之间）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包祝福语：</td>
                        <td class="value" width="350"><input id="wxWishing_shop" class="input" valType="WXSEND" value="<?=$res->wxWishing_shop?>" name="wxWishing_shop" msg="<font color=red>*</font>不能为空"></td>
                        <td class="tip">显示在红包上的祝福文字 （不超过60字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包备注：</td>
                        <td class="value" class="input" width="350"><textarea class="textarea exception" rows="4" maxlength="50" cols="20" name="wxRemark_shop" id="wxRemark_shop" valType="required" msg="<font color=red>*</font>不能为空"><?=$res->wxRemark_shop?></textarea></td>
                        <td class="tip">不超过120字</td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
            <script>
                $(function(){
                    <?=is_authorizer(2)?'':'common.wxauth(2);'?>
                });
            </script>
        </div>
    </div>
<?php include 'footer.php';?>
</body>
</html>