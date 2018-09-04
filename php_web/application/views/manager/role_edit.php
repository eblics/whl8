<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/role.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">
            <?php if ($edit): ?>
            编辑角色
            <?php else:?>
            添加角色
            <?php endif;?>
            </span>
        </div>
        <div class="h20"></div>
        <div class="content">
           <form>
                <table class="table-form">
                <tr><td class="name" width="100">角色名称：</td>
                    <td class="value" width="350">
                    <?php if ($edit): ?>
                        <input class="input" name="realname" id="realname" valType="ADMINNAME"
                            value="<?=$role->roleName?>"
                            msg="<font color=red>*</font>请正确输入『角色名称』" />
                    <?php else:?>
                    <input class="input" name="realname" id="realname" valType="ADMINNAME"
                        msg="<font color=red>*</font>请正确输入『角色名称』" />
                    <?php endif;?>
                    </td>
                    <td class="tip">请控制在2~8个字符之间</td></tr>
                <tr>
                    <td class="name" width="100">选择权限：</td>
                    <td class="value permission-list" width="350">
                    <?php foreach ($permissions as $key => $permission) {
                        print '<label><input type="checkbox" id="'. $permission->id .'" name="permission_'. 
                            $permission->id .'"> ' . $permission->name . '</label>';
                        if (($key + 1) % 2 == 0) {
                            print '<br/>';
                        }
                    } ?></td><td></tr>
                <tr><td></td>
                    <td><span id="selectAll" class="btn btn-blue noselect select-all">
                        全选
                    </span><span id="btnSave" class="btn btn-blue noselect">
                        保存
                    </span><span class="btn btn-blue noselect" onclick="javascript:location.href='/role'">
                        返回
                    </span></td>
                    <td></td></tr>
            </table>
            </form>
        </div>
    </div>
</div>
<?php if ($edit): ?>
<input id="role_id" type="hidden" value="<?=$role->id?>" />
<?php endif;?>
<?php include 'footer.php';?>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript">
var isEdit = <?=$edit ? 'true': 'false'?>;
<?php if ($edit): ?>
var rolePermissions = <?=$role_permissions?>;
$('.permission-list input').each(function() {
    for (var i = 0; i < rolePermissions.length; i++) {
        if ($(this).prop('id') == rolePermissions[i]) {
            $(this).prop('checked', true);
        }
    }
});
<?php endif;?>

// 如果是全选状态，要修改按钮的文字
var isAllSelected = true;
$('.permission-list input').each(function() {
    if (! $(this).prop('checked')) {
        isAllSelected = false;
    }
});
if (isAllSelected) {
    $('#selectAll').removeClass('select-all');
    $('#selectAll').text('取消全选');
}

$('.permission-list input').change(function() {
    if ($(this).prop('checked')) {
        $(this).parent().addClass('selected');
    } else {
        $(this).parent().removeClass('selected');
    }
});
$('#selectAll').click(function() {
    if ($(this).hasClass('select-all')) {
        $('.permission-list input').each(function() {
            $(this).prop('checked', true);
            $(this).parent().addClass('selected');
        });
        $(this).removeClass('select-all');
        $(this).text('取消全选');
    } else {
        $('.permission-list input').each(function() {
            $(this).prop('checked', false);
            $(this).parent().removeClass('selected');
        });
        $(this).addClass('select-all');
        $(this).text('全选');
    }
});
$("#btnSave").on("click", function() {
    var selectedPermissions = [];
    if (beforeSubmitAct()) {
        $('.permission-list input').each(function() {
            if ($(this).prop('checked')) {
                selectedPermissions.push($(this).prop('id'));
            }
        });

        var url = '/role/store';

        if (isEdit) {
            url = '/role/update/' + $('#role_id').val();
        }

        $.post(url, {"role_name": $('#realname').val(), "ids": JSON.stringify(selectedPermissions)}, function(resp) {
            if (resp.errcode == 0) {
                common.alert('操作成功！', function(yes) {
                    location.href = '/role';
                });
            } else {
                common.alert(resp.errmsg + '！');
            }
        }).fail(function(err) {
            common.alert('无法连接服务器！');
        });
    }
});

$('.permission-list input').each(function() {
    if ($(this).prop('checked')) {
        $(this).parent().addClass('selected');
    }
});
</script>
</body>
</html>