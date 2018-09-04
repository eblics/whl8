var groupPwd={
	init:function(){
		if(top.location.hash!=''){
            top.common.refreshTitle('修改群组');
        }else{
            common.refreshTitle('创建群组');
        }
		this.event();
	},
	event:function(){
        this.btnNext();
        common.noOverScroll('body');
	},
    btnNext:function(){
        var _this=this;
		$('#btnNext').on('tap',function(){
            var id=$.trim($('#groupId').val());
            var mchId=$.trim($('#mchId').val());
            location.href="/group/lists/"+mchId;
        });
	}
    
};
$(function(){
	groupPwd.init();
});
