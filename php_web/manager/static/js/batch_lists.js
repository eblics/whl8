/* global common */
var batchLists = {
    init:function(){
        var _this=this;
        _this.createTable();
    },
    showAct:function(){/*活动数据显示--ccz 20160419*/
    	var timeAct = setTimeout(function(){$('#showAct').hide().children().remove();},400);                    //保存当前弹出的div对象
    	var obj = {};				//保存当前div对象，id属性为键值，div属性为保存的div=$(div) 每个div是一个活动
    	var divArr = new Array();	//用于保存obj的数组，键值为obj。id。当获取过的div数据，下次访问时候，不再发送AJAX请求
    	$('#batchTable tbody tr td span').filter('[name=activies]').off().on('mouseover',function(ev){
			/*设置活动的样式，使Main活动在每行起始位置,如果一个乐码有多个主活动，则用'|'分隔
			 *    例如   主活动1/子活动1,子活动2|主活动2/子活动3,子活动4
			 *    idData用相同格式存储活动ID值*/
            clearTimeout(timeAct);
    		ev.stopPropagation();
    		$('#showAct').hide().children().remove();
    		var idArr = $(this).attr('act-id').split('|');
    		var infoArr = $(this).attr('content').split('|');
            var id= $(this).attr('data-id');
            var parentid = '';
            var mainAct = [];
            var mainId = [];
            var div= $("<div id = '"+id+"'></div>");
            if($.inArray(divArr[id],divArr)>0){
            	div=divArr[id];
            }else{
            	for(var i=0;i<idArr.length;i++){//对主活动
            		mainAct[i] = new Array();
            		mainId[i] = new Array();
            		//主活动1/子活动1,子活动2
            		var infoM = infoArr[i].split('/');
            		var idM = idArr[i].split('/');
            		mainAct[i][0] = infoM[0];
            		mainAct[i][1] = infoM[1].split(',');//子活动
            		mainId [i][0] =idM[0];
            		mainId[i][1]= idM[1].split(','); 
            		div.html(div.html()+'<div style="background-color: #fff;color: #666;padding:3px;">'+mainAct[i][0]+"</div>");
                    for(var j=0;j<mainAct[i][1].length;j++){//子活动
                    	div.html(div.html()+
                    			'<div style="padding:2px" class="link-gray btn-gray noselect" onclick="common.showAct('+mainId [i][0]+','+mainId[i][1][j]+')"><span id="btnAdd" font-size="8px">&nbsp;&nbsp;'+mainAct[i][1][j]+'</span></div>');
                    }
            	}
                div.html(div.html()+'<div style="padding:3px;"></div>');
	            obj.id =id;
	            obj.div =div.clone(true,true);
	            divArr[obj.id] =obj.div;
            }
            $('<div class="out"></div><div class="in"></div>').appendTo(showAct);
            div.appendTo(showAct);
	 		var offset = $(this).offset(); 
	 		$('#showAct').addClass("popup");
	 		$('#showAct').css('top',offset.top - 20);
	 		$('#showAct').css("left",offset.left + $(this).width() + 10); 
	 		$('#showAct').show();
	 	}).on('mouseout',function(ev){
	 		ev.stopPropagation();
	 		timeAct = setTimeout(function(){$('#showAct').hide().children().remove();},500);
	 	});
    	$('#showAct').on('mouseenter',function(){
    		clearTimeout(timeAct); 
    	}).on('mouseleave',function(){
    		timeAct = setTimeout(function(){$('#showAct').hide().children().remove();},500);
    	});
    },

    bindTip: function(target) {
        var tipLooper,
            closeTooltip = function() {
                tipLooper = setTimeout(function() {
                    $('#oprate_time_tip').hide().children().remove();
                }, 500);
            },
            openTooltip = function(object) {
                clearTimeout(tipLooper);
                var offset = object.offset();
                $('#oprate_time_tip').css('top', offset.top - 20);
                $('#oprate_time_tip').css("left", offset.left + object.width() + 10);
                $('#oprate_time_tip').show();
            };
        $(target).off().on('mouseover', function(event) {
            console.log($(this));
            var 
            title = $(this).attr('data-title'), 
            content = $(this).attr('data-content'),
            div = ' <div class="out"></div>';
            div += '    <div class="in"></div>';
            div += '    <div class="showtitle" >' + title + '</div>';
            div += '    <div class="showcontent">' + content + '</div>';
            $('#oprate_time_tip').html(div);
            openTooltip($(this));
        }).on('mouseout',function(event){
            closeTooltip();
        });
        
        $('#oprate_time_tip').on('mouseenter', function() {
            clearTimeout(tipLooper);
        }).on('mouseleave', function() {
            closeTooltip();
        });
    },

    delTr:function(){
        $('#batchTable tbody td .del').off('click').on("click",function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    $.post('/batch/del_batch',{'id':id},function(d){
                        common.unloading();
                        if(d.errorCode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#batchTable').DataTable();
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
        $('#batchTable tbody td .btn-state-start,#batchTable tbody td .btn-state-stop').off('click').on("click",function(){
            var _this=$(this);
            var thisTxt=$(this).text();
            common.confirm('确定'+thisTxt+'吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    var act='start';
                    var cellData=1;
                    if(_this.hasClass('btn-state-stop')){
                        act='stop';
                        cellData=2;
                    }
                    var url='/batch/'+act;
                    $.post(url,{'id':id},function(d){
                        var stateCell=_this.parent('td').parent('tr').find('td.state');
                        common.unloading();
                        if(d.errorCode==0){
                            if(cellData==1){
                                _this.removeClass('btn-state-start').addClass('btn-state-stop').text('停用');
                                stateCell.html('<font color=green>激活</font>');
                            }else{
                                _this.removeClass('btn-state-stop').addClass('btn-state-start').text('激活');
                                stateCell.html('<font color=red>停用</font>');
                            }
                            common.autoHeight();
                            location.reload();
                        }else{
                            common.alert(d.errorMsg);
                        }
                    },'json');
                }
            });
        });
        $('#batchTable tbody td .get_scan_num').off('click').on("click",function(){
            var _this=$(this);
            var thisId=$(this).attr('data-batchid');
            var thisTotalNum=$(this).attr('data-totalnum');
            _this.html('<img src="/static/images/loading-mini.gif" />');
            batchLists.getBatchScanNum(thisId,function(d){
                if(d.errcode!=0){
                    common.alert(d.errmsg);
                    _this.html('查看');
                    return;
                }
                _this.html('<font color=red>'+d.data.scanNum+'</font>/<font color=green>'+(thisTotalNum-d.data.scanNum)+'</font>');
            });
        });
        $('#batchTable .get_scan_num_all').off('click').on("click",function(){
            clearInterval(window.getScanT);
            window.getScanNums=$('.get_scan_num').length;
            window.getScanCount=0;
            window.getScanT=setInterval(function(){
                if(window.getScanCount>=window.getScanNums-1) clearInterval(window.getScanT);
                $('.get_scan_num').eq(window.getScanCount).click();
                window.getScanCount++;
            },600);
        });
        
    },
    stateFilter: function () {
     	var filter = '<div id ="filter" class="dataTables_length" style="padding:15px 15px 15px 40px">';
        filter += '     <label>状态筛选  ';
        filter += '       <select id ="status_select">';
        filter += '         <option index="-1" value="全部">全部</option>';
        filter += '         <option index="0" value="申请">申请</option>';
        filter += '         <option index="1" value="激活">激活</option>';
        filter += '         <option index="2" value="停用">停用</option>';
        filter += '       </select>';
        filter += '     </label>';
        filter += '   </div>';
    	$(filter).insertAfter($('#batchTable_length'));

    	var table = $('#batchTable').DataTable(), searchColumn = 6;
        $('#status_select').off().on('change', function() {
             var val = $(this).val();
             if (val == "全部") {
                table.ajax.url('/batch/data').load();
            	table.column(searchColumn).search('', false, false).draw();
             } else {
            	table.ajax.url('/batch/data?state=' + $(this).children(':selected').attr('index')).load();
            	// table.column(searchColumn).search(val ? '^' + val : '', true, true).draw();
             }
        });
        table.column(searchColumn).search('', false, false).draw();
    },

    getBatchScanNum: function (batchId,callback) {
     	$.post('/batch/data_batch_scannum/'+batchId,{},function(d){
             callback(d);
        },'json');
    },
    createTable:function(){
        var _this=this;
        _this.dataTable =
        $('#batchTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "ordering": true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "order":[[0,'desc']],
            "info":     true,
            "searching":true,
            "ajax": {
                "url":"/batch/data"
            },
            "columns": [
                { // 编号
                    "data":"id",
                    "class":"center nowrap"
                },
                { // 乐码批次
                    "data":null,
                    "render":function (data,type,row){
                        var withPubCode = '否';
                        if (data.ifPubCode == 1) {
                            withPubCode = '是';
                        }
                        var content = '<span class="item-batch-no" data-title="是否包含明码：' + withPubCode + '" data-content="过期时间：' + data.expireTime.substring(0, 10) + '">' + 
                            data.batchNo + '</span>';
                        return content;
                    }
                },
                { // 数量
                    "data":"len",
                    "class":"center nowrap"
                },
                { // 关联的活动
                	"data":null,"class":"actInfo nowrap",
                	"render":function(data,type,row){
                		var sdata = data.actInfo;
                		if(data.actInfo.length>9){
                			return '<span name="activies" data-id="'+data.id+'" act-id="'+data.idData+'" content="'+sdata+'">'+data.actInfo.substring(0,6)+'...</span>';
                		}else{
                			return '<span name="activies" data-id="'+data.id+'" act-id="'+data.idData+'" content="'+sdata+'">'+data.actInfo+'</span>';
                		}
                	}
                },
                { // 关联产品
                    "data":null, "class": "actInfo nowrap", "width": "35px", 
                    "render":function(data, type, row){
                        var sdata = data.category_name;
                        var content = '<span data-id="' + data.id + '" act-id="' + data.idData + '" content="' + sdata + '">';
                        if (data.category_name == null) {
                            content += ' - </span>';
                        } else if (data.product_name == null) {
                            var category_name_str = data.category_name;
                            if (category_name_str.length > 9) {
                                category_name_str = category_name_str.substring(0, 6) + '...';
                                content = '<span class="item-product" data-title="分类：'+ data.category_name +'" data-content="产品：没有关联产品">' + category_name_str + '</span>';
                            } else {
                                content = '<span data-id="' + data.id + '">' + category_name_str + '</span>';
                            }
                        } else {
                            var str = data.category_name + '/' + data.product_name;
                            if (str.length > 9) {
                                str = str.substring(0, 6) + '...';
                                content = '<span class="item-product" data-title="分类：' + data.category_name + '" data-content="产品：' + data.product_name + '" data-id="' + data.id + '">'+ str + '</span>';
                            } else {
                                content = '<span data-id="' + data.id + '">'+ str + '</span>';
                            }
                        }
                        return content;
                    }
                },
                { // 已扫和剩余
                    "data":null,"class":"center nowrap", "width": "35px", 
                    "orderable": false,
                    "render":function(data,type,row){
                        var html='<span class="btn-text noselect blue get_scan_num" data-batchid="'+data.id+'" data-totalnum="'+data.len+'">查看</span>';
                        return html;
                    }
                },
                { // 批次状态
                    "data":null,"class":"center state nowrap",
                    "orderable": false,
                    "render":function(data,type,row){
                        var val='';
                        if(data.state==0)
                        	val='<font data-title="申请时间：" data-content="'+data.createTime+'" color=gray>申请</font>';
                        if(data.state==1) 
                        	val='<font data-title="激活时间：" data-content="'+data.activeTime+'" color=green>激活</font>';
                        if(data.state==2) 
                        	val='<font data-title="停用时间：" data-content="'+data.stopTime+'" color=red>停用</font>';
                        return val;
                    }
                },
                { // 激活时间
                    "data":null,"class":"center nowrap", "width": "35px", 
                    "render":function(data,type,row){
                        var html='<span">' + (data.activeTime != null ? data.activeTime: '未激活') + '</span>';
                        return html;
                    }
                },
                { // 操作
                    "data":null,
                    "orderable": false,
                    "class":"center noselect nowrap",
                    "render": function (data,type,row) {
                        var stateBtn='';
                        if(row.state==0 || row.state==2)
                            stateBtn= '<span class="btn-text noselect blue btn-state-start" data-id="'+row.id+'">激活</span> &nbsp;&nbsp; ';
                        if(row.state==1)
                            stateBtn= '<span class="btn-text noselect blue btn-state-stop" data-id="'+row.id+'">停用</span> &nbsp;&nbsp; ';
                        return stateBtn+'<a class="btn-text noselect blue" href="/batch/download/'+row.mchId+'/'+row.id+'">下载</a> &nbsp;&nbsp; <a class="btn-text noselect blue" href="/batch/edit/'+row.id+'">修改</a> &nbsp;&nbsp; <span class="btn-text noselect del gray" data-id="'+row.id+'">删除</span>';
                    }
                }
            ],
            "initComplete": function () {
                _this.delTr();
                _this.btnState();
                common.autoHeight();
                _this.showAct();
                _this.bindTip('#batchTable .state font');
                _this.bindTip('#batchTable .item-batch-no');
                _this.bindTip('#batchTable .item-product');
                _this.stateFilter();
            },
            "drawCallback":function(){
                _this.delTr();
                _this.btnState();
				common.autoHeight();
				_this.showAct();
                _this.bindTip('#batchTable .state font');
                _this.bindTip('#batchTable .item-batch-no');
                _this.bindTip('#batchTable .item-product');
            }
            
        });

    }
};
$(function(){
    batchLists.init();
});
