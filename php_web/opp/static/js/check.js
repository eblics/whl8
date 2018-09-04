var check = {
	init:function(){
		var _this= this;
		$('#licenseImgUrl,#idCardImgUrl,#wxQrcodeUrl,#caPath,#keyPath,#certPath,#subscribeImgUrl,#subscriptImgUrl,#subscriptImgUrl_shop,#wxQrcodeUrl_shop,#caPath_shop,#keyPath_shop,#certPath_shop').parent().children('.hls-upload').children('.noselect').remove();
		_this.next();
	},
	next:function(){
		$('#tab1').off().on('click',function(){
			$('.tab1').css('display','none');
			$('.tab2').css('display','block');
			$('#maintitle').text("2.消费者微信查看");
		});
		$('#tab2').off().on('click',function(){
			$('.tab2').css('display','none');
			$('.tab3').css('display','block');
			$('#maintitle').text("3.供应链微信查看");
		});
		$('#tab3').off().on('click',function(){

		});
		$('#ntab').off().on('click',function(){
			$('.tab1').css('display','block');
			$('.tab2').css('display','none');
			$('#maintitle').text("1.消费者信息查看");
		});
		$('#nntab').off().on('click',function(){
			$('.tab1').css('display','none');
			$('.tab3').css('display','none');
			$('.tab2').css('display','block');
			$('#maintitle').text("2.消费者信息查看");
		});
		$('#unpass').off().on('click',function(){
			check.unpass();
		});
		$('#pass').off().on('click',function(){
			check.pass();
		});
		$("select").change(function(){
			$('#codeVersion').css('background','');
			$('#codeVersion').attr('edit-value',$(this).val());
		});
	},
	unpass:function(){
		// var val = $('#codeVersion').attr('edit-value');
		// if(val == '' || val == null || val == undefined){
		// 	$('#codeVersion').css('background','#EDBC67');
		// 	common.alert('请给企业选择码版本');
		// 	return;
		// }
		var id = $('#hid').val();
		var title = '驳回申请';
		common.formConfirm(title,'editForm',function(d){
			var checkReason = $('.confirm-form #checkReason').val();
			if(d == 1){
				$.post('/merchant/get_check',{status:2,id:id,checkReason:checkReason},function(e){
					if(e.errcode == 0){
						common.alert('操作成功',function(f){
							if(f == 1){
								window.history.go(-1);
							}
						});
					}else if(e.errcode == 52005){
						common.alert(e.errmsg,function(f){
							if(f == 1){
								window.history.go(-1);
							}
						});
					}
				},'json');
			}
		})
	},
	pass:function(){
		var val = $('#codeVersion').attr('edit-value');
		if(val == '' || val == null || val == undefined){
			common.alert('请给企业选择码版本');
			return;
		}
		var id = $('#hid').val();
		var title = '确认通过';
		var codeLimited = $('#codeLimited').val();
		var expireTime = $("#expireTime").val();
		common.confirm('确认通过验证',function(d){
			var code = $('#codeVersion').attr('edit-value');
			if(d == 1){
				$.post('/merchant/get_check',{status:1,id:id,code:code,codeLimited:codeLimited,expireTime:expireTime},function(e){
					console.log(e);
					if(e.errcode == 0){
						common.alert('操作成功',function(f){
							if(f == 1){
								window.history.go(-1);
							}
						});
					}else if(e.errcode == 52005){
						common.alert(e.errmsg,function(f){
							if(f == 1){
								window.history.go(-1);
							}
						});
					}else{
						common.alert(e.errmsg);
						return;
					}
				},'json');
			}
		})
	}
};
$(function(){
	check.init();
});