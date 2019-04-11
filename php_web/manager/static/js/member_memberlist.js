//全局变量定义
var dataurl = '/member/get_memberlist';//请求数据的url
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
            "ordering": false,//关闭排序
            "processing": true,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "serverSide":true,//开启服务器分页
            "ajax":{
                url:dataurl,//请求数据地址
                type:"POST",//请求方式
            },
            "columns": [
                        {"data":"userId","class":"center"},
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                if(data.headimgurl){
                                    return '<img style="width:40px;height:40px;border-radius:50%"; src="'+data.headimgurl+'">';
                                }else{
                                    return '<div><img style="width:40px;height:40px;border-radius:50%"; src="/static/images/zanwu.png"></div>';
                                }

                            }
                        },
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                if(data.nickName){
                                    return '<div><a class="btn-text noselect blue" href="/reporting/show_user_info/'+data.userId+'" target="_blank">'+ common.cutString(data.nickName,14) +'</a></div>';
                                }else{
                                    return '<div>红码用户</div>';
                                }

                            }
                        },
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                if(data.realName){
                                    return '<div><span title="'+data.realName+'">'+common.cutString(data.realName,14)+'</span></div>';
                                }else{
                                    return '<div>未知</div>';
                                }

                            }
                        },
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                if(data.sex=='1'){
                                    return '<div>男</div>';
                                }else if(data.sex=='2'){
                                    return '<div>女</div>';
                                }else{
                                    return '<div>未知</div>';
                                }

                            }
                        },
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                return '<div>'+data.mobile+'</div>';
                            }
                        },
                        {
                            "data":null,"class":"center",
                            "render": function (data,type,row) {
                                if(data.address){
                                    return '<div><span title="'+data.address+'">'+common.cutString(data.address,30)+'</span></div>';
                                }else{
                                    return '<div>未填写</div>';
                                }

                            }
                        }
                        // ,
                        // {
                        //     "data":null,"class":"center",
                        //     "render": function (data,type,row) {
                        //         if(data.subscribe_time){
                        //             return '<div>'+data.subscribe_time+'</div>';
                        //         }else{
                        //             return '<div>未知</div>';
                        //         }

                        //     }
                        // },
                        // {
                        //     "data":null,"class":"center",
                        //     "render": function (data,type,row) {
                        //         if(data.createTime!='1970-01-01 08:00:00'){
                        //             return '<div>'+data.createTime+'</div>';
                        //         }else{
                        //             return '<div>未知</div>';
                        //         }

                        //     }
                        // }
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
            }
        };

        this.table=$('#memberTable').dataTable(config);
    }
};