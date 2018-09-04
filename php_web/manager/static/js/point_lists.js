/* global common */
var pointLists = {
    init: function() {
        var _this = this;
        _this.createTable();
    },
    delTr: function() {
        $('#pointTable tbody td .del').off('click').on("click", function() {
            var _this = $(this);
            common.confirm('确定删除吗？', function(r) {
                if (r == 1) {
                    common.loading();
                    var id = _this.attr('data-id');
                    var dataType = parseInt(_this.attr('data-type'));
                    var url = '/point/del';
                    if (dataType == 1) {
                        url = '/point/delsub';
                    }
                    $.post(url, { 'id': id }, function(d) {
                        common.unloading();
                        if (d.errcode == 0) {
                            _this.parent('td').parent('tr').addClass('selected');
                            var table = $('#pointTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        } else {
                            common.alert(d.errmsg);
                        }
                    }, 'json');
                }
            });
        });
    },
    createTable: function() {
        var _this = this;
        $('#pointTable').on('xhr.dt', function(e, settings, json, xhr) {
            window.dataList = json.data;
        }).DataTable({
            "language": { "url": "/static/datatables/js/dataTables.language.js" },
            "paging": true,
            "ordering": false,
            "order": [
                [0, 'desc']
            ],
            "info": true,
            "stateSave": true, //保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching": false,
            "ajax": {
                "url": "/point/data"
            },
            "columns": [{
                    "data": null,
                    "class": "center",
                    "render": function(data, type, row) {
                        if (data.parentId) {
                            return '';
                        } else {
                            return data.id;
                        }
                    }
                }, {
                    "data": null,
                    "render": function(data, type, row) {
                        if (data.parentId) {
                            return '<div style="color:#999;padding-left:40px">' + '(ID:' + data.id + ') ' + data.name + '</div>';
                        } else {
                            return data.name;
                        }

                    }
                }, {
                    "data": null,
                    "class": "center",
                    "render": function(data, type, row) {
                        if (data.parentId) {
                            return data.amount;
                        } else {
                            return '';
                        }

                    }
                }, {
                    "data": "probability",
                    "class": "center",
                    "render": function(data, type, row) {
                        if (typeof data== 'undefined' || typeof row.parentId == 'undefined') {
                            var probabilityArr = [];
                            for (var i = 0; i < window.dataList.length; i++) {
                                if (typeof window.dataList[i].parentId != 'undefined') {
                                    if (window.dataList[i].parentId == row.id) {
                                        if (window.dataList[i].remainNum > 0) {
                                            probabilityArr.push(window.dataList[i].probability);
                                        }
                                    }
                                }
                            }
                            var lastPro = 1;
                            var xpro = 1;
                            for (var i = 0; i < probabilityArr.length; i++) {
                                xpro *= parseFloat((1 - probabilityArr[i]).toPrecision(12));
                            }
                            lastPro -= parseFloat(xpro.toPrecision(12));
                            var lastVal = '综合（' + (lastPro * 100).toFixed(3) + '%）';
                            return lastVal;
                        }
                        var val = parseFloat((data * 100).toPrecision(12)).toString();
                        return '<div style="color:#999;">' + val + '%</div>';
                    }
                }, {
                    "data": null,
                    "class": "center",
                    "render": function(data, type, row) {
                        if(typeof data.totalNum=='undefined'){
                            var totalNum = 0;
                            var remainNum = 0;
                            for (var i = 0; i < window.dataList.length; i++) {
                                if (typeof window.dataList[i].parentId != 'undefined') {
                                    if (window.dataList[i].parentId == row.id) {
                                        totalNum += Number(window.dataList[i].totalNum);
                                        remainNum += Number(window.dataList[i].remainNum);
                                    }
                                }
                            }
                            val = '总计（' + totalNum + ' / <font color=red>' + remainNum + '</font>）';
                            return val;
                        }
                        var val = data.totalNum + ' / <font color=red>' + data.remainNum + '</font>';
                        return '<div style="color:#999;">' + val + '</div>';
                    }
                }, {
                    "data": null,
                    "class": "right noselect nowrap",
                    "render": function(data, type, row) {
                        var add = '';
                        var edit = '<a class="btn-text noselect blue" href="/point/edit/' + data.id + '">修改</a> &nbsp;&nbsp; ';
                        var del = '<span class="btn-text noselect del gray" data-id="' + data.id + '">删除</span>';
                        if (typeof data.parentId == 'undefined') {
                            add = '<a class="btn-text noselect blue" href="/point/addsub/' + data.id + '">添加分级积分</a> &nbsp;&nbsp; ';
                        }
                        if (data.parentId) {
                            edit = '<a class="btn-text noselect blue" href="/point/editsub/' + data.parentId + '/' + data.id + '">修改</a> &nbsp;&nbsp; ';
                            del = '<span class="btn-text noselect del gray" data-type="' + (typeof data.parentId != 'undefined' ? '1' : '0') + '" data-id="' + data.id + '">删除</span>';
                        }
                        return add + edit + del;
                    }
                }
            ],
            "initComplete": function() {
                _this.delTr();
                common.autoHeight();
            },
            "drawCallback": function() {
                _this.delTr();
                common.autoHeight();
            }
        });

    }
};
$(function() {
    pointLists.init();
});