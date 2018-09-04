/**
 * 用户积分界面js逻辑
 * 
 * @author shizq
 */
$(function() {

    var Page = {

        init: function() {
            $('.trans-list .errcode_done').on('click',function(){
                var tip=$(this).attr('data');
                Page.showErrTip(tip);
            }).removeClass('errcode').addClass('errcode_done');
        },

        showErrTip: function(tip){
            if (tip == 'NO_AUTH')
                common.alert(0, '<div style="padding:0 10px;text-align:left;">提取失败！微信公司对活跃度过低的微信进行了红包拦截。<BR>以下方法能提高微信活跃度<BR>1.保持每天登陆，经常与好友聊天互动<BR>2.绑定实名认证的银行卡<BR>正常使用一段时间后，微信公司就会帮您提升活跃度。</div>', 1);
            if (tip == 'SENDNUM_LIMIT')
                common.alert(0, '提取失败！<br/><br/>您今日领取红包个数达到上限，明天再试吧。', 1);
            if (tip == 'FREQ_LIMIT')
                common.alert(0, '提取失败！<br/><br/>提现过于频繁，请稍后再试吧。', 1);
            if (tip == 'NOTENOUGH')
                common.alert(0, '提取失败！<br/><br/>红包发放助手今天忘带钱包了，明天再试吧。', 1);
            if (tip == 'MONEY_LIMIT')
                common.alert(0, '提取失败！<br/><br/>红包金额不在微信限制范围内，明天再试吧。', 1);
            if (tip == null || tip == "null") {
                common.alert(0, '提取失败！<br/><br/>您的微信账号活跃度过低<br />提现失败，已退回账户余额。', 1);
            }
        }
    };

    Page.init();

});