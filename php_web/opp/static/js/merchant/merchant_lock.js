var lock = {
    init: function() {
        var _this = this;
        _this.click();
        _this.createTable({data:''});
    },
    click:function(){
        var _this = this;
        $('#s-type').change(function(){
            console.log($(this).val());

        });
        $('#bsearch').off().on('click',function(){
            var vk = $('#s-type').find("option:selected").attr('value');
            var vv = $('#svalue').val();
            if(vv.length == 0){
                common.alert('搜索参数不能为空！');
                return;
            }
            //************************

            $.post('/api/merchant/get_user',{vk:vk,vv:vv},function(data){
                _this.createTable(data);
            })
            //************************


            // lock.createTable(vk,vv);
        });
    },
    createTable:function(data){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "lengthChange": false,
            "serverSide":false,//开启服务器分页
            "deferRender": true,
            "data": data.data,
            "columns": [
                {"data":"uid","class":"center"},
                {"data":"unickname","class":"center"},
                {"data":"uopenid","class":"center"},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.uimg){
                            return '<img style="width:40px;height:40px;border-radius:50%"; src="'+data.uimg+'">';
                        }else{
                            return '<div><img style="width:40px;height:40px;border-radius:50%"; src="/static/images/zanwu.png"></div>';
                        }

                    }
                },
                {"data":null,"class":"center",
                    "render":function(data,type,row){
                        if(data.ucstatus == 0){
                            return '已解锁用户';
                        }else if(data.ucstatus == 1){
                            return '封禁';
                        }else{
                            return '正常用户';
                        }
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render": function (data,type,row) {
                        var html1 = '<a class="btn-text noselect blue" onclick="lock.lock('+data.uid+')" ">锁定</a>';
                        var html2 = '<a class="btn-text noselect blue" onclick="lock.unlock('+data.uid+')" ">解锁</a>';
                        if(data.ucstatus == 0){
                            return html1;
                        }else if(data.ucstatus == 1){
                            return html2;
                        }else{
                            return html1;
                        }
                        // return '<a class="btn-text noselect blue" href="/product/edit/'+data.id+'">修改</a> &nbsp;&nbsp; <span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span>';
                    }
                }
                
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            },
            "preDrawCallback": function() {
                common.loading();
            }
        };
        
        this.table=$('#searchTable').dataTable(config); 
    },
    lock:function(id){
        console.log(id);
        $.post('/api/merchant/operation_user',{id:id,lock:"lock"},function(r){
            if(r.errcode == 0){
                common.alert(r.res,function(res){
                    if(res == 1){
                        location.reload();
                        return;
                    }
                });
            }else if(r.errcode == 1){
                common.alert(r.res,function(res){
                    if(res == 1){
                        location.reload();
                        return;
                    }
                });
            }else{
                common.alert('操作失败，具体联系开发人员');
                return;
            }
        },'json');
    },
    unlock:function(id){
        $.post('/api/merchant/operation_user',{id:id,lock:"unlock"},function(r){
            if(r.errcode == 0){
                common.alert(r.res,function(res){
                    if(res == 1){
                        location.reload();
                        return;
                    }
                });
            }else if(r.errcode == 1){
                common.alert(r.res,function(res){
                    if(res == 1){
                        location.reload();
                        return;
                    }
                });

            }else{
                common.alert('操作失败，具体联系开发人员');
                return
            }
        },'json');
    }
};
$(function() {
    lock.init();
});