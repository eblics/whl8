/**
 * 我的应用界面
 *
 * @author shizq
 */
 var pageMyApp = {

    init: function() {
        this.loadTable();
    },

    bindEvent: function() {
        // -----------------------------------------------------------
        // 企业编辑我的应用
        $('#myAppTable td .btn-edit').click(function() {
            var app_inst_id = $(this).prop('id');
            location.href = '/myapp/edit/' + app_inst_id;
        });

        // -----------------------------------------------------------
        // 企业启用应用
        $('#myAppTable td .btn-enable').click(function() {
            var app_inst_id = $(this).prop('id');
            common.confirm('确认已经进行配置并启用？', function(confirm) {
                if (confirm) {
                    $.post('/myapp/enable', {"app_inst_id": app_inst_id}, function(resp) {
                        if (! resp.errcode) {
                            location.reload();
                        } else {
                            common.alert(resp.errmsg + '！');
                        }
                    }, 'json').error(function(err) {
                        common.alert('无法连接服务器！');
                    });
                }
            });
        });

        // -----------------------------------------------------------
        // 企业停用应用
        $('#myAppTable td .btn-disable').click(function() {
            var app_inst_id = $(this).prop('id');
            common.confirm('确认停用该应用吗？', function(confirm) {
                if (confirm) {
                    $.post('/myapp/disable', {"app_inst_id": app_inst_id}, function(resp) {
                        if (! resp.errcode) {
                            location.reload();
                        } else {
                            common.alert(resp.errmsg + '！');
                        }
                    }, 'json').error(function(err) {
                        common.alert('无法连接服务器！');
                    });
                }
            });
        });

        // -----------------------------------------------------------
        // 企业删除我的应用
        $('#myAppTable td .btn-del').click(function() {
            var app_inst_id = $(this).prop('id');
            common.confirm('确认删除该应用吗？', function(confirm) {
                if (confirm) {
                    $.post('/myapp/del', {"app_inst_id": app_inst_id}, function(resp) {
                        if (! resp.errcode) {
                            location.reload();
                        } else {
                            common.alert(resp.errmsg + '！');
                        }
                    }, 'json').error(function(err) {
                        common.alert('无法连接服务器！');
                    });
                }
            });
        });
    },

    getMobileUrl: function() {
        var env = location.host;
        if (env == 'dev.www.lsa0.cn') {
            host = 'http://dev.m.lsa0.cn';
        } else if (env == 'test.www.lsa0.cn') {
            host = 'http://test.m.lsa0.cn';
        } else if (env == 'www.lsa0.cn') {
            host = 'http://m.lsa0.cn';
        }
        return host;
    },

    loadTable: function() {
        var self = this;
        $('#myAppTable').DataTable({
            "language": {
                         "url": "/static/datatables/js/dataTables.language.js"
            },
            "paging":    true,
            "ordering":  false,
            "order":     [[0, 'desc']],
            "info":      true,
            "stateSave": false,
            "searching": false,
            "ajax": {
                "url":   "/myapp/get"
            },
            "columns": [
                {
                    "data": 'inst_id', "class": "center"
                },
                {
                    "data": null, "class": "center title",
                    "render": function(data) {
                        var desc = JSON.parse(data.config).desc;
                        return '<span title="' + (typeof desc == 'undefined' ? '该应用没有描述' : desc) + '">' + JSON.parse(data.config).name + '</span>';
                    }
                }, 
                {
                    "data": 'path', "class": "left url",
                    "render": function(data, type, row) {
                        return self.getMobileUrl() + '/app/' + data + '/index.html?mch_id=' + mchId;
                    }
                },
                {
                    "data": 'status', "class": "center",
                    "render": function(data) {
                        var options;
                        if (data == 0) {
                            options = '<span class="red">停用</span>';
                        } else {
                            options = '<span class="green">启用</span>';
                        }
                        return options;
                    }
                },
                {
                    "data": null, "class": "center",
                    "render": function (data, type, row) {

                        // ------------------------ 启用按钮 -------------------------
                        if (data.status == 0) {
                            options = '<span class="btn-text noselect blue btn-enable"';
                            options += 'id="' + data.inst_id + '">启用</span>';
                        } else {
                            options = '<span class="btn-text noselect blue btn-disable"';
                            options += 'id="' + data.inst_id + '">停用</span>';
                        }
                        options += '&nbsp;&nbsp;&nbsp;';

                        // ------------------------ 编辑按钮 -------------------------
                        options += '<span class="btn-text noselect blue btn-edit"';
                        options += 'id="' + data.inst_id + '">配置</span>';
                        options += '&nbsp;&nbsp;&nbsp;';

                        // ------------------------ 删除按钮 -------------------------
                        options += '<span class="btn-text noselect gray btn-del"';
                        options += 'id="' + data.inst_id + '">删除</span>';
                        return options;
                    }
                }
            ],

            initComplete: function() {
                self.bindEvent();
                common.autoHeight();
            },

            drawCallback: function() {
                common.autoHeight();
            }
        });
    }
 };

 $(function() {
    pageMyApp.init();
 });
