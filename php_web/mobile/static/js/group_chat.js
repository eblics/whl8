var host='m.lsa0.cn';
if(CI_ENV=='development'){
    host='dev.m.lsa0.cn';
}
if(CI_ENV=='testing'){
    host='test.m.lsa0.cn';
}
if(CI_ENV=='product'){
    host='m.lsa0.cn';
}
var socket = io("ws://"+host+":3009/chat");

// 加入房间
socket.on('connect', function () {
    setTimeout(function() {
        socket.emit('join', currentUser, currentGroup);
    }, 1000);
});
// 监听消息
socket.on('msg', function (user,msg,syschat) {
    groupChat.addChatMessage(user,msg,syschat);
});

// 监听历史消息
socket.on('historyMsg', function (msgarr) {
    groupChat.addHistoryMessage(msgarr); 
    socket.off('historyMsg');
});

// 监听系统消息
socket.on('sys', function (users,msg,groupInfo) {
    groupChat.addSysMessage(users,msg,groupInfo);
});

var groupChat={
	init:function(){
        if(top.location.hash!=''){
            top.common.refreshTitle('群聊');
        }else{
            common.refreshTitle('群聊');
        }
		this.event();
        common.listenHash();
        common.noOverScroll('.page_chat');
        this.showRpTip(1);
	},
	event:function(){
        var _this=this;
        this.sendMessage();
        this.groupChatSetting();
        this.preLoadImg();
        this.scrollSolution();
        this.groupChatApps();
	},
    preLoadImg:function(){
        setTimeout(function(){
            if(typeof userImage!='undefined' && userImage!=''){
                for(var i=0;i<userImage.length;i++){
                    common.preLoadImg(userImage[i]);
                }
            }
        },3000);
    },
    scrollSolution:function(){
        var u = navigator.userAgent;
        var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        if(isIOS){
            $('.page_chat').css({'position':'absolute','left': 0,'top':0,'right': 0,'bottom': 0,'overflow-x':'hidden','overflow-y':'auto'});
        }
    },
    sendMessage:function(){
        $('#groupChatSend').on('tap',function(){
            var message=$('#groupChatInput').val();
            if(message!=''){
                socket.emit('message', message);
                $('#groupChatInput').val('').blur();
            }
            $('#groupChatInput').focus();
        });
        $('#groupChatInput').on('keydown',function(e){
            if(e.keyCode==13){
                $('#groupChatSend').trigger('tap');
                $(this).focus();
            }
        });
    },
    goToScrollBottom:function(type){
        var u = navigator.userAgent;
        var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        if(isIOS){
            if(type==1){
                $('.page_chat').scrollTop($('.page_chat')[0].scrollHeight+$('.page_chat')[0].offsetHeight);
            }else{
                $('.page_chat').animate({scrollTop:$('.page_chat')[0].scrollHeight+$('.page_chat')[0].offsetHeight}, 800);
            }
        }else{
            if(type==1){
                $('html,body').scrollTop($('body')[0].scrollHeight+$('body')[0].offsetHeight);
            }else{
                $('html,body').animate({scrollTop:$('body')[0].scrollHeight+$('body')[0].offsetHeight}, 800);
            }
        }
    },
    addChatMessage:function(user,msg,syschat){
        var mystyle='';
        var mytime1='<font style="color:#ccc">( '+user.time+' )</font>';
        var mytime2='';
        if(user.id==currentUser.id){
            mystyle='mychat';
            mytime1='';
            mytime2='<font style="color:#ccc">( '+user.time+' )</font>';
        }
        if(syschat){
            mystyle+=' syschat'
            this.showRpTip(1);
        }
        var html='<dl class="chat_list '+mystyle+'"><dt><img src="'+user.image+'"/></dt><dd><div class="name">'+mytime2+' '+user.name+'：'+mytime1+'</div><div class="txt"><p>'+msg+'</p></div></dd></dl>';
		$('.page_chat').append(html);
        this.goToScrollBottom();
	},
    addHistoryMessage:function(msgarr){
        var html='';
        for(var i=0;i<msgarr.length;i++){
            if(Number(msgarr[i][0])==-1 || typeof msgarr[i][4]=='undefined' || msgarr[i][1]==''){
                continue;
            }
            var mystyle='';
            var mytime1='<font style="color:#ccc">( '+msgarr[i][0].substr(5,11)+' )</font>';
            var mytime2='';
            if(msgarr[i][1]==currentUser.id){
                mystyle='mychat';
                mytime1='';
                mytime2='<font style="color:#ccc">( '+msgarr[i][0].substr(5,11)+' )</font>';
            }
            if(msgarr[i][1]==-1){
                //html+='<div class="sysmsg"><p>'+msgarr[i][2]+'</p></div>';
            }else{
                if(msgarr[i].length==6){
                    var syschat='';
                    if(msgarr[i][3]==1){
                        syschat=' syschat';
                    }
                    html+='<dl class="chat_list '+mystyle+syschat+'"><dt><img src="'+msgarr[i][4]+'"/></dt><dd><div class="name">'+mytime2+' '+msgarr[i][5]+'：'+mytime1+'</div><div class="txt"><p>'+msgarr[i][2]+'</p></div></dd></dl>';
                }else{
                    html+='<dl class="chat_list '+mystyle+'"><dt><img src="'+msgarr[i][3]+'"/></dt><dd><div class="name">'+mytime2+' '+msgarr[i][4]+'：'+mytime1+'</div><div class="txt"><p>'+msgarr[i][2]+'</p></div></dd></dl>';
                }
            }
        }
        $('.page_chat').html('<div id="historyLog"></div>');
		$('.page_chat #historyLog').html(html);
        this.goToScrollBottom('1');
	},
    addSysMessage:function(users,msg,groupInfo){
		$('.page_chat').append('<div class="sysmsg"><p>'+msg+'</p></div>');
        $('#online').html(users.length);
        if(typeof groupInfo!='undefined'){
            $('#memberNum').html(groupInfo.memberNum);
        }
        this.goToScrollBottom();
	},
    groupChatSetting:function(){
        $('#groupChatSetting').on('tap',function(){
            common.framePage('/group/setting/'+currentGroup.id);
        });
    },
    groupChatApps:function(){
        $('#groupPlus').on('tap',function(){
            var isHidden=$('#groupApps').is(":hidden");
            if(isHidden){
                $('#groupChatInput').blur();
                $('#groupApps').fadeIn('fast');
            }else{
                $('#groupApps').hide();
            }
        });
        $('.plus_tip').on('tap',function(){
            $(this).hide();
            $('#groupPlus').trigger('tap');
        });
        $('#groupChatInput').on('focus',function(){
            $('#groupApps').hide();
        });
        $('#groupApps li').on('tap',function(){
            $('#groupApps').hide();
            var thisId=$(this).attr('id');
            if(thisId=='appScanPK'){
                common.framePageMini('/group_app/scanpk/lists/'+currentGroup.id);
            }
            if(thisId=='appFishing'){
                common.framePageMini('/group_app/fishing/main/'+currentGroup.id);
            }
        });
    },
    showRpTip:function(atype){
        var isHidden=$('#groupApps').is(":hidden");
        if(!isHidden){
            return;
        }
        if(typeof atype=='undefined'){
            var atype=1;
        }
        clearTimeout(window.t_showRpTip);
        if(atype==1){
            $('.plus_tip').show();
            window.t_showRpTip=setTimeout(function(){
                $('.plus_tip').fadeOut();
            },5000);
        }else if(atype==0){
            $('.plus_tip').hide();
        }
    }
    
};
$(function(){
	groupChat.init();
});
