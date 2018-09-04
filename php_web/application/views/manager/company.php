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
<script type="text/javascript" src="/static/js/company.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">企业信息</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">公司名称：</td>
                        <td class="value" width="350">
                            <input class="input" id="name" name="name" valType="required" msg="<font color=red>*</font>不能为空"/>
                        </td>
                        <td class="tip">请填写贵公司名称 例如：北京爱创科技股份有限公司</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">公司地址：</td>
                        <td class="value" width="350">
                            <input class="input" id="addetail" name="addetail" valType="required" msg="<font color=red>*</font>不能为空"/>
                        </td>
                        <td class="tip">例如:河北省石家庄市大东区小东路蓝色科技园A栋8楼3号</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系人：</td>
                        <td class="value" width="350">
                            <input class="input" id="contact" name="contact" valType="required" msg="<font color=red>*</font>不能为空"/>
                        </td>
                        <td class="tip">请输入企业联系人名字</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系邮箱：</td>
                        <td class="value" width="350">
                            <input class="input" id="mail" name="mail" valType="MAIL" msg="<font color=red>*</font>邮箱不正确或为空"/>
                        </td>
                        <td class="tip">企业联系人邮箱</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">联系手机：</td>
                        <td class="value" width="350">
                            <input class="input" id="phoneNum" name="phoneNum" valType="MOBILE" msg="<font color=red>*</font>号码格式不正确"/>
                        </td>
                        <td class="tip">企业联系人电话</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">营业执照：</td>
                        <td class="value" width="350">
                            <input class="input" id="licenseNo" name="licenseNo"/>
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
                            <input class="input" id="idCardNum" name="idCardNum" valType="IDENTITY" msg="<font color=red>*</font>企业联系人身份证格式不正确"/>
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
                            <textarea class="textarea exception" rows="4" maxlength="100" cols="20" id="desc" valType="required" msg="<font color=red>*</font>不能为空"></textarea>
                        </td>
                        <td class="tip">请填入企业的简介 不超过100字</td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue" type="button" id="sub" value="保存"></td><td></td>
                    </tr>
                </table>
                
            </form>
        
         </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>