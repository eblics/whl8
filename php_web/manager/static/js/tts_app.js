/**
 * tts appid 显示
 *
 */
var tts = {
	init:function(){
		var _this = this;
		_this.click();
	},
	click:function(){
		$('.show').off().one('click',function(){
			$.post('/user/get_tts',{},function(response){
				if(response.errcode == 0){
					$('#appid').val(response.data.appId);
					$('#appsecret').val(response.data.appSecret);
					$('#sub').css("background-color","#C9C9C9");
					$('#sub').val('已经显示');
					$('#sub').removeClass('show');
				}else{
					common.alert(response.errmsg);
				}
			},'json');
			

		});
	}
}
$(function(){
	tts.init();
});