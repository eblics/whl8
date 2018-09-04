var searchs = {
    init: function() {
        var _this = this;
        _this.click();
        _this.createTable({data:''});
    },
    click:function(){
        var _this = this;
        $('#bsearch').off().on('click',function(){
            var vk = $('#s-type').find("option:selected").attr('value');
            var vv = $('#svalue').val();
            if(vv.length == 0){
                common.alert('搜索参数不能为空！');
                return;
            }
            //************************
            common.loading();
            $.post('/api/merchant/get_user',{vk:vk,vv:vv},function(data){
                common.unloading();
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
                {"data":"id","class":"center"},
                {"data":"nickname","class":"center"},
                {"data":"openid","class":"center"},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.headimgurl){
                            return '<img style="width:40px;height:40px;border-radius:50%"; src="'+data.headimgurl+'">';
                        }else{
                            return '<div><img style="width:40px;height:40px;border-radius:50%"; src="/static/images/zanwu.png"></div>';
                        }

                    }
                },{
                    "data":null,
                    "class":"center",
                    "render":function(data,type,row){
                        if(data.name == 'huanlesaopf'){
                            return '欢乐扫平台';
                        }else{
                            return data.name;
                        }
                    }
                },
                {"data":null,"class":"center",
                    "render":function(data,type,row){
                        if(data.status == 0){
                            return '正常';
                        }
                        if(data.status == 1){
                            return '<font color="red">封禁</font>';
                        }
                        if(data.status == null || data.status == ''){
                            return '正常';
                        }
                    }
                },
                {
                    "data":null,
                    "class":"center",
                    "render": function (data,type,row) {
                        if(data.status == 1){
                            var status = 'id="'+data.id+'" status=1';
                        }
                        if(data.status == null || data.status == '' || data.status == 0){
                            var status = 'id="'+data.id+'" status=0';
                        }
                        var html1 = '<a class="btn-text noselect blue" href="javascript:void(0)"'+status+' onclick="searchs.thisclick('+data.id+')" ">锁定</a>';
                        var html2 = '<a class="btn-text noselect blue" href="javascript:void(0)"'+status+' onclick="searchs.thisclick('+data.id+')" ">解锁</a>';
                        if(data.status == 0){
                            return html1;
                        }else if(data.status == 1){
                            return html2;
                        }else{
                            return html1;
                        }
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
    // 用户拉黑和恢复正常
    thisclick:function(id){
        $("td a").each(function(){
            if($(this).attr('id') == id ){
                var status = $(this).attr('status');
                if(status == 0){
                    var txt = '手动封禁功能不可使用！';
                    common.alert(txt);
                    return;
                }
                if(status == 1){
                    var txt = '确定解锁此用户！';
                }
                var _this = $(this);
                common.confirm(txt,function(r){
                    if(r){
                        $.post('/api/merchant/move_out_blacklists',{id:id,status:status},function(res){
                            if(res.errcode == 0){
                                common.alert('解锁成功！',function(response){
                                    if(response == 1){
                                        _this.parent().parent().remove();
                                        var num = $('#searchTable tbody tr').length;
                                        var html = '<tbody><tr class="odd"><td class="dataTables_empty" valign="top" colspan="5">没有数据</td></tr>';
                                        if(num == 0){   
                                            $('#searchTable thead').after(html);
                                            return;
                                        }
                                    }
                                });
                            }else{
                                common.alert('操作失败，请稍后再试！');
                                return;
                            }
                        })
                    }
                });

            }
        });
        // $.post('/api/merchant/operation_user',{id:id,lock:"lock"},function(r){
        //     if(r.errcode == 0){
        //         common.alert(r.res,function(res){
        //             if(res == 1){
        //                 location.reload();
        //                 return;
        //             }
        //         });
        //     }else if(r.errcode == 1){
        //         common.alert(r.res,function(res){
        //             if(res == 1){
        //                 location.reload();
        //                 return;
        //             }
        //         });
        //     }else{
        //         common.alert('操作失败，具体联系开发人员');
        //         return;
        //     }
        // },'json');
    },
    createNullTable:function(){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":true,
            "bDestroy": true,
            "lengthChange": false,
            "serverSide":false,//开启服务器分页
            "deferRender": true,
            "data": data,
            "columns": [
                {"data":"uid","class":"center"},
                {"data":"unickname","class":"center"},
                {"data":"uopenid","class":"center"},
                {"data":"uimg","class":"center"},
                {"data":"cstatus","class":"center"}
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
        
        this.table=$('#userscan_data').dataTable(config); 
    }
};
$(function() {
    searchs.init();
});