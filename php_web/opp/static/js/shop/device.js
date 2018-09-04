var device = {

    init: function() {
        this.createTable();
    },

    createTable: function() {
        var _this = this;
        var params = $.extend(hls.common.dataTable, {
            "ajax": {
                "url": '/shop/get_device_data'
            },
            "columns": [{
                class: 'center',
                "data": "id"
            }, {
                class: 'center',
                "data": "deviceId"
            }, {
                class: 'center',
                "data": "comment"
            }, {
                class: 'center',
                "data": "major"
            }, {
                class: 'center',
                "data": 'minor'
            }, {
                class: 'center',
                "data": 'state',
                "render": function(data){
                    if (data == 0) {
                        return "<font color='red'>未激活</font>";
                    }
                    return "<font color='green'>已激活</font>";
                }
            }, {
                "data": null,
                "class": "center nowrap",
                "render": function(data) {
                    /*var html = '<a class="btn-text noselect blue pclick" href="/merchant/pre_review?id=' + data.mid + '">预审核</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue" href="/merchant/review?id=' + data.mid + '">审核</a>';
                    html += '&nbsp;&nbsp;';
                    if (data.mstatus == 3) {
                        html += '<a class="btn-text noselect blue frozen" onclick="merchant.active(' + data.mid + ')">激活</a>';
                    } else {
                        html += '<a class="btn-text noselect blue frozen" onclick="merchant.freeze(' + data.mid + ')">冻结</a>';
                    }
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue reset" onclick="merchant.passwd(' + data.mid + ')">重置密码</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue rewhchat" onclick="merchant.rewhchat(' + data.mid + ')">重置授权</a>';
                    html += '&nbsp;&nbsp;<a class="btn-text noselect blue more" data-id="' + data.mid + '">更多</a>';*/
                    //return html;
                    return '<span>WWWW</span>';
                }
            }],
            "initComplete": function() {
                
            },
            "drawCallback": function() {
                
            }
        });

        $('#deviceTable').DataTable(params);

    }
};
$(function() {
    device.init();
});