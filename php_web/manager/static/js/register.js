/**
 * 企业注册
 * @type {Object}
 * @author shizq
 */
var Page = {

    init: function() {
        document.onkeydown = function(event) {
            e = event ? event :(window.event ? window.event : null); 
            if (e.keyCode == 13){ 
                var disabled = $("#submit_btn").prop("disabled");
                if (disabled) {
                    common.alert('信息填写不完全或有错误。');
                }
            }
        };
        this.bindEvent();
    },

    bindEvent: function() {
        $('#account').bind('input propertychange', function() {
            if ($('#account').val().length !== 11) {
                Page.changeInputstyle('account', null);
                return;
            }
            if (/^1[34578]\d{9}$/.test($.trim($('#account').val()))) {
                Page.changeInputstyle('account', true);
                Page.checkSubmitEnable();
            } else {
                Page.changeInputstyle('account', false);
            }
        });
        $('#verifyca').bind('input propertychange', function() {
            if ($('#verifyca').val().length !== 4) {
                Page.changeInputstyle('verifyca', null);
                return;
            }
            Page.isVerifyca(function(pass) {
                if (pass) {
                    Page.changeInputstyle('verifyca', true);
                } else {
                    Page.changeInputstyle('verifyca', false);
                }   
            });
        });
        $('#verify').bind('input propertychange', function() {
            if ($('#verify').val().length !== 6) {
                Page.changeInputstyle('verify', null);
                return;
            }
            Page.isVerify(function(pass) {
                if (pass) {
                    Page.changeInputstyle('verify', true);
                }else{
                    Page.changeInputstyle('verify', false);
                }   
            });
        });

        $('#password').bind('input propertychange', function() {
            var password = $(this).val();
            $('#repassword').val('');
            Page.changeInputstyle('repassword', null);
            if (password.length >= 6) {
                Page.changeInputstyle('password', true);
            } else {
                Page.changeInputstyle('password', false);
            }
        });
        $('#repassword').bind('input propertychange', function() {
            var repassword = $(this).val();
            if (repassword.length >= $('#password').val().length) {
                if (repassword === $('#password').val()) {
                    Page.changeInputstyle('repassword', true);
                } else {
                    Page.changeInputstyle('repassword', false);
                }
            } else {
                Page.changeInputstyle('repassword', null);
            }
        });

        $("#getCode").click(function() {
            if (! /^1[34578]\d{9}$/.test($.trim($('#account').val()))) {
                Page.changeInputstyle('account', false);
                $('#account').focus();
                return;
            }
            if ($('#verifyca').val().length !== 4) {
                Page.changeInputstyle('verifyca', false);
                $('#verifyca').focus();
                return;
            }
            $.post("/user/validcode", {account: $('#account').val()}, function(resp) {
                if (resp.errcode == 0) {
                    common.alert('验证码已发送,请在5分钟之内使用！');
                    Page.setGetcodeTime(60);
                } else {
                    common.alert(resp.errmsg);
                }
            });
        });
        
    },

    // 验证图形验证码
    isVerifyca: function(callback) {
        var verifyca = $.trim($("#verifyca").val());
        $.post('/user/check_validate', {imgvalid: verifyca}, function(resp) {
            if (resp.errcode === 0){
                callback(true);
            } else {
                callback(false);
            }
        });
    }, 

    // 验证手机验证码
    isVerify:function(callback) {
        if (! /\d{6}/.test($('#verify').val())) {
            callback(false);
            return;
        }
        var mobile = $.trim($('#account').val());
        var verify = $.trim($("#verify").val());
        $.post('/user/valid_mes', {account: mobile, value: verify}, function(resp) {
            if (resp.errcode === 0) {
                callback(true);
            } else {
                callback(false);
            }
        });
    },

    checkSubmitEnable: function() {
        if ($('.circle3').hasClass('circle_active')) {
            $("#submit_btn").prop("disabled", false);
            $("#submit_btn").css({'background': ''});
            $("#submit_btn").click(function() {
                if (! $("input[name='agree']").is(':checked')){
                    common.alert('请仔细阅读并勾选 同意《红码平台服务条款》');
                    return false;
                } else {
                    var account = $("#account").val();
                    var password = $("#password").val();
                    var verify = $("#verify").val();
                    var verifica = $("#verifica").val();
                    var params = {account: account, password: password,validcode:verify};
                    $.post("/user/reg_user", params, function(e) {
                        if (e.errcode === 0) {
                            common.alert('注册成功。');
                            setTimeout(function() {
                                window.location = '/user/login';
                            }, 3000);
                        } else {
                            common.alert(e.errmsg);
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        }
                    });
                }
            });
            return;
        }
        if ($('#password_img').html() === '<img src="/static/images/dui.png">' &&
        $('#repassword_img').html() === '<img src="/static/images/dui.png">') {
            $("#submit_btn").prop("disabled", false);
            $("#submit_btn").css({'background': ''});
            $("#submit_btn").click(function() {
                $('.step').removeClass('step_active');
                $('.step3').addClass('step_active');
                $('.rs').removeClass('rs_active');
                $('.rs3').addClass('rs_active');
                $('.circle').removeClass('circle_active');
                $('.circle3').addClass('circle_active');
                Page.checkSubmitEnable();
            });
            return;
        }
        if ($('#account_img').html() === '<img src="/static/images/dui.png">' &&
        $('#verifyca_img').html() === '<img src="/static/images/dui.png">' &&
        $('#verify_img').html() === '<img src="/static/images/dui.png">') {
            $("#submit_btn").prop("disabled", false);
            $("#submit_btn").css({'background': ''});
            $("#submit_btn").click(function() {
                $('.step').removeClass('step_active');
                $('.step2').addClass('step_active');
                $('.rs').removeClass('rs_active');
                $('.rs2').addClass('rs_active');
                $('.circle').removeClass('circle_active');
                $('.circle2').addClass('circle_active');
                $("#submit_btn").prop("disabled", true);
                $("#submit_btn").css({'background': '#ccc'});
            });
            return;
        }
    },

    setGetcodeTime:function(timer){
        var o = document.getElementById("getCode");
        if (timer == 0) {  
            o.removeAttribute("disabled");            
            o.value="免费获取验证码";  
            timer = 60;  
        } else { 
            o.setAttribute("disabled", true);  
            o.value="重新发送(" + timer + ")";  
            timer--;  
            setTimeout(function() {  
                Page.setGetcodeTime(timer);
            },  
            1000)  
        }  
    },

    changeInputstyle: function(id, flag) {
        if (flag === null) {
            $("#"+id+"_img").html('');
            $("#"+id).css("border-color","");
            $("#submit_btn").prop("disabled", true);
            $("#submit_btn").css({'background': '#ccc'});
            return;
        }
        if (flag) {
            $("#"+id+"_img").html('<img src="/static/images/dui.png">');
            $("#"+id).css("border-color","");
            Page.checkSubmitEnable();
        } else {
            $("#"+id+"_img").html('<img src="/static/images/cuo.png">');
            $("#"+id).css("border-color","red");
        }
    },

};

$(function(){
    Page.init();
});