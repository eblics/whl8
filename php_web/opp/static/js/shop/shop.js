var shop = {

    init: function() {
        this.createTable();
        $('#btnSend').on('click', function() {
            window.location.href = "/shop/shop_detail";
        });
    },

    deleteShop:function(id){
        common.confirm('确定删除吗？',function(r){
            if(r==1){
                $.post("/shop/delete_shop_data/"+id,function(d){
                    if(d.errcode == 0){
                        common.alert('删除成功',function(e){
                            if(e == 1){
                                location.href = '/shop/index';
                            }
                        });
                    } else {
                        common.alert(d.errmsg);
                    }
                });
            }
        });
    },
    
    createTable: function() {
        var _this = this;
        var params = $.extend(hls.common.dataTable, {
            "ajax": {
                "url": '/shop/get_shop_data'
            },
            "columns": [{
                class: 'center',
                "data": "id"
            }, {
                class: 'center',
                "data": "name"
            }, {
                class: 'center',
                "data": "area"
            }, {
                class: 'center',
                "data": "address"
            }, {
                class: 'center',
                "data": 'ownerName'
            }, {
                class: 'center',
                "data": 'ownerPhoneNum'
            }, {
                class: 'center',
                "data": 'createTime'
            }, {
                class: 'center',
                "data": 'state',
                "render": function(data) {
                    if (data == 0) {
                        return "<font color='gray'>未启用</font>";
                    }
                    else if (data == 1) {
                        return "<font color='red'>未激活</font>";
                    }
                    else if (data == 2) {
                        return "<font color='green'>已激活</font>";
                    }
                    return "<font color='gray'>未知</font>";
                }
            }, {
                "data": null,
                "class": "center nowrap",
                "render": function(data) {
                    var html = '<a class="btn-text noselect blue pclick" href="/shop/shop_detail/' + data.id + '">编辑</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue delete1" onclick="shop.deleteShop(' + data.id + ')">删除</a>';
                    return html;
                }
            }],
            "initComplete": function() {
                
            },
            "drawCallback": function() {
                
            }
        });

        $('#shopTable').DataTable(params);

    }
};
$(function() {
    shop.init();
});