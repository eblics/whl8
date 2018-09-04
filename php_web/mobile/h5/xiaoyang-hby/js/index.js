// JavaScript Document
var playScore = 0;
var playCount = 0;
var tWidth = 0;
var tHeight = 0;
var NUMBER_OF_LEAVES = 40;
var load_img = [];
load_img.push('img/hb.png');
load_img.push('img/cloud.png');
load_img.push('img/drink.png');

var load_img_progress = 0;
var load_img_total = load_img.length;

// 资源图片加载
jQuery.imgpreload(load_img, {
    all: function() {
        $('.page1').fadeIn("fast", function() {
            init();
            music();
            $(".page1").delay(4000).fadeOut("fast", function() {
                $(".page2").fadeIn("fast", function() {
                    cloudAnimate();
                });
            });
        });

    }
});

$(document).ready(function(e) {
    tWidth = $(window).width();
    if (navigator.userAgent.indexOf("Safari") > 0) {
        tHeight = $(window).height();
    } else {
        tHeight = window.screen.height - 64;
    }
    $(".page").height($(window).height());
    $(".overDom").height($(window).height());
    _initHScroll_($(".page4"));
    $(".drink").delay(300).fadeIn(1000);
    // $(".hbDom").animate({"left":"0"},500,function(){ hbAnimate()});
    // $(".product").animate({"left":"0"},500,function(){ productAnimate()});

    $(".main").on("touchmove", function() {
        /*event.preventDefault();
        event.stopPropagation();*/
    });
    $(".btn_rule").on('touchend', function() {
        $(".ruleDom").fadeIn("fast");
    });
    $(".btn_close").on('touchend', function() {
        $(".ruleDom").fadeOut("fast");
    });
    $(".btn_start").on('touchend', function() {
        $(".page2").fadeOut("fast", function() {
            $(".page3").fadeIn("fast", function() {
                //start();
                startTimeAnimate();
            });
        });
    });
    $(".btn_pointRank").on('touchend', function() {
        $(".pointRank").fadeIn();
    });
    $(".btn_pointDetail").on('touchend', function() {
        $(".pointDetail").fadeIn();
    });
    $(".btn_rank_close").on('touchend', function() {
        $(".pointRank").fadeOut();
    });
    $(".btn_detail_close").on('touchend', function() {
        $(".pointDetail").fadeOut();
    });
    $(".btn_jd").on('touchend', function() {
        window.location.href = "http://haocaitou.jd.com";
    });
    $(".btn_again").on('touchend', function() {
        var newurl = location.href + "?t=" + new Date().getTime();
        location.href = newurl;
        // location.reload()
        $("page").css("display", "none");
        $(".page2").fadeIn("fast");
    });

});
//点击开始游戏之后出现3S倒数
function startTimeAnimate() {
    $(".overDom").fadeIn("fast", function() {
        setTimeout(function() {
            $(".num").html("3");
        }, 1000);
        setTimeout(function() {
            $(".num").html("2");
        }, 2000);
        setTimeout(function() {
            $(".num").html("1");
        }, 3000);
        $(".overDom").delay(3500).fadeOut("fast", function() {
            start();
        });
    })
}

function init() {
    $("#moveCss").append("@-webkit-keyframes drop { 0% {-webkit-transform: translate(0px, -0px);} 100% {-webkit-transform: translate(0px, " + (parseInt($(window).height()) + 50) + "px);}}");
    var container = document.getElementById('leafContainer');
    for (var i = 0; i < NUMBER_OF_LEAVES; i++) {
        container.appendChild(createALeaf())
    }
}
//星星动画效果
function startAnimate(dom) {
    $(dom).animate({
        opacity: 1
    }, 500, function() {
        $(dom).animate({
            opacity: 0.2
        }, 500, function() {
            startAnimate(dom);
        });
    });
}
//红包动画
function hbAnimate() {
    $(".hbDom").animate({
        "left": "10px"
    }, 500, function() {
        $(".hbDom").animate({
            "left": "-10px"
        }, 500, function() {
            $(".hbDom").animate({
                "left": "10px"
            }, 500, function() {
                $(".hbDom").animate({
                    "left": "-10px"
                }, 500, function() {
                    $(".hbDom").animate({
                        "left": "0"
                    }, 500, function() {
                        setTimeout(function() {
                            hbAnimate();
                        }, 2000);
                    });
                });
            });
        });
    });
}
//产品动画
// function productAnimate(){
// 	$(".product").animate({"left":"10px"},500,function(){
// 		$(".product").animate({"left":"-10px"},500,function(){
// 			$(".product").animate({"left":"10px"},500,function(){
// 				$(".product").animate({"left":"-10px"},500,function(){
// 					$(".product").animate({"left":"0"},500,function(){
// 						setTimeout(function(){productAnimate();},2000);
// 			        });
// 			    });
// 		    });
// 		});
// 	});
// }
//云朵动画效果
function cloudAnimate() {
    $(".cloud1").animate({
        "left": "100%"
    }, 15000, function() {
        $(".cloud1").css("top", "30px");
        $(".cloud1").animate({
            "left": "-154px"
        }, 15000);
    });
    $(".cloud2").animate({
        "right": "100%"
    }, 17000, function() {
        $(".cloud2").css("top", "30px");
        $(".cloud2").animate({
            "right": "-154px"
        }, 17000);
    });
    $(".cloud3").animate({
        "right": "100%"
    }, 20000, function() {
        $(".cloud3").css("top", "70px");
        $(".cloud3").animate({
            "right": "-154px"
        }, 20000, function() {
            $(".cloud1").css("top", "60px");
            $(".cloud3").css("top", "40px");
            cloudAnimate();
        });
    });
}

function randomInteger(low, high) {
    return low + Math.floor(Math.random() * (high - low));
}

function randomFloat(low, high) {
    return low + Math.random() * (high - low);
}

function pixelValue(value) {
    return value + 'px';
}

function durationValue(value) {
    return value + 's';
}
//创建金币
function createALeaf() {
    var leafDiv = document.createElement('div');
    var image = document.createElement('img');
    image.src = 'img/icon/in' + randomInteger(1, 10) + '.png';
    leafDiv.style.top = "-82px";
    leafDiv.style.left = pixelValue(randomInteger(10, tWidth - 20));
    var spinAnimationName = (Math.random() < 0.5) ? 'clockwiseSpin' : 'counterclockwiseSpinAndFlip';
    leafDiv.style.webkitAnimationName = 'fade, drop';
    image.style.webkitAnimationName = spinAnimationName;
    var fadeAndDropDuration = durationValue(randomFloat(2, 4));
    var spinDuration = durationValue(randomFloat(1, 5));
    leafDiv.style.webkitAnimationDuration = fadeAndDropDuration + ', ' + fadeAndDropDuration;
    var leafDelay = durationValue(randomFloat(0, 3));
    leafDiv.style.webkitAnimationDelay = leafDelay + ', ' + leafDelay;
    image.style.webkitAnimationDuration = spinDuration;
    leafDiv.appendChild(image);
    return leafDiv;
}
// ########################################################################################
var timer, timer_c, timer_bomb, timer_over, over_tm;
var num = 0;
var in_min = 400,
    in_max = 800;
var Interval = 300;
var speed = 10;
var score = 0;
var overtime = 15 * 1000;
var isover = false;
var bomb = false;
var life = 3;

var audio_m = document.getElementById('audio-m');
var audio_d1 = document.getElementById('audio-d1');
var audio_d2 = document.getElementById('audio-d2');
var audio_b = document.getElementById('audio-b');

function music() {
    audio_m.load();
    audio_d1.load();
    audio_b.load();
    audio_m.load();
    audio_m.play();
    $(".music_btn").off().on('touchstart', function() {
        if (!audio_m.paused) {
            audio_m.pause();
            $(".music_btn img").attr('src', 'img/stop.png');
        } else {
            audio_m.play();
            $(".music_btn img").attr('src', 'img/play.png');
        }
    });
}

function start() {
    // audio_m.play();
    playScore = 0;
    box();
    setTimeout(function() {
        timer_c = setInterval(function() {
            collide();
        }, 100)
    }, 500);
    // setTimeout(function(){
    //     Interval = 700;
    // },5000);
    // setTimeout(function(){
    //     Interval = 500;
    // },10000);
    // setTimeout(function(){
    //     Interval = 400;
    // },15000);
    timer_over = setTimeout(function() {
        over();
    }, overtime);
    over_t();
    //handShake1();
    var draggie = new Draggabilly('#man', {
        axis: 'xy',
        containment: '.main'
    });
}


//生成下落礼物
function box() {
    game();
    timer = setTimeout(function() {
        box();
    }, Interval);
}

var box_left = 0;
var last_box_left = 0;

function game() {
    var xz = _random(100, 150);
    var dirnkNum = randomNum(1, 6);
    var el = document.createElement("div");
    //console.log(dirnkNum)
    if (dirnkNum == 4) {
        el.className = "box collide bomb";
    } else if (dirnkNum == 3) {
        el.className = "box collide collide1";
    } else {
        el.className = "box collide collide2";
    }
    // while(Math.abs(last_box_left - box_left) < 100){
    box_left = _random(10, (tWidth - $('.box').width() - 100));
    // }
    el.style.left = box_left + 'px';
    el.style.display = "block";
    var img = document.createElement("img");
    // console.log(img_in)
    img.src = "img/dirnk/" + dirnkNum + ".png";
    $(".page3").append(el);
    el.appendChild(img);

    // if(overtime_num/10 >= 0.85){
    // 	$(".page3 .box").css({
    // 		'-webkit-animation': 'boxfadeInDownBig '+overtime_num/10+'s linear'
    // 	})
    // }else{
    // 	$(".page3 .box").css({
    // 		'-webkit-animation': 'boxfadeInDownBig 0.85s linear'
    // 	})
    // }
    // console.log(Math.abs(last_box_left - box_left))
    last_box_left = box_left;


    //移除元素
    $(".box").each(function(index, element) {
        $(this).unbind("webkitAnimationEnd AnimationEnd");
        $(this).on("webkitAnimationEnd AnimationEnd", function() {
            $(this).remove();
        });
    });
}

function showScore(score) {
    TweenMax.from($(".getScore"), 0.5, {
        y: 20,
        opacity: 1,
        delay: 0.3
    });
    TweenMax.to($(".getScore"), 0.5, {
        y: -10,
        opacity: 0,
        delay: 0.3
    });
    $(".score").html(score);
}
//生成指定范围内的随机整数
function randomNum(minNum, maxNum) {
    switch (arguments.length) {
        case 1:
            return parseInt(Math.random() * minNum + 1);
            break;
        case 2:
            return parseInt(Math.random() * (maxNum - minNum + 1) + minNum);
            break;
        default:
            return 0;
            break;
    }
}
//获取范围内的随机数
function _random(min, max) {
    return Math.floor(min + Math.random() * (max - min));
}

//游戏结束
var is_over = true;

function over() {
    // audio_m.pause();
    isover = true;
    clearTimeout(timer, over_tm);
    // clearInterval(timer_c);
    // $(".topman").css('animation-play-state','paused');
    $(".page3 .man").css({
        'left': '44%'
    });
    $(".page").delay(2000).fadeOut("fast");
    $(".page4").fadeIn("fast", function() {
        take_ajax();
        setTimeout(function() {
            clearInterval(timer_c);

            // TweenMax.from($(".jt"), 1, {
            //                 y:-10,
            //                 repeat: -1,
            //                 yoyo:true
            // });
            // TweenMax.to($(".jt"), 1, {
            //                     y:10,
            //                     repeat: -1,
            //                     yoyo:true
            // });
            $(".font").fadeOut("fast");
            TweenMax.to($(".rhb"), 1, {
                opacity: 1,
                scale: 1,
                onComplete: function() {
                    $(".star").css("display", "block");
                    // $(".jt").css("opacity","1");
                    $(".gameInfo").fadeIn("fast");
                    setInterval(function() {
                        startAnimate($(".star"));
                    }, 500);
                }
            });
        }, 3000)

        hlsjs.takeActivity(function(resp) {
            console.log(resp);
        });
    });
    //***次数用完后，跳转到“game_over.html”页面


    playCount += 1;
    // $('.yshMoneyDom .')
    // $(".gameInfo .gameNumDom .gameNum").html(3-playCount);
    /*$(".over").fadeIn();
    $(".over .word p").html(score);

    $(".over .btn img.btn1").click(function(){
    	location.reload();
    })
    $(".over .btn img.btn2").click(function(){
    	$(".wrap").fadeOut();
    	$(".over").fadeOut();
    	$(".result").fadeIn();
    	result();
    })
    $(".over .btn img.btn3").click(function(){
    	$(".share").fadeIn();
    })
    $(".share_btn img").click(function(){
    	location.reload();
    })*/
}

var overtime_num = overtime / 1000;
//倒计时
function over_t() {
    $("#time").text(overtime_num);
    overtime_num--;
    // console.log(overtime_num)
    if (overtime_num < 0) {
        $("#time").text('00');
    } else if (overtime_num >= 0 && overtime_num < 10) {
        $("#time").text('0' + overtime_num + '');
    } else {
        $("#time").text(overtime_num);
    }

    over_tm = setTimeout(function() {
        over_t();
    }, 1000)
}

//碰撞检测
var b_left, b_top, b_width, m_left, m_top, m_width;
//var adds = "<img class='adds' style='margin-bottom:10px' src='img/game/main/adds.png'>"
function collide() {
    $(".box").each(function(index, element) {
        if ($(this).hasClass('collide')) {
            b_left = $(this).offset().left;
            b_top = $(this).offset().top;
            b_width = $(this).width();
            m_left = $("#man").offset().left;
            m_top = $("#man").offset().top;
            m_width = $("#man").width();
            if (b_left > m_left && b_left < m_left + m_width) {
                if (b_top > m_top && b_top < m_top + (m_width / 2)) {
                    if ($(this).hasClass('bomb')) {
                        audio_b.play();
                        prize("bomb");
                        // 	over();
                    } else if ($(this).hasClass('collide1')) {
                        prize("collide1");
                    } else {
                        prize("collide2");
                    }
                    $(this).remove();

                } else if (b_top < m_top && b_top + b_width > m_top) {
                    if ($(this).hasClass('bomb')) {
                        audio_b.play();
                        prize("bomb");
                        // 	over();
                    } else if ($(this).hasClass('collide1')) {
                        prize("collide1");
                    } else {
                        prize("collide2");
                    }
                    // if($(this).find('img').hasClass('bomb')){
                    // 	audio_b.play();
                    // 	over();
                    // }else{
                    //	prize();
                    // }
                    $(this).remove();
                }
            } else if (b_left < m_left && b_left + b_width > m_left) {
                if (b_top > m_top && b_top < m_top + (m_width / 2)) {
                    if ($(this).hasClass('bomb')) {
                        audio_b.play();
                        prize("bomb");
                        // 	over();
                    } else if ($(this).hasClass('collide1')) {
                        prize("collide1");
                    } else {
                        prize("collide2");
                    }
                    // if($(this).find('img').hasClass('bomb')){
                    // 	audio_b.play();
                    // 	over();
                    // }else{
                    //	prize();
                    // }
                    $(this).remove();
                } else if (b_top < m_top && b_top + b_width > m_top) {
                    if ($(this).hasClass('bomb')) {
                        audio_b.play();
                        prize("bomb");
                        // 	over();
                    } else if ($(this).hasClass('collide1')) {
                        prize("collide1");
                    } else {
                        prize("collide2");
                    }
                    // if($(this).find('img').hasClass('bomb')){
                    // 	audio_b.play();
                    // 	over();
                    // }else{
                    //	prize();
                    // }
                    $(this).remove();
                }
            }
        }
    });
}
//bomb
t_num = 1;
//接到物品时调用
function prize(type) {
    //$(".man").prepend(adds);
    /*setTimeout(function(){
    	$(".man").find('.adds').remove();
    },400)*/
    //score += 25;
    //console.log(type)
    if (type == "bomb") {
        showScore("-100");
        playScore -= 100;
        $(".money").html((parseInt($(".money").html()) - 100));
    } else if (type == "collide1") {
        if (!audio_d1.paused) {
            audio_d2.play();
        } else {
            audio_d1.play();
        }
        showScore("+100");
        playScore += 100;
        $(".money").html((parseInt($(".money").html()) + 100));
    } else {
        showScore("+200");
        playScore += 200;
        $(".money").html((parseInt($(".money").html()) + 200));
        if (!audio_d1.paused) {
            audio_d2.play();
        } else {
            audio_d1.play();
        }
    }
    //$(".score").text(score);
}
//向下滑动出现二维码
function _initHScroll_(dom) {
    var nHStartX;
    var isHMove_ = false;
    var twoStartLeft = 0;
    var hMoveLength_ = 0;
    var hMaxLength = 70;
    var twoF;
    var moveTime;

    function _initHMoveStart(e) {
        if (e.type == "touchstart") {
            nHStartX = event.touches[0].pageY;
        } else {
            nHStartX = e.y || e.pageY;
        }
        isHMove_ = true;
    }

    function _initHMoveMove(e) {
        event.preventDefault();
        event.stopPropagation();
        if (isHMove_) {
            var moveP;
            if (e.type == "touchmove") {
                moveP = event.touches[0].pageY;
            } else {
                moveP = e.y || e.pageY;
            }
            var hm = nHStartX - moveP;
            twoF = "";
            if (hm > 0) {
                //if (twoIndex < $(this).find("ul li").length - 1) {
                //dom.css("top", twoStartLeft - hm);

                twoF = "++";
                //}
            }
            hMoveLength_ = Math.abs(nHStartX - moveP);
        }
    }

    function _initHMoveEnd(e) {
        if (hMoveLength_ > hMaxLength && twoF == "++") {
            _heziAnimate();
        }
    }

    function _init() {
        dom.on("mousedown touchstart", _initHMoveStart);
        dom.on("mousemove touchmove", _initHMoveMove);
        dom.on("mouseup touchend", _initHMoveEnd);
    }
    _init();
}

function _heziAnimate() {
    $(".page4").fadeOut("fast");
    $(".page5").fadeIn("fast");
}