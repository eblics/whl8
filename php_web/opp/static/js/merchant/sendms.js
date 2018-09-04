var sendms = {
	init:function(){
		var _this=this;
        _this.valid();
	},
    valid:function(){
        var url = window.location.search;
        var loc = url.substring(url.lastIndexOf('=')+1, url.length);
        if(loc == 1){
            var title = '所有已审企业';
            $('#objectives').text(title);
            $('#objectives').attr('value',1);
        }
        if(loc == 2){
            var title = '所有驳回企业';
            $('#objectives').text(title);
            $('#objectives').attr('value',2);
        }
        if(loc == 3){
            var title = '所有冻结企业';
            $('#objectives').text(title);
            $('#objectives').attr('value',3);
        }
        if(loc == 4){
            var title = '所有待审企业';
            $('#objectives').text(title);
            $('#objectives').attr('value',4);
        }
        var _this=this;
        if(window.localStorage.getItem('phones')){
            var phones = window.localStorage.getItem('phones').split(",");
            var length = "已选中" + phones.length + "个企业";
            $('#objectives').text(length);
            $('#objectives').attr('value','diy');
            var phone = phones.join(",");
        }

        $("#sub").on("click",function(){
			if(beforeSubmitAct()){
				_this.submit();
			}
		});
    },
    submit:function(){
        if(!window.localStorage.getItem('phones')){
            var phones = null;
        }else{
            var phones = window.localStorage.getItem('phones');
        }
        
        var objectives = $('#objectives').attr("value");
    	var content1 = $.trim($('#content1').val());
    	var content2 = $.trim($('#content2').val());
        var content3 = $.trim($('#content3').val());
    	var data = {
            objectives:objectives,
            content1: content1, 
            content2: content2,
            content3: content3,
            phones:phones
        };
        //需要判断分组用户为空的情况
    	$.post("/api/merchant/send_ms",data,function(d){
    		if(d.errcode == 0){
    			common.alert('发送成功',function(e){
    				if(e == 1){
    					// location.href = '/merchant';
                        window.history.back(-1);
                        return;
    				}
    			});
    		} else {
                common.alert(d.errmsg);
                return;
            }
    	});
    }
};
$(function(){
	sendms.init();
});