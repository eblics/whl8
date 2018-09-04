/*
 *   created by zy.
 *
 *   本页需要后台添加的数据：
 *   1,数据请求，替换模拟数据
 *
 */
window.isWinnerComplete = 0;
window.isWinner = 0;
var isSend = 0;
$(function () {

    $('body').height($(window).height()); //固定内容高度

    //判断移动端类型
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

    // //做移动适配
    // if(isAndroid){
    //     var str = '<div class="content"><img class="code_img" src="images/s_msk1.png"><div class="closeBtn"></div></div>';
    //     $('.codeMsk').html(str);

    //     //二维码图层关闭事件
    //     $('.codeMsk .closeBtn').off().on('tap',function(event){
    //         event.stopPropagation();
    //         event.preventDefault();
    //         if(event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("codeMsk") < 0) {
    //             return;
    //         }
    //         $('.codeMsk').fadeOut(200);
    //     });
    // } else {
    //     //二维码图层关闭事件
    //     $('.codeMsk .closeBtn,.code_img').off().on('tap',function(event){
    //         event.stopPropagation();
    //         event.preventDefault();
    //         if(event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("code_img") < 0) {
    //             return;
    //         }
    //         $('.codeMsk').fadeOut(200);
    //     });
    // }

    //二维码图层关闭事件
    $('.codeMsk .closeBtn').off().on('tap', function (event) {
        event.stopPropagation();
        event.preventDefault();
        if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("codeMsk") < 0) {
            return;
        }
        $('.codeMsk').fadeOut(200);
    });

    //规则点击事件
    $('.index .rule_btn').off().on('tap', function () {
        $('.ruleMsk').fadeIn(200);
        $('.ruleMsk .closeBtn, .ruleMsk').off().on('tap', function (event) {
            event.stopPropagation();
            event.preventDefault();
            if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("ruleMsk") < 0) {
                return;
            }
            $('.ruleMsk').fadeOut(200);
        });
    });

    //开心一刻图层关闭事件
    $('.defMsk .closeBtn').off().on('tap', function (event) {
        event.stopPropagation();
        event.preventDefault();
        if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("defMsk") < 0) {
            return;
        }
        $('.defMsk').fadeOut(200);
    });

    hlsjs.ready(function (x) {
        $('.index .de_cont').hide();
        $('.index .sc_cont').show();
        $('.scene .index .b_cont .sc_msk').off().on('touchstart', function() {
            Pace.stop();
            $('.sc_msk').off('touchstart');
            $('#errmsg').html('正在抽奖..');
            hlsjs.takeActivity(function (data) {
                console.log('start');
                window.isWinnerComplete = 1;
                $('.index .de_cont').hide();
                $('.index .sc_cont').show();
                if (data.errcode == 0) {
                    if (data.datatype == 0) {
                        //奖品类型为：红包
                        //data.amount 中奖金额，单位分，实际使用元需要/100
                        console.log(1);
                        window.isWinner = 1;
                        $('#errmsg').html('恭喜获得了' + (Number(data.amount) / 100).toFixed(2) + '元红包');
                    }
                    if (data.datatype == 2) {
                        //奖品类型为：乐券
                        //data.data.name 奖品名称
                        console.log(2);
                        window.isWinner = 1;
                        $('#errmsg').html('恭喜获得了' + data.data.name);
                    }
                    if (data.datatype == 3) {
                        //奖品类型为：积分
                        //data.amount 中奖积分额度
                        console.log(3);
                        window.isWinner = 1;
                        $('#errmsg').html('恭喜获得了' + data.amount + '积分');
                    }
                    if (data.datatype == 100) {
                        //奖品类型为：红包、乐券、积分的叠加类型
                        if (data.multiData.length > 0) {
                            for (var i = 0; i < result.multiData.length; i++) {
                                if (data.multiData[i].strategyType == 0) {
                                    //奖品类型为：红包
                                    console.log(4);
                                    window.isWinner = 1;
                                    $('#errmsg').html('恭喜获得了' + (Number(result.multiData[i].value) / 100).toFixed(2) + '元红包');
                                }
                                if (data.multiData[i].strategyType == 2) {
                                    //奖品类型为：乐券
                                    console.log(5);
                                    window.isWinner = 1;
                                    $('#errmsg').html('恭喜获得了' + result.multiData[i].value);
                                }
                                if (data.multiData[i].strategyType == 3) {
                                    //奖品类型为：积分
                                    console.log(6);
                                    window.isWinner = 1;
                                    $('#errmsg').html('恭喜获得了' + result.multiData[i].value + '积分');
                                }
                            }
                        } else {
                            //未中奖
                            console.log(7);
                            window.isWinner = 0;
                            $('#alttext').html(data.alt_text);
                        }
                    }
                } else if (data.errcode == 20) {
                    //未中奖
                    console.log(8);
                    window.isWinner = 0;
                    $('#alttext').html(data.alt_text);
                    $('#errmsg').html("未中奖");
                } else if (data.errcode == 2) {
                    //此码已被他人扫过
                    console.log(9);
                    window.isWinner = 0;
                    $('#alttext').html(data.alt_text);
                    $('#errmsg').html("此码已被他人扫过");
                } else if (data.errcode == 3) {
                    //您已扫过此码
                    console.log(10);
                    window.isWinner = 0;
                    $('#alttext').html(data.alt_text);
                    $('#errmsg').html("您已扫过此码");
                } else {
                    //失败
                    console.log(11);
                    window.isWinner = 0;
                    //data.errmsg 失败错误信息
                    $('#alttext').html(data.alt_text);
                    $('#errmsg').html(data.errmsg);
                };
            });
        });

    });
});

/*加载进度条*/
(function () {
    Pace.start({
        ajax: false,
        restartOnPushState: false,
        restartOnRequestAfter: false,
        document: false
    });
    Pace.on('done', function () { //加载完成
        console.log('done');
        // $.post('/activity/take',{code:hlsjs.getQueryString('code')},function(result){
        //         if(result.errcode == 0){
        //             window.isWinner=1;
        //             $('#errmsg').html('恭喜获得了'+(Number(result.amount)/100).toFixed(2)+'元红包！');
        //         }
        //         else if(result.errcode==20){
        //             window.isWinner=0;
        //             $('#errmsg').html(result.errmsg);
        //             $('#alttext').html(result.alt_text);
        //         }
        //         else{
        //             window.isWinner=0;
        //             $('#errmsg').html(result.errmsg);
        //             $('#alttext').html(result.alt_text);
        //         }
        //         //设定中奖金额
        //         if(window.isWinner == 1){
        //             $('.index .de_cont').hide();
        //             $('.index .sc_cont').show();
        //         } else {
        //             $('.index .de_cont').show();
        //             $('.index .sc_cont').hide();
        //         }
        //
        // });



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
    // $('.index .tt').animate({'top': '50px', 'opacity': '1'}, 1000, function () {
    //     // $('.index .works').fadeIn(300, function () { //酒瓶动效
    //     //     $('.index .works img').addClass('shake');
    //     //     $('.index .b_cont .b_move').fadeIn(200);
    //     //     //初始化eraser
    //     //     $('#mskImg').eraser({
    //     //         "size": 30,
    //     //         "completeRatio": .5,
    //     //         "completeFunction": onEraser
    //     //     });
    //     // });
    //
    // });

    $('.index .b_cont .b_move').fadeIn(200);

    //初始化eraser
    $('#mskImg').eraser({
        "size": 30,
        "completeRatio": .5,
        "completeFunction": onEraser
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


    // if (window.isWinner === 1 && window.isWinnerComplete === 1) {
    //     // alert("恭喜您获得" +amount+ "元奖励！");
    //     $('.codeMsk').fadeIn(200);
    //     //为刮开的图层设定点击事件
    //     $('.index .b_cont .sc_cont').off().on('tap', function () {
    //         $('.codeMsk').fadeIn(200);
    //     });
    // } else if(window.isWinner === 0 && window.isWinnerComplete === 1) {
    //     // alert("红包已经被领走啦啦啦！");
    //     $('.defMsk').fadeIn(200);
    //
    //     //为刮开的图层设定点击事件
    //     $('.index .b_cont .de_cont').off().on('tap', function () {
    //         $('.defMsk').fadeIn(200);
    //     });
    // }
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

    $('.scene,.scene_b').css({
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

