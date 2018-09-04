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
<script type="text/javascript" src="/static/js/person.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">个人信息</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">用户昵称：</td>
                        <td class="value" width="350">
                            <input class="input" id="userName" name="userName" valType="NICKNAME" msg="<font color=red>*</font>用户昵称不正确"/>
                        </td>
                        <td class="tip">用户昵称 中文 英文或者 数字 不允许中英文混合 不允许符号 在2-20位之间</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">真实名字：</td>
                        <td class="value" width="350">
                            <input class="input" id="realName" name="realName" valType="NAME" msg="<font color=red>*</font>名字格式不正确"/>
                        </td>
                        <td class="tip">您的真实名字 中文名字</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">邮箱：</td>
                        <td class="value" width="350">
                            <input class="input" id="mail" name="mail" valType="MAIL" msg="<font color=red>*</font>电子邮件格式错误"/>
                        </td>
                        <td class="tip">您的电子邮件地址 例如：admin01@sina.com</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">手机号码：</td>
                        <td class="value" width="350">
                            <input class="input" id="phoneNum" readonly="readonly" name="phoneNum" valType="MOBILE" msg="<font color=red>*</font>手机号码格式错误"/>
                        </td>
                        <td class="tip">登录帐号 目前不允许修改</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">身份证号码：</td>
                        <td class="value" width="350">
                            <input class="input" id="idCardNum" name="idCardNum" valType="IDENTITY" msg="<font color=red>*</font>身份证号码格式错误"/>
                        </td>
                        <td class="tip">帐号管理人员身份证号码</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">身份证扫描件：</td>
                        <td class="value" width="350">
                            <input class="js-upload" type="file" edit-value="<?=$res->idCardImgUrl?>" id="idCardImgUrl" name="idCardImgUrl" msg="<font color=red>*</font>请上传身份证扫描件"/>
                        </td>
                        <td class="tip">帐号管理人员身份证扫描件 不大于500K</td>
                    </tr>
                    <tr><td></td><td>
                        <input class="btn btn-blue" type="button" id="sub" value="保存">
                    </td><td></td></tr>
                </table>
                <?php if ($_SESSION['role'] == ROLE_ADMIN_MASTER): ?>
                <div class="btn-container">
                    <button class="btn btn-blue" type="button" id="authorize">授权给企业号</button>
                <?php if ($account_type == AccountTypeEnum::Merchant):?>
                    <button class="btn btn-blue" disabled="disabled" type="button" id="upgrade">升级为企业号</button>
                <?php else:?>
                    <button class="btn btn-blue" type="button" id="upgrade">升级为企业号</button>
                <?php endif;?>
                </div>
                <!-- Added by shizq - begin -->
                <script type="text/javascript">
                    // ----------------------------------
                    // 处理升级为企业号
                    $('#upgrade').click(function() {
                        common.confirm('确认升级为企业号吗？', function(yes) {
                            if (! yes) return; 
                            $.post('/user/upgrade_account', {}, function(resp) {
                                if (resp.errcode === 0) {
                                    $('#upgrade').prop('disabled', true);
                                    common.alert('操作成功！');
                                } else {
                                    common.alert(resp.errmsg + '！');
                                }
                            }).fail(function(err) {
                                common.alert('无法连接服务器！');
                            });
                        });
                    });

                    // ----------------------------------
                    // 处理授权给企业号
                    $('#authorize').click(function() {
                        var mobile = prompt('请输入要授权的企业号（手机号）：');
                        if (mobile == null) {
                            return;
                        }
                        $.post('/user/authorize_account', {"mobile": mobile}, function(resp) {
                            if (resp.errcode == 10123) {
                                var smsCode = prompt('请输入该手机号收到的验证码：');
                                if (smsCode == null) {
                                    return;
                                }
                                $.post('/user/authorize_account', {"mobile": mobile, "sms_code": smsCode}, function(resp2) {
                                    if (! resp2.errcode) {
                                        common.alert('操作成功！');
                                    } else {
                                        common.alert(resp2.errmsg + '！');
                                    }
                                }).fail(function(err2) {
                                    common.alert('无法连接服务器！');
                                });
                            } else {
                                common.alert(resp.errmsg + '！');
                            }
                        }).fail(function(err) {
                            common.alert('无法连接服务器！');
                        });
                    });
                </script>
                <!-- Added by shizq - end -->
                <?php endif; // if ($_SESSION['role'] == ROLE_ADMIN_MASTER)?>
            </form>
        
         </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>