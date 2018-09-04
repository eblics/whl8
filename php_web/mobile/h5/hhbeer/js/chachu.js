$(function() {
    // 是否擦除完
    var chachuIs = false;
    // 擦除完执行chachuIsFun
    var chachuIsFun;
    // 抽奖结果
    var getRewardResult = null;
    var set_chachu_value = function(_a, event) {
        chachuIs = _a;
        if (event) {
            event();
        }
    };
    var canvas = document.getElementById("cas"),
        ctx = canvas.getContext("2d");
    var x1, y1, a = 10,
        timeout, totimes = 100,
        distance = 30;
    var saveDot = [];
    var canvasBox = document.getElementById("bb");
    // canvas.width = canvasBox.clientWidth;
    // canvas.height = canvasBox.clientHeight;
    canvas.width = $(".QR-result").width();
    canvas.height = $(".QR-result").height();
    var img = new Image();
    img.src = "images/QR-moca.jpg";
    img.onload = function() {
        var w = canvas.height * img.width / img.height;
        ctx.drawImage(img, (canvas.width - w) / 2, 0, w, canvas.height);
        tapClip()
    };

    function getClipArea(e, hastouch) {
        var x = hastouch ? e.targetTouches[0].pageX : e.clientX;
        var y = hastouch ? e.targetTouches[0].pageY : e.clientY;
        var ndom = canvas;
        while (ndom.tagName !== "BODY") {
            x -= ndom.offsetLeft;
            y -= ndom.offsetTop;
            ndom = ndom.parentNode;
        }
        return {
            x: x,
            y: y
        }
    }

    //通过修改globalCompositeOperation来达到擦除的效果
    function tapClip() {
        var hastouch = "ontouchstart" in window ? true : false,
            tapstart = hastouch ? "touchstart" : "mousedown",
            tapmove = hastouch ? "touchmove" : "mousemove",
            tapend = hastouch ? "touchend" : "mouseup";
        var area;
        var x2, y2;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.lineWidth = a * 2;
        var chachu = true;
        ctx.globalCompositeOperation = "destination-out";
        canvasBox.addEventListener(tapstart, function(e) {
            clearTimeout(timeout);
            e.preventDefault();
            area = getClipArea(e, hastouch);
            x1 = area.x;
            y1 = area.y;
            drawLine(x1, y1);
            this.addEventListener(tapmove, tapmoveHandler);
            this.addEventListener(tapend, function() {
                this.removeEventListener(tapmove, tapmoveHandler);
                //检测擦除状态
                timeout = setTimeout(function() {
                    var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    var dd = 0;
                    for (var x = 0; x < imgData.width; x += distance) {
                        for (var y = 0; y < imgData.height; y += distance) {
                            var i = (y * imgData.width + x) * 4;
                            if (imgData.data[i + 3] > 0) {
                                dd++
                            }
                        }
                    }
                    if (dd / (imgData.width * imgData.height / (distance * distance)) < 0.7 && chachu) {
                        canvas.className = "noOp";
                        $(".QR-hand").fadeOut(600);
                        chachu = false;
                        console.log("刮奖完毕");
                        set_chachu_value(true, chachuIsFun);
                    }
                }, totimes)
            });

            function tapmoveHandler(e) {
                clearTimeout(timeout);
                e.preventDefault();
                area = getClipArea(e, hastouch);
                x2 = area.x;
                y2 = area.y;
                drawLine(x1, y1, x2, y2);
                x1 = x2;
                y1 = y2;
            }
        })
    }

    function drawLine(x1, y1, x2, y2) {
        ctx.save();
        ctx.beginPath();
        if (arguments.length == 2) {
            ctx.arc(x1, y1, a, 0, 2 * Math.PI);
            ctx.fill();
        } else {
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
        }
        ctx.restore();
    }
    // 预先定义好mch_id 用户表单信息
    var mch_id;
    var mobile;
    var realname;
    var address;

    // 刚触摸抽奖区
    $('#cas').one('touchstart', function() {
        console.log("开始抽奖，向服务器请求数据...");
        hlsjs.ready(function(x) {
            hlsjs.takeActivity(function(data) {

                getRewardResult = data; //记录抽奖结果
                var fontN = [];
                var fontN = [{
                    font: "微"
                }, {
                    font: "波"
                }, {
                    font: "海"
                }, {
                    font: "尔"
                }, {
                    font: "电"
                }, {
                    font: "单"
                }, {
                    font: "相"
                }];
                var num = 0;
                num = parseInt(Math.random() * fontN.length);
                if (!localStorage.getItem("beerNumLocalhost")) {
                    localStorage.setItem("beerNumLocalhost", num);
                }
                var beerNumLocalhost = localStorage.getItem("beerNumLocalhost");
                if (data.errcode == 0) {
                    if (data.datatype == 2) {
                        if (data.data.name.indexOf("机") > -1) {
                            $(".QR-result").html("<div class=\"QR-word\"><span>\"机\"</span></div><em>字</em>");
                        } else if (data.data.name.indexOf("炉") > -1) {
                            $(".QR-result").html("<div class=\"QR-word\"><span>\"炉\"</span></div><em>字</em>");
                        } else if (data.data.name.indexOf("脑") > -1) {
                            $(".QR-result").html("<div class=\"QR-word\"><span>\"脑\"</span></div><em>字</em>");
                        }
                    }
                    $(".diag-prize").find(".show-info").html("长按关注公众号兑换大奖");
                } else if (data.errcode == 20) {
                    //未中奖
                    $(".QR-result").html("<div class=\"QR-word\"><span>\"" + fontN[num].font + "\"</span></div><em>字</em>");
                    $(".diag-prize").find(".show-info").html("长按关注公众号，关注更多精彩活动");
                    localStorage.setItem("beerNumLocalhost", num);
                    $('.diag-prize .form').hide();
                    $('.diag-prize .QR-img').show();
                } else if (data.errcode == 2) {
                    //重复扫码
                    $(".QR-result").html("<div class=\"QR-word\"><span>\"" + fontN[beerNumLocalhost].font + "\"</span></div><em>字</em>");
                    $(".diag-prize").find(".show-info").html("长按关注公众号，关注更多精彩活动");
                    $('.diag-prize .form').hide();
                    $('.diag-prize .QR-img').show();
                } else if (data.errcode == 3) {
                    //重复扫码
                    $(".QR-result").html("<div class=\"QR-word\"><span>\"" + fontN[beerNumLocalhost].font + "\"</span></div><em>字</em>");
                    $(".diag-prize").find(".show-info").html("长按关注公众号，关注更多精彩活动");
                    $('.diag-prize .form').hide();
                    $('.diag-prize .QR-img').show();
                } else {
                    //失败  没中奖
                    console.log(data.errmsg);
                    $(".QR-result").html("<div class=\"QR-word\"><span>\"" + fontN[num].font + "\"</span></div><em>字</em>");
                    $(".diag-prize").find(".show-info").html("长按关注公众号，关注更多精彩活动");
                    $('.diag-prize .form').hide();
                    $('.diag-prize .QR-img').show();
                }
                // 抽奖完毕
                // 当chachuIs 为 true时执行下面的函数
                chachuIsFun = function() {
                    console.log(JSON.stringify(getRewardResult));
                    if (getRewardResult.errcode == 0) {
                        if (getRewardResult.datatype == 2) {
                            $(".diag-prize").find(".prize-zhong").show().find("p").html(getRewardResult.data.name);
                        } else {
                            $(".diag-prize").find(".prize-zhong").show().find("p").html((getRewardResult.amount / 100).toFixed(2) + '元红包');
                        }
                    } else if (getRewardResult.errcode == 20) {
                        //未中奖
                        $(".diag-prize").find(".prize-sorry").show();
                    } else if (getRewardResult.errcode == 2) {
                        //别人重复扫码
                        $(".diag-prize").find(".prize-sorry").show();
                    } else if (getRewardResult.errcode == 3) {
                        //自己重复扫码
                        $(".diag-prize").find(".prize-shishen").show();
                    } else {
                        //失败  没中奖
                        $(".diag-prize").find(".prize-sorry").show();
                    }
                    jiance();
                };
                if (chachuIs) {
                    setTimeout(chachuIsFun, 1500);
                }
            });

            // 获取用户表单信息
            hlsjs.getCurrentUser(function(d){
                mch_id = d.data.mchId;
                mobile = d.data.mobile;
                realname = d.data.realName;
                address = d.data.address;
                // 默认填入中奖者表单信息（如果存在）
                $('.diag-prize .form-info').find('.input').eq(1).find('input').val(mobile);
                $('.diag-prize .form-info').find('.input').eq(0).find('input').val(realname);
                $('.diag-prize .form-info').find('.input').eq(2).find('input').val(address);
            })
            // 获取根路径
            RootUrl = hlsjs.getRootUrl();
        });


    })

    // 再次点击
    $("body").on("touchstart", '.QR-place', function() {
        var casTouched = $("#cas").attr("touched");
        if (casTouched == 'true') {
            jiance();
        }
    })

    // 提交个人信息
    $("body").on("touchstart", '.btn-submit', function() {
      var mobile = $(this).parent().find('.input').eq(1).find('input').val();
      var realname = $(this).parent().find('.input').eq(0).find('input').val();
      var address = $(this).parent().find('.input').eq(2).find('input').val();
      var phoneRight = /^1(3|4|5|7|8)\d{9}$/;
      if(mobile.length == 0 || realname.length == 0 || address.length == 0){
        alert('请输入完整信息！')
      }else if(!phoneRight.test(mobile)){
        alert('手机号输入错误！')
      }else{
        console.log("开始传递jsonp数据");
        $.ajax({
            url: RootUrl + "/user/api/user.update",
            data:{"mobile": mobile,
            "realname": realname,
            "address": address,
            "mchid": mch_id}, //"mobile="+mobile+"&realname="+realname+"&address="+address+"&mchid="+mch_id,
            success: function(result){
                if(result.errcode==0){
                    // 提交成功显示二维码
                    $('.diag-prize .form').hide();
                    $('.diag-prize .QR-img').show();
                }else{
                    alert("保存失败，请重试！");
                }
            },
            error: function(){
                alert("发送失败！");
            }
        });
        

      }
    })
})
