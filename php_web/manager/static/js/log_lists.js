/* global common */
function stateFilter() {
	var _this=this;
 	var filter = $('<li id ="filterContent" class="filterlist" ></li>');
	filter.append('<label>内容筛选  <select id ="s1" class = "filterState" style="width:125px"><option index="-1" value="all">全部</option></select></label>');
	filter.appendTo($('#filter'));
	var table = $('#logTable').DataTable();
    var select =$('#s1');
    select.off().on( 'change', function () {
    		 $('#daterange').html('').removeClass("active");
             var val = $(this).val();
      		 select.val(val);
             index = this.selectedIndex;
             if(val=="all"){
            	 loglist.createTable();
             }else{
            	 loglist.createTable({"obj":val});
             }
         	var url='/mchoprlog/getOpration';
        	
             $.post(url,{'obj':select.val()},function(d){
             	$('#s2').children().remove();
             	$('#s2').append('<option index="-1" value="all">全部</option>');
             	for(var index in d){
             		$('#s2').append( '<option index="'+index+'" value="'+index+'">'+d[index]+'</option>');
             	}
             },'json');                
        } );
	//动态
	var url='/mchoprlog/getobject';
    $.get(url,function(d){
    	for(var index in d){
    		select.append( '<option index="'+index+'" value="'+index+'">'+d[index]+'</option>');
    	}
    },'json');
}

function oprFilter() {
 	var filter = $('<li id ="filterOpr" class="filterlist"></li>');
	filter.append('<label>操作筛选  <select id ="s2" class = "filterState" style="width:125px"><option index="-1" value="all">全部</option></select></label>');
	filter.insertAfter($('#filterContent'));
	var table = $('#logTable').DataTable();
    var select =$('#s2');
    select.off().on( 'change', function () {
    		 $('#daterange').html('').removeClass("active");
        	 var obj = $("#s1").val();
             var val = $(this).val();
             if( val =="all"){
            	 loglist.createTable({"obj":obj});
             }else{
            	 loglist.createTable({"obj":obj,"opr":val});
             }
        } );
}


function dateFilter() {
	var filter = $('   <li class="filterlist"><img id="date_get" style="cursor:pointer;padding:5px 0 10px;vertical-align:middle" src="/static/images/rili.png"></li>');
	filter.append('<input type="hidden" id="start" value="" ><input type="hidden" id="end" value="" >');
	filter.append('<span id="daterange" ></span>');
	filter.insertAfter($('#filterOpr'));
	$("#daterange").removeClass("active");
	var table = $('#logTable').DataTable();
	var dateRange = new pickerDateRange('date_get', {
    	isTodayValid : true,
    	aRecent14Days : 'aRecent14Days',
    	defaultText : ' 至 ',
    	autoSubmit : false,
    	theme : 'ta',
    	success : function(date) {
    			$('#dateStr').remove();
	    		console.log(date);
	    		var min = date.startDate;
	    		var max = date.endDate==""?min:date.endDate;
	    		if(min.length>0){
		    		var obj = $("#s1").val();
		    		var opr = $("#s2").val();
		    		loglist.createTable({"obj":obj,"opr":opr,"datestart":min,"dateend":max});
		    		$("#daterange").text(min+"至"+max).addClass("active");
	    		}
	    		$('#start').val(min);
	    		$('#end').val(max);  		
    	    }
    	});
}

function ajax_period_get_data(level){
	//时间筛选事件
    var now=new Date();
	var first=new Date(now.getFullYear(),now.getMonth(),01);

	switch(level)
	{
	case 'today':
		
	  break;
	case 'week':
		
	  break;
	case 'month':
		
    	  break;
	default:
		
	}
};

var loglist = {
    init:function(){
        var _this=this;
        _this.createTable({});
    },
   
    createTable:function(params){
        var _this=this;
        _this.dataTable =
        $('#logTable').DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "info":     true,
            "searching":false,
            "serverSide":true,//开启服务器分页
            "bAutoWidth": false,
            "bDestroy": true,
            "ajax": {
                "url":"/mchoprlog/data",
                type:"POST",
                data:params!=undefined?params:{}
            },
            "columns": [
                {"data":"id","class":"center nowrap"},
                {"data":"oprobject","class":"center nowrap"},
                {"data":"opration","class":"center nowrap"},
                {"data":"detail","class":"left nowrap",
                	"render": function (data,type,row) {
                        return '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+data;
                    }
                },
                {"data":"oprtime","class":"center nowrap"},
                {"data":"username","class":"center noselect nowrap"}
            ],
            
            "initComplete": function () {
                common.autoHeight();
                //$("#filter").insertAfter($("#logTable_length"));
            },
            "drawCallback":function(){
				common.autoHeight();
            }
        });
    },
};
$(function(){
    loglist.init();
    stateFilter();
    oprFilter();
    dateFilter();
});
