$("window").mousemove(function(e) {
    e.preventDefault();
    e.stopPropagation();
});
$(function() {
    var nowpage = 0;
    //给最大的盒子增加事件监听
    $(".container").swipe(
        {
            swipe:function(event, direction, distance, duration, fingerCount) {
            if(direction == "up"){
                nowpage = nowpage + 1;
                }else if(direction == "down"){
                nowpage = nowpage - 1;
                }
                if(nowpage < 0){
                nowpage = 0;
                }
                if(nowpage > 0){
                nowpage = 0;
                } 
                $(".container").animate({"top":nowpage * -100 + "%"},400);	
                
            }
        }
    );

    $('.button1').on('click', function() {
        $(".changjing2").fadeIn(300);
    });
    $('#guanbi1').on('click', function() {
        $(".changjing2").fadeOut(300);
    });
    $('.button2').on('click', function() {
        $(".changjing3").fadeIn(300);
    });
    $('#guanbi2').on('click', function() {
        $(".changjing3").fadeOut(300);
    });
    $('.button3').on('click',takeAct);


    $('#guanbi3').on('click', function() {
        $(".changjing4").fadeOut(300);
        $(".rongyao").show();
        $(".erweima").show();
        $(".scene3").hide();
        $(".scene4").hide();
        $(".button3").hide();
    });
    /*活动结束*/
    $('#guanbi4').on('click', function() {
        $(".changjing5").fadeOut(300);
        $(".rongyao").show();
        $(".erweima").show();
        $(".scene3").hide();
        $(".scene4").hide();
        $(".button3").hide();
    });
    $('#guanbi5').on('click', function() {
        $(".changjing6").fadeOut(300);
    });
    
    imagesArray();
});



function takeAct() {
	$('.button3').off('click');
	hlsjs.ready(function(x) {
		hlsjs.takeActivity(function(data) {
			if(data.errcode == 0){
				//data.amount 中奖金额，单位分，实际使用元需要/100
			    //$('#amount').text((Number(data.amount) / 100).toFixed(2));
				$(".changjing4").fadeIn(300);
				$('#Money').html((Number(data.amount) / 100).toFixed(2));
				$(".zhuangtai1").show();
				$(".erweima_tc").show();
				$(".zhuangtai1").addClass('cur');
				$("#yaodongyaodong").removeClass('shake-constant');
			}else if(data.errcode == 2 || data.errcode == 20){
				//扫别人的码 或者 未中奖
				$(".changjing4").fadeIn(300);
				$(".zhuangtai2").show();
				$(".zhuangtai2").addClass('cur'); 

			}else if(data.errcode == 12 || data.errcode == 13){
				//活动结束 
				$(".changjing5").fadeIn(300);
				$(".changjing5").addClass('cur'); 
			}else if(data.errcode == 3){
				//重复扫码
				$(".changjing4").fadeOut(300);
				$(".rongyao").hide();
				$(".rongyao2").show();
				$(".erweima").show();
				$(".scene3").hide();
				$(".scene4").hide();
				$(".button3").hide();
			}else{
				//失败  错误信息
				//galert(data.errmsg);
				$(".changjing6 .cuowuwenan").html(data.errmsg);
				$(".changjing6").fadeIn(300);
				$(".zhuangtai4").addClass('cur'); 
			}
			
		});
	});
}


var pics=[];
function imagesArray(){
    for(var i=0;i<document.querySelectorAll('img').length;i++){
        pics.push(document.querySelectorAll('img')[i].src);
    }
    _loadImages(pics, function(){
		$(".load").hide();
        var t2345 = setTimeout(function(){
			setTimeout(function(){
			$('.container').show();
			},100);		
			
			$('.changjing1').addClass('cur');
            clearInterval(t2345);    
        },5);        
    });
}

function _loadImages(pics, callback, len){
    len = len || pics.length;
    if(pics.length){
        var IMG = new Image(),
            picelem = pics.shift();

        if(window._pandaImageLoadArray){
            window._pandaImageLoadArray = window._pandaImageLoadArray
        }else{
            window._pandaImageLoadArray = [];
        }
        window._pandaImageLoadArray.push(picelem);

        IMG.src = picelem;

        // 从数组中取出对象的一刻，就开始变化滚动条
        _drawLoadProgress(window._pandaImageLoadArray.length/(len*len));

        // 缓存处理
        if (IMG.complete) {
            window._pandaImageLoadArray.shift();
            return _loadImages(pics,callback, len); 
        }else{
            // 加载处理
            IMG.onload = function() {
                window._pandaImageLoadArray.shift();
                IMG.onload = null;  // 解决内存泄漏和GIF图多次触发onload的问题
            }
            IMG.onerror = function(){
                window._pandaImageLoadArray.shift();
                IMG.onerror = null;
            }
            return _loadImages(pics, callback, len);
        }

        return;
    }
    if(callback) _loadProgress(callback, window._pandaImageLoadArray.length, len);
}
// 监听实际的加载情况
function _loadProgress(callback, begin, all){
    var loadinterval = setInterval(function(){
        if(window._pandaImageLoadArray.length != 0 && window._pandaImageLoadArray.length != begin){
            _drawLoadProgress((begin - window._pandaImageLoadArray.length )/all);
        }else if(window._pandaImageLoadArray.length == 0){
            _drawLoadProgress(1)
            setTimeout(function(){
                callback.call(window);
            },500);
            clearInterval(loadinterval);
        }
    },300);
}
function _drawLoadProgress(w){
    var num = Math.floor(w*100) >= 100 ? 100 : Math.floor(w*100);
   $(".loadingtiao").html(num+"%");
}