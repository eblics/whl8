var forget = {
	init:function(){
		forget.step1();
	},
	step1:function(){
		$('.fmain-s1').css("display","block");
		$('.thisbutton').css({"background-color":"BCBCC4","color":"black"});
		var reg = /^1(3[0-9]|4[57]|5[0-35-9]|7[06-8]|8[01235-9])\d{8}$/;
		$('.step1').on('click',function(){
			var phoneNum = $.trim($('#phoneNum').val());
			if(reg.test(phoneNum)){
				$.post('/user/is_num_exists',{phoneNum:phoneNum},function(res){
					$('.fmain-s1').css("display","none");
					$('.fmain-s1').remove();
					$('.fmain-res').css("display","block");
					window.phoneNum = phoneNum;
					forget.step2();
				},'json');
			}else{
				common.alert("请填写正确的手机号码！");
				return;
			}
		});
	},
	step2:function(){
		$('.next2').off().on('click',function(){
			var valid = $.trim($('.valid input').val());
			var mesvalid = $.trim($('.mesvalid input').val());
			if(valid.length != 4){
				common.alert('请输入正确的图片验证码');
				return;
			}else if(mesvalid.length != 6 ){
				common.alert('请获取短信验证码！');
				return;
			}
			if(valid.length == 4){
				var pic = $.trim($('.valid .input').val());
				var code_null = "图片验证码不能为空";
				var code_error = "图片验证码错误";
				if(pic.length == 4){
					$.post("/user/check_validate",{imgvalid:pic},function(e){
						if(e.errcode == 0){
							forget.check();
						}else{
							common.alert(code_error);
							return;
						}
					},'json');
				}else if(pic.length == 0){
					common.alert(code_null);
					return;
				}else{
					common.alert(code_error);
					forget.step2();
				}
			}
		});
		$('.valid input').blur(function(){
			$('.thisbutton').css({"background-color":"#FFFFFF","color":"black"});
		});
		$(".valid input").change(function(){
			if($('.valid input').val().length == 4){
				$('.thisbutton').css({"background-color":"#FFFFFF","color":"white"});
			}
			$('.thisbutton').css({"background-color":"#FFFFFF","color":"white"});
			forget.check();
		});
		$('.mesvalid input').focus(function(){
			var pic = $.trim($('.valid .input').val());
			var code_null = "图片验证码不能为空";
			var code_error = "图片验证码错误";
			if(pic.length == 4){
				$.post("/user/check_validate",{imgvalid:pic},function(e){
					if(e.errcode == 0){
						forget.check();
					}else{
						common.alert(code_error);
					}
				},'json');
			}else if(pic.length == 0){
				common.alert(code_null);
				return;
			}else{
				common.alert(code_error);
				return;
			}
		});

		$('.thisbutton').off().on('click',function(){
			var pic = $.trim($('.valid .input').val());
			var code_null = "图片验证码不能为空";
			var code_error = "图片验证码错误";
			if(pic.length == 4){
				$.post("/user/check_validate",{imgvalid:pic},function(e){
					if(e.errcode == 0){
						forget.check();
					}else{
						common.alert(code_error);
						return;
					}
				},'json');
			}else if(pic.length == 0){
				common.alert(code_null);
				return;
			}else{
				common.alert(code_error);
				// forget.step2();
				return;
			}
		});
	},
	check:function(){
		var pic = $.trim($('.valid .input').val());
		var phoneNum = window.phoneNum;
		var ob = $('.tipmesvalid button');
		$('.tipmesvalid .thisbutton').off().on('click',function(){
			$.post("/user/check_validate",{imgvalid:pic},function(e){
				if(e.errcode == 0){
					var num = $('.input').val();
					if(num.length != 4){
						common.alert('图片验证码为空或错误');
						return;
					}
					if($('.tipmesvalid button').hasClass('thisbutton')){
						$.post('/user/send_mes',{account:phoneNum},function(res){
							if(res.errcode == 0){
								common.alert('短信已发送');
								window.send = 1;
							}else{
								common.alert(res.errmsg);
								return;
							}
						},'json')
						var time = 60;
						var timer = setInterval(function(){
							time--;
							ob.removeClass("thisbutton");
							ob.html(time+"S后再次获取");
							ob.css({"cursor":"default","background-color":"#D6D3D3"});
							if(time<=0){
								clearInterval(timer);
								ob.addClass("thisbutton");
								ob.css({"cursor":"pointer","background-color":"white"});
								ob.html("点击获取验证码");
							}
						},1000);
					}
				}else{
					common.alert("图片验证码错误");
					return;
				}
			},'json');
			
		});
		$('.next2').off().on('click',function(){
			var mesvalid = $('.mesvalid input').val();
			if(mesvalid.length != 6){
				common.alert('短信验证码为空或不正确！');
				return;
			}
			var account = window.phoneNum;
			$.post('/user/valid_mes',{value:mesvalid,account:account,change:1},function(res){
				if(res.errcode == 0){
					forget.updatepass();
				}else if(res.errcode == 10022){
					common.alert(res.errmsg);
					return;
				}else{
					common.alert("状态异常");
					return;
				}
			},'json');
		})
	},
	updatepass:function(){
		if(window.send == 1){
			$('.fmain-res').css('display','none');
			$('.fmain-suc').css('display','block');
			$(".save").off().on('click',function(){
					var reg = /^(\w){6,18}$/;
					var password = $.trim($('#password').val());
					var repassword = $.trim($('#repassword').val());
					var phoneNum = window.phoneNum;
					if(password != repassword){
						common.alert("新密码和确认密码不一致");
						return;
					}
					if(!reg.test(password)){
						common.alert("密码为空或不正确!");
						return;
					}
					if(reg.test(password)){
						$.post('/user/up_pass',{phoneNum:phoneNum,password:password},function(result){
							if(result.errcode == 0){
								common.alert("密码修改成功，请重新登录",function(res){
									if(res == 1){
										window.location.href="/user/login";
									}
								});
							}else if(result == 10023){
								common.alert(result.errmsg);
								return;
							}else{
								common.alert(result.errmsg);
								return;
							}
						},'json');
					}
			});

		}
	}
}
$(function(){
	forget.init();
});