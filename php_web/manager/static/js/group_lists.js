/* global common */
var groupLists = {
    init:function(){
        var _this=this;
        _this.createTable();
    },
    btnState:function(){
        $('#groupTable tbody td .btn-view').off('click').on("click",function(){
            var id=$(this).attr('data-id');
            var groupName=$(this).parent('td').parent('tr').find('.groupName').text();
            common.transDialog(function(callback){
                var t=setTimeout(common.loading,1000);
                $.post('/group/data_member',{'id':id},function(d){
                    clearTimeout(t);
                    common.unloading
                    if(d.errcode==0){
                        var html='<div class="trans_dialog_group_name">'+groupName+'</div>';
                        html+='<ul class="trans_dialog_member_list">';
                        $(d.data).each(function(index,element){
                            var style="";
                            if(element.role==1){
                                style='master';
                            }
                            html+='<li class="'+style+'"><span>'+element.userId+'</span><img src="'+element.headImage+'"/><strong>'+element.nickName+'</strong></li>'
                        });
                        html+='</ul>';
                        callback(html);
                    }else{
                        common.alert(d.errmsg);
                    }
                },'json');
            });
        });
    },
    createTable:function(){
        var _this=this;
        $('#groupTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "serverSide":true,//开启服务器分页
            "ordering": false,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "order":[[0,'desc']],
            "info":     true,
            "searching":false,
            "ajax": {
                "url":"/group/data",
                'type':"POST"
            },
            "columns": [
                {"data":"id","class":"center nowrap"},
                {"data":"groupImg","class":"center nowrap groupImg",
                    "render":function (data,type,row){
                        return '<img style="width:40px;height:40px;" src="'+data+'">';
                    }
                },
                {"data":"groupName","class":"nowrap groupName"},
                {"data":"memberNum","class":"center nowrap"},
                {"data":"createTime","class":"center nowrap"},
                {"data":"status","class":"center nowrap",
                    "render": function (data,type,row) {
                        var val='<font color=green>正常</font>'
                        if(data==1){
                            val='<font color=gray>已解散</font>'
                        }
                        return val;
                    }
                },
                {"data":null,"class":"center noselect nowrap",
                    "render": function (data,type,row) {
                        var stateBtn= '<span class="btn-text noselect blue btn-view" data-id="'+row.id+'">查看成员</span>';
                        return stateBtn;
                    }
                }
            ],
            "initComplete": function () {
                _this.btnState();
                common.autoHeight();
            },
            "drawCallback":function(){
                _this.btnState();
				common.autoHeight();
            }
            
        });

    }
};
$(function(){
    groupLists.init();
});
