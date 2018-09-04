var fromScan = 1; //是否是扫码进入H5  1是 0否
window.isWinner = 0; //是否中奖 1是 0否
window.isWinnerComplete = 0; //抽奖完成  1是 0否
$(function() {
    common.copyright({'fontSize':1.4});
    //$('head').append('<script type="text/javascript" src="'+hlsjs.getRootUrl()+'h5/userdata/?t=' + new Date().getTime() + Math.random().toString(36).substr(2) + '"></script>');
    //扫码排名
    // var user=hlsjs.getCurrentUser(show_rank);
    // function show_rank(data){
    //     if(data.errcode!=0){
    //         console.log(data);
    //         return;
    //     }
    //     var user=data.data;
    //     $.ajax({
    //         url: hlsjs.getRootUrl()+'/h5/userdata',
    //         jsonp: 'callback',
    //         dataType: 'jsonp',
    //         data:{mchid:user.mchId,userid:user.id}
    //     }).done(function(h5UserData){
    //         console.log(h5UserData);
    //         if (typeof h5UserData.rank != 'undefined' && h5UserData.rank != null) {
    //             var rankStyle = '<style>#scanRank{position:absolute;width:80%;left:10%;top:20%;height:400px;z-index:9999;background-color:rgba(0,0,0,.8);text-align:center;border-radius:20px;}';
    //             rankStyle += '#scanRank>.txt{position:absolute;width:100%;top:40px;color:#fff;}';
    //             rankStyle += '#scanRank>.txt>h1{line-height:100px;font-size:50px;}';
    //             rankStyle += '#scanRank>.txt>h1>strong{color:#fa0;font-size:90px;vertical-align:bottom;}';
    //             rankStyle += '#scanRank>.txt>h2{line-height:100px;font-size:40px;}';
    //             rankStyle += '#scanRank>.txt>h2>strong{color:#ff0;font-size:50px;}';
    //             rankStyle += '</style>';
    //             window.rankHtml = '<div id="scanRank">' + rankStyle + '<div class="txt"><h2>您当前扫码量位居</h2><h2><strong>' + h5UserData.city + '</strong></h2><h1>第 <strong>' + h5UserData.rank + '</strong> 名</h1></div></div>';
    //             if ($('#scanRank').length > 0) {
    //                 $('#scanRank').remove();
    //             }
    //             $('body').append(window.rankHtml);
    //             setTimeout(function() {
    //                 $('#scanRank').fadeOut('slow');
    //             }, 3000);
    //         }
    //     }).fail(function(data){
    //         console.log('排名出错');
    //         console.log(data);
    //     });
    // }
    $('body').height($(window).height()); //固定内容高度
    //判断移动端类型
    var u = navigator.userAgent;
    isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    //做移动适配
    if (isAndroid) {
        var str = '<div class="content"><img src="images/s_msk1.png"><div class="closeBtn"></div></div>';
        $('.codeMsk').html(str);
        //二维码图层关闭事件
        $('.codeMsk .closeBtn,.codeMsk').off().on('tap', function(event) {
            event.stopPropagation();
            event.preventDefault();
            if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("codeMsk") < 0) {
                return;
            }
            $('.codeMsk').fadeOut(200);
        });
    } else {
        //二维码图层关闭事件
        $('.codeMsk .closeBtn,.code_img').off().on('tap', function(event) {
            event.stopPropagation();
            event.preventDefault();
            if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("code_img") < 0) {
                return;
            }
            $('.codeMsk').fadeOut(200);
        });
    }
    //规则点击事件
    $('.index .rule_btn').off().on('tap', function() {
        $('.ruleMsk').fadeIn(200);
        $('.ruleMsk .closeBtn, .ruleMsk').off().on('tap', function(event) {
            event.stopPropagation();
            event.preventDefault();
            if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("ruleMsk") < 0) {
                return;
            }
            $('.ruleMsk').fadeOut(200);
        });
    });

    //开心一刻图层关闭事件
    $('.defMsk .closeBtn,.defMsk ').off().on('tap', function(event) {
        event.stopPropagation();
        event.preventDefault();
        if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("defMsk") < 0) {
            return;
        }
        $('.defMsk').fadeOut(200);
    });

    hlsjs.ready(function() {
        $('.index .de_cont').hide();
        $('.index .sc_cont').show();
        $('.sc_msk').off().on('touchstart', function() {
            Pace.stop();
            $('.sc_msk').off('touchstart');
            $('#errmsg').html('正在抽奖..');
            hlsjs.takeActivity(function(result) {
                window.isWinnerComplete = 1;
                $('.index .de_cont').hide();
                $('.index .sc_cont').show();
                if (result.errcode == 0) {
                    window.isWinner = 1;
                    $('#errmsg').html('恭喜获得了' + result.amount + '分红包！');
                } else if (result.errcode == 20) {
                    window.isWinner = 0;
                    //$('#errmsg').html(result.errmsg);
                    $('#errmsg').html('矮油，就差一点点，<BR>运气先攒着，下次大奖等着你');
                    $('#alttext').html(result.alt_text);
                } else if (result.errcode == 2) {
                    window.isWinner = 0;
                    $('#errmsg').html('此码已被他人扫过');
                    $('#alttext').html(result.alt_text);
                } else {
                    window.isWinner = 0;
                    $('#errmsg').html(result.errmsg);
                    $('#alttext').html(result.alt_text);
                }

            });
            // .fail(function(){
            //     window.isWinner=0;
            //     window.isWinnerComplete=1;
            //     $('.index .de_cont').hide();
            //     $('.index .sc_cont').show();
            //     $('#errmsg').html('网络太不给力了 :( ');
            // });

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
    Pace.once('done', function() { //加载完成
        console.log('done');
        //加载完成事件
        onLoadEnd();
    });

    
})();

//加载完成
function onLoadEnd() {
    window.onresize = onResize;
    onResize();
    $('.scene').removeClass('hidden'); //扫码进入
    //动画执行流
    onAnimateA();
}

//动画执行流
function onAnimateA() {
    //红包移动
    $('.index .hb .hbimg').animate({
        'top': '0px',
        'opacity': '1'
    }, 1000, function() {
        $('.index .hb .logo').fadeIn(800, function() { //logo浮现
            $('.index .works img').addClass('shake');
            $('.index .works').fadeIn(300, function() { //酒瓶动效
                $('.index .works_b,.index .tt_move').fadeIn(500, function() { //瓶盖出现
                    $('.index .shine').fadeIn(1500);
                    $('.index .tt_move .title_1,.index .tt_move .title_2,.index .tt_move .title_3,.index .tt_move .title_4').addClass('rs');
                    setTimeout(function() {
                        $('.index .tt_move .text_1').addClass('mas');
                        setTimeout(function() {
                            $('.index .tt_move .text_2').addClass('mas');
                            setTimeout(function() {
                                $('.index .tt_move .text_3').addClass('mas');
                                setTimeout(function() {
                                    $('.index .tt_move .text_bg').fadeIn(500);
                                    $('.index .tt_move .text_4').addClass('mas');
                                    $('.index .tt_move').addClass('hxm');
                                    $('.index .b_cont .b_move').fadeIn(200);
                                    //初始化eraser
                                    $('#mskImg').eraser({
                                        "size": 30,
                                        "completeRatio": .5,
                                        "completeFunction": onEraser
                                    });
                                }, 200);
                            }, 200);
                        }, 200);
                    }, 200);
                });
            });
        });
    });
}

//刮奖提示函数
function onEraser() {
    clearInterval(window.t);
    window.t = setInterval(function() {
        if (window.isWinnerComplete == 1) {
            setTimeout(function() {
                //移除蒙层
                $('.index .sc_msk').fadeOut(200);
                if (window.isWinner == 1) {
                    // alert("恭喜您获得" +amount+ "元奖励！");
                    $('.codeMsk').fadeIn(200);
                } else {
                    // alert("红包已经被领走啦啦啦！");
                    $('.defMsk').fadeIn(200);
                }
            }, 1000);
            clearInterval(window.t);
        }
    }, 50);
    //为刮开的图层设定点击事件
    $('.index .b_cont .sc_cont').off().on('touchstart', function() {
        if (window.isWinner == 1) {
            $('.codeMsk').fadeIn(200);
        } else {
            $('.defMsk').fadeIn(200);
        }
    });
    $('.index .b_cont .de_cont').off().on('touchstart', function() {
        $('.defMsk').fadeIn(200);
    });
    console.log("刮开了！");
}

//窗口改变对应事件
function onResize() {
    var windowWidth = $(window).width();
    var windowHeigh = $(window).height();
    var conWidth = $('.scene').width();
    var conHeight = $('.scene').height();
    var scaleX = windowWidth / conWidth;
    var scaleY = windowHeigh / conHeight;

    $('.scene').css({
        'transform-origin': '0% 0% 0px',
        '-webkit-transform-origin': '0% 0% 0px',
        '-moz-transform-origin': '0% 0% 0px',
        '-ms-transform-origin': '0% 0% 0px',
        'transform': 'scale(' + scaleX + ',' + scaleY + ')',
        '-webkit-transform': 'scale(' + scaleX + ',' + scaleY + ')',
        '-moz-transform': 'scale(' + scaleX + ',' + scaleY + ')',
        '-ms-transform': 'scale(' + scaleX + ',' + scaleY + ')'
    });
}
