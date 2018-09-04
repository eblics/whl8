/// <reference path="D:/SOFTWARE/typings/jquery/jquery.d.ts" />
//全局变量定义
var dataurl = '/tag/get_list';//请求数据的url
//执行初始化
$(function(){
    Init.init();
});
var Init = {
    init:function(){
        this.createTable();
    },
    createTable:function(){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": true,//排序
            "order":[[0,'desc']],
            "pageLength": 100,
            "processing": true,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "serverSide":false,//开启服务器分页
            "ajax":{
                url:dataurl,//请求数据地址
                type:"POST",//请求方式
            },
            "columns": [
                        {"data":"tagId","class":"center"},
                        {"data":"name","class":"left"},
                        {"data":"count","class":"center"},
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                var html='<span class="btn-text noselect blue btn-edit" data-id="'+row.id+'">编辑</span> &nbsp;&nbsp; ';
                                if(row.count<100000){
                                    html+='<span class="btn-text noselect blue btn-delete" data-id="'+row.id+'">删除</span>';
                                }
                                return html;
                            }
                        }
                      ],
            "initComplete": function () {
                common.autoHeight();
                Init.btnEvt();
            },
            "drawCallback":function(){
                common.autoHeight();
            }
        };
        this.table=$('#tagTable').dataTable(config);
    },
    btnEvt:function(){
        $('#tagTable tbody td .btn-edit').off('click').on("click",function(){
            var id=$(this).attr('data-id');
            location.href='/tag/edit/'+id;
        });
        $('#tagTable tbody td .btn-delete').off('click').on("click",function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    $.post('/tag/delete',{'id':id},function(d){
                        common.unloading();
                        if(d.errcode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#tagTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        }else{
                            if(d.errmsg==false){
                                common.alert('ID为0/1/2的标签不能删除');
                            }else{
                                common.alert('删除失败');
                            }
                        }
                    },'json');
                }
            });
        });
    }
};