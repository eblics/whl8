$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            var param=rptbase.getValue();
        	param.pro=$("#proCode").val();
        	param.city=$("#cityCode").val();
            _this.formSubmit(param);
            _this.search();
    },
    search:function(){
        var _this=this;
        //查询按钮请求数据
        $("#getSearch").click(function(){
        	var param=rptbase.getValue();
        	param.pro=$("#proCode").val();
        	param.city=$("#cityCode").val();
            _this.formSubmit(param);
        })
        //监测省份选择下拉选中事件
	   $("#proCode").change(function(){
	   	var proCode=$("#proCode").val();
	   	if(proCode==0){
	   		$("#cityCode").empty();
	   		$("#cityCode").append("<option value='0'>全部</option>");
	   	}else{
    	   	//请求选中省份的市
       	   	$.post('/charts/get_city_list',{proCode:proCode},function(data){
       	   		if(data==''){
       	   			$("#cityCode").hide();
       	   		}else{
       	   			$("#cityCode").show();
       	   		}
       	   		$("#cityCode").empty();
       	   		$("#cityCode").append("<option value='0'>全部</option>");
       	   		for(var i=0; i<data.length; i++){
       	   				var option = "";
       	   				option += "<option value='"+data[i]['code']+"'>"+data[i]['name']+"</option>";
       	   				$("#cityCode").append(option);
       	   		 }
       	   	},'json')
	   	}
	   });
    },
    formSubmit:function(param){
        var _this=this;
        common.loading();
        $("#getDown").show();
        _this.createTable(param);
        _this.getDown(common.getRptRootUrl()+'charts/down_ranking_list',param);
    },
    getDown:function(url,param){
        var _this=this;
        $("#getDown").click(function(){
            rptbase.postDowndata(url,param);
        })
    },
    createTable:function(params){
        var config={
    		"language": {"url": "/static/datatables/js/dataTables.language.js"},
            // "ordering": true,//关闭排序
            "columnDefs": [
                { "orderable": false, "targets": [ 0,1,2 ] }
            ],
            "order": [[ 3, 'desc' ]],
            "processing": false,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "lengthChange": false,
            "serverSide":true,//开启服务器分页
            "deferRender": true,
            "ajax":{
                url:common.getRptRootUrl()+"charts/get_ranking_list",//请求数据地址
                type:"POST",//请求方式
                data:params!=undefined?{param:params}:'',//携带参数
            },
            "columns": [
				{
				    "data":null,"class":"center",
				    "render": function (data,type,row) {
				        if(data.rank_id){
				        	if(data.rank_id==1){
				        		return '<img style="vertical-align:middle;width:25px" src="/static/images/1.png">';
				        	}
				        	if(data.rank_id==2){
				        		return '<img style="vertical-align:middle;width:25px" src="/static/images/2.png">';
				        	}
				        	if(data.rank_id==3){
				        		return '<img style="vertical-align:middle;width:25px" src="/static/images/3.png">';
				        	}
				        	return '<div>'+data.rank_id+'</div>';

				        }else{
				        	return '<div>'+data.rank_id+'</div>';
				        }
				    }
				},
				{
				    "data":null,"class":"center",
				    "render": function (data,type,row) {
				        if(data.nickname){
				            return '<div><a class="btn-text noselect blue" href="/reporting/show_user_info/'+data.userId+'" target="_blank">'+ common.cutString(data.nickname,14) +'</a></div>';
				        }else{
				        	return '<div><a class="btn-text noselect blue" href="/reporting/show_user_info/'+data.userId+'" target="_blank">红码用户</a></div>';
				        }

				    }
				},
				{"data":"userId","class":"center"},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.scanNum){
                            return '<div><a class="btn-text noselect blue rankscan" data-userid="'+data.userId+'" target="_blank">'+ data.scanNum +'</a></div>';
                        }
                    }
                },
                {"data":"transAmount","class":"center"},
                {"data":"pointAmount","class":"center"},
                {"data":"pointUsed","class":"center"}
                // {
                //     "data":null,"class":"center",
                //     "render": function (data,type,row) {
                //         if(data.transAmount){
                //             return '<div><a class="btn-text noselect blue rankscan" data-userid="'+data.userId+'" target="_blank">'+ data.transAmount +'</a></div>';
                //         }
                //     }
                // },
                // {
                //     "data":null,"class":"center",
                //     "render": function (data,type,row) {
                //         if(data.pointAmount){
                //             return '<div><a class="btn-text noselect blue rankscan" data-userid="'+data.userId+'" target="_blank">'+ data.pointAmount +'</a></div>';
                //         }
                //     }
                // }
            ],
            "initComplete": function () {
            	var table = $('#userrank_data').DataTable();
             	var info = table.page.info();
             	$("#total").html(info['recordsTotal']);
                common.autoHeight();
            },
            "drawCallback":function(){
                $(".rankscan").click(function(){
                    var userId=$(this).attr('data-userid');
                    params.userId=userId;
                    rptbase.postDowndata('/charts/show_userrank_scan',params,'_blank');
                })
            	common.unloading();
                common.autoHeight();
            },
            "preDrawCallback": function() {
            	common.loading();
            }
        };

        this.table=$('#userrank_data').dataTable(config);
    }
};
