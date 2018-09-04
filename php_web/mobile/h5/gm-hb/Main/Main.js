Main.amount = 0;
Main.canOpenRedpacket = true;
Main.errmsg = '二维码识别中，请重试';
Main.init=function(){//页面初始化时调用该方法，例如用于处理URL参数，获取用户信息等
    
};
Main.needProfile=function(){//该方法判断是否需要填写用户信息，返回true表示需要填写，返回false表示不需要填写，例如当初始化后获得用户信息了就不需要填写即返回false
    if (Main.profile == null) {
        return true;
    } 
    hlsjs.ready(function(result) {
        hlsjs.takeActivity(function(resp) {
            hls.util.Dialog.closeLoading();
            if (resp.errcode == 0) {
                handlePrize(resp);
                Main.errmsg = "OK";
            } else if (resp.errcode == 3) {
                Main.amount = Math.round((resp.data.amount/100));
                Main.errmsg = resp.errmsg;
                Main.open();
                $('#red').show();
                setTimeout(function() {
                    alert('红包已被领取，如需红包提现，请至“光明莫斯利安会员服务平台>长寿家族>百万红包”处领取');
                }, 300);
            } else {
                Main.amount = 0;
                Main.errmsg = resp.errmsg;
                Main.canOpenRedpacket = false;
            }
        });
    });
    return false;
};
Main.canDraw=function(){//该方法判断当前用户是否可以抽红包，返回true表示可以，返回false表示不可以，例如当用户不是通过扫码进入或者该码已失效即返回false
    if (! Main.canOpenRedpacket) {
        if (Main.errmsg === '此码已被他人扫过' || Main.errmsg === '您已扫过此码') {
            Main.errmsg = '红包已被领取，如需红包提现，请至“光明莫斯利安会员服务平台>长寿家族>百万红包”处领取';
        }
        alert(Main.errmsg);
    }
    return Main.canOpenRedpacket;
};
Main.goCenter = function() {
    if (! Main.canOpenRedpacket) {
        return false;
    }
    return true;
};
Main.sendValidate=function(mobile){//该方法用于验证码发送，参数mobile为手机号，返回true表示发送成功，返回false表示发送失败，当成功之后发送按钮即变为1分钟倒计时
    var resp = $.ajax({url:"/h5/api/sms.send", method: 'post', data: {"mobile": mobile}, async:false}).responseText;
    resp = JSON.parse(resp);
    if (resp.errcode === 0) {
    	return true;
    } else if (resp.errcode === 700) {
    	alert(resp.errmsg);
    	return true;
    } else {
    	alert(resp.errmsg);
    	return false;
    }
};
Main.sendProfile=function(profile){//该方法用于用户信息上传，参数profile为json格式的数据，返回true表示上传成功，返回false表示上传失败，当上传成功之后根据情况进入下一个页面
	var resp = $.ajax({url:"/h5/api/profile.save", method: 'post', data: profile, async:false}).responseText;
	resp = JSON.parse(resp);
	if (resp.errcode === 0) {
        alert('信息保存成功！');
		Main.profile = profile;
		Main.profile.icon = currentUser.headimgurl;
		Main.profile.nickname = currentUser.nickname;
		return true;
	} else {
		alert(resp.errmsg);
		return false;
	}
};
Main.getProfile=function(){//该方法用户获取用户信息，返回json格式的数据
    return Main.profile;
};
Main.getMoney=function(){//该方法在用户点击提现时调用
	location.href = '/user/red_packet?mch_id=' + currentUser.mchId;
};
Main.drawPacket=function(){//该方法在用户打开红包时调用
    if (Main.amount) {
        return parseInt(Main.amount);
    } else if (Main.errmsg !== 'OK') {
        if (Main.errmsg === '此码已被他人扫过' || Main.errmsg === '您已扫过此码') {
            Main.errmsg = '红包已被领取，如需红包提现，请至“光明莫斯利安会员服务平台>长寿家族>百万红包”处领取';
        }
        alert(Main.errmsg);
        return;
    }
};

var handlePrize = function(resp) {
	var prizeName;
	if (resp.datatype == 0) {
		Main.amount = Math.round((resp.amount/100));
	} else {
		Main.amount = 0;
	}
};
