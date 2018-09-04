/* global common */
var multistrategyLists = {
    init: function() {
        var _this = this;
        _this.createTable();
    },
    delTr: function() {
        $('#multistrategyTable tbody td .del').off('click').on("click", function() {
            var _this = $(this);
            common.confirm('确定删除吗？', function(r) {
                if (r == 1) {
                    common.loading();
                    var id = _this.attr('data-id');
                    var dataType = parseInt(_this.attr('data-type'));
                    var url = '/multistrategy/del';
                    if (dataType == 1) {
                        url = '/multistrategy/delsub';
                    }
                    $.post(url, {
                        'id': id
                    }, function(d) {
                        common.unloading();
                        if (d.errorCode == 0) {
                            _this.parent('td').parent('tr').addClass('selected');
                            $('.subdata[data-id=' + id + ']').parent('td').parent('tr').addClass('selected');
                            var table = $('#multistrategyTable').DataTable();
                            table.rows('.selected').remove().draw(false);
                            common.autoHeight();
                        } else {
                            common.alert(d.errorMsg);
                        }
                    }, 'json');
                }
            });
        });
    },
    createTable: function() {
        var _this = this;
        $('#multistrategyTable').on('xhr.dt', function(e, settings, json, xhr) {
            window.dataList = json.data;
        }).DataTable({
            "language": {
                "url": "/static/datatables/js/dataTables.language.js"
            },
            "paging": true,
            "ordering": false,
            "order": [
                [0, 'desc']
            ],
            "info": true,
            "stateSave": true, //保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching": false,
            "ajax": {
                "url": "/multistrategy/data"
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
                        var type = '';
                        if (data.strategyType == 0) type = '红包';
                        if (data.strategyType == 2) type = '乐券';
                        if (data.strategyType == 3) type = '积分';
                        return '<div style="color:#999;padding-left:40px">' + '【' + type + ':' + data.strategyId + '】 ' + data.strategyName + '</div>';
                    } else {
                        return data.name;
                    }
                }
            }, 
            {
                "data": 'avProbability',
                "class": "center",
                "render": function(data, type, row) {
                    if (row.parentId) {
                        return (data * 100).toFixed(3) + '%';
                    } else {
                        var avPro = 0;
                        for (var i = 0; i < window.dataList.length; i++) {
                            if (window.dataList[i].parentId == row.id) {
                                if(window.dataList[i].avProbability>avPro)
                                    avPro=window.dataList[i].avProbability;
                            }
                        }
                        return '综合（' + (avPro * 100).toFixed(3) + '%）';
                    }
                }
            }, {
                "data": null,
                "class": "right noselect nowrap",
                "render": function(data, type, row) {
                    var edit = '<a class="btn-text noselect blue" href="/multistrategy/edit/' + data.id + '">修改</a> &nbsp;&nbsp; ';
                    var del = '<span class="btn-text noselect del gray" data-id="' + data.id + '">删除</span>';
                    if (data.parentId) {
                        return '<input type="hidden" class="subdata" data-id="' + data.parentId + '"/>';
                    }
                    return edit + del;
                }
            }],
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
    multistrategyLists.init();
});