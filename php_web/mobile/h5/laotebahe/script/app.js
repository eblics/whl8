window.isWinner = 0;//是否中奖 1是 0否
window.isWinnerComplete = 0; //抽奖完成  1是 0否
window.errcode = 0;
$(function() {
    $('body').height($(window).height()); //固定内容高度
    //二维码图层关闭事件
    $('.codeMsk .closeBtn').off().on('tap',function(event){
        $('.codeMsk').fadeOut(200);
    });
    //err图层关闭事件
    $('.codeMsk_err .closeBtn').off().on('tap',function(event){
        $('.codeMsk_err').fadeOut(200);
    });
    //规则点击事件
    $('.index .rule_btn').off().on('tap',function(){
        $('.ruleMsk').fadeIn(200);
        $('.ruleMsk .closeBtn').off().on('tap',function(event){
            $('.ruleMsk').fadeOut(200);
        });
    });
    //开心一刻图层关闭事件
    $('.defMsk .closeBtn').off().on('tap',function(event){
        $('.defMsk').fadeOut(200);
    });

    hlsjs.ready(function() {
        $('.index .de_cont').hide();
        $('.index .sc_cont').show();
        $('.sc_msk').off().on('touchstart',function(){
            Pace.stop();
            $('.sc_msk').off('touchstart');
            $('#errmsg').html('正在抽奖..');
            hlsjs.takeActivity(function(result) {
                window.errcode = result.errcode;
                window.isWinnerComplete=1;
                $('.index .de_cont').hide();
                $('.index .sc_cont').show();
                if(result.errcode == 0){
                    window.isWinner=1;
                    $('#errmsg').html('恭喜获得了'+(Number(result.amount)/100).toFixed(2)+'元红包！');
                }else if(result.errcode==20){
                    window.isWinner=0;
                    $('#errmsg').html(result.errmsg);
                    $('#alttext').html(result.alt_text);
                }else if(result.errcode==2){
                    window.isWinner=0;
                    $('#errmsg').html('运气不够好哦');
                    $('.codeMsk_err').find('.error').html('运气不够好哦');
                    $('#alttext').html(result.alt_text);
                }else{
                    window.isWinner=0;
                    $('#errmsg').html(result.errmsg);
                    $('.codeMsk_err').find('.error').html(result.errmsg);
                    $('#alttext').html(result.alt_text);
                }
                
            });
        });
    });
    
});

/*加载进度条*/
(function() {
    Pace.start({
        ajax: false,
        restartOnPushState: false,
        restartOnRequestAfter: false,
        document: false
    });
    Pace.on('done', function() { //加载完成
        console.log('done');
        //加载完成事件
        onLoadEnd();
    });
    
    
    
})();

//加载完成
function onLoadEnd(){
    window.onresize = onResize;
    onResize();
    $('.scene').removeClass('hidden'); //扫码进入
    //动画执行流
    onAnimateA();
}

//动画执行流
function onAnimateA(){
    $('.index .tt').animate({'bottom':85,'opacity':1},1000,function(){
            $('.index .works').fadeIn(300,function(){ //酒瓶动效
                $('.index .works img').addClass('shake');
                $('.index .hat').addClass('quickLight').show();
                setTimeout(function(){
                    $('.index .h1title').addClass('floatUpDown').show();
                },1000);
                $('.index .b_cont .b_move').fadeIn(200);
                //初始化eraser
                $('#mskImg').eraser({
                    "size": 40,
                    "completeRatio":.4,
                    "completeFunction": onEraser,
                    "progressFunction": function(p) {
                        console.log(Math.round(p * 100) + '%');
                    }
                });
            });
    });
}

//刮奖提示函数
function onEraser(){
    clearInterval(window.t);
    window.t=setInterval(function(){
        if(window.isWinnerComplete==1){
            setTimeout(function(){
                //移除蒙层
                $('.index .sc_msk').fadeOut(200);
                if(window.isWinner == 1){
                    $('.codeMsk').fadeIn(200);
                } else {
                    if(window.errcode==20)
                        $('.defMsk').fadeIn(200);
                    if(window.errcode==2)
                        $('.codeMsk_err').fadeIn(200);
                    else
                        $('.defMsk').fadeIn(200);
                        
                }
                //为刮开的图层设定点击事件
                $('.index .b_cont .sc_cont').off().on('touchstart',function(){
                    if(window.isWinner == 1){
                        $('.codeMsk').fadeIn(200);
                    }else{
                        if(window.errcode==20)
                            $('.defMsk').fadeIn(200);
                        else
                            $('.codeMsk_err').fadeIn(200);
                    }
                });
                $('.index .b_cont .de_cont').off().on('touchstart',function(){
                    if(window.errcode==20)
                        $('.defMsk').fadeIn(200);
                        
                });
            },1000);
            clearInterval(window.t);
        }
    },50);
    console.log("刮开了！");
}

//窗口改变对应事件
function onResize(){
    var windowWidth = $(window).width();
    var windowHeigh = $(window).height();
    var conWidth  = $('.scene').width();
    var conHeight = $('.scene').height();
    var scaleX = windowWidth/conWidth;
    var scaleY = windowHeigh/conHeight;
    $('.scene,.scene_b').css({
        'transform-origin' : '0% 0% 0px',
        '-webkit-transform-origin' : '0% 0% 0px',
        '-moz-transform-origin' : '0% 0% 0px',
        '-ms-transform-origin' : '0% 0% 0px',
        'transform' : 'scale('+scaleX+','+scaleY+')',
        '-webkit-transform' : 'scale('+scaleX+','+scaleY+')',
        '-moz-transform' : 'scale('+scaleX+','+scaleY+')',
        '-ms-transform' : 'scale('+scaleX+','+scaleY+')'
    });
}

