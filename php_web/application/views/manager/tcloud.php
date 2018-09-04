<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/tcloud.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">腾讯云接口</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">secretId：</td>
                        <td class="value" width="350">
                        	<input class="input" type="text" id="secretId" value=""   name="secretId" msg="<font color=red>*</font>不能为空" />
                        </td>
                        <td class="tip">腾讯云-云产品-管理工具-云API秘钥-secretId</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">secretKey：</td>
                        <td class="value" width="350">
                            <input class="input" type="text" id="secretKey" value=""  name="secretKey" msg="<font color=red>*</font>不能为空" />
                        </td>
                        <td class="tip">腾讯云-云产品-管理工具-云API秘钥-secretKey</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">腾讯云接口是否可用：</td>
                        <td class="value" class="">
                            <label id="interfaceStatus" >未知</label> &nbsp;&nbsp;
                        </td>
                        <td class="tip">通过访问接口自动获取</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">是否启用防刷防护：</td>
                        <td class="value" class="">
                            <label for="isuse_1"><input id="isuse_1" name="isUse" type="radio" value="1" <?=(int) $data->isUse===1?'checked':''?>/>启用</label> &nbsp;&nbsp;
                            <label for="isuse_0"><input id="isuse_0" name="isUse" type="radio" value="0" <?=(int) $data->isUse===0?'checked':''?>/>禁用</label> &nbsp;&nbsp;
                        </td>
                        <td class="tip">是否启用腾讯云API活动防刷功能</td>
                    </tr>

                    <tr>
                        <td class="name" width="150">禁止活动级别设置：</td>
                        <td class="value" class="">
                           <label for="ignore_1" style=<?=(int) $data->ignoreLevel <= 1?'color:red;':''?>>
                            	<input id="ignore_1" name="ignoreLevel" type="radio" value="1" <?=(int) $data->ignoreLevel===1?'checked':''?>/>1
                            </label> &nbsp;&nbsp;
                            <label for="ignore_2" style=<?=(int) $data->ignoreLevel <= 2?'color:red;':''?>>
                            	<input id="ignore_2" name="ignoreLevel" type="radio" value="2" <?=(int) $data->ignoreLevel===2?'checked':''?>/>2
                            </label> &nbsp;&nbsp;
                            <label for="ignore_3" style=<?=(int) $data->ignoreLevel <= 3?'color:red;':''?>>
                            	<input id="ignore_3" name="ignoreLevel" type="radio" value="3" <?=(int) $data->ignoreLevel===3?'checked':''?>/>3
                            </label> &nbsp;&nbsp;
                            <label for="ignore_4" style=<?=(int) $data->ignoreLevel <= 4?'color:red;':''?>>
                            	<input id="ignore_4" name="ignoreLevel" type="radio" value="4" <?=(int) $data->ignoreLevel===4?'checked':''?>/>4
                            </label> &nbsp;&nbsp;
                        </td>
                        <td class="tip">当前选择级别以及比当前恶意级别高的用户禁止参加活动，恶意等级按数值由低到高</td>
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