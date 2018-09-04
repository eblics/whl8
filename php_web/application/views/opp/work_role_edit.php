<?php include 'common/header.php' ?>
<link rel="stylesheet" type="text/css" href="/static/css/merchant_lock.css">
<script type="text/javascript" src="/static/js/work/work_role_edit.js"></script>
</head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <?php include 'work_left.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
            <form type="validate">
                <table class="table-form">
                    <input type="hidden" name="id" id="id" value="<?=$data['id']?>">
                        <td class="name" width="150">系统角色：</td>
                        <td class="value" width="350">
                            <select class="select" name="role" id="role">
                                <option <?=$data['role'] ==1?'selected="selected"':'' ?> value="1">客服</option>
                                <option <?=$data['role'] ==2?'selected="selected"':'' ?> value="2">技术</option>
                            </select>
                        </td>
                        <td class="tip">展现给客户的角色名称，如：客服</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">员工编号：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="code" name="code" value="<?=$data['code']?>" valType="NOTNULL" msg="<font color=red>*</font>员工编号不能为空"/>
                        </td>
                        <td class="tip">建议格式为：客服001，技术010</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">员工名字：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="name" name="name" value="<?=$data['name']?>" valType="NOTNULL" msg="<font color=red>*</font>员工名字不能为空"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">手机号码：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="phoneNum" name="phoneNum" value="<?=$data['phoneNum']?>" valType="MOBILE" msg="<font color=red>*</font>手机号码格式错误"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td class="name" width="150">邮箱：</td>
                        <td class="value" width="350">
                            <input type="text" class="input" id="mail" name="mail" value="<?=$data['mail']?>" valType="MAIL" msg="<font color=red>*</font>邮箱格式错误"/>
                        </td>
                        <td class="tip"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><div id="btnAdd" class="btn btn-blue">保存</div></td>
                    </tr>
                </table>
            </form>
    </div>
</div>
<?php include 'common/footer.php';?>