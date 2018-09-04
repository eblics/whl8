var lists = {
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
                "url":"/workorder/listsdata",
                'type':"POST"
            },
            "columns": [
                {"data":"id","class":"center"},
                {"data":null,"class":"center",
                    "render":function (data,type,row){
                        if(data.type == 1){
                            return '<font color="red">投诉</font>';
                        }else if(data.type == 2){
                            return '建议';
                        }else if(data.type == 3){
                            return '使用';
                        }else{
                            return '未知';
                        }
                    }
                },
                {"data":"title","class":"center"},
                {"data":"name","class":"center"},
                {"data":"time","class":"center"},
                {"data":null,"class":"center",
                    "render":function(data,type,row){
                        if(data.rname != null){
                            return data.rname;
                        }else{  
                            return '工单未处理';
                        }
                    }
                },
                {"data":null,"class":"center",
                    "render":function (data,type,row){
                        if(data.status == 0){
                            return '<font color="green">新建</font>';
                        }else if(data.status == 1){
                            return '跟踪中';
                        }else if(data.status == 2){
                            return '关闭';
                        }else{
                            return '其他';
                        }
                    }
                },
                {"data":null,"class":"center",
                    "render":function (data,type,row){
                        var html = '<a class="btn-text blue" href="/workorder/treat_work/'+data.id+'">处理</a>';
                        return html;
                    }
                }  
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
            }
            
        });
    }
};
$(function(){
	lists.init();
});