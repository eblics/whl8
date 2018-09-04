var common={
	init:function(){
		$('body').on('touchstart',function(){});
        common.copyright();
		common.autoSize();
		$(window).resize(common.autoSize);
	},
	autoSize:function(){
		var w=$(window).width();
		var h=$(window).height();
		if(h/w<1.3){
			$('.wraper').height(1.3*w);
		}else{
			$('.wraper').height(h);
		}
	},
	loading:function(){
		var num=$('.loading').length;
		if(num==0){
			var html='<div class="loading"><div class="layer"></div><div class="gif"><div class="typing_loader"></div></div></div>';
			$('body').append(html);
		}
		$('.loading').show();
	},
	unloading:function(){
		$('.loading').hide();
	},
	alert:function(result,txt,type){
		$('.alert,.loading').remove();
		var style='ok';
		if(result==0){
			style='fail';
		}else if(result==1){
			style='ok';
		}
		var csscon='';
		if(typeof type!='undefined'){
			csscon='width:80%;margin-left:-40%;height:200px;margin-top:-100px;';
		}
		var html='<div class="alert"><div class="layer"></div><div class="box" style="'+csscon+'"></div><div class="con '+style+'" style="'+csscon+'"><div class="txt">'+txt+'</div><div class="close"></div></div></div>';
		$('body').append(html);
		$('.alert .close').on('touchend',function(){
			$('.alert').remove();
		});
		if(result==1){
			// clearTimeout(falertT);
			// var falertT=setTimeout(function(){
			// 	$('.alert .box').stop().animate({'opacity':0},300,function(){
			// 		$('.alert').remove();
			// 	});
			// },2000);
		}
	},
    copyright:function(config){
        //config={'fontSize':1,'color':'gray','zIndex':9999};
        if(typeof config=='undefined'){
            if($('.wraper').length==0) return;
            var scrHeight=$('.wraper')[0].scrollHeight;
            var winHeight=$(window).height();
            if(scrHeight==winHeight){
                $('.copyright').css('position','absolute');
            }else{
                $('.copyright').css('position','relative');
            }
        }else{
            if(typeof config.fontSize=='undefined') config.fontSize=1;
            if(typeof config.color=='undefined') config.color='gray';
            if(typeof config.zIndex=='undefined') config.zIndex=9999;
            var html='<div id="hls_copyright">';
            html+='<style>#hls_copyright{position:fixed;width:100%;font-size:'+config.fontSize+'rem;color:'+config.color+';height:'+config.fontSize+'rem; line-height:'+config.fontSize+'rem;padding:0 0 '+config.fontSize/2+'rem 0;text-align: center;bottom: 0;z-index:'+config.zIndex+';}';
            html+='#hls_copyright a,#hls_copyright a:visited{font-size:'+config.fontSize+'rem;color:'+config.color+';display: inline-block;border-bottom: '+config.fontSize/10+'rem solid '+config.color+'; text-decoration: none;}';
            html+='#hls_copyright a:active{text-decoration: none; color: #333;}</style>';
            html+='<a href="/about.html" target="_blank">爱创科技</a> · 提供技术支持</div>';
            $('body').append(html);
        }
    }
}
$(function(){
	common.init();
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        }
    });
});

/**
 ** 加法函数，用来得到精确的加法结果
 ** 说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
 ** 调用：accAdd(arg1,arg2)
 ** 返回值：arg1加上arg2的精确结果
 **/
function accAdd(arg1, arg2) {
    var r1, r2, m, c;
    try {
        r1 = arg1.toString().split(".")[1].length;
    }
    catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    }
    catch (e) {
        r2 = 0;
    }
    c = Math.abs(r1 - r2);
    m = Math.pow(10, Math.max(r1, r2));
    if (c > 0) {
        var cm = Math.pow(10, c);
        if (r1 > r2) {
            arg1 = Number(arg1.toString().replace(".", ""));
            arg2 = Number(arg2.toString().replace(".", "")) * cm;
        } else {
            arg1 = Number(arg1.toString().replace(".", "")) * cm;
            arg2 = Number(arg2.toString().replace(".", ""));
        }
    } else {
        arg1 = Number(arg1.toString().replace(".", ""));
        arg2 = Number(arg2.toString().replace(".", ""));
    }
    return (arg1 + arg2) / m;
}

//给Number类型增加一个add方法，调用起来更加方便。
Number.prototype.add = function (arg) {
    return accAdd(arg, this);
};

/**
 ** 减法函数，用来得到精确的减法结果
 ** 说明：javascript的减法结果会有误差，在两个浮点数相减的时候会比较明显。这个函数返回较为精确的减法结果。
 ** 调用：accSub(arg1,arg2)
 ** 返回值：arg1加上arg2的精确结果
 **/
function accSub(arg1, arg2) {
    var r1, r2, m, n;
    try {
        r1 = arg1.toString().split(".")[1].length;
    }
    catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    }
    catch (e) {
        r2 = 0;
    }
    m = Math.pow(10, Math.max(r1, r2)); //last modify by deeka //动态控制精度长度
    n = (r1 >= r2) ? r1 : r2;
    return ((arg1 * m - arg2 * m) / m).toFixed(n);
}

// 给Number类型增加一个sub方法，调用起来更加方便。
Number.prototype.sub = function (arg) {
    return accSub(this,arg);
};

/**
 ** 乘法函数，用来得到精确的乘法结果
 ** 说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
 ** 调用：accMul(arg1,arg2)
 ** 返回值：arg1乘以 arg2的精确结果
 **/
function accMul(arg1, arg2) {
    var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
    try {
        m += s1.split(".")[1].length;
    }
    catch (e) {
    }
    try {
        m += s2.split(".")[1].length;
    }
    catch (e) {
    }
    return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
}

// 给Number类型增加一个mul方法，调用起来更加方便。
Number.prototype.mul = function (arg) {
    return accMul(this,arg);
};

/** 
 ** 除法函数，用来得到精确的除法结果
 ** 说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
 ** 调用：accDiv(arg1,arg2)
 ** 返回值：arg1除以arg2的精确结果
 **/
function accDiv(arg1, arg2) {
    var t1 = 0, t2 = 0, r1, r2;
    try {
        t1 = arg1.toString().split(".")[1].length;
    }
    catch (e) {
    }
    try {
        t2 = arg2.toString().split(".")[1].length;
    }
    catch (e) {
    }
    with (Math) {
        r1 = Number(arg1.toString().replace(".", ""));
        r2 = Number(arg2.toString().replace(".", ""));
        return (r1 / r2) * pow(10, t2 - t1);
    }
}

//给Number类型增加一个div方法，调用起来更加方便。
Number.prototype.div = function (arg) {
    return accDiv(this, arg);
};