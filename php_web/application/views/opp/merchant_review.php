<?php include 'common/header.php'; ?>
<link type="text/css" rel="stylesheet" href="/static/css/company.css" />
</head>
<body>
    <?php include 'common/menus.php';?>
    <div class="main">
        <?php include 'merchant_lefter.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft" id="maintitle">1.企业信息查看</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <input type="hidden" id="hid" value="<?=$res->id?>">
            <table class="table-form tab1">
                    <tr>
                        <td class="name" width="150">公司名称：</td>
                        <td class="value" width="350">
                            <input class="input" id="name" name="name" value="<?=$res->name?>" disabled="disabled" msg="<font color=red>*</font>不能为空"/>
                        </td>
                        <td class="tip">请填写贵公司名称 例如：北京爱创科技股份有限公司</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">公司地址：</td>
                        <td class="value" width="350">
                            <input class="input" id="addetail" name="addetail"  value="<?=$res->address?>"   disabled="disabled"/>
                        </td>
                        <td class="tip">例如:河北省石家庄市大东区小东路蓝色科技园A栋8楼3号</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系人：</td>
                        <td class="value" width="350">
                            <input class="input" id="contact" name="contact"  value="<?=$res->contact?>"   disabled="disabled"/>
                        </td>
                        <td class="tip">请输入企业联系人名字</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系邮箱：</td>
                        <td class="value" width="350">
                            <input class="input" id="mail" name="mail"  value="<?=$res->mail?>"  disabled="disabled"/>
                        </td>
                        <td class="tip">企业联系人邮箱</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系手机：</td>
                        <td class="value" width="350">
                            <input class="input" id="phoneNum" name="phoneNum" value="<?=$res->phoneNum?>"   disabled="disabled"/>
                        </td>
                        <td class="tip">企业联系人电话</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">营业执照：</td>
                        <td class="value" width="350">
                            <input class="input"  value="<?=$res->licenseNo?>"  id="licenseNo" name="licenseNo"/>
                        </td>
                        <td class="tip">企业营业执照号码 三证合一请填写统一社会信用代码</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">营业执照扫描件：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->licenseImgUrl?>" id="licenseImgUrl" name="licenseImgUrl" />
                        </td>
                        <td class="tip">企业营业执照扫描件 建议不大于500K</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">身份证号码：</td>
                        <td class="value" width="350">
                            <input class="input" id="idCardNum" value="<?=$res->idCardNum?>"  name="idCardNum"  disabled="disabled"/>
                        </td>
                        <td class="tip">企业联系人身份证号码</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">身份证扫描件：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->idCardImgUrl?>" id="idCardImgUrl" name="idCardImgUrl"/>
                        </td>
                        <td class="tip">企业联系人身份证扫描件 建议不大于500K</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">企业简介：</td>
                        <td class="value" width="350">
                            <textarea class="textarea exception" rows="4" maxlength="100" cols="20"   id="desc"  disabled="disabled"><?=$res->desc?></textarea>
                        </td>
                        <td class="tip">请填入企业的简介 不超过100字</td>
                    </tr>
                    <div class="h20"></div>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><input class="btn btn-blue" type="button" id="tab1" value="下一步"></td>
                    </tr>
            </table>
            <table class="table-form tab2" style="display: none;">    
                    <tr>
                        <td class="name" width="150">公众号名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxName" name="wxName" value="<?=$res->wxName?>" disabled="disabled">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 名称</td>
                    </tr>
                     <tr>
                        <td class="name" width="150">原始ID：</td>
                        <td class="value" width="350">
                            <input type="text" value="<?=$res->wxYsId?>"class="input" id="wxYsId" name="wxYsId" disabled="disabled">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 原始ID （例如：gh_c3343450a8）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">AppId：</td>
                        <td class="value" width="350">
                            <input type="text"value="<?=$res->wxAppId?>" class="input" id="wxAppId" name="wxAppId" disabled="disabled">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppId（应用ID）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">AppSecret：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" value="<?=$res->wxAppSecret?>"id="wxAppSecret" name="wxAppSecret" disabled="disabled">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppSecret（应用密钥）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">二维码：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->wxQrcodeUrl?>" name="wxQrcodeUrl" id="wxQrcodeUrl"  disabled="disabled">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 二维码 （不大于500K）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付商户号：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" value="<?=$res->wxMchId?>" id="wxMchId" name="wxMchId" disabled="disabled">
                        </td>
                        <td class="tip">微信支付商户平台 - 账户概览 - 微信支付商户号</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付API密钥：</td>
                        <td class="value" width="350">
                           <input type="text" class="input" id="wxPayKey"value="<?=$res->wxPayKey?>" name="wxPayKey" disabled="disabled">
                        </td>
                        <td class="tip">微信支付商户平台 - 帐户设置 - API安全 - API密钥</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付证书cert：</td>
                        <td class="value" width="350">
                            <input class="js-upload-att" name="certPath" value="<?=$res->certPath?>"id="certPath" type="file" edit-value="<?=$res->certPath?>" />
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
                            <textarea class="textarea exception" rows="4" name="subscribeMsg" maxlength="50" cols="20" id="subscribeMsg" disabled="disabled"><?=$res->subscribeMsg?></textarea>
                        </td>
                        <td class="tip">用户关注公众号时，推送给用户的文字内容 （不超过50字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">订阅图片：</td>
                        <td class="value" width="350">
                        <input id="subscribeImgUrl" class="js-upload" type="file" edit-value="<?=$res->subscribeImgUrl?>" name="subscribeImgUrl" ></td>
                        <td class="tip">用户关注公众号时，推送给用户的图片 （不大于500K）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包发送者名称：</td>
                        <td class="value" width="350"><input id="wxSendName" class="input" name="wxSendName" value="<?=$res->wxSendName?>" disabled="disabled"></td>
                        <td class="tip">用户收到的微信红包，显示红包发送方名称，中文不超过10个或者英文不超过30个</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包活动名称：</td>
                        <td class="value" width="350"><input id="wxActName" class="input" name="wxActName" value="<?=$res->wxActName?>" disabled="disabled"></td>
                        <td class="tip">未关注公众号的情况下，用户收到的微信红包，显示红包活动的名称，中文不超过10个或者英文不超过30个</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包裂变人数：</td>
                        <td class="value" width="350"><input id="wxRpTotalNum" class="input" name="wxRpTotalNum" value="<?=$res->wxRpTotalNum?>" disabled="disabled"></td>
                        <td class="tip">可以领取每个裂变红包的人数，每人随机分得金额 （只能在3-20人之间）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包祝福语：</td>
                        <td class="value" width="350"><input id="wxWishing" class="input" name="wxWishing" value="<?=$res->wxWishing?>" disabled="disabled"></td>
                        <td class="tip">显示在红包上的祝福文字 （不超过60字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包备注：</td>
                        <td class="value" class="input" width="350"><textarea class="textarea exception" rows="4" maxlength="50" cols="20" name="wxRemark" id="wxRemark" disabled="disabled"><?=$res->wxRemark?></textarea></td>
                        <td class="tip">不超过120字</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn btn-blue" type="button" id="ntab" value="上一步"></td>
                        <td><input class="btn btn-blue" type="button" id="tab2" value="下一步"></td>
                    </tr>
            <table class="table-form tab3" style="display:none;">
                    <tr>
                        <td class="name" width="150">公众号名称：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxName_shop" name="wxName_shop"  value="<?=$res->wxName_shop?>" disabled="disabled">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 名称</td>
                    </tr>
                     <tr>
                        <td class="name" width="150">原始ID：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxYsId_shop" name="wxYsId_shop"  value="<?=$res->wxYsId_shop?>" disabled="disabled">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 原始ID （例如：gh_c3343450a8）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">AppId：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxAppId_shop" name="wxAppId_shop"  value="<?=$res->wxAppId_shop?>" disabled="disabled">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppId（应用ID）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">AppSecret：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxAppSecret_shop" name="wxAppSecret_shop" value="<?=$res->wxAppSecret_shop?>" disabled="disabled">
                        </td>
                        <td class="tip">开发 - 基本配置 - AppSecret（应用密钥）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">二维码：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->wxQrcodeUrl_shop?>" name="wxQrcodeUrl_shop" id="wxQrcodeUrl_shop" disabled="disabled">
                        </td>
                        <td class="tip">设置 - 公众号设置 - 二维码 （不大于500K）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付商户号：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="wxMchId_shop" name="wxMchId_shop"  value="<?=$res->wxMchId_shop?>" disabled="disabled">
                        </td>
                        <td class="tip">微信支付商户平台 - 账户概览 - 微信支付商户号</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">微信支付API密钥：</td>
                        <td class="value" width="350">
                           <input type="text" class="input" id="wxPayKey_shop" name="wxPayKey_shop"  value="<?=$res->wxPayKey_shop?>" disabled="disabled">
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
                            <textarea class="textarea exception" rows="4" name="subscribeMsg_shop" maxlength="50" cols="20" id="subscribeMsg_shop" disabled="disabled"><?=$res->subscribeMsg_shop?></textarea>
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
                        <td class="value" width="350"><input id="wxSendName_shop" class="input"  maxlength="10" value="<?=$res->wxSendName_shop?>" name="wxSendName_shop" disabled="disabled"></td>
                        <td class="tip">用户收到的微信红包，显示红包发送方名称，10字以内</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包活动名称：</td>
                        <td class="value" width="350"><input id="wxActName_shop" class="input" value="<?=$res->wxActName_shop?>" name="wxActName_shop" disabled="disabled"></td>
                        <td class="tip">未关注公众号的情况下，用户收到的微信红包，显示红包活动的名称，中文不超过10个或者英文不超过30个</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包裂变人数：</td>
                        <td class="value" width="350"><input id="wxRpTotalNum_shop" class="input"  name="wxRpTotalNum_shop" value="<?=$res->wxRpTotalNum_shop?>"  disabled="disabled"></td>
                        <td class="tip">可以领取每个裂变红包的人数，每人随机分得金额 （只能在3-20人之间）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包祝福语：</td>
                        <td class="value" width="350"><input id="wxWishing_shop" class="input"  value="<?=$res->wxWishing_shop?>" name="wxWishing_shop" disabled="disabled"></td>
                        <td class="tip">显示在红包上的祝福文字 （不超过60字）</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">红包备注：</td>
                        <td class="value" class="input" width="350"><textarea class="textarea exception" rows="4" maxlength="50" cols="20" name="wxRemark_shop" id="wxRemark_shop" disabled="disabled"><?=$res->wxRemark_shop?></textarea></td>
                        <td class="tip">不超过120字</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">码版本选择：</td>
                        <td class="value" class="input" width="350">
                            <select id="codeVersion" class="select" name="codeVersion" edit-value="<?=$res->codeVersion?>">
                                <option value="">请选择......</option>
                                <?php foreach ($code_version as $key => $value): ?>
                                <option <?=$res->codeVersion==$value->versionNum?'selected':'' ?> value="<?=$value->versionNum?>"><?=$value->versionNum?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="name">到期时间：</td>
                        <td class="value">
                            <input class="input Wdate" id="expireTime" name="expireTime" value="<?=date('Y-m-d',$expireTime+31535985)?>" valType="NOTNULL" msg="<font color=red>*</font>『到期时间』不能为空" style="background-position:98% 50%;"  onfocus="WdatePicker({isShowWeek:true})" />
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">码数量：</td>
                        <td class="value">
                            <input class="input" id="codeLimited" value="<?=$res->codeLimited?>" name="codeLimited">
                        </td>
                        <td class="tip">请填入企业允许使用的码量上限,仅可输入数字</td>
                    </tr>
                    <tr>
                        <td><input class="btn btn-blue" type="button" id="nntab" value="上一步"></td>
                        <td><input class="btn btn-blue" style="background:#DB5050" type="button" id="unpass" value="审核不通过"></td>
                        <td><input class="btn btn-blue" type="button" id="pass" value="审核通过"></td>
                    </tr>
                </table>
            </form>
         </div>
         <div id="editForm" style="display:none;">
            <div class="table-form">
                <label>请填写审核原因：<textarea class="textarea exception" rows="4" maxlength="50" cols="20" name="checkReason" id="checkReason" valType="required" msg="<font color=red>*</font>不能为空"></textarea> <BR></label>
            </div>
        </div>
    </div>

<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/check.js"></script>
<?php include 'common/footer.php';?>