var Init = {
    init:function(){
        var param=$("#param").val();
        this.createTable(JSON.parse(param));
    },
    createTable:function(params){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": true,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "serverSide":true,//开启服务器分页
            "ajax":{
                url:'/charts/get_userrank_scan',//请求数据地址
                type:"POST",//请求方式
                data:params!=undefined?params:{},//携带参数
            },
            "columns": [
                        {"data":"userId","class":"center"},
                        {"data":"nickName","class":"center"},
                        {"data":"date","class":"center"},
                        {"data":"name","class":"center"},
                        {"data":"batchNo","class":"center"},
                        {"data":"code","class":"center"}
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
            }
        };

        this.table=$('#mainTable').dataTable(config);
    }
};
$(function(){
    Init.init();
});