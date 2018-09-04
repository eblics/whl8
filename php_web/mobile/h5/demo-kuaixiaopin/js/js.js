/*
 * 全站公共脚本,基于jquery-2.1.1脚本库，库中已嵌入fastclickp,transition
 */
$(function() {
//	document.addEventListener("touchstart",function(event){
//		event.preventDefault();
//	})
	FastClick.attach(document.body);//fastclick
//滑动事件
    var mousedown = false;
    var x1,x2,y1,y2;
    var x0=0,y0=0;
    var distanceX = 0;
    var distanceY = 0;
    $("#home .hand").on("touchstart mousedown",function(e){
    	mousedown = true;
        switch(e.type){
            case "mousedown":
                x1 = e.pageX;
                y1 = e.pageY;
            	break;
            case "touchstart":
                x1 = e.originalEvent.targetTouches[0].pageX;
                y1 = e.originalEvent.targetTouches[0].pageY;
            	break;
    	}
    })
    $("#home .hand").on("touchmove mousemove",function(e){
    	if ( mousedown ){
    		e.preventDefault();
	        switch(e.type){
	            case "mousemove":
	                x2 = e.pageX;
	                y2 = e.pageY;
	            	break;
	            case "touchmove":
	                x2 = e.originalEvent.targetTouches[0].pageX;
	                y2 = e.originalEvent.targetTouches[0].pageY;
	            	break;
	    	}
			distanceX = x0 + ( x2 - x1 );
			distanceY = y0 + ( y2 - y1 );
			with ( this.style ){
				transform = "translate("+ distanceX +"px,"+ distanceY +"px)";
				webkitTransform = "translate("+ distanceX +"px,"+ distanceY +"px)";
			}
    	}
    })
    $("#home .hand").on("touchend mouseup",function(event){
    	mousedown = false;
    	x0 = distanceX;
    	y0 = distanceY;
    })
    
//页面加载成功
	window.onload = function(){
		$("#home").css({"visibility":"visible"}).addClass("on");
	}
//首页
	$(document).on("click",".huo-1",function(){
		$("#second").find(".thing-1").show();
		enterSecond();
	})
	$(document).on("click",".huo-2",function(){
		$("#second").find(".thing-2").show();
		enterSecond();
	})
	$(document).on("click",".huo-3",function(){
		$("#second").find(".thing-3").show();
		enterSecond();
	})
	function enterSecond(){
		$("#home").removeClass("on").addClass("fixed");
		$("#second").css({"display":"block","opacity":0}).addClass("on").transition({opacity:1},500);
	}
//第二页
	$(document).on("click",".thing-1-1",function(){
		$("#third").find(".third-1").show();
		$("#second").hide();
		// $("#third").css({"display":"block","opacity":0}).addClass("on").transition({opacity:1},500);
		enterThird();
	})
	$(document).on("click",".thing-2-1",function(){
		$("#third").find(".third-2").show();
		$(this).hide().next().show();
		enterThird();
	})
	$(document).on("click",".thing-3-1",function(){
		$("#third").find(".third-3").show();
		$(this).hide().next().show();
		enterThird();
	})
	function enterThird(){
		//红包抽奖
		hlsjs.ready(function(x){
			hlsjs.takeActivity(function(data) {                             
				if(data.errcode == 0){
					//成功 data.amount 中奖金额，单位分，实际使用元需要/100
					$("#third").find(".q").find("span").html((Number(data.amount) / 100).toFixed(2));
				}else if(data.errcode == 20){
					//未中奖
					$("#third").find(".q").html('未中奖，再一次');
				}else if(data.errcode == 3){
					//重复扫码
					$("#third").find(".q").html('抽奖机会已用完');
				}else{
					//失败  错误信息
					$("#third").find(".q").html(data.errmsg);
				}
				showAmount();
			});
		});
		function showAmount(){
			setTimeout(function(){
				$("#second").hide();
				$("#third").css({"display":"block","opacity":0}).addClass("on").transition({opacity:1},500);
			},100)
		}
	}
	//金额
	// var q = (parseInt(Math.random() * (100-49) + 49) + 1)/100;
	$("#third").find(".q").find("span").html('0');
})