var tWidth = tHeight = 0;
var NUMBER_OF_LEAVES = 15;
var tIndex=0;
var shiliObj=["    感谢你来到我的生命中，感谢你让我感受到了温暖和幸福,感谢你给我的支持和力量，感谢你……以后的日子，我们风雨同行，感谢上天让我们相遇！","    在这个平凡却不普通的日子里，我想起了你。你是夏天的凉风，是秋日的阳光，感谢你给我带来力量！","　　你给的温暖和爱，我统统都记得！谢谢你，我爱你！感恩节快乐！"]
var isTouch=[true,false,false]
$(function(){
	tWidth = $(window).width();
	  if(navigator.userAgent.indexOf("Safari")>0) { 
    	tHeight = $(window).height();
	  }else{
   		tHeight = window.screen.height-64;
	  }
})

$(document).ready(function(e) {
    _initPage();
	init();
	
	
	$(".music").hide();
	
    WeixinApi.ready(function(Api) {
        isWinxin = true;
        // 隐藏浏览器下方的工具栏
        Api.hideToolbar();

        // 获取网络状态
        Api.getNetworkType(function(network) {
            // 拿到 network 以后，做任何你想做的事
            if (network.indexOf("wifi") != -1) {
                $(".music").show();
                _initMusic();
            }
        });
    });
	
	
	TweenMax.to($(".page .topMove"), 0, {
		y:80,
		opacity: 0
	});
	TweenMax.to($(".in1Dom > img"), 0, {
		scale:0,
		opacity: 0
	});
	TweenMax.to($(".in1_1Info"), 0, {
		rotationX:-90,
		opacity: 0
	});
	
	$.get($(".in1Dom > img").attr("src"),function(){
		setTimeout(function(){_animiate();},100);
	})
});
function _addMove(){
	if(tIndex==2){
		if($("#sName").val()==""){
			alert("请填写收件人!");
			$("#sName").focus();
			return;
		}
		
		if($("#fontText").val()==""){
			alert("请填写祝福语!");
			$("#fontText").focus();
			return;
		}
		
		if($("#jName").val()==""){
			alert("请填写寄件人!");
			$("#jName").focus();
			return;
		}
	}
	tIndex++;
	_initAdd();
    $(".pageDom").animate({
            "left": -$(".pageDom").find("li").width() * tIndex
    },
    "fast", "",
    function() {
	});
}
function _initMusic() {
    var audios = document.getElementsByTagName('audio');
    PageMusic = audiojs.create(audios[0]);音乐开关 = true;
    PageMusic.load("mp3/music.mp3"); 
    PageMusic.setVolume(0.2);
}
function MusicPause() {
    PageMusic.playPause();
}
function _initPlay() {
    if (音乐开关) {
        $("#bottomBgImg").attr("src", "img/input/music_off.jpg");音乐开关 = false;
    } else {
        $("#bottomBgImg").attr("src", "img/input/music_on.jpg");音乐开关 = true;
    }
    MusicPause();
}
function _initPage() {
	$("#container,.pageInfo,.page").css({
        "width": tWidth,
        "height": tHeight
    });
	$(".pageDom").width($(".page").length*tWidth);
	
	$(".yunDom .yun").each(function(index, element) {
        TweenMax.to($(this), _random(3,4), {
			x: 50*_random(1,2)*Math.pow( -1 , Math.ceil( Math.random()*1000 ) ),
			repeat: -1,
			yoyo: true,
			ease: Linear.easeNone
		});
    });
	
	$(".music").click(function(e) {
        _initPlay();
    });
	_initHScroll($("#container"));
}
function _animiate(){
	TweenMax.to($(".page .topMove"), 0, {
		y:80,
		opacity: 0
	});
	TweenMax.to($(".in2InfoDom"), 0, {
		rotation:-720,
		opacity: 0,
		scale:0,
		x:400,
		y:-400
	});
	TweenMax.to($(".in1Dom > img"), 0, {
		scale:0,
		opacity: 0
	});
	TweenMax.to($(".in1_1Info"), 0, {
		rotationX:-90,
		opacity: 0
	});
	if(tIndex==0){
		TweenMax.to($(".in1Dom > img"), 1, {
			scale:1,
			opacity: 1,
			ease: Back.easeOut
		});
		$(".in1_1Info").each(function(index, element) {
			TweenMax.to($(this), .6, {
				rotationX:0,
				opacity: 1,
				delay:.8+.2*index,
				ease: Back.easeOut
			});
		});
		$(".page:eq("+tIndex+") .topMove").each(function(index, element) {
			TweenMax.to($(this), .6, {
				y:0,
				opacity: 1,
				delay:1+.25*index
			});
		});
		setTimeout(function(){
		TweenMax.to($(".tishi"), 1, {
			x: -10,
			repeat: -1,
			yoyo: true,
			ease: Linear.easeNone
		});
		},2000);
	}
	if(tIndex!=0){
		TweenMax.to($(".page:eq("+tIndex+") .in2InfoDom"), 1, {
			rotation:0,
			opacity: 1,
			scale:1,
			x:0,
			y:0
		});
		$(".page:eq("+tIndex+") .topMove").each(function(index, element) {
			TweenMax.to($(this), .6, {
				y:0,
				opacity: 1,
				delay:.25*index+.6
			});
		});
	}
	else{
		$(".page:eq("+tIndex+") .topMove").each(function(index, element) {
			TweenMax.to($(this), .6, {
				y:0,
				opacity: 1,
				delay:.25*index
			});
		});
	}
}

function _initHScroll(dom) {
    var nHStartX;
    var isHMove = false;
    var twoStartLeft = 0;
    var hMoveLength = 0;
    var hMaxLength = 70;
    var twoF;
    var moveTime;
    function _initHMoveStart(e) {
		if(isTouch[tIndex]){
        if (e.type == "touchstart") {
            nHStartX = event.touches[0].pageX;
        } else {
            nHStartX = e.x || e.pageX;
        }
        twoStartLeft = parseInt($(this).find("ul").css("left").replace("px", ""));
        isHMove = true;
		}
    }
    function _initHMoveMove(e) {
		if(isTouch[tIndex]){
    		event.preventDefault();
    		event.stopPropagation();
        if (isHMove) {
            var moveP;
            if (e.type == "touchmove") {
                moveP = event.touches[0].pageX;
            } else {
                moveP = e.x || e.pageX;
            }
            var hm = nHStartX - moveP;
            var dom = $(this).find("ul");
            twoF = "";
            if (hm < 0&&tIndex!=0) {
                //if (tIndex > 0) {
                dom.css("left", twoStartLeft - hm);
                twoF = "--";
                //}
            }
            if (hm > 0) {
                //if (tIndex < $(this).find("ul li").length - 1) {
                dom.css("left", twoStartLeft - hm);
                twoF = "++";
                //}
            }
            hMoveLength = Math.abs(nHStartX - moveP);
        }
		}
    }
    function _initHMoveEnd(e) {
		if(isTouch[tIndex]){
        var t = $(this);
        if (hMoveLength > hMaxLength) {
            _move();
        } else {
            t.find("ul").animate({
                "left": twoStartLeft
            },
            "fast");
        }
        isHMove = false;
        hMoveLength = 0;
		}
    }
    function _init() {
        dom.on("mousedown touchstart", _initHMoveStart);
        dom.on("mousemove touchmove", _initHMoveMove);
        dom.on("mouseup touchend", _initHMoveEnd);
		
    }
    function _move() {
        if (twoF == "--") { //上一页
            //if (tIndex > 0) { //第一页不能再上一页
            tIndex--;
            _initAdd();
            dom.find("ul").animate({
                "left": -dom.find("ul li").width() * tIndex
            },
            "fast", "",
            function() {
            });
            //}
        }
        if (twoF == "++") { //下一页
            //if (tIndex < dom.find("ul li").length - 1) { //最后一页不能再下一页
            tIndex++;
            _initAdd();
            dom.find("ul").animate({
                "left": -dom.find("ul li").width() * tIndex
            },
            "fast", "",
            function() {
            });
            //}
        }
    }
    _init();
}

function _initTishi(){
	$(".tishiDom").fadeIn("fast");
	setTimeout(function(){$(".tishiDom").fadeOut("fast")},2000);
}
function _initAdd() {
	if (tIndex < 0) {
		tIndex = $(".pageDom").find("li").length - 1;
	}
	if (tIndex > $(".pageDom").find("li").length - 1) {
		tIndex = 0;
	}
	if(tIndex==2){
		$("#fontText").val(shiliObj[0]);
	}
	if(tIndex==3){
		$("#toName1").html($("#sName").val());
		$("#toFontText").val($("#fontText").val());
		$("#toName2").html($("#jName").val());
	}
	_animiate();
}
var qieI=0;
function _qieFont(){
	qieI++;
	if(qieI>shiliObj.length-1){qieI=0;}
	$("#fontText").val(shiliObj[qieI]);
}
function fontDisabled(){
	$("#fontText").attr("readonly",false);
	$("#fontText").val("");
	$("#fontText").focus();
}
function _random(min,max){
    return Math.floor(min+Math.random()*(max-min));
}
function init() {
	$("#moveCss").append("@-webkit-keyframes drop { 0% {-webkit-transform: translate(0px, -0px);} 100% {-webkit-transform: translate(0px, "+(parseInt($(window).height())+50)+"px);}}");
    var container = document.getElementById('leafContainer');
    for (var i = 0; i < NUMBER_OF_LEAVES; i++) {
        container.appendChild(createALeaf())
    }
}
function randomInteger(low, high) {
    return low + Math.floor(Math.random() * (high - low))
}
function randomFloat(low, high) {
    return low + Math.random() * (high - low)
}
function pixelValue(value) {
    return value + 'px'
}
function durationValue(value) {
    return value + 's'
}
function createALeaf() {
    var leafDiv = document.createElement('div');
    var image = document.createElement('img');
    image.src = 'img/icon/in' + randomInteger(1, 10) + '.png';
    leafDiv.style.top = "-82px";
    leafDiv.style.left = pixelValue(randomInteger(10, tWidth-20));
    var spinAnimationName = (Math.random() < 0.5) ? 'clockwiseSpin': 'counterclockwiseSpinAndFlip';
    leafDiv.style.webkitAnimationName = 'fade, drop';
    image.style.webkitAnimationName = spinAnimationName;
    var fadeAndDropDuration = durationValue(randomFloat(5, 11));
    var spinDuration = durationValue(randomFloat(4, 8));
    leafDiv.style.webkitAnimationDuration = fadeAndDropDuration + ', ' + fadeAndDropDuration;
    var leafDelay = durationValue(randomFloat(0, 5));
    leafDiv.style.webkitAnimationDelay = leafDelay + ', ' + leafDelay;
    image.style.webkitAnimationDuration = spinDuration;
    leafDiv.appendChild(image);
    return leafDiv
}