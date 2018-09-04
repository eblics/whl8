/// <reference path="D:/SOFTWARE/typings/jquery/jquery.d.ts" />
var minVal = accountData.withdrawLimit;
var account = {
    init: function() {
        // document.addEventListener("touchmove",function(e){
        //     e.preventDefault();
        //     e.stopPropagation();
        // },false);
        var _this = this;
        _this.tab();
        _this.inputValid();
        _this.withdraw();
        $('.input input').val('');
        $('.btnlist').on('touchend',function(){
            location.href='/user/red_packet_logs/'+accountData.mchId;
        });
    },
    tab: function() {
        $('.tab li').on('touchend', function() {
            $(this).addClass('current').siblings('li').removeClass('current');
            var index = $(this).index();
            $('.tab-con .con').eq(index).show().siblings('.con').hide();
            if (index == 1) {
                $('.tab-float .block').stop().animate({
                    'left': '50%'
                }, 300);
            } else {
                $('.tab-float .block').stop().animate({
                    'left': 0
                }, 300);
            }
        });
    },
    inputValid: function() {
        $('.tab-con .input-val').on('input propertychange', function() {
            var index = $('.tab li.current').index();
            var thisVal = $(this).val();
            var btn = $(this).parent().siblings('.btn');
            var exp = /^[1-9]\d*(?:.\d{1,2})?$/;
            if (isNaN(thisVal)) {
                thisVal = thisVal.replace(/[^\.\d]/, "");
                $(this).val(thisVal);
            }
            if (exp.test(thisVal) || Math.round(thisVal * 100) >= minVal) {
                if (thisVal > 200 || Math.round(thisVal * 100) < minVal) {
                    btn.addClass('disabled');
                }
                if (index == 0) {
                    if (thisVal <= accountData.normalAmount / 100) {
                        btn.removeClass('disabled');
                    } else {
                        if (!btn.hasClass('disabled')) {
                            btn.addClass('disabled');
                        }
                    }
                }
                if (index == 1) {
                    if (thisVal <= accountData.groupAmount / 100 && thisVal >= accountData.wxRpTotalNum) {
                        btn.removeClass('disabled');
                    } else {
                        if (!btn.hasClass('disabled')) {
                            btn.addClass('disabled');
                        }
                    }
                }
            } else {
                if (!btn.hasClass('disabled')) {
                    btn.addClass('disabled');
                }
            }
        });
    },
    withdraw: function() {
        var _this = this;
        $('.tab-con .btn').on('touchend', function() {
            var __this = $(this);
            if (!$(this).hasClass('disabled')) {
                var input = $(this).siblings('.input').children('input')
                var val = input.val();
                var moneyType = $('.tab li.current').index();
                common.loading();
                $.ajax({
                    url: '/user/withdraw/' + accountData.mchId,
                    data: {
                        'amount': val,
                        'moneyType': moneyType
                    },
                    type: 'post',
                    cache: false,
                    dataType: 'json',
                    success: function(d) {
                        common.unloading();
                        var wxErrArr = ['NO_AUTH', 'SENDNUM_LIMIT', 'FREQ_LIMIT', 'NOTENOUGH'];
                        if (d.errorCode == 0) {
                            if (moneyType == 0) {
                                accountData.normalAmount = Number(accountData.normalAmount).sub(Number(val).mul(100));
                                $('#normalAmount').html((accountData.normalAmount / 100).toFixed(2));
                            } else if (moneyType == 1) {
                                accountData.groupAmount = Number(accountData.groupAmount).sub(Number(val).mul(100));
                                $('#groupAmount').html((accountData.groupAmount / 100).toFixed(2));
                            }
                            input.val('');
                            __this.addClass('disabled');
                            common.alert(1, '提现请求已发送给微信平台');
                            if(d.payAccountType==1 && d.commonSubscribe==0){
                                setTimeout(function(){
                                    $('.alert').remove();
                                    $('.pay_by_hls').show();
                                },1000);
                            }
                        } else if (typeof d.wxmsg != 'undefined' && typeof d.wxmsg.err_code != 'undefined' && $.inArray(d.wxmsg.err_code, wxErrArr) != -1) {
                            if (d.wxmsg.err_code == 'NO_AUTH')
                                common.alert(0, '<div style="padding:0 10px;text-align:left;">提取失败！您的微信帐号异常，解除异常步骤：<BR><BR>1.拨打微信客服电话95017<BR>2.按1进入个人微信业务<BR>3.输入异常微信绑定的手机号码按#<BR>4.按2进入微信红包业务<BR>5.按4进入微信红包收发异常业务<BR>6.按0进入人工客服，叫人工帮您解除微信异常</div>', 1);
                            if (d.wxmsg.err_code == 'SENDNUM_LIMIT')
                                common.alert(0, '提取失败！<br/><br/>您今日领取红包个数达到上限，明天再试吧。', 1);
                            if (d.wxmsg.err_code == 'FREQ_LIMIT')
                                common.alert(0, '提取失败！<br/><br/>提现过于频繁，请稍后再试吧。', 1);
                            if (d.wxmsg.err_code == 'NOTENOUGH')
                                common.alert(0, '提取失败！<br/><br/>红包发放助手今天忘带钱包了，明天再试吧。', 1);
                        } else {
                            common.alert(0, d.errorMsg,1);
                        }
                    },
                    error: function() {
                        common.unloading();
                        common.alert(0, '请求失败');
                    }
                });
            }
        });
    }
}
$(function() {
    account.init();
});