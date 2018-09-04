var work = {
	init:function(){
		var _this=this;
        _this.createTable();
	},
    createTable:function(){
        var _this = this;
        $('#roleTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "ordering": false,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "order":[[0,'desc']],
            "info":     true,
            "searching":false,
            "ajax": {
                "url":"/workorder/roledata",
                'type':"POST"
            },
            "columns": [
                {"data":"id","class":"center"},
                {"data":null,"class":"center",
                    "render":function (data,type,row){
                        return data.sRole;
                    }
                },
                {"data":"pCode","class":"center"},
                {"data":"name","class":"center"},
                {"data":"phoneNum","class":"center"},
                {"data":"mail","class":"center"},
                {"data":null,"class":"center",
                    "render":function(data,type,row){
                        if(data.status == 0){
                            return "正常";
                        }
                        if(data.status == 1){
                            return '<font color="red">锁定</font>';
                        }
                    }
                },
                {"data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.status == 0){
                            var sta = '<a class="btn-text red lock" onclick="work.lock('+data.id+')">锁定</a>';
                        }
                        if(data.status == 1){
                            var sta = '<a class="btn-text blue unlock" onclick="work.unlock('+data.id+')">解锁</a>';
                        }
                        var html = '<a href="/">'+sta+'</a>';
                        html += '&nbsp;&nbsp;<a href="/workorder/wr_edit/'+data.id+'">编辑</a>';
                        html += '&nbsp;&nbsp;<a class="delRole" dataid="'+data.id+'">删除</a>';
                        return html;
                    }
                }
            ],
            "initComplete": function () {
                _this.click();
                common.autoHeight();
            },
            "drawCallback":function(){
                _this.click();
                common.autoHeight();
            }
            
        });
    },
    click:function(){
        $("#btnAdd").off().on('click',function(){
            window.location.href = "/workorder/wr_add";
            return;
        });
        $(".delRole").off().on('click',function(){
            var id = $(this).attr('dataid');
            console.log(id);
            $.post('/workorder/del_role',{id:id},function(res){
                if(res.errcode == 0){
                    common.alert('删除成功！',function(r){
                        console.log(r);
                        if(r == 1){
                            window.location.reload();
                            return;
                        }
                    });
                    return;
                }else{
                    common.alert('删除失败，请刷新页面后！');
                    return;
                }
            });
        });
    },
    lock:function(id){
        $.post('/workorder/lock_role',{id:id},function(res){
            if(res.errcode == 0){
                common.alert('操作成功！',function(r){
                    window.location.reload();
                });
            }else{
                common.alert('操作失败，请刷新页面后再试！');
                return;
            }
        });
    },
    unlock:function(id){
        $.post('/workorder/unlock_role',{id:id},function(res){
            if(res.errcode == 0){
                common.alert('操作成功！',function(r){
                    window.location.reload();
                });
            }else{
                common.alert('操作失败，请刷新页面后再试！');
                return;
            }
        });
    }
};
$(function(){
	work.init();
});