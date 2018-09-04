//全局变量定义
var dataurl = '/reporting/user_scan_daylist_data';//请求数据的url
var ajax_type = 'POST';//ajax请求方式
var downurl = '/reporting/download_user_scan_daylist';//报表下载地址
//执行初始化
$(function(){
    Init.init();
});
var Init = {
    init:function(){
        var start=$('#startDate').val();
        var end=$('#endDate').val();
        this.createTable({from:start,to:end});
        this.createButton();
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
            "serverSide":true,//开启服务器分页
            "ajax":{
                url:dataurl,//请求数据地址
                type:ajax_type,//请求方式
                data:params!=undefined?params:{},//携带参数
            },
            "columns": [
                {"data":"userId","class":"center"},
				{
				    "data":null,"class":"center",
				    "render": function (data,type,row) {
				        if(data.nickName){
				            return '<div><a class="btn-text noselect blue" href="/reporting/show_user_info/'+data.userId+'">'+ data.nickName +'</a></div>';
				        }else{
				            return '<div>'+data.nickName+'</div>';
				        }

				    }
				},
                {"data":"date","class":"center"},
                {
				    "data":null,"class":"center",
				    "render": function (data,type,row) {
				        if(data.scan_num){
				            return '<div><a class="btn-text noselect blue" href="/reporting/show_scan_info/'+data.userId+'/'+data.date+'">'+ data.scan_num +'</a></div>';
				        }else{
				            return '<div>'+data.scan_num+'</div>';
				        }

				    }
				},
				{
				    "data":null,"class":"center",
				    "render": function (data,type,row) {
				        if(data.red_amount){
				            return '<div><a class="btn-text noselect blue" href="/reporting/show_redpack_info/'+data.userId+'/'+data.date+'">'+ data.red_amount +'</a></div>';
				        }else{
				            return '<div>'+data.red_amount+'</div>';
				        }

				    }
				},
				{
				    "data":null,"class":"center",
				    "render": function (data,type,row) {
				        if(data.trans_amount){
				            return '<div><a class="btn-text noselect blue" href="/reporting/show_trans_info/'+data.userId+'/'+data.date+'">'+ data.trans_amount +'</a></div>';
				        }else{
				            return '<div>'+data.trans_amount+'</div>';
				        }

				    }
				}
            ],
            "initComplete": function () {
                common.autoHeight();
                var start=$('#startDate').val();
                var end=$('#endDate').val();
                if(params!=undefined){
                    $('#btnDownload').removeClass('btn-disabled');
                    $('#btnDownload').data('range',{from:params.from,to:params.to});
                }

            },
            "drawCallback":function(){
                common.autoHeight();
            }
        };

        this.table=$('#reportingTable').dataTable(config);
    },
    createButton:function(){
        var _this=this;
        $('#btnSearch').click(function(){
            var start=$('#startDate').val();
            var end=$('#endDate').val();
            if(start==''||end==''){
                alert('选择日期不能为空');
                return;
            }
            $('#reportingTable').DataTable().clear();//查询的时候清除表格数据
            $('#reportingTable').DataTable().state.clear();//搜索时清除存储
            _this.createTable({from:start,to:end});//重新绘制表格
        });

        function dateToString(date){
            var year=date.getFullYear();
            var month=date.getMonth()+1;
            var day=date.getDate();
            var str=year+'-'+(month>9?month:'0'+month)+'-'+(day>9?day:'0'+day);
            return str;
        }
        var now=new Date();
        var from=new Date(now.getFullYear(),now.getMonth()-1,now.getDate());
        $('#startDate').val(dateToString(from));
        $('#endDate').val(dateToString(now));
        //$('#btnSearch').triggerHandler('click');

        $('#btnDownload').click(function(){
            if(!$(this).hasClass('btn-disabled')){
                var range=$('#btnDownload').data('range');
                location=downurl+'?from='+range.from+'&'+'to='+range.to;
            }
        });
    }
};