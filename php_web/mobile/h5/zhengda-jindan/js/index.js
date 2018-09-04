$(function () {

    $('.eggs').find('.egg').eq(0).css('height', 50).css('padding-top', $('body').width() * 1.52 * 0.24 - 48).find('.egg-li').css('height', $('body').width() * 1.52 * 0.24 - 13);
    $('.eggs').find('.egg').eq(1).css('height', 50).css('padding-top', $('body').width() * 1.52 * 0.21 - 48).find('.egg-li').css('height', $('body').width() * 1.52 * 0.21 - 13);
    $('.eggs').find('.egg').eq(2).css('height', 50).css('padding-top', $('body').width() * 1.52 * 0.21 - 48).find('.egg-li').css('height', $('body').width() * 1.52 * 0.21 - 13);

    // 初次进入页面，判断用户信息是否录入
    hlsjs.ready(function () {
        hlsjs.getCurrentUser(function (d) {
            // 如果用户信息不存在，弹出信息收集窗口
            if (d.errcode == 0 && typeof d.data != 'undefined' && typeof d.data.mobile != 'undefined' && d.data.mobile != '' && d.data.mobile != null) {
                // 展现抽奖页面
                showIndex();
            } else {
                $('.item3').addClass('show').find('.alert-big').removeClass('bounceOutUp').addClass('bounceInDown');
                if (d.errcode == 0 && typeof d.data != 'undefined' && typeof d.data.mchId != 'undefined') {
                    window.mch_id = d.data.mchId;
                }
            }
        });
    });


    var shakeDan = '';
    // 点击出现活动详情
    $('body').on('touchstart', '.btn-jianjie', function () {
        $('.item1').removeClass('show');
        $('.item2').addClass('show').find('.info').addClass('slideInLeft');
    })
    $('body').on('touchstart', '.btn-back', function () {
        $('.item2').removeClass('show').find('.info').removeClass('slideInLeft');;
        $('.item1').addClass('show');
    })
    // 点击蛋
    $('body').on('touchstart', '.egg', function () {
        var IS_touchstart = $(this).attr('istouchstart');
        if (IS_touchstart == 'true') {
            $('.egg').attr('istouchstart', false);
            var e = $(this);
            setTimeout(function () {
                lottery();
                $('.egg').attr('istouchstart', 'ready');
                e.find('.egg-li').removeClass('transition');
                e.addClass('sui').find('.dan').attr('src', './images/dan2.png');
                clearInterval(shakeDan);
            }, 400)
        } else if (IS_touchstart == 'ready') {
            $('.item4').addClass('show');
        }

    })

    // 点击阴影弹窗消失
    $('body').on('touchstart', '.item4', function () {
        $(this).find('.alert-small').removeClass('bounceInDown').addClass('bounceOutUp');
        setTimeout(function () {
            $('.item4').removeClass('show').find('.alert-small').removeClass('bounceOutUp');
        }, 700)
    })
    $('body').on('touchstart', '.alert-small', function (e) {
        e.stopPropagation();
    })

    // 马上领取
    $('body').on('touchstart', '#btn-get', function () {
        var QR = $('#qrcode').val();
        var mobile = $('#mobile').val();
        hlsjs.ready(function () {
            $.ajax({
                url: hlsjs.getRootUrl() + "/user/validate_sms_code",
                type: 'post',
                dataType: 'json',
                data: { mobile: mobile, sms_code: QR, mch_id: window.mch_id },
                success: function (result) {
                    if (result.errcode == 0) {
                        //成功
                        showIndex();
                        $.ajax({
                            url: hlsjs.getRootUrl() + "/myboard/update_personalinfo",
                            type:'post',
                            dataType: 'json',
                            data:{mobile:mobile,mchid:window.mch_id},
                            success: function(result){
                            },
                            error: function(){
                            }
                        });
                    } else {
                        //失败
                        //失败消息result.errmsg
                        alert(result.errmsg);
                    }
                },
                error: function () {
                    alert("发送失败！");
                }
            });
        });

    })
    // 发送验证码
    $('#btn-qr').on('touchstart', function () {
        var phoneRight = /^1(3|4|5|7|8)\d{9}$/;
        var mobile = $('#mobile').val();
        if (!phoneRight.test(mobile)) {
            alert('请输入正确手机号！')
        } else {
            // 发送验证码
            if (!$('#btn-qr').hasClass('loading')) {
                hlsjs.ready(function () {
                    $.ajax({
                        url: hlsjs.getRootUrl() + "/user/get_sms_code",
                        type: 'get',
                        dataType: 'json',
                        data: { mobile: mobile, mch_id: window.mch_id },
                        success: function (result) {
                            if (result.errcode == 0) {
                                var TIME = 60;
                                $('#btn-qr').addClass('loading').html(TIME + 's');
                                var timeLoad = setInterval(function () {
                                    TIME--;
                                    $('#btn-qr').addClass('loading').html(TIME + 's');
                                    if (TIME == 0) {
                                        clearInterval(timeLoad);
                                        $('#btn-qr').removeClass('loading').html('发送验证码');
                                    }
                                }, 1000)
                            } else {
                                //失败
                                //失败消息result.errmsg
                                alert(result.errmsg);
                            }
                        },
                        error: function () {
                            alert("发送失败！");
                        }
                    });
                });

            }
        }
    })

    // 抽奖代码
    function lottery() {

        // 开始抽奖
        hlsjs.ready(function () {
            hlsjs.takeActivity(function (data) {
                if (data.errcode == 0) {
                    $('.item4').addClass('item4-zhong');
                    if (data.datatype == 0) {
                        //奖品类型为：红包
                        //data.amount 中奖金额，单位分，实际使用元需要/100
                        $('.item4').find('h4').html('<span>' + (Number(data.amount) / 100).toFixed(2) + '</span>元');
                    }
                    if (data.datatype == 2) {
                        //奖品类型为：乐券
                        //data.data.name 奖品名称
                        $('.item4').find('h4').html('<span>' + data.data.name + '</span>');
                    }
                    if (data.datatype == 3) {
                        //奖品类型为：积分
                        //data.amount 中奖积分额度
                        $('.item4').find('h4').html('<span>' + data.amount + '</span>积分');
                    }
                    if (data.datatype == 100) {
                        //奖品类型为：红包、乐券、积分的叠加类型
                        if (data.multiData.length > 0) {
                            for (var i = 0; i < result.multiData.length; i++) {
                                if (data.multiData[i].strategyType == 0) {
                                    //奖品类型为：红包
                                    $('.item4').find('h4').html('<span>' + (Number(result.multiData[i].value) / 100).toFixed(2) + '</span>元');
                                }
                                if (data.multiData[i].strategyType == 2) {
                                    //奖品类型为：乐券
                                    $('.item4').find('h4').html('<span>' + result.multiData[i].value + '</span>');
                                }
                                if (data.multiData[i].strategyType == 3) {
                                    //奖品类型为：积分
                                    $('.item4').find('h4').html('<span>' + result.multiData[i].value + '</span>积分');
                                }
                            }
                        } else {
                            //未中奖
                        }
                    }
                } else if (data.errcode == 20) {
                    //未中奖
                    $('.item4 .alert-small h4 span').html('未中奖');
                } else if (data.errcode == 2) {
                    //此码已被他人扫过
                    $('.item4 .alert-small h4 span').html('此码已被他人扫过');
                    $('.item4').addClass('item4-no');
                } else if (data.errcode == 3) {
                    $('.item4 .alert-small h4 span').html('您已扫过此码');
                    //您已扫过此码
                } else {
                    $('.item4 .alert-small h4 span').html(data.errmsg);
                    //失败
                    //data.errmsg 失败错误信息
                }



            });
        });


        // 弹出信息
        $('.item4').addClass('show').find('.alert-small').addClass('bounceInDown');
        $('.chuizi').hide().removeClass('flash');
    }

    // 展现首页
    function showIndex() {
        // 初始化样式
        $('.item2 .info .txt').css('height', $('body').height() * 0.4-20);

        $('.item3').find('.alert-big').removeClass('bounceInDown').addClass('bounceOutUp');
        setTimeout(function () {
            $('.item3').removeClass('show');
        }, 1000)

        // 蛋移动
        $('.egg').each(function (index, el) {
            $(this).addClass('show1');
            var e = $(this);
            setTimeout(function () {
                $('.egg').addClass('show2');
                $('.egg').attr('istouchstart', true);
            }, 700)
            setTimeout(function () {
                $('.egg').find('.egg-li').css('height', e.find('.dan').height());
                $('.eggs').find('.egg').eq(0).find('.egg-li').css('height', $('.eggs').find('.egg').eq(0).find('.dan').height());
            }, 2200)
        });


        setTimeout(function () {
            shakeDan = setInterval(function () {
                $('.egg').find('.egg-li').addClass('shake-t');
                setTimeout(function () {
                    $('.egg').find('.egg-li').removeClass('shake-t');
                }, 1000)
            }, 2600)
        }, 100)


        $('.logo').addClass('swing');
    }



    //  hlsjs.ready(function() {
    //    	var RootUrl=hlsjs.getRootUrl();
    //    	var wx=hlsjs.wx();
    //    	wx.ready(function(){
    //    		wx.hideOptionMenu();
    //    		wx.showMenuItems({
    //    			menuList: ['menuItem:share:timeline','menuItem:share:appMessage']
    //    		});
    //    		wx.onMenuShareAppMessage({
    //    			title: '分享标题', // 分享标题
    //    			link:RootUrl+'/h5/file/index.html',//分享链接
    //    			desc: '分享描述', // 分享描述
    //    			imgUrl: RootUrl+'/h5/file/images/icon.jpg', // 分享图标
    //    			success: function() {
    //    				console.log('success');
    //    			},
    //    			cancel: function() {
    //    				console.log('cancel');
    //    			}
    //    		});
    //    		wx.onMenuShareTimeline({
    //    			title: '分享标题', // 分享标题
    //   			link:RootUrl+'/h5/file/index.html',//分享链接
    //    			desc: '分享描述', // 分享描述
    //    			imgUrl: RootUrl+'/h5/file/images/icon.jpg', // 分享图标
    //    			success: function() {
    //    				console.log('success');
    //    			},
    //    			cancel: function() {
    //    				console.log('cancel');
    //    			}
    //    		});
    //    	});
    //    });


    // 禁止页面移动
    var jinzhi = 0;
    document.addEventListener("touchmove", function (e) {
        if (jinzhi == 0) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, false);

    $('.item2').on('touchstart','.txt',function() {
        jinzhi = 1;
    });


})
