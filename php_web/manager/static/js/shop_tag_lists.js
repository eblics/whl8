var tag = {

    init: function() {
        this.createTable();
        $('#btnAdd').on('click', function() {
            window.location.href = "/shop/shop_detail";
        });
    },

    deleteTag:function(id){
        common.confirm('确定删除吗？',function(r){
            if(r==1){
                $.post("/shop/delete_tag_data/"+id,function(d){
                    if(d.errcode == 0){
                        common.alert('删除成功',function(e){
                            if(e == 1){
                                location.href = '/shop/tag_lists';
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
        var params = {
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "ordering": true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "order":[[0,'desc']],
            "info":     true,
            "searching":true,
            "ajax": {
                "url": '/shop/get_tag_data'
            },
            "columns": [{
                class: 'center',
                "data": "id"
            }, {
                class: 'center',
                "data": "name"
            }, {
                "data": null,
                "class": "center nowrap",
                "render": function(data) {
                    var html = '<a class="btn-text noselect blue pclick" href="/shop/tag_detail/' + data.id + '">修改</a>';
                    html += '&nbsp;&nbsp;';
                    html += '<a class="btn-text noselect blue delete" onclick="tag.deleteTag(' + data.id + ')">删除</a>';
                    return html;
                }
            }],
            "initComplete": function() {
                
            },
            "drawCallback": function() {
                
            }
        };

        $('#tagTable').DataTable(params);

    }
};
$(function() {
    tag.init();
});