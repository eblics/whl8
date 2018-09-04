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
<script type="text/javascript" src="/static/js/safe.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">帐号安全</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">原始密码：</td>
                        <td class="value" width="350">
                            <input class="input" type="password" id="oldpass" name="oldpass" valType="required" msg="<font color=red>*</font>不能为空"/>
                        </td>
                        <td class="tip">您的账户原始密码</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">新密码：</td>
                        <td class="value" width="350">
                            <input class="input" type="password" id="newpass" name="newpass" valType="PASS" msg="<font color=red>*</font>密码为英文或者数字且在6-18位之间"/>
                        </td>
                        <td class="tip">密码为英文或者数字且在6-18位之间</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">确认密码：</td>
                        <td class="value" width="350">
                            <input class="input" type="password" id="renewpass" name="renewpass" valType="PASS" msg="<font color=red>*</font>密码为英文或者数字且在6-18位之间"/>
                        </td>
                        <td class="tip">确认新密码</td>
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