//全局变量定义
var dataurl = common.getRptRootUrl()+'reporting/card_get_winlist';//请求数据的url
//执行初始化
$(function(){
    Init.init();
});
var Init = {
    init:function(){
    	var id = $('#id').val();
        this.createTable({id:id});
    },
    createTable:function(params){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": true,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "serverSide":false,//开启服务器分页
            "ajax":{
                url:dataurl,//请求数据地址
                type:"POST",//请求方式
                data:params!=undefined?params:{},//携带参数
            },
            "columns": [
                        {"data":"userId","class":"center"},
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                if(data.nickName){
                                    return '<div><a class="btn-text noselect blue" href="/reporting/show_user_info/'+data.userId+'" target="_blank">'+ common.cutString(data.nickName,14) +'</a></div>';
                                }else{
                                    return '未填写';
                                }

                            }
                        },
                        {
                            "data": 'realName',
                            "class":"center",
                            "render": function(data, type, row) {
                                if (data) {
                                    return '<div><span title="'+data+'">'+common.cutString(data,20)+'</span></div>';
                                } else {
                                    return '未填写';
                                }
                            }
                        },
                        {
                            "data": 'mobile',
                            "class":"center",
                            "render": function(data, type, row) {
                                if (data) {
                                    return '<div><span title="'+data+'">'+data+'</span></div>';
                                } else {
                                    return '未填写';
                                }
                            }
                        },
                        {
                            "data": 'address',
                            "render": function(data, type, row) {
                                if (data) {
                                    return '<div><span title="'+data+'">'+common.cutString(data,30)+'</span></div>';
                                } else {
                                    return '未填写';
                                }
                            }
                        },
                        {"data":"date","class":"center"},
                        {
                            "data": 'area',
                            "render": function(data, type, row) {
                                if (data) {
                                    return '<div><span title="'+data+'">'+common.cutString(data,30)+'</span></div>';
                                } else {
                                    return '未填写';
                                }
                            }
                        },
                        {
                            "data": 'sended',"class":"center",
                            "render": function(data, type, row) {
                                if (data==1) {
                                    return '<div>已发放</div>';
                                } else {
                                    return '<div class="red">未发放</div>';
                                }
                            }
                        },
                        {
                            "data": null,"class":"center",
                            "render": function(data, type, row) {
                                if (data.aprocessing==1) {
                                    return '<div>已处理</div>';
                                } else {
                                    return '<button class="chere" id="'+data.aid+'" onclick="btnclick('+data.aid+')" valid='+data.aid+' style="width:46px;height:24px;line-height:24px;font-size:12px;background:#5A8EDD;text-align:center;color:white;border-radius:3px;border:none;">处理</button>';
                                }
                            }
                        }
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
                $('#cardWinlist tbody').children('tr').each(function(){
                    var phonenum = $(this).children('td').eq(3).text();
                });
            }
        };

        this.table=$('#cardWinlist').dataTable(config);
        

    }
};
function btnclick(id){
    common.confirm('确认处理？',function(e){
        if(e == 1){
            $.post('/card/deal_with',{id:id},function(res){
                if(res.data == 1){
                    $("#"+id).hide();
                    $('#'+id).after("<div>已处理</div>");
                    $("#"+id).remove();
                    common.alert('操作成功！');
                    return;
                }else{
                    common.alert('操作失败！');
                    return;
                }
            });
        }else{
            return;
        }
    });
}