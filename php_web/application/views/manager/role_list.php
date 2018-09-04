<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME. ' - ' .SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<style type="text/css">
    
    .item {
        display: inline-block;
        min-width: 85px;
        text-align: center;
        margin-top: 5px;
    }
    .item:nth-child(2n) {
        border-right: 1px solid #CCC;
        border-left: 1px solid #CCC;
    }

</style>
<script type="text/javascript" src="/static/js/jquery.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">角色管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="role_list_container" class="display">
                <thead>
                    <tr>
                        <th width="30">编号</th> 
                        <th>名称</th>
                        <th>权限</th>
                        <th width="160">操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>编号</th> 
                        <th>名称</th>
                        <th>权限</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/role/create">添加角色</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript">
function bindEvent() {
    $('#role_list_container .del').click(function() {
        var roleId = $(this).attr('data-id');
        common.confirm('删除角色将删除角色下所有的用户，确认操作？', function(yes) {
            if (yes) {
                $.post('/role/destroy/' + roleId, {}, function(resp) {
                    if (resp.errcode == 0) {
                        common.alert('操作成功！', function() {
                            location.reload();
                        });
                    } else {
                        common.alert(err.errmsg + '！');
                    }
                }).fail(function(err) {
                    common.alert('无法连接服务器！');
                });
            }
        });
    });
}

var data = [
    {"data": 'id', "class":"center"},
    {"data": 'roleName', "class":""},
    {"data": 'permissions', "class":"items center", render: function(data) {
        var less = 3 - (data.length % 3);
        var content = '';
        if (less < 3) {
            for (var j = 0; j < less; j++) {
                data.push(' ');
            }
        }
        for (var i = 0; i < data.length; i++) {
            content += '<span class="item">' + data[i] + '</span>';
            if ((i + 1) % 3 == 0) {
                content += '<br/>';
            }
        }
        return content;
    }},
    {"data": null,
        "class": "right noselect nowrap",
        render: function(data) {
            var edit = '<a class="btn-text noselect blue" href="/role/edit/' + data.id + '">修改</a>　';
            var del = '<span class="btn-text noselect del gray" data-id="' + data.id + '">删除</span>';
            return edit + del;
        }
    }
];
$(function() {
    $('#role_list_container').DataTable({
        "language": {
            "url": "/static/datatables/js/dataTables.language.js"
        },
        "paging": true,
        "ordering": false,
        "order": [[0,'desc']],
        "info": true,
        "stateSave": false,
        "searching": true,
        "ajax": {
            "url": "/role/lists"
        },
        "columns": data,

        initComplete: function() {
            bindEvent();
            common.autoHeight();
        },

        drawCallback: function() {
            bindEvent();
            common.autoHeight();
        }
    });

    bindEvent();
});
</script>
</body>
</html>
