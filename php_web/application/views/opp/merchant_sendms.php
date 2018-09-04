<?php include 'common/header.php'; ?></head>
<body> <?php include 'common/menus.php';?>
<div class="main">
<?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path"><span class="title fleft">短信发送</span></div>
        <div class="h20"></div>
        <form type="validate">
            <table class="table-form">
                <tr><td class="name" width="150">当前选择企业：</td>
                    <td class="value" name="objectives" value="all" id="objectives" width="350">所有企业</td>
                    <td class="tip">   
                    </td>
                </tr>
                <tr>
                    <td class="name" width="150">更新概要1</td>
                    <td class="value" width="350">
                    <textarea class="textarea" style="resize: none;"  name="content1" id="content1" valType="required" maxlength="15" msg="<font color=red>*</font>不能为空"></textarea>
                    </td>
                    <td class="tip">包括标点在内，严格控制在15字以内。概要1必填！！！</td>
                </tr>
                <tr>
                    <td class="name" width="150">更新概要2</td>
                    <td class="value" width="350">
                        <textarea class="textarea" maxlength="15" style="resize: none;"  name="content2" id="content2"></textarea>
                    </td>
                    <td class="tip">没有请留空，要求同上。</td>
                </tr>
                <tr>
                    <td class="name" width="150">更新概要3</td>
                    <td class="value" width="350">
                        <textarea class="textarea" maxlength="15" style="resize: none;" name="content3" id="content3"></textarea>
                    </td>
                    <td class="tip">没有请留空，要求同上。</td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-blue" type="button" id="sub" value="发送短信"></td>
                    <td></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript" src="/static/js/merchant/sendms.js"></script>
<?php include 'common/footer.php';?>