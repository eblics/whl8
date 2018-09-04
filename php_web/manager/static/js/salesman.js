/**
 * 业务员管理界面js逻辑
 * 
 * @author shizq
 */
var Page = {

    init: function() {
        this.createTable();
    },

    bindEvent: function() {
        var _this = this;
        $('#salesmanTable tbody td .del').off('click').on("click", function() {
            $('#salesmanTable tr').removeClass('selected');
            $(this).parent('td').parent('tr').addClass('selected');
            var salesmanId = $(this).attr('data-id');
            common.confirm('确定删除吗？', function(confirmed) {
                if (confirmed == 1) {
                    _this.delAdmin(salesmanId);
                }
            });
        });

        $('#salesmanTable tbody td .freeze').off('click').on("click", function() {
            $('#salesmanTable tr').removeClass('selected');
            $(this).parent('td').parent('tr').addClass('selected');
            var salesmanId = $(this).attr('data-id');
            var lock = $(this).attr('data-status');
            var msg = '确定锁定此账户吗？';
            if (lock == 0) {
                msg = '确定解锁此账户吗？';
            }
            common.confirm(msg, function(confirmed) {
                if (confirmed == 1) {
                    _this.freezeAdmin(salesmanId, lock);
                }
            });
        });
    },

    /**
     * 删除一个业务员账户
     * 
     * @param  int salesmanId 业务员ID
     */
    delAdmin: function(salesmanId) {
        var params = {"salesman_id": salesmanId};
        common.loading();
        $.post('/salesman/del', params, function(resp) {
            common.unloading();
            if (resp.errcode == 0) {
                var table = $('#salesmanTable').DataTable();
                table.row('.selected').remove().draw(false);
                common.autoHeight();
            } else {
                common.alert(resp.errmsg + '！');
            }
        }).fail(function(error) {
            common.unloading();
            common.alert('无法连接服务器！');
        });;
    },

    /**
     * 锁定或解锁一个业务员账户
     * 
     * @param salesmanId 业务员ID
     */
    freezeAdmin: function(salesmanId, lock) {
        var self = this;
        var params = {"salesman_id": salesmanId, "lock": lock};
        common.loading();
        $.post('/salesman/freeze', params, function(resp) {
            common.unloading();
            if (resp.errcode == 0) {
                if (lock == 0) {
                    $('#salesmanTable tr.selected .state span').text('正常');
                    $('#salesmanTable tr.selected .freeze').text('锁定');
                    $('#salesmanTable tr.selected .freeze').attr('data-status', 2);
                }
                if (lock == 2) {
                    $('#salesmanTable tr.selected .state span').text('已锁定');
                    $('#salesmanTable tr.selected .freeze').text('解锁');
                    $('#salesmanTable tr.selected .freeze').attr('data-status', 0);
                }
                $('#salesmanTable tr').removeClass('selected');
            } else {
                common.alert(resp.errmsg + '！');
            }
        }).fail(function(error) {
            common.unloading();
            common.alert('无法连接服务器！');
        });
    },

    /**
     * DataTable表格数据加载初始化
     * 
     */
    createTable: function() {
        var _this = this;
        var data = [
            {
                "data": 'id', "class":"center"
            },
            {
                "data": 'realName', "class":"center"
            }, 
            {
                "data": 'mobile', "class":"center"
            },
            {
                "data": 'idCardNo', "class":"center"
            },
            {
                "data": 'openid', "class":"center",
                rander: function(data) {
                    if (data == null) {
                        return '未绑定';
                    }
                    return data;
                }
            },
            {
                "data": 'status', "class":"center state",
                render: function(data) {
                    var content;
                    if (data == 0 || data == 1) {
                        content = '<span class="gray">正常</span>';
                    }
                    if (data == 3) {
                        content = '<span class="gray">已删除</span>';
                    }
                    if (data == 2) {
                        content = '<span class="gray">已锁定</span>';
                    }
                    return content;
                }
            },
            {
                "data": null,
                "class":"right noselect nowrap",
                render: function(data) {
                    var edit = '<a class="btn-text noselect blue" href="/salesman/edit?id=' + data.id + '">修改</a>　';
                    var freeze;
                    if (data.status == 0 || data.status == 1) {
                        freeze = '<span class="btn-text noselect freeze" data-status="2" data-id="' + data.id + '">锁定</span>　';
                    }
                    if (data.status == 2) {
                        freeze = '<span class="btn-text noselect freeze" data-status="0" data-id="' + data.id + '">解锁</span>　';
                    }
                    var del = '<span class="btn-text noselect del gray" data-id="' + data.id + '">删除</span>';
                    return edit + freeze + del;
                }
            }
        ];
        $('#salesmanTable').DataTable({
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
                "url": "/salesman/get"
            },
            "columns": data,

            initComplete: function() {
                _this.bindEvent();
                common.autoHeight();
            },

            drawCallback: function() {
                _this.bindEvent();
                common.autoHeight();
            }
        });
    },

    /**
     * 编辑界面初始化
     * 
     */
    edit: function() {
        var self = this;
        $("#btnSave").on("click", function() {
            if (beforeSubmitAct()) {
                self.save();
            }
        });
        $("#btnBack").on("click", function() {
            history.back();
        });
    },

    /**
     * 保存业务员信息
     * 
     */
    save: function() {
        var editId = $('#salesman_id').val();
        var params = {};
        $('form input, form select').each(function(){
            var key = $(this).attr('name');
            var val = $(this).val();
            if ($.trim(key) != '') {
                params[key] = $.trim(val);
            }
        });
        if (editId) {
            params.salesman_id = editId;
            $.post('/salesman/update', params, function(resp) {
                if (resp.errcode === 0 || resp.errcode === '0') {
                    location.replace('/salesman');
                } else {
                    common.alert(resp.errmsg);
                }
            }).error(function(error) {
                common.alert('无法连接服务器！');
            });
        } else {
            $.post('/salesman/store', params, function(resp) {
                if (resp.errcode === 0 || resp.errcode === '0') {
                    location.replace('/salesman');
                } else {
                    common.alert(resp.errmsg);
                }
            }).error(function(error) {
                common.alert('无法连接服务器！');
            });
        }
    }
};