var extract={
	init:function(){
		//页面打开发生一个查询事件
		$.post("/user/account",{},function(data){
			alert(data);
			var res =JSON.parse(data.replace(/<.*>/g, ''));
			console.log(res.amount);
			$(".ext-total").text(res.amount);
		});
		$("#ext-click").click(function(){
		  	var value = $("#ext_value").val();
		  	if(value.length!=0){    
	        	reg = /^\d+(\.\d{2})?$/;   
	        	if(!reg.test(value)){    
	        		//备用弹窗
	            	// alert("输入的整数类型格式不正确!");  
	            	$(".ext-a3b4").css("color","#EF5033");
	            	$(".ext-a4b1").css("background-color","#999999");
	        	}    
	       	} 
		});
		$("#ext_value").click(function(){
			var value = $("#ext_value").val();
		  	if(value.length!=0){   
	        	reg = /^\d+(\.\d{2})?$/;   
	        	if(reg.test(value)){   
	        		$(".ext-a3b4").css("color","#A3A3A3");  
					$(".ext-a4b1").css("background-color","#EF5032");
	        	}    
	       	} 
		});
	}
}
$(function(){
	extract.init();
});
