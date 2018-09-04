/**
 * 登陆页面
 * 
 * @author shizq 
 */
var Page = {

    init: function() {
        $("#submit_btn").css('background', '#ccc');
        Page.mobilePass = false;
        Page.passwdPass = false;
        Page.smsCodePass = false;

        var account = window.localStorage.getItem('account');
        if (account) {
            $('#account').val(account);
            Page.mobilePass = true;
            $("#account_img").html('<img src="/static/images/dui.png">');
        }
        this.bindEvent();
    },

    bindEvent: function() {
        document.onkeydown = function(event) {
            if (event.keyCode == 13) { 
                if (Page.mobilePass && Page.passwdPass && Page.smsCodePass) {
                    Page.login();   
                } else {
                    console.log('not allow');
                }
            }
        };

        $('#account').bind('input propertychange', function() {
            var str = $(this).val();
            if (str.length !== 11) {
                Page.mobilePass = false;
                $("#submit_btn").css('background', '#ccc');
                $("#account_img").empty();
                $(this).css("border-color", "");
                return;
            }
            if (hls.utils.StringUtil.isMobile(str)) {
                Page.mobilePass = true;
                if (Page.mobilePass && Page.passwdPass && Page.smsCodePass) {
                    $("#submit_btn").css('background', '');  
                }
                $("#account_img").html('<img src="/static/images/dui.png">');
                $(this).css("border-color", "");
            } else {
                $("#account_img").html('<img src="/static/images/cuo.png">');
                $(this).css("border-color", "red");
            }
        });

        $('#password').bind('input propertychange', function() {
            var str = $(this).val();
            if (str.length < 6) {
                Page.passwdPass = false;
                $("#submit_btn").css('background', '#ccc');
                $("#password_img").empty();
                return;
            }
            Page.passwdPass = true;
            if (Page.mobilePass && Page.passwdPass && Page.smsCodePass) {
                $("#submit_btn").css('background', '');  
            }
            $("#password_img").html('<img src="/static/images/dui.png">');
        });

        $('#verify').bind('input propertychange', function() {
            var str = $(this).val();
            if (str.length < 6) {
                Page.smsCodePass = false;
                $("#submit_btn").css('background', '#ccc');
                $("#verify_img").empty();
                $(this).css("border-color", "");
                return;
            }
            Page.checkSmsCode();
        });

        $("#getCode").click(function() {
            if (Page.mobilePass) {
                var mobile = $('#account').val();
                Page.getSms(mobile);
            } else {
                common.alert('消息：请输入有效的登陆手机号。');
            }
        });

        $("#submit_btn").click(function() {
            if (Page.mobilePass && Page.passwdPass && Page.smsCodePass) {
                Page.login();   
            } else {
                console.log('not allow');
            }
        });
    },

    getSms: function(mobile) {
        var params = {"account": mobile, "for": 'login'};
        $.get("/utils/api/sms.get", params, function(result) {
            if (result.errcode === 0) {
                common.alert('验证码已发送,请在5分钟之内使用。');
                Page.setGetcodeTime(60);
            } else if (result.errcode === 700) {
                common.alert(result.errmsg);
                Page.setGetcodeTime(60);
            } else {
                common.alert(result.errmsg);
            }
        }).fail(netError);
    },

    checkSmsCode: function() {
        var mobile = $.trim($("#account").val());
        var smsCode = $.trim($("#verify").val());
        var params = {
            "account": mobile,
            "value": smsCode,
        };
        $.post('/user/valid_mes', params, function(resp) {
            if(resp.errcode == 0) {
                Page.smsCodePass = true;
                if (Page.mobilePass && Page.passwdPass && Page.smsCodePass) {
                    $("#submit_btn").css('background', '');  
                }
                $("#verify_img").html('<img src="/static/images/dui.png">');
                $("#verify").css("border-color", "");
                if (resp.data.length > 0 && $('#mch_id option').length === 1) {
                    Page.showMerchantList(resp.data);
                }
            } else {
                $("#verify_img").html('<img src="/static/images/cuo.png">');
                $("#verify").css("border-color", "red");
            }
        }).fail(netError);
    },

    login: function() {
        var account    = $("#account").val();
        var password   = $("#password").val();
        var pass       = $('#password').val();
        var keep_state = $("#ison").prop('checked');
        var mchId      = $("#mch_id").val();
        var params = {
            account: account, 
            password: password, 
            keep_state: keep_state, 
            mch_id: mchId,
        };
        $.post("/user/auth", params, function(resp) {
            if (resp.errcode == 0) {
                if (keep_state) {
                    window.localStorage.setItem('account', account);
                } else {
                    window.localStorage.setItem('account', null);
                }
                window.location = '/';
            } else {
                common.alert(resp.errmsg);
            }
        });
    },

    /**
     * 展示企业号下所有的商户列表
     * 
     * @param merchants
     * @return void
     */
    showMerchantList: function(merchants) {
        $('#mch_id').empty();
        var i = 0;
        merchants.forEach(function(merchant) {
            var option = '<option value="' + merchant.id + '">';
            option    +=   merchant.name;
            option    += '</option>';
            $('#mch_id').append(option);
            i++;
        });
        if (i < 2) return;
        $('.merchants-selector').show();
    },

    setGetcodeTime:function(timer){
        var o = document.getElementById("getCode");
        if (timer == 0) {  
            o.removeAttribute("disabled");            
            o.value="获取验证码";  
            timer = 60;  
        } else { 
            o.setAttribute("disabled", true);  
            o.value="重新发送(" + timer + ")";  
            timer--;  
            setTimeout(function() {  
                Page.setGetcodeTime(timer);
            }, 1000);  
        }  
    },

};
$(function(){
    Page.init();
});