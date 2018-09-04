//全局变量定义
var dataurl = common.getRptRootUrl()+'reporting/scan_info_data';//请求数据的url
var viewRewardsUrl = common.getRptRootUrl()+'reporting/getRewards';
//执行初始化
$(function(){
    Init.init();
});
var Init = {
    init:function(){
    	var param = $('#param').val();
        this.createTable({param:param});
    },
    createTable:function(params){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":true,
            "bDestroy": true,
            "serverSide":false,//开启服务器分页
            "ajax":{
                url:dataurl,//请求数据地址
                type:"POST",//请求方式
                data:params!=undefined?params:{},//携带参数
            },
            "columns": [
                        {"data":"userId","class":"center"},
                        {"data":"nickName","class":"center"},
                        {"data":"date","class":"center"},
                        {"data":"name","class":"center"},
                        {"data":"batchNo","class":"center"},
                        {"data":"code","class":"center"},
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                return '<div><a class="btn-text noselect blue viewRewards" href="javascript:;" data-code="'+data.code+'">查看奖励</a></div>';
                            }
                        }
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
                $(".viewRewards").click(function(){
                    var code=$(this).attr('data-code');
                    var html="";
                    html='<div class="title">乐码：'+code+'</div>';
                    common.loading();
                    $.post(viewRewardsUrl,{code:code},function(resp){
                        common.unloading();
                        if(resp.data.length==0){
                            common.alert('未获得任何奖励~~');
                            return false;
                        }
                        resp.data.forEach(function(data){
                            if(data.level=='red'){
                                html+='<div class="top_title">红包</div>';
                            }
                            if(data.level=='card'){
                                html+='<div class="top_title">卡券</div>';
                            }
                            if(data.level=='point'){
                                html+='<div class="top_title">积分</div>';
                            }

                            html+='<table class="table-form">';

                            html+='<thead>';
                            html+='    <th style="text-align:center;font-size:16px;">用户ID</th>';
                            if(data.level=='red'){
                                html+='    <th style="text-align:center;font-size:16px;">金额</th>';
                            }
                            if(data.level=='card'){
                                html+='    <th style="text-align:center;font-size:16px;">名称</th>';
                            }
                            if(data.level=='point'){
                                html+='    <th style="text-align:center;font-size:16px;">积分金额</th>';
                            }
                            html+='    <th style="text-align:center;font-size:16px;">获得时间</th>';
                            html+='</thead>';
                            html+='    <tbody>';
                            html+='    <tr>';
                            html+='        <td align="center" class="name">'+data.userId+'</td>';
                            if(data.level=='red'){
                                html+='        <td align="center" class="name">'+(data.amount/100).toFixed(2)+'</td>';
                            }else{
                                html+='        <td align="center" class="name">'+data.amount+'</td>';
                            }
                            
                            html+='        <td align="center" class="name">'+data.date+'</td>';
                            html+='    </tr>';
                            html+='    </tbody>';
                            html+='</table>';

                            common.transDialog(function(callback) {
                                callback(html);
                            });
                        })
                    },'json')
                })
            }
        };

        this.table=$('#reportingTable').dataTable(config);
    }
};