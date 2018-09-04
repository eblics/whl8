<?php
$isEdit=FALSE;
if(isset($data)){
    $isEdit=TRUE;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/product_edit.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">产品<?=$isEdit?'修改':'添加'?></span>
        </div>
        <div class="h20"></div>
        <form>
            <?=$isEdit?'<input type="hidden" name="id" value="'.$data->id.'"/>':''?>
            <table class="table-form">
                <tr>
                    <td class="name" width="100">产品名称：</td><td class="value" width="350"><input class="input" name="name" value="<?=$isEdit?$data->name:''?>" valType="PRONAME" msg="<font color=red>*</font>请正确输入『产品名称』" /></td><td class="tip">请控制在4~32个字符之间</td>
                </tr>
                <tr>
                    <td class="name">所属分类：</td><td class="value"><select id="categoryId" class="select" name="categoryId" edit-value="<?=$isEdit?$data->categoryId:''?>"></select></td><td class="tip"></td>
                </tr>
                <tr>
                    <td class="name">产品图片：</td><td class="value"><input class="js-upload" id="imgUrl" type="file" name="imgUrl" edit-value="<?=$isEdit?$data->imgUrl:''?>" /></td><td class="tip">图片大小不得超过500k</td>
                </tr>
                <tr>
                    <td class="name">计量单位：</td><td class="value"><input class="input" name="unit" value="<?=$isEdit?$data->unit:''?>" valType="NOTNULL" msg="<font color=red>*</font>『计量单位』不能为空" /></td><td class="tip">例如：千克、升、毫升等</td>
                </tr>
                <tr>
                    <td class="name">计量规格：</td><td class="value"><input class="input" name="specification" value="<?=$isEdit?$data->specification:''?>" valType="NOTNULL" msg="<font color=red>*</font>『计量规格』不能为空"  /></td><td class="tip">例如：袋、箱、盒等</td>
                </tr>
                <tr>
                    <td class="name">产品简介：</td><td class="value"><textarea class="textarea" name="description" maxlength="600" rows="8" valType="NOTNULL" msg="<font color=red>*</font>『产品描述』不能为空" ><?=$isEdit?$data->description:''?></textarea></td><td class="tip">产品的文字简介，控制在600字内</td>
                </tr>
                <tr>
                    <td></td><td><span id="btnSave" class="btn btn-blue noselect">保存</span></td><td></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>