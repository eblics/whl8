var groupSetting={
	init:function(){
        if(top.location.hash!=''){
            top.common.refreshTitle('设置群');
        }else{
            common.refreshTitle('设置群');
        }
		this.event();
	},
	event:function(){
		this.groupName();
        this.password();
        this.nickName();
        this.headImage();
        this.quit();
	},
    groupName:function(){
        $('#groupName').on('tap',function(){
            if(currentUser.id==currentGroup.masterId)
                top.common.framePage('/group/add/'+currentGroup.id);
        });
	},
    password:function(){
        $('#password').on('tap',function(){
            top.common.framePage('/group/pwd/'+currentGroup.id);
        });
    },
    nickName:function(){
        $('#nickName').on('tap',function(){
            top.common.framePage('/group/userinfo/'+currentGroup.id);
        });
    },
    headImage:function(){
        $('#headImage').on('tap',function(){
            top.common.framePage('/group/userinfo/'+currentGroup.id);
        });
    },
    quit:function(){
        $('#btnQuit').on('tap',function(){
            common.confirm('确定执行此操作吗？',function(r){
                if(r==1){
                    //退出
                    $.ajax({
                        url: "/group/quit",
                        data: {'groupId':currentGroup.id},
                        type: 'post',
                        dataType: 'json',
                    }).done(function(d){
                        if(d.errcode==0){
                            top.location.href="/group/lists/"+currentGroup.mchId;
                        }else{
                            common.alert('退出失败，请重试');
                        }
                    }).fail(function(d){
                        common.alert('操作失败，请重试');
                    });
                    
                }
            }); 
        });
    }
    
    
};
$(function(){
	groupSetting.init();
});
