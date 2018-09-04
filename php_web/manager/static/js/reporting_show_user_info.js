var show_user = {
	init:function(){
		show_user.check();
	},
	check:function(){
		$('.send_btn').off().on('click',function(){
			var title = $('.send_nickname').text();
			var html = '<div class="con_title">给昵称为【'+title+'】的用户发送微信消息提醒</div><div class="warnning">友情提醒：本消息为公众号主动给粉丝推送的形式，请勿滥用，因滥用导致接口被封，欢乐扫平台概不负责。</div><div class="mes_title msame"><div class="mt1">标&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;题：</div><div class="mt2"><input type="text" maxlength="20" value="您好，您有一条客服消息提醒"></div><div class="mt3 mtip">建议默认，不超过20个字</div></div><div class="mes_form msame"><div class="mf1">服务类型：</div><div class="mf2"><input type="text" value="重要提醒" maxlength="20"></div><div class="mf3 mtip">建议默认，不超过20个字</div></div><div class="mes_content msame"><div class="mc1">服务进度：</div><div class="mc2"><input type="text" value="进行中" maxlength="20"></div><div class="mc3 mtip">建议不超过20字</div></div><div class="mes_server msame"><div class="ms1">服务人员：</div><div class="ms2"><input type="text" maxlength="20" value="在线客服"></div><div class="ms3 mtip">建议不超过20字</div></div><div class="mes_remark msame"><div class="mr1">备注内容：</div><div class="mr2"><textarea maxlength="50"></textarea></div><div class="mr3 mtip">建议不超过50字</div></div><div class="mess_btn">点击发送</div>';
			common.transDialog(function(callback){
				callback(html);
				show_user.submit();
			});
		});
	},
	submit:function(){
		$('.mess_btn').off().on('click',function(){
			var title = $('.mt2 input').val();
			var form = $('.mf2 input').val();
			var text1 = $('.mc2 input').val();
			var text2 = $('.ms2 input').val();
			var text3 = $('.mr2 textarea').val();
			var openid = $('#youopenid').attr('openid');
			if(title.length == 0 || form.length == 0 || text1.length == 0 || text2.length == 0 || text3.length == 0){
				common.alert('请检查您的录入信息，不能为空！');
				return;
			}else{
				common.loading();
				$.post('/reporting/send_message',{title:title,form:form,text1:text1,text2:text2,text3:text3,openid:openid},function(response){
					if(response.errcode == 0){
						$('.transDialog').remove();
                		$('body').css('overflow','visible');
                		common.unloading();
						common.alert('发送成功');
						return;
					}else{
						$('.transDialog').remove();
						common.unloading();
                		$('body').css('overflow','visible');
						common.alert(response.errmsg);
						return;
					}
				});
			}
		});
	}
};
$(function(){
	show_user.init();
});