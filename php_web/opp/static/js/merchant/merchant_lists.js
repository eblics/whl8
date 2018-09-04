var mlists = {
    init: function() {
        var _this = this;
        _this.createTable();
    },
    createTable:function(){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "processing": false,//加载中
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":true,
            "bDestroy": true,
            "lengthChange": true,
            "ajax": {
                "url":"/api/merchant/get_blacklist_data"
            },
            "columns": [
                {
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        return data.uid;
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        return data.uopenid;
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        if(data.uimg){
                            return '<img style="width:40px;height:40px;border-radius:50%"; src="'+data.uimg+'">';
                        }else{
                            return '<div><img style="width:40px;height:40px;border-radius:50%"; src="/static/images/zanwu.png"></div>';
                        }
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        return data.utime;
                    }
                },{
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        return  data.umark;
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<a class="btn-text blue remove" dataid="'+data.id+'">移除</a>';
                    }
                }
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
                mlists.click();
            },
            "preDrawCallback": function() {
                common.loading();
            }
        };
        
        this.table=$('#searchTable').dataTable(config); 
    },
    click:function(){
        $('.remove').off().on('click',function(){
            var _this = $(this);
            var val = _this.attr('dataid');
            var openid = _this.parent().parent().children('td').eq(1).text();
            common.confirm('确定从黑名单移出？',function(r){
                if(r == 1){  
                    $.post('/api/merchant/move_out_blacklist',{id:val,openid:openid},function(res){
                        if(res.result == true){
                            common.alert('移出黑名单成功！');
                            _this.parent().parent().remove();
                            var num = $('#searchTable tbody tr').length;
                            var html = '<tbody><tr class="odd"><td class="dataTables_empty" valign="top" colspan="5">没有数据</td></tr>';
                            if(num == 0){   
                                $('#searchTable thead').after(html);
                                return;
                            }
                        }else{
                            common.alert('我告诉你，出错了！');
                            return;
                        }
                    });
                }
            });
        });
    }
};
$(function() {
    mlists.init();
});