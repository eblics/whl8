var order = {
	init:function(){
		if($('.content').children('div').hasClass('main-tip')){
            $('.tip-btn').off().on('click',function(){
                window.location.href = "/mall/configure";
            });
            return;
        }
		var snum = $('#status').attr('value');
		var tnum = $('#timedate').attr('value');
		if(!isNaN(snum)){
			$('.toolstatus div ul li').children('a').removeClass('active');
			$('.toolstatus div ul li').children('a').each(function(i){
				if($(this).attr('data-value') == snum){
					$(this).addClass('active');
				}
			});
		}
		if(typeof(tnum)== 'string' && tnum != '' && tnum.length > 1){
			var val = tnum.split(".");
			$('#b_time').attr("value",val[0]);
			$('#e_time').attr("value",val[1]);
			$('.tooltimedate div ul li').children('a').removeClass('active');
			$('.timea').addClass('active');
		}else if(!isNaN(tnum)){
			$('.tooltimedate div ul li').children('a').removeClass('active');
			$('.tooltimedate div ul li').children('a').each(function(i){
				if($(this).attr('data-value') == tnum){
					$(this).addClass('active');
				}
			});
		}
		// var s = $('#order_btn').attr('status');
		$('.order_btn').each(function(i){
			if($(this).attr('os') == 1){
				$(this).css("background-color","#BCBCBC");
			}
			if($(this).attr('paystatus') == 0){
				$(this).css("background-color","#BCBCBC");
			}
		});
		// --两种状态 start
		$('.status').off().on('click',function(){
			var status = $(this).attr('data-value');
			$('.status').removeClass('active');
			$(this).addClass('active');
		});
		$('.time').off().on('click',function(){
			var timedate = $(this).attr('data-value');
			$('.time').removeClass('active');
			$(this).addClass('active');
			$('.timea').removeClass('active');
			if(!$('.timea').hasClass('active')){
				$('#b_time,#e_time').val("");
			}
		});
		// --两种状态 end
		$('#getSearch').off().on('click',function(){
			order.submit();
		});
		$('#b_time,#e_time').off().on('click',function(){
			$('.time').removeClass('active');
			$(this).parent().children('a').addClass('active');
		});
	},
	submit:function(){
		if($('.timea').hasClass('active')){
			var btime = $('#b_time').val();
			var etime = $('#e_time').val();
			if(etime == null || etime ==''|| btime == null || btime == ''){
				common.alert('日期不能为空！');
				return;
			}
			if(btime>etime){
				common.alert('起始日期不能大于终止日期！');
				return;
			}
		}
		
		var status = $('.toolstatus div ul li a.active').attr('data-value');
		// 对自定义日期 值 结果 进行判断
		if($('.timea').hasClass('active')){
			var timedate = btime + '.' + etime;
		}else{
			var timedate = $('.tooltimedate div ul li a.active').attr('data-value');
		}
		var data = {
			status:status,
			timedate:timedate
		}
		var url = '/s/'+status+'/t/'+timedate;
		if(status == null || status == undefined || status == ''){
			common.alert('请选择查询订单状态！');
			return;
		}
		if(timedate == null || timedate == undefined || timedate == ''){
			common.alert('请选择查询时间段！');
			return;
		}
		if(!isNaN(status)){
			window.location = "/mall/orders"+url;
			return;
		}
	}
};
$(function(){
	order.init();
});
function alertLocal(){
	window.location.href="/mall/orders"; 
}