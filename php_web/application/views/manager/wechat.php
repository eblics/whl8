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
<script type="text/javascript" src="/static/js/wechat.js"></script>
</head>
<body>
<?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">消费者微信信息 （<?=$data->wxAuthStatus == 1?'<font color=green>已授权：'.$data->wxName.'</font>':'<font color=red>未授权</font>';?>）</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form" hinfo="<?=$data->status?>">
                    <tr style="display:<?=$data->status == 1?'':'none';?>">
                        <td class="name" width="150">公众号名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxName" name="wxName" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 名称</td>
                    </tr>
                     <tr style="<?=$data->status == 1?'':'display:none;';?>">
                        <td class="name" width="150">原始ID：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxYsId" name="wxYsId" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 原始ID （例如：gh_c3343450a8）</td>
                    </tr>
                    <tr style="<?=$data->status == 1?'':'display:none;';?>">
                        <td class="name" width="150">AppId：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxAppId" name="wxAppId" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppId（应用ID）</td>
                    </tr>
                    <tr style="<?=$data->status == 1?'':'display:none;';?>">
                        <td class="name" width="150">AppSecret：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxAppSecret" name="wxAppSecret" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppSecret（应用密钥）</td>
                    </tr>
                    <tr style="<?=$data->status == 1?'':'display:none;';?>">
                        <td class="name" width="150">二维码：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->wxQrcodeUrl?>" name="wxQrcodeUrl" id="wxQrcodeUrl"  msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 二维码 （不大于500K）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付商户号：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxMchId" name="wxMchId" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">微信支付商户平台 - 账户概览 - 微信支付商户号</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付API密钥：</td>
                        <td class="value" width="350">
                           <input type="text" class="input" id="wxPayKey" name="wxPayKey" valType="required" msg="<font color=red>*</font>不能为空">
                        </td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API密钥</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书cert：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" name="certPath" id="certPath" type="file" edit-value="<?=$res->certPath?>" />
                        </td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API证书 （apiclient_cert.pem）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书key：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" type="file" id="keyPath" edit-value="<?=$res->keyPath?>" name="keyPath" ></td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API证书 （apiclient_key.pem）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书rootca：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" type="file" id="caPath" edit-value="<?=$res->caPath?>" name="caPath"></td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API证书 （rootca.pem）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">订阅消息：</td>
                        <td class="value" class="input" width="350">
                            <textarea class="textarea exception" rows="4" name="subscribeMsg" maxlength="50" cols="20" id="subscribeMsg" /></textarea>
                        </td>
                        <td class="tip">用户关注公众号时，推送给用户的文字内容 （不超过50字，不填写则不推送）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">订阅图片：</td>
                        <td class="value" width="350">
                        <input id="subscribeImgUrl" class="js-upload" type="file" edit-value="<?=$res->subscribeImgUrl?>" name="subscribeImgUrl" ></td>
                        <td class="tip">用户关注公众号时，推送给用户的图片 （不大于500K，不填写则不推送）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包发送者名称：</td>
                        <td class="value" width="350"><input id="wxSendName" class="input" maxlength="10" valType="RED_DES" name="wxSendName" msg="<font color=red>*</font>格式不正确"></td>
                        <td class="tip">用户收到的微信红包，显示红包发送方名称，只允许中英文或数字，不超过10个字符</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包活动名称：</td>
                        <td class="value" width="350"><input id="wxActName" class="input" maxlength="10" valType="RED_DES" name="wxActName" msg="<font color=red>*</font>格式不正确"></td>
                        <td class="tip">未关注公众号的情况下，用户收到的微信红包，显示红包活动的名称，只允许中英文或数字，不超过10个字符</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包裂变人数：</td>
                        <td class="value" width="350"><input id="wxRpTotalNum" class="input" valType="NUM" name="wxRpTotalNum" msg="<font color=red>*</font>3-20的正整数"></td>
                        <td class="tip">可以领取每个裂变红包的人数，每人随机分得金额 （只能在3-20人之间）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包祝福语：</td>
                        <td class="value" width="350"><input id="wxWishing" class="input" valType="required" name="wxWishing" msg="<font color=red>*</font>不能为空"></td>
                        <td class="tip">显示在红包上的祝福文字 （不超过60字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包备注：</td>
                        <td class="value" class="input" width="350"><textarea class="textarea exception" rows="4" maxlength="50" cols="20" name="wxRemark" id="wxRemark" valType="required" msg="<font color=red>*</font>不能为空"></textarea></td>
                        <td class="tip">不超过120字</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">用户提现发放方式：</td>
                        <td class="value" class="">
                            <label for="type_0"><input id="type_0" name="wxSendType" type="radio" value="0" <?=(int) $res->wxSendType===0?'checked':''?>/> 微信红包</label> &nbsp;&nbsp;
                            <label for="type_1"><input id="type_1" name="wxSendType" type="radio" value="1" <?=(int) $res->wxSendType===1?'checked':''?>/> 微信企业付款</label>
                        </td>
                        <td class="tip">如选择微信企业付款，将无法发放裂变红包</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">用户提现界面通知：</td>
                        <td class="value" width="350"><input id="wxSendTip" class="input" valType="required" name="wxSendTip" maxlength=20 msg="<font color=red>*</font>不能为空"></td>
                        <td class="tip">显示在用户提现界面上的提示文字 （不超过20字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">开启扫码验证码：</td>
                        <td class="value" class="">
                            <label for="withCaptcha_0"><input id="withCaptcha_0" name="withCaptcha" type="radio" value="0" <?=(int) $res->withCaptcha===0?'checked':''?>/> 否</label> &nbsp;&nbsp;
                            <label for="withCaptcha_1"><input id="withCaptcha_1" name="withCaptcha" type="radio" value="1" <?=(int) $res->withCaptcha===1?'checked':''?>/> 是</label>
                        </td>
                        <td class="tip">开启后用户扫码环节需要正确输入验证码才能参加活动</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">扫码获取地理位置：</td>
                        <td class="value" class="">
                            <label for="geoLocation_0"><input id="geoLocation_0" name="geoLocation" type="radio" value="0" <?=(int) $res->geoLocation===0?'checked':''?>/> 开启</label> &nbsp;&nbsp;
                            <label for="geoLocation_1"><input id="geoLocation_1" name="geoLocation" type="radio" value="1" <?=(int) $res->geoLocation===1?'checked':''?>/> 关闭</label>
                        </td>
                        <td class="tip">关闭后则不获取消费者扫码时的地理位置，同时本系统中基于地理位置的所有功能将失效，请谨慎关闭</td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" value="保存"></td><td></td>
                    </tr>
                </table>
            </form>
            <script>
                $(function(){
                    <?=is_authorizer(1)?'':'common.wxauth(1);'?>
                });
            </script>
        </div>
    </div>
<?php include 'footer.php';?>
</body>
</html>