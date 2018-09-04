var pagging = {
	init:function(){
		//p_e 每页数目
		//window.setting 扩展配置
		var localPage = window.localStorage;
		if(localPage.page){
			$('.bar_select').val(localPage.page);
			window.setting = {p_e:localPage.page};
		}else{
			window.setting = {
				p_e:5
			};
		}
		var list = $('.total_form').find('.order_form');
		window.array = [];
		var num = list.length;
		window.num = num;
		for (var i =0; i < num; i++) {
			array.push(list[i]);
		}
		window.pageNum = Math.ceil(num/setting.p_e);
		pagging.page(pageNum,num);
		//本地存储 cover为0代表折叠
		if(localStorage.cover){
			$('#ff-select option').each(function(i){
				if(localStorage.cover == $(this).val()){
					$(this).attr("selected","selected");
				}
			});
		}else{
			localStorage.cover = 0;
		}
	},
	page:function(pageNum,num){
		// pageNum 分页数 num 条数
		// $('.o_prev')
		for (var i = 1; i <= pageNum; i++) {
			$('.o_next').before('<div class="o_page">'+i+'</div>');
		}
		$('div.o_page').mouseover(function(){
			$(this).addClass('chose');
			$(this).css("color","white");
			$(this).css("cursor","pointer");
		});
		$('div.o_page').mouseout(function(){
			$(this).removeClass('chose');
			$(this).css("color","black");
		});
		$('div.o_page').off().on('click',function(){
			$('div.o_page').removeClass('onchose');
			$(this).addClass('onchose');
		});
		if(!$('.o_paging').children('.o_page').hasClass('onchose')){
			pagging.default();
		}
		if(localStorage.cover == 1){
			$('.fold-div').removeClass('short');	
			$('.fold-div').parent().parent().parent().children(".nhide").fadeIn();
			$('.fold-div').parent().parent().parent().children('.dynamic').text('');
			$('.fold-div').parent().parent().parent().children('.dynamic').addClass('h30');
			$('.fold-div').text('点击折叠');
			$('.fold-div').addClass('normal');
		}else{
			$('.nhide').hide();
			$('.dynamic').removeClass('h30');
			$('.order_num').css("margin-bottom","0px");
		}
		common.autoHeight();
		pagging.click();
	},
	click:function(){
		$('.bar_select').change(function(){
			window.localStorage.page = $('.bar_select').val();
			window.setting.p_e = localStorage.page;
			location.reload();
		});
		$('#onSearch').off().on('click',function(){
			var searchval =$.trim($(this).parent().children('label').children('input').val());
			if(searchval == '' || searchval == null){
				var time = $(this).attr('time');
				if(isNaN(time)){
					time = 1;
				}
				time++;
				$(this).attr('time',time);
				if(time >5 && time <=10){
					var txt = "\u8bf7\u8f93\u5165\u8ba2\u5355\u53f7\u518d\u8fdb\u884c\u641c\u7d22";
					common.alert(txt);
				}
				if(time >10){
					var txt = "\u7ee7\u7eed\u70b9\uff1f\u4e0d\u8f93\u5165\u8ba2\u5355\u53f7\u8fd8\u662f\u6ca1\u7ed3\u679c";
					common.alert(txt);
				}
				return;
			}
			// $.post('/mall/get_search_order',{orderNum:searchval},function(response){
			window.location = "/mall/orders/ordernum/"+searchval;
			// });
		});
		$('#ff-select').change(function(){
			localStorage.cover = $(this).val();
			if(localStorage.cover == 0){
				if($('.fold-div').hasClass('normal')){
					$('.fold-div').removeClass('normal');
					$('.fold-div').addClass('short');
					$('.fold-div').parent().parent().parent().children(".nhide").fadeOut();
					$('.fold-div').parent().parent().parent().children('.dynamic').removeClass('h30');
					$('.fold-div').parent().parent().css("margin-bottom","0px");
					$('.fold-div').text('点击展开');
					pagging.click();
				}
			}
			if(localStorage.cover == 1){
				if($('.fold-div').hasClass('short')){
					$('.fold-div').removeClass('short');
					$('.fold-div').addClass('normal');
					$('.fold-div').parent().parent().parent().children(".nhide").fadeIn();
					$('.fold-div').parent().parent().parent().children('.dynamic').text('');
					$('.fold-div').text('点击折叠');
					$('.fold-div').parent().parent().parent().children('.dynamic').addClass('h30');
					pagging.click();
				}
			}
			common.autoHeight();
		});
		$('.o_page').off().on('click',function(){
			$('div.o_page').removeClass('onchose onthis');
			$(this).addClass('onchose');
			var page_n = $(this).text();
			var searchform = $('.searchform');
			$('.total_form').html('');
			// 每页数目 改成配置
			// var p_e = setting.p_e;
			p_start = (page_n-1)*setting.p_e;
			//page_n 可修改
			p_end = (page_n)*setting.p_e;
			$('.total_form').append(searchform);
			for (var i = p_start; i < p_end; i++) {
				$('.total_form').append(array[i]);
			}
			common.autoHeight();
			if(!$(this).prev().hasClass('o_prev') && $('.o_page').length>1 ){
				$('.o_prev').addClass('o_prev_d');
			}
			if($(this).prev().hasClass('o_prev')){
				$('.o_prev').removeClass('o_prev_d');
			}
			if($(this).next().hasClass('o_next')){
				$('.o_next').removeClass('o_next_d');
			}
			//分页隐藏计算 -- 开始
			if(pageNum>7){
				var divp = '<div class="ellihidep">...</div>';
				var divn = '<div class="ellihiden">...</div>';
				$('.o_paging').find('.ellihidep,.ellihiden').remove();
				// $(this).next().after(divn);
				$(this).prev().prev().show();
				$(this).prev().show();
				$(this).next().next().show();
				$(this).next().show();
				if($(this).next().next().next().next().hasClass('o_page')){
					$(this).next().next().after(divn);
					$(this).next().next().show();
					$(this).next().show();
					var last = pageNum - 1;
					$('.ellihiden').nextAll('.o_page').hide();
					$('.o_paging').children('.o_page').eq(last).show();
				}
				if($(this).prev().prev().prev().prev().hasClass('o_page')){
					$(this).prev().prev().before(divp);
					$('.ellihidep').prevAll('.o_page').hide();
					$('.o_paging').children('.o_page').eq(0).show();
					$(this).prev().show();
					$(this).prev().prev().show();
				}
			}
			//分页隐藏计算 -- 结束
			var n1 = p_start + 1;
			var n2 = p_end;
			if(n2 >= num){
				n2 = num;
			}
			var text = '当前第 '+n1+' ~ '+n2+' 条，共计 '+num+' 条记录';
			$('.o_mes').text(text);
			if(!$(this).next().hasClass('o_next') && $('.o_page').length>1 ){
				$('.o_next').addClass('o_next_d');
				pagging.click();
			}
			pagging.cover();
		});
		$('.o_next_d').off().on('click',function(){
			common.autoHeight();
			$('.o_paging').find('.onchose').next('.o_page').addClass('onchose onthis');
			$('.o_paging').find('.onthis').prev('.o_page').removeClass('onchose onthis');
			if($(this).prev('.o_page').hasClass('onthis')){
				$(this).removeClass('o_next_d');
			}
			var p_n = parseInt($("div.onchose").text());
			var searchform = $('.searchform'); 
			$('.total_form').html('');
			p_start = (p_n-1)*setting.p_e;
			p_end = p_n*setting.p_e;
			$('.total_form').append(searchform);
			for (var i = p_start; i < p_end; i++) {
				$('.total_form').append(array[i]);
			}
			if($('.o_page').length>1){
				$('.o_prev').addClass('o_prev_d');
				// pagging.click();
			}
			if(pageNum>7){
				var divp = '<div class="ellihidep">...</div>';
				var divn = '<div class="ellihiden">...</div>';
				$('.o_paging').find('.ellihidep,.ellihiden').remove();
				// $(this).next().after(divn);
				$('.o_paging').find('.onthis').prev().prev().show();
				$('.o_paging').find('.onthis').prev().show();
				$('.o_paging').find('.onthis').next().next().show();
				$('.o_paging').find('.onthis').next().show();
				if($('.o_paging').find('.onthis').next().next().next().next().hasClass('o_page')){
					$('.o_paging').find('.onthis').next().next().after(divn);
					$('.o_paging').find('.onthis').next().next().show();
					$('.o_paging').find('.onthis').next().show();
					var last = pageNum - 1;
					$('.ellihiden').nextAll('.o_page').hide();
					$('.o_paging').children('.o_page').eq(last).show();
				}
				if($('.o_paging').find('.onthis').prev().prev().prev().prev().hasClass('o_page')){
					$('.o_paging').find('.onthis').prev().prev().before(divp);
					$('.ellihidep').prevAll('.o_page').hide();
					$('.o_paging').children('.o_page').eq(0).show();
					$('.o_paging').find('.onthis').prev().show();
					$('.o_paging').find('.onthis').prev().prev().show();
				}
			}
			var n1 = p_start+1;
			var n2 = p_end;
			if(n2 >= num){
				n2 = num;
			}
			var text = '当前第 '+n1+' ~ '+n2+' 条，共计 '+num+' 条记录';
			if($('.total_form').children().hasClass('nullform')){
				var text = '当前没有记录';
			}
			$('.o_mes').text(text);
			common.autoHeight();
			pagging.cover();
		});
		$('.o_prev_d').off().on('click',function(){
			$('.o_paging').find('.onchose').prev('.o_page').addClass('onchose onthis');
			$('.o_paging').find('.onthis').next('.o_page').removeClass('onchose onthis');
			if($(this).next('.o_page').hasClass('onthis')){
				$(this).removeClass('o_prev_d');
			}
			var p_n = parseInt($("div.onchose").text());
			var searchform = $('.searchform'); 
			$('.total_form').html('');
			p_start = (p_n-1)*setting.p_e;
			p_end = p_n*setting.p_e;
			$('.total_form').append(searchform);
			for (var i = p_start; i < p_end; i++) {
				$('.total_form').append(array[i]);
			}
			if($('.o_page').length>1){
				$('.o_next').addClass('o_next_d');
			}
			if(pageNum>7){
				var divp = '<div class="ellihidep">...</div>';
				var divn = '<div class="ellihiden">...</div>';
				$('.o_paging').find('.ellihidep,.ellihiden').remove();
				// $(this).next().after(divn);
				$('.o_paging').find('.onthis').prev().prev().show();
				$('.o_paging').find('.onthis').prev().show();
				$('.o_paging').find('.onthis').next().next().show();
				$('.o_paging').find('.onthis').next().show();
				if($('.o_paging').find('.onthis').next().next().next().next().hasClass('o_page')){
					$('.o_paging').find('.onthis').next().next().after(divn);
					$('.o_paging').find('.onthis').next().next().show();
					$('.o_paging').find('.onthis').next().show();
					var last = pageNum - 1;
					$('.ellihiden').nextAll('.o_page').hide();
					$('.o_paging').children('.o_page').eq(last).show();
				}
				if($('.o_paging').find('.onthis').prev().prev().prev().prev().hasClass('o_page')){
					$('.o_paging').find('.onthis').prev().prev().before(divp);
					$('.ellihidep').prevAll('.o_page').hide();
					$('.o_paging').children('.o_page').eq(0).show();
					$('.o_paging').find('.onthis').prev().show();
					$('.o_paging').find('.onthis').prev().prev().show();
				}
			}
			var n1 = p_start + 1;
			var n2 = p_end;
			if(n2 >= num){
				n2 = num;
			}
			var text = '当前第 '+n1+' ~ '+n2+' 条，共计 '+num+' 条记录';
			if($('.total_form').children().hasClass('nullform')){
				var text = '当前没有记录';
			}
			$('.o_mes').text(text);
			common.autoHeight();
			pagging.cover();
		});
		$('.order1_btn').off().on('click',function(){
			var ordernum = $(this).parent().children('.order_btn').attr('ordernum');
			var id = $(this).parent().children('.order_btn').attr('value');
			$.post('/mall/get_remark',{id:id},function(response){
				common.sendConfirm(ordernum,response.data[0].reMark,function(res,textarea){

				});
			});
		});
		$('.order_btn').off().on('click',function(){
			var id = $(this).attr('value');
			var status = $(this).attr('status');
			var paystatus = $(this).attr('paystatus');
			var ordernum = $(this).attr('ordernum');
			var os = $(this).attr('os');
			var isVirual = $(this).hasClass('virual');
			if(status == 1){
				common.confirm('是否确认收货？',function(d){
					if(d==1){
						$.post('/mall/confirm_get',{id:id,ordernum:ordernum},function(r){
							if(r.errcode == 0){
								common.alert('确认收货成功',function(e){
									if(e == 1){
										location.reload();
										return;
									}
								});
							}else{
								common.alert(r.errmsg);
								return;
							}
						});
					}
				});
			}
			if(status == 2 && os ==0){
				common.confirm('确认完成订单？',function(e){
					if(e == 1){
						$.post('/mall/end_order',{id:id,ordernum:ordernum},function(r){
							if(r.errcode == 0){
								common.alert('操作成功！',function(res){
									if(res == 1){
										location.reload();
									}
								});
								return;
							}else{
								common.alert(r.errmsg);
								return;
							}
						});
					}
				});
			}
			if(paystatus == 0){
				common.alert('请等待用户付款后再操作！');
				return;
			}
			if(status == 0 && paystatus == 1){
				$.post('/mall/get_remark',{id:id},function(response){
					common.sendConfirm(ordernum,1,function(res,textarea){
						if(res == 1){
							$.post('/mall/remark',{id:id,textarea:textarea,ordernum:ordernum},function(response){
								if(response.errcode == 1){
									common.alert(response.errmsg);
									return;
								}
								if(response.errcode == 0){
									common.alert('保存成功',function(r){
										if(r == 1){
											location.reload();
											return;
										}
									});
								}
							});
						}
					}, isVirual);
				});
			
			}
		});
		$('.normal').off().on('click',function(){
			if($(this).hasClass('normal')){
				$(this).removeClass('normal');
				$(this).addClass('short');
				$(this).parent().parent().parent().children(".nhide").fadeOut();
				$(this).parent().parent().parent().children('.dynamic').removeClass('h30');
				$(this).parent().parent().css("margin-bottom","0px");
				$(this).text('点击展开');
				pagging.click();
			}
		});
		$('.short').off().on('click',function(){
			if($(this).hasClass('short')){
				$(this).removeClass('short');
				$(this).addClass('normal');
				$(this).parent().parent().parent().children(".nhide").fadeIn();
				$(this).parent().parent().parent().children('.dynamic').text('');
				$(this).text('点击折叠');
				$(this).parent().parent().parent().children('.dynamic').addClass('h30');
				pagging.click();
			}
		});
	},
	default:function(){
		$('.total_form').html('');
		$('.o_paging').children('.o_page').eq(0).addClass('onchose');
		for (var i = 0; i < setting.p_e; i++) {
			$('.total_form').append(array[i]);
		}
		if(!$(this).next().hasClass('o_next') && $('.o_page').length>1 ){
			$('.o_next').addClass('o_next_d');
		}
		if(!$(this).prev().hasClass('o_next') && $('.o_page').length>1 ){
			$('.o_prev').addClass('o_prev_d');
		}
		if($('.onchose').prev().hasClass('o_prev_d')){
			$('.o_prev').removeClass('o_prev_d');
		}
		if(pageNum>7){
			var last = pageNum - 1;
			var divn = '<div class="ellihiden">...</div>';
			var vtext = $('.o_paging').find('.onchose').next().next().after(divn);
			$('.ellihiden').nextAll('.o_page').hide();
			$('.o_page').eq(last).show();
		}
		var n1 = 1;
		var n2 = null;
		if(num <= setting.p_e){
			n2 = num;
		}else{
			n2 = setting.p_e;
		}
		var text = '当前第 '+n1+' ~ '+n2+' 条，共计 '+num+' 条记录';
		if($('.total_form').children().hasClass('nullform')){
			var text = '当前没有记录';
		}
		$('.o_mes').text(text);
		common.autoHeight();
		$('.order_btn').off().on('click',function(){
			var id = $(this).attr('value');
			var status = $(this).attr('status');
			var paystatus = $(this).attr('paystatus');
			var ordernum = $(this).attr('ordernum');
			if(status == 1){
				common.confirm('是否确认收货？',function(d){
					if(d==1){
						$.post('/mall/confirm_get',{id:id,ordernum:ordernum},function(r){
							if(r.errcode == 0){
								common.alert('确认收货成功');
								location.reload();
								return;
							}else{
								common.alert(r.errmsg);
								return;
							}
						})
					}
				});
			}
			if(paystatus == 0){
				common.alert('请等待用户付款后再操作！');
				return;
			}
			if(!isNaN(id) && status == 0){
				common.sendConfirm(id,'a',function(res,textarea){
					if(res == 1){
						$.post('/mall/remark',{id:id,textarea:textarea,ordernum:ordernum},function(response){
							if(response.errcode == 1){
								common.alert(response.errmsg);
								return;
							}
							if(response.errcode == 0){
								common.alert('保存成功',function(r){
									if(r == 1){
										// location.reload();
										return;
									}
								});
							}
						});
					}
				});
			}
			// pagging.click();
		});
		pagging.click();
	},
	cover:function(){
		if(!localStorage.cover){
			localStorage.cover == 0;
		}
		if(localStorage.cover == 1){
			$('.fold-div').removeClass('short');
			$('.fold-div').addClass('normal');
			$('.fold-div').parent().parent().parent().children(".nhide").fadeIn();
			$('.fold-div').parent().parent().parent().children('.dynamic').text('');
			$('.fold-div').text('点击折叠');
			$('.fold-div').parent().parent().parent().children('.dynamic').addClass('h30');
			common.autoHeight();
		}
		if(localStorage.cover == 0){
				$('.fold-div').removeClass('normal');
				$('.fold-div').addClass('short');
				$('.fold-div').parent().parent().parent().children(".nhide").hide();
				$('.fold-div').parent().parent().parent().children('.dynamic').removeClass('h30');
				$('.fold-div').parent().parent().css("margin-bottom","0px");
				$('.fold-div').text('点击展开');
				common.autoHeight();
		}
		pagging.click();
	}

};
$(function(){
	pagging.init();
});