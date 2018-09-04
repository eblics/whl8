/* global common */
var activityLists = {
    init:function(){
        var _this=this;
        _this.createTable();
    },

    showBind:function(){/*关联数据显示--ccz 20160412*/
    	var timeAct = setTimeout("$('#showBind').hide().children().remove();",400);                    //保存当前弹出的div对象
    	var obj = {};				//保存当前div对象，id属性为键值，div属性为保存的div=$(div)
    	var divArr = new Array();	//用于保存obj的数组，键值为obj。id。当获取过的div数据，下次访问时候，不再发送AJAX请求
    	$('#activityTable tbody td span').filter('[name=dataInfo]').off().on('mouseover',function(ev){
    		ev.stopPropagation();
    		$('#showBind').hide().children().remove();	
            var id= $(this).attr('data-id');
            var parentid = $(this).attr('parent-id');
            var div;
            if($.inArray(divArr[id],divArr)>0){
            	div=divArr[id];
            }else{
	            var areaCode = $(this).attr('areaCode');
	            var saletoagc = $(this).attr('saletoagc');
	            var binding = $(this).attr('binding');
	            var url='/activity/relateData';
	            div= $("<div style=''></div>");
	            div.html(div.html()+'<div style="color:#666;padding:4px 0" >ID:'+id+' 的关联条件</div>')
	            var currentDiv;
	            $.post(url,{'id':id,'saletoagc':saletoagc,'areaCode':areaCode,'binding':binding},function(data){
	            	if(data.area){
	            		div.html(div.html()+'<div style="padding:2px 0">活动地区: &nbsp;'+data.area+' </div>');
	            	}
	             	if(data.batchNo){
	             		div.html(div.html()+'<div style="padding:2px 0">乐码批次: &nbsp'+data.batchNo+' </div>');
	            	}
	             	if(data.order_in){
	             		div.html(div.html()+'<div style="padding:2px 0">入库单号: &nbsp'+data.order_in+' </div>');
	            	}
	             	if(data.order_out){
	             		div.html(div.html()+'<div style="padding:2px 0">出库单号: &nbsp'+data.order_out+' </div>');
	            	}
	             	if(data.expire_time){
	             		div.html(div.html()+'<div style="padding:2px 0">过期时间: &nbsp'+data.expire_time+' </div>');
	            	}
	             	if(data.saletoagc){
	             		div.html(div.html()+'<div style="padding:2px 0">销售地区: &nbsp'+data.saletoagc+' </div>');
	            	}
	             	div.html(div.html()+'<div><a id="btnSubInfo'+id+'" class="btnInner btn-blue noselect" onclick="common.showAct('+parentid+','+id+')" >活动详情</a></div>');
		            obj.id =id;
		            obj.div =div.clone(true,true);
		            divArr[obj.id] =obj.div;	 
	            }); 		
            }
            $('<div class="out"></div><div class="in"></div>').appendTo($('#showBind'));//小箭头
            div.appendTo($('#showBind'));
	 		var offset = $(this).offset(); 
	 		$('#showBind').addClass("popup");
	 		$('#showBind').css('top',offset.top - 20);
	 		$('#showBind').css("left",offset.left + $(this).width()+50); 
	 		$('#showBind').show();
            clearTimeout(timeAct);
	 	}).on('mouseout',function(ev){
	 		ev.stopPropagation();
	 		timeAct = setTimeout("$('#showBind').hide().children().remove();",500);
	 	});
    	$('#showBind').on('mouseenter',function(){
    		//$('#showBind').children().remove();
    		//$('#showBind').append(divArr[obj.id]);
    		clearTimeout(timeAct); 
    	}).on('mouseleave',function(){
    		timeAct = setTimeout("$('#showBind').hide().children().remove();",500);
    	});
    },
    delTr:function(){
        $('#activityTable tbody td .del').off('click').on("click",function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    var dataType=parseInt(_this.attr('data-type'));
                    var url='/activity/del';
                    if(dataType==1){
                        url='/activity/delsub';
                    }
                    $.post(url,{'id':id},function(d){
                        common.unloading();
                        if(d.errorCode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#activityTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        }else{
                            common.alert(d.errorMsg);
                        }
                    },'json');
                }
            });
        });
    },
    btnState:function(){
        $('#activityTable tbody td .btn-state-start,#activityTable tbody td .btn-state-stop').off('click').on("click",function(){
            var _this=$(this);
            var thisTxt=$(this).text();
            common.confirm('确定'+thisTxt+'吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    var dataType=parseInt(_this.attr('data-type'));
                    var act='start';
                    var cellData=1;
                    if(_this.hasClass('btn-state-stop')){
                        act='stop';
                        cellData=2;
                    }
                    var url='/activity/'+act+'/'+dataType;
                    $.post(url,{'id':id},function(d){
                        var stateCell=_this.parent('td').parent('tr').find('td.state');
                        common.unloading();
                        if(d.errorCode==0){
                            if(cellData==1){
                                _this.removeClass('btn-state-start').addClass('btn-state-stop').text('停用');
                                stateCell.html('<span class="green">启用</span>');
                            }else{
                                _this.removeClass('btn-state-stop').addClass('btn-state-start').text('启用');
                                stateCell.html('<span class="red">停用</span>');
                            }
                            common.autoHeight();
                        } else if (d.errorCode === 173) {
                            var caps = 0, amount = 0;
                            for (var i = 0; i < d.data.length; i++) {
                                caps += parseInt(d.data[i].weight);
                                amount += d.data[i].weight * d.data[i].amount;
                            }
                            common.confirm('启用此活动将激活'+ caps +'个瓶盖，'+ amount +'个积分，单瓶成本为'+ amount/caps +'积分，确定启用吗？', function(r2) {
                                if (r2 != 1) {
                                    return;
                                }
                                $.post(url + '/confirm',{'id':id},function(d){
                                    var stateCell=_this.parent('td').parent('tr').find('td.state');
                                    common.unloading();
                                    if(d.errorCode==0){
                                        if(cellData==1){
                                            _this.removeClass('btn-state-start').addClass('btn-state-stop').text('停用');
                                            stateCell.html('<span class="green">启用</span>');
                                        }else{
                                            _this.removeClass('btn-state-stop').addClass('btn-state-start').text('启用');
                                            stateCell.html('<span class="red">停用</span>');
                                        }
                                        common.autoHeight();
                                    } else{
                                        common.alert(d.errorMsg);
                                    }
                                },'json');
                            });
                        } else{
                            common.alert(d.errorMsg);
                        }
                    },'json');
                }
            });
        });
    },
    createTable:function(){
        var _this=this;
        $('#activityTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "ordering": false,
            "order":[[0,'desc']],
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "ajax": {
                "url":"/activity/data"
            },
            "columns": [
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '';
                        }else{
                            return data.id;
                        }
                    }
                },
                {
                    "data":null,
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '<span name="dataInfo" binding="'+data.binding+'" parent-id="'+data.parentId+'" areaCode="'+data.areaCode+'" saletoagc="'+data.saletoagc+'" data-id="'+data.id+'" style="color:#999;padding-left:40px">'+'(ID:'+data.id+') '+data.name+'</span>';
                        }else{
                            return data.name;
                        }
                    }
                }, 

                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '<div style="color:#999;">'+data.startTime+'</div>';
                        }else{
                            return data.startTime;
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '<div style="color:#999;">'+data.endTime+'</div>';
                        }else{
                            return data.endTime;
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center state",
                    "render": function (data,type,row) {
                        if(data.state==0)
                            return '<span class="gray">新建</span>';
                        if(data.state==1)
                            return '<span class="green">启用</span>';
                        if(data.state==2)
                            return '<span class="red">停用</span>';
                        
                    }
                },
                {
                    "data":null,
                    "class":"right noselect nowrap",
                    "render": function (data,type,row) {
                        var stateBtn='';
                        if(data.state==0)
                            stateBtn= '<span class="btn-text noselect blue btn-state-start" data-type="'+(typeof data.parentId!='undefined'?'1':'0')+'" data-id="'+data.id+'">启用</span> &nbsp;&nbsp; ';
                        if(data.state==1)
                            stateBtn= '<span class="btn-text noselect blue btn-state-stop" data-type="'+(typeof data.parentId!='undefined'?'1':'0')+'" data-id="'+data.id+'">停用</span> &nbsp;&nbsp; ';
                        if(data.state==2)
                            stateBtn= '<span class="btn-text noselect blue btn-state-start" data-type="'+(typeof data.parentId!='undefined'?'1':'0')+'" data-id="'+data.id+'">启用</span> &nbsp;&nbsp; ';
                        var addHtml='';
                        var editHtml='<a class="btn-text noselect blue" href="/activity/editsub/'+data.parentId+'/'+data.id+'">修改</a> &nbsp;&nbsp; ';
                        if(!data.parentId){
                            addHtml='<a class="btn-text noselect blue" href="/activity/addsub/'+data.id+'">添加子活动</a> &nbsp;&nbsp; ';
                            editHtml='<a class="btn-text noselect blue" href="/activity/edit/'+data.id+'">修改</a> &nbsp;&nbsp; ';
                        }
                        /*
                         * 客户提的需求 这里隐藏 暂时用不到
                         * else{
                        	addHtml='<a class="btn-text noselect blue" href="/reporting/export_hls_sub_activiey_export/'+data.id+'">导出</a>&nbsp;&nbsp; ';
                        }*/
                        return addHtml+editHtml+stateBtn+'<span class="btn-text noselect del gray" data-type="'+(typeof data.parentId!='undefined'?'1':'0')+'" data-id="'+data.id+'">删除</span>';
                    }
                }
            ],
            "initComplete": function () {
                _this.delTr();
                _this.btnState();
                common.autoHeight();
                _this.showBind();
            },
            "drawCallback":function(){
                _this.delTr();
                _this.btnState();
				common.autoHeight();
				_this.showBind();
            }
        });

    }
};
$(function(){
    activityLists.init();
});
